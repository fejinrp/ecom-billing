<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Purchase Invoice Ledger - MTL Mart</title>
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                        outfit: ['Outfit', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <style>
        @media print {
            body {
                background: white !important;
                color: #0f172a !important;
            }
            .no-print {
                display: none !important;
            }
            .print-sheet {
                border: none !important;
                box-shadow: none !important;
                padding: 0 !important;
                margin: 0 !important;
                width: 100% !important;
                max-width: 100% !important;
            }
            * {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
        }
        body {
            background-color: #f8fafc;
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>
<body class="min-h-screen text-slate-800 antialiased print:bg-white print:text-slate-900">

    <!-- Floating Top Bar (Hidden on print) -->
    <div class="no-print bg-white/85 backdrop-blur-md border-b border-slate-200/80 sticky top-0 z-50 px-6 py-4 flex items-center justify-between shadow-sm">
        <div class="flex items-center gap-3">
            <x-admin.button href="{{ route('admin.purchases.index') }}" variant="secondary" icon="fa-solid fa-arrow-left" class="px-3.5 py-2 rounded-xl text-slate-700 border-none bg-slate-100 hover:bg-slate-200/80">
                Back to List
            </x-admin.button>
            <span class="text-xs font-semibold px-2.5 py-1 bg-slate-100 text-slate-600 border border-slate-200 rounded-full font-mono">
                Purchase ID: #{{ $purchase->morder_id }}
            </span>
        </div>
        <div class="flex items-center gap-2">
            <x-admin.button onclick="window.print()" variant="indigo-flat" icon="fa-solid fa-print" class="px-5 py-2.5 rounded-xl">
                Print Invoice
            </x-admin.button>
        </div>
    </div>

    <!-- Printable Area -->
    <div class="max-w-4xl mx-auto my-8 p-6 print:m-0 print:p-0">
        <div class="print-sheet bg-white border border-slate-200 shadow-xl rounded-3xl p-10 print:bg-white print:border-none print:shadow-none print:rounded-none">
            
            <!-- Header Slanted Bars -->
            <div class="flex h-6 mb-8 overflow-hidden rounded-lg print:rounded-none print:h-5">
                <div class="bg-indigo-500 w-1/4 -skew-x-12 origin-top -ml-2"></div>
                <div class="bg-purple-600 w-1/4 -skew-x-12 origin-top"></div>
                <div class="bg-amber-500 w-2/4 -skew-x-12 origin-top flex items-center justify-center -mr-2">
                    <span class="text-[10px] font-bold text-white tracking-widest font-outfit skew-x-12">WWW.MTLMART.COM</span>
                </div>
            </div>

            <!-- Company Brand Block -->
            <div class="text-center space-y-2 pb-6 border-b border-slate-200">
                <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight font-outfit uppercase">MTL COMPUTER GARDEN</h1>
                <p class="text-xs text-slate-500 font-medium">1st Floor, SUS Building, Opposite to MRF Tyres, Main Road, Marthandam, TN, India - 629154</p>
                <div class="flex justify-center gap-4 text-[10px] font-bold font-mono">
                    <span class="inline-block px-3 py-1 bg-slate-50 border border-slate-200 text-slate-600 rounded-lg">
                        GSTIN: 33AQQPJ1772L1ZG
                    </span>
                    <span class="inline-block px-3 py-1 bg-slate-50 border border-slate-200 text-slate-600 rounded-lg">
                        Mob: 9944228686
                    </span>
                </div>
            </div>

            <!-- Supplier & Document Meta block -->
            <div class="grid grid-cols-2 gap-8 my-8 text-xs">
                <!-- Supplier Details -->
                <div class="space-y-2">
                    <span class="block text-[10px] font-extrabold text-slate-400 uppercase tracking-widest">Supplier Profile</span>
                    <div class="space-y-1">
                        <h4 class="text-sm font-bold text-slate-900 uppercase">{{ $purchase->s_name }}</h4>
                        <p class="text-slate-600 whitespace-pre-line uppercase font-medium">
                            {{ $purchase->s_contact }}
                        </p>
                    </div>
                </div>

                <!-- Document Details -->
                <div class="text-right space-y-3">
                    <div>
                        <span class="block text-[10px] font-extrabold text-slate-400 uppercase tracking-widest">Document Type</span>
                        <h2 class="text-xl font-black text-indigo-600 font-outfit uppercase tracking-tight">
                            Purchase Order
                        </h2>
                    </div>
                    <div class="space-y-1 font-medium text-slate-600">
                        <p>Purchase Invoice: <span class="font-mono text-slate-900 font-bold">#{{ str_pad($purchase->morder_id, 4, '0', STR_PAD_LEFT) }}</span></p>
                        <p>Date: <span class="font-mono text-slate-900 font-bold">{{ date('d-m-Y', strtotime($purchase->porder_date)) }}</span></p>
                        <p>Recorded By: <span class="font-bold text-slate-900 uppercase">{{ $creator->username ?? 'ADMIN' }}</span></p>
                    </div>
                </div>
            </div>

            <!-- Items Table -->
            <div class="my-6 overflow-x-auto print:overflow-visible">
                <table class="w-full text-left text-xs border-collapse">
                    <thead>
                        <tr class="bg-slate-50 border-y border-slate-200 text-slate-600 font-bold uppercase tracking-wider">
                            <th class="py-3 px-4 w-12 text-center">#</th>
                            <th class="py-3 px-4">Product Name / Description</th>
                            <th class="py-3 px-4 w-24 text-center">Base Unit</th>
                            <th class="py-3 px-4 w-32 text-center">Packaging Details</th>
                            <th class="py-3 px-4 w-24 text-right">Purchase Rate</th>
                            <th class="py-3 px-4 w-20 text-center">Total Pieces</th>
                            <th class="py-3 px-4 w-28 text-right">Row Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($items as $index => $item)
                            <tr class="text-slate-700 align-middle">
                                <td class="py-3 px-4 text-center font-mono font-semibold text-slate-400">{{ $index + 1 }}</td>
                                <td class="py-3 px-4 font-semibold text-slate-900 uppercase">
                                    {{ $item->product->productname ?? 'PRODUCT ID: ' . $item->prod_id }}
                                </td>
                                <td class="py-3 px-4 text-center uppercase text-slate-500 font-medium">{{ $item->punit ?: 'PCS' }}</td>
                                <td class="py-3 px-4 text-center font-mono text-slate-500">
                                    @if ($item->pqty > 0)
                                        <span>{{ $item->tqty }} {{ $item->punit }} x {{ $item->pqty }} PCS</span>
                                    @else
                                        <span>-</span>
                                    @endif
                                </td>
                                <td class="py-3 px-4 text-right font-mono text-slate-600">
                                    Rs. {{ \App\Helpers\NumberHelper::indianFormat($item->rate) }}
                                </td>
                                <td class="py-3 px-4 text-center font-bold font-mono text-slate-700">{{ $item->qty }}</td>
                                <td class="py-3 px-4 text-right font-mono font-bold text-slate-900">
                                    Rs. {{ \App\Helpers\NumberHelper::indianFormat($item->tamount) }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Financial Breakdown -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 my-8 pt-6 border-t border-slate-200 text-xs">
                <!-- Left: Words Total & Bank Details -->
                <div class="space-y-4">
                    <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200">
                        <span class="block text-[10px] font-extrabold text-slate-400 uppercase tracking-widest">Amount in Words</span>
                        <p class="text-slate-800 font-bold mt-1 font-outfit">
                            {{ \App\Helpers\NumberHelper::convertToIndianCurrency($purchase->g_total) }}
                        </p>
                    </div>

                    <div class="text-slate-500 space-y-1">
                        <span class="block text-[10px] font-extrabold text-slate-400 uppercase tracking-widest">Store Declarations</span>
                        <p>This document acts as a verified inward inventory purchase voucher for the company warehouses.</p>
                    </div>
                </div>

                <!-- Right: Financial Breakdown -->
                <div class="space-y-3 bg-slate-50/50 p-6 rounded-2xl border border-slate-200/80 print:bg-white print:border-none print:p-0 print:space-y-2">
                    <div class="flex items-center justify-between text-slate-500">
                        <span>Subtotal Amount:</span>
                        <span class="font-mono font-semibold text-slate-800">Rs. {{ \App\Helpers\NumberHelper::indianFormat($purchase->sub_total) }}</span>
                    </div>

                    @if ($purchase->discount > 0)
                        <div class="flex items-center justify-between text-amber-600 font-medium">
                            <span>Supplier Discount:</span>
                            <span class="font-mono font-bold">- Rs. {{ \App\Helpers\NumberHelper::indianFormat($purchase->discount) }}</span>
                        </div>
                    @endif

                    <div class="flex items-center justify-between text-sm font-bold text-slate-900 pt-3 border-t border-slate-250/60 print:border-slate-200">
                        <span class="font-outfit text-base">Grand Total:</span>
                        <span class="font-mono text-lg text-indigo-600">Rs. {{ \App\Helpers\NumberHelper::indianFormat($purchase->g_total) }}</span>
                    </div>

                    <!-- Payment Details (Paid vs Balance) -->
                    <div class="pt-3 mt-1 border-t border-slate-200 space-y-1.5">
                        <div class="flex items-center justify-between text-xs text-emerald-600 font-semibold">
                            <span>Amount Paid:</span>
                            <span class="font-mono text-sm">Rs. {{ \App\Helpers\NumberHelper::indianFormat($purchase->ppaid) }}</span>
                        </div>
                        <div class="flex items-center justify-between text-xs text-rose-600 font-bold">
                            <span>Balance Due Ledger:</span>
                            <span class="font-mono text-sm">Rs. {{ \App\Helpers\NumberHelper::indianFormat($purchase->pbal) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Signatures -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mt-12 border-t border-slate-200 pt-6 text-xs">
                <!-- Terms & Conditions -->
                <div class="text-[10px] text-slate-500 space-y-1">
                    <h5 class="font-bold text-slate-700 uppercase tracking-wider">Accounting and Stock Ledger Inward Details</h5>
                    <p>1. Product physical inventory counts have been successfully adjusted in backend databases.</p>
                    <p>2. Payment ledgers and outstanding supplier balance accounts updated accordingly.</p>
                </div>

                <!-- Signatures -->
                <div class="flex flex-col items-center justify-end text-center space-y-3 md:items-end pt-8">
                    <div class="w-48 border-t border-slate-200 pt-2 text-[10px] font-bold text-slate-500 uppercase tracking-wider">
                        Authorized Accountant
                    </div>
                </div>
            </div>

            <!-- Footer slant -->
            <div class="flex h-2 mt-12 overflow-hidden rounded-lg print:hidden">
                <div class="bg-amber-500 w-2/4 skew-y-6"></div>
                <div class="bg-indigo-600 w-1/4 skew-y-6"></div>
                <div class="bg-purple-500 w-1/4 skew-y-6"></div>
            </div>

            <div class="text-center text-[10px] text-slate-400 mt-6 border-t border-slate-100 pt-4">
                MTL COMPUTER GARDEN Inventory Control Sheet. All rights reserved.
            </div>

        </div>
    </div>

    <!-- Auto print logic if requested -->
    <script>
        window.addEventListener('DOMContentLoaded', () => {
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.has('autoprint')) {
                setTimeout(() => {
                    window.print();
                }, 500);
            }
        });
    </script>
</body>
</html>
