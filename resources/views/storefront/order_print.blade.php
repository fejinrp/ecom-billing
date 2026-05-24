<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order #{{ $order->morder_id }} Print</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script>
        (function () {
            var t = localStorage.getItem('sf-theme');
            if (t !== 'dark') t = 'light';
            if (t === 'dark') {
                document.documentElement.classList.add('dark');
                document.documentElement.classList.remove('light');
            } else {
                document.documentElement.classList.remove('dark');
                document.documentElement.classList.add('light');
            }
        })();
    </script>
    <style>
        @page {
            size: A4 portrait;
            margin: 10mm;
        }

        html, body {
            width: 100%;
        }

        .print-sheet {
            max-width: 180mm;
            margin: 0 auto;
        }

        .print-table-wrap {
            scrollbar-width: thin;
        }

        .print-table-wrap::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }

        .print-table-wrap::-webkit-scrollbar-thumb {
            background: rgba(28, 63, 206, 0.28);
            border-radius: 9999px;
        }

        @media print {
            html, body {
                width: 100%;
                min-height: auto;
                background: #fff !important;
                color: #0f172a !important;
                overflow: visible !important;
            }

            body {
                background: #fff !important;
                color: #0f172a !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            .print-sheet {
                max-width: none !important;
                width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
            }

            .print-hidden {
                display: none !important;
            }
            .print-card,
            .print-surface,
            .print-surface-strong {
                background: #fff !important;
                border-color: #e2e8f0 !important;
                box-shadow: none !important;
                color: #0f172a !important;
                overflow: visible !important;
            }
            .print-muted {
                color: #475569 !important;
            }
            .print-accent {
                color: #1c3fce !important;
            }
            .print-break-avoid {
                break-inside: avoid;
                page-break-inside: avoid;
            }
            .print-limit {
                max-height: none !important;
                overflow: visible !important;
            }
            .print-table-wrap {
                overflow: visible !important;
                scrollbar-width: none !important;
            }
            .print-table-wrap::-webkit-scrollbar {
                display: none !important;
            }
            .print-table {
                width: 100% !important;
                min-width: 0 !important;
                table-layout: fixed !important;
            }
            .print-table thead {
                display: table-header-group;
            }
            .print-table tbody tr {
                break-inside: avoid;
                page-break-inside: avoid;
            }
            .print-table th,
            .print-table td {
                padding-left: 8px !important;
                padding-right: 8px !important;
            }
            a[href]:after {
                content: "";
            }
        }
    </style>
