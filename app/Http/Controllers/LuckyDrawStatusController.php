<?php

namespace App\Http\Controllers;

use App\Models\LuckyDrawSetting;
use App\Models\LuckyDrawWinner;
use App\Models\Order;
use App\Models\Uorder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LuckyDrawStatusController extends Controller
{
    /**
     * Public Lucky Draw status board for customers.
     * - Shows all active draw categories with live pool progress
     * - Shows full Hall of Winners history
     * - If customer is logged in, shows their own eligibility and any wins
     */
    public function index()
    {
        $settings = LuckyDrawSetting::active()->orderBy('min_amount')->get();
        $winners  = LuckyDrawWinner::orderBy('id', 'desc')->with('categorySetting')->take(50)->get();
        $categories = \App\Models\Category::orderBy('cat_name', 'asc')->get();

        $categoryPools = $settings->map(function (LuckyDrawSetting $setting) {
            $batchCount = LuckyDrawWinner::where('category', $setting->category_key)->count();
            $count      = $this->countEligiblePool($setting);
            $needed     = $setting->batch_size;
            $progress   = $needed > 0 ? min(($count / $needed) * 100, 100) : 0;

            return [
                'setting'    => $setting,
                'count'      => $count,
                'needed'     => $needed,
                'progress'   => round($progress),
                'batchCount' => $batchCount,
                'isFull'     => $count >= $needed,
                'justDrawn'  => $batchCount > 0 && $count === 0,
            ];
        });

        // Customer eligibility — only if logged in
        $myStatus = null;
        if (Auth::check()) {
            $user = Auth::user();
            $myStatus = $this->getCustomerStatus($user);
        }

        return view('storefront.lucky_draw', compact('categoryPools', 'winners', 'myStatus', 'categories'));
    }

    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Count eligible pool size for a category (no data exposure — count only).
     */
    private function countEligiblePool(LuckyDrawSetting $setting): int
    {
        $batchSize = $setting->batch_size;

        $offlineCount = Order::where('order_status', 1)
            ->where('due', 0)
            ->whereNull('lucky_draw_batch_no')
            ->where('grand_total', '>=', $setting->min_amount)
            ->when(!is_null($setting->max_amount), fn ($q) => $q->where('grand_total', '<=', $setting->max_amount))
            ->take($batchSize)
            ->count();

        $remaining = max(0, $batchSize - $offlineCount);

        $onlineCount = $remaining > 0
            ? Uorder::where('ostatus', '1')
                ->where('bamount', 0)
                ->whereNull('lucky_draw_batch_no')
                ->where('total', '>=', $setting->min_amount)
                ->when(!is_null($setting->max_amount), fn ($q) => $q->where('total', '<=', $setting->max_amount))
                ->take($remaining)
                ->count()
            : 0;

        return $offlineCount + $onlineCount;
    }

    /**
     * Get the logged-in customer's lucky draw status:
     *   - Their eligible online orders (in current pools)
     *   - Their past wins
     */
    private function getCustomerStatus($user): array
    {
        // Check if any of their online orders are in an active pool
        $eligibleOrders = Uorder::where('userid', $user->id)
            ->where('ostatus', '1')
            ->where('bamount', 0)
            ->whereNull('lucky_draw_batch_no')
            ->orderBy('orderid', 'desc')
            ->get(['orderid', 'total', 'orderdate']);

        // Check if any of their online orders have been in past draws
        $drawnOrders = Uorder::where('userid', $user->id)
            ->whereNotNull('lucky_draw_batch_no')
            ->orderBy('orderid', 'desc')
            ->get(['orderid', 'total', 'lucky_draw_batch_no', 'lucky_draw_cat']);

        // Check if they've won
        $myWins = LuckyDrawWinner::where('source', 'online')
            ->where('winner_mobile', $user->contactno ?? '')
            ->orderBy('id', 'desc')
            ->with('categorySetting')
            ->get();

        return [
            'eligible_orders' => $eligibleOrders,
            'drawn_orders'    => $drawnOrders,
            'wins'            => $myWins,
            'is_eligible'     => $eligibleOrders->isNotEmpty(),
        ];
    }
}
