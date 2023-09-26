<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function transaction(){
        return $this->hasMany(Transaction::class);
    }

    public function product(){
        return $this->belongsTo(Product::class);
    }

    public function customer_balance(){
        return $this->hasOne(CustomerBalance::class);
    }
}
