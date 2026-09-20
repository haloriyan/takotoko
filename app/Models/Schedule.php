<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    protected $fillable = [
        'store_id', 'date', 'label', 'check_in_time', 'check_out_time', 'duration'
    ];

    public function users() {
        return $this->hasMany(ScheduleUser::class, 'schedule_id');
    }
}
