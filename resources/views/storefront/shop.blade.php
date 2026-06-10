@extends('layouts.storefront')

@section('content')
    @php
        $categoryItems = collect($categories ?? [])
            ->unique(fn ($cat) => $cat->cat_name ?? $cat->cat_id ?? spl_object_id($cat))
            ->values();
    @endphp

    <!-- Breadcrumb Trace -->
    <div class="mb-4">
        <div class="flex items-center gap-2 text-[11px] font-medium text-slate-500 mb-1.5">
            <a href="{{ route('storefront.index') }}" class="hover:text-[#0059e3] transition-colors">Home</a>
            <i class="fa-solid fa-chevron-right text-[8px] text-slate-400"></i>
            <span class="text-slate-800 font-bold">Shop All Products</span>
        </div>
    </div>

    <!-- Main Shop Container (Snapdeal Left Filter + Right Grid) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start mb-16">
        
        <!-- ============================================================
             LEFT COLUMN: Filters Sidebar (3 cols on lg)
             ============================================================ -->
        <div class="lg:col-span-3 space-y-4 hidden lg:block">
            <!-- Categories Filter -->
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-4 shadow-sm space-y-3">
                <div class="border-b border-slate-100 dark:border-slate-800 pb-2 flex items-center justify-between">
                    <span class="text-xs font-black uppercase tracking-wider text-slate-800 dark:text-white">Categories</span>
                </div>
                <ul class="space-y-1 max-h-56 overflow-y-auto custom-scrollbar">
                    @if ($categoryItems->count() > 0)
                        @foreach ($categoryItems as $cat)
                            <li>
                                <a href="{{ route('storefront.category', $cat->cat_name) }}" 
                                   class="flex items-center justify-between px-2.5 py-1.5 rounded-lg text-xs font-medium text-slate-655 dark:text-slate-400 hover:text-[#0059e3] transition-colors">
                                    <span class="truncate">{{ $cat->cat_name }}</span>
                                    <i class="fa-solid fa-chevron-right text-[7px] text-slate-400"></i>
                                </a>
                            </li>
                        @endforeach
                    @endif
                </ul>
            </div>

            <!-- Price Filters (Visual Only) -->
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-4 shadow-sm space-y-3">
                <span class="block text-xs font-black uppercase tracking-wider text-slate-800 dark:text-white">Filter by Price</span>
                <div class="space-y-2 text-xs text-slate-600 dark:text-slate-400">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" class="rounded border-slate-300 text-[#0059e3] focus:ring-[#0059e3]">
                        <span>Under Rs. 1,000</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" class="rounded border-slate-300 text-[#0059e3] focus:ring-[#0059e3]">
                        <span>Rs. 1,000 - Rs. 5,000</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" class="rounded border-slate-300 text-[#0059e3] focus:ring-[#0059e3]">
                        <span>Over Rs. 5,000</span>
                    </label>
                </div>
            </div>

            <!-- GST Rates Filter -->
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-4 shadow-sm space-y-3">
                <span class="block text-xs font-black uppercase tracking-wider text-slate-800 dark:text-white">GST Rates</span>
                <div class="space-y-2 text-xs text-slate-600 dark:text-slate-400">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" class="rounded border-slate-300 text-[#0059e3] focus:ring-[#0059e3]">
                        <span>18% GST Bracket</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" class="rounded border-slate-300 text-[#0059e3] focus:ring-[#0059e3]">
                        <span>28% GST Bracket</span>
                    </label>
                </div>
            </div>
        </div>

        <!-- ============================================================
             RIGHT COLUMN: Sort controls and Product Grid (9 cols)
             ============================================================ -->
        <div class="lg:col-span-9 space-y-4">
            
            <!-- Controls Bar -->
            <div class="flex items-center justify-between bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl px-4 py-3 shadow-sm flex-wrap gap-3">
                <div>
                    <h1 class="text-base font-black text-slate-900 dark:text-white uppercase tracking-wide inline-block mr-2">Shop Catalog</h1>
                    <span class="text-xs text-slate-500">({{ $products->total() }} items found)</span>
                </div>
                
                <!-- Sorting dropdown -->
                <div class="flex items-center gap-2 text-xs">
                    <span class="text-slate-500">Sort by:</span>
                    <select class="border border-slate-200 dark:border-slate-850 rounded-lg px-2.5 py-1.5 bg-slate-50 dark:bg-slate-955 text-slate-800 dark:text-slate-200 font-bold outline-none cursor-pointer">
                        <option>Relevance</option>
                        <option>Price: Low to High</option>
                        <option>Price: High to Low</option>
                        <option>Newest Arrivals</option>
                    </select>
                </div>
            </div>

            <!-- High-Density Product Grid (4 Columns) -->
            @if (count($products) > 0)
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
                    @foreach ($products as $prod)
                        <x-product-card :product="$prod" />
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-10 flex justify-center">
                    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 px-4 py-2.5 rounded-xl flex items-center shadow-sm text-xs">
                        {{ $products->links() }}
                    </div>
                </div>
            @else
                <!-- Empty state -->
                <div class="text-center py-16 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-8 max-w-sm mx-auto shadow-sm">
                    <div class="h-14 w-14 bg-slate-50 dark:bg-slate-950 rounded-full flex items-center justify-center text-slate-400 text-2xl mx-auto mb-4 border border-slate-150">
                        <i class="fa-solid fa-box-open"></i>
                    </div>
                    <h3 class="text-xs font-black uppercase tracking-wider text-slate-900 dark:text-slate-200">No items listed</h3>
                    <p class="text-[11px] text-slate-500 mt-2">There are currently no products available under this shop catalog.</p>
                    <a href="{{ route('storefront.index') }}" class="inline-block mt-5 px-4 py-2 text-white text-xs font-bold rounded-lg transition-all" style="background-color: #0059e3;">
                        Reset Filters
                    </a>
                </div>
            @endif
        </div>
    </div>
@endsection
