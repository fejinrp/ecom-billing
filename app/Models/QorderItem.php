<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QorderItem extends Model
{
    use HasFactory;

    protected $table = 'qorder_item';
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
        'gstr'
    ];

    public function order()
    {
        return $this->belongsTo(Qorder::class, 'order_id', 'order_id');
    }
}