<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $fillable = [
        'user_id', 'date', 'punch_in', 'punch_out', 
        'punch_in_lat', 'punch_in_long', 
        'punch_out_lat', 'punch_out_long',
        'punch_in_device', 'punch_out_device',
        'punch_in_ip', 'punch_out_ip',
        'status', 'notes', 'total_hours', 'overtime_hours', 'overtime_minutes', 'late_minutes', 'early_exit_minutes', 'earned_salary', 'basic_earned', 'overtime_earned'
    ];

    public function user()
    {
        return $this->belongsTo(Auser::class, 'user_id');
    }
}
