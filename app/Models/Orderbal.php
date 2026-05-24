<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Orderbal extends Model
{
    use HasFactory;

    protected $table = 'orderbal';
    protected $primaryKey = 'order_idbal';
    public $timestamps = false;

    protected $fillable = [
        'order_idbal',
        'order_id',
        'gtotal',
        'pamount',
        'bamount',
        'pdate'
    ];
}