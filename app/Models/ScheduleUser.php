<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ScheduleUser extends Model
{
    protected $fillable = [
        'store_id', 'user_id', 'schedule_id', 'date',
        'check_in_at', 'check_in_latitude', 'check_in_longitude', 'check_in_address', 'check_in_image',
        'check_out_at', 'check_out_latitude', 'check_out_longitude', 'check_out_address', 'check_out_image',
        'check_in_diff', 'check_out_diff', 'in_out_diff'
    ];

    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function schedule() {
        return $this->belongsTo(Schedule::class, 'schedule_id');
    }
}
