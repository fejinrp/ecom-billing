<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $table = 'products';
    public $timestamps = false;

    protected $fillable = [
        'id',
        'brandid',
        'catid',
        'subcatid',
        'productname',
        'unit',
        'productdes',
        'pqty',
        'tqty',
        'pfrom',
        'prate',
        'srate',
        'mrp',
        'gst',
        'cprice',
        'dprice',
        'status',
        'pimagef',
        'pimages',
        'pimaget',
        'hsnsac',
        'slno',
        'postingdate',
        'updationdate',
        'sdprice',
        'pcode',
        'warranty_months'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class, 'catid', 'cat_id');
    }

    public function subcategory()
    {
        return $this->belongsTo(Subcategory::class, 'subcatid', 'id');
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class, 'brandid', 'brand_id');
    }

    public function batches()
    {
        return $this->hasMany(ProductBatch::class, 'product_id');
    }

    public function activeBatches()
    {
        return $this->batches()
            ->where('status', 1)
            ->where('current_qty', '>', 0)
            ->where(function($q) {
                $q->whereNull('expiry_date')
                  ->orWhere('expiry_date', '>=', now()->toDateString());
            });
    }

    public function getPrimaryImageUrlAttribute(): ?string
    {
        if (!$this->pimagef) {
            return null;
        }

        return asset('productimage/' . $this->id . '/' . rawurlencode($this->pimagef));
    }

    public function getDisplayPriceAttribute()
    {
        // Fetch oldest active batch with stock
        $oldestBatch = $this->activeBatches()->orderBy('id', 'asc')->first();
        $source = $oldestBatch ?: $this;

        // Default base price (Customer)
        $price = $source->cprice ?: ($source->srate ?: $source->mrp);

        if (\Illuminate\Support\Facades\Auth::check()) {
            $userType = \Illuminate\Support\Facades\Auth::user()->usertype;
            if ($userType === 'D') {
                $price = $source->dprice ?: $price;
            } elseif ($userType === 'S') {
                $price = $source->sdprice ?: $price;
            }
        }
        
        return $price;
    }

    public function getDisplayPriceLabelAttribute()
    {
        if (\Illuminate\Support\Facades\Auth::check()) {
            $userType = \Illuminate\Support\Facades\Auth::user()->usertype;
            if ($userType === 'D') {
                return 'Dealer Price';
            } elseif ($userType === 'S') {
                return 'Super Dealer Price';
            }
        }
        return 'Customer Price';
    }
}
