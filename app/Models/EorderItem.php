<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EorderItem extends Model
{
    use HasFactory;

    protected $table = 'eorder_item';
    public $timestamps = false;

    protected $fillable = [
        'item_id',
        'order_id',
        'product_id',
        'hsnsan',
        'gst',
        'qty',
        'rate',
        'unit',
        'total'
    ];
}