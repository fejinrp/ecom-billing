<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    use HasFactory;

    protected $table = 'brands';
    protected $primaryKey = 'brand_id';
    public $timestamps = false;

    protected $fillable = [
        'brand_id',
        'brand_name',
        'catid',
        'scatid',
        'brand_status'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class, 'catid', 'cat_id');
    }

    public function subcategory()
    {
        return $this->belongsTo(Subcategory::class, 'scatid', 'id');
    }
}