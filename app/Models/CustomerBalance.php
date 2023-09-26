<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerBalance extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function product_detail(){
        return $this->belongsTo(ProductDetail::class);
    }

    public function customer(){
        return $this->belongsTo(Customer::class);
    }
}
