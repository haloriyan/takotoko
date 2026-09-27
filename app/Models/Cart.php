<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    protected $fillable = [
        'store_id', 'user_id', 'product_id', 'stock_id', 'composition_id',
        'price', 'quantity', 'total_price', 'notes', 'is_composition', 'cart_parent_id'
    ];

    public function product() {
        return $this->belongsTo(Product::class, 'product_id');
    }
    public function stock() {
        return $this->belongsTo(ProductStock::class, 'stock_id');
    }

    public function children() {
        return $this->hasMany(Cart::class, 'cart_parent_id');
    }
    public function composition() {
        return $this->belongsTo(ProductComposition::class, 'composition_id');
    }
}
