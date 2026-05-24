@php
    use App\Helpers\NumberHelper;
@endphp

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profit & Loss Statement - MTL Mart</title>
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
                P&L Statement: {{ $startDate->format('d-m-Y') }} to {{ $endDate->format('d-m-Y') }}
            </span>
        </div>
        <div class="flex items-center gap-2">
            <button onclick="window.print()" class="flex items-center gap-2 px-5 py-2.5 rounded-xl bg-indigo-650 hover:bg-indigo-700 text-white font-bold text-xs uppercase tracking-wider shadow-sm transition">
                <i class="fa-solid fa-print"></i>
                Print Sheet
            </button>
        </div>
    </div>

    <!-- Printable Area -->
    <div class="max-w-4xl mx-auto my-8 p-6 print:m-0 print:p-0">
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

            <!-- Report Meta Block -->
            <div class="flex justify-between items-center my-6 text-xs bg-slate-50 p-4 rounded-2xl border border-slate-200/80">
                <div>
                    <span class="block text-[10px] font-extrabold text-slate-400 uppercase tracking-widest">Financial Statement</span>
                    <h2 class="text-base font-bold text-slate-900 font-outfit uppercase">
                        PROFIT & LOSS AUDIT LEDGER
                    </h2>
                </div>
                <div class="text-right space-y-0.5">
                    <span class="block text-[10px] font-extrabold text-slate-400 uppercase tracking-widest">Date Range</span>
                    <p class="font-bold text-slate-700 font-mono">
                        {{ $startDate->format('d-m-Y') }} <span class="text-slate-450 font-sans mx-1">to</span> {{ $endDate->format('d-m-Y') }}
                    </p>
                </div>
            </div>

            <!-- Table 1: Detailed Sales Ledger -->
            <div class="my-6 space-y-2">
                <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider font-outfit">1. Aggregated Operational Sales Ledger</h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead>
                            <tr class="bg-slate-50 border-y border-slate-200 text-slate-600 font-bold uppercase tracking-wider">
                                <th class="py-2.5 px-3 w-10 text-center">No</th>
                                <th class="py-2.5 px-3">Sales Channels Description</th>
                                <th class="py-2.5 px-3 w-28 text-right">Gross Sales</th>
                                <th class="py-2.5 px-3 w-28 text-right">Cash Received</th>
                                <th class="py-2.5 px-3 w-28 text-right">Outstanding Balance</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <!-- Retail Customer -->
                            <tr class="text-slate-700 align-middle">
                                <td class="py-2.5 px-3 text-center font-semibold text-slate-400 font-mono">1</td>
                                <td class="py-2.5 px-3 font-semibold text-slate-800 uppercase">Local Customer Retail</td>
                                <td class="py-2.5 px-3 text-right font-mono">Rs. {{ number_format($CData->gt, 2) }}</td>
                                <td class="py-2.5 px-3 text-right font-mono text-emerald-600">Rs. {{ number_format($CData->pa, 2) }}</td>
                                <td class="py-2.5 px-3 text-right font-mono text-rose-600 font-medium">Rs. {{ number_format($CData->du, 2) }}</td>
                            </tr>
                            <!-- Trade Dealer -->
                            <tr class="text-slate-700 align-middle">
                                <td class="py-2.5 px-3 text-center font-semibold text-slate-400 font-mono">2</td>
                                <td class="py-2.5 px-3 font-semibold text-slate-800 uppercase">Local Dealer Trade</td>
                                <td class="py-2.5 px-3 text-right font-mono">Rs. {{ number_format($DData->gt, 2) }}</td>
                                <td class="py-2.5 px-3 text-right font-mono text-emerald-600">Rs. {{ number_format($DData->pa, 2) }}</td>
                                <td class="py-2.5 px-3 text-right font-mono text-rose-600 font-medium">Rs. {{ number_format($DData->du, 2) }}</td>
                            </tr>
                            <!-- Online Customer -->
                            <tr class="text-slate-700 align-middle">
                                <td class="py-2.5 px-3 text-center font-semibold text-slate-400 font-mono">3</td>
                                <td class="py-2.5 px-3 font-semibold text-slate-800 uppercase">Online Customer E-Retail</td>
                                <td class="py-2.5 px-3 text-right font-mono">Rs. {{ number_format($OCData->gt, 2) }}</td>
                                <td class="py-2.5 px-3 text-right font-mono text-emerald-600">Rs. {{ number_format($OCData->pa, 2) }}</td>
                                <td class="py-2.5 px-3 text-right font-mono text-rose-600 font-medium">Rs. {{ number_format($OCData->du, 2) }}</td>
                            </tr>
                            <!-- Online Dealer -->
                            <tr class="text-slate-700 align-middle">
                                <td class="py-2.5 px-3 text-center font-semibold text-slate-400 font-mono">4</td>
                                <td class="py-2.5 px-3 font-semibold text-slate-800 uppercase">Online Dealer E-Wholesale</td>
                                <td class="py-2.5 px-3 text-right font-mono">Rs. {{ number_format($ODData->gt, 2) }}</td>
                                <td class="py-2.5 px-3 text-right font-mono text-emerald-600">Rs. {{ number_format($ODData->pa, 2) }}</td>
                                <td class="py-2.5 px-3 text-right font-mono text-rose-600 font-medium">Rs. {{ number_format($ODData->du, 2) }}</td>
                            </tr>

                            @php
                                $tgAmount = $CData->gt + $DData->gt + $OCData->gt + $ODData->gt;
                                $tpAmount = $CData->pa + $DData->pa + $OCData->pa + $ODData->pa;
                                $tbAmount = $CData->du + $DData->du + $OCData->du + $ODData->du;
                            @endphp

                            <!-- Subtotal Sales -->
                            <tr class="bg-slate-50 border-t border-slate-200 font-bold text-slate-900">
                                <td colspan="2" class="py-3 px-3 text-right font-outfit uppercase">Total Accumulated Sales:</td>
                                <td class="py-3 px-3 text-right font-mono">Rs. {{ number_format($tgAmount, 2) }}</td>
                                <td class="py-3 px-3 text-right font-mono text-emerald-600">Rs. {{ number_format($tpAmount, 2) }}</td>
                                <td class="py-3 px-3 text-right font-mono text-rose-600">Rs. {{ number_format($tbAmount, 2) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Table 2: Purchases and Expenses -->
            <div class="my-6 space-y-2">
                <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider font-outfit">2. Procurement and Operating Costs</h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead>
                            <tr class="bg-slate-50 border-y border-slate-200 text-slate-600 font-bold uppercase tracking-wider">
                                <th class="py-2.5 px-3 w-10 text-center">No</th>
                                <th class="py-2.5 px-3">Expense Category</th>
                                <th class="py-2.5 px-3 w-28 text-right">Gross Total</th>
                                <th class="py-2.5 px-3 w-28 text-right">Paid Outflow</th>
                                <th class="py-2.5 px-3 w-28 text-right">Outstanding Credit</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <!-- Purchase Orders -->
                            <tr class="text-slate-700 align-middle">
                                <td class="py-2.5 px-3 text-center font-semibold text-slate-400 font-mono">1</td>
                                <td class="py-2.5 px-3 font-semibold text-slate-800 uppercase">Vendor Purchases (Raw Stock)</td>
                                <td class="py-2.5 px-3 text-right font-mono">Rs. {{ number_format($PData->gt, 2) }}</td>
                                <td class="py-2.5 px-3 text-right font-mono text-indigo-600 font-semibold">Rs. {{ number_format($PData->pa, 2) }}</td>
                                <td class="py-2.5 px-3 text-right font-mono text-slate-500 font-medium">Rs. {{ number_format($PData->du, 2) }}</td>
                            </tr>
                            <!-- Operational Expenses -->
                            <tr class="text-slate-700 align-middle">
                                <td class="py-2.5 px-3 text-center font-semibold text-slate-400 font-mono">2</td>
                                <td class="py-2.5 px-3 font-semibold text-slate-800 uppercase">Operational Expenses (Salaries / Bills)</td>
                                <td class="py-2.5 px-3 text-right font-mono">Rs. {{ number_format($EData->gt, 2) }}</td>
                                <td class="py-2.5 px-3 text-right font-mono text-indigo-600 font-semibold">Rs. {{ number_format($EData->gt, 2) }}</td>
                                <td class="py-2.5 px-3 text-right font-mono text-slate-500 font-medium">Rs. 0.00</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Balance T-Account (Income vs Cost outflow) -->
            <div class="my-8 space-y-2">
                <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider font-outfit">3. Double-Entry Profit Matrix</h3>
                
                <div class="grid grid-cols-2 border border-slate-200 rounded-3xl overflow-hidden text-xs">
                    <!-- Left: Income Inflow -->
                    <div class="border-r border-slate-200 divide-y divide-slate-200">
                        <div class="bg-emerald-50/50 p-4 font-outfit font-extrabold text-emerald-800 uppercase tracking-wider text-center border-b border-slate-200">
                            Cash Inflow (Income Ledger)
                        </div>
                        <div class="p-4 flex justify-between items-center bg-white">
                            <span class="font-semibold text-slate-750 uppercase">Accumulated Paid Sales:</span>
                            <span class="font-mono font-bold text-slate-900">Rs. {{ number_format($tpAmount, 2) }}</span>
                        </div>
                        <div class="p-4 flex justify-between items-center bg-slate-50/50 font-bold">
                            <span class="uppercase">Total Inflow Value:</span>
                            <span class="font-mono text-emerald-600">Rs. {{ number_format($tpAmount, 2) }}</span>
                        </div>
                    </div>

                    <!-- Right: Cost Outflow -->
                    <div class="divide-y divide-slate-200">
                        <div class="bg-indigo-50/50 p-4 font-outfit font-extrabold text-indigo-800 uppercase tracking-wider text-center border-b border-slate-200">
                            Cash Outflow (Cost Ledger)
                        </div>
                        <div class="p-3.5 flex justify-between items-center bg-white">
                            <span class="font-semibold text-slate-750 uppercase">Paid Purchases:</span>
                            <span class="font-mono font-bold text-slate-900">Rs. {{ number_format($PData->pa, 2) }}</span>
                        </div>
                        <div class="p-3.5 flex justify-between items-center bg-white">
                            <span class="font-semibold text-slate-750 uppercase">Operating Expenses:</span>
                            <span class="font-mono font-bold text-slate-900">Rs. {{ number_format($EData->gt, 2) }}</span>
                        </div>
                        <div class="p-4 flex justify-between items-center bg-slate-50/50 font-bold">
                            <span class="uppercase">Total Outflow Value:</span>
                            <span class="font-mono text-indigo-650">Rs. {{ number_format($PData->pa + $EData->gt, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- P&L Aggregation Box -->
            @php
                $Gp = $tpAmount - $PData->pa - $EData->gt;
                $isProfit = $Gp > 0;
                $absGp = abs($Gp);
            @endphp

            <div class="my-8 p-8 rounded-3xl border {{ $isProfit ? 'bg-emerald-500/10 border-emerald-500/20' : 'bg-rose-500/10 border-rose-500/20' }} flex flex-col items-center justify-center space-y-4">
                <span class="text-[10px] font-extrabold {{ $isProfit ? 'text-emerald-600' : 'text-rose-600' }} uppercase tracking-widest font-outfit">Net Profit & Loss Margin</span>
                
                <h2 class="text-3xl font-black font-outfit tracking-tight uppercase flex items-center gap-3 {{ $isProfit ? 'text-emerald-700 animate-pulse' : 'text-rose-700' }}">
                    <i class="fa-solid {{ $isProfit ? 'fa-circle-arrow-up' : 'fa-circle-arrow-down' }}"></i>
                    {{ $isProfit ? 'PROFIT' : 'LOSS' }}: Rs. {{ number_format($absGp, 2) }}
                </h2>

                <p class="font-outfit font-extrabold text-xs uppercase {{ $isProfit ? 'text-emerald-750' : 'text-rose-750' }} text-center max-w-lg">
                    {{ NumberHelper::convertToIndianCurrency($absGp) }} {{ $isProfit ? '' : 'Negative Outstanding' }}
                </p>
            </div>

            <!-- Footer Signatures -->
            <div class="flex justify-between items-center mt-12 border-t border-slate-200 pt-6 text-[10px] font-bold text-slate-500 uppercase tracking-wider">
                <div>MTL Mart Corporate Financial Sheet</div>
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
