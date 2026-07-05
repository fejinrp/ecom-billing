<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LuckyDrawSetting;
use App\Models\LuckyDrawWinner;
use App\Models\Order;
use App\Models\Uorder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LuckyDrawController extends Controller
{
    /**
     * Display the lucky draw dashboard.
     * Loads all active categories, builds the eligibility pool from both
     * offline (orders) and online (uorder) tables, and fetches winner history.
     */
    public function index()
    {
        $settings  = LuckyDrawSetting::active()->orderBy('min_amount')->get();
        $winners   = LuckyDrawWinner::orderBy('id', 'desc')->with('categorySetting')->get();

        // Build per-category pool data
        $poolData = [];
        foreach ($settings as $setting) {
            $poolData[$setting->category_key] = [
                'setting'       => $setting,
                'eligible'      => $this->buildEligiblePool($setting),
                'batchCount'    => LuckyDrawWinner::where('category', $setting->category_key)->count(),
            ];
        }

        return view('admin.lucky_draw.index', compact('settings', 'winners', 'poolData'));
    }

    /**
     * Perform a lucky draw for the given category.
     */
    public function draw(Request $request)
    {
        $request->validate([
            'category_key' => 'required|string|exists:lucky_draw_settings,category_key',
        ]);

        $setting = LuckyDrawSetting::where('category_key', $request->category_key)
            ->where('is_active', true)
            ->firstOrFail();

        $candidates = $this->buildEligiblePool($setting);

        if ($candidates->count() < $setting->batch_size) {
            $needed = $setting->batch_size - $candidates->count();
            return back()->with('error',
                "Not enough eligible customers for {$setting->category_label}. " .
                "Need {$needed} more fully-paid order(s)."
            );
        }

        DB::beginTransaction();
        try {
            $completedBatches = LuckyDrawWinner::where('category', $setting->category_key)->count();
            $batchNo          = $completedBatches + 1;

            // Take exactly batch_size entries, then stamp them so they can't re-enter the next batch
            $batch = $candidates->take($setting->batch_size);

            // Stamp offline orders
            $offlineIds = $batch->where('source', 'offline')->pluck('id');
            if ($offlineIds->isNotEmpty()) {
                Order::whereIn('order_id', $offlineIds)->update([
                    'lucky_draw_batch_no' => $batchNo,
                    'lucky_draw_cat'      => $setting->category_key,
                ]);
            }

            // Stamp online orders
            $onlineIds = $batch->where('source', 'online')->pluck('id');
            if ($onlineIds->isNotEmpty()) {
                Uorder::whereIn('orderid', $onlineIds)->update([
                    'lucky_draw_batch_no' => $batchNo,
                    'lucky_draw_cat'      => $setting->category_key,
                ]);
            }

            // Pick a random winner from the batch
            $winner = $batch->random();

            LuckyDrawWinner::create([
                'category'      => $setting->category_key,
                'batch_no'      => $batchNo,
                'source'        => $winner['source'],
                'order_id'      => $winner['source'] === 'offline' ? $winner['id'] : null,
                'uorder_id'     => $winner['source'] === 'online'  ? $winner['id'] : null,
                'winner_name'   => $winner['name'],
                'winner_mobile' => $winner['mobile'],
                'prize_amount'  => $setting->prize_amount,
                'drawn_by'      => Auth::guard('admin')->id() ?? null,
            ]);

            DB::commit();

            return back()->with('success',
                "🎉 Batch B-{$batchNo} ({$setting->category_label}) draw complete! " .
                "Winner: {$winner['name']} — Prize: ₹" . number_format($setting->prize_amount, 0)
            );
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Draw failed: ' . $e->getMessage());
        }
    }

    /**
     * Display the lucky draw settings/configuration page.
     */
    public function settings()
    {
        $settings = LuckyDrawSetting::orderBy('min_amount')->get();
        return view('admin.lucky_draw.settings', compact('settings'));
    }

    /**
     * Create or update a lucky draw category setting.
     */
    public function updateSettings(Request $request)
    {
        $request->validate([
            'settings'                       => 'required|array|min:1',
            'settings.*.category_key'        => 'required|string|max:50',
            'settings.*.category_label'      => 'required|string|max:100',
            'settings.*.min_amount'          => 'required|numeric|min:0',
            'settings.*.max_amount'          => 'nullable|numeric|gt:settings.*.min_amount',
            'settings.*.batch_size'          => 'required|integer|min:1',
            'settings.*.prize_amount'        => 'required|numeric|min:0',
            'settings.*.is_active'           => 'sometimes|boolean',
        ]);

        foreach ($request->settings as $row) {
            LuckyDrawSetting::updateOrCreate(
                ['category_key' => $row['category_key']],
                [
                    'category_label' => $row['category_label'],
                    'min_amount'     => $row['min_amount'],
                    'max_amount'     => $row['max_amount'] ?? null,
                    'batch_size'     => $row['batch_size'],
                    'prize_amount'   => $row['prize_amount'],
                    'is_active'      => isset($row['is_active']) ? (bool) $row['is_active'] : false,
                ]
            );
        }

        return back()->with('success', 'Lucky draw settings updated successfully.');
    }

    /**
     * Delete a lucky draw category.
     */
    public function destroySetting($id)
    {
        $setting = LuckyDrawSetting::findOrFail($id);

        // Guard: don't delete a category that already has winners
        if ($setting->winners()->count() > 0) {
            return back()->with('error',
                "Cannot delete '{$setting->category_label}' — it has existing draw history."
            );
        }

        $setting->delete();
        return back()->with('success', "Category '{$setting->category_label}' removed.");
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Private helpers
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Build a unified eligible pool from both offline and online orders for
     * a given draw category setting.
     *
     * Eligibility rules:
     *  - Fully paid  (due = 0 for offline / bamount = 0 for online)
     *  - Delivered   (order_status = 1 for offline / ostatus = 1 for online)
     *  - Not already in a lucky draw batch (lucky_draw_batch_no IS NULL)
     *  - grand_total within the category's amount range
     *
     * @param  LuckyDrawSetting  $setting
     * @return \Illuminate\Support\Collection  [{id, name, mobile, amount, source}]
     */
    private function buildEligiblePool(LuckyDrawSetting $setting): Collection
    {
        $batchSize = $setting->batch_size;

        // ── Offline (walk-in) orders ──────────────────────────────────────────
        $offlineQuery = Order::query()
            ->where('order_status', 1)           // Delivered / completed
            ->where('due', 0)                     // Fully paid
            ->whereNull('lucky_draw_batch_no')    // Not already used in a batch
            ->where('grand_total', '>=', $setting->min_amount)
            ->orderBy('order_id', 'asc')
            ->take($batchSize);                  // ← cap to batch_size (oldest first)

        if (!is_null($setting->max_amount)) {
            $offlineQuery->where('grand_total', '<=', $setting->max_amount);
        }

        $offlineEntries = $offlineQuery->get()->map(fn ($o) => [
            'id'     => $o->order_id,
            'name'   => $o->client_name ?? 'Walk-in Customer',
            'mobile' => $o->mobile ?? '—',
            'amount' => $o->grand_total,
            'source' => 'offline',
        ]);

        // Remaining slots after offline entries
        $remaining = $batchSize - $offlineEntries->count();

        // ── Online orders (registered storefront users) ───────────────────────
        $onlineEntries = collect();

        if ($remaining > 0) {
            $onlineQuery = Uorder::query()
                ->where('ostatus', '1')               // Delivered / completed (string field)
                ->where('bamount', 0)                 // Fully paid (balance amount = 0)
                ->whereNull('lucky_draw_batch_no')    // Not already used in a batch
                ->where('total', '>=', $setting->min_amount)
                ->orderBy('orderid', 'asc')
                ->take($remaining);                   // ← fill remaining slots only

            if (!is_null($setting->max_amount)) {
                $onlineQuery->where('total', '<=', $setting->max_amount);
            }

            $onlineEntries = $onlineQuery->get()->map(fn ($u) => [
                'id'     => $u->orderid,
                'name'   => $u->username ?? 'Online Customer',
                'mobile' => optional($u->user)->contactno ?? '—',
                'amount' => $u->total,
                'source' => 'online',
            ]);
        }

        // Merge both pools, offline first, then online
        return $offlineEntries->concat($onlineEntries);
    }
}
