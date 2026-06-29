<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Auser extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'ausers';
    protected $primaryKey = 'user_id';
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'username',
        'password',
        'email',
        'mobile',
        'section',
        'ustatus',
        'hourly_rate',
        'shift_start',
        'shift_end',
        'mac_address',
        'permissions'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function attendances()
    {
        return $this->hasMany(Attendance::class, 'user_id');
    }
}