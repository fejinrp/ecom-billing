<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UorderItem extends Model
{
    use HasFactory;

    protected $table = 'uorders';
    public $timestamps = false;

    protected $fillable = [
        'id',
        'userId',
        'productId',
        'quantity',
        'cprice',
        'hsnsan',
        'srate',
        'gst',
        'unit',
        'orderid',
        'price',
        'slno'
    ];

    public function order()
    {
        return $this->belongsTo(Uorder::class, 'orderid', 'orderid');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'productId', 'id');
    }
}
