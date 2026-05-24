<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stategst extends Model
{
    use HasFactory;

    protected $table = 'stategst';
    protected $primaryKey = 'sid';
    public $timestamps = false;

    protected $fillable = [
        'sid',
        'sname',
        'scode',
        'status'
    ];
}