<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SorderItem extends Model
{
    use HasFactory;

    protected $table = 'sorder_item';
    public $timestamps = false;

    protected $fillable = [
        'item_id',
        'order_id',
        'sname',
        'qty',
        'rate',
        'total'
    ];
}