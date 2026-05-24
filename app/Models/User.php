<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'users';
    public $timestamps = false;

    protected $fillable = [
        'id',
        'uname',
        'email',
        'contactno',
        'password',
        'usertype',
        'ustatus',
        'shippingaddress',
        'shippingstate',
        'shippingcity',
        'shippingpincode',
        'billingaddress',
        'billingstate',
        'billingcity',
        'billingpincode',
        'regdate',
        'updationdate',
        'gsttin',
        'mcoin',
        'mcoinp',
        'mcoinb'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];
}