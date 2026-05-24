<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Proddetail extends Model
{
    use HasFactory;

    protected $table = 'proddetails';
    public $timestamps = false;

    protected $fillable = [
        'product_id',
        'product_name',
        'product_active',
        'product_status'
    ];
}