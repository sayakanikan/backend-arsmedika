<?php

namespace App\Exports;

use App\Models\Transaction;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;

class TransactionExport extends DefaultValueBinder implements FromView, WithCustomValueBinder, WithStyles, ShouldAutoSize
{
    /**
    * @return \Illuminate\Support\Collection
    */
    // public function collection()
    // {
    //     return Transaction::with(['transaction_detail', 'customer'])->get();
    //     // return Transaction::all();
    // }

    public function styles(Worksheet $sheet){
        return [
            1    => ['font' => ['bold' => true]],
        ];
    }

    public function bindValue(Cell $cell, $value)
    {
        if (is_numeric($value)) {
            $cell->setValueExplicit($value, DataType::TYPE_STRING);

            return true;
        }

        // else return default behavior
        return parent::bindValue($cell, $value);
    }

    public function view(): View
    {
        $transactions = Transaction::with(['customer' => function($queryCustomer){
            $queryCustomer->with(['product' => function ($queryProduct) {
                $queryProduct->select('id', 'product_name');
            }])->select('id', 'product_id', 'name', 'customer_no', 'gender', 'age');
        }, 'transaction_detail' => function($queryTransactionDetail){
            $queryTransactionDetail->with(['product_detail' => function ($queryProductDetail){
                $queryProductDetail->select('id', 'item', 'code');
            }])->select('id', 'transaction_id', 'product_detail_id', 'total_payment', 'payment_amount', 'quantity', 'covered', 'customer_pay');
        }])->get();

        return view('export_template', compact(['transactions']));
    }
}
