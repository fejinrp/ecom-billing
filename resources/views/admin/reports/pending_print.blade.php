<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pending Balances Report - MTL Mart</title>
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
            <button onclick="window.close()" class="px-3.5 py-2 rounded-xl text-slate-700 bg-slate-100 hover:bg-slate-200/80 transition text-xs font-bold uppercase tracking-wider flex items-center gap-2">
                <i class="fa-solid fa-arrow-left"></i>
                Close Tab
            </button>
            <span class="text-xs font-semibold px-2.5 py-1 bg-slate-100 text-slate-650 border border-slate-200 rounded-full font-mono">
                Ledger Category: {{ ($ctype === 1 || $ctype === 2) ? 'Offline Sales' : (($ctype === 4) ? 'Online Dealer' : 'Online Customer') }}
            </span>
        </div>
        <div class="flex items-center gap-2">
            <button onclick="window.print()" class="flex items-center gap-2 px-5 py-2.5 rounded-xl bg-violet-650 hover:bg-violet-750 text-white font-bold text-xs uppercase tracking-wider shadow-sm transition">
                <i class="fa-solid fa-print"></i>
                Print Statements
            </button>
        </div>
    </div>

    <!-- Printable Area -->
    <div class="max-w-6xl mx-auto my-8 p-6 print:m-0 print:p-0">
        <div class="print-sheet bg-white border border-slate-200 shadow-xl rounded-3xl p-10 print:bg-white print:border-none print:shadow-none print:rounded-none">
            
            <!-- Header Slanted Bars -->
            <div class="flex h-6 mb-8 overflow-hidden rounded-lg print:rounded-none print:h-5">
                <div class="bg-indigo-500 w-1/4 -skew-x-12 origin-top -ml-2"></div>
                <div class="bg-violet-600 w-1/4 -skew-x-12 origin-top"></div>
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

            <!-- Document Title Block -->
            <div class="flex justify-between items-center my-6 text-xs bg-slate-50 p-4 rounded-2xl border border-slate-200/80">
                <div>
                    <span class="block text-[10px] font-extrabold text-slate-400 uppercase tracking-widest">Accounts outstanding Ledger</span>
                    <h2 class="text-base font-bold text-slate-900 font-outfit uppercase">
                        PENDING BALANCES REPORT
                    </h2>
                </div>
                <div class="text-right space-y-0.5">
                    <span class="block text-[10px] font-extrabold text-slate-400 uppercase tracking-widest">Statement Bounds</span>
                    <p class="font-bold text-slate-700 font-mono">
                        {{ $startDate->format('d-m-Y') }} <span class="text-slate-400 font-sans mx-1">to</span> {{ $endDate->format('d-m-Y') }}
                    </p>
                </div>
            </div>

            <!-- Items Table -->
            <div class="my-6 overflow-x-auto print:overflow-visible">
                <table class="w-full text-left text-xs border-collapse">
                    <thead>
                        <tr class="bg-slate-50 border-y border-slate-200 text-slate-600 font-bold uppercase tracking-wider">
                            <th class="py-3 px-3 w-10 text-center">No</th>
                            <th class="py-3 px-3 w-24 text-center">Order Date</th>
                            <th class="py-3 px-3">Customer Name</th>
                            <th class="py-3 px-3">Address / Mobile</th>
                            @if ($ctype === 1 || $ctype === 2)
                                <th class="py-3 px-3 w-20 text-right">Total Amt</th>
                                <th class="py-3 px-3 w-16 text-right">Discount</th>
                                <th class="py-3 px-3 w-16 text-right">Mcoin</th>
                                <th class="py-3 px-3 w-20 text-right">Grand Amt</th>
                                <th class="py-3 px-3 w-20 text-right">Paid Amt</th>
                                <th class="py-3 px-3 w-20 text-right text-rose-600">Balance due</th>
                            @else
                                <th class="py-3 px-3 w-24 text-right">Grand Amt</th>
                                <th class="py-3 px-3 w-24 text-right">Paid Amt</th>
                                <th class="py-3 px-3 w-24 text-right text-rose-600">Balance due</th>
                            @endif
                            <th class="py-3 px-3 w-16 text-center">P_Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @php
                            $totalBal = 0;
                        @endphp
                        @forelse ($sales as $index => $sale)
                            @php
                                $orderDate = ($ctype === 1 || $ctype === 2) ? date('d-m-Y', strtotime($sale->order_date)) : date('d-m-Y', strtotime($sale->orderdate));
                                
                                $clientName = $sale->user->uname ?? ($sale->client_name ?? '');
                                $clientContact = $sale->user->billingaddress ?? ($sale->client_contact ?? '');
                                $mobile = $sale->mobile ?: ($sale->user->contactno ?? '');
                                if ($mobile) {
                                    $clientContact .= ", Mob: " . $mobile;
                                }

                                $dueAmt = ($ctype === 1 || $ctype === 2) ? $sale->due : $sale->bamount;
                                $paidAmt = ($ctype === 1 || $ctype === 2) ? $sale->paid : $sale->pamount;
                                $grandAmt = ($ctype === 1 || $ctype === 2) ? $sale->grand_total : $sale->gamount;
                                $totalBal += $dueAmt;

                                $paymentStatus = ($dueAmt > 0) ? 'Pend' : 'Paid';
                            @endphp
                            <tr class="text-slate-700 align-middle">
                                <!-- No -->
                                <td class="py-3 px-3 text-center font-semibold text-slate-400">
                                    {{ $index + 1 }}
                                </td>
                                
                                <!-- Date -->
                                <td class="py-3 px-3 text-center font-mono font-medium text-slate-600">
                                    {{ $orderDate }}
                                </td>

                                <!-- Customer Name -->
                                <td class="py-3 px-3 font-semibold text-slate-900 uppercase">
                                    {{ $clientName }}
                                </td>

                                <!-- Customer Address -->
                                <td class="py-3 px-3 text-xs text-slate-500 uppercase max-w-[200px] truncate" title="{{ $clientContact }}">
                                    {{ $clientContact }}
                                </td>

                                <!-- Column variations -->
                                @if ($ctype === 1 || $ctype === 2)
                                    <td class="py-3 px-3 text-right font-mono text-slate-600">
                                        {{ number_format($sale->total_amount, 2) }}
                                    </td>
                                    <td class="py-3 px-3 text-right font-mono text-slate-600">
                                        {{ number_format($sale->discount, 2) }}
                                    </td>
                                    <td class="py-3 px-3 text-right font-mono text-slate-600">
                                        {{ number_format($sale->pcoin, 2) }}
                                    </td>
                                    <td class="py-3 px-3 text-right font-mono text-slate-600">
                                        {{ number_format($grandAmt, 2) }}
                                    </td>
                                    <td class="py-3 px-3 text-right font-mono text-slate-600">
                                        {{ number_format($paidAmt, 2) }}
                                    </td>
                                    <td class="py-3 px-3 text-right font-mono font-bold text-rose-600">
                                        Rs. {{ number_format($dueAmt, 2) }}
                                    </td>
                                @else
                                    <td class="py-3 px-3 text-right font-mono text-slate-600">
                                        Rs. {{ number_format($grandAmt, 2) }}
                                    </td>
                                    <td class="py-3 px-3 text-right font-mono text-slate-600">
                                        Rs. {{ number_format($paidAmt, 2) }}
                                    </td>
                                    <td class="py-3 px-3 text-right font-mono font-bold text-rose-600">
                                        Rs. {{ number_format($dueAmt, 2) }}
                                    </td>
                                @endif

                                <!-- Status -->
                                <td class="py-3 px-3 text-center">
                                    <span class="px-2 py-0.5 text-[10px] font-bold rounded bg-rose-50 border border-rose-200 text-rose-600 uppercase">
                                        {{ $paymentStatus }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ ($ctype === 1 || $ctype === 2) ? 11 : 8 }}" class="py-8 text-center text-slate-500 font-medium">No pending balances matching filters.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Financial Ledgers & Summary -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 my-8 pt-6 border-t border-slate-200 text-xs">
                <!-- Left Details -->
                <div class="space-y-4">
                    <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200">
                        <span class="block text-[10px] font-extrabold text-slate-400 uppercase tracking-widest">Total Outstanding Statements</span>
                        <p class="text-slate-800 font-bold mt-1 font-outfit text-base">
                            {{ count($sales) }} Pending Ledgers Listed
                        </p>
                    </div>

                    <div class="text-slate-500 space-y-1">
                        <span class="block text-[10px] font-extrabold text-slate-400 uppercase tracking-widest">Statement Declaration</span>
                        <p>This document details outstanding accounts receivable matches generated from verified user journals.</p>
                    </div>
                </div>

                <!-- Right Details -->
                <div class="space-y-3 bg-slate-50/50 p-6 rounded-2xl border border-slate-200/80 print:bg-white print:border-none print:p-0 print:space-y-2 flex flex-col justify-center">
                    <div class="flex items-center justify-between text-base font-black text-rose-600">
                        <span class="font-outfit uppercase">Total Outstanding Receivable:</span>
                        <span class="font-mono text-xl">Rs. {{ number_format($totalBal, 2) }}</span>
                    </div>
                </div>
            </div>

            <!-- Footer Signatures -->
            <div class="flex justify-between items-center mt-12 border-t border-slate-200 pt-6 text-[10px] font-bold text-slate-500 uppercase tracking-wider">
                <div>MTL Mart Balance Ledger Inward</div>
                <div>Authorized Auditor Signatory</div>
            </div>

        </div>
    </div>

    <!-- Auto-print trigger -->
    <script>
        window.addEventListener('DOMContentLoaded', () => {
            setTimeout(() => {
                window.print();
            }, 800);
        });
    </script>
</body>
</html>
