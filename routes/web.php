<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\BrandController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\SubcategoryController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ExpenseCategoryController;
use App\Http\Controllers\Admin\ExpenseController;
use App\Http\Controllers\Admin\SalesController;
use App\Http\Controllers\Admin\OnlineOrderController;
use App\Http\Controllers\Admin\QuotationController;
use App\Http\Controllers\Admin\BarcodeController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\PurchaseController;
use App\Http\Controllers\Admin\PurchaseStockController;
use App\Http\Controllers\Admin\StategstController;
use App\Http\Controllers\Admin\AgentController;
use App\Http\Controllers\Admin\AgentpayController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\UserSettingController;
use App\Http\Controllers\Admin\UserCustomerController;
use App\Http\Controllers\Admin\BackupController;
use App\Models\Category;
use App\Models\Order;
use App\Http\Controllers\StorefrontController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;


// Public E-Commerce Storefront Routes
Route::get('/', [StorefrontController::class, 'index'])->name('storefront.index');
Route::get('/api/search-suggestions', [StorefrontController::class, 'searchSuggestions'])->name('storefront.search_suggestions');
Route::get('/shop', [StorefrontController::class, 'shop'])->name('storefront.shop');
Route::get('/category/{name}', [StorefrontController::class, 'category'])->name('storefront.category');
Route::get('/product/{id}', [StorefrontController::class, 'product'])->name('storefront.product');
Route::get('/cart', [StorefrontController::class, 'cart'])->name('storefront.cart');
Route::post('/cart/add/{id}', [StorefrontController::class, 'addToCart'])->name('storefront.cart.add');
Route::post('/cart/update', [StorefrontController::class, 'updateCart'])->name('storefront.cart.update');
Route::get('/cart/remove/{id}', [StorefrontController::class, 'removeFromCart'])->name('storefront.cart.remove');
Route::get('/checkout', [StorefrontController::class, 'checkout'])->name('storefront.checkout');
Route::post('/checkout/order', [StorefrontController::class, 'placeOrder'])->name('storefront.checkout.order');
Route::get('/order/success/{id}', [StorefrontController::class, 'orderSuccess'])->name('storefront.order.success');
Route::get('/orders', [StorefrontController::class, 'orderHistory'])->name('storefront.orders');
Route::get('/orders/{id}', [StorefrontController::class, 'orderDetails'])->name('storefront.order_details');
Route::get('/orders/{id}/print', [StorefrontController::class, 'orderPrint'])->name('storefront.order_print');

