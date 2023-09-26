<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductDetail extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function transaction_detail(){
        return $this->hasMany(TransactionDetail::class);
    }

    public function product(){
        return $this->belongsTo(Product::class);
    }

    public function customer_balance(){
        return $this->hasOne(CustomerBalance::class);
    }
}
