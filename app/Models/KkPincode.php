<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KkPincode extends Model
{
    use HasFactory;

    protected $table = 'kkpincode';
    public $timestamps = false;

    protected $fillable = [
        'pinid',
        'pincode'
    ];
}