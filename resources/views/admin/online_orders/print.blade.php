@php
    use App\Helpers\NumberHelper;
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tax Invoice - MTL Mart</title>
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
            <x-admin.button href="{{ route('admin.online_orders.index') }}" variant="secondary" icon="fa-solid fa-arrow-left" class="px-3.5 py-2 rounded-xl text-slate-700 border-none bg-slate-100 hover:bg-slate-200/80">
                Back to List
            </x-admin.button>
            <span class="text-xs font-semibold px-2.5 py-1 bg-slate-100 text-slate-650 border border-slate-200 rounded-full font-mono">
                Order ID: #{{ str_pad($order->orderid, 5, '0', STR_PAD_LEFT) }}
            </span>
        </div>
        <div class="flex items-center gap-2">
            <x-admin.button onclick="window.print()" variant="indigo-flat" icon="fa-solid fa-print" class="px-5 py-2.5 rounded-xl">
                Print Tax Invoice
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

            <!-- Customer & Document Meta block -->
            <div class="grid grid-cols-2 gap-8 my-8 text-xs">
                <!-- Customer Details -->
                <div class="space-y-2">
                    <span class="block text-[10px] font-extrabold text-slate-400 uppercase tracking-widest">Buyer / Client</span>
                    <div class="space-y-1">
                        <h4 class="text-sm font-bold text-slate-900 uppercase">{{ $order->user->uname ?? $order->username }}</h4>
                        <p class="text-slate-600 whitespace-pre-line uppercase font-medium">
                            {{ $order->user->billingaddress ?? ($order->user->shippingaddress ?? '') }}
                            @if($order->user && (!empty($order->user->billingcity) || !empty($order->user->billingstate)))
                                <br>{{ $order->user->billingcity ?? '' }}{{ !empty($order->user->billingcity) && !empty($order->user->billingstate) ? ', ' : '' }}{{ $order->user->billingstate ?? '' }}
                            @endif
                            @if($order->user && !empty($order->user->billingpincode))
                                &nbsp;Pin-{{ $order->user->billingpincode }}
                            @endif
                        </p>
                        @php
                            $mobileNum = $order->user->contactno ?? '';
                            $gsttinNum = $order->gsttin ?: ($order->user->gsttin ?? '');
                        @endphp
                        @if ($mobileNum)
                            <p class="text-slate-700 font-medium mt-1">Contact: {{ $mobileNum }}</p>
                        @endif
                        @if ($gsttinNum)
                            <p class="text-slate-800 font-mono font-bold uppercase mt-0.5">GSTIN: {{ $gsttinNum }}</p>
                        @endif
                    </div>
                </div>

                <!-- Document Details -->
                <div class="text-right space-y-3">
                    <div>
                        <span class="block text-[10px] font-extrabold text-slate-400 uppercase tracking-widest">Document Type</span>
                        <h2 class="text-xl font-black text-indigo-600 font-outfit uppercase tracking-tight">
                            Online Order Tax Invoice
                        </h2>
                    </div>
                    <div class="space-y-1 font-medium text-slate-600">
                        <p>Order Ref: <span class="font-mono text-slate-900 font-bold">#{{ str_pad($order->orderid, 5, '0', STR_PAD_LEFT) }}</span></p>
                        <p>Date: <span class="font-mono text-slate-900 font-bold">{{ date('d-m-Y h:i A', strtotime($order->orderdate)) }}</span></p>
                        
                        @php
                            $paytype = 'Cash';
                            if ($order->paymethod == 'q') {
                                $paytype = 'Cheque';
                            } elseif ($order->paymethod == 'C') {
                                $paytype = 'Credit Card';
                            } elseif ($order->paymethod == 'D') {
                                $paytype = 'Debit Card';
                            } elseif ($order->paymethod == 'I') {
                                $paytype = 'UPI / NetBanking';
                            }
                        @endphp
                        <p>Mode of Payment: <span class="font-bold text-slate-900 uppercase">{{ $paytype }}</span></p>
                        @if ($order->username)
                            <p>Operator Tag: <span class="uppercase font-semibold text-indigo-650">{{ $order->username }}</span></p>
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
                            <th class="py-3 px-4 w-16 text-center">Qty</th>
                            <th class="py-3 px-4 w-16 text-center">Per</th>
                            <th class="py-3 px-4 w-28 text-right">Total Amount</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($order->items as $index => $item)
                            <tr class="text-slate-700 align-middle">
                                <td class="py-3 px-4 text-center font-mono font-semibold text-slate-400">{{ $index + 1 }}</td>
                                <td class="py-3 px-4 font-semibold text-slate-900 uppercase">
                                    {{ $item->product->productname ?? $item->productId }}
                                </td>
                                <td class="py-3 px-4 text-center font-mono text-slate-500">{{ $item->hsnsan ?: '-' }}</td>
                                <td class="py-3 px-4 text-center font-mono font-medium text-slate-600">{{ $item->gst }}%</td>
                                <td class="py-3 px-4 text-right font-mono text-slate-600">
                                    {{ NumberHelper::indianFormat($item->price) }}
                                </td>
                                <td class="py-3 px-4 text-center font-bold font-mono text-slate-700">{{ $item->quantity }}</td>
                                <td class="py-3 px-4 text-center uppercase text-slate-500 font-medium">{{ $item->unit ?: 'PCS' }}</td>
                                <td class="py-3 px-4 text-right font-mono font-bold text-slate-900">
                                    Rs. {{ NumberHelper::indianFormat($item->cprice) }}
                                </td>
                            </tr>
                        @endforeach

                        <!-- Extra Charges (Installation/Shipping) -->
                        @if ($order->install > 0)
                            <tr class="text-slate-700 align-middle">
                                <td class="py-3 px-4 text-center font-mono font-semibold text-slate-400">{{ count($order->items) + 1 }}</td>
                                <td class="py-3 px-4 font-semibold text-slate-900 uppercase">INSTALLATION & SERVICE CHARGES</td>
                                <td class="py-3 px-4 text-center font-mono text-slate-500">-</td>
                                <td class="py-3 px-4 text-center font-mono text-slate-500">-</td>
                                <td class="py-3 px-4 text-right font-mono text-slate-500">-</td>
                                <td class="py-3 px-4 text-center font-bold font-mono text-slate-700">1</td>
                                <td class="py-3 px-4 text-center uppercase text-slate-500 font-medium">SERV</td>
                                <td class="py-3 px-4 text-right font-mono font-bold text-slate-900">
                                    Rs. {{ NumberHelper::indianFormat($order->install) }}
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
                            {{ NumberHelper::convertToIndianCurrency($order->gamount) }}
                        </p>
                    </div>

                    <div class="text-slate-500 space-y-1">
                        <span class="block text-[10px] font-extrabold text-slate-400 uppercase tracking-widest">Bank Remittance Details</span>
                        <p class="font-bold text-slate-800">Indian Overseas Bank, Kuzhithurai Branch</p>
                        <p class="font-mono text-slate-650">Account No: <span class="text-slate-900 font-semibold">2869020000000349</span></p>
                        <p class="font-mono text-slate-650">IFSC Code: <span class="text-slate-900 font-semibold">IOBA0002869</span></p>
                    </div>
                </div>

                <!-- Right: Financial Breakdown -->
                <div class="space-y-3 bg-slate-50/50 p-6 rounded-2xl border border-slate-200/80 print:bg-white print:border-none print:p-0 print:space-y-2">
                    <div class="flex items-center justify-between text-slate-500">
                        <span>Subtotal Amount:</span>
                        <span class="font-mono font-semibold text-slate-800">Rs. {{ NumberHelper::indianFormat($order->total) }}</span>
                    </div>

                    @php
                        $gstTotal = floatval($order->gsta);
                    @endphp

                    <!-- Intra-State CGST & SGST Breakdown -->
                    <div class="flex items-center justify-between text-slate-550 text-xs">
                        <span>Central GST (CGST @ {{ count($order->items) ? $order->items->avg('gst') / 2 : 9 }}% Avg):</span>
                        <span class="font-mono text-slate-700">Rs. {{ NumberHelper::indianFormat($gstTotal / 2) }}</span>
                    </div>
                    <div class="flex items-center justify-between text-slate-550 text-xs">
                        <span>State GST (SGST @ {{ count($order->items) ? $order->items->avg('gst') / 2 : 9 }}% Avg):</span>
                        <span class="font-mono text-slate-700">Rs. {{ NumberHelper::indianFormat($gstTotal / 2) }}</span>
                    </div>

                    @if ($order->discount > 0)
                        <div class="flex items-center justify-between text-amber-600 font-medium">
                            <span>Discount Given:</span>
                            <span class="font-mono font-bold">- Rs. {{ NumberHelper::indianFormat($order->discount) }}</span>
                        </div>
                    @endif

                    @if ($order->pcoin > 0)
                        <div class="flex items-center justify-between text-indigo-650 font-medium">
                            <span>MTLMoney Redeemed:</span>
                            <span class="font-mono font-bold">- Rs. {{ NumberHelper::indianFormat($order->pcoin) }}</span>
                        </div>
                    @endif

                    <div class="flex items-center justify-between text-slate-500 pt-2 border-t border-slate-200/60">
                        <span>Delivery / Shipping:</span>
                        <span class="font-mono text-slate-800">
                            @if ($order->tship > 0)
                                Rs. {{ NumberHelper::indianFormat($order->tship) }}
                            @else
                                Free Delivery
                            @endif
                        </span>
                    </div>

                    <div class="flex items-center justify-between text-sm font-bold text-slate-900 pt-3 border-t border-slate-200">
                        <span class="font-outfit text-base">Grand Total (Inclusive of Taxes):</span>
                        <span class="font-mono text-lg text-indigo-600">Rs. {{ NumberHelper::indianFormat($order->gamount) }}</span>
                    </div>

                    <!-- Payment Details (Paid vs Balance) -->
                    <div class="pt-3 mt-1 border-t border-slate-200 space-y-1.5">
                        <div class="flex items-center justify-between text-xs text-emerald-600 font-semibold">
                            <span>Amount Paid:</span>
                            <span class="font-mono text-sm">Rs. {{ NumberHelper::indianFormat($order->pamount) }}</span>
                        </div>
                        <div class="flex items-center justify-between text-xs text-rose-600 font-bold">
                            <span>Balance Outstanding:</span>
                            <span class="font-mono text-sm">Rs. {{ NumberHelper::indianFormat($order->bamount) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Terms & Signatures -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mt-12 border-t border-slate-200 pt-6 text-xs">
                <!-- Terms & Conditions -->
                <div class="text-[10px] text-slate-500 space-y-1">
                    <h5 class="font-bold text-slate-700 uppercase tracking-wider">Terms & Conditions & Declaration</h5>
                    <p>1. Goods once sold will not be taken back or exchanged.</p>
                    <p>2. Warranty/Guarantee claims are subject directly to corporate service centers.</p>
                    <p>3. Overdue payments will incur an interest charge of 18% p.a.</p>
                    <p>4. All disputes are strictly subject to Kuzhithurai jurisdiction only.</p>
                    <p class="font-semibold mt-2 text-slate-550">
                        * We declare that this invoice shows the actual price of the goods described and that all particulars are true and correct.
                    </p>
                </div>

                <!-- Signatures -->
                <div class="flex flex-col items-center justify-end text-center space-y-3 md:items-end">
                    <div class="h-16"></div>
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
                Thank you for your business! MTL COMPUTER GARDEN's selling is not the end, it's just the beginning of our relationship.
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
