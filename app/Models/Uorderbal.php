<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Uorderbal extends Model
{
    use HasFactory;

    protected $table = 'uorderbal';
    public $timestamps = false;

    protected $fillable = [
        'balid',
        'orderid',
        'gtotal',
        'pamount',
        'bamount',
        'ptype',
        'pdate'
    ];
}