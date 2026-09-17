<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $fillable = [
        'store_id', 'sales_id', 'customer_id', 'rate', 'body'
    ];

    public function store() {
        return $this->belongsTo(Store::class, 'store_id');
    }
    public function customer() {
        return $this->belongsTo(Customer::class, 'customer_id');
    }
    public function sales() {
        return $this->belongsTo(Sales::class, 'sales_id');
    }
}
