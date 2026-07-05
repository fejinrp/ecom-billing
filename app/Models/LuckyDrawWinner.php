<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LuckyDrawWinner extends Model
{
    protected $table = 'lucky_draw_winners';

    protected $fillable = [
        'category',
        'batch_no',
        'source',
        'order_id',
        'uorder_id',
        'winner_name',
        'winner_mobile',
        'prize_amount',
        'drawn_by',
    ];

    protected $casts = [
        'prize_amount' => 'decimal:2',
    ];

    /**
     * Offline (walk-in) order relationship.
     */
    public function offlineOrder()
    {
        return $this->belongsTo(Order::class, 'order_id', 'order_id');
    }

    /**
     * Online order relationship.
     */
    public function onlineOrder()
    {
        return $this->belongsTo(Uorder::class, 'uorder_id', 'orderid');
    }

    /**
     * Admin user who performed the draw.
     */
    public function drawnBy()
    {
        return $this->belongsTo(Auser::class, 'drawn_by', 'user_id');
    }

    /**
     * Category settings relationship.
     */
    public function categorySetting()
    {
        return $this->belongsTo(LuckyDrawSetting::class, 'category', 'category_key');
    }

    /**
     * Returns the coupon/invoice reference number shown to the admin.
     */
    public function getCouponReferenceAttribute(): string
    {
        if ($this->source === 'offline' && $this->order_id) {
            return '#' . $this->order_id;
        }
        if ($this->source === 'online' && $this->uorder_id) {
            return 'ONL-' . $this->uorder_id;
        }
        return 'N/A';
    }

    /**
     * Masked mobile for public display.
     */
    public function getMaskedMobileAttribute(): string
    {
        $mobile = $this->winner_mobile;
        if (strlen($mobile) < 4) {
            return $mobile;
        }
        return substr($mobile, 0, 2) . '******' . substr($mobile, -2);
    }
}
