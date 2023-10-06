<?php

namespace App\Http\Controllers;

use Dompdf\Dompdf;
use Dompdf\Options;
use App\Models\Customer;
use App\Models\CustomerBalance;
use App\Models\ProductDetail;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Exports\TransactionExport;
use Maatwebsite\Excel\Facades\Excel;
use stdClass;

class TransactionController extends Controller
{
    public function index(Request $request){
        $customer_no = $request->customer_no;
        $product_payment = $request->product_payment;
        $customer = Customer::where('customer_no', $customer_no)->first();

        // Validation
        if ($customer == null) {
            return response_json(404, 'failed', 'Customer not found');
        }
        foreach ($product_payment as $item) {
            $product_detail = ProductDetail::where('code', $item['product_code'])->first();
            if ($product_detail == null) {
                return response_json(404, 'failed', 'Product code ' . $item['product_code'] . ' not found in database');
            }
            if ($item['duration'] == 'null') {
                $item['duration'] = null;
            }
        }
        // Create transaction
        $transaction = Transaction::create([
            'customer_id' => $customer->id,
        ]);

        // Calculate and create detail transaction
        $payments = [];
        foreach ($product_payment as $item) {
            $product_detail = ProductDetail::whereHas('product_value', function($query) use ($customer) {
                $query->where('product_id', $customer->product_id);
            })
            ->with(['product_value' => function($queryValue) use ($customer) {
                $queryValue->where('product_id', $customer->product_id)
                ->select('id', 'product_id', 'product_detail_id', 'value');
            }])
            ->where('code', $item['product_code'])->first();

            if ($item['duration'] == null || $item['duration'] == 'null') {
                $duration = 1;
            } else {
                $duration = $item['duration'];
            }

            $total_payment = $item['payment'] * $duration;

            // Check the duration of the product
            if ($product_detail->maximum_used != null) {
                if ($item['duration'] > $product_detail->maximum_used) {
                    $duration = $product_detail->maximum_used;
                } else {
                    $duration = $item['duration'];
                }
            } else {
                $duration = $item['duration'];
            }

            if ($duration == null || $duration == 'null') {
                $duration = 1;
            }

            // Calculate claimed payment, and customer payment
            $claimed_payment = $product_detail->product_value[0]->value * $duration;
            
            $customer_payment = $total_payment - $claimed_payment;
            
            if ($claimed_payment > $total_payment) {
                $claimed_payment = $total_payment - 0;
                $customer_payment = 0;
            }
            
            // Check the customer balance for the customer payment and update the customer balance
            $customer_balance = CustomerBalance::where('customer_id', $customer->id)->where('product_detail_id', $product_detail->id)->first();
            
            // Customer balance akan berkurang jika product yang diklaim ada maximum_usednya atau masih dalam jangka waktu per tahun. Jika product yang diklaim tidak ada maximum_usednya dan tidak dalam jangka waktu per tahun, maka customer balance tidak akan berkurang. 
            // Cek apakah product tersebut ada maximum_usednya, jika iya (Kurangin maximum_used dengan duration, jika maximum_used sudah habis, ), jika tidak (Cek apakah product dalam jangka waktu setahun, jika ya cek apakah balance masih cukup baru dikurangin (kalo ga cukup, update claimed_payment jadi balancenya aja dan customer_payment jadi total_payment - claimed_payment baru). jika tidak balance tidak usah dikurangin)

            // Jika product ada maximum_used
            if ($product_detail->maximum_used != null || $customer_balance->maximum_used_balance == -1) {
                // cek apakah balance sebelumnya sudah berbeda bulan, dan tahun, jika iya, update maximum_used_balance
                if ($customer_balance->created_at->addYear()->format('d m Y') == now()->format('d m Y')) {
                    $customer_balance->update([
                        'balance' => $product_detail->product_value[0]->value,
                        'maximum_used_balance' => $product_detail->maximum_used,
                        'created_at' => now(),
                    ]);
                }

                // Jika maximum_used sudah habis
                if ($customer_balance->maximum_used_balance < 0) {
                    $claimed_payment = 0;
                    $customer_payment = $total_payment - $claimed_payment;
                    $balance = 0;
                    $maximum_used_balance = -1;
                }
                // Jika maximum_used belum habis 
                else {
                    // Jika durasi penggunaan lebih dari maximum_used
                    if ($customer_balance->maximum_used_balance - $item['duration'] <= 0) {
                        $claimed_payment = $customer_balance->balance * $customer_balance->maximum_used_balance;
                        $customer_payment = $total_payment - $claimed_payment;
                        $balance = 0;
                        $maximum_used_balance = -1;
                    }
                    // Jika durasi penggunaan kurang dari maximum_used
                    else {
                        $balance = $customer_balance->balance;
                        $maximum_used_balance = $customer_balance->maximum_used_balance - $duration;
                    }
                }
            }
            // Jika product tidak ada maximum_used
            else {
                // Jika productnya digunakan per-tahun
                if ($product_detail->product_per == 'year') {
                    // cek apakah balance sebelumnya berbeda tahun, jika ya update balance sesuai dengan product valuenya
                    if ($customer_balance->created_at->addYear()->format('d m Y') == now()->format('d m Y')) {
                        $customer_balance->update([
                            'balance' => $product_detail->product_value[0]->value,
                            'maximum_used_balance' => $product_detail->maximum_used,
                            'created_at' => now(),
                        ]);
                    }

                    $balance = $customer_balance->balance - $total_payment;
                    if ($balance < 0) {
                        $claimed_payment = $customer_balance->balance;
                        $customer_payment = $total_payment - $claimed_payment;
                        $balance = 0;
                    }
                    $maximum_used_balance = null;
                }
                // Jika productnya tidak digunakan per-tahun (unlimited use)
                else {
                    $balance = $customer_balance->balance;
                    $maximum_used_balance = null;
                }
            }

            $customer_balance->update([
                'balance' => $balance,
                'maximum_used_balance' => $maximum_used_balance,
            ]);

            // Add payment for result response
            $payment = new stdClass;
            $payment->product = $product_detail->item;
            $payment->payment_amount = $item['payment'] - 0;
            $payment->quantity = $item['duration'];
            $payment->total_payment = $total_payment;
            $payment->covered_payment = $claimed_payment;
            $payment->customer_payment = $customer_payment;
            $payments[] = $payment;

            TransactionDetail::create([
                'transaction_id' => $transaction->id,
                'product_detail_id' => $product_detail->id,
                'payment_amount' => $item['payment'],
                'quantity' => $duration,
                'total_payment' => $total_payment,
                'covered' => $claimed_payment,
                'customer_pay' => $customer_payment,
            ]);
        }

        $total_covered = TransactionDetail::where('transaction_id', $transaction->id)->sum('covered');
        $total_customer_pay = TransactionDetail::where('transaction_id', $transaction->id)->sum('customer_pay');

        $transaction->update([
            'total_covered' => $total_covered,
            'total_customer_pay' => $total_customer_pay
        ]);

        $customer = Customer::with('product')->where('id', $customer->id)->first();

        $result = new stdClass;
        
        $result = [
            'message' => 'Transaction completed successfully',
            'id_transaction' => $transaction->id,
            'transaction_date' => $transaction->created_at->format('d F Y'),
            'customer_name' => $customer->name,
            'customer_no' => $customer->customer_no,
            'customer_product' => $customer->product->product_name,
            'payement' => $payments,
            'total_covered' => $total_covered -0,
            'total_customer_pay' => $total_customer_pay -0,
        ];
        
        // Make pdf
        $transaction = Transaction::with(['customer' => function($queryCustomer){
            $queryCustomer->with(['product' => function ($queryProduct) {
                $queryProduct->select('id', 'product_name');
            }])->select('id', 'product_id', 'name', 'customer_no', 'gender', 'age');
        }, 'transaction_detail' => function($queryTransactionDetail){
            $queryTransactionDetail->with(['product_detail' => function ($queryProductDetail){
                $queryProductDetail->select('id', 'item');
            }])->select('id', 'transaction_id', 'product_detail_id', 'total_payment', 'payment_amount', 'quantity', 'covered', 'customer_pay');
        }])->where('id', $transaction->id)->first();
        
        return response_json(200, 'success', $result);
    }

