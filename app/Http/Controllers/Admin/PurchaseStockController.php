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

    public function update(Request $request, $pitem_id)
    {
        $item = PItem::findOrFail($pitem_id);

        $request->validate([
            'quantity' => 'required|integer|min:0|max:' . $item->bqty,
            'damaged' => 'required|integer|min:0|max:' . $item->bqty,
            'batch_number' => 'required_if:quantity,>,0|string|max:100',
            'mfg_date' => 'nullable|date',
            'expiry_date' => 'nullable|date|after_or_equal:mfg_date',
            'warranty_months' => 'nullable|integer|min:0'
        ]);

        $quantity = intval($request->input('quantity'));
        $damaged = intval($request->input('damaged', 0));
        $totalProcessed = $quantity + $damaged;

        if ($totalProcessed > $item->bqty) {
            return response()->json([
                'success' => false,
                'messages' => 'Total received and damaged quantity cannot exceed the outstanding balance (' . $item->bqty . ')'
            ], 422);
        }

        try {
            DB::beginTransaction();

            $warrantyMonths = intval($request->input('warranty_months', 0));

            // 1. Subtract the processed quantity from bqty and change status if fully processed
            $newBqty = $item->bqty - $totalProcessed;
            $item->update([
                'bqty' => $newBqty,
                'status' => ($newBqty <= 0) ? 2 : 1
            ]);

            $batchId = null;
            if ($quantity > 0) {
                // 2. Create Product Batch record only if items were received
                $batch = \App\Models\ProductBatch::create([
                    'product_id' => $item->prod_id,
                    'batch_number' => $request->input('batch_number'),
                    'mfg_date' => $request->input('mfg_date'),
                    'expiry_date' => $request->input('expiry_date'),
                    'initial_qty' => $quantity,
                    'current_qty' => $quantity,
                    'warranty_months' => $warrantyMonths,
                    'status' => 1
                ]);
                $batchId = $batch->id;

                // 3. Increment stock in products table
                $product = Product::findOrFail($item->prod_id);
                $product->update([
                    'tqty' => $product->tqty + $quantity
                ]);
            }

            // 4. Create Inward Delivery Note (Goods Received Note)
            $dn = \App\Models\DeliveryNote::create([
                'dn_number' => 'DN-IN-' . time() . '-' . rand(100, 999),
                'type' => 'inward',
                'porder_id' => $item->porder_id,
                'status' => 3, // Received
                'dn_date' => now()->toDateString()
            ]);

            \App\Models\DeliveryNoteItem::create([
                'delivery_note_id' => $dn->id,
                'product_id' => $item->prod_id,
                'batch_id' => $batchId,
                'qty_shipped' => $totalProcessed,
                'qty_received' => $quantity,
                'qty_damaged' => $damaged
            ]);

            // 5. Check if all items in this parent order are fully added (no status = 1 left)
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
                'messages' => 'Successfully updated stock ledger with received and damaged counts.'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'messages' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function batches(Request $request)
    {
        $query = \App\Models\ProductBatch::with('product');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('batch_number', 'like', "%{$search}%")
                  ->orWhereHas('product', function($q) use ($search) {
                      $q->where('productname', 'like', "%{$search}%");
                  });
        }

        $batches = $query->orderBy('id', 'desc')->paginate(15);
        return view('admin.purchases.batches', compact('batches'));
    }

    public function deliveryNotes(Request $request)
    {
        $query = \App\Models\DeliveryNote::with(['purchaseOrder', 'order'])
            ->withSum('items as total_received', 'qty_received')
            ->withSum('items as total_damaged', 'qty_damaged');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('dn_number', 'like', "%{$search}%")
                  ->orWhereHas('purchaseOrder', function($q) use ($search) {
                      $q->where('s_name', 'like', "%{$search}%")
                        ->orWhere('morder_id', 'like', "%{$search}%");
                  });
        }

        $notes = $query->orderBy('dn_date', 'desc')->orderBy('id', 'desc')->paginate(15);
        return view('admin.purchases.delivery_notes', compact('notes'));
    }

    public function pendingPurchases()
    {
        $orders = POrder::where('porder_status', 1)
            ->whereHas('deliveryNotes', function($q) {
                // Keep showing even if it has some delivery notes but is not complete
            }, '<', 100) // safety limit
            ->orderBy('porder_id', 'desc')
            ->get();
        
        // Double-check if they actually have pending items
        $orders = $orders->filter(function($order) {
            return PItem::where('porder_id', $order->porder_id)->where('status', 1)->where('bqty', '>', 0)->count() > 0;
        })->values();

        return response()->json($orders);
    }

    public function purchaseItems($porder_id)
    {
        $items = PItem::with('product')
            ->where('porder_id', $porder_id)
            ->where('status', 1)
            ->where('bqty', '>', 0)
            ->get();
        return response()->json($items);
    }

    public function storeDeliveryNote(Request $request)
    {
        $request->validate([
            'porder_id' => 'required|integer',
            'items' => 'required|array',
            'items.*.pitem_id' => 'required|integer',
            'items.*.qty_received' => 'required|integer|min:0',
            'items.*.qty_damaged' => 'required|integer|min:0',
            'items.*.batch_number' => 'nullable|string|max:100',
            'items.*.mfg_date' => 'nullable|date',
            'items.*.expiry_date' => 'nullable|date',
            'items.*.warranty_months' => 'nullable|integer|min:0',
            'items.*.prate' => 'nullable|numeric|min:0',
            'items.*.srate' => 'nullable|numeric|min:0',
            'items.*.mrp' => 'nullable|numeric|min:0',
            'items.*.cprice' => 'nullable|numeric|min:0',
            'items.*.dprice' => 'nullable|numeric|min:0',
            'items.*.sdprice' => 'nullable|numeric|min:0',
        ]);

        $porderId = $request->input('porder_id');
        $porder = POrder::findOrFail($porderId);

        try {
            DB::beginTransaction();

            // Create Delivery Note Header
            $dn = \App\Models\DeliveryNote::create([
                'dn_number' => 'DN-IN-' . time() . '-' . rand(100, 999),
                'type' => 'inward',
                'porder_id' => $porderId,
                'status' => 3, // Received
                'dn_date' => now()->toDateString()
            ]);

            foreach ($request->input('items') as $itemData) {
                $pitemId = $itemData['pitem_id'];
                $qtyReceived = intval($itemData['qty_received']);
                $qtyDamaged = intval($itemData['qty_damaged']);
                $totalProcessed = $qtyReceived + $qtyDamaged;

                if ($totalProcessed <= 0) {
                    continue;
                }

                $pitem = PItem::findOrFail($pitemId);

                if ($totalProcessed > $pitem->bqty) {
                    throw new \Exception("Processed quantity for " . ($pitem->product->productname ?? 'Item') . " exceeds remaining balance ($pitem->bqty)");
                }

                // 1. Update PItem balance
                $newBqty = $pitem->bqty - $totalProcessed;
                $pitem->update([
                    'bqty' => $newBqty,
                    'status' => ($newBqty <= 0) ? 2 : 1
                ]);

                $batchId = null;
                if ($qtyReceived > 0) {
                    // 2. Create batch
                    $batch = \App\Models\ProductBatch::create([
                        'product_id' => $pitem->prod_id,
                        'batch_number' => $itemData['batch_number'] ?? ('BATCH-' . rand(1000, 9999)),
                        'mfg_date' => $itemData['mfg_date'] ?? null,
                        'expiry_date' => $itemData['expiry_date'] ?? null,
                        'initial_qty' => $qtyReceived,
                        'current_qty' => $qtyReceived,
                        'warranty_months' => intval($itemData['warranty_months'] ?? 0),
                        'prate' => isset($itemData['prate']) ? floatval($itemData['prate']) : $pitem->rate,
                        'srate' => isset($itemData['srate']) ? floatval($itemData['srate']) : $pitem->product->srate,
                        'mrp' => isset($itemData['mrp']) ? floatval($itemData['mrp']) : $pitem->product->mrp,
                        'cprice' => isset($itemData['cprice']) ? floatval($itemData['cprice']) : $pitem->product->cprice,
                        'dprice' => isset($itemData['dprice']) ? floatval($itemData['dprice']) : $pitem->product->dprice,
                        'sdprice' => isset($itemData['sdprice']) ? floatval($itemData['sdprice']) : $pitem->product->sdprice,
                        'status' => 1
                    ]);
                    $batchId = $batch->id;

                    // 3. Update product stock
                    $product = Product::findOrFail($pitem->prod_id);
                    $product->update([
                        'tqty' => $product->tqty + $qtyReceived
                    ]);
                }

                // 4. Create Delivery Note Item
                \App\Models\DeliveryNoteItem::create([
                    'delivery_note_id' => $dn->id,
                    'product_id' => $pitem->prod_id,
                    'batch_id' => $batchId,
                    'qty_shipped' => $totalProcessed,
                    'qty_received' => $qtyReceived,
                    'qty_damaged' => $qtyDamaged
                ]);
            }

            // Check if PO is fully complete
            $pendingCount = PItem::where('porder_id', $porderId)->where('status', 1)->count();
            if ($pendingCount == 0) {
                $porder->update([
                    'porder_status' => 2 // Fully received
                ]);
            }

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Delivery Note successfully created.']);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'messages' => $e->getMessage()], 500);
        }
    }

    public function createDeliveryNote()
    {
        return view('admin.purchases.create_delivery_note');
    }

    public function deliveryNoteDetails($id)
    {
        $note = \App\Models\DeliveryNote::with(['purchaseOrder', 'items.product', 'items.batch'])->findOrFail($id);
        return response()->json($note);
    }
}
