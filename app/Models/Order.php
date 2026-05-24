<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $table = 'orders';
    protected $primaryKey = 'order_id';
    public $timestamps = false;

    protected $fillable = [
        'order_id',
        'order_date',
        'client_name',
        'client_contact',
        'sub_total',
        'total_amount',
        'discount',
        'grand_total',
        'paid',
        'due',
        'payment_type',
        'payment_status',
        'payment_place',
        'gstn',
        'order_status',
        'user_id',
        'paymentname',
        'morder_id',
        'mobile',
        'gsttin',
        'section',
        'instamt',
        'shipamt',
        'mcoin',
        'bcoin',
        'tcoin',
        'pcoin'
    ];

    public function items()
    {
        return $this->hasMany(OrderItem::class, 'order_id', 'order_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}