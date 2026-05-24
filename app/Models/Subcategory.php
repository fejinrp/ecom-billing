<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subcategory extends Model
{
    use HasFactory;

    protected $table = 'subcategory';
    public $timestamps = false;

    protected $fillable = [
        'id',
        'catid',
        'subcategoryname',
        'creationdate',
        'status'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class, 'catid', 'cat_id');
    }
}