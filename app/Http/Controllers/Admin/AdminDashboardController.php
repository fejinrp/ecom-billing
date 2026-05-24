<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Order;
use App\Models\Uorder;
use App\Models\POrder;
use App\Models\Expdetail;
use App\Models\Mtlstock;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdminDashboardController extends Controller
{
    /**
     * Show the unified administrative dashboard.
     */
    public function index(Request $request)
    {
        $today = Carbon::today('Asia/Kolkata');
        $currentTime = $today->format('Y-m-d');
        $yesterdayTime = Carbon::yesterday('Asia/Kolkata')->format('Y-m-d');
        $firstday = Carbon::now('Asia/Kolkata')->startOfMonth()->format('Y-m-d');
        $lastday = Carbon::now('Asia/Kolkata')->endOfMonth()->format('Y-m-d');

        // Yesterday's stats
        $yesterdayRevenuedayMOC = Uorder::where('ostatus', '<>', '')
            ->where('ostatus', '<>', 'c')
            ->whereDate('orderdate', $yesterdayTime)
            ->sum('pamount');

        $yesterdayRevenuedayMC = Order::whereDate('order_date', $yesterdayTime)
            ->sum('paid');

        $yesterdaySales = $yesterdayRevenuedayMOC + $yesterdayRevenuedayMC;

        $PyesterdayMD = POrder::whereDate('porder_date', $yesterdayTime)
            ->sum('ppaid');

        $yesterdayExp = Expdetail::whereDate('exp_date', $yesterdayTime)
            ->sum('exp_amount');

        $yesterdaySalesFinal = $yesterdaySales - $yesterdayExp - $PyesterdayMD;

        // Today's stats
        $totalRevenuedayMOC = Uorder::where('ostatus', '<>', '')
            ->where('ostatus', '<>', 'c')
            ->whereDate('orderdate', $currentTime)
            ->sum('pamount');

        $totalRevenuedayMC = Order::whereDate('order_date', $currentTime)
            ->sum('paid');

        $todaySales = $totalRevenuedayMOC + $totalRevenuedayMC;

        $totalPurchaseday = POrder::whereDate('porder_date', $currentTime)
            ->sum('g_total');

        $totalPurchasePaidday = POrder::whereDate('porder_date', $currentTime)
            ->sum('ppaid');

        $totalExp = Expdetail::whereDate('exp_date', $currentTime)
            ->sum('exp_amount');

        $todayIncome = $todaySales - $totalExp - $totalPurchasePaidday;

        // Sync Stock hand
        $this->syncStockHand($currentTime, $yesterdayTime, $yesterdaySalesFinal, $todayIncome);

        // Fetch openstock and closestock
        $openstockRow = Mtlstock::whereDate('stockdate', $yesterdayTime)->first();
        $openstock = $openstockRow ? $openstockRow->closestock : 0;

        $closestockRow = Mtlstock::whereDate('stockdate', $currentTime)->first();
        $closestock = $closestockRow ? $closestockRow->closestock : $todayIncome;

        // Monthly stats
        $MtotalRevenuedayMOC = Uorder::where('ostatus', '<>', '')
            ->where('ostatus', '<>', 'c')
            ->where('utype', 'C')
            ->whereDate('orderdate', '>=', $firstday)
            ->whereDate('orderdate', '<=', $lastday)
            ->sum('pamount');

        $MtotalRevenuedayMOD = Uorder::where('ostatus', '<>', '')
            ->where('ostatus', '<>', 'c')
            ->where('utype', 'D')
            ->whereDate('orderdate', '>=', $firstday)
            ->whereDate('orderdate', '<=', $lastday)
            ->sum('pamount');

        $MtotalRevenuedayMC = Order::where('section', 1)
            ->whereDate('order_date', '>=', $firstday)
            ->whereDate('order_date', '<=', $lastday)
            ->sum('paid');

        $MtotalPurchaseday = POrder::whereDate('porder_date', '>=', $firstday)
            ->whereDate('porder_date', '<=', $lastday)
            ->sum('g_total');

        $MtotalPurchasePaidday = POrder::whereDate('porder_date', '>=', $firstday)
            ->whereDate('porder_date', '<=', $lastday)
            ->sum('ppaid');

        $MtotalExp = Expdetail::whereDate('exp_date', '>=', $firstday)
            ->whereDate('exp_date', '<=', $lastday)
            ->sum('exp_amount');

        $monthlyIncomeM = $MtotalRevenuedayMOC + $MtotalRevenuedayMOD + $MtotalRevenuedayMC - $MtotalExp - $MtotalPurchasePaidday;

        // Counts
        $countOrder = Uorder::whereNull('ostatus')
            ->whereDate('orderdate', $currentTime)
            ->count();

        $countPenOrder = Uorder::where(function($query) {
                $query->where('ostatus', 'p')->orWhereNull('ostatus');
            })
            ->whereDate('orderdate', '<=', $currentTime)
            ->count();

        $countOOrder = Order::whereDate('order_date', $currentTime)
            ->count();

        $countLowStock = Product::where('tqty', '<', 10)
            ->where('status', 1)
            ->count();

        // Lists
        $todayBills = Order::whereDate('order_date', $currentTime)->get();
        $todayDealerBills = Uorder::whereDate('orderdate', $currentTime)->get();

        $todayExpenses = Expdetail::select('expdetails.*', 'ausers.username', 'expname.exp_name as expense_category_name')
            ->join('ausers', 'ausers.user_id', '=', 'expdetails.sname')
            ->join('expname', 'expname.exp_id', '=', 'expdetails.exp_name')
            ->whereDate('expdetails.exp_date', $currentTime)
            ->orderBy('expdetails.exp_id', 'desc')
            ->get();

        $products = Product::where('tqty', '>', 0)
            ->where('status', 1)
            ->orderBy('productname', 'asc')
            ->get();

        $expenseCategories = DB::table('expname')->where('estatus', 1)->get();

        return view('admin.dashboard', compact(
            'todaySales', 'totalPurchaseday', 'totalExp', 'todayIncome',
            'openstock', 'closestock', 'monthlyIncomeM', 'countLowStock',
            'countOrder', 'countOOrder', 'countPenOrder', 'todayBills', 'todayDealerBills',
            'todayExpenses', 'products', 'expenseCategories'
        ));
    }

    /**
     * Store new expense.
     */
    public function storeExpense(Request $request)
    {
        $request->validate([
            'eName' => 'required|integer',
            'eAmount' => 'required|numeric|min:0.01',
            'eDate' => 'required|date',
        ]);

        $user = Auth::guard('admin')->user();

        Expdetail::create([
            'exp_date' => Carbon::parse($request->input('eDate'))->format('Y-m-d'),
            'exp_name' => $request->input('eName'),
            'exp_amount' => $request->input('eAmount'),
            'sname' => $user->user_id,
            'mexp_id' => 0, // default
            'estatus' => 1
        ]);

        return redirect()->route('admin.dashboard')->with('success', 'Expense successfully added!');
    }

    /**
     * Replicates legacy stock synchronization logic.
     */
    protected function syncStockHand($currentTime, $yesterdayTime, $yesterdaySalesFinal, $todayIncome)
    {
        $userId = Auth::guard('admin')->id();
        $stock = Mtlstock::whereDate('stockdate', $currentTime)->first();

        if ($stock) {
            $stock->stockdate = Carbon::now('Asia/Kolkata');
            $stock->closestock = $todayIncome;
            $stock->todaystock = $todayIncome;
            $stock->save();
        } else {
            Mtlstock::create([
                'stockdate' => Carbon::now('Asia/Kolkata'),
                'cashhand' => $yesterdaySalesFinal,
                'openstock' => $yesterdaySalesFinal,
                'closestock' => $todayIncome,
                'todaystock' => $todayIncome,
                'uname' => $userId
            ]);
        }
    }
}
