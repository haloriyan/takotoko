<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductComposition extends Model
{
    protected $fillable = [
        'product_id', 'composition_id', 'quantity'
    ];

    public function product() {
        return $this->belongsTo(Product::class, 'product_id');
    }
    public function composition() {
        return $this->belongsTo(Product::class, 'composition_id');
    }
}
