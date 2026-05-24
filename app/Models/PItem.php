<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PItem extends Model
{
    use HasFactory;

    protected $table = 'p_item';
    protected $primaryKey = 'pitem_id';
    public $timestamps = false;

    protected $fillable = [
        'pitem_id',
        'porder_id',
        'prod_id',
        'rate',
        'punit',
        'tqty',
        'pqty',
        'qty',
        'tamount',
        'bqty',
        'status',
        'slno'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'prod_id', 'id');
    }

    public function purchaseOrder()
    {
        return $this->belongsTo(POrder::class, 'porder_id', 'porder_id');
    }
}