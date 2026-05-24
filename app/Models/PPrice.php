<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PPrice extends Model
{
    use HasFactory;

    protected $table = 'p_price';
    public $timestamps = false;

    protected $fillable = [
        'price_id',
        'product_id',
        'brand_id',
        'cat_id',
        'tsheet',
        'psheet',
        'quantity',
        'iunit',
        'p_gst',
        'p_pur',
        'p_mrp',
        'p_sel',
        'active',
        'status'
    ];
}