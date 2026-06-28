@extends('layouts.admin', ['title' => 'Supplier Ledger Report'])

@section('content')
<div class="space-y-8 animate-fadeIn">

    <!-- Header Section -->
    <x-admin.header 
        title="Supplier Ledger" 
        description="Lookup, track, and print detailed supplier ledgers, purchase logs, and outstanding balances." 
        icon="fa-solid fa-refresh" 
        glass="true"
    />

    <div class="max-w-4xl mx-auto">
        <div class="p-8 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xl space-y-8">
            
            <h3 class="text-sm font-bold text-slate-800 dark:text-slate-200 uppercase tracking-wider border-l-4 border-indigo-500 pl-3 flex items-center gap-2">
                <i class="fa-solid fa-file-invoice text-indigo-400"></i>
                Select Supplier Profile
            </h3>
            
            <form action="{{ route('admin.reports.supplier_ledger.generate') }}" method="POST" target="_blank" id="supplierLedgerForm" class="space-y-6">
                @csrf

                <div class="p-5 rounded-2xl bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800/80 hover:border-indigo-500/20 transition space-y-3">
                    <div class="flex items-center gap-2 text-indigo-500 dark:text-indigo-400 font-bold text-xs uppercase tracking-wider">
                        <i class="fa-solid fa-user-tie"></i>
                        Supplier Name
                    </div>
                    <p class="text-[11px] text-slate-550 dark:text-slate-500 leading-normal">Select a supplier to trace purchase orders, payments made, and view outstanding credit balances.</p>
                    <select name="supplier_name" id="supplierSelect"
                        class="w-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl px-3 py-2.5 text-xs text-slate-800 dark:text-slate-300 focus:outline-none focus:ring-1 focus:ring-indigo-500 transition">
                        <option value="">~Select Supplier~</option>
                        @foreach ($suppliers as $supp)
                            <option value="{{ $supp->s_name }}" {{ $selectedSupplier == $supp->s_name ? 'selected' : '' }}>
                                {{ $supp->s_name }} {{ $supp->s_contact ? '('.$supp->s_contact.')' : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="pt-4 flex items-center justify-between border-t border-slate-200 dark:border-slate-850">
                    <x-admin.button type="button" onclick="previewLedger()" variant="secondary" icon="fa-solid fa-magnifying-glass" class="uppercase text-xs tracking-wider">
                        Preview In-Page Ledger
                    </x-admin.button>

                    <x-admin.button type="submit" variant="primary" icon="fa-solid fa-print" class="uppercase text-xs tracking-wider">
                        Generate Printable Ledger
                    </x-admin.button>
                </div>
            </form>
        </div>
    </div>

    <!-- Dynamic In-Page Ledger Preview -->
    @if ($selectedSupplier && $ledgerData)
        <div class="max-w-4xl mx-auto space-y-6">
            <div class="p-8 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xl space-y-6">
                
                <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-850 pb-4">
                    <div>
                        <h4 class="text-sm font-bold text-slate-800 dark:text-slate-200 uppercase tracking-wider border-l-4 border-violet-500 pl-3 flex items-center gap-2">
                            <i class="fa-solid fa-file-contract text-violet-400"></i>
                            Ledger History: {{ $selectedSupplier }}
                        </h4>
                    </div>
                    <div class="text-right">
                        <span class="text-[10px] font-extrabold text-slate-500 dark:text-slate-400 uppercase tracking-widest block font-outfit">Accumulated Balance Due</span>
                        <span class="text-lg font-mono font-black text-rose-400">Rs. {{ number_format($ledgerData['overall_balance'], 2) }}</span>
                    </div>
                </div>

                <div class="space-y-4">
                    @forelse ($ledgerData['ledger'] as $record)
                        @php
                            $order = $record['order'];
                            $payments = $record['payments'];
                            $billNo = $order->morder_id;
                            $orderDate = date('d-m-Y', strtotime($order->porder_date));
                            $grandTotal = $order->g_total;
                        @endphp

                        <div class="p-5 rounded-2xl bg-slate-50 dark:bg-slate-950/80 border border-slate-200 dark:border-slate-850 space-y-4">
                            <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-850/60 pb-3 text-xs">
                                <div class="flex items-center gap-3">
                                    <span class="font-mono font-bold text-indigo-500 dark:text-indigo-400 bg-indigo-500/5 px-2 py-0.5 rounded border border-indigo-500/10">PO: #{{ $billNo }}</span>
                                    <span class="font-mono text-slate-500">Date: {{ $orderDate }}</span>
                                </div>
                                <div class="flex items-center gap-4 text-slate-500 dark:text-slate-400 font-mono">
                                    <span>PO Total: <strong class="text-slate-800 dark:text-slate-100 font-semibold">Rs. {{ number_format($grandTotal, 2) }}</strong></span>
                                </div>
                            </div>

                            <div class="overflow-x-auto">
                                <table class="w-full text-left text-[11px] border-collapse">
                                    <thead>
                                        <tr class="text-slate-500 font-bold uppercase tracking-wider border-b border-slate-200 dark:border-slate-850">
                                            <th class="py-2 px-3">No</th>
                                            <th class="py-2 px-3">Payment Date</th>
                                            <th class="py-2 px-3 text-right">PO Total</th>
                                            <th class="py-2 px-3 text-right">Amount Paid</th>
                                            <th class="py-2 px-3 text-right">Balance Remaining</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-200 dark:divide-slate-850/40">
                                        @forelse ($payments as $pIdx => $pay)
                                            <tr class="text-slate-700 dark:text-slate-350">
                                                <td class="py-2 px-3 font-mono">{{ $pIdx + 1 }}</td>
                                                <td class="py-2 px-3 font-mono">{{ date('d-m-Y', strtotime($pay->pdate)) }}</td>
                                                <td class="py-2 px-3 text-right font-mono">Rs. {{ number_format($pay->gtotal, 2) }}</td>
                                                <td class="py-2 px-3 text-right font-mono text-emerald-600 dark:text-emerald-400">Rs. {{ number_format($pay->paid, 2) }}</td>
                                                <td class="py-2 px-3 text-right font-mono text-rose-600 dark:text-rose-400 font-semibold">Rs. {{ number_format($pay->bal, 2) }}</td>
                                            </tr>
                                        @empty
                                            <tr class="text-slate-500 font-medium">
                                                <td colspan="5" class="py-3 text-center">No payment log installments recorded for this purchase order.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @empty
                        <p class="text-center text-slate-500 font-medium py-4">No purchase ledger logs associated with this supplier profile.</p>
                    @endforelse
                </div>

            </div>
        </div>
    @endif

</div>

<script>
    function previewLedger() {
        const select = document.getElementById('supplierSelect');
        const supplierName = select.value;
        if (!supplierName) {
            alert('Please select a supplier first.');
            return;
        }
        window.location.href = '?supplier_name=' + encodeURIComponent(supplierName);
    }
</script>
@endsection
