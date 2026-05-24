<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PItem;
use App\Models\POrder;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class PurchaseStockController extends Controller
{
    /**
     * Display a listing of items for purchase stock approval.
     */
    public function index(Request $request)
    {
        $query = PItem::with(['product.brand', 'product.category', 'purchaseOrder']);

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->whereHas('product', function($qp) use ($search) {
                    $qp->where('productname', 'like', "%{$search}%");
                })->orWhereHas('purchaseOrder', function($qo) use ($search) {
                    $qo->where('s_name', 'like', "%{$search}%");
                });
            });
        }

        // Retrieve items, ordered by status ASC (Pending first) and Purchase Date DESC
        $items = $query->select('p_item.*')
            ->join('p_orders', 'p_orders.porder_id', '=', 'p_item.porder_id')
            ->orderBy('p_item.status', 'asc')
            ->orderBy('p_orders.porder_date', 'desc')
            ->paginate(15);

        // Stats for premium dashboard headers
        $totalPending = PItem::where('status', 1)->count();
        $totalApproved = PItem::where('status', 2)->count();

        return view('admin.purchases.stock', compact('items', 'totalPending', 'totalApproved'));
    }

    /**
     * Return JSON metadata of the selected purchase item for the modal.
     */
    public function detail($pitem_id)
    {
        $item = PItem::with(['product.brand', 'product.category', 'purchaseOrder'])->findOrFail($pitem_id);

        return response()->json([
            'pitem_id' => $item->pitem_id,
            'porder_id' => $item->porder_id,
            'prod_id' => $item->prod_id,
            'brand_name' => $item->product->brand->brand_name ?? '',
            'cat_name' => $item->product->category->cat_name ?? '',
            'punit' => $item->punit,
            'tqty' => $item->tqty,
            'pqty' => $item->pqty,
            'bqty' => $item->bqty,
            'qty' => $item->qty,
            'status' => $item->status,
            'productname' => $item->product->productname ?? '',
            'id' => $item->product->id ?? '',
            'brandid' => $item->product->brandid ?? '',
            'catid' => $item->product->catid ?? '',
            's_name' => $item->purchaseOrder->s_name ?? '',
            'prate' => $item->rate,
            'mrp' => $item->product->mrp ?? 0,
            'cprice' => $item->product->cprice ?? 0
        ]);
    }

    /**
     * Process stock addition approval inside transaction.
     */
    public function update(Request $request, $pitem_id)
    {
        $item = PItem::findOrFail($pitem_id);

        $request->validate([
            'quantity' => 'required|integer|min:1|max:' . $item->bqty
        ]);

        try {
            DB::beginTransaction();

            $quantity = intval($request->input('quantity'));

            // 1. Subtract the approved quantity from bqty and change status to 2
            $item->update([
                'bqty' => $item->bqty - $quantity,
                'status' => 2
            ]);

            // 2. Increment stock in products table
            $product = Product::findOrFail($item->prod_id);
            $product->update([
                'tqty' => $product->tqty + $quantity
            ]);

            // 3. Check if all items in this parent order are fully added (no status = 1 left)
            $pendingCount = PItem::where('porder_id', $item->porder_id)
                ->where('status', 1)
                ->count();

            if ($pendingCount == 0) {
                $purchase = POrder::findOrFail($item->porder_id);
                $purchase->update([
                    'porder_status' => 2 // 2 = Fully received (Godown)
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'messages' => 'Successfully update stock'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'messages' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }
}
