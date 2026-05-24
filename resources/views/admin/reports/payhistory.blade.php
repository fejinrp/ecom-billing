@extends('layouts.admin')

@section('content')
<div class="space-y-8 animate-fadeIn">

    <!-- Header Section -->
    <x-admin.header 
        title="Payment History Ledger" 
        description="Lookup, track, and print detailed financial ledgers, transactional logs, and outstanding balances for specific customer profiles." 
        icon="fa-solid fa-refresh" 
        glass="true"
    />

    <div class="max-w-4xl mx-auto">
        <div class="p-8 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xl space-y-8">
            
            <h3 class="text-sm font-bold text-slate-800 dark:text-slate-200 uppercase tracking-wider border-l-4 border-indigo-500 pl-3 flex items-center gap-2">
                <i class="fa-solid fa-file-invoice text-indigo-400"></i>
                Select Customer or Partner Profile
            </h3>
            
            <form action="{{ route('admin.reports.payhistory.generate') }}" method="POST" target="_blank" id="payHistoryForm" class="space-y-6">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    
                    <!-- Customer Card -->
                    <div class="p-5 rounded-2xl bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800/80 hover:border-indigo-500/20 transition space-y-3">
                        <div class="flex items-center gap-2 text-indigo-500 dark:text-indigo-400 font-bold text-xs uppercase tracking-wider">
                            <i class="fa-solid fa-user"></i>
                            Customer_Name_Address
                        </div>
                        <p class="text-[11px] text-slate-550 dark:text-slate-500 leading-normal">Track direct retail customers, offline purchase balance, and payments.</p>
                        <select name="agentname" id="agentSelect" onchange="resetOthers('agent')"
                            class="w-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl px-3 py-2.5 text-xs text-slate-800 dark:text-slate-300 focus:outline-none focus:ring-1 focus:ring-indigo-500 transition">
                            <option value="">~Select~</option>
                            @foreach ($customers as $cust)
                                <option value="{{ $cust->id }}">{{ $cust->uname }} ({{ $cust->billingcity ?? 'N/A' }})</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Dealer Card -->
                    <div class="p-5 rounded-2xl bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800/80 hover:border-purple-500/20 transition space-y-3">
                        <div class="flex items-center gap-2 text-purple-500 dark:text-purple-400 font-bold text-xs uppercase tracking-wider">
                            <i class="fa-solid fa-store"></i>
                            Dealer_Name_Address
                        </div>
                        <p class="text-[11px] text-slate-550 dark:text-slate-500 leading-normal">Track registered wholesale merchants, trade orders, and billing cycles.</p>
                        <select name="oagentname" id="dealerSelect" onchange="resetOthers('dealer')"
                            class="w-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl px-3 py-2.5 text-xs text-slate-800 dark:text-slate-300 focus:outline-none focus:ring-1 focus:ring-purple-500 transition">
                            <option value="">~Select~</option>
                            @foreach ($dealers as $dlr)
                                <option value="{{ $dlr->id }}">{{ $dlr->uname }} ({{ $dlr->billingcity ?? 'N/A' }})</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- SDealer Card -->
                    <div class="p-5 rounded-2xl bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800/80 hover:border-emerald-500/20 transition space-y-3">
                        <div class="flex items-center gap-2 text-emerald-550 dark:text-emerald-400 font-bold text-xs uppercase tracking-wider">
                            <i class="fa-solid fa-truck-moving"></i>
                            SDealer_Name_Address
                        </div>
                        <p class="text-[11px] text-slate-550 dark:text-slate-500 leading-normal">Track distributorship sales partners, transport logs, and credit limits.</p>
                        <select name="soagentname" id="sdealerSelect" onchange="resetOthers('sdealer')"
                            class="w-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl px-3 py-2.5 text-xs text-slate-800 dark:text-slate-300 focus:outline-none focus:ring-1 focus:ring-emerald-500 transition">
                            <option value="">~Select~</option>
                            @foreach ($sdealers as $sd)
                                <option value="{{ $sd->id }}">{{ $sd->uname }} ({{ $sd->billingcity ?? 'N/A' }})</option>
                            @endforeach
                        </select>
                    </div>

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

    <!-- Dynamic In-Page Ledger Preview (Visible only when selected User details are loaded) -->
    @if ($selectedUser && $ledgerData)
        <div class="max-w-4xl mx-auto space-y-6">
            <div class="p-8 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xl space-y-6">
                
                <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-850 pb-4">
                    <div>
                        <h4 class="text-sm font-bold text-slate-800 dark:text-slate-200 uppercase tracking-wider border-l-4 border-violet-500 pl-3 flex items-center gap-2">
                            <i class="fa-solid fa-file-contract text-violet-400"></i>
                            Ledger History: {{ $selectedUser->uname }}
                        </h4>
                        <p class="text-[10px] text-slate-550 dark:text-slate-500 uppercase font-mono mt-0.5 ml-4">
                            Address: {{ $selectedUser->billingaddress ?? 'N/A' }}, {{ $selectedUser->billingcity ?? 'N/A' }} | Contact: {{ $selectedUser->contactno ?? 'N/A' }}
                        </p>
                    </div>
                    <div class="text-right">
                        <span class="text-[10px] font-extrabold text-slate-500 dark:text-slate-400 uppercase tracking-widest block font-outfit">Accumulated Balance Due</span>
                        <span class="text-lg font-mono font-black text-rose-400">Rs. {{ number_format($ledgerData['overall_balance'], 2) }}</span>
                    </div>
                </div>

                <div class="space-y-4">
                    @forelse ($ledgerData['ledger'] as $idx => $record)
                        @php
                            $order = $record['order'];
                            $payments = $record['payments'];
                            $isOnline = $record['is_online'];

                            $orderId = $isOnline ? $order->orderid : $order->order_id;
                            $billNo = $isOnline ? $order->morderid : $order->morder_id;
                            $orderDate = date('d-m-Y', strtotime($isOnline ? $order->orderdate : $order->order_date));
                            $grandTotal = $isOnline ? $order->gamount : $order->grand_total;
                        @endphp

                        <div class="p-5 rounded-2xl bg-slate-50 dark:bg-slate-950/80 border border-slate-200 dark:border-slate-850 space-y-4">
                            <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-850/60 pb-3 text-xs">
                                <div class="flex items-center gap-3">
                                    <span class="font-mono font-bold text-indigo-500 dark:text-indigo-400 bg-indigo-500/5 px-2 py-0.5 rounded border border-indigo-500/10">BILL: #{{ $billNo }}</span>
                                    <span class="font-mono text-slate-500">Date: {{ $orderDate }}</span>
                                </div>
                                <div class="flex items-center gap-4 text-slate-500 dark:text-slate-400 font-mono">
                                    <span>Grand Total: <strong class="text-slate-800 dark:text-slate-100 font-semibold">Rs. {{ number_format($grandTotal, 2) }}</strong></span>
                                </div>
                            </div>

                            <div class="overflow-x-auto">
                                <table class="w-full text-left text-[11px] border-collapse">
                                    <thead>
                                        <tr class="text-slate-500 font-bold uppercase tracking-wider border-b border-slate-200 dark:border-slate-850">
                                            <th class="py-2 px-3">No</th>
                                            <th class="py-2 px-3">Payment Date</th>
                                            <th class="py-2 px-3 text-right">Bill Total</th>
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
                                                <td class="py-2 px-3 text-right font-mono text-emerald-600 dark:text-emerald-400">Rs. {{ number_format($pay->pamount, 2) }}</td>
                                                <td class="py-2 px-3 text-right font-mono text-rose-600 dark:text-rose-400 font-semibold">Rs. {{ number_format($pay->bamount, 2) }}</td>
                                            </tr>
                                        @empty
                                            <tr class="text-slate-500 font-medium">
                                                <td colspan="5" class="py-3 text-center">No payment log installments recorded for this invoice.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @empty
                        <p class="text-center text-slate-500 font-medium py-4">No order ledger logs associated with this customer profile.</p>
                    @endforelse
                </div>

            </div>
        </div>
    @endif

</div>

<script>
    // Reset other selectors when one gets clicked to keep forms strict
    function resetOthers(source) {
        const agent = document.getElementById('agentSelect');
        const dealer = document.getElementById('dealerSelect');
        const sdealer = document.getElementById('sdealerSelect');

        if (source === 'agent' && agent.value !== '') {
            dealer.value = '';
            sdealer.value = '';
        } else if (source === 'dealer' && dealer.value !== '') {
            agent.value = '';
            sdealer.value = '';
        } else if (source === 'sdealer' && sdealer.value !== '') {
            agent.value = '';
            dealer.value = '';
        }
    }

    // Trigger in-page ledger preview loading
    function previewLedger() {
        const agent = document.getElementById('agentSelect').value;
        const dealer = document.getElementById('dealerSelect').value;
        const sdealer = document.getElementById('sdealerSelect').value;
        const userId = agent || dealer || sdealer;

        if (!userId) {
            alert('Please select a customer or dealer profile first.');
            return;
        }

        // Reload current page with chosen user_id query param
        window.location.href = "{{ route('admin.reports.payhistory') }}?user_id=" + userId;
    }
</script>
@endsection
