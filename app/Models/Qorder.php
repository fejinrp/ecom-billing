<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Qorder extends Model
{
    use HasFactory;

    protected $table = 'qorders';
    protected $primaryKey = 'order_id';
    public $timestamps = false;

    protected $fillable = [
        'order_id',
        'order_date',
        'client_name',
        'client_contact',
        'sub_total',
        'grand_total',
        'gstn',
        'user_id',
        'morder_id',
        'mobile',
        'gsttin',
        'instamt',
        'shipamt',
        'status',
        'qtype',
        'signa',
        'discount',
        'gtotal',
        'qstate'
    ];

    public function items()
    {
        return $this->hasMany(QorderItem::class, 'order_id', 'order_id');
    }

    public function user()
    {
        return $this->belongsTo(Auser::class, 'user_id', 'user_id');
    }
}