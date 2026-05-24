<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sorder extends Model
{
    use HasFactory;

    protected $table = 'sorders';
    public $timestamps = false;

    protected $fillable = [
        'order_id',
        'order_date',
        'client_name',
        'client_contact',
        'sub_total',
        'lamount',
        'pamount',
        'bamount',
        'grand_total',
        'user_id',
        'morder_id',
        'mobile',
        'status'
    ];
}