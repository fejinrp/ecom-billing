@extends('layouts.admin', ['title' => $title])

@section('content')
<div class="space-y-6" x-data="{ 
    paymentModalOpen: false, 
    activeSaleId: null, 
    activeSaleBill: '', 
    activeSaleDue: 0, 
    paymentAmount: '',
    cancelModalOpen: false,
    cancelSaleId: null,
    cancelSaleBill: ''
}">
    <x-admin.header :title="$title" :description="$description">
        <x-slot:action>
            <x-admin.button href="{{ route('admin.sales.create') }}" icon="fa-solid fa-plus text-xs">
                Record New Sale
            </x-admin.button>
        </x-slot:action>
    </x-admin.header>

    <!-- Search & Filter Bar -->
    <x-admin.search-bar :action="route('admin.sales.index')" placeholder="Search by Bill #, Customer name or mobile...">
        <x-slot:info>
            <span>Showing {{ $sales->firstItem() ?? 0 }}-{{ $sales->lastItem() ?? 0 }} of {{ $sales->total() }} recorded transactions</span>
        </x-slot:info>
    </x-admin.search-bar>

    <!-- Responsive Sales Table -->
    @php
    $tableHeaders = [
        ['label' => 'Bill #'],
        ['label' => 'Date'],
        ['label' => 'Customer'],
        ['label' => 'Contact'],
        ['label' => 'Grand Total', 'align' => 'right'],
        ['label' => 'Paid', 'align' => 'right'],
        ['label' => 'Due Balance', 'align' => 'right'],
        ['label' => 'Payment Status', 'align' => 'center'],
        ['label' => 'Actions', 'align' => 'center']
    ];
    @endphp

    <x-admin.table :headers="$tableHeaders" :collection="$sales" type="glass" minWidth="900px">
        @forelse ($sales as $sale)
            <tr class="hover:bg-slate-900/20 transition-all block lg:table-row w-full bg-slate-900/30 border border-slate-800/60 rounded-2xl p-4 lg:p-0 mb-4 lg:mb-0 relative grid grid-cols-2 gap-x-4 gap-y-3 lg:grid-cols-none lg:bg-transparent lg:border-0 lg:hover:bg-slate-900/20 lg:transition-all">
                
                <!-- Bill # Badge / Mobile Card Header -->
                <td class="col-span-2 bg-indigo-500/10 text-indigo-400 px-3 py-2 rounded-lg text-xs font-mono font-bold flex items-center justify-between lg:table-cell lg:col-span-none lg:bg-transparent lg:px-6 lg:py-4 lg:text-indigo-400">
                    <span class="lg:hidden uppercase tracking-wider text-[10px] font-bold text-indigo-400">Bill #</span>
                    <span>#{{ str_pad($sale->morder_id, 4, '0', STR_PAD_LEFT) }}</span>
                </td>

                <!-- Date -->
                <td class="py-2 lg:px-6 lg:py-4 col-span-1 block lg:table-cell lg:col-span-none">
                    <span class="block lg:hidden text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Date</span>
                    <span class="font-medium whitespace-nowrap">{{ date('d-m-Y', strtotime($sale->order_date)) }}</span>
                </td>

                <!-- Customer Name -->
                <td class="py-2 lg:px-6 lg:py-4 col-span-2 block lg:table-cell lg:col-span-none">
                    <span class="block lg:hidden text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Customer</span>
                    <span class="font-semibold text-slate-200">{{ $sale->client_name }}</span>
                </td>

                <!-- Contact -->
                <td class="py-2 lg:px-6 lg:py-4 col-span-1 block lg:table-cell lg:col-span-none text-xs font-medium text-slate-400">
                    <span class="block lg:hidden text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Contact</span>
                    <span>{{ $sale->mobile ?: 'N/A' }}</span>
                </td>

                <!-- Grand Total -->
                <td class="py-2 lg:px-6 lg:py-4 col-span-1 block lg:table-cell lg:col-span-none lg:text-right font-semibold text-slate-200">
                    <span class="block lg:hidden text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Grand Total</span>
                    <span>Rs. {{ number_format($sale->grand_total, 2) }}</span>
                </td>

                <!-- Paid -->
                <td class="py-2 lg:px-6 lg:py-4 col-span-1 block lg:table-cell lg:col-span-none lg:text-right font-semibold text-emerald-400">
                    <span class="block lg:hidden text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Paid</span>
                    <span>Rs. {{ number_format($sale->paid, 2) }}</span>
                </td>

                <!-- Due Balance -->
                <td class="py-2 lg:px-6 lg:py-4 col-span-1 block lg:table-cell lg:col-span-none lg:text-right font-semibold">
                    <span class="block lg:hidden text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Due Balance</span>
                    <span>
                        @if ($sale->due > 0)
                            <span class="text-rose-400 bg-rose-500/5 px-2 py-1 rounded-lg border border-rose-500/10 font-bold">
                                Rs. {{ number_format($sale->due, 2) }}
                            </span>
                        @else
                            <span class="text-slate-500">Rs. 0.00</span>
                        @endif
                    </span>
                </td>

                <!-- Payment Status -->
                <td class="py-2 lg:px-6 lg:py-4 col-span-1 block lg:table-cell lg:col-span-none lg:text-center whitespace-nowrap">
                    <span class="block lg:hidden text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Payment Status</span>
                    <span>
                        @if ($sale->due <= 0)
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                                <i class="fa-solid fa-circle-check text-[10px]"></i>
                                <span>Full Paid</span>
                            </span>
                        @elseif ($sale->paid > 0)
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-amber-500/10 text-amber-400 border border-amber-500/20 animate-pulse">
                                <i class="fa-solid fa-clock text-[10px]"></i>
                                <span>Pending</span>
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
                        <a href="{{ route('admin.sales.edit', $sale->order_id) }}" 
                           class="p-2 text-slate-400 hover:text-indigo-400 hover:bg-indigo-500/10 rounded-xl transition-all" 
                           title="Edit Order">
                            <i class="fa-solid fa-pen-to-square text-base"></i>
                        </a>

                        @if ($sale->due > 0)
                            <button type="button" 
                                    @click="
                                        activeSaleId = {{ $sale->order_id }};
                                        activeSaleBill = '#{{ str_pad($sale->morder_id, 4, '0', STR_PAD_LEFT) }}';
                                        activeSaleDue = {{ $sale->due }};
                                        paymentAmount = {{ $sale->due }};
                                        paymentModalOpen = true;
                                    "
                                    class="p-2 text-slate-400 hover:text-emerald-400 hover:bg-emerald-500/10 rounded-xl transition-all" 
                                    title="Record Payment">
                                <i class="fa-solid fa-indian-rupee-sign text-base"></i>
                            </button>
                        @endif

                        <!-- Print Order Trigger -->
                        <a href="{{ route('admin.sales.print', $sale->order_id) }}" 
                           target="_blank"
                           class="p-2 text-slate-400 hover:text-sky-400 hover:bg-sky-500/10 rounded-xl transition-all" 
                           title="Print Invoice / PDF">
                            <i class="fa-solid fa-print text-base"></i>
                        </a>

                        <!-- Delete / Cancel order trigger -->
                        <button type="button" 
                                @click="
                                    cancelSaleId = {{ $sale->order_id }};
                                    cancelSaleBill = '#{{ str_pad($sale->morder_id, 4, '0', STR_PAD_LEFT) }}';
                                    cancelModalOpen = true;
                                "
                                class="p-2 text-slate-400 hover:text-rose-500 hover:bg-rose-500/10 rounded-xl transition-all" 
                                title="Cancel Order">
                            <i class="fa-solid fa-trash-can text-base"></i>
                        </button>
                    </div>
                </td>
            </tr>
        @empty
            <tr class="block lg:table-row w-full bg-slate-900/30 border border-slate-800/60 rounded-2xl p-6 lg:p-0">
                <td colspan="9" class="px-6 py-12 text-center text-slate-500 font-medium block lg:table-cell w-full">
                    <div class="flex flex-col items-center justify-center gap-3">
                        <i class="fa-solid fa-receipt text-4xl text-slate-600 animate-bounce"></i>
                        <span>No sales transactions found matching search criteria.</span>
                    </div>
                </td>
            </tr>
        @endforelse
    </x-admin.table>

    <!-- Record Payment Modal -->
    <x-admin.modal id="paymentModalOpen" title="Record Balance Payment">
        <form :action="'/admin/sales/' + activeSaleId + '/payment'" method="POST" class="mt-6 space-y-4">
            @csrf
            <div>
                <span class="block text-xs font-semibold text-slate-500 uppercase tracking-wider">Invoice Details</span>
                <span class="block text-lg font-bold text-slate-800 dark:text-slate-200 mt-1" x-text="'Bill: ' + activeSaleBill"></span>
                <span class="block text-sm text-slate-600 dark:text-slate-400 mt-0.5" x-text="'Outstanding Due: Rs. ' + activeSaleDue.toFixed(2)"></span>
            </div>

            <div class="space-y-1.5">
                <label for="paymentAmount" class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Payment Amount (Rs.)</label>
                <input type="number" 
                       id="paymentAmount" 
                       name="paymentAmount" 
                       x-model="paymentAmount" 
                       :max="activeSaleDue" 
                       step="0.01" 
                       required 
                       class="w-full px-4 py-3 bg-slate-100 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-slate-800 dark:text-slate-200 placeholder-slate-400 dark:placeholder-slate-600 focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 font-bold text-lg">
            </div>

            <div class="pt-4 flex items-center justify-end gap-3">
                <button type="button" @click="paymentModalOpen = false" class="px-4 py-2 text-sm font-semibold text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200">
                    Cancel
                </button>
                <button type="submit" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-xl text-sm shadow-lg shadow-emerald-600/10 active:scale-95 transition-all">
                    Post Payment
                </button>
            </div>
        </form>
    </x-admin.modal>

    <!-- Cancel Order Confirmation Modal -->
    <x-admin.modal id="cancelModalOpen" title="Cancel Sales Order">
        <form :action="'/admin/sales/' + cancelSaleId" method="POST" class="mt-6 space-y-4">
            @csrf
            @method('DELETE')
            <div class="p-4 bg-rose-500/10 border border-rose-500/20 rounded-xl flex items-start gap-3">
                <i class="fa-solid fa-circle-exclamation text-rose-500 text-lg mt-0.5"></i>
                <div class="space-y-1">
                    <span class="block text-sm font-bold text-slate-800 dark:text-slate-200">Are you absolutely sure?</span>
                    <p class="text-xs text-slate-600 dark:text-slate-400 leading-relaxed">
                        You are about to cancel sales order <strong class="text-slate-800 dark:text-slate-200" x-text="cancelSaleBill"></strong>. This action is irreversible and will automatically restore original product stock levels in the warehouse.
                    </p>
                </div>
            </div>

            <div class="pt-4 flex items-center justify-end gap-3">
                <button type="button" @click="cancelModalOpen = false" class="px-4 py-2 text-sm font-semibold text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200">
                    Nevermind
                </button>
                <button type="submit" class="px-5 py-2.5 bg-rose-600 hover:bg-rose-700 text-white font-semibold rounded-xl text-sm shadow-lg shadow-rose-600/10 active:scale-95 transition-all">
                    Confirm Cancellation
                </button>
            </div>
        </form>
    </x-admin.modal>
</div>
@endsection
