<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductBatch extends Model
{
    use HasFactory;

    protected $table = 'product_batches';

    protected $fillable = [
        'product_id',
        'batch_number',
        'mfg_date',
        'expiry_date',
        'initial_qty',
        'current_qty',
        'warranty_months',
        'prate',
        'srate',
        'mrp',
        'cprice',
        'dprice',
        'sdprice',
        'status',
    ];

    protected $casts = [
        'mfg_date' => 'date',
        'expiry_date' => 'date',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }
}
