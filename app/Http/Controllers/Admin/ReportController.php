<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Expname;
use App\Models\Expdetail;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Uorder;
use App\Models\UorderItem;
use App\Models\POrder;
use App\Models\PItem;
use App\Models\Auser;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ReportController extends Controller
{
    /**
     * Show the General Reports & Customer Matrices panel.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');

        // Setup base queries for each ledger type
        $customersQuery = User::where('usertype', 'C')->orderBy('uname', 'asc');
        $dealersQuery = User::where('usertype', 'D')->orderBy('uname', 'asc');
        $sdealersQuery = User::where('usertype', 'S')->orderBy('uname', 'asc');

        // Apply searching if supplied
        if ($search) {
            $customersQuery->where(function($q) use ($search) {
                $q->where('uname', 'like', "%{$search}%")
                  ->orWhere('contactno', 'like', "%{$search}%")
                  ->orWhere('billingcity', 'like', "%{$search}%");
            });

            $dealersQuery->where(function($q) use ($search) {
                $q->where('uname', 'like', "%{$search}%")
                  ->orWhere('contactno', 'like', "%{$search}%")
                  ->orWhere('billingcity', 'like', "%{$search}%");
            });

            $sdealersQuery->where(function($q) use ($search) {
                $q->where('uname', 'like', "%{$search}%")
                  ->orWhere('contactno', 'like', "%{$search}%")
                  ->orWhere('billingcity', 'like', "%{$search}%");
            });
        }

        // Retrieve paginated records for extreme performance
        $customers = $customersQuery->paginate(15, ['*'], 'customers_page');
        $dealers = $dealersQuery->paginate(15, ['*'], 'dealers_page');
        $sdealers = $sdealersQuery->paginate(15, ['*'], 'sdealers_page');

        // Fetch active expenses and products for the legacy report form
        $expenses = Expname::where('estatus', 1)->orderBy('exp_name', 'asc')->get();
        $products = Product::where('status', 1)->orderBy('productname', 'asc')->get();

        return view('admin.reports.index', compact('customers', 'dealers', 'sdealers', 'search', 'expenses', 'products'));
    }

    /**
     * Streams Excel ledger lists based on selected report types.
     */
    public function exportExcel(Request $request)
    {
        $request->validate([
            'report' => 'required|integer|in:1,2,3,4,5'
        ]);

        $reportType = (int) $request->input('report');
        
        // Define filters and schemas based on user report choice
        switch ($reportType) {
            case 1: // Customer Report (Local)
                $usertype = 'C';
                $isOnline = false;
                $filename = 'Customer_Details.xls';
                $title = 'CUSTOMER DETAILS';
                break;
            case 2: // Dealer Report (Local)
                $usertype = 'D';
                $isOnline = false;
                $filename = 'Dealer_Details.xls';
                $title = 'DEALER DETAILS';
                break;
            case 3: // S-Dealer Report (Local)
                $usertype = 'S';
                $isOnline = false;
                $filename = 'SDealer_Details.xls';
                $title = 'S-DEALER DETAILS';
                break;
            case 4: // Online Customer Report
                $usertype = 'C';
                $isOnline = true;
                $filename = 'Online_Customer_Details.xls';
                $title = 'ONLINE CUSTOMER DETAILS';
                break;
            case 5: // Online Dealer Report
                $usertype = 'D';
                $isOnline = true;
                $filename = 'Online_Dealer_Details.xls';
                $title = 'ONLINE DEALER DETAILS';
                break;
        }

        // Retrieve dataset
        $users = User::where('usertype', $usertype)
            ->orderBy('uname', 'asc')
            ->get();

        // Build Tab-Separated Data matching legacy Fopen/Headers stream exactly
        return response()->stream(function() use ($users, $isOnline, $title) {
            // Write column headers
            if (!$isOnline) {
                $columns = ["Name", "Address", "City", "State", "Pincode", "Mobile No"];
            } else {
                $columns = ["Name", "Street", "City", "State", "Pincode", "Email", "Mobile No"];
            }

            // Capitalize headers
            $headerLine = implode("\t", array_map('ucwords', $columns)) . "\n";
            echo $headerLine;

            // Stream rows
            foreach ($users as $user) {
                $row = [];
                if (!$isOnline) {
                    $row[] = '"' . str_replace('"', '""', $user->uname) . '"';
                    $row[] = '"' . str_replace('"', '""', $user->billingaddress) . '"';
                    $row[] = '"' . str_replace('"', '""', $user->billingcity) . '"';
                    $row[] = '"' . str_replace('"', '""', $user->billingstate) . '"';
                    $row[] = '"' . str_replace('"', '""', $user->billingpincode) . '"';
                    $row[] = '"' . str_replace('"', '""', $user->contactno) . '"';
                } else {
                    $row[] = '"' . str_replace('"', '""', $user->uname) . '"';
                    $row[] = '"' . str_replace('"', '""', $user->billingaddress) . '"';
                    $row[] = '"' . str_replace('"', '""', $user->billingcity) . '"';
                    $row[] = '"' . str_replace('"', '""', $user->billingstate) . '"';
                    $row[] = '"' . str_replace('"', '""', $user->billingpincode) . '"';
                    $row[] = '"' . str_replace('"', '""', $user->email) . '"';
                    $row[] = '"' . str_replace('"', '""', $user->contactno) . '"';
                }

                echo implode("\t", $row) . "\n";
            }
        }, 200, [
            'Content-Type' => 'application/octet-stream',
            'Content-Disposition' => 'attachment; filename=' . $filename,
            'Pragma' => 'no-cache',
            'Expires' => '0'
        ]);
    }

    /**
     * Show the printable general business reports.
     */
    public function print(Request $request)
    {
        $request->validate([
            'report' => 'required|string|in:expenses,itemnameoff,itemnameon,purchase',
            'startDate' => 'required',
            'endDate' => 'required',
        ]);

        $reportType = $request->input('report');
        $startDateRaw = $request->input('startDate');
        $endDateRaw = $request->input('endDate');

        // Parse dates safely using Carbon
        try {
            $startDate = Carbon::createFromFormat('d-m-Y', $startDateRaw)->startOfDay();
        } catch (\Exception $e) {
            try {
                $startDate = Carbon::parse($startDateRaw)->startOfDay();
            } catch (\Exception $ex) {
                return redirect()->back()->with('error', 'Invalid start date format.');
            }
        }

        try {
            $endDate = Carbon::createFromFormat('d-m-Y', $endDateRaw)->endOfDay();
        } catch (\Exception $e) {
            try {
                $endDate = Carbon::parse($endDateRaw)->endOfDay();
            } catch (\Exception $ex) {
                return redirect()->back()->with('error', 'Invalid end date format.');
            }
        }

        $data = null;
        $selectedName = 'All';

        if ($reportType === 'expenses') {
            $expenseId = (int) $request->input('expNameo', 0);
            $query = Expdetail::with(['category', 'staff'])
                ->whereBetween('exp_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')]);

            if ($expenseId > 0) {
                $query->where('exp_name', $expenseId);
                $selectedCat = Expname::find($expenseId);
                if ($selectedCat) {
                    $selectedName = $selectedCat->exp_name;
                }
            }

            $data = $query->orderBy('exp_id', 'asc')->get();

        } elseif ($reportType === 'itemnameoff') {
            $productId = (int) $request->input('productd', 0);
            $query = OrderItem::with(['order.user', 'product'])
                ->whereHas('order', function($q) use ($startDate, $endDate) {
                    $q->where('order_status', 1)
                      ->whereBetween('order_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')]);
                });

            if ($productId > 0) {
                $query->where('product_id', $productId);
                $prod = Product::find($productId);
                if ($prod) {
                    $selectedName = $prod->productname;
                }
            }

            $data = $query->get()->sortBy(function($item) {
                return $item->order->order_date;
            });

        } elseif ($reportType === 'itemnameon') {
            $productId = (int) $request->input('productd', 0);
            $query = UorderItem::with(['order.user', 'product'])
                ->whereHas('order', function($q) use ($startDate, $endDate) {
                    $q->whereNotNull('ostatus')
                      ->where('ostatus', '<>', 'c')
                      ->whereBetween(DB::raw('DATE(orderdate)'), [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')]);
                });

            if ($productId > 0) {
                $query->where('productId', $productId);
                $prod = Product::find($productId);
                if ($prod) {
                    $selectedName = $prod->productname;
                }
            }

            $data = $query->get()->sortBy(function($item) {
                return $item->order->orderdate;
            });

        } elseif ($reportType === 'purchase') {
            $productId = (int) $request->input('productd', 0);
            $query = PItem::with(['purchaseOrder', 'product'])
                ->whereHas('purchaseOrder', function($q) use ($startDate, $endDate) {
                    $q->whereBetween('porder_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')]);
                });

            if ($productId > 0) {
                $query->where('prod_id', $productId);
                $prod = Product::find($productId);
                if ($prod) {
                    $selectedName = $prod->productname;
                }
            }

            $data = $query->get()->sortBy(function($item) {
                return $item->purchaseOrder->porder_date;
            });
        }

        return view('admin.reports.print', compact('reportType', 'startDate', 'endDate', 'data', 'selectedName'));
    }

    /**
     * Show the Billwise Report page (ref: admin/reportbillmtl.php).
     */
    public function billwise()
    {
        return view('admin.reports.billwise');
    }

    /**
     * AJAX: Fetch bill numbers for a given month, year, and order type.
     * Mirrors logic from admin/php_action/fetchBillwiseReportmtl.php.
     */
    public function fetchBillwiseNumbers(Request $request)
    {
        $request->validate([
            'month' => 'required|integer|between:1,12',
            'year'  => 'required|integer|min:2020',
            'sname' => 'required|integer',
        ]);

        $month = (int) $request->input('month');
        $year  = (int) $request->input('year');
        $sname = (int) $request->input('sname');

        if (in_array($sname, [1, 2])) {
            // Offline orders
            $rows = DB::table('orders')
                ->whereMonth('order_date', $month)
                ->whereYear('order_date', $year)
                ->where('order_status', 1)
                ->orderBy('morder_id', 'asc')
                ->get(['order_id', 'morder_id']);

            $data = $rows->map(fn($r) => [$r->order_id, $r->morder_id])->values();

        } elseif (in_array($sname, [3, 4])) {
            // Online orders
            $query = DB::table('uorder')
                ->whereMonth('orderdate', $month)
                ->whereYear('orderdate', $year)
                ->where('ostatus', '<>', 'c');

            if ($sname === 4) {
                $query->where('utype', 'D');
            }

            $rows = $query->orderBy('morderid', 'asc')->get(['orderid', 'morderid']);
            $data = $rows->map(fn($r) => [$r->orderid, $r->morderid])->values();

        } else {
            // Purchase orders
            $rows = DB::table('p_orders')
                ->whereMonth('porder_date', $month)
                ->whereYear('porder_date', $year)
                ->orderBy('porder_id', 'asc')
                ->get(['porder_id', 'morder_id']);

            $data = $rows->map(fn($r) => [$r->porder_id, $r->morder_id])->values();
        }

        return response()->json($data);
    }

    /**
     * Print a single offline or online sale bill (mirrors printSalesmtl / printSalesocmtl).
     */
    public function printBillwiseSale(Request $request)
    {
        $request->validate([
            'orderId' => 'required|integer',
            'sname'   => 'required|integer',
        ]);

        $orderId = (int) $request->input('orderId');
        $sname   = (int) $request->input('sname');

        if (in_array($sname, [1, 2])) {
            // Offline sale
            $sale = Order::with(['items.product', 'user'])->find($orderId);
            if (!$sale) {
                abort(404, 'Order not found.');
            }
            return view('admin.sales.print', compact('sale'));
        } else {
            // Online order
            $order = Uorder::with(['items.product', 'user'])->find($orderId);
            if (!$order) {
                abort(404, 'Order not found.');
            }
            return view('admin.online_orders.print', compact('order'));
        }
    }

    /**
     * Print a single purchase bill (mirrors printPurchasemtl).
     */
    public function printBillwisePurchase(Request $request)
    {
        $request->validate([
            'orderId' => 'required|integer',
        ]);

        $orderId = (int) $request->input('orderId');

        $purchase = POrder::with(['items.product'])->find($orderId);
        if (!$purchase) {
            abort(404, 'Purchase not found.');
        }

        $items = $purchase->items;
        foreach ($items as $item) {
            $item->product = Product::find($item->prod_id);
        }

        $creator = Auser::find($purchase->staffname);

        return view('admin.purchases.print', compact('purchase', 'items', 'creator'));
    }

    /**
     * Show the Sales Report page (ref: admin/reportsalesmtl.php).
     */
    public function sales()
    {
        return view('admin.reports.sales');
    }

    /**
     * AJAX: Fetch customers by order section/type.
     * Mirrors logic from admin/php_action/fetchReportSalesmtl.php.
     */
    public function fetchSalesCustomers(Request $request)
    {
        $request->validate([
            'csect' => 'required|integer',
        ]);

        $csect = (int) $request->input('csect');
        $data = [];

        if ($csect === 1 || $csect === 2) {
            // Offline customers: id, uname, billingaddress, contactno
            $data = DB::table('orders')
                ->join('users', 'users.id', '=', 'orders.user_id')
                ->select('users.id', 'users.uname', 'users.billingaddress', 'users.contactno')
                ->distinct()
                ->orderBy('users.uname', 'asc')
                ->get()
                ->map(fn($row) => [$row->id, $row->uname, $row->billingaddress ?? '', $row->contactno ?? '']);
        } elseif ($csect === 3 || $csect === 4) {
            // Online customers/dealers
            $utype = ($csect === 4) ? 'D' : 'C';
            $data = DB::table('uorder')
                ->join('users', 'users.id', '=', 'uorder.userid')
                ->where('uorder.utype', $utype)
                ->where('uorder.ostatus', '<>', 'c')
                ->whereNotNull('uorder.ostatus')
                ->select('users.id', 'users.uname', 'users.contactno')
                ->distinct()
                ->orderBy('users.uname', 'asc')
                ->get()
                ->map(fn($row) => [$row->id, $row->uname, $row->contactno ?? '']);
        }

        return response()->json($data);
    }

    /**
     * Print sales report filtered by order type & date range.
     * Mirrors logic from admin/php_action/getReportSalesmtl.php.
     */
    public function generateSalesReportByType(Request $request)
    {
        $request->validate([
            'catname'   => 'required|integer',
            'startDate' => 'required',
            'endDate'   => 'required',
        ]);

        $cat = (int) $request->input('catname');
        $startDateRaw = $request->input('startDate');
        $endDateRaw = $request->input('endDate');

        try {
            $startDate = Carbon::createFromFormat('d-m-Y', $startDateRaw)->startOfDay();
        } catch (\Exception $e) {
            try {
                $startDate = Carbon::parse($startDateRaw)->startOfDay();
            } catch (\Exception $ex) {
                return redirect()->back()->with('error', 'Invalid start date format.');
            }
        }

        try {
            $endDate = Carbon::createFromFormat('d-m-Y', $endDateRaw)->endOfDay();
        } catch (\Exception $e) {
            try {
                $endDate = Carbon::parse($endDateRaw)->endOfDay();
            } catch (\Exception $ex) {
                return redirect()->back()->with('error', 'Invalid end date format.');
            }
        }

        $sales = collect();
        $catname = '';

        if ($cat === 1 || $cat === 2) {
            $catname = 'OFFLINE';
            $sales = Order::with(['items.product', 'user'])
                ->whereBetween('order_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                ->where('order_status', 1)
                ->orderBy('order_date', 'asc')
                ->get();
        } elseif ($cat === 3 || $cat === 4) {
            $catname = ($cat === 4) ? 'O_DEALER' : 'O_CUSTOMER';
            $utype = ($cat === 4) ? 'D' : 'C';

            $sales = Uorder::with(['items.product', 'user'])
                ->whereBetween(DB::raw('DATE(orderdate)'), [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                ->where('utype', $utype)
                ->where('ostatus', '<>', 'c')
                ->whereNotNull('ostatus')
                ->orderBy('orderdate', 'asc')
                ->get();
        }

        return view('admin.reports.sales_print_type', compact('sales', 'startDate', 'endDate', 'catname', 'cat'));
    }

    /**
     * Print sales report filtered by customer name/ID.
     * Mirrors logic from admin/php_action/getReportSalesNamemtl.php.
     */
    public function generateSalesReportByName(Request $request)
    {
        $request->validate([
            'csect' => 'required|integer',
            'cname' => 'required|integer',
        ]);

        $csect = (int) $request->input('csect');
        $cname = (int) $request->input('cname');

        $user = User::findOrFail($cname);
        $sales = collect();
        $catname = '';

        if ($csect === 1 || $csect === 2) {
            $catname = 'OFFLINE';
            $sales = Order::with(['items.product', 'user'])
                ->where('user_id', $cname)
                ->where('order_status', 1)
                ->orderBy('order_date', 'asc')
                ->get();
        } elseif ($csect === 3 || $csect === 4) {
            $utype = ($csect === 4) ? 'D' : 'C';
            $catname = ($csect === 4) ? 'DEALER' : 'CUSTOMER';

            $sales = Uorder::with(['items.product', 'user'])
                ->where('userid', $cname)
                ->where('utype', $utype)
                ->where('ostatus', '<>', 'c')
                ->whereNotNull('ostatus')
                ->orderBy('orderdate', 'asc')
                ->get();
        }

        return view('admin.reports.sales_print_name', compact('sales', 'user', 'catname', 'csect'));
    }

    /**
     * Show the Pending Amount Report page (ref: admin/reportpendingmtl.php).
     */
    public function pending()
    {
        return view('admin.reports.pending');
    }

    /**
     * Print pending report filtered by order type & date range.
     * Mirrors logic from admin/php_action/getPendingReportmtl.php.
     */
    public function generatePendingReport(Request $request)
    {
        $request->validate([
            'oname'     => 'required|integer',
            'startDate' => 'required',
            'endDate'   => 'required',
        ]);

        $ctype = (int) $request->input('oname');
        $startDateRaw = $request->input('startDate');
        $endDateRaw = $request->input('endDate');

        try {
            $startDate = Carbon::createFromFormat('d-m-Y', $startDateRaw)->startOfDay();
        } catch (\Exception $e) {
            try {
                $startDate = Carbon::parse($startDateRaw)->startOfDay();
            } catch (\Exception $ex) {
                return redirect()->back()->with('error', 'Invalid start date format.');
            }
        }

        try {
            $endDate = Carbon::createFromFormat('d-m-Y', $endDateRaw)->endOfDay();
        } catch (\Exception $e) {
            try {
                $endDate = Carbon::parse($endDateRaw)->endOfDay();
            } catch (\Exception $ex) {
                return redirect()->back()->with('error', 'Invalid end date format.');
            }
        }

        $sales = collect();

        if ($ctype === 1 || $ctype === 2) {
            // Offline pending orders
            $sales = Order::with('user')
                ->where('order_status', 1)
                ->where('due', '>', 0)
                ->whereBetween('order_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                ->orderBy('order_date', 'asc')
                ->get();
        } elseif ($ctype === 3 || $ctype === 4) {
            // Online pending orders (utype: 'C' or 'D')
            $utype = ($ctype === 4) ? 'D' : 'C';

            $sales = Uorder::with('user')
                ->where(function($q) {
                    $q->whereNull('ostatus')
                      ->orWhere('ostatus', 'p')
                      ->orWhere('ostatus', '');
                })
                ->where('utype', $utype)
                ->whereBetween(DB::raw('DATE(orderdate)'), [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                ->orderBy('orderdate', 'desc')
                ->get();
        }

        return view('admin.reports.pending_print', compact('sales', 'startDate', 'endDate', 'ctype'));
    }

    /**
     * Show the Stock Report dashboard (ref: admin/reportstockmtl.php).
     */
    public function stock()
    {
        $brands = DB::table('brands')->where('brand_status', 1)->orderBy('brand_name', 'asc')->get();
        $categories = DB::table('category')->where('status', 1)->orderBy('cat_name', 'asc')->get();
        
        // Paginated list of products for high-performance preview in the dashboard
        $products = Product::with(['brand', 'category'])
            ->where('status', 1)
            ->orderBy('productname', 'asc')
            ->paginate(15);

        return view('admin.reports.stock', compact('brands', 'categories', 'products'));
    }

    /**
     * Print stock report filtered by selection criteria.
     * Mirrors logic from admin/php_action/getStockReportmtl.php and sub-actions.
     */
    public function generateStockReport(Request $request)
    {
        $stock = $request->input('report');
        $bname = $request->input('bname');
        $cname = $request->input('cname');
        $qty = $request->input('tno');

        $query = Product::with(['brand', 'category'])->where('status', 1);
        $stockType = 'STOCK DETAILS';
        $isStockLevelReport = false;

        if ($stock && $stock !== '0') {
            $isStockLevelReport = true;
            if ($stock === 'A') {
                $query->where('tqty', '>=', 1);
                $stockType = 'ALL STOCK DETAILS';
            } elseif ($stock === 'L') {
                $query->where('tqty', '<=', 5);
                $stockType = 'LOW STOCK DETAILS';
            } elseif ($stock === 'M') {
                $query->whereBetween('tqty', [5, 15]);
                $stockType = 'MEDIUM STOCK DETAILS';
            } elseif ($stock === 'H') {
                $query->where('tqty', '>', 15);
                $stockType = 'HIGH STOCK DETAILS';
            }
            $query->orderBy('catid')->orderBy('tqty');
        } elseif ($bname && $bname !== '0') {
            $query->where('brandid', $bname)->orderBy('tqty');
            $brand = DB::table('brands')->where('brand_id', $bname)->first();
            if ($brand) {
                $stockType = 'STOCK DETAILS FOR BRAND: ' . strtoupper($brand->brand_name);
            }
        } elseif ($cname && $cname !== '0') {
            $query->where('catid', $cname)->orderBy('tqty');
            $cat = DB::table('category')->where('cat_id', $cname)->first();
            if ($cat) {
                $stockType = 'STOCK DETAILS FOR CATEGORY: ' . strtoupper($cat->cat_name);
            }
        } elseif ($qty !== null && $qty !== '') {
            $qtyVal = (int) $qty;
            $query->where('tqty', '<=', $qtyVal)->orderBy('tqty');
            $stockType = 'STOCK DETAILS (QTY <= ' . $qtyVal . ')';
        } else {
            return redirect()->back()->with('error', 'Please select at least one filtering option.');
        }

        $products = $query->get();

        return view('admin.reports.stock_print', compact('products', 'stockType', 'isStockLevelReport'));
    }

    /**
     * Show the Pay History Report lookup dashboard (ref: admin/reportpayhistrymtl.php).
     */
    public function payHistory(Request $request)
    {
        $customers = User::where('ustatus', 1)->where('usertype', 'C')->orderBy('uname', 'asc')->get();
        $dealers = User::where('ustatus', 1)->where('usertype', 'D')->orderBy('uname', 'asc')->get();
        $sdealers = User::where('ustatus', 1)->where('usertype', 'S')->orderBy('uname', 'asc')->get();

        $selectedUserId = $request->input('user_id');
        $ledgerData = null;
        $selectedUser = null;

        if ($selectedUserId) {
            $selectedUser = User::find($selectedUserId);
            if ($selectedUser) {
                $ledgerData = $this->fetchLedgerData($selectedUser);
            }
        }

        return view('admin.reports.payhistory', compact('customers', 'dealers', 'sdealers', 'ledgerData', 'selectedUser'));
    }

    /**
     * Print the payment history ledger for a chosen customer/dealer/sdealer.
     */
    public function generatePayHistoryReport(Request $request)
    {
        $userId = $request->input('agentname') ?: ($request->input('oagentname') ?: $request->input('soagentname'));
        
        if (!$userId) {
            return redirect()->back()->with('error', 'Please select a customer or dealer first.');
        }

        $user = User::findOrFail($userId);
        $ledgerData = $this->fetchLedgerData($user);

        return view('admin.reports.payhistory_print', compact('user', 'ledgerData'));
    }

    /**
     * Helper to fetch ledger history log matching legacy queries exactly.
     */
    private function fetchLedgerData(User $user)
    {
        $ledger = [];
        $overallBalance = 0;

        if ($user->usertype === 'C') {
            // Local Customer - fetch offline order details
            $orders = Order::where('user_id', $user->id)
                ->where('order_status', 1)
                ->orderBy('order_date', 'desc')
                ->get();

            foreach ($orders as $order) {
                $payments = DB::table('orderbal')
                    ->where('order_id', $order->order_id)
                    ->orderBy('order_idbal', 'asc')
                    ->get();

                $ledger[] = [
                    'order' => $order,
                    'payments' => $payments,
                    'is_online' => false
                ];
                
                if ($payments->count() > 0) {
                    $overallBalance += $payments->last()->bamount;
                } else {
                    $overallBalance += $order->due;
                }
            }
        } else {
            // Dealer or S-Dealer - fetch online order details
            $orders = Uorder::where('userid', $user->id)
                ->where('utype', $user->usertype)
                ->where(function($q) {
                    $q->whereNull('ostatus')
                      ->orWhere('ostatus', '<>', 'c');
                })
                ->orderBy('orderdate', 'desc')
                ->get();

            foreach ($orders as $order) {
                $payments = DB::table('uorderbal')
                    ->where('orderid', $order->orderid)
                    ->orderBy('balid', 'asc')
                    ->get();

                $ledger[] = [
                    'order' => $order,
                    'payments' => $payments,
                    'is_online' => true
                ];

                if ($payments->count() > 0) {
                    $overallBalance += $payments->last()->bamount;
                } else {
                    $overallBalance += $order->bamount;
                }
            }
        }

        return [
            'ledger' => $ledger,
            'overall_balance' => $overallBalance
        ];
    }

    /**
     * Show the Excel export dashboard form selection (ref: admin/reportoexcel.php).
     */
    public function excel()
    {
        return view('admin.reports.excel');
    }

    /**
     * Show the Profit & Loss statement configuration dashboard (ref: admin/reportpl.php).
     */
    public function profit_loss()
    {
        // Enforce Super Admin security restrict
        if (Auth::guard('admin')->user()->section != 1) {
            abort(403, 'Unauthorized access to Financial balance sheets.');
        }

        return view('admin.reports.pl');
    }

    /**
     * Print Profit & Loss balance sheet calculations.
     * Mirrors logic from admin/php_action/getProfitLossn.php exactly.
     */
    public function generateProfitLossReport(Request $request)
    {
        // Enforce Super Admin security restrict
        if (Auth::guard('admin')->user()->section != 1) {
            abort(403, 'Unauthorized access.');
        }

        $request->validate([
            'startDate' => 'required',
            'endDate'   => 'required',
        ]);

        $startDateRaw = $request->input('startDate');
        $endDateRaw = $request->input('endDate');

        try {
            $startDate = Carbon::createFromFormat('d-m-Y', $startDateRaw)->startOfDay();
        } catch (\Exception $e) {
            try {
                $startDate = Carbon::parse($startDateRaw)->startOfDay();
            } catch (\Exception $ex) {
                return redirect()->back()->with('error', 'Invalid start date format.');
            }
        }

        try {
            $endDate = Carbon::createFromFormat('d-m-Y', $endDateRaw)->endOfDay();
        } catch (\Exception $e) {
            try {
                $endDate = Carbon::parse($endDateRaw)->endOfDay();
            } catch (\Exception $ex) {
                return redirect()->back()->with('error', 'Invalid end date format.');
            }
        }

        $start_date = $startDate->format('Y-m-d');
        $end_date = $endDate->format('Y-m-d');

        // 1. Customer Offline Sales (section 1)
        $CData = DB::table('orders')
            ->whereBetween('order_date', [$start_date, $end_date])
            ->where('section', 1)
            ->selectRaw('COALESCE(sum(grand_total), 0) as gt, COALESCE(sum(paid), 0) as pa, COALESCE(sum(due), 0) as du')
            ->first();

        // 2. Dealer Offline Sales (section 2)
        $DData = DB::table('orders')
            ->whereBetween('order_date', [$start_date, $end_date])
            ->where('section', 2)
            ->selectRaw('COALESCE(sum(grand_total), 0) as gt, COALESCE(sum(paid), 0) as pa, COALESCE(sum(due), 0) as du')
            ->first();

        // 3. Online Customer Sales (utype 'C')
        $OCData = DB::table('uorder')
            ->whereBetween(DB::raw('DATE(orderdate)'), [$start_date, $end_date])
            ->where('ostatus', '<>', 'c')
            ->where('ostatus', '<>', '')
            ->where('utype', 'C')
            ->selectRaw('COALESCE(sum(gamount), 0) as gt, COALESCE(sum(pamount), 0) as pa, COALESCE(sum(bamount), 0) as du')
            ->first();

        // 4. Online Dealer Sales (utype 'D')
        $ODData = DB::table('uorder')
            ->whereBetween(DB::raw('DATE(orderdate)'), [$start_date, $end_date])
            ->where('ostatus', '<>', 'c')
            ->where('ostatus', '<>', '')
            ->where('utype', 'D')
            ->selectRaw('COALESCE(sum(gamount), 0) as gt, COALESCE(sum(pamount), 0) as pa, COALESCE(sum(bamount), 0) as du')
            ->first();

        // 5. Purchases Paid
        $PData = DB::table('p_orders')
            ->whereBetween('porder_date', [$start_date, $end_date])
            ->selectRaw('COALESCE(sum(g_total), 0) as gt, COALESCE(sum(ppaid), 0) as pa, COALESCE(sum(pbal), 0) as du')
            ->first();

        // 6. Expenses Paid
        $EData = DB::table('expdetails')
            ->whereBetween('exp_date', [$start_date, $end_date])
            ->selectRaw('COALESCE(sum(exp_amount), 0) as gt')
            ->first();

        return view('admin.reports.pl_print', compact('startDate', 'endDate', 'CData', 'DData', 'OCData', 'ODData', 'PData', 'EData'));
    }
}
