<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionDetail extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function transaction(){
        return $this->belongsTo(Transaction::class);
    }

    public function product_detail(){
        return $this->belongsTo(ProductDetail::class);
    }
}
