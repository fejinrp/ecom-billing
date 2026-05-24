@php
    use App\Helpers\NumberHelper;
@endphp

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stock Report - MTL Mart</title>
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
            <span class="text-xs font-semibold px-2.5 py-1 bg-slate-100 text-slate-600 border border-slate-200 rounded-full font-mono">
                Report Type: {{ $stockType }}
            </span>
        </div>
        <div class="flex items-center gap-2">
            <button onclick="window.print()" class="flex items-center gap-2 px-5 py-2.5 rounded-xl bg-indigo-650 hover:bg-indigo-700 text-white font-bold text-xs uppercase tracking-wider shadow-sm transition">
                <i class="fa-solid fa-print"></i>
                Print Report
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

            <!-- Report Meta Block -->
            <div class="flex justify-between items-center my-6 text-xs bg-slate-50 p-4 rounded-2xl border border-slate-200/80">
                <div>
                    <span class="block text-[10px] font-extrabold text-slate-400 uppercase tracking-widest">Inventory Sheet</span>
                    <h2 class="text-base font-bold text-slate-900 font-outfit uppercase">
                        {{ $stockType }}
                    </h2>
                </div>
                <div class="text-right space-y-0.5">
                    <span class="block text-[10px] font-extrabold text-slate-400 uppercase tracking-widest">Report Date</span>
                    <p class="font-bold text-slate-700 font-mono">
                        {{ now('Asia/Kolkata')->format('d-m-Y h:i A') }}
                    </p>
                </div>
            </div>

            <!-- Table -->
            <div class="my-6 overflow-x-auto print:overflow-visible">
                <table class="w-full text-left text-xs border-collapse">
                    <thead>
                        <tr class="bg-slate-50 border-y border-slate-200 text-slate-600 font-bold uppercase tracking-wider">
                            <th class="py-3 px-3 w-10 text-center">No</th>
                            <th class="py-3 px-3 w-24">P_Code</th>
                            <th class="py-3 px-3">Product Name</th>
                            <th class="py-3 px-3">Brand Name</th>
                            <th class="py-3 px-3 w-14 text-center">Unit</th>
                            
                            @if($isStockLevelReport)
                                <th class="py-3 px-3 w-20 text-right">GPrice</th>
                                <th class="py-3 px-3 w-20 text-right">SPrice</th>
                                <th class="py-3 px-3 w-20 text-right">DPrice</th>
                                <th class="py-3 px-3 w-20 text-right">CPrice</th>
                            @else
                                <th class="py-3 px-3 w-20 text-right">Price</th>
                            @endif

                            <th class="py-3 px-3 w-14 text-center">Qty</th>
                            <th class="py-3 px-3 w-28 text-right">Total Value</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @php
                            $i = 1;
                            $overallTotal = 0;
                            $currentCatId = null;
                        @endphp

                        @forelse ($products as $prod)
                            @php
                                $unitStr = 'No';
                                if ($prod->unit == 1) { $unitStr = 'No'; }
                                elseif ($prod->unit == 2) { $unitStr = 'M'; }
                                elseif ($prod->unit == 4) { $unitStr = 'Lt'; }
                                else { $unitStr = 'Pk'; }

                                $productTotal = $prod->prate * $prod->tqty;
                                $overallTotal += $productTotal;
                            @endphp

                            <!-- Category break row if category changes -->
                            @if ($prod->catid !== $currentCatId)
                                @php
                                    $currentCatId = $prod->catid;
                                    $catName = $prod->category->cat_name ?? 'UNCLASSIFIED CATEGORY';
                                @endphp
                                <tr class="bg-indigo-50/50 print:bg-slate-50 border-y border-slate-100">
                                    <td colspan="{{ $isStockLevelReport ? 12 : 8 }}" class="py-2.5 px-3 text-center font-outfit font-extrabold text-[11px] text-indigo-700 tracking-widest uppercase">
                                        {{ $catName }}
                                    </td>
                                </tr>
                            @endif

                            <tr class="text-slate-700 align-middle">
                                <!-- Index -->
                                <td class="py-3 px-3 text-center font-semibold text-slate-400 font-mono">{{ $i }}</td>
                                
                                <!-- PCode -->
                                <td class="py-3 px-3 font-mono font-bold text-slate-800 uppercase">{{ $prod->pcode }}</td>
                                
                                <!-- Product Name -->
                                <td class="py-3 px-3 font-semibold text-slate-900 uppercase">{{ $prod->productname }}</td>
                                
                                <!-- Brand Name -->
                                <td class="py-3 px-3 text-slate-500 uppercase">{{ $prod->brand->brand_name ?? 'N/A' }}</td>
                                
                                <!-- Unit -->
                                <td class="py-3 px-3 text-center font-medium">{{ $unitStr }}</td>
                                
                                <!-- Prices -->
                                @if($isStockLevelReport)
                                    <td class="py-3 px-3 text-right font-mono">Rs. {{ number_format($prod->prate, 2) }}</td>
                                    <td class="py-3 px-3 text-right font-mono">Rs. {{ number_format($prod->sdprice, 2) }}</td>
                                    <td class="py-3 px-3 text-right font-mono">Rs. {{ number_format($prod->dprice, 2) }}</td>
                                    <td class="py-3 px-3 text-right font-mono">Rs. {{ number_format($prod->cprice, 2) }}</td>
                                @else
                                    <td class="py-3 px-3 text-right font-mono">Rs. {{ number_format($prod->prate, 2) }}</td>
                                @endif

                                <!-- Qty -->
                                <td class="py-3 px-3 text-center font-mono font-bold {{ $prod->tqty <= 5 ? 'text-rose-600' : 'text-slate-750' }}">
                                    {{ $prod->tqty }}
                                </td>

                                <!-- Total Value -->
                                <td class="py-3 px-3 text-right font-mono font-bold text-slate-900">
                                    Rs. {{ number_format($productTotal, 2) }}
                                </td>
                            </tr>
                            @php
                                $i++;
                            @endphp
                        @empty
                            <tr>
                                <td colspan="{{ $isStockLevelReport ? 12 : 8 }}" class="py-8 text-center text-slate-500 font-medium">No stock records matching criteria.</td>
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
                        <span class="block text-[10px] font-extrabold text-slate-400 uppercase tracking-widest font-outfit">Total Stock Assets Value (In Words)</span>
                        <p class="text-indigo-700 font-extrabold mt-1 font-outfit text-sm">
                            {{ NumberHelper::convertToIndianCurrency($overallTotal) }}
                        </p>
                    </div>

                    <div class="text-slate-500 space-y-1">
                        <span class="block text-[10px] font-extrabold text-slate-400 uppercase tracking-widest font-outfit">Disclaimer</span>
                        <p>This report compiles physical counts stored in active logs and serves as the authorized balance list sheet.</p>
                    </div>
                </div>

                <!-- Right Details -->
                <div class="flex flex-col justify-center items-end bg-slate-50 p-6 rounded-2xl border border-slate-200/80 print:bg-white print:border-none print:p-0">
                    <span class="text-[10px] font-extrabold text-slate-450 uppercase tracking-widest font-outfit">Aggregated Inventory Value</span>
                    <h3 class="font-mono text-2xl font-black text-indigo-700 mt-1">
                        Rs. {{ number_format($overallTotal, 2) }}
                    </h3>
                </div>
            </div>

            <!-- Footer Signatures -->
            <div class="flex justify-between items-center mt-12 border-t border-slate-200 pt-6 text-[10px] font-bold text-slate-500 uppercase tracking-wider">
                <div>MTL Mart Inventory Control Sheet</div>
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
