@extends('layouts.admin', ['title' => 'Purchases Ledgers'])

@section('content')
<div class="space-y-6" x-data="{ 
    paymentModalOpen: false, 
    activePurchaseId: null, 
    activePurchaseBill: '', 
    activePurchaseDue: 0, 
    paymentAmount: '' 
}">
    <!-- Header -->
    <x-admin.header title="Purchase Transactions" description="View and track supplier stock acquisitions, payments, and outstanding balances.">
        <x-slot:action>
            <x-admin.button href="{{ route('admin.purchases.create') }}" icon="fa-solid fa-plus text-xs">
                Record New Purchase
            </x-admin.button>
        </x-slot:action>
    </x-admin.header>

    <!-- Purchases Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 animate-fadeIn">
        <!-- Stat Card 1 -->
        <div class="p-6 rounded-3xl bg-slate-900/50 border border-slate-800/80 shadow-xl flex items-center justify-between">
            <div class="space-y-1.5">
                <span class="block text-[10px] font-extrabold text-slate-500 uppercase tracking-widest">Total Purchases</span>
                <span class="block text-2xl font-extrabold text-indigo-400 tracking-tight">Rs. {{ number_format($totalPurchases, 2) }}</span>
            </div>
            <div class="p-4 bg-gradient-to-tr from-indigo-500/10 to-purple-500/10 border border-indigo-500/20 text-indigo-400 rounded-2xl shadow-lg shadow-indigo-500/5">
                <i class="fa-solid fa-cart-shopping text-2xl"></i>
            </div>
        </div>

        <!-- Stat Card 2 -->
        <div class="p-6 rounded-3xl bg-slate-900/50 border border-slate-800/80 shadow-xl flex items-center justify-between">
            <div class="space-y-1.5">
                <span class="block text-[10px] font-extrabold text-slate-500 uppercase tracking-widest">Total Paid Balance</span>
                <span class="block text-2xl font-extrabold text-emerald-400 tracking-tight">Rs. {{ number_format($totalPaid, 2) }}</span>
            </div>
            <div class="p-4 bg-gradient-to-tr from-emerald-500/10 to-teal-500/10 border border-emerald-500/20 text-emerald-400 rounded-2xl shadow-lg shadow-emerald-500/5">
                <i class="fa-solid fa-circle-check text-2xl"></i>
            </div>
        </div>

        <!-- Stat Card 3 -->
        <div class="p-6 rounded-3xl bg-slate-900/50 border border-slate-800/80 shadow-xl flex items-center justify-between">
            <div class="space-y-1.5">
                <span class="block text-[10px] font-extrabold text-slate-500 uppercase tracking-widest">Remaining Due Dues</span>
                <span class="block text-2xl font-extrabold text-rose-400 tracking-tight">Rs. {{ number_format($totalDue, 2) }}</span>
            </div>
            <div class="p-4 bg-gradient-to-tr from-rose-500/10 to-red-500/10 border border-rose-500/20 text-rose-400 rounded-2xl shadow-lg shadow-rose-500/5">
                <i class="fa-solid fa-triangle-exclamation text-2xl"></i>
            </div>
        </div>
    </div>

    <!-- Search & Filter Bar -->
    <x-admin.search-bar :action="route('admin.purchases.index')" placeholder="Search by Invoice # or Supplier name...">
        <x-slot:info>
            <span>Showing {{ $purchases->firstItem() ?? 0 }}-{{ $purchases->lastItem() ?? 0 }} of {{ $purchases->total() }} recorded purchases</span>
        </x-slot:info>
    </x-admin.search-bar>

    <!-- Responsive Purchases Table -->
    @php
    $tableHeaders = [
        ['label' => 'Invoice #'],
        ['label' => 'Date'],
        ['label' => 'Supplier'],
        ['label' => 'Address & Contact'],
        ['label' => 'Grand Total', 'align' => 'right'],
        ['label' => 'Paid', 'align' => 'right'],
        ['label' => 'Due Balance', 'align' => 'right'],
        ['label' => 'Staff Creator'],
        ['label' => 'Actions', 'align' => 'center']
    ];
    @endphp

    <x-admin.table :headers="$tableHeaders" :collection="$purchases" type="glass" minWidth="950px">
        @forelse ($purchases as $purchase)
            <tr class="hover:bg-slate-900/20 transition-all block lg:table-row w-full bg-slate-900/30 border border-slate-800/60 rounded-2xl p-4 lg:p-0 mb-4 lg:mb-0 relative grid grid-cols-2 gap-x-4 gap-y-3 lg:grid-cols-none lg:bg-transparent lg:border-0 lg:hover:bg-slate-900/20 lg:transition-all">
                
                <!-- Invoice # Badge / Mobile Card Header -->
                <td class="col-span-2 bg-indigo-500/10 text-indigo-400 px-3 py-2 rounded-lg text-xs font-mono font-bold flex items-center justify-between lg:table-cell lg:col-span-none lg:bg-transparent lg:px-6 lg:py-4 lg:text-indigo-400">
                    <span class="lg:hidden uppercase tracking-wider text-[10px] font-bold text-indigo-400">Invoice #</span>
                    <span>#{{ str_pad($purchase->morder_id, 4, '0', STR_PAD_LEFT) }}</span>
                </td>

                <!-- Date -->
                <td class="py-2 lg:px-6 lg:py-4 col-span-1 block lg:table-cell lg:col-span-none">
                    <span class="block lg:hidden text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Date</span>
                    <span class="font-medium whitespace-nowrap">{{ date('d-m-Y', strtotime($purchase->porder_date)) }}</span>
                </td>

                <!-- Supplier Name -->
                <td class="py-2 lg:px-6 lg:py-4 col-span-2 block lg:table-cell lg:col-span-none">
                    <span class="block lg:hidden text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Supplier</span>
                    <span class="font-semibold text-slate-200">{{ $purchase->s_name }}</span>
                </td>

                <!-- Contact -->
                <td class="py-2 lg:px-6 lg:py-4 col-span-1 block lg:table-cell lg:col-span-none text-xs font-medium text-slate-400">
                    <span class="block lg:hidden text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Contact Details</span>
                    <span>{{ $purchase->s_contact }}</span>
                </td>

                <!-- Grand Total -->
                <td class="py-2 lg:px-6 lg:py-4 col-span-1 block lg:table-cell lg:col-span-none lg:text-right font-semibold text-slate-200">
                    <span class="block lg:hidden text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Grand Total</span>
                    <span>Rs. {{ number_format($purchase->g_total, 2) }}</span>
                </td>

                <!-- Paid -->
                <td class="py-2 lg:px-6 lg:py-4 col-span-1 block lg:table-cell lg:col-span-none lg:text-right font-semibold text-emerald-400">
                    <span class="block lg:hidden text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Paid</span>
                    <span>Rs. {{ number_format($purchase->ppaid, 2) }}</span>
                </td>

                <!-- Due Balance -->
                <td class="py-2 lg:px-6 lg:py-4 col-span-1 block lg:table-cell lg:col-span-none lg:text-right font-semibold">
                    <span class="block lg:hidden text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Due Balance</span>
                    <span>
                        @if ($purchase->pbal > 0)
                            <span class="text-rose-400 bg-rose-500/5 px-2 py-1 rounded-lg border border-rose-500/10 font-bold">
                                Rs. {{ number_format($purchase->pbal, 2) }}
                            </span>
                        @else
                            <span class="text-slate-500">Rs. 0.00</span>
                        @endif
                    </span>
                </td>

                <!-- Staff Creator -->
                <td class="py-2 lg:px-6 lg:py-4 col-span-1 block lg:table-cell lg:col-span-none text-xs font-semibold text-slate-400">
                    <span class="block lg:hidden text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Staff Creator</span>
                    <span>{{ $staff[$purchase->staffname]->username ?? $purchase->staffname }}</span>
                </td>

                <!-- Actions -->
                <td class="py-3 border-t border-slate-800/40 mt-2 lg:border-t-0 lg:mt-0 col-span-2 block lg:table-cell lg:col-span-none lg:px-6 lg:py-4 lg:text-center whitespace-nowrap">
                    <span class="block lg:hidden text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-2">Actions</span>
                    <div class="inline-flex items-center gap-2">
                        <!-- Edit -->
                        <a href="{{ route('admin.purchases.edit', $purchase->porder_id) }}" 
                           class="p-2 text-slate-400 hover:text-indigo-400 hover:bg-indigo-500/10 rounded-xl transition-all" 
                           title="Edit Purchase">
                            <i class="fa-solid fa-pen-to-square text-base"></i>
                        </a>

                        <!-- Post payment -->
                        @if ($purchase->pbal > 0)
                            <button type="button" 
                                    @click="
                                        activePurchaseId = {{ $purchase->porder_id }};
                                        activePurchaseBill = '#{{ str_pad($purchase->morder_id, 4, '0', STR_PAD_LEFT) }}';
                                        activePurchaseDue = {{ $purchase->pbal }};
                                        paymentAmount = {{ $purchase->pbal }};
                                        paymentModalOpen = true;
                                    "
                                    class="p-2 text-slate-400 hover:text-emerald-400 hover:bg-emerald-500/10 rounded-xl transition-all" 
                                    title="Record Payment">
                                    <i class="fa-solid fa-indian-rupee-sign text-base"></i>
                            </button>
                        @endif

                        <!-- Print -->
                        <a href="{{ route('admin.purchases.print', $purchase->porder_id) }}" 
                           target="_blank"
                           class="p-2 text-slate-400 hover:text-sky-400 hover:bg-sky-500/10 rounded-xl transition-all" 
                           title="Print Purchase Sticker / PDF">
                            <i class="fa-solid fa-print text-base"></i>
                        </a>

                        <!-- Cancel -->
                        @if ($purchase->porder_status != 3)
                            <form method="POST" action="{{ route('admin.purchases.destroy', $purchase->porder_id) }}" 
                                  onsubmit="return confirm('Are you sure you want to cancel this purchase order? This will revert added product stock levels.')" 
                                  class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="p-2 text-slate-400 hover:text-rose-500 hover:bg-rose-500/10 rounded-xl transition-all" 
                                        title="Cancel Purchase Order">
                                    <i class="fa-solid fa-trash-can text-base"></i>
                                </button>
                            </form>
                        @endif
                    </div>
                </td>
            </tr>
        @empty
            <tr class="block lg:table-row w-full bg-slate-900/30 border border-slate-800/60 rounded-2xl p-6 lg:p-0">
                <td colspan="9" class="px-6 py-12 text-center text-slate-500 font-medium block lg:table-cell w-full">
                    <div class="flex flex-col items-center justify-center gap-3">
                        <i class="fa-solid fa-receipt text-4xl text-slate-600 animate-bounce"></i>
                        <span>No purchases found matching search criteria.</span>
                    </div>
                </td>
            </tr>
        @endforelse
    </x-admin.table>

    <!-- Record Payment Modal -->
    <x-admin.modal id="paymentModalOpen" title="Record Purchase Payment">
        <form :action="'/admin/purchases/' + activePurchaseId + '/payment'" method="POST" class="mt-6 space-y-4">
            @csrf
            <div>
                <span class="block text-xs font-semibold text-slate-500 uppercase tracking-wider">Invoice Details</span>
                <span class="block text-lg font-bold text-slate-200 mt-1" x-text="'Bill: ' + activePurchaseBill"></span>
                <span class="block text-sm text-slate-400 mt-0.5" x-text="'Outstanding Due: Rs. ' + activePurchaseDue.toFixed(2)"></span>
            </div>

            <div class="space-y-1.5">
                <label for="paymentAmount" class="block text-xs font-bold text-slate-400 uppercase tracking-wider">Payment Amount (Rs.)</label>
                <input type="number" 
                       id="paymentAmount" 
                       name="paymentAmount" 
                       x-model="paymentAmount" 
                       :max="activePurchaseDue" 
                       step="0.01" 
                       required 
                       class="w-full px-4 py-3 bg-slate-950 border border-slate-800 rounded-xl text-slate-200 placeholder-slate-650 focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 font-bold text-lg text-right">
            </div>

            <div class="pt-4 flex items-center justify-end gap-3">
                <button type="button" @click="paymentModalOpen = false" class="px-4 py-2 text-sm font-semibold text-slate-400 hover:text-slate-200">
                    Cancel
                </button>
                <button type="submit" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-xl text-sm shadow-lg shadow-emerald-600/10 active:scale-95 transition-all">
                    Post Payment
                </button>
            </div>
        </form>
    </x-admin.modal>
</div>
@endsection
