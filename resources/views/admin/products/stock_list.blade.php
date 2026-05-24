@extends('layouts.admin')

@section('content')
<div class="space-y-8 animate-fadeIn">
    <x-admin.header 
        title="Godown Stock List" 
        description="Real-time godown stock summary, status categorizations, and detailed price tiers." 
        icon="fa-solid fa-list-check" 
        glass="true"
        class="no-print"
    >

    </x-admin.header>

    <!-- Print Title (Only visible during printing) -->
    <div class="hidden print-only-block mb-6 text-center">
        <h1 class="text-2xl font-bold text-black uppercase tracking-wider">GODOWN STOCK LIST</h1>
        <p class="text-xs text-slate-600 mt-1">Generated on {{ now('Asia/Kolkata')->format('d-m-Y h:i A') }}</p>
    </div>

    <!-- Stock Metrics Dashboard Widgets -->
    <div class="grid grid-cols-2 lg:grid-cols-5 gap-4 no-print">
        <!-- 1. Total Active -->
        <a href="{{ route('admin.products.stock_list') }}" class="p-5 rounded-2xl bg-slate-900/40 border border-slate-800 hover:border-slate-700/80 transition-all flex flex-col justify-between h-28 group">
            <span class="text-xs font-semibold text-slate-400 group-hover:text-slate-300 uppercase tracking-wider">Active Items</span>
            <div class="flex items-baseline justify-between mt-2">
                <span class="text-3xl font-extrabold text-slate-100">{{ $allActiveCount }}</span>
                <span class="p-2 rounded-xl bg-indigo-500/10 text-indigo-400 text-xs"><i class="fa-solid fa-box"></i></span>
            </div>
        </a>

        <!-- 2. High Stock -->
        <a href="{{ route('admin.products.stock_list', ['stock_status' => 'high']) }}" class="p-5 rounded-2xl bg-slate-900/40 border border-slate-800 hover:border-emerald-500/20 transition-all flex flex-col justify-between h-28 group">
            <span class="text-xs font-semibold text-slate-400 group-hover:text-emerald-400 uppercase tracking-wider">High Stock (>15)</span>
            <div class="flex items-baseline justify-between mt-2">
                <span class="text-3xl font-extrabold text-emerald-400">{{ $highStockCount }}</span>
                <span class="p-2 rounded-xl bg-emerald-500/10 text-emerald-400 text-xs"><i class="fa-solid fa-circle-check"></i></span>
            </div>
        </a>

        <!-- 3. Medium Stock -->
        <a href="{{ route('admin.products.stock_list', ['stock_status' => 'medium']) }}" class="p-5 rounded-2xl bg-slate-900/40 border border-slate-800 hover:border-amber-500/20 transition-all flex flex-col justify-between h-28 group">
            <span class="text-xs font-semibold text-slate-400 group-hover:text-amber-400 uppercase tracking-wider">Medium (6-15)</span>
            <div class="flex items-baseline justify-between mt-2">
                <span class="text-3xl font-extrabold text-amber-400">{{ $mediumStockCount }}</span>
                <span class="p-2 rounded-xl bg-amber-500/10 text-amber-400 text-xs"><i class="fa-solid fa-circle-exclamation"></i></span>
            </div>
        </a>

        <!-- 4. Low Stock -->
        <a href="{{ route('admin.products.stock_list', ['stock_status' => 'low']) }}" class="p-5 rounded-2xl bg-slate-900/40 border border-slate-800 hover:border-rose-500/20 transition-all flex flex-col justify-between h-28 group relative">
            <span class="text-xs font-semibold text-slate-400 group-hover:text-rose-400 uppercase tracking-wider">Low Stock (1-5)</span>
            <div class="flex items-baseline justify-between mt-2">
                <span class="text-3xl font-extrabold text-rose-400">{{ $lowStockCount }}</span>
                <span class="p-2 rounded-xl bg-rose-500/10 text-rose-400 text-xs @if($lowStockCount > 0) animate-pulse @endif"><i class="fa-solid fa-triangle-exclamation"></i></span>
            </div>
        </a>

        <!-- 5. Out Of Stock -->
        <a href="{{ route('admin.products.stock_list', ['stock_status' => 'out']) }}" class="p-5 rounded-2xl bg-slate-900/40 border border-slate-800 hover:border-red-500/40 transition-all flex flex-col justify-between h-28 group @if($outOfStockCount > 0) shadow-lg shadow-red-950/20 ring-1 ring-red-500/20 @endif">
            <span class="text-xs font-semibold text-slate-400 group-hover:text-red-400 uppercase tracking-wider">Out of Stock</span>
            <div class="flex items-baseline justify-between mt-2">
                <span class="text-3xl font-extrabold text-red-500">{{ $outOfStockCount }}</span>
                <span class="p-2 rounded-xl bg-red-500/10 text-red-500 text-xs @if($outOfStockCount > 0) animate-bounce @endif"><i class="fa-solid fa-skull"></i></span>
            </div>
        </a>
    </div>

    <!-- Filters Section -->
    <div class="p-6 rounded-3xl bg-slate-900/50 border border-slate-800/80 shadow-xl space-y-4 no-print">
        <form method="GET" action="{{ route('admin.products.stock_list') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
            <!-- Search Query -->
            <div class="space-y-1.5 col-span-1 sm:col-span-2 lg:col-span-1">
                <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider">Search</label>
                <div class="relative">
                    <input type="text" name="search" value="{{ $search }}" placeholder="P-Code, Name, Desc..." class="w-full bg-slate-950 border border-slate-800 rounded-xl pl-10 pr-4 py-2.5 text-xs text-slate-300 focus:outline-none focus:ring-1 focus:ring-indigo-500 placeholder-slate-600">
                    <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-500 text-[10px]"></i>
                </div>
            </div>

            <!-- Category -->
            <div class="space-y-1.5">
                <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider">Category</label>
                <select name="catid" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2.5 text-xs text-slate-300 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                    <option value="">All Categories</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->cat_id }}" {{ $catId == $cat->cat_id ? 'selected' : '' }}>{{ $cat->cat_name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Brand -->
            <div class="space-y-1.5">
                <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider">Brand</label>
                <select name="brandid" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2.5 text-xs text-slate-300 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                    <option value="">All Brands</option>
                    @foreach($brands as $brand)
                        <option value="{{ $brand->brand_id }}" {{ $brandId == $brand->brand_id ? 'selected' : '' }}>{{ $brand->brand_name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Stock Status -->
            <div class="space-y-1.5">
                <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider">Stock Level</label>
                <select name="stock_status" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2.5 text-xs text-slate-300 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                    <option value="">All Levels</option>
                    <option value="high" {{ $stockStatus == 'high' ? 'selected' : '' }}>High (>15)</option>
                    <option value="medium" {{ $stockStatus == 'medium' ? 'selected' : '' }}>Medium (6-15)</option>
                    <option value="low" {{ $stockStatus == 'low' ? 'selected' : '' }}>Low (1-5)</option>
                    <option value="out" {{ $stockStatus == 'out' ? 'selected' : '' }}>Out of Stock (<=0)</option>
                </select>
            </div>

            <!-- Buttons -->
            <div class="flex items-end gap-2">
                <x-admin.button type="submit" variant="primary" icon="fa-solid fa-filter" class="flex-1 rounded-xl py-2.5 text-xs">
                    Apply
                </x-admin.button>
                @if($search || $catId || $brandId || $stockStatus)
                    <x-admin.button href="{{ route('admin.products.stock_list') }}" variant="secondary" icon="fa-solid fa-arrow-rotate-left" class="rounded-xl py-2.5 px-3.5 text-xs" title="Reset Filters"></x-admin.button>
                @endif
            </div>
        </form>
    </div>

    <!-- Stock Table Reusable Component -->
    @php
    $tableHeaders = [
        ['label' => 'No', 'align' => 'center'],
        ['label' => 'P_Code'],
        ['label' => 'Product Name'],
        ['label' => 'Brand / Category'],
        ['label' => 'TQty', 'align' => 'right'],
        ['label' => 'Status', 'align' => 'center'],
        ['label' => 'MRP', 'align' => 'right'],
        ['label' => 'P_Rate', 'align' => 'right'],
        ['label' => 'GST', 'align' => 'center'],
        ['label' => 'Cust P.', 'align' => 'right'],
        ['label' => 'Dealer P.', 'align' => 'right'],
        ['label' => 'S_Dealer P.', 'align' => 'right']
    ];
    @endphp

    <div class="printable-area">
        <x-admin.table :headers="$tableHeaders" :collection="$products" type="card" minWidth="1200px">
            @forelse($products as $index => $prod)
                @php
                    // Determine Stock Status Badge
                    if ($prod->tqty > 15) {
                        $statusLabel = 'High';
                        $badgeClass = 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20';
                    } elseif ($prod->tqty > 5) {
                        $statusLabel = 'Medium';
                        $badgeClass = 'bg-amber-500/10 text-amber-400 border-amber-500/20';
                    } elseif ($prod->tqty >= 1) {
                        $statusLabel = 'Low';
                        $badgeClass = 'bg-rose-500/10 text-rose-400 border-rose-500/25';
                    } else {
                        $statusLabel = 'No Stock';
                        $badgeClass = 'bg-red-600/20 text-red-500 border-red-500/30 font-extrabold animate-pulse';
                    }

                    // Unit determination
                    $unitStr = 'no';
                    if ($prod->unit == 1) {
                        $unitStr = 'no';
                    } elseif ($prod->unit == 2) {
                        $unitStr = 'm';
                    } elseif ($prod->unit == 3) {
                        $unitStr = 'pk';
                    } elseif ($prod->unit == 4) {
                        $unitStr = 'lt';
                    }
                @endphp
                <tr class="hover:bg-slate-900/30 transition-all block lg:table-row w-full bg-slate-900/20 border border-slate-800/50 rounded-2xl p-4 lg:p-0 mb-4 lg:mb-0 relative grid grid-cols-2 gap-x-4 gap-y-2 lg:grid-cols-none lg:bg-transparent lg:border-0 lg:hover:bg-slate-900/10">
                    <!-- No -->
                    <td class="col-span-2 bg-indigo-500/10 text-indigo-400 px-3 py-1.5 rounded-lg text-xs font-mono font-bold flex items-center justify-between lg:table-cell lg:col-span-none lg:bg-transparent lg:px-3 lg:py-3.5 lg:text-slate-400 lg:text-center">
                        <span class="lg:hidden uppercase tracking-wider text-[9px] font-bold text-indigo-400">Index</span>
                        <span>#{{ $index + 1 }}</span>
                    </td>

                    <!-- P_Code -->
                    <td class="py-1 lg:px-3 lg:py-3.5 col-span-1 block lg:table-cell lg:col-span-none">
                        <span class="block lg:hidden text-[9px] font-bold text-slate-500 uppercase tracking-wider mb-0.5">Product Code</span>
                        <span class="font-mono font-semibold text-slate-200 bg-slate-950 px-2 py-1 rounded-lg text-xs border border-slate-805/50">{{ $prod->pcode }}</span>
                    </td>

                    <!-- Product Name -->
                    <td class="py-1 lg:px-3 lg:py-3.5 col-span-2 block lg:table-cell lg:col-span-none">
                        <span class="block lg:hidden text-[9px] font-bold text-slate-500 uppercase tracking-wider mb-0.5">Product Name</span>
                        <div class="font-bold text-slate-100 max-w-sm truncate">{{ $prod->productname }}</div>
                        <div class="text-[11px] text-slate-450 max-w-sm truncate">{{ $prod->productdes }}</div>
                    </td>

                    <!-- Brand / Category -->
                    <td class="py-1 lg:px-3 lg:py-3.5 col-span-1 block lg:table-cell lg:col-span-none">
                        <span class="block lg:hidden text-[9px] font-bold text-slate-500 uppercase tracking-wider mb-0.5">Brand / Category</span>
                        <div class="font-semibold text-slate-300">{{ $prod->brand ? $prod->brand->brand_name : 'N/A' }}</div>
                        <div class="text-xs text-slate-500">{{ $prod->category ? $prod->category->cat_name : 'N/A' }}</div>
                    </td>

                    <!-- TQty -->
                    <td class="py-1 lg:px-3 lg:py-3.5 col-span-1 block lg:table-cell lg:col-span-none lg:text-right">
                        <span class="block lg:hidden text-[9px] font-bold text-slate-500 uppercase tracking-wider mb-0.5">Total Qty</span>
                        <span class="font-mono font-bold text-slate-100 text-sm whitespace-nowrap">
                            {{ $prod->tqty }} <span class="text-xs text-slate-500 font-medium">{{ $unitStr }}</span>
                        </span>
                    </td>

                    <!-- Status -->
                    <td class="py-1 lg:px-3 lg:py-3.5 col-span-1 block lg:table-cell lg:col-span-none lg:text-center">
                        <span class="block lg:hidden text-[9px] font-bold text-slate-500 uppercase tracking-wider mb-0.5">Stock Status</span>
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase border {{ $badgeClass }} whitespace-nowrap inline-block">
                            {{ $statusLabel }}
                        </span>
                    </td>

                    <!-- MRP -->
                    <td class="py-1 lg:px-3 lg:py-3.5 col-span-1 block lg:table-cell lg:col-span-none lg:text-right">
                        <span class="block lg:hidden text-[9px] font-bold text-slate-500 uppercase tracking-wider mb-0.5">MRP</span>
                        <span class="font-bold text-slate-300">₹{{ number_format($prod->mrp, 2) }}</span>
                    </td>

                    <!-- Purchase Rate -->
                    <td class="py-1 lg:px-3 lg:py-3.5 col-span-1 block lg:table-cell lg:col-span-none lg:text-right">
                        <span class="block lg:hidden text-[9px] font-bold text-slate-500 uppercase tracking-wider mb-0.5">Purchase Rate</span>
                        <span class="font-semibold text-slate-400">₹{{ number_format($prod->prate, 2) }}</span>
                    </td>

                    <!-- GST -->
                    <td class="py-1 lg:px-3 lg:py-3.5 col-span-1 block lg:table-cell lg:col-span-none lg:text-center">
                        <span class="block lg:hidden text-[9px] font-bold text-slate-500 uppercase tracking-wider mb-0.5">GST %</span>
                        <span class="font-mono text-slate-300 text-xs">{{ $prod->gst }}%</span>
                    </td>

                    <!-- Cust P -->
                    <td class="py-1 lg:px-3 lg:py-3.5 col-span-1 block lg:table-cell lg:col-span-none lg:text-right">
                        <span class="block lg:hidden text-[9px] font-bold text-slate-500 uppercase tracking-wider mb-0.5">Customer Price</span>
                        <span class="font-bold text-purple-400">₹{{ number_format($prod->cprice, 2) }}</span>
                    </td>

                    <!-- Dealer P -->
                    <td class="py-1 lg:px-3 lg:py-3.5 col-span-1 block lg:table-cell lg:col-span-none lg:text-right">
                        <span class="block lg:hidden text-[9px] font-bold text-slate-500 uppercase tracking-wider mb-0.5">Dealer Price</span>
                        <span class="font-bold text-emerald-400">₹{{ number_format($prod->dprice, 2) }}</span>
                    </td>

                    <!-- S_Dealer P -->
                    <td class="py-1 lg:px-3 lg:py-3.5 col-span-1 block lg:table-cell lg:col-span-none lg:text-right">
                        <span class="block lg:hidden text-[9px] font-bold text-slate-500 uppercase tracking-wider mb-0.5">Super Dealer Price</span>
                        <span class="font-bold text-indigo-400">₹{{ number_format($prod->sdprice, 2) }}</span>
                    </td>
                </tr>
            @empty
                <tr class="block lg:table-row w-full bg-slate-900/30 border border-slate-800/60 rounded-2xl p-6 lg:p-0">
                    <td colspan="12" class="px-6 py-12 text-center text-slate-500 font-medium block lg:table-cell w-full">
                        <div class="flex flex-col items-center justify-center gap-3">
                            <i class="fa-solid fa-box-open text-4xl text-slate-600 animate-bounce"></i>
                            <span>No active products found matching the filter criteria.</span>
                        </div>
                    </td>
                </tr>
            @endforelse
        </x-admin.table>
    </div>
</div>

<!-- Print Stylesheets -->
<style>
    @media print {
        body {
            background-color: white !important;
            color: black !important;
            font-size: 10px !important;
        }
        .no-print {
            display: none !important;
        }
        .print-only-block {
            display: block !important;
        }
        .printable-area {
            background-color: transparent !important;
            border: 0 !important;
            padding: 0 !important;
            box-shadow: none !important;
        }
        table {
            color: black !important;
            border-collapse: collapse !important;
            width: 100% !important;
        }
        thead {
            display: table-header-group !important;
            background-color: #f1f5f9 !important;
        }
        thead th {
            color: black !important;
            border: 1px solid #cbd5e1 !important;
            padding: 6px !important;
            font-size: 10px !important;
        }
        tbody tr {
            display: table-row !important;
            background: transparent !important;
            border: 0 !important;
        }
        tbody td {
            display: table-cell !important;
            color: black !important;
            border: 1px solid #cbd5e1 !important;
            padding: 6px !important;
            font-size: 9px !important;
        }
        .font-mono, .font-bold {
            color: black !important;
        }
        span, div {
            color: black !important;
            background: transparent !important;
            border: 0 !important;
        }
    }
</style>
@endsection
