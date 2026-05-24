<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expname extends Model
{
    use HasFactory;

    protected $table = 'expname';
    protected $primaryKey = 'exp_id';
    public $timestamps = false;

    protected $fillable = [
        'exp_id',
        'exp_name',
        'estatus'
    ];
}