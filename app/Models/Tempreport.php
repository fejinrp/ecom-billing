<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tempreport extends Model
{
    use HasFactory;

    protected $table = 'tempreport';
    public $timestamps = false;

    protected $fillable = [
        'pro_id',
        'pname',
        'qty',
        'amount'
    ];
}