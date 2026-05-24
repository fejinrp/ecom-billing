<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class POrder extends Model
{
    use HasFactory;

    protected $table = 'p_orders';
    protected $primaryKey = 'porder_id';
    public $timestamps = false;

    protected $fillable = [
        'porder_id',
        'porder_date',
        's_name',
        's_contact',
        'staffname',
        'sub_total',
        'vat',
        't_amount',
        'discount',
        'g_total',
        'ppaid',
        'pbal',
        'gstn',
        'porder_status',
        'morder_id'
    ];
}