</head>
<body class="min-h-full bg-slate-50 text-slate-900 dark:bg-slate-950 dark:text-slate-100">
    @php
        $paymentMethod = 'Cash';
        if ($order->payment_type == 1) $paymentMethod = 'Cheque';
        elseif ($order->payment_type == 3) $paymentMethod = 'Online / UPI';
        elseif ($order->payment_type == 4) $paymentMethod = 'Debit Card';
    @endphp

    <div class="print-sheet max-w-5xl mx-auto px-4 py-6 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between gap-4 mb-6 print-hidden">
            <a href="{{ route('storefront.order_details', $order->order_id) }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-700 dark:text-slate-300 text-xs font-black uppercase tracking-widest shadow-sm hover:text-slate-900 dark:hover:text-white transition-all">
                <i class="fa-solid fa-arrow-left"></i> Back to Order
            </a>
            <button onclick="window.print()" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-gradient-to-r from-indigo-600 to-purple-600 text-white text-xs font-black uppercase tracking-widest shadow-lg shadow-indigo-600/15 transition-all">
                <i class="fa-solid fa-print"></i> Print Invoice
            </button>
        </div>

        <div class="print-card print-limit rounded-[2rem] border border-slate-200/70 dark:border-slate-800/70 bg-gradient-to-br from-white/90 via-white/70 to-slate-50/80 dark:from-slate-950/45 dark:via-slate-950/30 dark:to-slate-900/40 overflow-hidden print:overflow-visible shadow-2xl shadow-slate-900/5">
            <div class="p-6 sm:p-8 border-b border-slate-200/70 dark:border-slate-800/70 flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4">
                <div class="space-y-2">
                    <span class="block text-[10px] font-black uppercase tracking-[0.35em] text-slate-500 dark:text-slate-400">Invoice Specification</span>
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white font-mono">#{{ $order->morder_id }}</span>
                        @if ($order->order_status == 1)
                            <span class="px-3 py-1.5 rounded-full text-[10px] font-black bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-500/20 uppercase tracking-widest">Placed</span>
                        @elseif ($order->order_status == 2)
                            <span class="px-3 py-1.5 rounded-full text-[10px] font-black bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20 uppercase tracking-widest">Delivered</span>
                        @else
                            <span class="px-3 py-1.5 rounded-full text-[10px] font-black bg-rose-500/10 text-rose-600 dark:text-rose-400 border border-rose-500/20 uppercase tracking-widest">Cancelled</span>
                        @endif
                    </div>
                    <p class="text-sm text-slate-500 dark:text-slate-400 max-w-2xl leading-relaxed">
                        Print-friendly invoice view with the same storefront theme system used across the site.
                    </p>
                </div>

                <div class="flex items-center gap-3 print-hidden">
                    <a href="{{ route('storefront.order_details', $order->order_id) }}" class="px-4 py-2.5 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-700 dark:text-slate-300 text-xs font-black uppercase tracking-widest shadow-sm hover:text-slate-900 dark:hover:text-white transition-all">
                        <i class="fa-solid fa-eye text-[11px] mr-1.5"></i> View Page
                    </a>
                </div>
            </div>

            <div class="p-6 sm:p-8 space-y-8">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 print-break-avoid">
                    <div class="print-surface rounded-2xl border border-slate-200/70 dark:border-slate-800 bg-white/80 dark:bg-slate-900/70 p-4">
                        <span class="block text-[10px] font-black uppercase tracking-[0.35em] text-slate-500 dark:text-slate-400 mb-2">Dispatch & Billing</span>
                        <p class="font-black text-slate-900 dark:text-white uppercase">{{ $order->client_name }}</p>
                        <p class="text-sm text-slate-600 dark:text-slate-400 uppercase whitespace-pre-line mt-1 leading-relaxed">{{ $order->client_contact }}</p>
                        @if ($order->mobile)
                            <p class="text-xs font-black text-blue-600 dark:text-indigo-400 mt-3 font-mono">MOB: {{ $order->mobile }}</p>
                        @endif
                    </div>

                    <div class="print-surface rounded-2xl border border-slate-200/70 dark:border-slate-800 bg-white/80 dark:bg-slate-900/70 p-4">
                        <span class="block text-[10px] font-black uppercase tracking-[0.35em] text-slate-500 dark:text-slate-400 mb-2">Remittance</span>
                        <p class="text-sm text-slate-600 dark:text-slate-400">Channel: <span class="font-black text-slate-900 dark:text-white uppercase">{{ $paymentMethod }}</span></p>
                        <p class="text-sm text-slate-600 dark:text-slate-400 mt-1">Payment Tag: <span class="font-black text-blue-600 dark:text-indigo-400 uppercase">{{ $order->paymentname ?: 'ONLINE STORE' }}</span></p>
                        <p class="text-sm text-slate-600 dark:text-slate-400 mt-1">Tax Mode: <span class="font-black text-blue-600 dark:text-indigo-400 uppercase">{{ $order->payment_place == 1 ? 'Tamil Nadu (Intra-State)' : 'Out of State (Inter-State)' }}</span></p>
                    </div>

                    <div class="print-surface rounded-2xl border border-slate-200/70 dark:border-slate-800 bg-white/80 dark:bg-slate-900/70 p-4">
                        <span class="block text-[10px] font-black uppercase tracking-[0.35em] text-slate-500 dark:text-slate-400 mb-2">Audit Log</span>
                        <p class="text-sm text-slate-600 dark:text-slate-400">Order Date: <span class="font-mono font-black text-slate-900 dark:text-white">{{ date('d-m-Y', strtotime($order->order_date)) }}</span></p>
                        <p class="text-[11px] text-slate-500 dark:text-slate-500 mt-1">Session: <span class="font-mono">{{ sha1($order->order_id) }}</span></p>
                    </div>
                </div>

                <div class="space-y-3">
                    <div class="flex items-center justify-between gap-3">
                        <span class="block text-[10px] font-black uppercase tracking-[0.35em] text-slate-500 dark:text-slate-400">Hardware Itemizations</span>
                        <span class="hidden sm:inline-flex px-3 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest bg-blue-500/10 text-blue-600 dark:text-indigo-400 border border-blue-500/20">
                            {{ $order->items->count() }} line items
                        </span>
                    </div>

                    <div class="print-surface-strong rounded-[1.5rem] overflow-hidden print:overflow-visible border border-slate-200/70 dark:border-slate-800 bg-white/90 dark:bg-slate-900/80 shadow-lg shadow-slate-900/5 print-break-avoid">
                        <div class="print-table-wrap overflow-x-auto custom-scrollbar">
                            <table class="print-table w-full min-w-[920px] text-left text-xs border-collapse">
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
                                            <td class="py-4 px-5 font-bold text-slate-900 dark:text-white uppercase max-w-[320px] truncate print:max-w-none print:whitespace-normal print:break-words">
                                                {{ $item->product->productname ?? $item->product_id }}
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
                                                {{ $item->qty }}
                                            </td>
                                            <td class="py-4 px-5 text-right font-mono font-black text-blue-600 dark:text-indigo-400 whitespace-nowrap">
                                                Rs. {{ \App\Helpers\NumberHelper::indianFormat($item->total) }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-6 border-t border-slate-200/70 dark:border-slate-800 print-break-avoid">
                    <div class="space-y-4">
                        @if ($order->due > 0 && $order->payment_type == 3)
                            <div class="rounded-[1.5rem] p-5 bg-blue-500/5 dark:bg-indigo-500/10 border border-blue-500/15 dark:border-indigo-500/20 text-sm text-slate-600 dark:text-slate-400 leading-relaxed space-y-3">
                                <span class="block font-black text-slate-900 dark:text-white uppercase tracking-[0.25em] flex items-center gap-2 text-[10px]">
                                    <i class="fa-solid fa-bank text-blue-600 dark:text-indigo-400"></i> Direct Bank Transfer Instruction
                                </span>
                                <p>
                                    Transfer the pending amount of
                                    <span class="font-mono font-black text-blue-600 dark:text-indigo-400">Rs. {{ \App\Helpers\NumberHelper::indianFormat($order->due) }}</span>
                                    to our corporate account for instant dispatch clearance.
                                </p>
                                <div class="rounded-2xl p-4 bg-white/90 dark:bg-slate-950/50 border border-slate-200/70 dark:border-slate-800 font-mono text-sm text-slate-700 dark:text-slate-300 space-y-1">
                                    <p class="font-black text-slate-900 dark:text-white">INDIAN OVERSEAS BANK</p>
                                    <p>Branch: Kuzhithurai</p>
                                    <p>A/C: <span class="text-blue-600 dark:text-indigo-400 font-black">2869020000000349</span></p>
                                    <p>IFSC: <span class="text-blue-600 dark:text-indigo-400 font-black">IOBA0002869</span></p>
                                </div>
                            </div>
                        @else
                            <div class="rounded-[1.5rem] p-5 bg-emerald-500/5 dark:bg-emerald-500/10 border border-emerald-500/15 dark:border-emerald-500/20 text-sm text-emerald-700 dark:text-emerald-300 leading-relaxed flex items-start gap-3">
                                <i class="fa-solid fa-shield-halved text-base mt-0.5"></i>
                                <div>
                                    <span class="block font-black uppercase tracking-[0.25em] text-[10px] text-slate-900 dark:text-white">Transaction Auditor Tag</span>
                                    <span>No immediate payment action is required. Dispatch parameters are processed within 24 working hours. Contact +91 99442 28686 for tracking queries.</span>
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="rounded-[1.5rem] p-5 bg-white/90 dark:bg-slate-900/80 border border-slate-200/70 dark:border-slate-800 shadow-sm space-y-3 text-sm">
                        <div class="flex items-center justify-between gap-3 text-slate-600 dark:text-slate-400">
                            <span>Items Subtotal</span>
                            <span class="font-mono font-semibold text-slate-900 dark:text-white">Rs. {{ \App\Helpers\NumberHelper::indianFormat($order->sub_total) }}</span>
                        </div>

                        @php
                            $gstTotal = floatval($order->gstn);
                        @endphp
                        @if ($order->payment_place == 1)
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
                            <span class="text-sm font-black uppercase tracking-[0.25em] text-slate-900 dark:text-white">Grand Total</span>
                            <span class="font-mono text-xl font-black text-blue-600 dark:text-indigo-400">Rs. {{ \App\Helpers\NumberHelper::indianFormat($order->grand_total) }}</span>
                        </div>

                        <div class="border-t border-slate-200/70 dark:border-slate-800 pt-3 space-y-2 font-mono text-xs">
                            <div class="flex items-center justify-between text-emerald-600 dark:text-emerald-400">
                                <span class="font-black uppercase tracking-widest">Amount Paid</span>
                                <span>Rs. {{ \App\Helpers\NumberHelper::indianFormat($order->paid) }}</span>
                            </div>
                            <div class="flex items-center justify-between text-rose-600 dark:text-rose-400">
                                <span class="font-black uppercase tracking-widest">Balance Due</span>
                                <span>Rs. {{ \App\Helpers\NumberHelper::indianFormat($order->due) }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="text-center text-[10px] text-slate-500 dark:text-slate-400 mt-6 border-t border-slate-200/70 dark:border-slate-800 pt-4 leading-relaxed">
                    Thank you for your trust. MTL COMPUTER GARDEN's custom computing is engineered with care and delivered with compliance.
                </div>
            </div>
        </div>
    </div>

    <script>
        if (new URLSearchParams(window.location.search).get('autoprint') === '1') {
            window.addEventListener('load', () => {
                const triggerPrint = () => window.print();
                if (document.fonts && document.fonts.ready) {
                    document.fonts.ready.then(() => setTimeout(triggerPrint, 200));
                } else {
                    setTimeout(triggerPrint, 200);
                }
            }, { once: true });
        }
    </script>
</body>
</html>
