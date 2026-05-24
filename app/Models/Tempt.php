<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tempt extends Model
{
    use HasFactory;

    protected $table = 'tempt';
    public $timestamps = false;

    protected $fillable = [
        'tid',
        'porder_id',
        'pid',
        'qty',
        'rate',
        'total'
    ];
}