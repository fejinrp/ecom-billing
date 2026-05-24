<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mtlstock extends Model
{
    use HasFactory;

    protected $table = 'mtlstock';
    protected $primaryKey = 'mtlid';
    public $timestamps = false;

    protected $fillable = [
        'mtlid',
        'stockdate',
        'cashhand',
        'openstock',
        'closestock',
        'todaystock',
        'uname'
    ];
}