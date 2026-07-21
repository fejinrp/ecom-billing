@extends('layouts.admin')

@section('content')
<div class="space-y-8 animate-fadeIn">

    <!-- Header Section -->
    <x-admin.header 
        title="Inventory Stock Ledger" 
        description="Filter, view, and print real-time stock balances, track low-level items, and analyze product categories." 
        icon="fa-solid fa-briefcase" 
        glass="true"
    />

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Filter Form Column (1/3 width on large screens) -->
        <div class="space-y-6">
            <div class="p-8 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xl space-y-6">
                
                <h3 class="text-sm font-bold text-slate-700 dark:text-slate-200 uppercase tracking-wider border-l-4 border-indigo-500 pl-3 flex items-center gap-2">
                    <i class="fa-solid fa-filter text-indigo-400"></i>
                    Select Filter Criteria
                </h3>
                
                <form action="{{ route('admin.reports.stock.generate') }}" method="POST" target="_blank" id="stockReportForm" class="space-y-5">
                    @csrf
                    
                    <!-- Stock Level -->
                    <div class="space-y-1.5">
                        <label class="block text-xs font-semibold text-slate-550 dark:text-slate-400 uppercase tracking-wider">Stock Levels</label>
                        <select name="report" id="reportSelect" onchange="resetOthers('report')"
                            class="w-full bg-slate-100 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl px-4 py-3 text-sm text-slate-800 dark:text-slate-300 focus:outline-none focus:ring-1 focus:ring-indigo-500 transition">
                            <option value="0" class="bg-white dark:bg-slate-950 text-slate-800 dark:text-slate-300">Select Stock Level</option>
                            <option value="A" class="bg-white dark:bg-slate-950 text-slate-800 dark:text-slate-300">All Items</option>
                            <option value="L" class="bg-white dark:bg-slate-950 text-slate-800 dark:text-slate-300">Low Stock (Qty &lt;= 5)</option>
                            <option value="M" class="bg-white dark:bg-slate-950 text-slate-800 dark:text-slate-300">Medium Stock (Qty 5 to 15)</option>
                            <option value="H" class="bg-white dark:bg-slate-950 text-slate-800 dark:text-slate-300">High Stock (Qty &gt; 15)</option>
                        </select>
                    </div>

                    <!-- Brands -->
                    <div class="space-y-1.5">
                        <label class="block text-xs font-semibold text-slate-550 dark:text-slate-400 uppercase tracking-wider">Brand Name</label>
                        <select name="bname" id="brandSelect" onchange="resetOthers('brand')"
                            class="w-full bg-slate-100 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl px-4 py-3 text-sm text-slate-800 dark:text-slate-300 focus:outline-none focus:ring-1 focus:ring-indigo-500 transition">
                            <option value="0" class="bg-white dark:bg-slate-950 text-slate-800 dark:text-slate-300">Select Brand Name</option>
                            @foreach ($brands as $brand)
                                <option value="{{ $brand->brand_id }}" class="bg-white dark:bg-slate-950 text-slate-800 dark:text-slate-300">{{ $brand->brand_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Category & Subcategory Multi-Level Tree Select -->
                    <div class="space-y-1.5" @category-tree-changed="resetOthers('tree')">
                        <x-admin.category-tree-select 
                            catName="cname" 
                            subcatName="subcatid" 
                            label="Category & Subcategory Hierarchy (Optional)" 
                            :required="false" 
                        />
                    </div>

                    <!-- Quantity Bounds -->
                    <div class="space-y-1.5">
                        <label class="block text-xs font-semibold text-slate-550 dark:text-slate-400 uppercase tracking-wider">Quantity Limit (From 0 to)</label>
                        <input type="number" name="tno" id="qtyInput" placeholder="Enter max quantity threshold" min="0" oninput="resetOthers('qty')"
                            class="w-full bg-slate-100 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl px-4 py-3 text-sm text-slate-800 dark:text-slate-300 focus:outline-none focus:ring-1 focus:ring-indigo-500 transition">
                    </div>

                    <input type="hidden" name="auto_print" id="autoPrintInput" value="1">

                    <div class="pt-2 flex flex-col sm:flex-row gap-3">
                        <button type="submit" onclick="document.getElementById('autoPrintInput').value='0';" class="flex-1 py-3 px-4 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 font-bold text-xs uppercase tracking-wider transition-all flex items-center justify-center gap-2 border border-slate-200 dark:border-slate-700">
                            <i class="fa-solid fa-eye text-indigo-500 text-sm"></i>
                            <span>Preview Report</span>
                        </button>

                        <button type="submit" onclick="document.getElementById('autoPrintInput').value='1';" class="flex-1 py-3 px-4 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs uppercase tracking-wider transition-all flex items-center justify-center gap-2 shadow-lg shadow-indigo-600/20">
                            <i class="fa-solid fa-print text-sm"></i>
                            <span>Print Report</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Inventory Preview Table (2/3 width on large screens) -->
        <div class="lg:col-span-2 space-y-6">
            <div class="p-8 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xl space-y-6 flex flex-col h-full justify-between">
                
                <div>
                    <div class="flex items-center justify-between mb-4 border-b border-slate-200 dark:border-slate-850 pb-4">
                        <h3 class="text-sm font-bold text-slate-700 dark:text-slate-200 uppercase tracking-wider border-l-4 border-violet-500 pl-3 flex items-center gap-2">
                            <i class="fa-solid fa-list-check text-violet-400"></i>
                            Active Inventory Preview
                        </h3>
                        <span class="text-xs bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 px-3 py-1 rounded-full font-semibold">
                            Total Records: {{ $products->total() }}
                        </span>
                    </div>

                    <div class="responsive-table-container scrollbar-thin">
                        <table class="w-full text-left text-xs border-collapse">
                            <thead>
                                <tr class="bg-slate-100 dark:bg-slate-950 text-slate-700 dark:text-slate-400 font-bold uppercase tracking-wider border-b border-slate-200 dark:border-slate-850">
                                    <th class="py-3.5 px-4 w-12 text-center">No</th>
                                    <th class="py-3.5 px-4">Product Details</th>
                                    <th class="py-3.5 px-4">Brand / Category</th>
                                    <th class="py-3.5 px-4 w-20 text-center">Qty</th>
                                    <th class="py-3.5 px-4 w-28 text-right">MRP / GST</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200 dark:divide-slate-850/60">
                                @forelse ($products as $idx => $prod)
                                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-950/20 text-slate-700 dark:text-slate-300 align-middle">
                                        <!-- Index number -->
                                        <td class="py-3 px-4 text-center font-mono text-slate-500 font-semibold">
                                            {{ $products->firstItem() + $idx }}
                                        </td>
                                        <!-- Product Code & Name -->
                                        <td class="py-3 px-4 space-y-0.5">
                                            <span class="font-mono text-[10px] font-bold text-indigo-500 dark:text-indigo-400 uppercase bg-indigo-500/5 px-2 py-0.5 rounded border border-indigo-500/10">{{ $prod->pcode }}</span>
                                            <span class="block font-bold text-slate-850 dark:text-slate-100 uppercase mt-1">{{ $prod->productname }}</span>
                                        </td>
                                        <!-- Brand & Category -->
                                        <td class="py-3 px-4 space-y-0.5 text-xs text-slate-600 dark:text-slate-400 font-medium">
                                            <span class="block text-slate-800 dark:text-slate-300 font-semibold uppercase">{{ $prod->brand->brand_name ?? 'N/A' }}</span>
                                            <span class="block opacity-75 uppercase">{{ $prod->category->cat_name ?? 'N/A' }}</span>
                                        </td>
                                        <!-- Qty and Status badge -->
                                        <td class="py-3 px-4 text-center space-y-1">
                                            <span class="block font-mono font-bold text-sm {{ $prod->tqty <= 5 ? 'text-rose-500 dark:text-rose-400 font-extrabold' : 'text-slate-850 dark:text-slate-100' }}">{{ $prod->tqty }}</span>
                                            @if ($prod->tqty <= 5)
                                                <span class="inline-block text-[9px] font-bold tracking-widest uppercase bg-rose-500/10 text-rose-550 dark:text-rose-450 border border-rose-500/20 px-1.5 py-0.5 rounded">Low</span>
                                            @else
                                                <span class="inline-block text-[9px] font-bold tracking-widest uppercase bg-emerald-500/10 text-emerald-550 dark:text-emerald-450 border border-emerald-500/20 px-1.5 py-0.5 rounded">Normal</span>
                                            @endif
                                        </td>
                                        <!-- Rates -->
                                        <td class="py-3 px-4 text-right space-y-0.5 font-mono text-xs text-slate-800 dark:text-slate-200">
                                            <span class="block font-bold">Rs. {{ number_format($prod->prate, 2) }}</span>
                                            <span class="block text-[10px] text-slate-500">GST: {{ $prod->gst ?? 0 }}%</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="py-8 text-center text-slate-500 font-medium">No inventory products available.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Custom styled paginator -->
                <div class="mt-6 border-t border-slate-850 pt-4">
                    {{ $products->links() }}
                </div>

            </div>
        </div>

    </div>

</div>

<script>
    // Vanishing fields logic mimicking legacy JS to prevent multiple-filters overlapping
    function resetOthers(source) {
        const report = document.getElementById('reportSelect');
        const brand = document.getElementById('brandSelect');
        const qty = document.getElementById('qtyInput');

        if (source === 'report' && report.value !== '0') {
            if (brand) brand.value = '0';
            if (qty) qty.value = '';
        } else if (source === 'brand' && brand.value !== '0') {
            if (report) report.value = '0';
            if (qty) qty.value = '';
        } else if (source === 'tree') {
            if (report) report.value = '0';
            if (brand) brand.value = '0';
            if (qty) qty.value = '';
        } else if (source === 'qty' && qty.value.trim() !== '') {
            if (report) report.value = '0';
            if (brand) brand.value = '0';
        }
    }
</script>
@endsection
