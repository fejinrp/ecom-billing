<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryNote extends Model
{
    use HasFactory;

    protected $table = 'delivery_notes';

    protected $fillable = [
        'dn_number',
        'type',
        'porder_id',
        'order_id',
        'status',
        'carrier_info',
        'dn_date',
    ];

    protected $casts = [
        'dn_date' => 'date',
    ];

    public function items()
    {
        return $this->hasMany(DeliveryNoteItem::class, 'delivery_note_id');
    }

    public function purchaseOrder()
    {
        return $this->belongsTo(POrder::class, 'porder_id', 'porder_id');
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id', 'id');
    }
}
