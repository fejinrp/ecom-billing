@extends('layouts.admin')

@section('content')
<div x-data="{
    open: false,
    search: '',
    products: @js($products),
    selectedProduct: null,
    loading: false,
    details: null,

    get filteredProducts() {
        if (!this.search) return this.products;
        const s = this.search.toLowerCase();
        return this.products.filter(p => 
            p.productname.toLowerCase().includes(s) || 
            p.pcode.toLowerCase().includes(s)
        );
    },

    selectProduct(prod) {
        this.selectedProduct = prod;
        this.open = false;
        this.search = '';
        this.fetchDetails(prod.id);
    },

    async fetchDetails(id) {
        this.loading = true;
        this.details = null;
        try {
            const res = await fetch(`/admin/products/price-search/${id}`);
            if (res.ok) {
                this.details = await res.json();
            } else {
                console.error('Failed to load product details');
            }
        } catch (e) {
            console.error(e);
        } finally {
            this.loading = false;
        }
    },

    formatRupee(val) {
        return '₹' + parseFloat(val).toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    },

    calculatePercent(sub, base) {
        if (!base || base == 0) return '0%';
        const pct = ((base - sub) / base) * 100;
        return pct.toFixed(1) + '%';
    }
}" class="space-y-8 animate-fadeIn">

    <!-- Header Section -->
    <x-admin.header 
        title="Search Product Price" 
        description="Lookup instant pricing tiers, margins, customer/dealer rates, and vendor purchase costs." 
        icon="fa-solid fa-magnifying-glass-dollar" 
        glass="true"
    />

    <!-- Search Input Widget -->
    <div class="max-w-xl mx-auto p-6 rounded-3xl bg-white dark:bg-slate-900/50 border border-slate-200 dark:border-slate-800/80 shadow-xl space-y-4">
        <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider text-center mb-1">Select Product to View Prices</label>
        
        <div class="relative">
            <!-- Searchable Dropdown Button -->
            <button @click="open = !open" 
                    type="button"
                    class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-2xl px-4 py-3.5 text-sm text-slate-700 dark:text-slate-300 flex items-center justify-between focus:outline-none focus:ring-1 focus:ring-indigo-500 transition-all hover:bg-slate-100 dark:hover:bg-slate-900/40">
                <span class="truncate font-semibold text-slate-850 dark:text-slate-200" x-text="selectedProduct ? selectedProduct.productname + ' (' + selectedProduct.pcode + ')' : '~~ Search MRP, C & D Product Price ~~'"></span>
                <i class="fa-solid fa-chevron-down text-slate-500 text-xs transition-transform duration-200" :class="open ? 'rotate-180' : ''"></i>
            </button>

            <!-- Search Options Panel -->
            <div x-show="open" 
                 x-cloak
                 @click.outside="open = false"
                 x-transition:enter="transition ease-out duration-150"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 class="absolute z-50 w-full mt-2 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-2xl p-3 space-y-2 max-h-80 overflow-y-auto scrollbar-thin">
                
                <!-- Search Box Inside Panel -->
                <div class="relative">
                    <input type="text" 
                           x-model="search"
                           placeholder="Type to filter products..." 
                           class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-805 rounded-xl pl-9 pr-4 py-2 text-xs text-slate-800 dark:text-slate-300 focus:outline-none focus:ring-1 focus:ring-indigo-500 placeholder-slate-400 dark:placeholder-slate-650">
                    <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-slate-500 text-[10px]"></i>
                </div>

                <!-- Products Options List -->
                <div class="divide-y divide-slate-850 overflow-y-auto max-h-56 scrollbar-thin pr-1">
                    <template x-for="prod in filteredProducts" :key="prod.id">
                        <button @click="selectProduct(prod)" 
                                type="button"
                                class="w-full text-left py-2.5 px-3 rounded-xl text-xs font-semibold text-slate-600 dark:text-slate-300 hover:text-indigo-650 dark:hover:text-indigo-400 hover:bg-slate-100 dark:hover:bg-slate-800/40 transition-all block truncate">
                            <span class="text-slate-800 dark:text-slate-100 font-bold" x-text="prod.productname"></span>
                            <span class="text-[10px] text-slate-500 ml-2 font-mono" x-text="'[' + prod.pcode + ']'"></span>
                        </button>
                    </template>
                    <div x-show="filteredProducts.length === 0" class="py-4 text-center text-xs text-slate-600 font-semibold">
                        No active stock products found.
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Loading Spinner -->
    <div x-show="loading" class="flex flex-col items-center justify-center py-16 space-y-4">
        <div class="h-10 w-10 border-4 border-indigo-500/20 border-t-indigo-500 rounded-full animate-spin"></div>
        <span class="text-sm text-slate-500 font-semibold uppercase tracking-widest animate-pulse">Fetching pricing tiers...</span>
    </div>

    <!-- Product Details Pricing Summary -->
    <div x-show="details" x-cloak class="space-y-8 animate-fadeIn max-w-5xl mx-auto">
        
        <!-- Product Profile Card -->
        <div class="p-6 rounded-3xl bg-white dark:bg-slate-900/40 border border-slate-200 dark:border-slate-800/80 shadow-md flex flex-col md:flex-row md:items-center justify-between gap-6 relative overflow-hidden">
            <!-- Background Glow Decorator -->
            <div class="absolute -right-24 -top-24 h-48 w-48 bg-indigo-650/10 rounded-full blur-3xl"></div>

            <div class="space-y-2">
                <span class="px-3 py-1 rounded-full text-[10px] font-mono font-bold bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 border border-indigo-500/20 uppercase tracking-wider" x-text="'CODE: ' + details.pcode"></span>
                <h2 class="text-2xl font-extrabold text-slate-800 dark:text-slate-100 tracking-tight" x-text="details.productname"></h2>
                <p class="text-sm text-slate-600 dark:text-slate-400 max-w-2xl" x-text="details.productdes"></p>
            </div>

            <div class="grid grid-cols-2 gap-x-8 gap-y-2 border-t md:border-t-0 md:border-l border-slate-200 dark:border-slate-800/80 pt-4 md:pt-0 md:pl-8 min-w-[220px]">
                <div>
                    <span class="block text-[10px] text-slate-500 font-bold uppercase tracking-wider">Brand</span>
                    <span class="font-bold text-slate-800 dark:text-slate-200" x-text="details.brand_name"></span>
                </div>
                <div>
                    <span class="block text-[10px] text-slate-500 font-bold uppercase tracking-wider">Category</span>
                    <span class="font-bold text-slate-800 dark:text-slate-200" x-text="details.cat_name"></span>
                </div>
                <div>
                    <span class="block text-[10px] text-slate-500 font-bold uppercase tracking-wider">Stock Qty</span>
                    <span class="px-2 py-0.5 rounded-lg text-xs font-bold bg-indigo-500/10 dark:bg-indigo-500/15 text-indigo-700 dark:text-indigo-300 border border-indigo-500/10" x-text="details.tqty + ' Qty'"></span>
                </div>
                <div>
                    <span class="block text-[10px] text-slate-500 font-bold uppercase tracking-wider">GST Tax Rate</span>
                    <span class="font-bold text-slate-800 dark:text-slate-200 font-mono" x-text="details.gst + '%'"></span>
                </div>
            </div>
        </div>

        <!-- Pricing Cards Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
            
            <!-- 1. MRP -->
            <div class="p-5 rounded-3xl bg-gradient-to-br from-rose-500/5 to-rose-600/15 border border-rose-500/20 shadow-lg flex flex-col justify-between h-36 group relative">
                <div>
                    <span class="text-xs font-bold text-rose-450 uppercase tracking-widest block">MRP</span>
                    <span class="text-[9px] text-rose-550 block mt-0.5 font-medium">Retail Maximum</span>
                </div>
                <div class="text-right">
                    <span class="text-2xl font-black text-rose-400 tracking-tight" x-text="formatRupee(details.mrp)"></span>
                </div>
            </div>

            <!-- 2. GP (Vendor Rate) -->
            <div class="p-5 rounded-3xl bg-white dark:bg-slate-900/60 border border-slate-200 dark:border-slate-805 shadow-md flex flex-col justify-between h-36 group relative">
                <div>
                    <span class="text-xs font-bold text-slate-500 dark:text-slate-450 uppercase tracking-widest block">GP (Cost)</span>
                    <span class="text-[9px] text-slate-500 dark:text-slate-550 block mt-0.5 font-medium">Purchase Rate</span>
                </div>
                <div class="text-right">
                    <span class="text-2xl font-extrabold text-slate-800 dark:text-slate-200 tracking-tight font-mono" x-text="formatRupee(details.prate)"></span>
                </div>
            </div>

            <!-- 3. Customer Price (CP) -->
            <div class="p-5 rounded-3xl bg-gradient-to-br from-purple-500/5 to-purple-600/15 border border-purple-500/20 shadow-lg flex flex-col justify-between h-36 group relative">
                <div>
                    <span class="text-xs font-bold text-purple-400 uppercase tracking-widest block">CP</span>
                    <span class="text-[9px] text-purple-500 block mt-0.5 font-medium">Retail Customer</span>
                </div>
                <div class="text-right">
                    <span class="text-2xl font-black text-purple-450 tracking-tight" x-text="formatRupee(details.cprice)"></span>
                </div>
            </div>

            <!-- 4. Dealer Price (DP) -->
            <div class="p-5 rounded-3xl bg-gradient-to-br from-emerald-500/5 to-emerald-600/15 border border-emerald-500/20 shadow-lg flex flex-col justify-between h-36 group relative">
                <div>
                    <span class="text-xs font-bold text-emerald-400 uppercase tracking-widest block">DP</span>
                    <span class="text-[9px] text-emerald-500 block mt-0.5 font-medium">Wholesale Dealer</span>
                </div>
                <div class="text-right">
                    <span class="text-2xl font-black text-emerald-450 tracking-tight" x-text="formatRupee(details.dprice)"></span>
                </div>
            </div>

            <!-- 5. Super Dealer (SD) -->
            <div class="p-5 rounded-3xl bg-gradient-to-br from-indigo-500/5 to-indigo-600/15 border border-indigo-500/20 shadow-lg flex flex-col justify-between h-36 group relative">
                <div>
                    <span class="text-xs font-bold text-indigo-400 uppercase tracking-widest block">SD</span>
                    <span class="text-[9px] text-indigo-500 block mt-0.5 font-medium">Bulk Super Dealer</span>
                </div>
                <div class="text-right">
                    <span class="text-2xl font-black text-indigo-450 tracking-tight" x-text="formatRupee(details.sdprice)"></span>
                </div>
            </div>
        </div>

        <!-- Active Batches Pricing Breakdown -->
        <div x-show="details.batches && details.batches.length > 0" class="p-6 rounded-3xl bg-white dark:bg-slate-900/30 border border-slate-200 dark:border-slate-800 shadow-xl space-y-4">
            <h3 class="text-sm font-bold text-slate-700 dark:text-slate-450 uppercase tracking-wider border-l-4 border-indigo-500 pl-3">Active Batches Pricing & Stock</h3>
            
            <div class="border border-slate-850 rounded-2xl overflow-hidden overflow-x-auto scrollbar-thin">
                <table class="w-full text-left text-xs text-slate-300 border-collapse">
                    <thead class="bg-slate-950/80 text-[10px] font-bold text-slate-500 uppercase tracking-wider border-b border-slate-850">
                        <tr>
                            <th class="p-3">Batch Number</th>
                            <th class="p-3 text-center">Mfg Date</th>
                            <th class="p-3 text-center">Expiry Date</th>
                            <th class="p-3 text-center">Warranty (m)</th>
                            <th class="p-3 text-center">Current Stock</th>
                            <th class="p-3 text-right">P. Rate</th>
                            <th class="p-3 text-right">S. Rate</th>
                            <th class="p-3 text-right">MRP</th>
                            <th class="p-3 text-right">Cust Price</th>
                            <th class="p-3 text-right">Dealer Price</th>
                            <th class="p-3 text-right">Super Dealer</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-850">
                        <template x-for="b in details.batches" :key="b.id">
                            <tr class="hover:bg-slate-900/20 transition-all font-semibold">
                                <td class="p-3 font-bold text-indigo-400 font-mono" x-text="b.batch_number"></td>
                                <td class="p-3 text-center text-slate-400" x-text="b.mfg_date ? b.mfg_date.substring(0,10) : 'N/A'"></td>
                                <td class="p-3 text-center text-slate-400" x-text="b.expiry_date ? b.expiry_date.substring(0,10) : 'N/A'"></td>
                                <td class="p-3 text-center font-mono text-slate-300" x-text="b.warranty_months + ' M'"></td>
                                <td class="p-3 text-center">
                                    <span class="px-2 py-0.5 rounded bg-emerald-500/10 text-emerald-450 font-bold" x-text="b.current_qty + ' PCS'"></span>
                                </td>
                                <td class="p-3 text-right text-slate-200 font-mono" x-text="formatRupee(b.prate)"></td>
                                <td class="p-3 text-right text-slate-200 font-mono" x-text="formatRupee(b.srate)"></td>
                                <td class="p-3 text-right text-rose-450 font-mono" x-text="formatRupee(b.mrp)"></td>
                                <td class="p-3 text-right text-purple-400 font-mono" x-text="formatRupee(b.cprice)"></td>
                                <td class="p-3 text-right text-emerald-400 font-mono" x-text="formatRupee(b.dprice)"></td>
                                <td class="p-3 text-right text-indigo-400 font-mono" x-text="formatRupee(b.sdprice)"></td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Profit & Margin Analytics Panel -->
        <div class="p-6 rounded-3xl bg-white dark:bg-slate-900/30 border border-slate-200 dark:border-slate-800 shadow-xl space-y-4">
            <h3 class="text-sm font-bold text-slate-700 dark:text-slate-450 uppercase tracking-wider border-l-4 border-indigo-500 pl-3">Margin & Saving Analytics</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 pt-2">
                <!-- Customer Saving -->
                <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-950/40 border border-slate-200 dark:border-slate-850 flex items-center justify-between">
                    <div>
                        <span class="block text-[10px] text-slate-500 font-bold uppercase tracking-wider">Customer Saving (vs MRP)</span>
                        <div class="flex items-baseline gap-2 mt-1">
                            <span class="text-xl font-bold text-purple-600 dark:text-purple-400" x-text="formatRupee(details.mrp - details.cprice)"></span>
                            <span class="text-xs text-slate-500">saved</span>
                        </div>
                    </div>
                    <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-purple-500/10 text-purple-650 dark:text-purple-400 border border-purple-500/20" x-text="calculatePercent(details.cprice, details.mrp) + ' Off'"></span>
                </div>

                <!-- Dealer Wholesale Margin -->
                <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-950/40 border border-slate-200 dark:border-slate-850 flex items-center justify-between">
                    <div>
                        <span class="block text-[10px] text-slate-500 font-bold uppercase tracking-wider">Dealer Markup (vs MRP)</span>
                        <div class="flex items-baseline gap-2 mt-1">
                            <span class="text-xl font-bold text-emerald-600 dark:text-emerald-400" x-text="formatRupee(details.mrp - details.dprice)"></span>
                            <span class="text-xs text-slate-500">markup</span>
                        </div>
                    </div>
                    <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-500/10 text-emerald-650 dark:text-emerald-400 border border-emerald-500/20" x-text="calculatePercent(details.dprice, details.mrp) + ' Margin'"></span>
                </div>

                <!-- Super Dealer Bulk Margin -->
                <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-950/40 border border-slate-200 dark:border-slate-850 flex items-center justify-between">
                    <div>
                        <span class="block text-[10px] text-slate-500 font-bold uppercase tracking-wider">Super Dealer Markup (vs MRP)</span>
                        <div class="flex items-baseline gap-2 mt-1">
                            <span class="text-xl font-bold text-indigo-650 dark:text-indigo-400" x-text="formatRupee(details.mrp - details.sdprice)"></span>
                            <span class="text-xs text-slate-500">markup</span>
                        </div>
                    </div>
                    <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-indigo-500/10 text-indigo-650 dark:text-indigo-400 border border-indigo-500/20" x-text="calculatePercent(details.sdprice, details.mrp) + ' Margin'"></span>
                </div>
            </div>
            
            <p class="text-[10px] text-slate-500 text-center font-medium italic mt-2">All calculated savings and markup margins are based on recommended selling points compared to maximum retail prices (MRP) inclusive of standard GST rates.</p>
        </div>
    </div>
</div>
@endsection
