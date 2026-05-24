@extends('layouts.admin')

@section('content')
<div x-data="{
    activeTab: '{{ request()->has('dealers_page') ? 'dealers' : (request()->has('sdealers_page') ? 'sdealers' : 'customers') }}'
}" class="space-y-8 animate-fadeIn">

    <!-- Header Section -->
    <x-admin.header 
        title="General Reports Ledger" 
        description="Export customer, dealer, and special dealer matrix directories directly to Excel or inspect live." 
        icon="fa-solid fa-chart-line" 
        glass="true"
    />

    <!-- Quick Excel Export Control Panel & Live Search -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Left Tab Lists panel -->
        <div class="lg:col-span-2 space-y-6">
            
            <!-- Custom Premium Tabs -->
            <div class="flex flex-col sm:flex-row p-1.5 bg-slate-100 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl gap-1">
                <button @click="activeTab = 'customers'" 
                        :class="activeTab === 'customers' ? 'bg-indigo-600 text-white shadow-lg' : 'text-slate-500 dark:text-slate-400 hover:text-slate-850 dark:hover:text-slate-100 hover:bg-slate-200 dark:hover:bg-slate-800/30'"
                        class="flex-1 py-3 px-4 text-xs font-bold uppercase tracking-wider rounded-xl transition-all flex items-center justify-center gap-2">
                    <i class="fa-solid fa-users"></i>
                    <span>Customers ({{ $customers->total() }})</span>
                </button>
                
                <button @click="activeTab = 'dealers'" 
                        :class="activeTab === 'dealers' ? 'bg-indigo-600 text-white shadow-lg' : 'text-slate-500 dark:text-slate-400 hover:text-slate-850 dark:hover:text-slate-100 hover:bg-slate-200 dark:hover:bg-slate-800/30'"
                        class="flex-1 py-3 px-4 text-xs font-bold uppercase tracking-wider rounded-xl transition-all flex items-center justify-center gap-2">
                    <i class="fa-solid fa-store"></i>
                    <span>Dealers ({{ $dealers->total() }})</span>
                </button>
                
                <button @click="activeTab = 'sdealers'" 
                        :class="activeTab === 'sdealers' ? 'bg-indigo-600 text-white shadow-lg' : 'text-slate-500 dark:text-slate-400 hover:text-slate-850 dark:hover:text-slate-100 hover:bg-slate-200 dark:hover:bg-slate-800/30'"
                        class="flex-1 py-3 px-4 text-xs font-bold uppercase tracking-wider rounded-xl transition-all flex items-center justify-center gap-2">
                    <i class="fa-solid fa-handshake"></i>
                    <span>S-Dealers ({{ $sdealers->total() }})</span>
                </button>
            </div>

            <!-- Search Form -->
            <form method="GET" action="{{ route('admin.reports.index') }}" class="flex gap-4">
                <div class="relative flex-1">
                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-500">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </span>
                    <input type="text" name="search" value="{{ $search }}" placeholder="Search ledger by name, contact, city..." class="w-full bg-slate-100 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl pl-11 pr-4 py-3.5 text-sm text-slate-800 dark:text-slate-300 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                </div>
                @if($search)
                    <a href="{{ route('admin.reports.index') }}" class="px-5 py-3.5 bg-slate-200 hover:bg-slate-300 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 text-sm font-semibold rounded-2xl transition-all flex items-center">Reset</a>
                @endif
                <x-admin.button type="submit" variant="primary">Filter</x-admin.button>
            </form>

            <!-- 1. Customers Tab Grid -->
            <div x-show="activeTab === 'customers'" x-transition class="p-6 rounded-3xl bg-white dark:bg-slate-900/50 border border-slate-200 dark:border-slate-800/80 shadow-xl space-y-4">
                <div class="responsive-table-container scrollbar-thin rounded-2xl border border-slate-200 dark:border-slate-800/80">
                    <table class="w-full text-left text-sm text-slate-800 dark:text-slate-300 min-w-0 lg:min-w-[750px] block lg:table">
                        <thead class="bg-slate-100 dark:bg-slate-950 text-xs font-semibold uppercase text-slate-500 dark:text-slate-400 border-b border-slate-200 dark:border-slate-800 hidden lg:table-header-group">
                            <tr>
                                <th class="px-4 py-4">Name</th>
                                <th class="px-4 py-4">Mobile</th>
                                <th class="px-4 py-4">Pincode</th>
                                <th class="px-4 py-4">City / State</th>
                                <th class="px-4 py-4">Full Address</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 dark:divide-slate-850 block lg:table-row-group w-full space-y-4 lg:space-y-0 lg:divide-y lg:divide-slate-800/40 p-4 lg:p-0">
                            @forelse($customers as $cust)
                                <tr class="hover:bg-slate-100 dark:hover:bg-slate-800/30 transition-all block lg:table-row w-full bg-white dark:bg-slate-900/30 border border-slate-200 dark:border-slate-800/60 rounded-2xl p-4 lg:p-0 mb-4 lg:mb-0 relative grid grid-cols-2 gap-x-4 gap-y-3 lg:grid-cols-none lg:bg-transparent lg:border-0 lg:hover:bg-slate-900/20 lg:transition-all">
                                    <!-- Name -->
                                    <td class="col-span-2 bg-indigo-500/10 text-indigo-400 px-3 py-2.5 rounded-xl text-xs font-mono font-bold flex items-center justify-between lg:table-cell lg:col-span-none lg:bg-transparent lg:px-4 lg:py-4 lg:text-slate-800 lg:dark:text-slate-200 lg:font-bold lg:uppercase">
                                        <span class="lg:hidden uppercase tracking-wider text-[10px] font-bold text-indigo-400/80">Account Name</span>
                                        <span>{{ $cust->uname }}</span>
                                    </td>
                                    <!-- Mobile -->
                                    <td class="py-2 lg:px-4 lg:py-4 col-span-1 block lg:table-cell lg:col-span-none">
                                        <span class="block lg:hidden text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Mobile</span>
                                        <span class="font-semibold text-indigo-500 dark:text-indigo-400">{{ $cust->contactno }}</span>
                                    </td>
                                    <!-- Pincode -->
                                    <td class="py-2 lg:px-4 lg:py-4 col-span-1 block lg:table-cell lg:col-span-none">
                                        <span class="block lg:hidden text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Pincode</span>
                                        <span class="font-mono text-slate-600 dark:text-slate-400">{{ $cust->billingpincode ?? 'N/A' }}</span>
                                    </td>
                                    <!-- City / State -->
                                    <td class="py-2 lg:px-4 lg:py-4 col-span-2 lg:col-span-1 block lg:table-cell lg:col-span-none">
                                        <span class="block lg:hidden text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">City / State</span>
                                        <div class="font-bold text-slate-800 dark:text-slate-300 inline-block lg:block">{{ $cust->billingcity ?? 'N/A' }}</div>
                                        <span class="lg:hidden text-slate-500 mx-2">/</span>
                                        <div class="text-xs text-slate-500 inline-block lg:block">{{ $cust->billingstate ?? 'N/A' }}</div>
                                    </td>
                                    <!-- Full Address -->
                                    <td class="py-2 lg:px-4 lg:py-4 col-span-2 block lg:table-cell lg:col-span-none">
                                        <span class="block lg:hidden text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Full Address</span>
                                        <span class="text-xs text-slate-600 dark:text-slate-400 max-w-xs truncate uppercase block lg:inline" title="{{ $cust->billingaddress }}">{{ $cust->billingaddress ?? 'N/A' }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr class="block lg:table-row w-full bg-white dark:bg-slate-900/30 border border-slate-200 dark:border-slate-800/60 rounded-2xl p-6 lg:p-0">
                                    <td colspan="5" class="px-6 py-8 text-center text-slate-500 font-medium block lg:table-cell w-full">No customer records matching filter.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="pt-4">
                    {{ $customers->appends(['dealers_page' => $dealers->currentPage(), 'sdealers_page' => $sdealers->currentPage(), 'search' => $search])->links() }}
                </div>
            </div>

            <!-- 2. Dealers Tab Grid -->
            <div x-show="activeTab === 'dealers'" x-transition class="p-6 rounded-3xl bg-white dark:bg-slate-900/50 border border-slate-200 dark:border-slate-800/80 shadow-xl space-y-4">
                <div class="responsive-table-container scrollbar-thin rounded-2xl border border-slate-200 dark:border-slate-800/80">
                    <table class="w-full text-left text-sm text-slate-800 dark:text-slate-300 min-w-0 lg:min-w-[750px] block lg:table">
                        <thead class="bg-slate-100 dark:bg-slate-950 text-xs font-semibold uppercase text-slate-500 dark:text-slate-400 border-b border-slate-200 dark:border-slate-800 hidden lg:table-header-group">
                            <tr>
                                <th class="px-4 py-4">Name</th>
                                <th class="px-4 py-4">Mobile</th>
                                <th class="px-4 py-4">Pincode</th>
                                <th class="px-4 py-4">City / State</th>
                                <th class="px-4 py-4">Full Address</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 dark:divide-slate-850 block lg:table-row-group w-full space-y-4 lg:space-y-0 lg:divide-y lg:divide-slate-800/40 p-4 lg:p-0">
                            @forelse($dealers as $dlr)
                                <tr class="hover:bg-slate-100 dark:hover:bg-slate-800/30 transition-all block lg:table-row w-full bg-white dark:bg-slate-900/30 border border-slate-200 dark:border-slate-800/60 rounded-2xl p-4 lg:p-0 mb-4 lg:mb-0 relative grid grid-cols-2 gap-x-4 gap-y-3 lg:grid-cols-none lg:bg-transparent lg:border-0 lg:hover:bg-slate-900/20 lg:transition-all">
                                    <!-- Name -->
                                    <td class="col-span-2 bg-indigo-500/10 text-indigo-400 px-3 py-2.5 rounded-xl text-xs font-mono font-bold flex items-center justify-between lg:table-cell lg:col-span-none lg:bg-transparent lg:px-4 lg:py-4 lg:text-slate-800 lg:dark:text-slate-200 lg:font-bold lg:uppercase">
                                        <span class="lg:hidden uppercase tracking-wider text-[10px] font-bold text-indigo-400/80">Account Name</span>
                                        <span>{{ $dlr->uname }}</span>
                                    </td>
                                    <!-- Mobile -->
                                    <td class="py-2 lg:px-4 lg:py-4 col-span-1 block lg:table-cell lg:col-span-none">
                                        <span class="block lg:hidden text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Mobile</span>
                                        <span class="font-semibold text-indigo-555 dark:text-indigo-400">{{ $dlr->contactno }}</span>
                                    </td>
                                    <!-- Pincode -->
                                    <td class="py-2 lg:px-4 lg:py-4 col-span-1 block lg:table-cell lg:col-span-none">
                                        <span class="block lg:hidden text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Pincode</span>
                                        <span class="font-mono text-slate-600 dark:text-slate-400">{{ $dlr->billingpincode ?? 'N/A' }}</span>
                                    </td>
                                    <!-- City / State -->
                                    <td class="py-2 lg:px-4 lg:py-4 col-span-2 lg:col-span-1 block lg:table-cell lg:col-span-none">
                                        <span class="block lg:hidden text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">City / State</span>
                                        <div class="font-bold text-slate-800 dark:text-slate-300 inline-block lg:block">{{ $dlr->billingcity ?? 'N/A' }}</div>
                                        <span class="lg:hidden text-slate-500 mx-2">/</span>
                                        <div class="text-xs text-slate-500 inline-block lg:block">{{ $dlr->billingstate ?? 'N/A' }}</div>
                                    </td>
                                    <!-- Full Address -->
                                    <td class="py-2 lg:px-4 lg:py-4 col-span-2 block lg:table-cell lg:col-span-none">
                                        <span class="block lg:hidden text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Full Address</span>
                                        <span class="text-xs text-slate-600 dark:text-slate-400 max-w-xs truncate uppercase block lg:inline" title="{{ $dlr->billingaddress }}">{{ $dlr->billingaddress ?? 'N/A' }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr class="block lg:table-row w-full bg-white dark:bg-slate-900/30 border border-slate-200 dark:border-slate-800/60 rounded-2xl p-6 lg:p-0">
                                    <td colspan="5" class="px-6 py-8 text-center text-slate-500 font-medium block lg:table-cell w-full">No dealer records matching filter.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="pt-4">
                    {{ $dealers->appends(['customers_page' => $customers->currentPage(), 'sdealers_page' => $sdealers->currentPage(), 'search' => $search])->links() }}
                </div>
            </div>

            <!-- 3. S-Dealers Tab Grid -->
            <div x-show="activeTab === 'sdealers'" x-transition class="p-6 rounded-3xl bg-white dark:bg-slate-900/50 border border-slate-200 dark:border-slate-800/80 shadow-xl space-y-4">
                <div class="responsive-table-container scrollbar-thin rounded-2xl border border-slate-200 dark:border-slate-800/80">
                    <table class="w-full text-left text-sm text-slate-800 dark:text-slate-300 min-w-0 lg:min-w-[750px] block lg:table">
                        <thead class="bg-slate-100 dark:bg-slate-950 text-xs font-semibold uppercase text-slate-500 dark:text-slate-400 border-b border-slate-200 dark:border-slate-800 hidden lg:table-header-group">
                            <tr>
                                <th class="px-4 py-4">Name</th>
                                <th class="px-4 py-4">Mobile</th>
                                <th class="px-4 py-4">Pincode</th>
                                <th class="px-4 py-4">City / State</th>
                                <th class="px-4 py-4">Full Address</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 dark:divide-slate-850 block lg:table-row-group w-full space-y-4 lg:space-y-0 lg:divide-y lg:divide-slate-800/40 p-4 lg:p-0">
                            @forelse($sdealers as $sdlr)
                                <tr class="hover:bg-slate-100 dark:hover:bg-slate-800/30 transition-all block lg:table-row w-full bg-white dark:bg-slate-900/30 border border-slate-200 dark:border-slate-800/60 rounded-2xl p-4 lg:p-0 mb-4 lg:mb-0 relative grid grid-cols-2 gap-x-4 gap-y-3 lg:grid-cols-none lg:bg-transparent lg:border-0 lg:hover:bg-slate-900/20 lg:transition-all">
                                    <!-- Name -->
                                    <td class="col-span-2 bg-indigo-500/10 text-indigo-400 px-3 py-2.5 rounded-xl text-xs font-mono font-bold flex items-center justify-between lg:table-cell lg:col-span-none lg:bg-transparent lg:px-4 lg:py-4 lg:text-slate-800 lg:dark:text-slate-200 lg:font-bold lg:uppercase">
                                        <span class="lg:hidden uppercase tracking-wider text-[10px] font-bold text-indigo-400/80">Account Name</span>
                                        <span>{{ $sdlr->uname }}</span>
                                    </td>
                                    <!-- Mobile -->
                                    <td class="py-2 lg:px-4 lg:py-4 col-span-1 block lg:table-cell lg:col-span-none">
                                        <span class="block lg:hidden text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Mobile</span>
                                        <span class="font-semibold text-indigo-400">{{ $sdlr->contactno }}</span>
                                    </td>
                                    <!-- Pincode -->
                                    <td class="py-2 lg:px-4 lg:py-4 col-span-1 block lg:table-cell lg:col-span-none">
                                        <span class="block lg:hidden text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Pincode</span>
                                        <span class="font-mono text-slate-400">{{ $sdlr->billingpincode ?? 'N/A' }}</span>
                                    </td>
                                    <!-- City / State -->
                                    <td class="py-2 lg:px-4 lg:py-4 col-span-2 lg:col-span-1 block lg:table-cell lg:col-span-none">
                                        <span class="block lg:hidden text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">City / State</span>
                                        <div class="font-bold text-slate-300 inline-block lg:block">{{ $sdlr->billingcity ?? 'N/A' }}</div>
                                        <span class="lg:hidden text-slate-500 mx-2">/</span>
                                        <div class="text-xs text-slate-500 inline-block lg:block">{{ $sdlr->billingstate ?? 'N/A' }}</div>
                                    </td>
                                    <!-- Full Address -->
                                    <td class="py-2 lg:px-4 lg:py-4 col-span-2 block lg:table-cell lg:col-span-none">
                                        <span class="block lg:hidden text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Full Address</span>
                                        <span class="text-xs text-slate-600 dark:text-slate-400 max-w-xs truncate uppercase block lg:inline" title="{{ $sdlr->billingaddress }}">{{ $sdlr->billingaddress ?? 'N/A' }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr class="block lg:table-row w-full bg-white dark:bg-slate-900/30 border border-slate-200 dark:border-slate-800/60 rounded-2xl p-6 lg:p-0">
                                    <td colspan="5" class="px-6 py-8 text-center text-slate-500 font-medium block lg:table-cell w-full">No S-dealer records matching filter.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="pt-4">
                    {{ $sdealers->appends(['customers_page' => $customers->currentPage(), 'dealers_page' => $dealers->currentPage(), 'search' => $search])->links() }}
                </div>
            </div>

        </div>

        <!-- Right Control Panel: Export to Excel + Business Reports -->
        <div class="space-y-6">

            <!-- Business Reports Generator Card -->
            <div x-data="{
                    reportType: '',
                    showExpense: false,
                    showProduct: false,
                    updateFields() {
                        this.showExpense = (this.reportType === 'expenses');
                        this.showProduct = ['itemnameoff','itemnameon','purchase'].includes(this.reportType);
                    },
                    submit() {
                        const f = this.$refs.bizForm;
                        if (!f.checkValidity()) { f.reportValidity(); return; }
                        const params = new URLSearchParams(new FormData(f));
                        window.open('{{ route('admin.reports.print') }}?' + params.toString(), '_blank');
                    }
                }"
                class="p-6 md:p-8 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-violet-800/40 shadow-xl shadow-slate-100 dark:shadow-violet-900/10 space-y-5">

                <h3 class="text-sm font-bold text-slate-800 dark:text-slate-200 uppercase tracking-wider border-l-4 border-violet-500 pl-3 flex items-center gap-2">
                    <i class="fa-solid fa-chart-bar text-violet-400"></i>
                    Business Reports
                </h3>

                <form x-ref="bizForm" class="space-y-4" @submit.prevent="submit()">

                    {{-- Report Type --}}
                    <div class="space-y-1.5">
                        <label class="block text-xs font-semibold text-slate-550 dark:text-slate-400 uppercase tracking-wider">Report Type</label>
                        <select name="report" id="biz_report" required
                            x-model="reportType" @change="updateFields()"
                            class="w-full bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-3 text-sm text-slate-800 dark:text-slate-300 focus:outline-none focus:ring-1 focus:ring-violet-500 transition">
                            <option value="">~ Select Report ~</option>
                            <option value="expenses">Expenses Report</option>
                            <option value="itemnameoff">Offline Sales Itemnamewise</option>
                            <option value="itemnameon">Online Sales Itemnamewise</option>
                            <option value="purchase">Purchase Namewise</option>
                        </select>
                    </div>

                    {{-- Expense Name (only for Expenses) --}}
                    <div x-show="showExpense" x-transition.opacity class="space-y-1.5">
                        <label class="block text-xs font-semibold text-slate-550 dark:text-slate-400 uppercase tracking-wider">Expense Category</label>
                        <select name="expNameo" id="biz_expNameo"
                            class="w-full bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-3 text-sm text-slate-850 dark:text-slate-300 focus:outline-none focus:ring-1 focus:ring-violet-500 transition">
                            <option value="0">All Expense Categories</option>
                            @foreach($expenses as $exp)
                                <option value="{{ $exp->exp_id }}">{{ $exp->exp_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Product Name (only for Sales / Purchase) --}}
                    <div x-show="showProduct" x-transition.opacity class="space-y-1.5">
                        <label class="block text-xs font-semibold text-slate-550 dark:text-slate-400 uppercase tracking-wider">Product</label>
                        <select name="productd" id="biz_productd"
                            class="w-full bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-3 text-sm text-slate-850 dark:text-slate-300 focus:outline-none focus:ring-1 focus:ring-violet-500 transition">
                            <option value="0">All Products</option>
                            @foreach($products as $prod)
                                <option value="{{ $prod->id }}">{{ $prod->productname }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Date Range --}}
                    <div class="grid grid-cols-2 gap-3">
                        <div class="space-y-1.5">
                            <label class="block text-xs font-semibold text-slate-550 dark:text-slate-400 uppercase tracking-wider">Start Date</label>
                            <input type="date" name="startDate" id="biz_startDate" required
                                class="w-full bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-700 rounded-xl px-3 py-3 text-sm text-slate-800 dark:text-slate-300 focus:outline-none focus:ring-1 focus:ring-violet-500 transition">
                        </div>
                        <div class="space-y-1.5">
                            <label class="block text-xs font-semibold text-slate-550 dark:text-slate-400 uppercase tracking-wider">End Date</label>
                            <input type="date" name="endDate" id="biz_endDate" required
                                class="w-full bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-700 rounded-xl px-3 py-3 text-sm text-slate-800 dark:text-slate-300 focus:outline-none focus:ring-1 focus:ring-violet-500 transition">
                        </div>
                    </div>

                    <x-admin.button type="submit" variant="primary" icon="fa-solid fa-print text-lg" class="w-full">
                        Preview &amp; Print Report
                    </x-admin.button>
                </form>
            </div>

            <!-- Excel Generation Card -->
            <div class="p-6 md:p-8 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xl space-y-6">
                <h3 class="text-sm font-bold text-slate-800 dark:text-slate-200 uppercase tracking-wider border-l-4 border-emerald-500 pl-3">Excel Export Panel</h3>
                
                <form method="POST" action="{{ route('admin.reports.excel') }}" class="space-y-6">
                    @csrf
                    
                    <div class="space-y-2">
                        <label class="block text-xs font-semibold text-slate-550 dark:text-slate-400 uppercase tracking-wider">Report Directory</label>
                        <select name="report" required class="w-full bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl px-4 py-3.5 text-sm text-slate-800 dark:text-slate-300 focus:outline-none focus:ring-1 focus:ring-emerald-500">
                            <option value="">~Select~</option>
                            <option value="1">Customer Report</option>
                            <option value="2">Dealer Report</option>
                            <option value="3">SDealer Report</option>
                            <option value="4">Online Customer Report</option>
                            <option value="5">Online Dealer Report</option>
                        </select>
                    </div>

                    <p class="text-xs text-slate-500 leading-relaxed">
                        Downloads are packaged instantly inside lightweight Tab-Separated `.xls` spreadsheets, fully compatible with Excel, Google Sheets, LibreOffice, and Numbers.
                    </p>

                    <x-admin.button type="submit" variant="primary" icon="fa-solid fa-file-excel text-lg" class="w-full">
                        Generate Report to Excel
                    </x-admin.button>
                </form>
            </div>

            <!-- Interactive Metric Highlights Card -->
            <div class="p-6 md:p-8 rounded-3xl glassmorphism space-y-6">
                <h3 class="text-xs font-bold text-slate-800 dark:text-slate-200 uppercase tracking-wider">Ledger Statistics</h3>
                <div class="space-y-4">
                    <!-- Stat 1 -->
                    <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-950/40 border border-slate-200 dark:border-slate-850 flex items-center justify-between">
                        <div>
                            <span class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest font-mono">Active Accounts</span>
                            <span class="block text-2xl font-black text-slate-800 dark:text-slate-200 mt-1 font-outfit">
                                {{ \App\Models\User::count() }}
                            </span>
                        </div>
                        <div class="p-3 bg-indigo-500/10 text-indigo-400 rounded-xl">
                            <i class="fa-solid fa-circle-nodes text-lg"></i>
                        </div>
                    </div>

                    <!-- Stat 2 -->
                    <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-950/40 border border-slate-200 dark:border-slate-850 flex items-center justify-between">
                        <div>
                            <span class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest font-mono">Special Dealers</span>
                            <span class="block text-2xl font-black text-emerald-400 mt-1 font-outfit">
                                {{ \App\Models\User::where('usertype', 'S')->count() }}
                            </span>
                        </div>
                        <div class="p-3 bg-emerald-500/10 text-emerald-400 rounded-xl">
                            <i class="fa-solid fa-handshake text-lg"></i>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </div>

</div>
@endsection
