@extends('layouts.admin')

@section('content')
<div x-data="{ activeTab: 'ledger', showExpenseModal: false }" class="space-y-8 animate-fadeIn">
    <!-- Header Summary Hero -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6 p-6 rounded-3xl glassmorphism">
        <div>
            <h1 class="text-3xl font-extrabold tracking-tight text-slate-900 dark:text-slate-100">Welcome Back, {{ Auth::guard('admin')->user()->username }}</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Here is a real-time summary of your MTL Mart operations for today.</p>
        </div>
        
        <div class="flex flex-wrap items-center gap-4">
            <!-- Quick Product Search trigger / status info -->
            <div class="relative" x-data="{ openSearch: false, searchQuery: '', products: @js($products), selectedProduct: null }">
                <button @click="openSearch = !openSearch" class="flex items-center gap-2.5 px-5 py-2.5 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-sm font-semibold text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white hover:border-slate-300 dark:hover:border-slate-700 transition-all">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <span>Quick Price Lookup</span>
                </button>

                <!-- Search Popup Modal -->
                <div x-show="openSearch" 
                     @click.outside="openSearch = false"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100 scale-100"
                     x-transition:leave-end="opacity-0 scale-95"
                     class="absolute right-0 mt-3 w-[360px] sm:w-[480px] p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-2xl z-50 text-left">
                    
                    <h3 class="text-sm font-bold text-slate-700 dark:text-slate-200 uppercase tracking-wider mb-3">Lookup Price Matrix</h3>
                    
                    <!-- Search input -->
                    <input type="text" 
                           x-model="searchQuery"
                           placeholder="Type product name or code..."
                           class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl px-4 py-2.5 text-sm text-slate-700 dark:text-slate-300 placeholder-slate-400 dark:placeholder-slate-600 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 mb-4">

                    <!-- Search Results -->
                    <div class="max-h-60 overflow-y-auto space-y-2 mb-4 scrollbar-thin">
                        <template x-for="prod in products.filter(p => p.productname.toLowerCase().includes(searchQuery.toLowerCase()) || p.pcode.toLowerCase().includes(searchQuery.toLowerCase()))" :key="prod.id">
                            <button @click="selectedProduct = prod; searchQuery = ''" 
                                    class="w-full text-left p-3 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 hover:border-indigo-500 transition-all cursor-pointer">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <span class="block text-sm font-semibold text-slate-800 dark:text-slate-200" x-text="prod.productname"></span>
                                        <span class="block text-xs text-slate-500" x-text="'Code: ' + prod.pcode"></span>
                                    </div>
                                    <span class="bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 text-xs px-2 py-1 rounded font-bold" x-text="'Qty: ' + prod.tqty"></span>
                                </div>
                            </button>
                        </template>
                    </div>

                    <!-- Selected Product Price Grid -->
                    <template x-if="selectedProduct">
                        <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-950/80 border border-slate-200 dark:border-slate-800 space-y-3">
                            <div class="flex justify-between items-center border-b border-slate-200 dark:border-slate-900 pb-2">
                                <span class="text-sm font-bold text-indigo-600 dark:text-indigo-400" x-text="selectedProduct.productname"></span>
                                <button @click="selectedProduct = null" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 cursor-pointer"><i class="fa-solid fa-xmark"></i></button>
                            </div>
                            <div class="grid grid-cols-2 gap-3 text-xs">
                                <div class="bg-white dark:bg-slate-900 p-2.5 rounded-lg border border-slate-200 dark:border-slate-800">
                                    <span class="block text-slate-500 font-semibold mb-0.5">MRP Price</span>
                                    <span class="text-sm font-bold text-slate-800 dark:text-slate-200" x-text="'₹ ' + selectedProduct.mrp"></span>
                                </div>
                                <div class="bg-white dark:bg-slate-900 p-2.5 rounded-lg border border-slate-200 dark:border-slate-800">
                                    <span class="block text-slate-500 font-semibold mb-0.5">Customer Rate</span>
                                    <span class="text-sm font-bold text-slate-800 dark:text-slate-200" x-text="'₹ ' + selectedProduct.srate"></span>
                                </div>
                                <div class="bg-white dark:bg-slate-900 p-2.5 rounded-lg border border-slate-200 dark:border-slate-800">
                                    <span class="block text-slate-500 font-semibold mb-0.5">Dealer Rate</span>
                                    <span class="text-sm font-bold text-slate-800 dark:text-slate-200" x-text="'₹ ' + selectedProduct.dprice"></span>
                                </div>
                                <div class="bg-white dark:bg-slate-900 p-2.5 rounded-lg border border-slate-200 dark:border-slate-800">
                                    <span class="block text-slate-500 font-semibold mb-0.5">Sub-Dealer Rate</span>
                                    <span class="text-sm font-bold text-slate-800 dark:text-slate-200" x-text="'₹ ' + (selectedProduct.sdprice || 0)"></span>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Add Expense Modal Trigger -->
            <button @click="showExpenseModal = true" class="flex items-center gap-2.5 px-5 py-2.5 rounded-xl bg-gradient-to-r from-rose-500 to-red-600 text-white text-sm font-bold shadow-xl shadow-rose-500/10 hover:shadow-rose-500/20 active:scale-[0.98] transition-all cursor-pointer">
                <i class="fa-solid fa-plus-circle"></i>
                <span>Add Daily Expense</span>
            </button>
        </div>
    </div>

    <!-- Stat boxes -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-6">
        <!-- Pending Orders Card -->
        <a href="{{ route('admin.online_orders.index', ['status' => 'pending']) }}" class="p-6 rounded-3xl bg-white/80 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-800/80 shadow-lg shadow-slate-200/50 dark:shadow-none flex items-center justify-between group hover:border-indigo-400/40 dark:hover:border-indigo-500/30 transition-all duration-300 cursor-pointer">
            <div class="space-y-2">
                <span class="block text-xs font-semibold text-slate-500 uppercase tracking-wider">Pending Orders</span>
                <span class="block text-3xl font-extrabold text-slate-800 dark:text-slate-200 group-hover:text-indigo-500 dark:group-hover:text-indigo-400 transition-colors">{{ $countPenOrder }}</span>
                <span class="block text-xs text-indigo-500/80 font-medium">
                    <i class="fa-solid fa-clock mr-1 animate-pulse"></i> Needs fulfillment
                </span>
            </div>
            <div class="p-4 rounded-2xl bg-indigo-500/10 text-indigo-500 dark:text-indigo-400 group-hover:bg-indigo-500/20 transition-all">
                <i class="fa-solid fa-clock-rotate-left text-2xl"></i>
            </div>
        </a>

        <!-- Low Stock Card -->
        <a href="{{ route('admin.products.stock_list') }}" class="p-6 rounded-3xl bg-white/80 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-800/80 shadow-lg shadow-slate-200/50 dark:shadow-none flex items-center justify-between group hover:border-red-400/40 dark:hover:border-red-500/30 transition-all duration-300 cursor-pointer">
            <div class="space-y-2">
                <span class="block text-xs font-semibold text-slate-500 uppercase tracking-wider">Low Stock alerts</span>
                <span class="block text-3xl font-extrabold text-slate-800 dark:text-slate-200 group-hover:text-red-500 dark:group-hover:text-red-400 transition-colors">{{ $countLowStock }}</span>
                <span class="block text-xs text-red-500/80 font-medium">
                    <i class="fa-solid fa-circle-exclamation mr-1 animate-pulse"></i> Products below 10 qty
                </span>
            </div>
            <div class="p-4 rounded-2xl bg-red-500/10 text-red-500 dark:text-red-400 group-hover:bg-red-500/20 transition-all">
                <i class="fa-solid fa-box-open text-2xl animate-pulse"></i>
            </div>
        </a>

        <!-- Total Orders Card -->
        <a href="{{ route('admin.online_orders.index') }}" class="p-6 rounded-3xl bg-white/80 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-800/80 shadow-lg shadow-slate-200/50 dark:shadow-none flex items-center justify-between group hover:border-emerald-400/40 dark:hover:border-emerald-500/30 transition-all duration-300 cursor-pointer">
            <div class="space-y-2">
                <span class="block text-xs font-semibold text-slate-500 uppercase tracking-wider">Today's Orders</span>
                <span class="block text-3xl font-extrabold text-slate-800 dark:text-slate-200 group-hover:text-emerald-500 dark:group-hover:text-emerald-400 transition-colors">
                    {{ $countOOrder + $countOrder }}
                </span>
                <span class="block text-xs text-emerald-500/80 font-medium">
                    <i class="fa-solid fa-circle-check mr-1"></i> Active orders matching today
                </span>
            </div>
            <div class="p-4 rounded-2xl bg-emerald-500/10 text-emerald-500 dark:text-emerald-400 group-hover:bg-emerald-500/20 transition-all">
                <i class="fa-solid fa-cart-arrow-down text-2xl"></i>
            </div>
        </a>

        <!-- Today Net Income Card -->
        <div class="p-6 rounded-3xl bg-white/80 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-800/80 shadow-lg shadow-slate-200/50 dark:shadow-none flex items-center justify-between group hover:border-amber-400/40 dark:hover:border-amber-500/30 transition-all duration-300">
            <div class="space-y-2">
                <span class="block text-xs font-semibold text-slate-500 uppercase tracking-wider">Today's Net Revenue</span>
                <span class="block text-3xl font-extrabold text-slate-800 dark:text-slate-200 group-hover:text-amber-500 dark:group-hover:text-amber-400 transition-colors">
                    ₹ {{ number_format($todayIncome, 2) }}
                </span>
                <span class="block text-xs text-slate-500 dark:text-slate-400 font-medium">
                    Sales minus expenses
                </span>
            </div>
            <div class="p-4 rounded-2xl bg-amber-500/10 text-amber-500 dark:text-amber-400 group-hover:bg-amber-500/20 transition-all">
                <i class="fa-solid fa-indian-rupee-sign text-2xl"></i>
            </div>
        </div>

        <!-- Monthly Net Income Card -->
        <div class="p-6 rounded-3xl bg-white/80 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-800/80 shadow-lg shadow-slate-200/50 dark:shadow-none flex items-center justify-between group hover:border-cyan-400/40 dark:hover:border-cyan-500/30 transition-all duration-300">
            <div class="space-y-2">
                <span class="block text-xs font-semibold text-slate-500 uppercase tracking-wider">Month Net Revenue</span>
                <span class="block text-3xl font-extrabold text-slate-800 dark:text-slate-200 group-hover:text-cyan-500 dark:group-hover:text-cyan-400 transition-colors">
                    ₹ {{ number_format($monthlyIncomeM, 2) }}
                </span>
                <span class="block text-xs text-slate-500 dark:text-slate-400 font-medium">
                    Consolidated billing period
                </span>
            </div>
            <div class="p-4 rounded-2xl bg-cyan-500/10 text-cyan-500 dark:text-cyan-400 group-hover:bg-cyan-500/20 transition-all">
                <i class="fa-solid fa-coins text-2xl"></i>
            </div>
        </div>
    </div>

    <!-- Interactive Navigation Tabs -->
    <div class="border-b border-slate-200 dark:border-slate-800">
        <div class="flex gap-4">
            <button @click="activeTab = 'ledger'" 
                    :class="activeTab === 'ledger' ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200'"
                    class="py-4 px-2 border-b-2 font-semibold text-sm transition-all flex items-center gap-2 cursor-pointer">
                <i class="fa-solid fa-wallet text-sm"></i>
                <span>Daily Financial Ledger</span>
            </button>
            
            <button @click="activeTab = 'bills'" 
                    :class="activeTab === 'bills' ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200'"
                    class="py-4 px-2 border-b-2 font-semibold text-sm transition-all flex items-center gap-2 cursor-pointer">
                <i class="fa-solid fa-receipt text-sm"></i>
                <span>Today's Credit Bills</span>
            </button>
            
            <button @click="activeTab = 'expenses'" 
                    :class="activeTab === 'expenses' ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200'"
                    class="py-4 px-2 border-b-2 font-semibold text-sm transition-all flex items-center gap-2 cursor-pointer">
                <i class="fa-solid fa-hand-holding-dollar text-sm"></i>
                <span>Today's Expenses Tracker</span>
            </button>
        </div>
    </div>

    <!-- Tabs Content view -->
    <div class="space-y-6">
        <!-- 1. Daily Financial Ledger Tab -->
        <div x-show="activeTab === 'ledger'" x-transition:enter="transition ease-out duration-200" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Financial Card Breakdown -->
                <div class="p-6 rounded-3xl bg-white/80 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-800/80 space-y-4">
                    <h3 class="text-lg font-bold text-slate-800 dark:text-slate-200"> Ledger Balance Sheet</h3>
                    
                    <div class="space-y-3">
                        <div class="flex items-center justify-between p-3.5 rounded-2xl bg-emerald-500/5 border border-emerald-500/10">
                            <span class="text-sm text-slate-600 dark:text-slate-400 font-semibold"><i class="fa-solid fa-plus-circle text-emerald-400 mr-2"></i>Total Sales</span>
                            <span class="text-sm font-bold text-slate-800 dark:text-slate-200">₹ {{ number_format($todaySales, 2) }}</span>
                        </div>
                        <div class="flex items-center justify-between p-3.5 rounded-2xl bg-rose-500/5 border border-rose-500/10">
                            <span class="text-sm text-slate-600 dark:text-slate-400 font-semibold"><i class="fa-solid fa-cart-shopping text-rose-400 mr-2"></i>Product Purchases</span>
                            <span class="text-sm font-bold text-slate-800 dark:text-slate-200">₹ {{ number_format($totalPurchaseday, 2) }}</span>
                        </div>
                        <div class="flex items-center justify-between p-3.5 rounded-2xl bg-red-500/5 border border-red-500/10">
                            <span class="text-sm text-slate-600 dark:text-slate-400 font-semibold"><i class="fa-solid fa-hand-holding-dollar text-red-400 mr-2"></i>Office & Staff Expenses</span>
                            <span class="text-sm font-bold text-slate-800 dark:text-slate-200">₹ {{ number_format($totalExp, 2) }}</span>
                        </div>
                        <div class="flex items-center justify-between p-4 rounded-2xl bg-gradient-to-r from-indigo-500/10 to-purple-600/10 border border-indigo-500/20">
                            <span class="text-sm text-slate-700 dark:text-slate-300 font-bold"><i class="fa-solid fa-coins text-indigo-400 mr-2"></i>Net Cash-in-Hand</span>
                            <span class="text-base font-extrabold text-slate-900 dark:text-slate-100">₹ {{ number_format($todayIncome, 2) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Stock Inventory Status Card -->
                <div class="p-6 rounded-3xl bg-white/80 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-800/80 space-y-4">
                    <h3 class="text-lg font-bold text-slate-800 dark:text-slate-200">Daily Stock Valuation</h3>
                    
                    <div class="space-y-3">
                        <div class="flex items-center justify-between p-5 rounded-2xl bg-slate-50 dark:bg-slate-950/60 border border-slate-200 dark:border-slate-800">
                            <div class="space-y-1">
                                <span class="block text-xs font-semibold text-slate-500 uppercase tracking-wider">Opening Stock</span>
                                <span class="block text-xl font-bold text-slate-800 dark:text-slate-200">₹ {{ number_format($openstock, 2) }}</span>
                            </div>
                            <div class="p-3 rounded-xl bg-indigo-500/5 border border-indigo-500/10 text-indigo-500 dark:text-indigo-400">
                                <i class="fa-solid fa-hourglass-start text-xl"></i>
                            </div>
                        </div>

                        <div class="flex items-center justify-between p-5 rounded-2xl bg-slate-50 dark:bg-slate-950/60 border border-slate-200 dark:border-slate-800">
                            <div class="space-y-1">
                                <span class="block text-xs font-semibold text-slate-500 uppercase tracking-wider">Closing Stock</span>
                                <span class="block text-xl font-bold text-slate-800 dark:text-slate-200">₹ {{ number_format($closestock, 2) }}</span>
                            </div>
                            <div class="p-3 rounded-xl bg-purple-500/5 border border-purple-500/10 text-purple-500 dark:text-purple-400">
                                <i class="fa-solid fa-hourglass-end text-xl"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. Today's Credit Bills Tab -->
        <div x-show="activeTab === 'bills'" x-transition:enter="transition ease-out duration-200" class="p-6 rounded-3xl bg-white/80 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-800/80 space-y-4">
            <div class="flex justify-between items-center">
                <h3 class="text-lg font-bold text-slate-800 dark:text-slate-200">Today's Credit and Cash Invoices</h3>
                <span class="text-xs bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 font-bold px-3 py-1.5 rounded-full border border-indigo-500/20">Active List</span>
            </div>

            <div class="responsive-table-container scrollbar-thin rounded-2xl border border-slate-200 dark:border-slate-800">
                <table class="w-full text-left text-sm text-slate-600 dark:text-slate-300 min-w-0 lg:min-w-[750px] block lg:table">
                    <thead class="bg-slate-50 dark:bg-slate-950 text-xs font-semibold uppercase text-slate-500 dark:text-slate-400 border-b border-slate-200 dark:border-slate-800 hidden lg:table-header-group">
                        <tr>
                            <th class="px-6 py-4">Bill/Order No</th>
                            <th class="px-6 py-4">Customer Name</th>
                            <th class="px-6 py-4">Contact / Phone</th>
                            <th class="px-6 py-4">Payment Place</th>
                            <th class="px-6 py-4">Payment Method</th>
                            <th class="px-6 py-4 text-right">Total Amount</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 dark:divide-slate-800 block lg:table-row-group w-full space-y-4 lg:space-y-0 lg:divide-y lg:divide-slate-200/60 dark:lg:divide-slate-800/40 p-4 lg:p-0">
                        <!-- Retail Orders -->
                        @forelse($todayBills as $bill)
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/30 transition-all block lg:table-row w-full bg-white dark:bg-slate-900/30 border border-slate-200 dark:border-slate-800/60 rounded-2xl p-4 lg:p-0 mb-4 lg:mb-0 relative grid grid-cols-2 gap-x-4 gap-y-3 lg:grid-cols-none lg:bg-transparent lg:border-0 lg:hover:bg-slate-50 dark:lg:hover:bg-slate-900/20 lg:transition-all">
                                <!-- Bill/Order No -->
                                <td class="col-span-2 bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 px-3 py-2 rounded-lg text-xs font-mono font-bold flex items-center justify-between lg:table-cell lg:col-span-none lg:bg-transparent lg:px-6 lg:py-4 lg:text-indigo-600 dark:lg:text-indigo-400">
                                    <span class="lg:hidden uppercase tracking-wider text-[10px] font-bold text-indigo-600 dark:text-indigo-400">Bill/Order No</span>
                                    <span>#{{ $bill->morder_id ?: $bill->order_id }}</span>
                                </td>

                                <!-- Customer Name -->
                                <td class="py-2 lg:px-6 lg:py-4 col-span-2 block lg:table-cell lg:col-span-none">
                                    <span class="block lg:hidden text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Customer Name</span>
                                    <span class="font-semibold text-slate-800 dark:text-slate-200">{{ $bill->client_name }}</span>
                                </td>

                                <!-- Contact / Phone -->
                                <td class="py-2 lg:px-6 lg:py-4 col-span-1 block lg:table-cell lg:col-span-none">
                                    <span class="block lg:hidden text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Contact</span>
                                    <span>{{ $bill->client_contact }}</span>
                                </td>

                                <!-- Payment Place -->
                                <td class="py-2 lg:px-6 lg:py-4 col-span-1 block lg:table-cell lg:col-span-none">
                                    <span class="block lg:hidden text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Payment Place</span>
                                    <span>{{ $bill->payment_place }}</span>
                                </td>

                                <!-- Payment Method -->
                                <td class="py-2 lg:px-6 lg:py-4 col-span-1 block lg:table-cell lg:col-span-none">
                                    <span class="block lg:hidden text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Payment Method</span>
                                    <span class="px-2 py-0.5 rounded text-xs bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 inline-block">{{ $bill->paymentname ?: 'Direct' }}</span>
                                </td>

                                <!-- Total Amount -->
                                <td class="py-2 lg:px-6 lg:py-4 col-span-1 block lg:table-cell lg:col-span-none lg:text-right">
                                    <span class="block lg:hidden text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Total Amount</span>
                                    <span class="font-bold text-slate-800 dark:text-slate-200">₹ {{ number_format($bill->grand_total, 2) }}</span>
                                </td>
                            </tr>
                        @empty
                            <!-- Dealer Orders -->
                            @forelse($todayDealerBills as $dbill)
                                <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/30 transition-all block lg:table-row w-full bg-white dark:bg-slate-900/30 border border-slate-200 dark:border-slate-800/60 rounded-2xl p-4 lg:p-0 mb-4 lg:mb-0 relative grid grid-cols-2 gap-x-4 gap-y-3 lg:grid-cols-none lg:bg-transparent lg:border-0 lg:hover:bg-slate-50 dark:lg:hover:bg-slate-900/20 lg:transition-all">
                                    <!-- Bill/Order No -->
                                    <td class="col-span-2 bg-purple-500/10 text-purple-600 dark:text-purple-400 px-3 py-2 rounded-lg text-xs font-mono font-bold flex items-center justify-between lg:table-cell lg:col-span-none lg:bg-transparent lg:px-6 lg:py-4 lg:text-purple-600 dark:lg:text-purple-400">
                                        <span class="lg:hidden uppercase tracking-wider text-[10px] font-bold text-purple-600 dark:text-purple-400">Bill/Order No</span>
                                        <span>#{{ $dbill->morderid ?: $dbill->orderid }}</span>
                                    </td>

                                    <!-- Customer Name -->
                                    <td class="py-2 lg:px-6 lg:py-4 col-span-2 block lg:table-cell lg:col-span-none">
                                        <span class="block lg:hidden text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Customer Name</span>
                                        <span class="font-semibold text-slate-800 dark:text-slate-200">{{ $dbill->username ?: 'Dealer Account' }}</span>
                                    </td>

                                    <!-- GSTIN / Contact -->
                                    <td class="py-2 lg:px-6 lg:py-4 col-span-1 block lg:table-cell lg:col-span-none">
                                        <span class="block lg:hidden text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">GSTIN</span>
                                        <span>{{ $dbill->gsttin ?: '-' }}</span>
                                    </td>

                                    <!-- Portal / Place -->
                                    <td class="py-2 lg:px-6 lg:py-4 col-span-1 block lg:table-cell lg:col-span-none">
                                        <span class="block lg:hidden text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Source</span>
                                        <span>{{ $dbill->utype == 'D' ? 'Dealer Portal' : 'Sub-Dealer Portal' }}</span>
                                    </td>

                                    <!-- Payment Method -->
                                    <td class="py-2 lg:px-6 lg:py-4 col-span-1 block lg:table-cell lg:col-span-none">
                                        <span class="block lg:hidden text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Payment Method</span>
                                        <span class="px-2 py-0.5 rounded text-xs bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 inline-block">{{ $dbill->paymethod ?: 'Credit' }}</span>
                                    </td>

                                    <!-- Total Amount -->
                                    <td class="py-2 lg:px-6 lg:py-4 col-span-1 block lg:table-cell lg:col-span-none lg:text-right">
                                        <span class="block lg:hidden text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Total Amount</span>
                                        <span class="font-bold text-slate-800 dark:text-slate-200">₹ {{ number_format($dbill->gamount, 2) }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr class="block lg:table-row w-full bg-white dark:bg-slate-900/30 border border-slate-200 dark:border-slate-800/60 rounded-2xl p-6 lg:p-0">
                                    <td colspan="6" class="px-6 py-8 text-center text-slate-500 font-medium block lg:table-cell w-full">No sales transactions processed today.</td>
                                </tr>
                            @endforelse
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- 3. Today's Expenses Tracker Tab -->
        <div x-show="activeTab === 'expenses'" x-transition:enter="transition ease-out duration-200" class="p-6 rounded-3xl bg-white/80 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-800/80 space-y-4">
            <div class="flex justify-between items-center">
                <h3 class="text-lg font-bold text-slate-800 dark:text-slate-200">Today's Office & Staff Expenses</h3>
                <span class="text-xs bg-rose-500/10 text-rose-600 dark:text-rose-400 font-bold px-3 py-1.5 rounded-full border border-rose-500/20">Ledger View</span>
            </div>

            <div class="responsive-table-container scrollbar-thin rounded-2xl border border-slate-200 dark:border-slate-800">
                <table class="w-full text-left text-sm text-slate-600 dark:text-slate-300 min-w-0 lg:min-w-[650px] block lg:table">
                    <thead class="bg-slate-50 dark:bg-slate-950 text-xs font-semibold uppercase text-slate-500 dark:text-slate-400 border-b border-slate-200 dark:border-slate-800 hidden lg:table-header-group">
                        <tr>
                            <th class="px-6 py-4">Ref ID</th>
                            <th class="px-6 py-4">Staff Name</th>
                            <th class="px-6 py-4">Expense Category</th>
                            <th class="px-6 py-4">Expense Date</th>
                            <th class="px-6 py-4 text-right">Amount</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 dark:divide-slate-800 block lg:table-row-group w-full space-y-4 lg:space-y-0 lg:divide-y lg:divide-slate-200/60 dark:lg:divide-slate-800/40 p-4 lg:p-0">
                        @forelse($todayExpenses as $exp)
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/30 transition-all block lg:table-row w-full bg-white dark:bg-slate-900/30 border border-slate-200 dark:border-slate-800/60 rounded-2xl p-4 lg:p-0 mb-4 lg:mb-0 relative grid grid-cols-2 gap-x-4 gap-y-3 lg:grid-cols-none lg:bg-transparent lg:border-0 lg:hover:bg-slate-50 dark:lg:hover:bg-slate-900/20 lg:transition-all">
                                <!-- Ref ID -->
                                <td class="col-span-2 bg-rose-500/10 text-rose-600 dark:text-rose-400 px-3 py-2 rounded-lg text-xs font-mono font-bold flex items-center justify-between lg:table-cell lg:col-span-none lg:bg-transparent lg:px-6 lg:py-4 lg:text-slate-500 dark:lg:text-slate-400">
                                    <span class="lg:hidden uppercase tracking-wider text-[10px] font-bold text-rose-600 dark:text-rose-400">Ref ID</span>
                                    <span>#{{ $exp->exp_id }}</span>
                                </td>

                                <!-- Staff Name -->
                                <td class="py-2 lg:px-6 lg:py-4 col-span-2 block lg:table-cell lg:col-span-none">
                                    <span class="block lg:hidden text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Staff Name</span>
                                    <span class="font-semibold text-slate-800 dark:text-slate-200">{{ $exp->username }}</span>
                                </td>

                                <!-- Expense Category -->
                                <td class="py-2 lg:px-6 lg:py-4 col-span-1 block lg:table-cell lg:col-span-none">
                                    <span class="block lg:hidden text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Expense Category</span>
                                    <span>{{ $exp->expense_category_name }}</span>
                                </td>

                                <!-- Expense Date -->
                                <td class="py-2 lg:px-6 lg:py-4 col-span-1 block lg:table-cell lg:col-span-none">
                                    <span class="block lg:hidden text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Expense Date</span>
                                    <span>{{ \Carbon\Carbon::parse($exp->exp_date)->format('d-m-Y') }}</span>
                                </td>

                                <!-- Amount -->
                                <td class="py-2 lg:px-6 lg:py-4 col-span-2 block lg:table-cell lg:col-span-none lg:text-right font-bold text-rose-600 dark:text-rose-400">
                                    <span class="block lg:hidden text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Amount</span>
                                    <span>₹ {{ number_format($exp->exp_amount, 2) }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr class="block lg:table-row w-full bg-white dark:bg-slate-900/30 border border-slate-200 dark:border-slate-800/60 rounded-2xl p-6 lg:p-0">
                                <td colspan="5" class="px-6 py-8 text-center text-slate-500 font-medium block lg:table-cell w-full">No expenses logged today.</td>
                            </tr>
                        @endforelse
                        <!-- Expense Total Summary Card Row -->
                        <tr class="bg-slate-50 dark:bg-slate-950/80 border-t border-slate-200 dark:border-slate-800 block lg:table-row w-full rounded-2xl p-4 lg:p-0 mb-0 relative flex flex-row items-center justify-between lg:bg-slate-50 dark:lg:bg-slate-950 lg:border-t lg:border-slate-200 dark:lg:border-slate-800 lg:divide-y-0">
                            <td colspan="4" class="px-0 py-0 lg:px-6 lg:py-4 text-left lg:text-right font-bold text-slate-700 dark:text-slate-300 block lg:table-cell">
                                <span class="text-sm">Total logged expenses</span>
                            </td>
                            <td class="px-0 py-0 lg:px-6 lg:py-4 text-right font-extrabold text-rose-600 dark:text-rose-400 text-base lg:text-lg block lg:table-cell">
                                <span>₹ {{ number_format($totalExp, 2) }}</span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- 4. Add Expense Overlay Modal -->
    <template x-teleport="body">
    <div x-show="showExpenseModal" 
         x-cloak
         class="admin-modal fixed inset-0 z-50 flex items-center justify-center p-4 overflow-y-auto"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
        
        <!-- Backdrop -->
        <div class="absolute inset-0 bg-slate-900/60 dark:bg-slate-950/80 backdrop-blur-sm" @click="showExpenseModal = false"></div>

        <!-- Modal Card -->
        <div x-show="showExpenseModal"
             x-transition:enter="transition ease-out duration-300 delay-75"
             x-transition:enter-start="opacity-0 scale-95 translate-y-4"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100 translate-y-0"
             x-transition:leave-end="opacity-0 scale-95 translate-y-4"
             class="relative w-full max-w-lg rounded-3xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-8 shadow-2xl space-y-6">
            
            <div class="flex justify-between items-center">
                <h3 class="text-xl font-bold text-slate-800 dark:text-slate-200 flex items-center gap-2">
                    <i class="fa-solid fa-money-bill-wave text-rose-500"></i>
                    <span>Log Daily Expense</span>
                </h3>
                <button @click="showExpenseModal = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition-all cursor-pointer">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <form method="POST" action="{{ route('admin.expense.store') }}" class="space-y-4">
                @csrf

                <!-- Date -->
                <div>
                    <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Expense Date</label>
                    <input type="date" 
                           name="eDate" 
                           required 
                           value="{{ date('Y-m-d') }}"
                           class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl px-4 py-3 text-sm text-slate-700 dark:text-slate-300 focus:outline-none focus:ring-1 focus:ring-rose-500 focus:border-rose-500">
                </div>

                <!-- Category -->
                <div>
                    <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Expense Category</label>
                    <select name="eName" 
                            required
                            class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl px-4 py-3 text-sm text-slate-700 dark:text-slate-300 focus:outline-none focus:ring-1 focus:ring-rose-500 focus:border-rose-500">
                        <option value="">~ Select Category ~</option>
                        @foreach($expenseCategories as $cat)
                            <option value="{{ $cat->exp_id }}">{{ $cat->exp_name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Amount -->
                <div>
                    <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Expense Amount (₹)</label>
                    <input type="number" 
                           step="0.01" 
                           min="0.01" 
                           name="eAmount" 
                           required
                           placeholder="0.00"
                           class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl px-4 py-3 text-sm text-slate-700 dark:text-slate-300 focus:outline-none focus:ring-1 focus:ring-rose-500 focus:border-rose-500">
                </div>

                <!-- Staff ID (Locked) -->
                <div>
                    <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Authenticated Staff</label>
                    <input type="text" 
                           readonly 
                           value="{{ Auth::guard('admin')->user()->username }} (ID: {{ Auth::guard('admin')->id() }})"
                           class="w-full bg-slate-100 dark:bg-slate-950/50 border border-slate-200 dark:border-slate-800 rounded-xl px-4 py-3 text-sm text-slate-500 cursor-not-allowed">
                </div>

                <!-- Actions -->
                <div class="flex gap-4 pt-4 border-t border-slate-200 dark:border-slate-800">
                    <button type="button" @click="showExpenseModal = false" class="flex-1 py-3 px-4 rounded-xl border border-slate-200 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-800/50 text-slate-600 dark:text-slate-300 font-semibold text-sm transition-all cursor-pointer">
                        Cancel
                    </button>
                    <button type="submit" class="flex-1 py-3 px-4 rounded-xl bg-gradient-to-r from-rose-500 to-red-600 text-white font-bold text-sm shadow-xl shadow-rose-500/10 hover:shadow-rose-500/25 transition-all cursor-pointer">
                        Save Expense
                    </button>
                </div>
            </form>
        </div>
    </div>
    </template>
</div>
@endsection
