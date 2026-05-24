@extends('layouts.storefront')

@section('content')
    @php
        $totalSpent = 0;
        if (isset($orders)) {
            foreach ($orders as $ord) {
                if ($ord->order_status != 3) {
                    $totalSpent += $ord->grand_total;
                }
            }
        }
    @endphp

    <!-- ── Header ── -->
    <div class="mb-8">
        <div class="flex items-center gap-2 text-[10px] font-black uppercase tracking-[0.35em] text-blue-600 dark:text-indigo-400 mb-3">
            <a href="{{ route('storefront.index') }}" class="hover:underline">Home</a>
            <i class="fa-solid fa-chevron-right text-[8px] text-slate-400 dark:text-slate-600"></i>
            <span class="text-slate-500 dark:text-slate-400">Orders</span>
        </div>
        <div class="flex flex-col gap-3">
            <h1 class="text-3xl sm:text-4xl font-black text-slate-900 dark:text-white uppercase tracking-tight">Purchase History</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 max-w-3xl leading-relaxed">
                Review invoices, payment channels, dispatch status, and print-ready order records from your customer portal.
            </p>
        </div>
    </div>

    <!-- ── Dashboard Layout ── -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        
        <!-- Left Sidebar: Customer Profile -->
        <div class="lg:col-span-3 space-y-6">
            <!-- Profile Card -->
            <div class="bg-white/92 dark:bg-slate-900/82 border border-slate-200/70 dark:border-slate-800/70 rounded-[1.75rem] p-6 shadow-lg shadow-slate-900/5 text-center relative overflow-hidden">
                <div class="absolute -right-12 -top-12 w-28 h-28 bg-blue-600/5 dark:bg-indigo-600/5 rounded-full blur-xl"></div>
                <div class="absolute -left-10 -bottom-10 w-24 h-24 bg-orange-500/5 rounded-full blur-2xl"></div>
                
                <div class="relative z-10 space-y-4">
                    <!-- Avatar circle -->
                    <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-blue-600 to-indigo-700 text-white flex items-center justify-center font-black text-2xl mx-auto shadow-lg shadow-blue-500/20 uppercase font-outfit">
                        {{ substr(Auth::user()->uname ?: Auth::user()->name ?: 'C', 0, 1) }}
                    </div>
                    
                    <div>
                        <h2 class="text-base font-black text-slate-900 dark:text-white uppercase tracking-tight truncate">{{ Auth::user()->uname ?: Auth::user()->name }}</h2>
                        <span class="inline-flex mt-1 items-center gap-1 px-2.5 py-0.5 rounded-full text-[9px] font-black uppercase tracking-wider bg-blue-50 dark:bg-indigo-500/10 text-blue-600 dark:text-indigo-400 border border-blue-100 dark:border-indigo-500/15">
                            Valued Customer
                        </span>
                    </div>

                    <div class="border-t border-slate-200/70 dark:border-slate-800 pt-4 space-y-2 text-left text-[11px] text-slate-500 dark:text-slate-400">
                        <div class="flex justify-between">
                            <span class="font-bold text-slate-400 dark:text-slate-500 uppercase">Registered Email:</span>
                            <span class="font-semibold text-slate-700 dark:text-slate-300 truncate max-w-[130px]" title="{{ Auth::user()->email }}">{{ Auth::user()->email }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="font-bold text-slate-400 dark:text-slate-500 uppercase">Contact No:</span>
                            <span class="font-mono font-bold text-slate-700 dark:text-slate-300">{{ Auth::user()->contactno ?: 'N/A' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="font-bold text-slate-400 dark:text-slate-500 uppercase">B2B GSTIN:</span>
                            <span class="font-mono font-bold text-slate-700 dark:text-slate-300">{{ Auth::user()->gsttin ?: 'Personal' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Links -->
            <div class="bg-white/92 dark:bg-slate-900/82 border border-slate-200/70 dark:border-slate-800/70 rounded-[1.5rem] p-4 shadow-lg shadow-slate-900/5 space-y-1">
                <a href="{{ route('storefront.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800/50 hover:text-blue-600 dark:hover:text-white transition-all uppercase tracking-wider">
                    <i class="fa-solid fa-store text-blue-600 dark:text-indigo-400 w-4"></i> Shop Components
                </a>
                <a href="{{ route('storefront.cart') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800/50 hover:text-blue-600 dark:hover:text-white transition-all uppercase tracking-wider">
                    <i class="fa-solid fa-cart-shopping text-blue-600 dark:text-indigo-400 w-4"></i> Shopping Cart
                </a>
            </div>
        </div>

        <!-- Right Side: Metrics Summary & Purchase History -->
        <div class="lg:col-span-9 space-y-6">
            
            <!-- ── Top Metrics Row ── -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                <!-- Widget 1: Total Orders Count -->
                <div class="bg-white/92 dark:bg-slate-900/82 border border-slate-200/70 dark:border-slate-800/70 rounded-[1.5rem] p-5 shadow-lg shadow-slate-900/5 flex items-center gap-4 relative overflow-hidden">
                    <div class="absolute -right-6 -bottom-6 w-16 h-16 bg-blue-600/5 rounded-full blur-md"></div>
                    <div class="w-11 h-11 bg-blue-50 dark:bg-slate-950 text-blue-600 dark:text-indigo-400 rounded-xl flex items-center justify-center text-lg border border-blue-100 dark:border-slate-850">
                        <i class="fa-solid fa-receipt"></i>
                    </div>
                    <div>
                        <span class="block text-[8px] text-slate-400 dark:text-slate-500 font-black uppercase tracking-widest">Total Purchases</span>
                        <span class="text-xl font-black text-slate-900 dark:text-white leading-none font-outfit">{{ $orders->total() }} Orders</span>
                    </div>
                </div>

                <!-- Widget 2: Total Spent Estimate -->
                @php
                    $totalSpent = 0;
                    if(isset($orders)) {
                        foreach ($orders as $ord) {
                            if($ord->order_status != 3) { // Exclude cancelled orders
                                $totalSpent += $ord->grand_total;
                            }
                        }
                    }
                @endphp
                <div class="bg-white/92 dark:bg-slate-900/82 border border-slate-200/70 dark:border-slate-800/70 rounded-[1.5rem] p-5 shadow-lg shadow-slate-900/5 flex items-center gap-4 relative overflow-hidden">
                    <div class="absolute -right-6 -bottom-6 w-16 h-16 bg-emerald-600/5 rounded-full blur-md"></div>
                    <div class="w-11 h-11 bg-emerald-50 dark:bg-slate-950 text-emerald-600 dark:text-emerald-400 rounded-xl flex items-center justify-center text-lg border border-emerald-100 dark:border-slate-850">
                        <i class="fa-solid fa-wallet"></i>
                    </div>
                    <div>
                        <span class="block text-[8px] text-slate-400 dark:text-slate-500 font-black uppercase tracking-widest">Active Ledger Sum</span>
                        <span class="text-xl font-black text-emerald-600 dark:text-emerald-400 leading-none font-mono font-outfit">Rs. {{ \App\Helpers\NumberHelper::indianFormat($totalSpent) }}</span>
                    </div>
                </div>

                <!-- Widget 3: Account Dispatch Health -->
                <div class="bg-white/92 dark:bg-slate-900/82 border border-slate-200/70 dark:border-slate-800/70 rounded-[1.5rem] p-5 shadow-lg shadow-slate-900/5 flex items-center gap-4 relative overflow-hidden">
                    <div class="absolute -right-6 -bottom-6 w-16 h-16 bg-orange-600/5 rounded-full blur-md"></div>
                    <div class="w-11 h-11 bg-orange-50 dark:bg-slate-950 text-orange-500 dark:text-orange-400 rounded-xl flex items-center justify-center text-lg border border-orange-100 dark:border-slate-850">
                        <i class="fa-solid fa-truck-fast"></i>
                    </div>
                    <div>
                        <span class="block text-[8px] text-slate-400 dark:text-slate-500 font-black uppercase tracking-widest">Courier Dispatch</span>
                        <span class="text-xl font-black text-slate-900 dark:text-white leading-none font-outfit">100% Insured</span>
                    </div>
                </div>
            </div>

            <!-- ── Purchase Ledger Section ── -->
            @if (count($orders) > 0)
                <div class="bg-white/92 dark:bg-slate-900/82 border border-slate-200/70 dark:border-slate-800/70 rounded-[1.75rem] overflow-hidden shadow-lg shadow-slate-900/5">
                    <div class="px-6 py-4 border-b border-slate-200/70 dark:border-slate-800/70 flex items-center justify-between gap-3 bg-gradient-to-r from-slate-50/80 to-white/60 dark:from-slate-950/50 dark:to-slate-900/40">
                        <div>
                            <h3 class="text-xs font-black uppercase tracking-[0.35em] text-slate-500 dark:text-slate-400">Orders Table</h3>
                            <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1">Clean invoice rows with print and inspect actions.</p>
                        </div>
                        <span class="hidden sm:inline-flex items-center px-3 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest bg-blue-500/10 text-blue-600 dark:text-indigo-400 border border-blue-500/20">
                            {{ $orders->total() }} items
                        </span>
                    </div>

                    <div class="overflow-x-auto custom-scrollbar hidden lg:block">
                        <table class="w-full min-w-[980px] text-left text-xs border-collapse">
                            <thead class="sticky top-0 z-10">
                                <tr class="bg-slate-50/95 dark:bg-slate-950/95 border-b border-slate-200/70 dark:border-slate-800 text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-[0.35em]">
                                    <th class="py-4 px-6">Invoice ID</th>
                                    <th class="py-4 px-6">Dispatch Target</th>
                                    <th class="py-4 px-6">Order Date</th>
                                    <th class="py-4 px-6 text-right">Grand Total</th>
                                    <th class="py-4 px-6 text-center">Payment Channel</th>
                                    <th class="py-4 px-6 text-center">Dispatch Status</th>
                                    <th class="py-4 px-6 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200/70 dark:divide-slate-800/70">
                                @foreach ($orders as $ord)
                                    @php
                                        $pm = 'Cash';
                                        if ($ord->payment_type == 1) $pm = 'Cheque';
                                        elseif ($ord->payment_type == 3) $pm = 'Online / UPI';
                                        elseif ($ord->payment_type == 4) $pm = 'Debit Card';
                                    @endphp
                                    <tr class="group text-slate-700 dark:text-slate-300 hover:bg-slate-50/70 dark:hover:bg-slate-950/30 transition-colors">
                                        <td class="py-5 px-6 font-mono font-black text-slate-900 dark:text-white text-sm whitespace-nowrap">
                                            #{{ $ord->morder_id }}
                                        </td>
                                        <td class="py-5 px-6 uppercase font-bold text-slate-800 dark:text-slate-200 max-w-[220px] truncate">
                                            {{ $ord->client_name }}
                                        </td>
                                        <td class="py-5 px-6 font-mono text-slate-500 dark:text-slate-400 whitespace-nowrap">
                                            {{ date('d-m-Y', strtotime($ord->order_date)) }}
                                        </td>
                                        <td class="py-5 px-6 text-right font-mono font-black text-blue-600 dark:text-indigo-400 text-sm whitespace-nowrap">
                                            Rs. {{ \App\Helpers\NumberHelper::indianFormat($ord->grand_total) }}
                                        </td>
                                        <td class="py-5 px-6 text-center">
                                            <span class="inline-flex items-center justify-center px-3 py-1.5 rounded-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 text-slate-600 dark:text-slate-400 text-[10px] uppercase font-black tracking-widest">
                                                {{ $pm }}
                                            </span>
                                        </td>
                                        <td class="py-5 px-6 text-center">
                                            @if ($ord->order_status == 1)
                                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-[10px] font-black bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-500/20 uppercase tracking-widest">
                                                    <span class="w-1.5 h-1.5 bg-amber-500 dark:bg-amber-400 rounded-full animate-pulse"></span> Processing
                                                </span>
                                            @elseif ($ord->order_status == 2)
                                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-[10px] font-black bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20 uppercase tracking-widest">
                                                    <span class="w-1.5 h-1.5 bg-emerald-500 dark:bg-emerald-400 rounded-full"></span> Delivered
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-[10px] font-black bg-rose-500/10 text-rose-600 dark:text-rose-400 border border-rose-500/20 uppercase tracking-widest">
                                                    <span class="w-1.5 h-1.5 bg-rose-500 dark:bg-rose-400 rounded-full"></span> Cancelled
                                                </span>
                                            @endif
                                        </td>
                                        <td class="py-5 px-6 text-right">
                                            <div class="inline-flex items-center gap-2">
                                                <a href="{{ route('storefront.order_details', $ord->order_id) }}" class="inline-flex items-center justify-center px-3.5 py-2 rounded-xl bg-slate-50 dark:bg-slate-950 hover:bg-slate-100 dark:hover:bg-slate-800 border border-slate-200 dark:border-slate-800 hover:border-slate-300 dark:hover:border-slate-700 text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white text-[10px] font-black uppercase tracking-widest transition-all">
                                                    Inspect
                                                </a>
                                                <a href="{{ route('storefront.order_print', $ord->order_id) }}?autoprint=1" target="_blank" class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-blue-600/10 hover:bg-blue-600 dark:bg-indigo-600/10 dark:hover:bg-indigo-600 text-blue-600 dark:text-indigo-400 hover:text-white border border-blue-500/20 dark:border-indigo-500/20 transition-all" title="Print Invoice Record">
                                                    <i class="fa-solid fa-print text-[11px]"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="lg:hidden divide-y divide-slate-200/70 dark:divide-slate-800/70">
                        @foreach ($orders as $ord)
                            @php
                                $pm = 'Cash';
                                if ($ord->payment_type == 1) $pm = 'Cheque';
                                elseif ($ord->payment_type == 3) $pm = 'Online / UPI';
                                elseif ($ord->payment_type == 4) $pm = 'Debit Card';
                            @endphp
                            <div class="p-5 space-y-4">
                                <div class="flex items-start justify-between gap-4">
                                    <div>
                                        <div class="text-[10px] font-black uppercase tracking-[0.35em] text-slate-400 dark:text-slate-500">Invoice</div>
                                        <div class="mt-1 font-mono text-lg font-black text-slate-900 dark:text-white">#{{ $ord->morder_id }}</div>
                                        <div class="mt-1 text-sm font-bold uppercase text-slate-700 dark:text-slate-200 truncate">{{ $ord->client_name }}</div>
                                    </div>
                                    <span class="inline-flex items-center px-3 py-1.5 rounded-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 text-[10px] font-black uppercase tracking-widest text-slate-600 dark:text-slate-400 whitespace-nowrap">
                                        {{ date('d M Y', strtotime($ord->order_date)) }}
                                    </span>
                                </div>

                                <div class="grid grid-cols-2 gap-3">
                                    <div class="rounded-2xl border border-slate-200/70 dark:border-slate-800 bg-slate-50/80 dark:bg-slate-950/40 p-3">
                                        <span class="block text-[9px] font-black uppercase tracking-widest text-slate-400 dark:text-slate-500">Grand Total</span>
                                        <span class="block mt-1 text-sm font-black text-blue-600 dark:text-indigo-400 font-mono">Rs. {{ \App\Helpers\NumberHelper::indianFormat($ord->grand_total) }}</span>
                                    </div>
                                    <div class="rounded-2xl border border-slate-200/70 dark:border-slate-800 bg-slate-50/80 dark:bg-slate-950/40 p-3">
                                        <span class="block text-[9px] font-black uppercase tracking-widest text-slate-400 dark:text-slate-500">Payment</span>
                                        <span class="block mt-1 text-[10px] font-black uppercase tracking-widest text-slate-700 dark:text-slate-300">{{ $pm }}</span>
                                    </div>
                                </div>

                                <div class="flex items-center justify-between gap-3">
                                    @if ($ord->order_status == 1)
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-[10px] font-black bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-500/20 uppercase tracking-widest">
                                            <span class="w-1.5 h-1.5 bg-amber-500 dark:bg-amber-400 rounded-full animate-pulse"></span> Processing
                                        </span>
                                    @elseif ($ord->order_status == 2)
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-[10px] font-black bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20 uppercase tracking-widest">
                                            <span class="w-1.5 h-1.5 bg-emerald-500 dark:bg-emerald-400 rounded-full"></span> Delivered
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-[10px] font-black bg-rose-500/10 text-rose-600 dark:text-rose-400 border border-rose-500/20 uppercase tracking-widest">
                                            <span class="w-1.5 h-1.5 bg-rose-500 dark:bg-rose-400 rounded-full"></span> Cancelled
                                        </span>
                                    @endif

                                    <div class="flex items-center gap-2">
                                        <a href="{{ route('storefront.order_details', $ord->order_id) }}" class="inline-flex items-center justify-center px-3.5 py-2 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 text-slate-600 dark:text-slate-300 text-[10px] font-black uppercase tracking-widest">
                                            Inspect
                                        </a>
                                        <a href="{{ route('storefront.order_print', $ord->order_id) }}?autoprint=1" target="_blank" class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-blue-600/10 dark:bg-indigo-600/10 text-blue-600 dark:text-indigo-400 border border-blue-500/20 dark:border-indigo-500/20" title="Print Invoice Record">
                                            <i class="fa-solid fa-print text-[11px]"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Paginate links -->
                <div class="flex justify-center mt-6">
                    <div class="sf-pagination px-5 py-3 rounded-2xl border border-slate-200 dark:border-slate-800 flex items-center gap-2 bg-white/90 dark:bg-slate-900/85 shadow-lg">
                        {{ $orders->links() }}
                    </div>
                </div>
            @else
                <!-- Empty Ledger State -->
                <div class="text-center py-20 bg-white/92 dark:bg-slate-900/82 border border-slate-200/70 dark:border-slate-800/70 rounded-[1.75rem] p-8 max-w-md mx-auto shadow-lg shadow-slate-900/5">
                    <div class="h-20 w-20 bg-slate-50 dark:bg-slate-950 rounded-2xl flex items-center justify-center text-slate-400 dark:text-slate-600 text-4xl mx-auto mb-4 border border-slate-200 dark:border-slate-850">
                        <i class="fa-solid fa-receipt"></i>
                    </div>
                    <h3 class="text-lg font-black text-slate-800 dark:text-slate-200 uppercase tracking-tight">No Transactions Registered</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-450 mt-2 leading-relaxed">
                        You have not placed any custom computing components or corporate supply transactions under this customer profile yet.
                    </p>
                    <a href="{{ route('storefront.index') }}" class="inline-block mt-6 px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-700 hover:from-blue-500 hover:to-indigo-600 text-white text-xs font-black uppercase tracking-widest rounded-xl transition-all shadow-md shadow-blue-500/10">
                        Shop Components Now
                    </a>
                </div>
            @endif
        </div>
    </div>
@endsection
