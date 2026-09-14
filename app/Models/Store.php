<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    protected $fillable = [
        'package', 'name', 'username', 'icon', 'address', 'latitude', 'longitude', 'max_radius_attendance',
        'inventory_method',
    ];

    public function plan() {
        return $this->hasOne(
            StorePlan::class, 'store_id'
        )->orderBy('expired_at', 'DESC');
    }
    public function plans() {
        return $this->hasMany(
            StorePlan::class, 'store_id'
        );
    }
    public function active_plan() {
        return $this->hasOne(
            StorePlan::class, 'store_id'
        )->where('payment_status', 'PAID')
        ->orderBy('created_at', 'DESC');
    }
}
