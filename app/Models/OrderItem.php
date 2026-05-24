<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    protected $table = 'order_item';
    protected $primaryKey = 'item_id';
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
        'total',
        'status',
        'slno'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id', 'order_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }
}