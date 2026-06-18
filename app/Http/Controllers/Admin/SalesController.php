<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use App\Models\Product;
use App\Models\Orderbal;
use App\Models\Stategst;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class SalesController extends Controller
{
    /**
     * Display a listing of sales.
     */
    public function index(Request $request)
    {
        $view = $request->input('view');

        if ($view === 'list') {
            $title = 'CUSTOMER SALES ITEM LIST';
            $description = 'View detailed itemized log of all sold product items.';

            $query = OrderItem::with(['product', 'order.user'])
                ->whereHas('order', function($q) {
                    $q->where('order_status', 1)->where('section', 1);
                });

            if ($request->filled('search')) {
                $search = $request->input('search');
                $query->where(function($q) use ($search) {
                    $q->whereHas('order', function($oq) use ($search) {
                        $oq->where('client_name', 'like', "%{$search}%")
                           ->orWhere('mobile', 'like', "%{$search}%")
                           ->orWhere('morder_id', 'like', "%{$search}%");
                    })
                    ->orWhereHas('product', function($pq) use ($search) {
                        $pq->where('productname', 'like', "%{$search}%");
                    });
                });
            }

            $items = $query->orderBy('item_id', 'desc')->paginate(15)->withQueryString();

            return view('admin.sales.item_list', compact('items', 'title', 'description', 'view'));
        }

        if ($view === 'manage') {
            $title = 'MANAGE INVOICE';
            $description = 'View and manage all billing invoices, records, and summaries.';
        } else {
            $title = 'MANAGE CUSTOMER INVOICE';
            $description = 'View and manage customer billing invoices and balance due ledgers.';
        }

        $query = Order::with('user')
            ->where('order_status', 1)
            ->where('section', 1);

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('client_name', 'like', "%{$search}%")
                  ->orWhere('mobile', 'like', "%{$search}%")
                  ->orWhere('morder_id', 'like', "%{$search}%");
            });
        }

        $sales = $query->orderBy('order_id', 'desc')->paginate(15)->withQueryString();

        return view('admin.sales.index', compact('sales', 'title', 'description', 'view'));
    }

    /**
     * Show the form for creating a new sale.
     */
    public function create()
    {
        $products = Product::where('status', 1)->orderBy('productname', 'asc')->get();
        $customers = User::where('usertype', 'C')->where('ustatus', 1)->orderBy('uname', 'asc')->get();
        $states = Stategst::orderBy('sname', 'asc')->get();

        return view('admin.sales.create', compact('products', $customers ? 'customers' : [], 'states'));
    }

    /**
     * Store a newly created sale in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'orderDate' => 'required|date',
            'customername' => 'required',
            'clientName' => 'required|string|max:255',
            'mobileno' => 'required|string|max:25',
            'clientContact' => 'required|string',
            'city' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'pincode' => 'required|string',
            'subTotalValue' => 'required|numeric',
            'grandTotalValue' => 'required|numeric',
            'paid' => 'required|numeric',
            'dueValue' => 'required|numeric',
            'paymentType' => 'required|integer',
            'paymentStatus' => 'required|integer',
            'paymentPlace' => 'required|integer',
            'productName' => 'required|array',
            'quantity' => 'required|array',
            'rateValue' => 'required|array',
            'totalValue' => 'required|array',
        ]);

        $orderDate = date('Y-m-d', strtotime($request->input('orderDate')));
        $month = date('m', strtotime($orderDate));
        $year = date('Y', strtotime($orderDate));

        // Calculate morder_id (invoice number) for this month and section 1
        $maxMorderId = Order::whereMonth('order_date', $month)
            ->whereYear('order_date', $year)
            ->where('section', 1)
            ->max('morder_id');

        $morder_id = ($maxMorderId ?? 0) + 1;

        try {
            DB::beginTransaction();

            // 1. Create or Update Customer User
            $customername = $request->input('customername');
            $clientname = strtoupper(trim($request->input('clientName')));
            $clientcontact = strtoupper(trim($request->input('clientContact')));
            $city = strtoupper(trim($request->input('city')));
            $state = strtoupper(trim($request->input('state')));
            $mobile = trim($request->input('mobileno'));
            $gsttin = trim($request->input('gsttin', ''));
            $pincode = trim($request->input('pincode'));

            $mcoin = floatval($request->input('mcoin', 0));
            $bmcoin = floatval($request->input('bmcoin', 0));
            $tmcoin = floatval($request->input('tmcoin', 0));
            $mcoinp = floatval($request->input('mcoinp', 0));

            $uid = 0;

            if ($customername == '0' || $customername == 'new') {
                $user = User::create([
                    'uname' => $clientname,
                    'contactno' => $mobile,
                    'usertype' => 'C',
                    'ustatus' => 1,
                    'shippingaddress' => $clientcontact,
                    'shippingstate' => $state,
                    'shippingcity' => $city,
                    'shippingpincode' => $pincode,
                    'billingaddress' => $clientcontact,
                    'billingstate' => $state,
                    'billingcity' => $city,
                    'billingpincode' => $pincode,
                    'regdate' => date('Y-m-d H:i:s'),
                    'gsttin' => $gsttin,
                    'mcoin' => $mcoin,
                    'mcoinp' => $mcoinp,
                    'mcoinb' => $mcoin - $mcoinp
                ]);
                $uid = $user->id;
            } else {
                $user = User::findOrFail($customername);
                $user->update([
                    'uname' => $clientname,
                    'billingaddress' => $clientcontact,
                    'contactno' => $mobile,
                    'billingstate' => $state,
                    'billingcity' => $city,
                    'billingpincode' => $pincode,
                    'gsttin' => $gsttin,
                    'mcoin' => $user->mcoin + $mcoin,
                    'mcoinp' => $user->mcoinp + $mcoinp,
                    'mcoinb' => $tmcoin - $mcoinp
                ]);
                $uid = $customername;
            }

            // 2. Insert order
            $order = Order::create([
                'order_date' => $orderDate,
                'client_name' => $clientname,
                'client_contact' => $clientcontact,
                'sub_total' => $request->input('subTotalValue'),
                'total_amount' => $request->input('totalAmountValue', $request->input('subTotalValue')),
                'discount' => $request->input('discount', 0),
                'grand_total' => $request->input('grandTotalValue'),
                'paid' => $request->input('paid'),
                'due' => $request->input('dueValue'),
                'payment_type' => $request->input('paymentType'),
                'payment_status' => $request->input('paymentStatus'),
                'payment_place' => $request->input('paymentPlace'),
                'gstn' => $request->input('igst', 0),
                'order_status' => 1,
                'user_id' => $uid,
                'paymentname' => strtoupper($request->input('paymentName', 'MTL')),
                'morder_id' => $morder_id,
                'mobile' => $mobile,
                'gsttin' => $gsttin,
                'section' => 1,
                'instamt' => $request->input('intcharge', 0),
                'shipamt' => $request->input('shipcharge', 0),
                'mcoin' => $mcoin,
                'bcoin' => $bmcoin,
                'tcoin' => $tmcoin,
                'pcoin' => $mcoinp
            ]);

            // 3. Insert orderbal
            Orderbal::create([
                'order_id' => $order->order_id,
                'gtotal' => $order->grand_total,
                'pamount' => $order->paid,
                'bamount' => $order->due,
                'pdate' => $orderDate
            ]);

            // 4. Save items & update stock
            $productIds = $request->input('productName');
            $quantities = $request->input('quantity');
            $rates = $request->input('rateValue');
            $totals = $request->input('totalValue');
            $hsnsacs = $request->input('hsnsac');
            $gsts = $request->input('gst');
            $units = $request->input('unit');
            $slnos = $request->input('slno', []);
            $batchIds = $request->input('batchId', []);

            for ($x = 0; $x < count($productIds); $x++) {
                $pid = $productIds[$x];
                if (empty($pid)) {
                    continue;
                }
                $qty = intval($quantities[$x]);
                $selectedBatchId = !empty($batchIds[$x]) ? intval($batchIds[$x]) : null;

                // Decrement product total stock
                $product = Product::findOrFail($pid);
                $product->update([
                    'tqty' => $product->tqty - $qty
                ]);

                $deductedBatches = [];

                if ($selectedBatchId) {
                    // Deduct from the user-selected batch directly
                    $batch = \App\Models\ProductBatch::where('product_id', $pid)
                        ->where('id', $selectedBatchId)
                        ->first();

                    if ($batch) {
                        $deductQty = min($batch->current_qty, $qty);
                        $batch->update([
                            'current_qty' => $batch->current_qty - $deductQty
                        ]);
                        
                        $qtyRemaining = $qty - $deductQty;
                        
                        $warrantyExpiry = null;
                        $wMonths = $batch->warranty_months > 0 ? $batch->warranty_months : $product->warranty_months;
                        if ($wMonths > 0) {
                            $warrantyExpiry = date('Y-m-d', strtotime("+$wMonths months", strtotime($orderDate)));
                        }
                        
                        $deductedBatches[] = [
                            'batch_id' => $batch->id,
                            'qty' => $deductQty,
                            'warranty_expiry_date' => $warrantyExpiry
                        ];

                        // If the chosen batch has insufficient stock, deduct remaining using FIFO
                        if ($qtyRemaining > 0) {
                            $batches = \App\Models\ProductBatch::where('product_id', $pid)
                                ->where('status', 1)
                                ->where('id', '!=', $selectedBatchId)
                                ->where('current_qty', '>', 0)
                                ->orderBy('id', 'asc') // FIFO
                                ->get();

                            foreach ($batches as $fifoBatch) {
                                if ($qtyRemaining <= 0) {
                                    break;
                                }
                                $deductFifoQty = min($fifoBatch->current_qty, $qtyRemaining);
                                $fifoBatch->update([
                                    'current_qty' => $fifoBatch->current_qty - $deductFifoQty
                                ]);
                                $qtyRemaining -= $deductFifoQty;

                                $wMonthsFifo = $fifoBatch->warranty_months > 0 ? $fifoBatch->warranty_months : $product->warranty_months;
                                $warrantyExpiryFifo = null;
                                if ($wMonthsFifo > 0) {
                                    $warrantyExpiryFifo = date('Y-m-d', strtotime("+$wMonthsFifo months", strtotime($orderDate)));
                                }

                                $deductedBatches[] = [
                                    'batch_id' => $fifoBatch->id,
                                    'qty' => $deductFifoQty,
                                    'warranty_expiry_date' => $warrantyExpiryFifo
                                ];
                            }
                        }

                        // Fallback if still remaining
                        if ($qtyRemaining > 0) {
                            $fallbackBatch = \App\Models\ProductBatch::create([
                                'product_id' => $pid,
                                'batch_number' => 'LEGACY-BATCH',
                                'initial_qty' => 0,
                                'current_qty' => -$qtyRemaining,
                                'warranty_months' => $product->warranty_months,
                                'status' => 1
                            ]);
                            
                            $warrantyExpiry = null;
                            if ($product->warranty_months > 0) {
                                $warrantyExpiry = date('Y-m-d', strtotime("+" . $product->warranty_months . " months", strtotime($orderDate)));
                            }
                            
                            $deductedBatches[] = [
                                'batch_id' => $fallbackBatch->id,
                                'qty' => $qtyRemaining,
                                'warranty_expiry_date' => $warrantyExpiry
                            ];
                        }
                    }
                } else {
                    // Deduct from batches using FIFO
                    $qtyToDeduct = $qty;
                    $batches = \App\Models\ProductBatch::where('product_id', $pid)
                        ->where('status', 1)
                        ->where('current_qty', '>', 0)
                        ->orderBy('id', 'asc') // FIFO
                        ->get();

                    foreach ($batches as $batch) {
                        if ($qtyToDeduct <= 0) {
                            break;
                        }
                        
                        $deductQty = min($batch->current_qty, $qtyToDeduct);
                        $batch->update([
                            'current_qty' => $batch->current_qty - $deductQty
                        ]);
                        
                        $qtyToDeduct -= $deductQty;
                        
                        $warrantyExpiry = null;
                        $wMonths = $batch->warranty_months > 0 ? $batch->warranty_months : $product->warranty_months;
                        if ($wMonths > 0) {
                            $warrantyExpiry = date('Y-m-d', strtotime("+$wMonths months", strtotime($orderDate)));
                        }
                        
                        $deductedBatches[] = [
                            'batch_id' => $batch->id,
                            'qty' => $deductQty,
                            'warranty_expiry_date' => $warrantyExpiry
                        ];
                    }

                    if ($qtyToDeduct > 0) {
                        $fallbackBatch = \App\Models\ProductBatch::create([
                            'product_id' => $pid,
                            'batch_number' => 'LEGACY-BATCH',
                            'initial_qty' => 0,
                            'current_qty' => -$qtyToDeduct,
                            'warranty_months' => $product->warranty_months,
                            'status' => 1
                        ]);
                        
                        $warrantyExpiry = null;
                        if ($product->warranty_months > 0) {
                            $warrantyExpiry = date('Y-m-d', strtotime("+" . $product->warranty_months . " months", strtotime($orderDate)));
                        }
                        
                        $deductedBatches[] = [
                            'batch_id' => $fallbackBatch->id,
                            'qty' => $qtyToDeduct,
                            'warranty_expiry_date' => $warrantyExpiry
                        ];
                    }
                }

                // Create OrderItem records for each batch deduction
                foreach ($deductedBatches as $dbatch) {
                    OrderItem::create([
                        'order_id' => $order->order_id,
                        'product_id' => $pid,
                        'hsnsan' => $hsnsacs[$x] ?? '',
                        'gst' => $gsts[$x] ?? 0,
                        'qty' => $dbatch['qty'],
                        'rate' => $rates[$x],
                        'unit' => $units[$x] ?? 'PCS',
                        'total' => $rates[$x] * $dbatch['qty'],
                        'status' => 1,
                        'slno' => $slnos[$x] ?? ($x + 1),
                        'batch_id' => $dbatch['batch_id'],
                        'warranty_expiry_date' => $dbatch['warranty_expiry_date']
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('admin.sales.index')->with('success', 'Sale successfully recorded! Invoice #' . $morder_id);

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'Error recording sale: ' . $e->getMessage());
        }
    }

    /**
     * Show the edit form.
     */
    public function edit($id)
    {
        $sale = Order::with('items.product')->findOrFail($id);
        $products = Product::where('status', 1)->orderBy('productname', 'asc')->get();
        $customers = User::where('usertype', 'C')->where('ustatus', 1)->orderBy('uname', 'asc')->get();
        $states = Stategst::orderBy('sname', 'asc')->get();

        return view('admin.sales.edit', compact('sale', 'products', 'customers', 'states'));
    }

    /**
     * Update the sale.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'orderDate' => 'required|date',
            'clientName' => 'required|string|max:255',
            'mobileno' => 'required|string|max:25',
            'clientContact' => 'required|string',
            'city' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'pincode' => 'required|string',
            'subTotalValue' => 'required|numeric',
            'grandTotalValue' => 'required|numeric',
            'paid' => 'required|numeric',
            'dueValue' => 'required|numeric',
            'paymentType' => 'required|integer',
            'paymentStatus' => 'required|integer',
            'paymentPlace' => 'required|integer',
            'productName' => 'required|array',
            'quantity' => 'required|array',
            'rateValue' => 'required|array',
            'totalValue' => 'required|array',
        ]);

        $order = Order::findOrFail($id);
        $orderDate = date('Y-m-d', strtotime($request->input('orderDate')));

        try {
            DB::beginTransaction();

            // Restore previous product stock & batch stock
            $oldItems = OrderItem::where('order_id', $order->order_id)->get();
            foreach ($oldItems as $oldItem) {
                $product = Product::find($oldItem->product_id);
                if ($product) {
                    $product->update([
                        'tqty' => $product->tqty + $oldItem->qty
                    ]);
                }
                if ($oldItem->batch_id) {
                    $batch = \App\Models\ProductBatch::find($oldItem->batch_id);
                    if ($batch) {
                        $batch->update([
                            'current_qty' => $batch->current_qty + $oldItem->qty
                        ]);
                    }
                }
            }

            // Delete old items
            OrderItem::where('order_id', $order->order_id)->delete();

            // Update user details
            $user = User::findOrFail($order->user_id);
            $clientname = strtoupper(trim($request->input('clientName')));
            $clientcontact = strtoupper(trim($request->input('clientContact')));
            $city = strtoupper(trim($request->input('city')));
            $state = strtoupper(trim($request->input('state')));
            $mobile = trim($request->input('mobileno'));
            $gsttin = trim($request->input('gsttin', ''));
            $pincode = trim($request->input('pincode'));

            $user->update([
                'uname' => $clientname,
                'billingaddress' => $clientcontact,
                'contactno' => $mobile,
                'billingstate' => $state,
                'billingcity' => $city,
                'billingpincode' => $pincode,
                'gsttin' => $gsttin,
            ]);

            // Update Order
            $order->update([
                'order_date' => $orderDate,
                'client_name' => $clientname,
                'client_contact' => $clientcontact,
                'sub_total' => $request->input('subTotalValue'),
                'total_amount' => $request->input('totalAmountValue', $request->input('subTotalValue')),
                'discount' => $request->input('discount', 0),
                'grand_total' => $request->input('grandTotalValue'),
                'paid' => $request->input('paid'),
                'due' => $request->input('dueValue'),
                'payment_type' => $request->input('paymentType'),
                'payment_status' => $request->input('paymentStatus'),
                'payment_place' => $request->input('paymentPlace'),
                'gstn' => $request->input('igst', 0),
                'paymentname' => strtoupper($request->input('paymentName', $order->paymentname)),
                'mobile' => $mobile,
                'gsttin' => $gsttin,
                'instamt' => $request->input('intcharge', 0),
                'shipamt' => $request->input('shipcharge', 0),
            ]);

            // Update Orderbal
            Orderbal::where('order_id', $order->order_id)->update([
                'gtotal' => $order->grand_total,
                'pamount' => $order->paid,
                'bamount' => $order->due,
                'pdate' => $orderDate
            ]);

            // Insert new items & subtract stock
            $productIds = $request->input('productName');
            $quantities = $request->input('quantity');
            $rates = $request->input('rateValue');
            $totals = $request->input('totalValue');
            $hsnsacs = $request->input('hsnsac');
            $gsts = $request->input('gst');
            $units = $request->input('unit');
            $slnos = $request->input('slno', []);

            for ($x = 0; $x < count($productIds); $x++) {
                $pid = $productIds[$x];
                if (empty($pid)) {
                    continue;
                }
                $qty = intval($quantities[$x]);

                $product = Product::findOrFail($pid);
                $product->update([
                    'tqty' => $product->tqty - $qty
                ]);

                // Deduct from batches using FIFO
                $qtyToDeduct = $qty;
                $batches = \App\Models\ProductBatch::where('product_id', $pid)
                    ->where('status', 1)
                    ->where('current_qty', '>', 0)
                    ->orderBy('id', 'asc')
                    ->get();

                $deductedBatches = [];

                foreach ($batches as $batch) {
                    if ($qtyToDeduct <= 0) {
                        break;
                    }
                    
                    $deductQty = min($batch->current_qty, $qtyToDeduct);
                    $batch->update([
                        'current_qty' => $batch->current_qty - $deductQty
                    ]);
                    
                    $qtyToDeduct -= $deductQty;
                    
                    $warrantyExpiry = null;
                    $wMonths = $batch->warranty_months > 0 ? $batch->warranty_months : $product->warranty_months;
                    if ($wMonths > 0) {
                        $warrantyExpiry = date('Y-m-d', strtotime("+$wMonths months", strtotime($orderDate)));
                    }
                    
                    $deductedBatches[] = [
                        'batch_id' => $batch->id,
                        'qty' => $deductQty,
                        'warranty_expiry_date' => $warrantyExpiry
                    ];
                }

                if ($qtyToDeduct > 0) {
                    $fallbackBatch = \App\Models\ProductBatch::create([
                        'product_id' => $pid,
                        'batch_number' => 'LEGACY-BATCH',
                        'initial_qty' => 0,
                        'current_qty' => -$qtyToDeduct,
                        'warranty_months' => $product->warranty_months,
                        'status' => 1
                    ]);
                    
                    $warrantyExpiry = null;
                    if ($product->warranty_months > 0) {
                        $warrantyExpiry = date('Y-m-d', strtotime("+" . $product->warranty_months . " months", strtotime($orderDate)));
                    }
                    
                    $deductedBatches[] = [
                        'batch_id' => $fallbackBatch->id,
                        'qty' => $qtyToDeduct,
                        'warranty_expiry_date' => $warrantyExpiry
                    ];
                }

                foreach ($deductedBatches as $dbatch) {
                    OrderItem::create([
                        'order_id' => $order->order_id,
                        'product_id' => $pid,
                        'hsnsan' => $hsnsacs[$x] ?? '',
                        'gst' => $gsts[$x] ?? 0,
                        'qty' => $dbatch['qty'],
                        'rate' => $rates[$x],
                        'unit' => $units[$x] ?? 'PCS',
                        'total' => $rates[$x] * $dbatch['qty'],
                        'status' => 1,
                        'slno' => $slnos[$x] ?? ($x + 1),
                        'batch_id' => $dbatch['batch_id'],
                        'warranty_expiry_date' => $dbatch['warranty_expiry_date']
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('admin.sales.index')->with('success', 'Sale successfully updated! Invoice #' . $order->morder_id);

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'Error updating sale: ' . $e->getMessage());
        }
    }

    /**
     * Soft delete/cancel sale.
     */
    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $order = Order::findOrFail($id);

            // Restore stock & batch stock
            $items = OrderItem::where('order_id', $order->order_id)->get();
            foreach ($items as $item) {
                $product = Product::find($item->product_id);
                if ($product) {
                    $product->update([
                        'tqty' => $product->tqty + $item->qty
                    ]);
                }
                if ($item->batch_id) {
                    $batch = \App\Models\ProductBatch::find($item->batch_id);
                    if ($batch) {
                        $batch->update([
                            'current_qty' => $batch->current_qty + $item->qty
                        ]);
                    }
                }
            }

            // Update order status to 2 (Inactive/Cancelled)
            $order->update(['order_status' => 2]);

            DB::commit();
            return redirect()->route('admin.sales.index')->with('success', 'Sale successfully cancelled and stock restored!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error cancelling sale: ' . $e->getMessage());
        }
    }

    /**
     * Record a manual payment payment
     */
    public function addPayment(Request $request, $id)
    {
        $request->validate([
            'paymentAmount' => 'required|numeric|min:0.01',
        ]);

        $order = Order::findOrFail($id);
        $amount = floatval($request->input('paymentAmount'));

        if ($amount > $order->due) {
            return redirect()->back()->with('error', 'Payment amount exceeds the due balance!');
        }

        try {
            DB::beginTransaction();

            $newPaid = $order->paid + $amount;
            $newDue = $order->due - $amount;
            $status = ($newDue <= 0) ? 1 : 2; // 1 = Full paid, 2 = Part paid

            $order->update([
                'paid' => $newPaid,
                'due' => $newDue,
                'payment_status' => $status
            ]);

            Orderbal::create([
                'order_id' => $order->order_id,
                'gtotal' => $order->grand_total,
                'pamount' => $amount,
                'bamount' => $newDue,
                'pdate' => date('Y-m-d')
            ]);

            DB::commit();
            return redirect()->route('admin.sales.index')->with('success', 'Payment of Rs. ' . number_format($amount, 2) . ' successfully recorded!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error recording payment: ' . $e->getMessage());
        }
    }

    /**
     * Print/View Tax Invoice in beautiful premium printable format.
     */
    public function print($id)
    {
        $sale = Order::with(['items.product', 'user'])->findOrFail($id);
        return view('admin.sales.print', compact('sale'));
    }
}

