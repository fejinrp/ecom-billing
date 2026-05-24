<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purbal extends Model
{
    use HasFactory;

    protected $table = 'purbal';
    public $timestamps = false;

    protected $fillable = [
        'bal_id',
        'porder_id',
        'gtotal',
        'paid',
        'bal',
        'pdate'
    ];
}