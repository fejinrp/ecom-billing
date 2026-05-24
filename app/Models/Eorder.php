<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Eorder extends Model
{
    use HasFactory;

    protected $table = 'eorders';
    public $timestamps = false;

    protected $fillable = [
        'order_id',
        'order_date',
        'client_name',
        'client_contact',
        'sub_total',
        'grand_total',
        'gstn',
        'user_id',
        'morder_id',
        'mobile',
        'gsttin',
        'instamt',
        'shipamt',
        'status'
    ];
}