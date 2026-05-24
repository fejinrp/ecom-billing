<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Retproduct extends Model
{
    use HasFactory;

    protected $table = 'retproduct';
    public $timestamps = false;

    protected $fillable = [
        'ret_id',
        'ret_date',
        'order_itemid',
        'ret_qty',
        'ret_amount',
        'order_id',
        'product_id'
    ];
}