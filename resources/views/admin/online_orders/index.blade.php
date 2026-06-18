@extends('layouts.admin', ['title' => $title])

@section('content')
<div class="space-y-6" x-data="{ 
    paymentModalOpen: false, 
    statusModalOpen: false,
    activeOrderId: null, 
    activeOrderBill: '', 
    activeOrderDue: 0, 
    activeOrderStatus: '',
    paymentAmount: '',
    paymentType: '',
    paymentStatus: ''
}">
    <x-admin.header :title="$title" :description="$description">
        <!-- Optional action buttons -->
    </x-admin.header>

    <!-- Tabs Navigation -->
    <div class="flex flex-wrap gap-2 border-b border-slate-200 dark:border-slate-800/80 pb-4">
        <a href="{{ route('admin.online_orders.index', ['status' => 'all']) }}" 
           class="px-4 py-2 text-xs font-bold uppercase tracking-wider rounded-xl transition-all {{ $status === 'all' ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/10' : 'bg-slate-100 dark:bg-slate-900/60 text-slate-600 dark:text-slate-400 hover:bg-slate-200/60 dark:hover:bg-slate-800/60' }}">
            All Orders
        </a>
        <a href="{{ route('admin.online_orders.index', ['status' => 'pending']) }}" 
           class="px-4 py-2 text-xs font-bold uppercase tracking-wider rounded-xl transition-all {{ $status === 'pending' ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/10' : 'bg-slate-100 dark:bg-slate-900/60 text-slate-600 dark:text-slate-400 hover:bg-slate-200/60 dark:hover:bg-slate-800/60' }}">
            Pending / Processing
        </a>
        <a href="{{ route('admin.online_orders.index', ['status' => 'sending']) }}" 
           class="px-4 py-2 text-xs font-bold uppercase tracking-wider rounded-xl transition-all {{ $status === 'sending' ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/10' : 'bg-slate-100 dark:bg-slate-900/60 text-slate-600 dark:text-slate-400 hover:bg-slate-200/60 dark:hover:bg-slate-800/60' }}">
            Sending / Transit
        </a>
        <a href="{{ route('admin.online_orders.index', ['status' => 'delivered']) }}" 
           class="px-4 py-2 text-xs font-bold uppercase tracking-wider rounded-xl transition-all {{ $status === 'delivered' ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/10' : 'bg-slate-100 dark:bg-slate-900/60 text-slate-600 dark:text-slate-400 hover:bg-slate-200/60 dark:hover:bg-slate-800/60' }}">
            Delivered
        </a>
        <a href="{{ route('admin.online_orders.index', ['status' => 'cancelled']) }}" 
           class="px-4 py-2 text-xs font-bold uppercase tracking-wider rounded-xl transition-all {{ $status === 'cancelled' ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/10' : 'bg-slate-100 dark:bg-slate-900/60 text-slate-600 dark:text-slate-400 hover:bg-slate-200/60 dark:hover:bg-slate-800/60' }}">
            Cancelled
        </a>
    </div>

    <!-- Search & Filter Bar -->
    <x-admin.search-bar :action="route('admin.online_orders.index')" placeholder="Search by Order ID, username, customer name or contact...">
        <input type="hidden" name="status" value="{{ $status }}">
        <x-slot:info>
            <span>Showing {{ $orders->firstItem() ?? 0 }}-{{ $orders->lastItem() ?? 0 }} of {{ $orders->total() }} recorded transactions</span>
        </x-slot:info>
    </x-admin.search-bar>

    <!-- Online Orders Table -->
    @php
    $tableHeaders = [
        ['label' => 'Order Specs'],
        ['label' => 'Customer & Delivery'],
        ['label' => 'Charges & Discount', 'align' => 'right'],
        ['label' => 'Payment & Balance', 'align' => 'right'],
        ['label' => 'Status Tags', 'align' => 'center'],
        ['label' => 'Actions', 'align' => 'center']
    ];
    @endphp

    <x-admin.table :headers="$tableHeaders" :collection="$orders" type="glass" minWidth="1000px">
        @forelse ($orders as $order)
            <tr class="hover:bg-slate-100 dark:hover:bg-slate-800/20 transition-all block lg:table-row w-full bg-white dark:bg-slate-900/30 border border-slate-200 dark:border-slate-800/60 rounded-2xl p-4 lg:p-0 mb-4 lg:mb-0 relative grid grid-cols-2 gap-x-4 gap-y-3 lg:grid-cols-none lg:bg-transparent lg:border-0 lg:hover:bg-slate-900/20 lg:transition-all">
                
                <!-- Order ID & Date -->
                <td class="col-span-2 bg-indigo-500/10 text-indigo-400 px-3 py-2 rounded-lg text-xs font-mono font-bold flex items-center justify-between lg:table-cell lg:col-span-none lg:bg-transparent lg:px-6 lg:py-4">
                    <span class="lg:hidden uppercase tracking-wider text-[10px] font-bold text-indigo-400">Order Specs</span>
                    <div class="leading-relaxed">
                        <span class="text-sm font-black text-slate-900 dark:text-white block">#{{ str_pad($order->orderid, 5, '0', STR_PAD_LEFT) }}</span>
                        <span class="text-[10px] text-slate-500 dark:text-slate-400 font-mono block mt-0.5">
                            {{ $order->orderdate ? date('d-m-Y h:i A', strtotime($order->orderdate)) : 'N/A' }}
                        </span>
                    </div>
                </td>

                <!-- Customer Details & Shipping Address -->
                <td class="py-2 lg:px-6 lg:py-4 col-span-2 block lg:table-cell lg:col-span-none">
                    <span class="block lg:hidden text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Customer & Delivery</span>
                    <div class="leading-normal">
                        <span class="font-bold text-slate-800 dark:text-slate-250 block text-sm">{{ $order->user->uname ?? $order->username }}</span>
                        <span class="text-[11px] text-slate-500 dark:text-slate-450 block font-mono mt-0.5">
                            MOB: {{ $order->user->contactno ?? 'N/A' }} @if($order->user && $order->user->email) &bull; {{ $order->user->email }}@endif
                        </span>
                        <div class="text-[11px] text-slate-500 dark:text-slate-500 mt-1 max-w-xs truncate" title="{{ $order->user->shippingaddress ?? 'N/A' }}">
                            {{ $order->user ? ($order->user->shippingaddress . ', ' . $order->user->shippingcity . ', ' . $order->user->shippingstate . ' - ' . $order->user->shippingpincode) : 'N/A' }}
                        </div>
                    </div>
                </td>

                <!-- Charges & Discounts -->
                <td class="py-2 lg:px-6 lg:py-4 col-span-1 block lg:table-cell lg:col-span-none lg:text-right text-xs">
                    <span class="block lg:hidden text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Charges & Discount</span>
                    <div class="leading-relaxed font-medium">
                        <div class="text-slate-600 dark:text-slate-400">Ship: Rs. {{ number_format($order->tship, 2) }} | Inst: Rs. {{ number_format($order->install, 2) }}</div>
                        <div class="text-indigo-600 dark:text-indigo-400 mt-0.5">Coins: -Rs. {{ number_format($order->pcoin, 2) }}</div>
                        <div class="text-amber-600 dark:text-amber-500 mt-0.5 font-semibold">Disc: -Rs. {{ number_format($order->discount, 2) }}</div>
                    </div>
                </td>

                <!-- Payment Breakdown (Total, Paid, Due) -->
                <td class="py-2 lg:px-6 lg:py-4 col-span-1 block lg:table-cell lg:col-span-none lg:text-right font-semibold whitespace-nowrap">
                    <span class="block lg:hidden text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Payment & Balance</span>
                    <div class="leading-normal">
                        <div class="text-slate-800 dark:text-slate-200 font-bold text-sm">Total: Rs. {{ number_format($order->gamount, 2) }}</div>
                        <div class="text-emerald-600 dark:text-emerald-400 text-xs mt-0.5">Paid: Rs. {{ number_format($order->pamount, 2) }}</div>
                        <div class="mt-1">
                            @if ($order->bamount > 0)
                                <span class="text-rose-600 bg-rose-500/10 dark:text-rose-400 dark:bg-rose-500/5 px-2.5 py-0.5 rounded-lg border border-rose-500/20 dark:border-rose-500/10 text-[11px] font-bold inline-block whitespace-nowrap">
                                    Due: Rs. {{ number_format($order->bamount, 2) }}
                                </span>
                            @else
                                <span class="text-slate-500 text-xs">Due: Rs. 0.00</span>
                            @endif
                        </div>
                    </div>
                </td>

                <!-- Order Status & Payment Status -->
                <td class="py-2 lg:px-6 lg:py-4 col-span-1 block lg:table-cell lg:col-span-none lg:text-center whitespace-nowrap text-xs font-bold">
                    <span class="block lg:hidden text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Status Tags</span>
                    <div class="flex flex-col gap-1.5 items-center justify-center">
                        @if ($order->ostatus == 'd')
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20 text-[10px]">
                                <i class="fa-solid fa-truck-flat"></i>
                                <span>Delivered</span>
                            </span>
                        @elseif ($order->ostatus == 's')
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-sky-500/10 text-sky-600 dark:text-sky-400 border border-sky-500/20 text-[10px]">
                                <i class="fa-solid fa-paper-plane animate-pulse"></i>
                                <span>Sending</span>
                            </span>
                        @elseif ($order->ostatus == 'c')
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-rose-500/10 text-rose-600 dark:text-rose-400 border border-rose-500/20 text-[10px]">
                                <i class="fa-solid fa-ban"></i>
                                <span>Cancelled</span>
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-500/20 animate-pulse text-[10px]">
                                <i class="fa-solid fa-spinner fa-spin"></i>
                                <span>Processing</span>
                            </span>
                        @endif

                        @if ($order->bamount <= 0)
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20">
                                <i class="fa-solid fa-circle-check"></i>
                                <span>Full Paid</span>
                            </span>
                        @elseif ($order->pamount > 0)
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-500/20 animate-pulse">
                                <i class="fa-solid fa-clock"></i>
                                <span>Part Paid</span>
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-rose-500/10 text-rose-600 dark:text-rose-400 border border-rose-500/20">
                                <i class="fa-solid fa-triangle-exclamation"></i>
                                <span>No Paid</span>
                            </span>
                        @endif
                    </div>
                </td>

                <!-- Actions -->
                <td class="py-3 border-t border-slate-800/40 mt-2 lg:border-t-0 lg:mt-0 col-span-2 block lg:table-cell lg:col-span-none lg:px-6 lg:py-4 lg:text-center whitespace-nowrap">
                    <span class="block lg:hidden text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-2">Actions</span>
                    <div class="inline-flex items-center gap-2">
                        <!-- Edit -->
                        <a href="{{ route('admin.online_orders.edit', $order->orderid) }}" 
                           class="p-2 text-slate-400 hover:text-indigo-400 hover:bg-indigo-500/10 rounded-xl transition-all" 
                           title="Edit Order / Items">
                           <i class="fa-solid fa-pen-to-square text-base"></i>
                        </a>

                        <!-- Record Payment -->
                        @if ($order->bamount > 0)
                            <button type="button" 
                                    @click="
                                        activeOrderId = {{ $order->orderid }};
                                        activeOrderBill = '#{{ str_pad($order->orderid, 5, '0', STR_PAD_LEFT) }}';
                                        activeOrderDue = {{ $order->bamount }};
                                        paymentAmount = {{ $order->bamount }};
                                        paymentModalOpen = true;
                                    "
                                    class="p-2 text-slate-400 hover:text-emerald-400 hover:bg-emerald-500/10 rounded-xl transition-all" 
                                    title="Record Payment">
                                <i class="fa-solid fa-indian-rupee-sign text-base"></i>
                            </button>
                        @endif

                        <!-- Change Transit Status -->
                        <button type="button"
                                @click="
                                    activeOrderId = {{ $order->orderid }};
                                    activeOrderStatus = '{{ $order->ostatus ?: 'p' }}';
                                    statusModalOpen = true;
                                "
                                class="p-2 text-slate-400 hover:text-amber-400 hover:bg-amber-500/10 rounded-xl transition-all"
                                title="Change Order Transit Status">
                            <i class="fa-solid fa-truck text-base"></i>
                        </button>

                        <!-- Print Tax Invoice -->
                        <a href="{{ route('admin.online_orders.print', $order->orderid) }}" 
                           target="_blank"
                           class="p-2 text-slate-400 hover:text-sky-400 hover:bg-sky-500/10 rounded-xl transition-all" 
                           title="Print Tax Invoice / PDF">
                            <i class="fa-solid fa-print text-base"></i>
                        </a>
                    </div>
                </td>
            </tr>
        @empty
            <tr class="block lg:table-row w-full bg-white dark:bg-slate-900/30 border border-slate-200 dark:border-slate-800/60 rounded-2xl p-6 lg:p-0">
                <td colspan="13" class="px-6 py-12 text-center text-slate-500 font-medium block lg:table-cell w-full">
                    <div class="flex flex-col items-center justify-center gap-3">
                        <i class="fa-solid fa-globe text-4xl text-slate-650 animate-pulse"></i>
                        <span>No online orders found in this category.</span>
                    </div>
                </td>
            </tr>
        @endforelse
    </x-admin.table>

    <!-- Modal 1: Record Balance Payment -->
    <x-admin.modal id="paymentModalOpen" title="Post Balance Payment">
        <form :action="'/admin/online-orders/' + activeOrderId + '/payment'" method="POST" class="mt-6 space-y-4">
            @csrf
            <div>
                <span class="block text-xs font-semibold text-slate-500 uppercase tracking-wider">Online Order Info</span>
                <span class="block text-lg font-bold text-slate-200 mt-1" x-text="'Order Ref: ' + activeOrderBill"></span>
                <span class="block text-sm text-slate-400 mt-0.5" x-text="'Outstanding Balance: Rs. ' + activeOrderDue.toFixed(2)"></span>
            </div>

            <div class="space-y-1.5">
                <label for="paymentAmount" class="block text-xs font-bold text-slate-400 uppercase tracking-wider">Payment Amount (Rs.)</label>
                <input type="number" 
                       id="paymentAmount" 
                       name="paymentAmount" 
                       x-model="paymentAmount" 
                       :max="activeOrderDue" 
                       step="0.01" 
                       required 
                       class="w-full px-4 py-3 bg-slate-950 border border-slate-800 rounded-xl text-slate-200 focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 font-bold text-lg">
            </div>

            <div class="space-y-1.5">
                <label for="paymentType" class="block text-xs font-bold text-slate-400 uppercase tracking-wider">Payment Type / Mode</label>
                <select id="paymentType" 
                        name="paymentType" 
                        required 
                        class="w-full px-4 py-3 bg-slate-950 border border-slate-800 rounded-xl text-slate-200 focus:outline-none focus:border-indigo-500 text-sm font-semibold">
                    <option value="h">Cash</option>
                    <option value="q">Cheque</option>
                    <option value="C">Credit Card</option>
                    <option value="D">Debit Card</option>
                    <option value="I">Internet Banking / UPI</option>
                </select>
            </div>

            <div class="space-y-1.5">
                <label for="paymentStatus" class="block text-xs font-bold text-slate-400 uppercase tracking-wider">Payment Status</label>
                <select id="paymentStatus" 
                        name="paymentStatus" 
                        required 
                        class="w-full px-4 py-3 bg-slate-950 border border-slate-800 rounded-xl text-slate-200 focus:outline-none focus:border-indigo-500 text-sm font-semibold">
                    <option value="1">Full Payment</option>
                    <option value="2">Advance Payment</option>
                    <option value="3">No Payment</option>
                </select>
            </div>

            <div class="pt-4 flex items-center justify-end gap-3">
                <button type="button" @click="paymentModalOpen = false" class="px-4 py-2 text-sm font-semibold text-slate-400 hover:text-slate-200">
                    Cancel
                </button>
                <button type="submit" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-xl text-sm shadow-lg shadow-emerald-600/10 active:scale-95 transition-all">
                    Post Payment Record
                </button>
            </div>
        </form>
    </x-admin.modal>

    <!-- Modal 2: Change Order Transit Status -->
    <x-admin.modal id="statusModalOpen" title="Update Transit / Order Status">
        <form :action="'/admin/online-orders/' + activeOrderId + '/status'" method="POST" class="mt-6 space-y-4">
            @csrf
            <div>
                <span class="block text-xs font-semibold text-slate-500 uppercase tracking-wider">Select New Status</span>
            </div>

            <div class="space-y-1.5">
                <label for="ostatus" class="block text-xs font-bold text-slate-400 uppercase tracking-wider">Status</label>
                <select id="ostatus" 
                        name="ostatus" 
                        x-model="activeOrderStatus"
                        required 
                        class="w-full px-4 py-3 bg-slate-950 border border-slate-800 rounded-xl text-slate-200 focus:outline-none focus:border-amber-500 text-sm font-semibold uppercase">
                    <option value="p">Processing / Pending</option>
                    <option value="s">Sending / Transit</option>
                    <option value="d">Delivered</option>
                    <option value="c">Cancel / Returned</option>
                </select>
            </div>

            <div class="pt-4 flex items-center justify-end gap-3">
                <button type="button" @click="statusModalOpen = false" class="px-4 py-2 text-sm font-semibold text-slate-400 hover:text-slate-200">
                    Cancel
                </button>
                <button type="submit" class="px-5 py-2.5 bg-amber-500 hover:bg-amber-600 text-white font-semibold rounded-xl text-sm shadow-lg active:scale-95 transition-all">
                    Update Order Status
                </button>
            </div>
        </form>
    </x-admin.modal>
</div>
@endsection
