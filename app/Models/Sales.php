<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sales extends Model
{
    protected $fillable = [
        'store_id', 'user_id', 'customer_id', 'movement_id',
        'invoice_number', 'total_quantity', 'total_price', 'total_margin', 'notes',
        'fee', 'total_pay', 'payment_method', 'payment_status', 'payment_payload', 'has_payout'
    ];

    public function store() {
        return $this->belongsTo(Store::class, 'store_id');
    }
    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function customer() {
        return $this->belongsTo(Customer::class, 'customer_id');
    }
    public function items() {
        return $this->hasMany(SalesItem::class, 'sales_id');
    }
    public function review() {
        return $this->hasOne(Review::class, 'sales_id');
    }
}
