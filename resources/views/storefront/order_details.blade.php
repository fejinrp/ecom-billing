@extends('layouts.storefront')

@section('content')
    @php
        $paymentMethod = 'Cash';
        if ($order->paymethod === 'q') $paymentMethod = 'Cheque';
        elseif ($order->paymethod === 'I') $paymentMethod = 'Online / UPI';
        elseif ($order->paymethod === 'C') $paymentMethod = 'Credit Card';
        elseif ($order->paymethod === 'D') $paymentMethod = 'Debit Card';
    @endphp

    <div class="order-sheet">
    <nav class="flex items-center gap-3 text-[10px] font-black uppercase tracking-[0.35em] mb-8 flex-wrap text-slate-500 dark:text-slate-400 print-hidden">
        <a href="{{ route('storefront.index') }}" class="text-[#0059e3] hover:text-[#0040a6] transition-colors">Hardware Store</a>
        <i class="fa-solid fa-chevron-right text-[8px] text-slate-400 dark:text-slate-600"></i>
        <a href="{{ route('storefront.orders') }}" class="text-[#0059e3] hover:text-[#0040a6] transition-colors">Purchase History</a>
        <i class="fa-solid fa-chevron-right text-[8px] text-slate-400 dark:text-slate-600"></i>
        <span class="text-slate-500 dark:text-slate-400 truncate max-w-[220px]">Order #{{ $order->morderid }} Details</span>
    </nav>

    <div class="rounded-[2rem] border border-slate-200/70 dark:border-slate-800/70 bg-gradient-to-br from-white/90 via-white/70 to-slate-50/80 dark:from-slate-950/45 dark:via-slate-950/30 dark:to-slate-900/40 p-4 sm:p-6 lg:p-8 shadow-2xl shadow-slate-900/5 mb-12 print-card">
        <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-5 pb-6 border-b border-slate-200/70 dark:border-slate-800/70">
            <div class="space-y-2">
                <span class="block text-[10px] font-black uppercase tracking-[0.35em] text-slate-500 dark:text-slate-400">Invoice Specification</span>
                <div class="flex flex-wrap items-center gap-2">
                    <span class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white font-mono">#{{ $order->morderid }}</span>
                    @if ($order->ostatus === 'd')
                        <span class="px-3 py-1.5 rounded-full text-[10px] font-black bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20 uppercase tracking-widest">Delivered</span>
                    @elseif ($order->ostatus === 's')
                        <span class="px-3 py-1.5 rounded-full text-[10px] font-black bg-sky-500/10 text-sky-600 dark:text-sky-400 border border-sky-500/20 uppercase tracking-widest">Transit</span>
                    @elseif ($order->ostatus === 'c')
                        <span class="px-3 py-1.5 rounded-full text-[10px] font-black bg-rose-500/10 text-rose-600 dark:text-rose-400 border border-rose-500/20 uppercase tracking-widest">Cancelled</span>
                    @else
                        <span class="px-3 py-1.5 rounded-full text-[10px] font-black bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-500/20 uppercase tracking-widest">Processing</span>
                    @endif
                </div>
                <p class="text-sm text-slate-500 dark:text-slate-400 max-w-2xl leading-relaxed">
                    This order detail page now follows the storefront theme system, so light and dark modes use the same surfaces, borders, and typography.
                </p>
            </div>

            <a href="{{ route('storefront.order_print', $order->orderid) }}?autoprint=1" target="_blank" class="print-hidden inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-[#0059e3] hover:bg-[#0040a6] text-white text-[10px] font-black uppercase tracking-widest shadow-lg shadow-blue-600/15 transition-all">
                <i class="fa-solid fa-print text-[11px]"></i> Print Receipt
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 pt-6">
            <div class="rounded-2xl border border-slate-200/70 dark:border-slate-800 bg-white/80 dark:bg-slate-900/70 p-4 shadow-sm print-card">
                <span class="block text-[10px] font-black uppercase tracking-[0.35em] text-slate-500 dark:text-slate-400 mb-2">Dispatch & Billing</span>
                <p class="font-black text-slate-900 dark:text-white uppercase font-bold">{{ $order->username }}</p>
                <p class="text-sm text-slate-600 dark:text-slate-400 uppercase whitespace-pre-line mt-1 leading-relaxed">{{ $order->user ? ($order->user->billingaddress . ', ' . $order->user->billingcity . ', ' . $order->user->billingstate . ' - ' . $order->user->billingpincode) : 'N/A' }}</p>
                @if ($order->user && $order->user->contactno)
                    <p class="text-xs font-black text-[#0059e3] mt-3 font-mono">MOB: {{ $order->user->contactno }}</p>
                @endif
            </div>

            <div class="rounded-2xl border border-slate-200/70 dark:border-slate-800 bg-white/80 dark:bg-slate-900/70 p-4 shadow-sm print-card">
                <span class="block text-[10px] font-black uppercase tracking-[0.35em] text-slate-500 dark:text-slate-400 mb-2">Remittance</span>
                <p class="text-sm text-slate-600 dark:text-slate-400">Channel: <span class="font-black text-slate-900 dark:text-white uppercase font-bold">{{ $paymentMethod }}</span></p>
                <p class="text-sm text-slate-600 dark:text-slate-400 mt-1">Payment Tag: <span class="font-black text-[#0059e3] uppercase font-bold">ONLINE STORE</span></p>
                <p class="text-sm text-slate-600 dark:text-slate-400 mt-1">Tax Mode: <span class="font-black text-[#0059e3] uppercase font-bold">{{ ($order->user && $order->user->billingstate == 'TAMIL NADU') ? 'Tamil Nadu (Intra-State)' : 'Out of State (Inter-State)' }}</span></p>
            </div>

            <div class="rounded-2xl border border-slate-200/70 dark:border-slate-800 bg-white/80 dark:bg-slate-900/70 p-4 shadow-sm print-card">
                <span class="block text-[10px] font-black uppercase tracking-[0.35em] text-slate-500 dark:text-slate-400 mb-2">Audit Log</span>
                <p class="text-sm text-slate-600 dark:text-slate-400">Order Date: <span class="font-mono font-black text-slate-900 dark:text-white font-bold">{{ date('d-m-Y', strtotime($order->orderdate)) }}</span></p>
                <p class="text-[11px] text-slate-500 dark:text-slate-500 mt-1">Session: <span class="font-mono">{{ sha1($order->orderid) }}</span></p>
            </div>
        </div>

        <div class="mt-8 space-y-3">
            <div class="flex items-center justify-between gap-3">
                <span class="text-[10px] font-black uppercase tracking-[0.35em] text-slate-500 dark:text-slate-400">Hardware Itemizations</span>
                <span class="hidden sm:inline-flex px-3 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest bg-blue-500/10 text-[#0059e3] border border-blue-500/20">
                    {{ $order->items->count() }} line items
                </span>
            </div>

            <div class="rounded-[1.75rem] overflow-hidden border border-slate-200/70 dark:border-slate-800 bg-white/90 dark:bg-slate-900/80 shadow-lg shadow-blue-500/5 print-card">
                <div class="overflow-x-auto custom-scrollbar">
                    <table class="w-full min-w-[920px] text-left text-xs border-collapse">
                        <thead class="sticky top-0 z-10">
                            <tr class="bg-slate-50/95 dark:bg-slate-950/95 border-b border-slate-200/70 dark:border-slate-800 text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-[0.35em]">
                                <th class="py-4 px-5">Spec Item Name</th>
                                <th class="py-4 px-5 w-28 text-center">HSN/SAC</th>
                                <th class="py-4 px-5 w-20 text-center">GST %</th>
                                <th class="py-4 px-5 w-28 text-right">Basic Rate</th>
                                <th class="py-4 px-5 w-20 text-center">Qty</th>
                                <th class="py-4 px-5 w-32 text-right">Line Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200/70 dark:divide-slate-800/70">
                            @foreach ($order->items as $item)
                                <tr class="group hover:bg-slate-50/70 dark:hover:bg-slate-950/30 transition-colors">
                                    <td class="py-4 px-5 font-bold text-slate-900 dark:text-white uppercase max-w-[320px] truncate">
                                        {{ $item->product->productname ?? $item->productId }}
                                    </td>
                                    <td class="py-4 px-5 text-center font-mono text-[11px] text-slate-500 dark:text-slate-400">
                                        {{ $item->hsnsan ?: '84713010' }}
                                    </td>
                                    <td class="py-4 px-5 text-center font-mono font-black text-slate-700 dark:text-slate-300">
                                        {{ intval($item->gst) }}%
                                    </td>
                                    <td class="py-4 px-5 text-right font-mono font-semibold text-slate-700 dark:text-slate-300 whitespace-nowrap">
                                        Rs. {{ \App\Helpers\NumberHelper::indianFormat($item->rate) }}
                                    </td>
                                    <td class="py-4 px-5 text-center font-mono font-black text-slate-900 dark:text-white">
                                        {{ $item->quantity }}
                                    </td>
                                    <td class="py-4 px-5 text-right font-mono font-black text-[#0059e3] whitespace-nowrap">
                                        Rs. {{ \App\Helpers\NumberHelper::indianFormat($item->cprice) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-8 mt-8 border-t border-slate-200/70 dark:border-slate-800/70">
            <div class="space-y-4">
                @if ($order->bamount > 0 && $order->paymethod === 'I')
                    <div class="rounded-[1.5rem] p-5 bg-blue-500/5 dark:bg-blue-500/10 border border-blue-500/15 dark:border-blue-500/20 text-sm text-slate-650 dark:text-slate-400 leading-relaxed space-y-3 print-card">
                        <span class="block font-black text-slate-900 dark:text-white uppercase tracking-[0.25em] flex items-center gap-2 text-[10px] font-bold">
                            <i class="fa-solid fa-bank text-[#0059e3]"></i> Direct Bank Transfer Instruction
                        </span>
                        <p>
                            Transfer the pending amount of
                            <span class="font-mono font-black text-[#0059e3]">Rs. {{ \App\Helpers\NumberHelper::indianFormat($order->bamount) }}</span>
                            to our corporate account for instant dispatch clearance.
                        </p>
                        <div class="rounded-2xl p-4 bg-white/90 dark:bg-slate-950/50 border border-slate-200/70 dark:border-slate-800 font-mono text-sm text-slate-700 dark:text-slate-300 space-y-1 print-card">
                            <p class="font-black text-slate-900 dark:text-white font-bold">INDIAN OVERSEAS BANK</p>
                            <p>Branch: Kuzhithurai</p>
                            <p>A/C: <span class="text-[#0059e3] font-black">2869020000000349</span></p>
                            <p>IFSC: <span class="text-[#0059e3] font-black">IOBA0002869</span></p>
                        </div>
                    </div>
                @else
                    <div class="rounded-[1.5rem] p-5 bg-emerald-500/5 dark:bg-emerald-500/10 border border-emerald-500/15 dark:border-emerald-500/20 text-sm text-emerald-700 dark:text-emerald-300 leading-relaxed flex items-start gap-3">
                        <i class="fa-solid fa-shield-halved text-base mt-0.5"></i>
                        <div>
                            <span class="block font-black uppercase tracking-[0.25em] text-[10px] text-slate-900 dark:text-white font-bold">Transaction Auditor Tag</span>
                            <span>No immediate payment action is required. Dispatch parameters are processed within 24 working hours. Contact +91 99442 28686 for tracking queries.</span>
                        </div>
                    </div>
                @endif
            </div>

            <div class="rounded-[1.5rem] p-5 bg-white/90 dark:bg-slate-900/80 border border-slate-200/70 dark:border-slate-800 shadow-sm space-y-3 text-sm print-card">
                <div class="flex items-center justify-between gap-3 text-slate-650 dark:text-slate-400">
                    <span>Items Subtotal</span>
                    <span class="font-mono font-semibold text-slate-900 dark:text-white font-bold">Rs. {{ \App\Helpers\NumberHelper::indianFormat($order->total) }}</span>
                </div>

                @php
                    $gstTotal = floatval($order->gsta);
                @endphp
                @if ($order->user && $order->user->billingstate == 'TAMIL NADU')
                    <div class="flex items-center justify-between text-slate-500 dark:text-slate-400 text-xs">
                        <span>CGST Split (50%)</span>
                        <span class="font-mono">Rs. {{ \App\Helpers\NumberHelper::indianFormat($gstTotal / 2) }}</span>
                    </div>
                    <div class="flex items-center justify-between text-slate-500 dark:text-slate-400 text-xs">
                        <span>SGST Split (50%)</span>
                        <span class="font-mono">Rs. {{ \App\Helpers\NumberHelper::indianFormat($gstTotal / 2) }}</span>
                    </div>
                @else
                    <div class="flex items-center justify-between text-slate-500 dark:text-slate-400 text-xs">
                        <span>IGST Split (100%)</span>
                        <span class="font-mono">Rs. {{ \App\Helpers\NumberHelper::indianFormat($gstTotal) }}</span>
                    </div>
                @endif

                <div class="border-t border-slate-200/70 dark:border-slate-800 pt-3 flex items-center justify-between gap-3">
                    <span class="text-sm font-black uppercase tracking-[0.25em] text-slate-900 dark:text-white font-bold">Grand Total</span>
                    <span class="font-mono text-xl font-black text-[#0059e3]">Rs. {{ \App\Helpers\NumberHelper::indianFormat($order->gamount) }}</span>
                </div>

                <div class="border-t border-slate-200/70 dark:border-slate-800 pt-3 space-y-2 font-mono text-xs">
                    <div class="flex items-center justify-between text-emerald-600 dark:text-emerald-400 font-bold">
                        <span class="font-black uppercase tracking-widest">Amount Paid</span>
                        <span>Rs. {{ \App\Helpers\NumberHelper::indianFormat($order->pamount) }}</span>
                    </div>
                    <div class="flex items-center justify-between text-rose-600 dark:text-rose-400 font-bold">
                        <span class="font-black uppercase tracking-widest">Balance Due</span>
                        <span>Rs. {{ \App\Helpers\NumberHelper::indianFormat($order->bamount) }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-8 pt-5 border-t border-slate-200/70 dark:border-slate-800 text-center text-[10px] text-slate-500 dark:text-slate-400 leading-relaxed print-hidden">
            Thank you for your trust. MTL Computer Garden custom computing is engineered with care and delivered with compliance.
        </div>
    </div>

    <style>
        .order-sheet {
            print-color-adjust: exact;
            -webkit-print-color-adjust: exact;
        }
        @media print {
            .order-sheet,
            .order-sheet * {
                box-shadow: none !important;
                text-shadow: none !important;
                filter: none !important;
            }
            .order-sheet .print-card {
                background: #fff !important;
                border-color: #e2e8f0 !important;
            }
            .order-sheet .print-hidden {
                display: none !important;
            }
            .order-sheet .print-muted {
                color: #475569 !important;
            }
            .order-sheet .print-accent {
                color: #0059e3 !important;
            }
        }
        @media print {
            .print-hidden {
                display: none !important;
            }
        }
    </style>

    @if (request()->query('autoprint') === '1')
        <script>
            window.addEventListener('load', () => window.print());
        </script>
    @endif
</div>
@endsection
