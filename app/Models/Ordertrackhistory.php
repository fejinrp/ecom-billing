<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ordertrackhistory extends Model
{
    use HasFactory;

    protected $table = 'ordertrackhistory';
    public $timestamps = false;

    protected $fillable = [
        'id',
        'orderId',
        'status',
        'remark',
        'postingDate'
    ];
}