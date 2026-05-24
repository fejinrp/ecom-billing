<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Document - MTL Mart</title>
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
            <x-admin.button href="{{ route('admin.quotations.index') }}" variant="secondary" icon="fa-solid fa-arrow-left" class="px-3.5 py-2 rounded-xl text-slate-700 border-none bg-slate-100 hover:bg-slate-200/80">
                Back to List
            </x-admin.button>
            <span class="text-xs font-semibold px-2.5 py-1 bg-slate-100 text-slate-600 border border-slate-200 rounded-full font-mono">
                Quote ID: #{{ $quotation->morder_id }}
            </span>
        </div>
        <div class="flex items-center gap-2">
            <x-admin.button onclick="window.print()" variant="indigo-flat" icon="fa-solid fa-print" class="px-5 py-2.5 rounded-xl">
                Print Document
            </x-admin.button>
        </div>
    </div>

    <!-- Printable Area -->
    <div class="max-w-4xl mx-auto my-8 p-6 print:m-0 print:p-0">
        <div class="print-sheet bg-white border border-slate-200 shadow-xl rounded-3xl p-10 print:bg-white print:border-none print:shadow-none print:rounded-none">
            
            <!-- Header Slanted Bars (Vibrant Legacy Representation) -->
            <div class="flex h-6 mb-8 overflow-hidden rounded-lg print:rounded-none print:h-5">
                <div class="bg-emerald-500 w-1/4 -skew-x-12 origin-top -ml-2"></div>
                <div class="bg-blue-600 w-1/4 -skew-x-12 origin-top"></div>
                <div class="bg-amber-500 w-2/4 -skew-x-12 origin-top flex items-center justify-center -mr-2">
                    <span class="text-[10px] font-bold text-white tracking-widest font-outfit skew-x-12">WWW.MTLMART.COM</span>
                </div>
            </div>

            <!-- Company Brand Block -->
            <div class="text-center space-y-2 pb-6 border-b border-slate-200">
                <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight font-outfit uppercase">MTL DISTRIBUTORS</h1>
                <p class="text-xs text-slate-500 font-medium">SUS Building, Opposite to MRF Tyres, Main Road, Marthandam, TN, India</p>
                <div class="inline-block px-3 py-1 bg-slate-50 border border-slate-200 text-slate-600 font-mono text-[10px] font-bold rounded-lg">
                    GSTIN: 33AQQPJ1772L1ZG
                </div>
            </div>

            <!-- Customer & Document Meta block -->
            <div class="grid grid-cols-2 gap-8 my-8 text-xs">
                <!-- Customer Details -->
                <div class="space-y-2">
                    <span class="block text-[10px] font-extrabold text-slate-400 uppercase tracking-widest">Bill To</span>
                    <div class="space-y-1">
                        <h4 class="text-sm font-bold text-slate-900 uppercase">{{ $quotation->client_name }}</h4>
                        <p class="text-slate-600 whitespace-pre-line uppercase font-medium">{{ $quotation->client_contact }}</p>
                        @if ($quotation->mobile)
                            <p class="text-slate-700 font-medium mt-1">Contact: {{ $quotation->mobile }}</p>
                        @endif
                        @if ($quotation->gsttin)
                            <p class="text-slate-800 font-mono font-bold uppercase mt-0.5">GSTIN: {{ $quotation->gsttin }}</p>
                        @endif
                    </div>
                </div>

                <!-- Document Details -->
                <div class="text-right space-y-3">
                    <div>
                        <span class="block text-[10px] font-extrabold text-slate-400 uppercase tracking-widest">Document Type</span>
                        <h2 class="text-xl font-black text-indigo-600 font-outfit uppercase tracking-tight">
                            @if ($quotation->qtype == 1)
                                Proforma Invoice
                            @elseif ($quotation->qtype == 2)
                                Cost Estimate
                            @else
                                Sales Quotation
                            @endif
                        </h2>
                    </div>
                    <div class="space-y-1 font-medium text-slate-600">
                        <p>Date: <span class="font-mono text-slate-900 font-bold">{{ date('d-m-Y', strtotime($quotation->order_date)) }}</span></p>
                        @if ($quotation->shipamt)
                            <p>Valid Until: <span class="font-bold text-slate-900">{{ $quotation->shipamt }} Days</span></p>
                        @endif
                        <p>Doc No: <span class="font-mono font-bold text-indigo-600">
                            @php
                                $year = date('Y', strtotime($quotation->order_date));
                                if ($quotation->qtype == 1 || $quotation->qtype == 2) {
                                    echo 'Quot/'.$year.'/'.$quotation->morder_id;
                                } elseif ($quotation->qtype == 3) {
                                    echo 'Bill/'.$year.'/'.$quotation->morder_id;
                                } else {
                                    echo 'Est/'.$year.'/'.$quotation->morder_id;
                                }
                            @endphp
                        </span></p>
                        @if ($quotation->qstate)
                            @php
                                $stateGst = \App\Models\Stategst::find($quotation->qstate);
                            @endphp
                            @if ($stateGst)
                                <p>Place of Sale: <span class="uppercase text-slate-900 font-semibold">{{ $stateGst->sname }} ({{ $stateGst->scode }})</span></p>
                            @endif
                        @endif
                    </div>
                </div>
            </div>

            <!-- Items Table -->
            <div class="my-6 overflow-x-auto print:overflow-visible">
                <table class="w-full text-left text-xs border-collapse">
                    <thead>
                        <tr class="bg-slate-50 border-y border-slate-200 text-slate-600 font-bold uppercase tracking-wider">
                            <th class="py-3 px-4 w-12 text-center">#</th>
                            <th class="py-3 px-4">Item Name / Description</th>
                            <th class="py-3 px-4 w-24 text-center">HSN/SAC</th>
                            <th class="py-3 px-4 w-20 text-center">GST %</th>
                            <th class="py-3 px-4 w-24 text-right">Basic Rate</th>
                            <th class="py-3 px-4 w-24 text-right">GST Portion</th>
                            <th class="py-3 px-4 w-16 text-center">Qty</th>
                            <th class="py-3 px-4 w-28 text-right">Total Amount</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($quotation->items as $index => $item)
                            <tr class="text-slate-700 align-middle">
                                <td class="py-3 px-4 text-center font-mono font-semibold text-slate-400">{{ $index + 1 }}</td>
                                <td class="py-3 px-4 font-semibold text-slate-900 uppercase">{{ $item->product_id }}</td>
                                <td class="py-3 px-4 text-center font-mono text-slate-500">{{ $item->hsnsan ?: '-' }}</td>
                                <td class="py-3 px-4 text-center font-mono font-medium text-slate-650">{{ $item->gst }}%</td>
                                <td class="py-3 px-4 text-right font-mono text-slate-600">
                                    {{ \App\Helpers\NumberHelper::indianFormat($item->rate) }}
                                </td>
                                <td class="py-3 px-4 text-right font-mono text-slate-500">
                                    {{ \App\Helpers\NumberHelper::indianFormat($item->gstr ?: 0) }}
                                </td>
                                <td class="py-3 px-4 text-center font-bold font-mono text-slate-700">{{ $item->qty }}</td>
                                <td class="py-3 px-4 text-right font-mono font-bold text-slate-900">
                                    Rs. {{ \App\Helpers\NumberHelper::indianFormat($item->total) }}
                                </td>
                            </tr>
                        @endforeach

                        <!-- Extra Charges (Installation/Shipping) if any -->
                        @if ($quotation->instamt > 0)
                            <tr class="text-slate-700 align-middle">
                                <td class="py-3 px-4 text-center font-mono font-semibold text-slate-400">{{ count($quotation->items) + 1 }}</td>
                                <td class="py-3 px-4 font-semibold text-slate-900 uppercase">INSTALLATION & SERVICE CHARGES</td>
                                <td class="py-3 px-4 text-center font-mono text-slate-500">-</td>
                                <td class="py-3 px-4 text-center font-mono text-slate-500">-</td>
                                <td class="py-3 px-4 text-right font-mono text-slate-500">-</td>
                                <td class="py-3 px-4 text-right font-mono text-slate-500">-</td>
                                <td class="py-3 px-4 text-center font-bold font-mono text-slate-700">1</td>
                                <td class="py-3 px-4 text-right font-mono font-bold text-slate-900">
                                    Rs. {{ \App\Helpers\NumberHelper::indianFormat($quotation->instamt) }}
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>

            <!-- Tax Ledgers & Breakdown -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 my-8 pt-6 border-t border-slate-200 text-xs">
                <!-- Left: Words Total & Bank Details -->
                <div class="space-y-4">
                    <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200">
                        <span class="block text-[10px] font-extrabold text-slate-400 uppercase tracking-widest">Amount in Words</span>
                        <p class="text-slate-800 font-bold mt-1 font-outfit">
                            {{ \App\Helpers\NumberHelper::convertToIndianCurrency($quotation->grand_total) }}
                        </p>
                    </div>

                    <div class="text-slate-500 space-y-1">
                        <span class="block text-[10px] font-extrabold text-slate-400 uppercase tracking-widest">Bank Remittance</span>
                        <p class="font-bold text-slate-800">Indian Overseas Bank, Kuzhithurai Branch</p>
                        <p class="font-mono text-slate-650">Account No: <span class="text-slate-900 font-semibold">2869020000000349</span></p>
                        <p class="font-mono text-slate-650">IFSC Code: <span class="text-slate-900 font-semibold">IOBA0002869</span></p>
                    </div>
                </div>

                <!-- Right: Financial Breakdown -->
                <div class="space-y-3 bg-slate-50/50 p-6 rounded-2xl border border-slate-200/80 print:bg-white print:border-none print:p-0 print:space-y-2">
                    <div class="flex items-center justify-between text-slate-500">
                        <span>Subtotal Amount:</span>
                        <span class="font-mono font-semibold text-slate-800">Rs. {{ \App\Helpers\NumberHelper::indianFormat($quotation->sub_total) }}</span>
                    </div>

                    @php
                        $scode = 0;
                        if ($quotation->qstate) {
                            $stateGst = \App\Models\Stategst::find($quotation->qstate);
                            if ($stateGst) {
                                $scode = (int)$stateGst->scode;
                            }
                        }
                        $gstTotal = floatval($quotation->gstn);
                    @endphp

                    @if ($scode === 33)
                        <!-- Tamil Nadu Place of Sale: CGST & SGST Breakdown -->
                        <div class="flex items-center justify-between text-slate-550 text-xs">
                            <span>Central GST (CGST @ {{ count($quotation->items) ? $quotation->items->avg('gst') / 2 : 9 }}% Avg):</span>
                            <span class="font-mono text-slate-700">Rs. {{ \App\Helpers\NumberHelper::indianFormat($gstTotal / 2) }}</span>
                        </div>
                        <div class="flex items-center justify-between text-slate-550 text-xs">
                            <span>State GST (SGST @ {{ count($quotation->items) ? $quotation->items->avg('gst') / 2 : 9 }}% Avg):</span>
                            <span class="font-mono text-slate-700">Rs. {{ \App\Helpers\NumberHelper::indianFormat($gstTotal / 2) }}</span>
                        </div>
                    @else
                        <!-- Integrated GST for out of state -->
                        <div class="flex items-center justify-between text-slate-550 text-xs">
                            <span>Integrated GST (IGST @ {{ count($quotation->items) ? $quotation->items->avg('gst') : 18 }}% Avg):</span>
                            <span class="font-mono text-slate-700">Rs. {{ \App\Helpers\NumberHelper::indianFormat($gstTotal) }}</span>
                        </div>
                    @endif

                    @if ($quotation->discount > 0)
                        <div class="flex items-center justify-between text-amber-600 font-medium">
                            <span>Special Discount:</span>
                            <span class="font-mono font-bold">- Rs. {{ \App\Helpers\NumberHelper::indianFormat($quotation->discount) }}</span>
                        </div>
                    @endif

                    <div class="flex items-center justify-between text-slate-500 pt-2 border-t border-slate-200/60">
                        <span>Delivery / Shipping:</span>
                        <span class="font-mono text-slate-800">
                            @if ($quotation->shipamt > 0)
                                Rs. {{ \App\Helpers\NumberHelper::indianFormat($quotation->shipamt) }}
                            @else
                                Free Delivery
                            @endif
                        </span>
                    </div>

                    <div class="flex items-center justify-between text-sm font-bold text-slate-900 pt-3 border-t border-slate-200">
                        <span class="font-outfit text-base">Grand Total (Inclusive of Taxes):</span>
                        <span class="font-mono text-lg text-indigo-600">Rs. {{ \App\Helpers\NumberHelper::indianFormat($quotation->grand_total) }}</span>
                    </div>
                </div>
            </div>

            <!-- Terms & Signatures -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mt-12 border-t border-slate-200 pt-6 text-xs">
                <!-- Terms & Conditions -->
                <div class="text-[10px] text-slate-500 space-y-1">
                    <h5 class="font-bold text-slate-700 uppercase tracking-wider">Terms & Conditions:</h5>
                    <p>1. Goods once sold will not be taken back or exchanged.</p>
                    <p>2. Warranty claims are subject directly to corporate service centers.</p>
                    <p>3. Overdue payments will incur an interest charge of 18% p.a.</p>
                    <p>4. All disputes are strictly subject to Kuzhithurai jurisdiction only.</p>
                </div>

                <!-- Signatures -->
                <div class="flex flex-col items-center justify-end text-center space-y-3 md:items-end">
                    @if ($quotation->signa)
                        <div class="h-16 w-36 flex items-center justify-center overflow-hidden border border-slate-200 rounded-xl p-1">
                            <!-- In legacy application, signature is stored in Downloads/admin/sign/ -->
                            <img src="/sign/{{ $quotation->signa }}" alt="Authorized Signature" class="max-h-full max-w-full object-contain">
                        </div>
                    @else
                        <div class="h-16"></div>
                    @endif
                    <div class="w-48 border-t border-slate-200 pt-2 text-[10px] font-bold text-slate-500 uppercase tracking-wider">
                        Authorized Signatory
                    </div>
                </div>
            </div>

            <!-- Footer slant (Vibrant bottom legacy representation) -->
            <div class="flex h-2 mt-12 overflow-hidden rounded-lg print:hidden">
                <div class="bg-amber-500 w-2/4 skew-y-6"></div>
                <div class="bg-blue-600 w-1/4 skew-y-6"></div>
                <div class="bg-emerald-500 w-1/4 skew-y-6"></div>
            </div>

            <div class="text-center text-[10px] text-slate-400 mt-6 border-t border-slate-100 pt-4">
                If you have any queries regarding this quotation, please contact <span class="font-bold text-slate-600">T. Jalal Singh (994422 8686)</span>. Thank you for your business!
            </div>

        </div>
    </div>

    <!-- Auto print logic if requested -->
    <script>
        // Trigger print dialog automatically on load when print parameter is set
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
