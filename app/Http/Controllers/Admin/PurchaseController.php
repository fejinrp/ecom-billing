<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\POrder;
use App\Models\PItem;
use App\Models\Purbal;
use App\Models\Product;
use App\Models\Auser;
use App\Models\Supplier;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PurchaseController extends Controller
{
    /**
     * Display a listing of the purchases.
     */
    public function index(Request $request)
    {
        $query = POrder::whereIn('porder_status', [1, 2]);

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('s_name', 'like', "%{$search}%")
                  ->orWhere('morder_id', 'like', "%{$search}%");
            });
        }

        // Calculate Stats based on active filtered query
        $totalPurchases = (clone $query)->sum('g_total');
        $totalPaid = (clone $query)->sum('ppaid');
        $totalDue = (clone $query)->sum('pbal');

        // Retrieve items with staff/creator details
        $purchases = $query->orderBy('porder_id', 'desc')->paginate(15);

        // Fetch staff name mapping for rendering
        $staffIds = $purchases->pluck('staffname')->unique();
        $staff = Auser::whereIn('user_id', $staffIds)->get()->keyBy('user_id');

        return view('admin.purchases.index', compact('purchases', 'totalPurchases', 'totalPaid', 'totalDue', 'staff'));
    }

    /**
     * Show the form for creating a new purchase.
     */
    public function create()
    {
        $products = Product::where('status', 1)->orderBy('productname', 'asc')->get();
        $suppliers = Supplier::where('status', 1)->orderBy('name', 'asc')->get();
        return view('admin.purchases.create', compact('products', 'suppliers'));
    }

    /**
     * Store a newly created purchase in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'pDate' => 'required|date',
            'sName' => 'required|string|max:255',
            'sContact' => 'required|string',
            'supplier_id' => 'nullable|integer|exists:suppliers,id',
            'subTotalValue' => 'required|numeric',
            'grandTotalValue' => 'required|numeric',
            'paid' => 'required|numeric',
            'dueValue' => 'required|numeric',
            'productName' => 'required|array',
            'qty' => 'required|array',
            'pqty' => 'required|array',
            'quantity' => 'required|array',
            'rate' => 'required|array',
            'punit' => 'required|array',
            'totalValue' => 'required|array',
        ]);

        $pDate = date('Y-m-d', strtotime($request->input('pDate')));
        $month = date('m', strtotime($pDate));
        $year = date('Y', strtotime($pDate));

        // Generate monthly sequential sequence ID
        $maxMorderId = POrder::whereMonth('porder_date', $month)
            ->whereYear('porder_date', $year)
            ->max('morder_id');

        $morder_id = ($maxMorderId ?? 0) + 1;

        try {
            DB::beginTransaction();

            // 1. Insert Purchase Order Header
            $porder = POrder::create([
                'supplier_id' => $request->input('supplier_id'),
                'porder_date' => $pDate,
                's_name' => strtoupper(trim($request->input('sName'))),
                's_contact' => strtoupper(trim($request->input('sContact'))),
                'staffname' => Auth::guard('admin')->user()->user_id,
                'sub_total' => $request->input('subTotalValue'),
                'vat' => 0.00,
                't_amount' => $request->input('subTotalValue'),
                'discount' => $request->input('discount', 0),
                'g_total' => $request->input('grandTotalValue'),
                'ppaid' => $request->input('paid'),
                'pbal' => $request->input('dueValue'),
                'gstn' => 0.00,
                'porder_status' => 1,
                'morder_id' => $morder_id
            ]);

            // 2. Insert Balance record in purbal
            Purbal::create([
                'porder_id' => $porder->porder_id,
                'gtotal' => $porder->g_total,
                'paid' => $porder->ppaid,
                'bal' => $porder->pbal,
                'pdate' => $pDate
            ]);

            // 3. Save line items and adjust stock quantities
            $productIds = $request->input('productName');
            $cartonQtys = $request->input('qty');
            $multipliers = $request->input('pqty');
            $pieceQtys = $request->input('quantity');
            $rates = $request->input('rate');
            $punits = $request->input('punit');
            $totals = $request->input('totalValue');
            $slnos = $request->input('slno', []);

            for ($x = 0; $x < count($productIds); $x++) {
                $pid = $productIds[$x];
                if (empty($pid)) {
                    continue;
                }
                $packQty = intval($cartonQtys[$x]);
                $multiplier = intval($multipliers[$x]);
                $finalQty = intval($pieceQtys[$x]);

                // Insert into PItem details table
                PItem::create([
                    'porder_id' => $porder->porder_id,
                    'prod_id' => $pid,
                    'rate' => $rates[$x],
                    'punit' => $punits[$x] ?? 'PCS',
                    'tqty' => $packQty,
                    'pqty' => $multiplier,
                    'qty' => $finalQty,
                    'tamount' => $totals[$x],
                    'bqty' => $finalQty,
                    'status' => 1,
                    'slno' => $slnos[$x] ?? ($x + 1)
                ]);
            }

            DB::commit();
            return redirect()->route('admin.purchases.index')->with('success', 'Purchase order successfully recorded! Invoice #' . $morder_id);

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'Error recording purchase: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified purchase.
     */
    public function edit($id)
    {
        // Preload items and their products details
        $purchase = POrder::findOrFail($id);
        
        $items = PItem::where('porder_id', $purchase->porder_id)->orderBy('slno', 'asc')->get();
        // Attach product relation manually
        foreach ($items as $item) {
            $item->product = Product::find($item->prod_id);
        }

        $products = Product::where('status', 1)->orderBy('productname', 'asc')->get();

        return view('admin.purchases.edit', compact('purchase', 'items', 'products'));
    }

    /**
     * Update the specified purchase in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'pDate' => 'required|date',
            'sName' => 'required|string|max:255',
            'sContact' => 'required|string',
            'subTotalValue' => 'required|numeric',
            'grandTotalValue' => 'required|numeric',
            'paid' => 'required|numeric',
            'dueValue' => 'required|numeric',
            'productName' => 'required|array',
            'qty' => 'required|array',
            'pqty' => 'required|array',
            'quantity' => 'required|array',
            'rate' => 'required|array',
            'punit' => 'required|array',
            'totalValue' => 'required|array',
        ]);

        $porder = POrder::findOrFail($id);
        $pDate = date('Y-m-d', strtotime($request->input('pDate')));

        try {
            DB::beginTransaction();

            // 1. Subtract previous purchase additions from stock count (only what was actually added!)
            $oldItems = PItem::where('porder_id', $porder->porder_id)->get();
            foreach ($oldItems as $oldItem) {
                $addedQty = $oldItem->qty - $oldItem->bqty;
                if ($addedQty > 0) {
                    $product = Product::find($oldItem->prod_id);
                    if ($product) {
                        $product->update([
                            'tqty' => $product->tqty - $addedQty
                        ]);
                    }
                }
            }

            // 2. Delete old records from p_item
            PItem::where('porder_id', $porder->porder_id)->delete();

            // 3. Update POrder header values
            $porder->update([
                'porder_date' => $pDate,
                's_name' => strtoupper(trim($request->input('sName'))),
                's_contact' => strtoupper(trim($request->input('sContact'))),
                'sub_total' => $request->input('subTotalValue'),
                't_amount' => $request->input('subTotalValue'),
                'discount' => $request->input('discount', 0),
                'g_total' => $request->input('grandTotalValue'),
                'ppaid' => $request->input('paid'),
                'pbal' => $request->input('dueValue'),
            ]);

            // 4. Update the primary Purbal mapping record
            Purbal::where('porder_id', $porder->porder_id)->update([
                'gtotal' => $porder->g_total,
                'paid' => $porder->ppaid,
                'bal' => $porder->pbal,
                'pdate' => $pDate
            ]);

            // 5. Save updated line items and increment stock balances
            $productIds = $request->input('productName');
            $cartonQtys = $request->input('qty');
            $multipliers = $request->input('pqty');
            $pieceQtys = $request->input('quantity');
            $rates = $request->input('rate');
            $punits = $request->input('punit');
            $totals = $request->input('totalValue');
            $slnos = $request->input('slno', []);

            for ($x = 0; $x < count($productIds); $x++) {
                $pid = $productIds[$x];
                if (empty($pid)) {
                    continue;
                }
                $packQty = intval($cartonQtys[$x]);
                $multiplier = intval($multipliers[$x]);
                $finalQty = intval($pieceQtys[$x]);

                // Re-create items details row
                PItem::create([
                    'porder_id' => $porder->porder_id,
                    'prod_id' => $pid,
                    'rate' => $rates[$x],
                    'punit' => $punits[$x] ?? 'PCS',
                    'tqty' => $packQty,
                    'pqty' => $multiplier,
                    'qty' => $finalQty,
                    'tamount' => $totals[$x],
                    'bqty' => $finalQty,
                    'status' => 1,
                    'slno' => $slnos[$x] ?? ($x + 1)
                ]);
            }

            DB::commit();
            return redirect()->route('admin.purchases.index')->with('success', 'Purchase order successfully updated! Invoice #' . $porder->morder_id);

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'Error updating purchase: ' . $e->getMessage());
        }
    }

    /**
     * Cancel/delete the specified purchase and restore stock counts.
     */
    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $porder = POrder::findOrFail($id);

            // Subtract stock balances back (only what was actually added!)
            $items = PItem::where('porder_id', $porder->porder_id)->get();
            foreach ($items as $item) {
                $addedQty = $item->qty - $item->bqty;
                if ($addedQty > 0) {
                    $product = Product::find($item->prod_id);
                    if ($product) {
                        $product->update([
                            'tqty' => $product->tqty - $addedQty
                        ]);
                    }
                }
            }

            // Update order status to 3 (Cancelled)
            $porder->update(['porder_status' => 3]);

            DB::commit();
            return redirect()->route('admin.purchases.index')->with('success', 'Purchase order successfully cancelled and stock adjusted!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error cancelling purchase: ' . $e->getMessage());
        }
    }

    /**
     * Record a manual transaction payment.
     */
    public function addPayment(Request $request, $id)
    {
        $request->validate([
            'paymentAmount' => 'required|numeric|min:0.01',
        ]);

        $porder = POrder::findOrFail($id);
        $amount = floatval($request->input('paymentAmount'));

        if ($amount > $porder->pbal) {
            return redirect()->back()->with('error', 'Payment amount exceeds the due balance!');
        }

        try {
            DB::beginTransaction();

            $newPaid = $porder->ppaid + $amount;
            $newDue = $porder->pbal - $amount;

            $porder->update([
                'ppaid' => $newPaid,
                'pbal' => $newDue
            ]);

            // Append record history in purbal
            Purbal::create([
                'porder_id' => $porder->porder_id,
                'gtotal' => $porder->g_total,
                'paid' => $amount,
                'bal' => $newDue,
                'pdate' => date('Y-m-d')
            ]);

            DB::commit();
            return redirect()->route('admin.purchases.index')->with('success', 'Payment of Rs. ' . number_format($amount, 2) . ' successfully recorded!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error recording payment: ' . $e->getMessage());
        }
    }

    /**
     * Renders clean premium printable receipt.
     */
    public function print($id)
    {
        $purchase = POrder::findOrFail($id);
        $items = PItem::where('porder_id', $purchase->porder_id)->orderBy('slno', 'asc')->get();
        
        foreach ($items as $item) {
            $item->product = Product::find($item->prod_id);
        }

        $creator = Auser::find($purchase->staffname);

        return view('admin.purchases.print', compact('purchase', 'items', 'creator'));
    }
}
