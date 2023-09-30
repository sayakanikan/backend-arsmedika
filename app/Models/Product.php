<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function product_value(){
        return $this->hasMany(ProductValue::class);
    }

    public function customer(){
        return $this->hasMany(Customer::class);
    }

    public function customer_balance(){
        return $this->hasMany(CustomerBalance::class);
    }
}
