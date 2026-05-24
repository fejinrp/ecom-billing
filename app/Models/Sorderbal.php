<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sorderbal extends Model
{
    use HasFactory;

    protected $table = 'sorderbal';
    public $timestamps = false;

    protected $fillable = [
        'order_idbal',
        'order_id',
        'gtotal',
        'pamount',
        'bamount',
        'pdate'
    ];
}