@extends('layouts.storefront')

@section('content')
    @php
        $categoryItems = collect(is_object($categories) && method_exists($categories, 'items') ? $categories->items() : ($categories ?? []))
            ->unique(fn ($cat) => $cat->cat_name ?? $cat->cat_id ?? spl_object_id($cat))
            ->values();
    @endphp

    <!-- ── Category Page Header & Breadcrumb ── -->
    <div class="relative overflow-hidden rounded-[2rem] border border-slate-200/70 dark:border-slate-800/70 mb-10 p-8 md:p-12 shadow-2xl shadow-blue-500/5 bg-white/92 dark:bg-slate-900/82 backdrop-blur-sm">
        <div class="absolute -right-24 -top-24 w-80 h-80 bg-blue-600/5 dark:bg-indigo-600/10 rounded-full blur-3xl animate-pulse pointer-events-none"></div>
        
        <div class="relative z-10 space-y-4">
            <!-- Breadcrumbs -->
            <div class="flex flex-wrap items-center gap-2.5 text-xs font-black text-blue-600 dark:text-indigo-400">
                <a href="{{ route('storefront.index') }}" class="hover:text-blue-500 dark:hover:text-indigo-300 transition-colors uppercase tracking-wider">Hardware Store</a>
                <i class="fa-solid fa-chevron-right text-[8px] text-slate-400 dark:text-slate-600"></i>
                <span class="text-slate-500 dark:text-slate-400 uppercase tracking-wide">Category</span>
                <i class="fa-solid fa-chevron-right text-[8px] text-slate-400 dark:text-slate-600"></i>
                <span class="text-slate-800 dark:text-slate-300 uppercase tracking-wide bg-blue-500/5 dark:bg-indigo-500/10 px-2 py-0.5 rounded border border-blue-500/10 dark:border-indigo-500/15">{{ $category->cat_name }}</span>
            </div>
            
            <h1 class="text-3xl md:text-4xl font-black text-slate-900 dark:text-white uppercase tracking-tight">
                Vertical: <span class="bg-gradient-to-r from-slate-900 via-blue-700 to-indigo-700 dark:from-slate-100 dark:via-blue-300 dark:to-indigo-300 bg-clip-text text-transparent">{{ $category->cat_name }}</span>
            </h1>
            <p class="text-xs md:text-sm text-slate-500 dark:text-slate-400 max-w-2xl leading-relaxed">
                Browse our premium selection of certified hardware models under the {{ $category->cat_name }} vertical catalog. Certified imports are backed by authentic Indian corporate warranty.
            </p>
        </div>
    </div>

    <!-- ── Dual Column Shop Layout ── -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        
        <!-- Left Sidebar Filter (Desktop) -->
        <div class="lg:col-span-3 space-y-6 hidden lg:block">
            <!-- Category Sidebar card -->
            <div class="bg-white/92 dark:bg-slate-900/82 border border-slate-200/70 dark:border-slate-800/70 rounded-2xl p-5 shadow-lg shadow-blue-500/5 space-y-5">
                <div class="flex items-center justify-between border-b border-slate-200/70 dark:border-slate-800/70 pb-3">
                    <h3 class="text-xs font-black uppercase tracking-widest text-slate-900 dark:text-white">Active Categories</h3>
                    <a href="{{ route('storefront.index') }}" class="text-[9px] font-black uppercase text-blue-600 dark:text-indigo-400 hover:underline">Reset</a>
                </div>
                
                <ul class="space-y-1.5 max-h-72 overflow-y-auto custom-scrollbar pr-1">
                    @if ($categoryItems->count() > 0)
                        @foreach ($categoryItems as $cat)
                            <li>
                                <a href="{{ route('storefront.category', $cat->cat_name) }}" 
                                   class="flex items-center justify-between px-3 py-2 rounded-xl text-xs font-semibold uppercase transition-all {{ $category->cat_id == $cat->cat_id ? 'bg-blue-600 text-white font-bold' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800/50 hover:text-slate-900 dark:hover:text-white' }}">
                                    <span class="truncate pr-2">{{ $cat->cat_name }}</span>
                                    <i class="fa-solid fa-chevron-right text-[8px] {{ $category->cat_id == $cat->cat_id ? 'text-white' : 'text-slate-400' }}"></i>
                                </a>
                            </li>
                        @endforeach
                    @endif
                </ul>
            </div>

            <!-- Trust Badge Card -->
            <div class="bg-gradient-to-br from-blue-900 to-indigo-950 text-white rounded-2xl p-5 mt-1 shadow-md space-y-3.5 border border-blue-800/30">
                <div class="w-8 h-8 rounded-lg bg-white/10 flex items-center justify-center text-orange-400 text-sm">
                    <i class="fa-solid fa-shield-halved"></i>
                </div>
                <h4 class="text-xs font-extrabold uppercase tracking-wider">Corporate Security</h4>
                <p class="text-[10px] text-slate-300 leading-relaxed font-medium">Every purchased component comes with verified transactional invoices, compliant GST outputs, and authentic brand serial authentication codes.</p>
            </div>
        </div>

        <!-- Right Grid Column -->
        <div class="lg:col-span-9 space-y-6">
            
            <!-- Category Header Controls -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white/92 dark:bg-slate-900/82 border border-slate-200/70 dark:border-slate-800/70 rounded-2xl p-4 shadow-lg shadow-blue-500/5">
                <div>
                    <span class="text-xs font-black uppercase text-slate-500 dark:text-slate-400 tracking-wider">Active View:</span>
                    <span class="text-xs font-extrabold uppercase text-slate-800 dark:text-slate-200 ml-1.5">{{ $products->total() }} Premium hardware units</span>
                </div>
                
                <!-- Category Horizontal Chips (Mobile & General Navigation) -->
                <div class="flex items-center gap-2 overflow-x-auto custom-scrollbar pb-1 max-w-full lg:max-w-xs">
                    <a href="{{ route('storefront.index') }}" class="px-3.5 py-1.5 rounded-lg text-[10px] font-black uppercase bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white transition-all whitespace-nowrap">
                        ALL HARDWARE
                    </a>
                    @if ($categoryItems->count() > 0)
                        @foreach ($categoryItems->take(6) as $cat)
                            @if($category->cat_id != $cat->cat_id)
                                <a href="{{ route('storefront.category', $cat->cat_name) }}" class="px-3.5 py-1.5 rounded-lg text-[10px] font-black uppercase bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white transition-all whitespace-nowrap">
                                    {{ $cat->cat_name }}
                                </a>
                            @endif
                        @endforeach
                    @endif
                </div>
            </div>

            <!-- Products Grid -->
            @if (count($products) > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach ($products as $prod)
                        <x-product-card :product="$prod" />
                    @endforeach
                </div>

                <!-- Paginate Links -->
                <div class="mt-12 flex justify-center">
                    <div class="sf-pagination px-5 py-3 rounded-2xl border border-slate-200/70 dark:border-slate-800/70 flex items-center gap-2 bg-white/92 dark:bg-slate-900/82 shadow-lg shadow-blue-500/5">
                        {{ $products->links() }}
                    </div>
                </div>
            @else
                <!-- Empty State -->
                <div class="text-center py-16 bg-white/92 dark:bg-slate-900/82 border border-slate-200/70 dark:border-slate-850/80 rounded-[2rem] p-8 max-w-md mx-auto shadow-2xl shadow-blue-500/5 relative overflow-hidden">
                    <div class="absolute -right-12 -top-12 w-32 h-32 bg-blue-600/5 dark:bg-indigo-600/5 rounded-full blur-2xl pointer-events-none"></div>
                    <div class="h-16 w-16 bg-blue-50 dark:bg-indigo-950 rounded-2xl flex items-center justify-center text-blue-600 dark:text-indigo-400 text-3xl mx-auto mb-5 border border-blue-100 dark:border-indigo-900 shadow-inner">
                        <i class="fa-solid fa-folder-open animate-bounce"></i>
                    </div>
                    <h3 class="text-sm font-black text-slate-800 dark:text-slate-100 uppercase tracking-widest">Category currently empty</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-2.5 leading-relaxed">
                        There are currently no products registered under this vertical category. Our team is constantly sourcing imported hardware units. Please check back shortly.
                    </p>
                    <a href="{{ route('storefront.index') }}" class="inline-flex mt-6 px-5 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-700 hover:from-blue-500 hover:to-indigo-600 text-white text-xs font-black uppercase tracking-widest rounded-xl transition-all shadow-lg shadow-blue-600/15 cursor-pointer">
                        Reset Storefront Filters
                    </a>
                </div>
            @endif
        </div>
    </div>
@endsection
