@extends('layouts.storefront')

@section('content')
    @php
        $displayName = $user->uname ?: $user->name ?: 'Customer';
        $initial = strtoupper(substr($displayName, 0, 1));
    @endphp

    <div class="mb-8">
        <div class="flex items-center gap-2 text-xs font-black text-blue-600 dark:text-indigo-400 mb-2 uppercase tracking-wider">
            <a href="{{ route('storefront.index') }}" class="hover:underline">Home</a>
            <i class="fa-solid fa-chevron-right text-[8px] text-slate-400 dark:text-slate-600"></i>
            <span class="text-slate-500 dark:text-slate-400">Customer Portal</span>
        </div>
        <h1 class="text-3xl sm:text-4xl font-black text-slate-900 dark:text-white uppercase tracking-tight">
            Welcome back, {{ $displayName }}
        </h1>
        <p class="text-sm text-slate-500 dark:text-slate-400 mt-2 max-w-3xl">
            Review your orders, check payment status, and jump back into the storefront without losing the same visual language used across the site.
        </p>
    </div>

    <div class="rounded-[2rem] border border-slate-200/70 dark:border-slate-800/70 bg-gradient-to-br from-white/90 via-white/70 to-slate-50/80 dark:from-slate-950/45 dark:via-slate-950/30 dark:to-slate-900/40 p-5 sm:p-6 lg:p-8 shadow-2xl shadow-slate-900/5 mb-8">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-stretch">
            <div class="lg:col-span-5 rounded-[1.75rem] overflow-hidden border border-slate-200/70 dark:border-slate-800/70 bg-gradient-to-br from-[#1c3fce] via-[#1631a6] to-[#0f1b3d] text-white p-6 sm:p-8 relative">
                <div class="absolute -right-16 -top-12 w-56 h-56 bg-white/10 rounded-full blur-3xl"></div>
                <div class="absolute -left-14 -bottom-14 w-48 h-48 bg-orange-500/10 rounded-full blur-3xl"></div>

                <div class="relative z-10 flex items-center gap-4 mb-6">
                    <div class="w-16 h-16 rounded-2xl bg-white text-blue-700 flex items-center justify-center font-black text-2xl shadow-xl shadow-black/10">
                        {{ $initial }}
                    </div>
                    <div>
                        <span class="block text-[10px] font-black uppercase tracking-[0.35em] text-blue-100/80">Logged In Customer</span>
                        <h2 class="text-2xl sm:text-3xl font-black uppercase tracking-tight">{{ $displayName }}</h2>
                        <p class="text-sm text-blue-100/80 mt-1">{{ $user->email }}</p>
                    </div>
                </div>

                <p class="relative z-10 text-sm sm:text-base text-blue-100/85 leading-relaxed max-w-xl">
                    Your dashboard is connected to the same storefront theme, so orders, billing, and account actions feel consistent across login, checkout, and purchase history.
                </p>

                <div class="relative z-10 flex flex-wrap gap-3 mt-6">
                    <a href="{{ route('storefront.index') }}" class="px-4 py-2.5 rounded-xl bg-white text-slate-900 text-[10px] font-black uppercase tracking-widest hover:bg-slate-100 transition-all">
                        Continue Shopping
                    </a>
                    <a href="{{ route('storefront.orders') }}" class="px-4 py-2.5 rounded-xl bg-white/10 border border-white/20 text-white text-[10px] font-black uppercase tracking-widest hover:bg-white/15 transition-all">
                        View Orders
                    </a>
                    <a href="{{ route('profile.edit') }}" class="px-4 py-2.5 rounded-xl bg-white/10 border border-white/20 text-white text-[10px] font-black uppercase tracking-widest hover:bg-white/15 transition-all">
                        Edit Profile
                    </a>
                </div>
            </div>

            <div class="lg:col-span-7 grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="glassmorphism rounded-2xl p-5 border border-slate-200/70 dark:border-slate-800/70">
                    <span class="block text-[10px] font-black uppercase tracking-widest text-slate-500 dark:text-slate-400">Total Orders</span>
                    <div class="mt-3 flex items-end justify-between gap-4">
                        <div>
                            <div class="text-3xl font-black text-slate-900 dark:text-white leading-none">{{ $orderCount }}</div>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-2">Completed and pending invoices linked to your account.</p>
                        </div>
                        <div class="w-12 h-12 rounded-2xl bg-blue-500/10 text-blue-600 dark:text-indigo-400 flex items-center justify-center">
                            <i class="fa-solid fa-receipt"></i>
                        </div>
                    </div>
                </div>

                <div class="glassmorphism rounded-2xl p-5 border border-slate-200/70 dark:border-slate-800/70">
                    <span class="block text-[10px] font-black uppercase tracking-widest text-slate-500 dark:text-slate-400">Active Orders</span>
                    <div class="mt-3 flex items-end justify-between gap-4">
                        <div>
                            <div class="text-3xl font-black text-slate-900 dark:text-white leading-none">{{ $activeOrders }}</div>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-2">Orders placed or in transit right now.</p>
                        </div>
                        <div class="w-12 h-12 rounded-2xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center">
                            <i class="fa-solid fa-truck-fast"></i>
                        </div>
                    </div>
                </div>

                <div class="glassmorphism rounded-2xl p-5 border border-slate-200/70 dark:border-slate-800/70">
                    <span class="block text-[10px] font-black uppercase tracking-widest text-slate-500 dark:text-slate-400">Total Spent</span>
                    <div class="mt-3 flex items-end justify-between gap-4">
                        <div>
                            <div class="text-2xl font-black text-blue-600 dark:text-indigo-400 leading-none font-mono">Rs. {{ \App\Helpers\NumberHelper::indianFormat($totalSpent) }}</div>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-2">Excludes cancelled orders from the active ledger.</p>
                        </div>
                        <div class="w-12 h-12 rounded-2xl bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 flex items-center justify-center">
                            <i class="fa-solid fa-wallet"></i>
                        </div>
                    </div>
                </div>

                <div class="glassmorphism rounded-2xl p-5 border border-slate-200/70 dark:border-slate-800/70">
                    <span class="block text-[10px] font-black uppercase tracking-widest text-slate-500 dark:text-slate-400">Pending Due</span>
                    <div class="mt-3 flex items-end justify-between gap-4">
                        <div>
                            <div class="text-2xl font-black text-amber-600 dark:text-amber-400 leading-none font-mono">Rs. {{ \App\Helpers\NumberHelper::indianFormat($pendingDue) }}</div>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-2">Outstanding amounts still linked to open orders.</p>
                        </div>
                        <div class="w-12 h-12 rounded-2xl bg-amber-500/10 text-amber-600 dark:text-amber-400 flex items-center justify-center">
                            <i class="fa-solid fa-circle-exclamation"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        <div class="lg:col-span-4 space-y-6">
            <div class="bg-white/92 dark:bg-slate-900/82 border border-slate-200/70 dark:border-slate-800/70 rounded-[1.75rem] p-6 shadow-lg shadow-slate-900/5">
                <h3 class="text-[10px] font-black uppercase tracking-[0.35em] text-slate-500 dark:text-slate-400 mb-4">Account Snapshot</h3>
                <div class="space-y-4 text-sm">
                    <div class="flex items-start justify-between gap-4">
                        <span class="text-slate-500 dark:text-slate-400 font-black uppercase text-[10px]">Email</span>
                        <span class="text-slate-900 dark:text-white font-semibold text-right break-all">{{ $user->email }}</span>
                    </div>
                    <div class="flex items-start justify-between gap-4">
                        <span class="text-slate-500 dark:text-slate-400 font-black uppercase text-[10px]">Contact</span>
                        <span class="text-slate-900 dark:text-white font-semibold">{{ $user->contactno ?: 'Not set' }}</span>
                    </div>
                    <div class="flex items-start justify-between gap-4">
                        <span class="text-slate-500 dark:text-slate-400 font-black uppercase text-[10px]">GSTIN</span>
                        <span class="text-slate-900 dark:text-white font-semibold">{{ $user->gsttin ?: 'Personal account' }}</span>
                    </div>
                    <div class="pt-4 border-t border-slate-200/70 dark:border-slate-800/70">
                        <a href="{{ route('profile.edit') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-slate-900 dark:bg-white text-white dark:text-slate-900 text-[10px] font-black uppercase tracking-widest hover:opacity-90 transition-all">
                            Manage Account
                        </a>
                    </div>
                </div>
            </div>

            <div class="bg-white/92 dark:bg-slate-900/82 border border-slate-200/70 dark:border-slate-800/70 rounded-[1.75rem] p-6 shadow-lg shadow-slate-900/5">
                <h3 class="text-[10px] font-black uppercase tracking-[0.35em] text-slate-500 dark:text-slate-400 mb-4">Quick Actions</h3>
                <div class="space-y-2">
                    <a href="{{ route('storefront.index') }}" class="flex items-center justify-between px-4 py-3 rounded-xl border border-slate-200/70 dark:border-slate-800 bg-slate-50/90 dark:bg-slate-950/40 text-slate-700 dark:text-slate-300 hover:border-blue-500/20 hover:text-slate-900 dark:hover:text-white transition-all">
                        <span class="flex items-center gap-3 text-xs font-black uppercase tracking-widest"><i class="fa-solid fa-store text-blue-600 dark:text-indigo-400 w-4"></i> Continue Shopping</span>
                        <i class="fa-solid fa-arrow-right text-[10px]"></i>
                    </a>
                    <a href="{{ route('storefront.orders') }}" class="flex items-center justify-between px-4 py-3 rounded-xl border border-slate-200/70 dark:border-slate-800 bg-slate-50/90 dark:bg-slate-950/40 text-slate-700 dark:text-slate-300 hover:border-blue-500/20 hover:text-slate-900 dark:hover:text-white transition-all">
                        <span class="flex items-center gap-3 text-xs font-black uppercase tracking-widest"><i class="fa-solid fa-receipt text-blue-600 dark:text-indigo-400 w-4"></i> Purchase History</span>
                        <i class="fa-solid fa-arrow-right text-[10px]"></i>
                    </a>
                    <a href="{{ route('storefront.cart') }}" class="flex items-center justify-between px-4 py-3 rounded-xl border border-slate-200/70 dark:border-slate-800 bg-slate-50/90 dark:bg-slate-950/40 text-slate-700 dark:text-slate-300 hover:border-blue-500/20 hover:text-slate-900 dark:hover:text-white transition-all">
                        <span class="flex items-center gap-3 text-xs font-black uppercase tracking-widest"><i class="fa-solid fa-cart-shopping text-blue-600 dark:text-indigo-400 w-4"></i> Cart Review</span>
                        <i class="fa-solid fa-arrow-right text-[10px]"></i>
                    </a>
                </div>
            </div>
        </div>

        <div class="lg:col-span-8">
            <div class="bg-white/92 dark:bg-slate-900/82 border border-slate-200/70 dark:border-slate-800/70 rounded-[1.75rem] overflow-hidden shadow-lg shadow-slate-900/5">
                <div class="px-6 py-4 border-b border-slate-200/70 dark:border-slate-800/70 flex items-center justify-between gap-4">
                    <div>
                        <h3 class="text-sm font-black uppercase tracking-[0.35em] text-slate-500 dark:text-slate-400">Recent Orders</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">The last five invoices tied to your customer profile.</p>
                    </div>
                    <a href="{{ route('storefront.orders') }}" class="text-[10px] font-black uppercase tracking-widest text-blue-600 dark:text-indigo-400 hover:underline">
                        View All
                    </a>
                </div>

                @if ($recentOrders->count())
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs">
                            <thead class="bg-slate-50/90 dark:bg-slate-950/50 text-[10px] uppercase tracking-widest text-slate-500 dark:text-slate-400">
                                <tr>
                                    <th class="px-6 py-4">Invoice</th>
                                    <th class="px-6 py-4">Date</th>
                                    <th class="px-6 py-4 text-right">Total</th>
                                    <th class="px-6 py-4 text-center">Status</th>
                                    <th class="px-6 py-4 text-right">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200/70 dark:divide-slate-800/70">
                                @foreach ($recentOrders as $ord)
                                    <tr class="hover:bg-slate-50/70 dark:hover:bg-slate-950/30 transition-colors">
                                        <td class="px-6 py-4 font-mono font-black text-slate-900 dark:text-white">#{{ $ord->morder_id }}</td>
                                        <td class="px-6 py-4 text-slate-500 dark:text-slate-400 font-mono">{{ date('d M Y', strtotime($ord->order_date)) }}</td>
                                        <td class="px-6 py-4 text-right font-mono font-black text-blue-600 dark:text-indigo-400">Rs. {{ \App\Helpers\NumberHelper::indianFormat($ord->grand_total) }}</td>
                                        <td class="px-6 py-4 text-center">
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-widest border {{ $ord->order_status == 2 ? 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border-emerald-500/20' : ($ord->order_status == 3 ? 'bg-rose-500/10 text-rose-600 dark:text-rose-400 border-rose-500/20' : 'bg-amber-500/10 text-amber-600 dark:text-amber-400 border-amber-500/20') }}">
                                                {{ $ord->order_status == 2 ? 'Delivered' : ($ord->order_status == 3 ? 'Cancelled' : 'Processing') }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-right space-x-2">
                                            <a href="{{ route('storefront.order_details', $ord->order_id) }}" class="inline-flex items-center px-3 py-1.5 rounded-lg border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950/40 text-[10px] font-black uppercase tracking-widest text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white hover:border-slate-300 dark:hover:border-slate-700 transition-all">
                                                Inspect
                                            </a>
                                            <a href="{{ route('storefront.order_print', $ord->order_id) }}?autoprint=1" target="_blank" class="inline-flex items-center px-3 py-1.5 rounded-lg border border-blue-500/20 bg-blue-500/10 text-[10px] font-black uppercase tracking-widest text-blue-600 dark:text-indigo-400 hover:bg-blue-600 hover:text-white transition-all">
                                                <i class="fa-solid fa-print"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="p-8 text-center">
                        <div class="w-16 h-16 mx-auto mb-4 rounded-2xl bg-slate-50 dark:bg-slate-950/40 border border-slate-200 dark:border-slate-800 flex items-center justify-center text-slate-400 dark:text-slate-600 text-2xl">
                            <i class="fa-solid fa-receipt"></i>
                        </div>
                        <h4 class="text-base font-black uppercase tracking-tight text-slate-900 dark:text-white">No orders yet</h4>
                        <p class="text-sm text-slate-500 dark:text-slate-400 mt-2">Your first order will appear here once checkout is complete.</p>
                        <a href="{{ route('storefront.index') }}" class="inline-flex mt-5 px-5 py-3 rounded-xl bg-gradient-to-r from-indigo-600 to-purple-600 text-white text-[10px] font-black uppercase tracking-widest shadow-lg shadow-indigo-600/15">
                            Start Shopping
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
