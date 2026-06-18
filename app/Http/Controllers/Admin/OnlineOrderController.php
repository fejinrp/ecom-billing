<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Uorder;
use App\Models\UorderItem;
use App\Models\Uorderbal;
use App\Models\Product;
use App\Models\Ordertrackhistory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class OnlineOrderController extends Controller
{
    /**
     * Display a listing of online orders.
     */
    public function index(Request $request)
    {
        $status = $request->input('status', 'pending');
        
        switch ($status) {
            case 'all':
                $title = "ALL ONLINE ORDERS";
                $description = "View and search all online orders.";
                $filterStatus = [];
                break;
            case 'sending':
                $title = "SENDING ORDERS";
                $description = "Manage online orders currently in transit or out for delivery.";
                $filterStatus = ['s'];
                break;
            case 'delivered':
                $title = "DELIVERED ORDERS";
                $description = "View completed and delivered online orders.";
                $filterStatus = ['d'];
                break;
            case 'cancelled':
                $title = "CANCELLED ORDERS";
                $description = "View cancelled online orders.";
                $filterStatus = ['c'];
                break;
            case 'pending':
            default:
                $status = 'pending';
                $title = "TODAY'S ORDERS MANAGE";
                $description = "Manage today's incoming online order queues, status transitions, and payments.";
                $filterStatus = ['p', null];
                break;
        }

        $query = Uorder::with('user');

        // Apply ostatus filters
        if ($status !== 'all') {
            if (in_array(null, $filterStatus, true)) {
                $query->where(function($q) {
                    $q->whereIn('ostatus', ['p'])
                      ->orWhereNull('ostatus');
                });
            } else {
                $query->whereIn('ostatus', $filterStatus);
            }
        }

        // Apply Search filters
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('orderid', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%")
                  ->orWhereHas('user', function($uq) use ($search) {
                      $uq->where('uname', 'like', "%{$search}%")
                        ->orWhere('contactno', 'like', "%{$search}%");
                  });
            });
        }

        $orders = $query->orderBy('orderid', 'desc')->paginate(15)->withQueryString();

        return view('admin.online_orders.index', compact('orders', 'status', 'title', 'description'));
    }

    /**
     * Show the form for editing the specified online order.
     */
    public function edit($id)
    {
        $order = Uorder::with(['items.product', 'user'])->findOrFail($id);
        $products = Product::where('status', 1)->orderBy('productname', 'asc')->get();

        return view('admin.online_orders.edit', compact('order', 'products'));
    }

    /**
     * Update the specified online order in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'orderDate' => 'required|date',
            'clientName' => 'required|string|max:255',
            'mobileno' => 'required|string|max:25',
            'email' => 'required|email|max:255',
            'sstreet' => 'required|string',
            'scity' => 'required|string',
            'sstate' => 'required|string',
            'spin' => 'required|string|max:10',
            'bstreet' => 'required|string',
            'bcity' => 'required|string',
            'bstate' => 'required|string',
            'bpin' => 'required|string|max:10',
            'subTotal' => 'required|numeric',
            'grandTotal' => 'required|numeric',
            'paid' => 'required|numeric',
            'dueValue' => 'required|numeric',
            'paymentType' => 'required',
            'paymentStatus' => 'required',
            'ostatus' => 'required|string|max:2',
            'productName' => 'required|array',
            'quantity' => 'required|array',
            'rateValue' => 'required|array',
            'totalValue' => 'required|array',
        ]);

        $order = Uorder::findOrFail($id);

        try {
            DB::beginTransaction();

            // 1. Restore previous product stock
            foreach ($order->items as $item) {
                $product = Product::find($item->productId);
                if ($product) {
                    $product->update([
                        'tqty' => $product->tqty + $item->quantity
                    ]);
                }
            }

            // 2. Delete old items
            $order->items()->delete();

            // 3. Update customer user details
            $user = $order->user;
            if ($user) {
                $user->update([
                    'uname' => strtoupper(trim($request->input('clientName'))),
                    'contactno' => trim($request->input('mobileno')),
                    'email' => trim($request->input('email')),
                    'shippingaddress' => strtoupper(trim($request->input('sstreet'))),
                    'shippingcity' => strtoupper(trim($request->input('scity'))),
                    'shippingstate' => strtoupper(trim($request->input('sstate'))),
                    'shippingpincode' => trim($request->input('spin')),
                    'billingaddress' => strtoupper(trim($request->input('bstreet'))),
                    'billingcity' => strtoupper(trim($request->input('bcity'))),
                    'billingstate' => strtoupper(trim($request->input('bstate'))),
                    'billingpincode' => trim($request->input('bpin')),
                    'gsttin' => trim($request->input('gsttin', '')),
                ]);
            }

            // 4. Update order details
            $orderDate = date('Y-m-d H:i:s', strtotime($request->input('orderDate')));
            $order->update([
                'orderdate' => $orderDate,
                'paymethod' => $request->input('paymentType'),
                'gamount' => $request->input('grandTotal'),
                'tship' => $request->input('shipcharge', 0),
                'pamount' => $request->input('paid'),
                'bamount' => $request->input('dueValue'),
                'discount' => $request->input('discount', 0),
                'gsta' => $request->input('igst', 0),
                'ostatus' => $request->input('ostatus'),
                'install' => $request->input('intcharge', 0),
                'gsttin' => trim($request->input('gsttin', '')),
                'username' => Auth::guard('admin')->user()->username,
            ]);

            // 5. Insert new items and decrement stock
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
                $qty = $quantities[$x];

                $product = Product::findOrFail($pid);
                $product->update([
                    'tqty' => $product->tqty - $qty
                ]);

                UorderItem::create([
                    'userId' => $order->userid,
                    'productId' => $pid,
                    'quantity' => $qty,
                    'cprice' => $totals[$x],
                    'hsnsan' => $hsnsacs[$x] ?? '',
                    'srate' => $rates[$x],
                    'gst' => $gsts[$x] ?? 0,
                    'unit' => $units[$x] ?? 'PCS',
                    'orderid' => $order->orderid,
                    'price' => $rates[$x],
                    'slno' => $slnos[$x] ?? ($x + 1)
                ]);
            }

            // 6. Delete and Reinsert balance ledger
            Uorderbal::where('orderid', $order->orderid)->delete();
            Uorderbal::create([
                'orderid' => $order->orderid,
                'gtotal' => $order->gamount,
                'pamount' => $order->pamount,
                'bamount' => $order->bamount,
                'ptype' => $order->paymethod,
                'pdate' => $orderDate
            ]);

            // 7. Update Track History
            $ostatus = $request->input('ostatus');
            $ostatusn = 'Pending';
            if ($ostatus == 'd') $ostatusn = 'Delivery Successfully';
            elseif ($ostatus == 's') $ostatusn = 'Sending';
            elseif ($ostatus == 'c') $ostatusn = 'Cancel';

            Ordertrackhistory::updateOrCreate(
                ['orderId' => $order->orderid],
                [
                    'status' => $ostatus,
                    'remark' => $ostatusn,
                    'postingDate' => now('Asia/Kolkata')->format('Y-m-d H:i:s')
                ]
            );

            DB::commit();
            return redirect()->route('admin.online_orders.index')->with('success', 'Online Order #' . $order->orderid . ' successfully updated!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'Error updating online order: ' . $e->getMessage());
        }
    }

    /**
     * Record payment of due balance for an online order.
     */
    public function updatePayment(Request $request, $id)
    {
        $request->validate([
            'paymentAmount' => 'required|numeric|min:0.01',
            'paymentType' => 'required|string',
            'paymentStatus' => 'required'
        ]);

        $order = Uorder::findOrFail($id);
        $amount = floatval($request->input('paymentAmount'));

        if ($amount > $order->bamount) {
            return redirect()->back()->with('error', 'Payment amount exceeds outstanding balance!');
        }

        try {
            DB::beginTransaction();

            $newPaid = $order->pamount + $amount;
            $newDue = $order->bamount - $amount;

            $order->update([
                'pamount' => $newPaid,
                'bamount' => $newDue
            ]);

            Uorderbal::create([
                'orderid' => $order->orderid,
                'gtotal' => $order->gamount,
                'pamount' => $amount,
                'bamount' => $newDue,
                'ptype' => $request->input('paymentType'),
                'pdate' => now('Asia/Kolkata')->format('Y-m-d H:i:s')
            ]);

            DB::commit();
            return redirect()->back()->with('success', 'Balance payment of Rs. ' . number_format($amount, 2) . ' successfully posted!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error recording payment: ' . $e->getMessage());
        }
    }

    /**
     * Update order transit status.
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'ostatus' => 'required|string|max:2',
        ]);

        $order = Uorder::findOrFail($id);
        $ostatus = $request->input('ostatus');

        try {
            DB::beginTransaction();

            $order->update([
                'ostatus' => $ostatus
            ]);

            $ostatusn = 'Pending';
            if ($ostatus == 'd') $ostatusn = 'Delivery Successfully';
            elseif ($ostatus == 's') $ostatusn = 'Sending';
            elseif ($ostatus == 'c') $ostatusn = 'Cancel';

            Ordertrackhistory::updateOrCreate(
                ['orderId' => $order->orderid],
                [
                    'status' => $ostatus,
                    'remark' => $ostatusn,
                    'postingDate' => now('Asia/Kolkata')->format('Y-m-d H:i:s')
                ]
            );

            DB::commit();
            return redirect()->back()->with('success', 'Order status updated successfully to ' . $ostatusn . '!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error updating status: ' . $e->getMessage());
        }
    }

    /**
     * Print the tax invoice for the online order.
     */
    public function print($id)
    {
        $order = Uorder::with(['items.product', 'user'])->findOrFail($id);
        return view('admin.online_orders.print', compact('order'));
    }
}
