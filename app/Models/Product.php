<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function product_detail(){
        return $this->hasMany(ProductDetail::class);
    }

    public function customer(){
        return $this->hasMany(Customer::class);
    }
}