    public function history(){
        $transaction = Transaction::with(['customer' => function($queryCustomer){
            $queryCustomer->select('id', 'name', 'customer_no');
        }])->latest()->get();
        
        if ($transaction->first() != null) {
            return response_json(200, 'success', $transaction);
        }
        return response_json(404, 'failed', 'There is no transaction');
    }

    public function history_detail(Request $request){
        $transaction = Transaction::with(['customer' => function($queryCustomer){
            $queryCustomer->with(['product' => function ($queryProduct) {
                $queryProduct->select('id', 'product_name');
            }])->select('id', 'product_id', 'name', 'customer_no', 'gender', 'age');
        }, 'transaction_detail' => function($queryTransactionDetail){
            $queryTransactionDetail->with(['product_detail' => function ($queryProductDetail){
                $queryProductDetail->select('id', 'item');
            }])->select('id', 'transaction_id', 'product_detail_id', 'total_payment', 'payment_amount', 'quantity', 'covered', 'customer_pay');
        }])->where('id', $request->id)->first();
        
        if ($transaction != null) {
            return response_json(200, 'success', $transaction);
        }
        return response_json(404, 'failed', 'Transaction not found');
    }

    public function download_struk(Request $request){
        // Make pdf
        $result = Transaction::with(['customer' => function($queryCustomer){
            $queryCustomer->with(['product' => function ($queryProduct) {
                $queryProduct->select('id', 'product_name');
            }])->select('id', 'product_id', 'name', 'customer_no', 'gender', 'age');
        }, 'transaction_detail' => function($queryTransactionDetail){
            $queryTransactionDetail->with(['product_detail' => function ($queryProductDetail){
                $queryProductDetail->select('id', 'item');
            }])->select('id', 'transaction_id', 'product_detail_id', 'total_payment', 'payment_amount', 'quantity', 'covered', 'customer_pay');
        }])->where('id', $request->id_transaction)->first();

        $html = view('template', [
            'result' => $result,
       ])->render();

       $options = new Options();
       $options->set('isHtml5ParserEnabled', true);
       $options->set('isPhpEnabled', true);
       
       $dompdf = new Dompdf($options);
       
       $dompdf->loadHtml($html);
       
       $dompdf->setPaper('A4', 'portrait');

       $dompdf->render();

       return $dompdf->stream('transaksi' . $result->customer->name . '.pdf');
    }

    public function export_transaction()
    {
        return Excel::download(new TransactionExport(), now() . '_transaction_export.xlsx');
    }
}
