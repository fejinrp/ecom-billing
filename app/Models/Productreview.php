<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Productreview extends Model
{
    use HasFactory;

    protected $table = 'productreviews';
    public $timestamps = false;

    protected $fillable = [
        'id',
        'productId',
        'quality',
        'price',
        'value',
        'name',
        'summary',
        'review',
        'reviewDate'
    ];
}