Route::get('/dashboard', function () {
    $user = Auth::user();
    $categories = Category::orderBy('cat_name', 'asc')->get();
    $recentOrders = Order::where('user_id', $user->id)
        ->orderByDesc('order_id')
        ->limit(5)
        ->get();
    $orderCount = Order::where('user_id', $user->id)->count();
    $activeOrders = Order::where('user_id', $user->id)->whereIn('order_status', [1, 2])->count();
    $totalSpent = Order::where('user_id', $user->id)
        ->where('order_status', '!=', 3)
        ->sum('grand_total');
    $pendingDue = Order::where('user_id', $user->id)
        ->where('order_status', '!=', 3)
        ->sum('due');

    return view('dashboard', compact(
        'categories',
        'recentOrders',
        'orderCount',
        'activeOrders',
        'totalSpent',
        'pendingDue',
        'user'
    ));
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Administrative Portal Routes
Route::prefix('admin')->group(function () {
    // Guest Admin Routes
    Route::middleware('admin.guest')->group(function () {
        Route::get('/login', [AdminAuthController::class, 'showLoginForm'])->name('admin.login');
        Route::post('/login', [AdminAuthController::class, 'login']);
    });

    // Authenticated Admin Routes
    Route::middleware(['admin.auth', 'admin.permission'])->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
        Route::post('/expense/store', [AdminDashboardController::class, 'storeExpense'])->name('admin.expense.store');
        Route::get('/logo', function () {
            $path = '/Users/chikku/Downloads/mtllogo.webp';
            if (file_exists($path)) {
                return response()->file($path);
            }
            abort(404);
        })->name('admin.logo');

        Route::post('/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');

        // Master Tables CRUD Routes
        Route::resource('brands', BrandController::class)->names([
            'index' => 'admin.brands.index',
            'store' => 'admin.brands.store',
            'update' => 'admin.brands.update',
            'destroy' => 'admin.brands.destroy',
        ])->except(['create', 'show', 'edit']);

        Route::resource('categories', CategoryController::class)->names([
            'index' => 'admin.categories.index',
            'store' => 'admin.categories.store',
            'update' => 'admin.categories.update',
            'destroy' => 'admin.categories.destroy',
        ])->except(['create', 'show', 'edit']);

        Route::resource('subcategories', SubcategoryController::class)->names([
            'index' => 'admin.subcategories.index',
            'store' => 'admin.subcategories.store',
            'update' => 'admin.subcategories.update',
            'destroy' => 'admin.subcategories.destroy',
        ])->except(['create', 'show', 'edit']);

        Route::resource('products', ProductController::class)->names([
            'index' => 'admin.products.index',
            'create' => 'admin.products.create',
            'store' => 'admin.products.store',
            'edit' => 'admin.products.edit',
            'update' => 'admin.products.update',
            'destroy' => 'admin.products.destroy',
        ])->except(['show']);

        Route::resource('expenses/categories', ExpenseCategoryController::class)->names([
            'index' => 'admin.expenses.categories.index',
            'store' => 'admin.expenses.categories.store',
            'update' => 'admin.expenses.categories.update',
            'destroy' => 'admin.expenses.categories.destroy',
        ])->except(['create', 'show', 'edit']);

        Route::resource('expenses', ExpenseController::class)->names([
            'index' => 'admin.expenses.index',
            'store' => 'admin.expenses.store',
            'update' => 'admin.expenses.update',
            'destroy' => 'admin.expenses.destroy',
        ])->except(['create', 'show', 'edit']);

        Route::resource('stategst', StategstController::class)->names([
            'index' => 'admin.stategst.index',
            'store' => 'admin.stategst.store',
            'update' => 'admin.stategst.update',
            'destroy' => 'admin.stategst.destroy',
        ])->except(['create', 'show', 'edit']);

        Route::resource('agents', AgentController::class)->names([
            'index' => 'admin.agents.index',
            'store' => 'admin.agents.store',
            'update' => 'admin.agents.update',
            'destroy' => 'admin.agents.destroy',
        ])->except(['create', 'show', 'edit']);

        Route::resource('agents-payments', AgentpayController::class)->names([
            'index' => 'admin.agents_payments.index',
            'store' => 'admin.agents_payments.store',
            'update' => 'admin.agents_payments.update',
            'destroy' => 'admin.agents_payments.destroy',
        ])->except(['create', 'show', 'edit']);

        // Sales Routes
        Route::get('sales/{sale}/print', [SalesController::class, 'print'])->name('admin.sales.print');
        Route::post('sales/{sale}/payment', [SalesController::class, 'addPayment'])->name('admin.sales.payment');
        Route::resource('sales', SalesController::class)->names([
            'index' => 'admin.sales.index',
            'create' => 'admin.sales.create',
            'store' => 'admin.sales.store',
            'edit' => 'admin.sales.edit',
            'update' => 'admin.sales.update',
            'destroy' => 'admin.sales.destroy',
        ]);

        // Online Orders Routes
        Route::get('online-orders', [OnlineOrderController::class, 'index'])->name('admin.online_orders.index');
        Route::get('online-orders/{order}/edit', [OnlineOrderController::class, 'edit'])->name('admin.online_orders.edit');
        Route::put('online-orders/{order}', [OnlineOrderController::class, 'update'])->name('admin.online_orders.update');
        Route::post('online-orders/{order}/status', [OnlineOrderController::class, 'updateStatus'])->name('admin.online_orders.status');
        Route::post('online-orders/{order}/payment', [OnlineOrderController::class, 'updatePayment'])->name('admin.online_orders.payment');
        Route::get('online-orders/{order}/print', [OnlineOrderController::class, 'print'])->name('admin.online_orders.print');

        // Purchases Routes
        Route::get('purchases/stock', [PurchaseStockController::class, 'index'])->name('admin.purchases.stock.index');
        Route::get('purchases/stock/{pitem_id}/detail', [PurchaseStockController::class, 'detail'])->name('admin.purchases.stock.detail');
        Route::post('purchases/stock/{pitem_id}/update', [PurchaseStockController::class, 'update'])->name('admin.purchases.stock.update');

        Route::get('purchases/{purchase}/print', [PurchaseController::class, 'print'])->name('admin.purchases.print');
        Route::post('purchases/{purchase}/payment', [PurchaseController::class, 'addPayment'])->name('admin.purchases.payment');
        Route::resource('purchases', PurchaseController::class)->names([
            'index' => 'admin.purchases.index',
            'create' => 'admin.purchases.create',
            'store' => 'admin.purchases.store',
            'edit' => 'admin.purchases.edit',
            'update' => 'admin.purchases.update',
            'destroy' => 'admin.purchases.destroy',
        ]);

        // Quotations Routes
        Route::get('quotations/{quotation}/print', [QuotationController::class, 'print'])->name('admin.quotations.print');
        Route::resource('quotations', QuotationController::class)->names([
            'index' => 'admin.quotations.index',
            'create' => 'admin.quotations.create',
            'store' => 'admin.quotations.store',
            'edit' => 'admin.quotations.edit',
            'update' => 'admin.quotations.update',
            'destroy' => 'admin.quotations.destroy',
        ]);

        // Products Barcode Routes
        Route::get('products/barcode', [BarcodeController::class, 'index'])->name('admin.products.barcode');
        Route::post('products/barcode/print', [BarcodeController::class, 'print'])->name('admin.products.barcode.print');

        // Product Stock List Route
        Route::get('products/stock-list', [ProductController::class, 'stockList'])->name('admin.products.stock_list');

        // Product Price Search Routes
        Route::get('products/price-search', [ProductController::class, 'priceSearch'])->name('admin.products.price_search');
        Route::get('products/price-search/{id}', [ProductController::class, 'getPriceDetails'])->name('admin.products.price_search.details');

        // General Reports Routes
        Route::get('reports', [ReportController::class, 'index'])->name('admin.reports.index');
        Route::get('reports/print', [ReportController::class, 'print'])->name('admin.reports.print');
        Route::post('reports/excel', [ReportController::class, 'exportExcel'])->name('admin.reports.excel');

        // Billwise Report Routes (ref: admin/reportbillmtl.php)
        Route::get('reports/billwise', [ReportController::class, 'billwise'])->name('admin.reports.billwise');
        Route::post('reports/billwise/fetch-bills', [ReportController::class, 'fetchBillwiseNumbers'])->name('admin.reports.billwise.fetch');
        Route::get('reports/billwise/print-sale', [ReportController::class, 'printBillwiseSale'])->name('admin.reports.billwise.print_sale');
        Route::get('reports/billwise/print-purchase', [ReportController::class, 'printBillwisePurchase'])->name('admin.reports.billwise.print_purchase');

        // Sales Report Routes (ref: admin/reportsalesmtl.php)
        Route::get('reports/sales', [ReportController::class, 'sales'])->name('admin.reports.sales');
        Route::post('reports/sales/fetch-customers', [ReportController::class, 'fetchSalesCustomers'])->name('admin.reports.sales.fetch_customers');
        Route::post('reports/sales/generate-type', [ReportController::class, 'generateSalesReportByType'])->name('admin.reports.sales.generate_type');
        Route::post('reports/sales/generate-name', [ReportController::class, 'generateSalesReportByName'])->name('admin.reports.sales.generate_name');

        // Pending Amount Report Routes (ref: admin/reportpendingmtl.php)
        Route::get('reports/pending', [ReportController::class, 'pending'])->name('admin.reports.pending');
        Route::post('reports/pending/generate', [ReportController::class, 'generatePendingReport'])->name('admin.reports.pending.generate');

        // Stock Report Routes (ref: admin/reportstockmtl.php)
        Route::get('reports/stock', [ReportController::class, 'stock'])->name('admin.reports.stock');
        Route::post('reports/stock/generate', [ReportController::class, 'generateStockReport'])->name('admin.reports.stock.generate');

        // Pay History Report Routes (ref: admin/reportpayhistrymtl.php)
        Route::get('reports/payhistory', [ReportController::class, 'payHistory'])->name('admin.reports.payhistory');
        Route::post('reports/payhistory/generate', [ReportController::class, 'generatePayHistoryReport'])->name('admin.reports.payhistory.generate');

        // Excel Export Panel Route (ref: admin/reportoexcel.php)
        Route::get('reports/excel', [ReportController::class, 'excel'])->name('admin.reports.excel_panel');

        // Profit & Loss Report Routes (ref: admin/reportpl.php)
        Route::get('reports/pl', [ReportController::class, 'profit_loss'])->name('admin.reports.pl');
        Route::post('reports/pl/generate', [ReportController::class, 'generateProfitLossReport'])->name('admin.reports.pl.generate');

        // Setting Admin Routes (ref: admin/setting.php)
        Route::get('settings', [SettingController::class, 'index'])->name('admin.settings.index');
        Route::post('settings/username', [SettingController::class, 'updateUsername'])->name('admin.settings.username');
        Route::post('settings/password', [SettingController::class, 'updatePassword'])->name('admin.settings.password');

        // User Management Routes (ref: admin/user.php)
        Route::resource('users', UserController::class)->names([
            'index' => 'admin.users.index',
            'store' => 'admin.users.store',
            'update' => 'admin.users.update',
            'destroy' => 'admin.users.destroy',
        ])->except(['create', 'show', 'edit']);
        Route::post('users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('admin.users.toggle_status');

        // User Permission Settings (ref: admin/usersetting.php)
        Route::get('usersettings', [UserSettingController::class, 'index'])->name('admin.usersettings.index');
        Route::post('usersettings/update', [UserSettingController::class, 'updatePermission'])->name('admin.usersettings.update');

        // Customer/Dealer Management Settings (ref: admin/usercustomer.php)
        Route::resource('customers', UserCustomerController::class)->names([
            'index' => 'admin.customers.index',
            'store' => 'admin.customers.store',
            'update' => 'admin.customers.update',
            'destroy' => 'admin.customers.destroy',
        ])->except(['create', 'show', 'edit']);
        Route::post('customers/{customer}/toggle-status', [UserCustomerController::class, 'toggleStatus'])->name('admin.customers.toggle_status');

        // Database Backup Management (ref: admin/myphpbackup.php)
        Route::get('backups', [BackupController::class, 'index'])->name('admin.backups.index');
        Route::post('backups/create', [BackupController::class, 'create'])->name('admin.backups.create');
        Route::get('backups/{fileName}/download', [BackupController::class, 'download'])->name('admin.backups.download');
        Route::delete('backups/{fileName}', [BackupController::class, 'destroy'])->name('admin.backups.destroy');
        Route::post('backups/{fileName}/restore', [BackupController::class, 'restore'])->name('admin.backups.restore');
        Route::post('backups/upload-restore', [BackupController::class, 'uploadRestore'])->name('admin.backups.upload_restore');

    });
});

require __DIR__.'/auth.php';
