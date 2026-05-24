<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Temporder extends Model
{
    use HasFactory;

    protected $table = 'temporder';
    public $timestamps = false;

    protected $fillable = [
        'pid',
        'go_id',
        'qty'
    ];
}