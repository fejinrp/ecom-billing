<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Sales Report - MTL Mart</title>
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
                Customer ID: #{{ $user->id }}
            </span>
        </div>
        <div class="flex items-center gap-2">
            <button onclick="window.print()" class="flex items-center gap-2 px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs uppercase tracking-wider shadow-sm transition">
                <i class="fa-solid fa-print"></i>
                Print Ledger Statement
            </button>
        </div>
    </div>

    <!-- Printable Area -->
    <div class="max-w-6xl mx-auto my-8 p-6 print:m-0 print:p-0">
        <div class="print-sheet bg-white border border-slate-200 shadow-xl rounded-3xl p-10 print:bg-white print:border-none print:shadow-none print:rounded-none">
            
            <!-- Header Slanted Bars -->
            <div class="flex h-6 mb-8 overflow-hidden rounded-lg print:rounded-none print:h-5">
                <div class="bg-emerald-500 w-1/4 -skew-x-12 origin-top -ml-2"></div>
                <div class="bg-blue-600 w-1/4 -skew-x-12 origin-top"></div>
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

            <!-- Customer & Document Meta Block -->
            <div class="grid grid-cols-2 gap-8 my-8 text-xs bg-slate-50 p-6 rounded-2xl border border-slate-200/80">
                <div class="space-y-2">
                    <span class="block text-[10px] font-extrabold text-slate-400 uppercase tracking-widest">Buyer / Client Ledger</span>
                    <div class="space-y-1">
                        <h4 class="text-sm font-bold text-slate-900 uppercase">{{ $user->uname }}</h4>
                        <p class="text-slate-600 uppercase font-medium">
                            {{ $user->billingaddress }}
                            @if(!empty($user->billingcity) || !empty($user->billingstate))
                                <br>{{ $user->billingcity ?? '' }}{{ !empty($user->billingcity) && !empty($user->billingstate) ? ', ' : '' }}{{ $user->billingstate ?? '' }}
                            @endif
                            @if(!empty($user->billingpincode))
                                &nbsp;Pin-{{ $user->billingpincode }}
                            @endif
                        </p>
                        @if ($user->contactno)
                            <p class="text-slate-700 font-medium">Contact: {{ $user->contactno }}</p>
                        @endif
                        @if ($user->gsttin)
                            <p class="text-slate-800 font-mono font-bold uppercase">GSTIN: {{ $user->gsttin }}</p>
                        @endif
                    </div>
                </div>

                <div class="text-right space-y-3">
                    <div>
                        <span class="block text-[10px] font-extrabold text-slate-400 uppercase tracking-widest">Document Type</span>
                        <h2 class="text-lg font-black text-indigo-650 font-outfit uppercase tracking-tight">
                            Customer Sales Report
                        </h2>
                    </div>
                    <div class="space-y-1 font-medium text-slate-600">
                        <p>Customer Profile ID: <span class="font-mono text-slate-900 font-bold">#{{ $user->id }}</span></p>
                        <p>Category Type: <span class="font-bold text-slate-900 uppercase">{{ $catname }}</span></p>
                    </div>
                </div>
            </div>

            <!-- Items Table -->
            <div class="my-6 overflow-x-auto print:overflow-visible">
                <table class="w-full text-left text-xs border-collapse">
                    <thead>
                        <tr class="bg-slate-50 border-y border-slate-200 text-slate-600 font-bold uppercase tracking-wider">
                            <th class="py-3 px-3 w-10 text-center">No</th>
                            <th class="py-3 px-3 w-20 text-center">B_No</th>
                            <th class="py-3 px-3 w-24 text-center">Order Date</th>
                            <th class="py-3 px-3">Product Name</th>
                            <th class="py-3 px-3 w-16 text-center">Qty</th>
                            <th class="py-3 px-3 w-28 text-right">Amount</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @php
                            $i = 1;
                            $subt = 0;
                            $grandt = 0;
                            $paidt = 0;
                            $duet = 0;
                            $instamtt = 0;
                            $shipamtt = 0;
                            $dis = 0;
                            $coin = 0;
                        @endphp

                        @forelse ($sales as $sale)
                            @php
                                $invoiceNo = ($csect === 1 || $csect === 2) ? $sale->morder_id : $sale->morderid;
                                $orderDate = ($csect === 1 || $csect === 2) ? date('d-m-Y', strtotime($sale->order_date)) : date('d-m-Y', strtotime($sale->orderdate));

                                $items = $sale->items;
                                $itemCount = count($items);

                                // Add totals
                                $instamtt += ($csect === 1 || $csect === 2) ? $sale->instamt : $sale->install;
                                $shipamtt += ($csect === 1 || $csect === 2) ? $sale->shipamt : $sale->tship;
                                $dis += $sale->discount;
                                $coin += ($csect === 1 || $csect === 2) ? $sale->pcoin : 0;
                                $grandt += ($csect === 1 || $csect === 2) ? $sale->grand_total : $sale->gamount;
                                $paidt += ($csect === 1 || $csect === 2) ? $sale->paid : $sale->pamount;
                                $duet += ($csect === 1 || $csect === 2) ? $sale->due : $sale->bamount;
                            @endphp

                            @foreach ($items as $itemIdx => $item)
                                @php
                                    $productName = $item->product->productname ?? 'N/A';
                                    $qty = ($csect === 1 || $csect === 2) ? $item->qty : $item->quantity;
                                    $rate = ($csect === 1 || $csect === 2) ? $item->total : ($item->price * $item->quantity);
                                    $subt += $rate;
                                @endphp
                                <tr class="text-slate-700 align-middle">
                                    <!-- No -->
                                    <td class="py-3 px-3 text-center font-semibold text-slate-400">
                                        @if ($itemIdx === 0)
                                            {{ $i }}
                                        @endif
                                    </td>
                                    
                                    <!-- Bill No -->
                                    <td class="py-3 px-3 text-center font-mono font-bold text-slate-800">
                                        @if ($itemIdx === 0)
                                            #{{ $invoiceNo }}
                                        @endif
                                    </td>

                                    <!-- Date -->
                                    <td class="py-3 px-3 text-center font-mono font-medium text-slate-600">
                                        @if ($itemIdx === 0)
                                            {{ $orderDate }}
                                        @endif
                                    </td>

                                    <!-- Product Name -->
                                    <td class="py-3 px-3 font-medium uppercase text-slate-800">
                                        {{ $productName }}
                                    </td>

                                    <!-- Qty -->
                                    <td class="py-3 px-3 text-center font-mono font-bold text-slate-700">
                                        {{ $qty }}
                                    </td>

                                    <!-- Amount -->
                                    <td class="py-3 px-3 text-right font-mono text-slate-900">
                                        Rs. {{ number_format($rate, 2) }}
                                    </td>
                                </tr>
                            @endforeach
                            @php
                                $i++;
                            @endphp
                        @empty
                            <tr>
                                <td colspan="6" class="py-8 text-center text-slate-500 font-medium">No sales recorded for this customer yet.</td>
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
                        <span class="block text-[10px] font-extrabold text-slate-400 uppercase tracking-widest">Customer Lifetime Summary</span>
                        <p class="text-slate-800 font-bold mt-1 font-outfit text-base">
                            {{ count($sales) }} Total Orders Recorded
                        </p>
                    </div>

                    <div class="text-slate-500 space-y-1">
                        <span class="block text-[10px] font-extrabold text-slate-400 uppercase tracking-widest">Statement Declaration</span>
                        <p>This statement constitutes a compiled ledger history of all purchases and payment balances for the selected customer profile.</p>
                    </div>
                </div>

                <!-- Right Details -->
                <div class="space-y-3 bg-slate-50/50 p-6 rounded-2xl border border-slate-200/80 print:bg-white print:border-none print:p-0 print:space-y-2">
                    <div class="flex items-center justify-between text-slate-500">
                        <span>Items Subtotal:</span>
                        <span class="font-mono font-semibold text-slate-850">Rs. {{ number_format($subt, 2) }}</span>
                    </div>

                    <div class="flex items-center justify-between text-slate-500">
                        <span>Installation Amount:</span>
                        <span class="font-mono font-semibold text-slate-850">Rs. {{ number_format($instamtt, 2) }}</span>
                    </div>

                    <div class="flex items-center justify-between text-slate-500">
                        <span>Shipping Amount:</span>
                        <span class="font-mono font-semibold text-slate-850">Rs. {{ number_format($shipamtt, 2) }}</span>
                    </div>

                    <div class="flex items-center justify-between text-slate-550 pt-2 border-t border-slate-200/60 font-semibold">
                        <span>Total Combined:</span>
                        <span class="font-mono text-slate-900">Rs. {{ number_format($subt + $instamtt + $shipamtt, 2) }}</span>
                    </div>

                    <div class="flex items-center justify-between text-amber-600 font-semibold">
                        <span>Discount Allowed:</span>
                        <span class="font-mono font-bold">- Rs. {{ number_format($dis, 2) }}</span>
                    </div>

                    @if ($coin > 0)
                        <div class="flex items-center justify-between text-indigo-600 font-semibold">
                            <span>Mcoin Redeemed:</span>
                            <span class="font-mono font-bold">- Rs. {{ number_format($coin, 2) }}</span>
                        </div>
                    @endif

                    <div class="flex items-center justify-between text-sm font-bold text-slate-900 pt-3 border-t border-slate-200">
                        <span class="font-outfit text-base">Grand Total Lifetime Purchases:</span>
                        <span class="font-mono text-lg text-indigo-600">Rs. {{ number_format($grandt, 2) }}</span>
                    </div>

                    <div class="pt-3 mt-1 border-t border-slate-200 space-y-1.5">
                        <div class="flex items-center justify-between text-xs text-emerald-600 font-semibold">
                            <span>Total Collected (Paid):</span>
                            <span class="font-mono text-sm">Rs. {{ number_format($paidt, 2) }}</span>
                        </div>
                        <div class="flex items-center justify-between text-xs text-rose-600 font-bold">
                            <span>Total Outstanding (Due):</span>
                            <span class="font-mono text-sm">Rs. {{ number_format($duet, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer Signatures -->
            <div class="flex justify-between items-center mt-12 border-t border-slate-200 pt-6 text-[10px] font-bold text-slate-500 uppercase tracking-wider">
                <div>MTL Mart Inventory Control Sheet</div>
                <div>Authorized Accountant Signatory</div>
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
