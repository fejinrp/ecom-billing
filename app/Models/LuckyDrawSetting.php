<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LuckyDrawSetting extends Model
{
    protected $table = 'lucky_draw_settings';

    protected $fillable = [
        'category_key',
        'category_label',
        'min_amount',
        'max_amount',
        'batch_size',
        'prize_amount',
        'is_active',
    ];

    protected $casts = [
        'min_amount'   => 'decimal:2',
        'max_amount'   => 'decimal:2',
        'prize_amount' => 'decimal:2',
        'is_active'    => 'boolean',
    ];

    /**
     * Get only active draw categories.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get the winners that belong to this category.
     */
    public function winners()
    {
        return $this->hasMany(LuckyDrawWinner::class, 'category', 'category_key');
    }

    /**
     * Human-readable amount range label.
     */
    public function getAmountRangeLabelAttribute(): string
    {
        $min = '₹' . number_format((float) $this->min_amount, 0);

        if (is_null($this->max_amount)) {
            return "{$min} and above";
        }

        $max = '₹' . number_format((float) $this->max_amount, 0);
        return "{$min} – {$max}";
    }
}
