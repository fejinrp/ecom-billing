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
        ['label' => 'Order ID'],
        ['label' => 'Date & Time'],
        ['label' => 'Customer'],
        ['label' => 'Contact'],
        ['label' => 'Shipping Address'],
        ['label' => 'Charges (S/I)', 'align' => 'right'],
        ['label' => 'Redeem/Dis', 'align' => 'right'],
        ['label' => 'Grand Total', 'align' => 'right'],
        ['label' => 'Paid', 'align' => 'right'],
        ['label' => 'Outstanding Due', 'align' => 'right'],
        ['label' => 'Order Status', 'align' => 'center'],
        ['label' => 'Payment Status', 'align' => 'center'],
        ['label' => 'Actions', 'align' => 'center']
    ];
    @endphp

    <x-admin.table :headers="$tableHeaders" :collection="$orders" type="glass" minWidth="1200px">
        @forelse ($orders as $order)
            <tr class="hover:bg-slate-100 dark:hover:bg-slate-800/20 transition-all block lg:table-row w-full bg-white dark:bg-slate-900/30 border border-slate-200 dark:border-slate-800/60 rounded-2xl p-4 lg:p-0 mb-4 lg:mb-0 relative grid grid-cols-2 gap-x-4 gap-y-3 lg:grid-cols-none lg:bg-transparent lg:border-0 lg:hover:bg-slate-900/20 lg:transition-all">
                
                <!-- Order ID Badge -->
                <td class="col-span-2 bg-indigo-500/10 text-indigo-400 px-3 py-2 rounded-lg text-xs font-mono font-bold flex items-center justify-between lg:table-cell lg:col-span-none lg:bg-transparent lg:px-6 lg:py-4">
                    <span class="lg:hidden uppercase tracking-wider text-[10px] font-bold text-indigo-400">Order ID</span>
                    <span>#{{ str_pad($order->orderid, 5, '0', STR_PAD_LEFT) }}</span>
                </td>

                <!-- Date & Time -->
                <td class="py-2 lg:px-6 lg:py-4 col-span-1 block lg:table-cell lg:col-span-none">
                    <span class="block lg:hidden text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Date</span>
                    <span class="font-medium whitespace-nowrap text-xs text-slate-300">
                        {{ $order->orderdate ? date('d-m-Y h:i A', strtotime($order->orderdate)) : 'N/A' }}
                    </span>
                </td>

                <!-- Customer -->
                <td class="py-2 lg:px-6 lg:py-4 col-span-2 block lg:table-cell lg:col-span-none">
                    <span class="block lg:hidden text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Customer</span>
                    <div class="leading-none">
                        <span class="font-semibold text-slate-200 block text-sm">{{ $order->user->uname ?? $order->username }}</span>
                        @if($order->user && $order->user->email)
                            <span class="text-[10px] text-slate-500 truncate block mt-0.5 max-w-[120px]">{{ $order->user->email }}</span>
                        @endif
                    </div>
                </td>

                <!-- Contact -->
                <td class="py-2 lg:px-6 lg:py-4 col-span-1 block lg:table-cell lg:col-span-none text-xs font-semibold text-slate-400">
                    <span class="block lg:hidden text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Contact</span>
                    <span>{{ $order->user->contactno ?? 'N/A' }}</span>
                </td>

                <!-- Shipping Address -->
                <td class="py-2 lg:px-6 lg:py-4 col-span-2 block lg:table-cell lg:col-span-none text-xs text-slate-400 max-w-[180px]">
                    <span class="block lg:hidden text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Shipping Address</span>
                    <div class="line-clamp-2" title="{{ $order->user->shippingaddress ?? 'N/A' }}">
                        {{ $order->user ? ($order->user->shippingaddress . ', ' . $order->user->shippingcity . ', ' . $order->user->shippingstate . ' - ' . $order->user->shippingpincode) : 'N/A' }}
                    </div>
                </td>

                <!-- Charges (Ship/Inst) -->
                <td class="py-2 lg:px-6 lg:py-4 col-span-1 block lg:table-cell lg:col-span-none lg:text-right text-xs">
                    <span class="block lg:hidden text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Charges (S/I)</span>
                    <div class="leading-none font-medium text-slate-300">
                        <span class="block">Ship: Rs. {{ number_format($order->tship, 2) }}</span>
                        <span class="block mt-1 text-slate-500">Inst: Rs. {{ number_format($order->install, 2) }}</span>
                    </div>
                </td>

                <!-- Redeem/Discount -->
                <td class="py-2 lg:px-6 lg:py-4 col-span-1 block lg:table-cell lg:col-span-none lg:text-right text-xs">
                    <span class="block lg:hidden text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Redeem/Discount</span>
                    <div class="leading-none font-medium">
                        <span class="block text-indigo-400">Coin: -Rs. {{ number_format($order->pcoin, 2) }}</span>
                        <span class="block mt-1 text-amber-500">Disc: -Rs. {{ number_format($order->discount, 2) }}</span>
                    </div>
                </td>

                <!-- Grand Total -->
                <td class="py-2 lg:px-6 lg:py-4 col-span-1 block lg:table-cell lg:col-span-none lg:text-right font-bold text-slate-200">
                    <span class="block lg:hidden text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Grand Total</span>
                    <span>Rs. {{ number_format($order->gamount, 2) }}</span>
                </td>

                <!-- Paid -->
                <td class="py-2 lg:px-6 lg:py-4 col-span-1 block lg:table-cell lg:col-span-none lg:text-right font-bold text-emerald-400">
                    <span class="block lg:hidden text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Paid</span>
                    <span>Rs. {{ number_format($order->pamount, 2) }}</span>
                </td>

                <!-- Outstanding Due -->
                <td class="py-2 lg:px-6 lg:py-4 col-span-1 block lg:table-cell lg:col-span-none lg:text-right font-bold">
                    <span class="block lg:hidden text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Outstanding Due</span>
                    <span>
                        @if ($order->bamount > 0)
                            <span class="text-rose-400 bg-rose-500/5 px-2.5 py-1 rounded-lg border border-rose-500/10">
                                Rs. {{ number_format($order->bamount, 2) }}
                            </span>
                        @else
                            <span class="text-slate-500 font-normal">Rs. 0.00</span>
                        @endif
                    </span>
                </td>

                <!-- Order Status -->
                <td class="py-2 lg:px-6 lg:py-4 col-span-1 block lg:table-cell lg:col-span-none lg:text-center whitespace-nowrap text-xs font-bold">
                    <span class="block lg:hidden text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Order Status</span>
                    <span>
                        @if ($order->ostatus == 'd')
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                                <i class="fa-solid fa-truck-flat"></i>
                                <span>Delivered</span>
                            </span>
                        @elseif ($order->ostatus == 's')
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-sky-500/10 text-sky-400 border border-sky-500/20">
                                <i class="fa-solid fa-paper-plane animate-pulse"></i>
                                <span>Sending</span>
                            </span>
                        @elseif ($order->ostatus == 'c')
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-rose-500/10 text-rose-400 border border-rose-500/20">
                                <i class="fa-solid fa-ban"></i>
                                <span>Cancelled</span>
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-amber-500/10 text-amber-400 border border-amber-500/20 animate-pulse">
                                <i class="fa-solid fa-spinner fa-spin"></i>
                                <span>Processing</span>
                            </span>
                        @endif
                    </span>
                </td>

                <!-- Payment Status -->
                <td class="py-2 lg:px-6 lg:py-4 col-span-1 block lg:table-cell lg:col-span-none lg:text-center whitespace-nowrap">
                    <span class="block lg:hidden text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Payment Status</span>
                    <span>
                        @if ($order->bamount <= 0)
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                                <i class="fa-solid fa-circle-check text-[10px]"></i>
                                <span>Full Paid</span>
                            </span>
                        @elseif ($order->pamount > 0)
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-amber-500/10 text-amber-400 border border-amber-500/20 animate-pulse">
                                <i class="fa-solid fa-clock text-[10px]"></i>
                                <span>Part Paid</span>
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-rose-500/10 text-rose-400 border border-rose-500/20">
                                <i class="fa-solid fa-triangle-exclamation text-[10px]"></i>
                                <span>No Paid</span>
                            </span>
                        @endif
                    </span>
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
