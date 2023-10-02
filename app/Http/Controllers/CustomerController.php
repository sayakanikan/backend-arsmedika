<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Models\ProductDetail;
use App\Models\CustomerBalance;
use Illuminate\Support\Facades\Validator;

class CustomerController extends Controller
{
    public function index(){
        $customer = Customer::with(['product' => function ($queryProduct) {
            $queryProduct->select('id', 'product_name');
        }])->latest()->get();

        if ($customer->first() != null) {
            return response_json(200, 'success', $customer);
        }
        return response_json(404, 'failed', 'There is no customer');
    }

    public function detail(Request $request){
        $id = $request->id;

        $customer = Customer::with(['product' => function ($queryProduct) {
            $queryProduct->select('id', 'product_name');
        }])->where('id', $id)->first();

        if ($customer != null) {
            return response_json(200, 'success', $customer);
        }
        return response_json(404, 'failed', 'Customer not found');
    }

    public function create(Request $request){
        // Validate input
        $validator = Validator::make($request->all(), [
            'product_id' => 'required',
            'name' => 'required',
            'gender' => 'required',
            'age' => 'required',
        ]);

        if ($validator->fails()) {
            return response_json(422, 'failed', $validator->messages());
        }

        // Create customer account
        $customer = Customer::create([
            'product_id' => $request->product_id,
            'name' => $request->name,
            'customer_no' => 'R',
            'gender' => $request->gender,
            'age' => $request->age,
            'premi' => $this->getPremi($request->product_id, $request->gender, $request->age),
        ]);

        $customer->update([
            'customer_no' => 'R' . str_pad($customer->id, 12, '0', STR_PAD_LEFT),
        ]);

        // Create customer balance
        $product_detail = ProductDetail::whereHas('product_value', function ($query) use ($request) {
            $query->where('product_id', $request->product_id);
        })
        ->with(['product_value' => function($queryValue) use ($request) {
            $queryValue->where('product_id', $request->product_id)
            ->select('id', 'product_id', 'product_detail_id', 'value');
        }])
        ->get();

        foreach ($product_detail as $item) {
            CustomerBalance::create([
                'customer_id' => $customer->id,
                'product_id' => $request->product_id,
                'product_detail_id' => $item->id,
                'balance' => $item->product_value[0]->value,
                'maximum_used_balance' => $item->maximum_used
            ]);
        }

        // return response
        if (!$customer) {
            return response_json(400, 'failed', 'Error saving customer data');
        }
        
        return response_json(200, 'success', $request->name . ' successfully saved as a customer');
    }

    public function update(Request $request){
        // Validate input
        $validator = Validator::make($request->all(), [
            'product_id' => 'required',
            'name' => 'required',
            'gender' => 'required',
            'age' => 'required',
        ]);

        if ($validator->fails()) {
            return response_json(422, 'failed', $validator->messages());
        }

        // Update customer account
        $old_data = Customer::where('id', $request->id)->first();
        $customer = Customer::where('id', $request->id)->first();
        if ($customer == null) {
            return response_json(404, 'failed', 'Customer not found');
        }
        $customer->update([
            'product_id' => $request->product_id,
            'name' => $request->name,
            'gender' => $request->gender,
            'age' => $request->age,
            'premi' => $this->getPremi($request->product_id, $request->gender, $request->age),
        ]);

        // Update customer balance if customer changed their product
        if ($old_data->product_id != $customer->product_id) {
            // Delete customer balance old data
            $product_detail = ProductDetail::whereHas('product_value', function ($query) use ($old_data) {
                $query->where('product_id', $old_data->product_id);
            })
            ->with(['product_value' => function($queryValue) use ($old_data) {
                $queryValue->where('product_id', $old_data->product_id)
                ->select('id', 'product_id', 'product_detail_id', 'value');
            }])
            ->get();
            foreach ($product_detail as $item) {
                CustomerBalance::where('customer_id', $request->id)->where('product_detail_id', $item->id)->delete();
            }

            // Create new customer balance
            $new_product_detail = ProductDetail::whereHas('product_value', function ($query) use ($customer) {
                $query->where('product_id', $customer->product_id);
            })
            ->with(['product_value' => function($queryValue) use ($customer) {
                $queryValue->where('product_id', $customer->product_id)
                ->select('id', 'product_id', 'product_detail_id', 'value');
            }])
            ->get();
            foreach ($new_product_detail as $item) {
                CustomerBalance::create([
                    'customer_id' => $request->id,
                    'product_id' => $request->product_id,
                    'product_detail_id' => $item->id,
                    'balance' => $item->product_value[0]->value,
                    'maximum_used_balance' => $item->maximum_used,
                ]);
            }
        }

        // return response
        if (!$customer) {
            return response_json(400, 'failed', 'Error saving customer data');
        }
        
        return response_json(200, 'success', $request->name . " data updated successfully");
    }

    public function delete(Request $request){
        // Delete customer account
        $customer = Customer::where('id', $request->id)->first();

        if ($customer != null) {
            $customer->delete();
            // Delete customer balance
            CustomerBalance::where('customer_id', $request->id)->delete();
            
            return response_json(200, 'success', 'Customer deleted successfully');
        }
        
        return response_json(404, 'failed', 'Customer not found');
    }

    public function customer_transaction(Request $request){
        $id = $request->id;

        $transaction = Transaction::with(['customer' => function($queryCustomer){
            $queryCustomer->select('id', 'name', 'customer_no', 'gender', 'age');
        }, 'transaction_detail' => function($queryTransactionDetail){
            $queryTransactionDetail->with(['product_detail' => function ($queryProductDetail){
                $queryProductDetail->select('id', 'item');
            }])->select('id', 'transaction_id', 'product_detail_id', 'covered', 'customer_pay');
        }])->where('customer_id', $id)->get();

        if ($transaction != null) {
            return response_json(200, 'success', $transaction);
        }
        return response_json(404, 'failed', 'Customer not found');
    }

    protected function getPremi($product_id, $gender, $age){
        if ($product_id == 1) {
            if ($gender == 'L') {
                $premi = 750000;
            } else if ($gender == 'P') {
                $premi = 850000;
            }

            if ($age < 12) {
                $premi = 825000;
            }
        } else if ($product_id == 2) {
            if ($gender == 'L') {
                $premi = 1250000;
            } else if ($gender == 'P') {
                $premi = 1350000;
            }

            if ($age < 12) {
                $premi = 1300000;
            }
        } else if ($product_id == 3) {
            if ($gender == 'L') {
                $premi = 1750000;
            } else if ($gender == 'P') {
                $premi = 2000000;
            }

            if ($age < 12) {
                $premi = 1800000;
            }
        } else if ($product_id == 4) {
            if ($gender == 'L') {
                $premi = 2450000;
            } else if ($gender == 'P') {
                $premi = 2700000;
            }

            if ($age < 12) {
                $premi = 2500000;
            }
        }

        return $premi;
    }
}
