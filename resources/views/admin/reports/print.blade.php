<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ match($reportType) {
        'expenses'    => 'Expenses Report',
        'itemnameoff' => 'Offline Sales Itemnamewise Report',
        'itemnameon'  => 'Online Sales Itemnamewise Report',
        'purchase'    => 'Purchase Namewise Report',
        default       => 'Business Report',
    } }} - MTL Mart</title>
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
        @@media print {
            body { background: white !important; color: #0f172a !important; }
            .no-print { display: none !important; }
            .print-sheet {
                border: none !important;
                box-shadow: none !important;
                padding: 0 !important;
                margin: 0 !important;
                width: 100% !important;
                max-width: 100% !important;
            }
            * { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
            table { page-break-inside: auto; }
            tr { page-break-inside: avoid; page-break-after: auto; }
            thead { display: table-header-group; }
        }
        body { background-color: #f8fafc; font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="min-h-screen text-slate-800 antialiased print:bg-white print:text-slate-900">

    <!-- Floating Top Bar (Hidden on print) -->
    <div class="no-print bg-white/85 backdrop-blur-md border-b border-slate-200/80 sticky top-0 z-50 px-6 py-4 flex items-center justify-between shadow-sm">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.reports.index') }}"
               class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-slate-700 bg-slate-100 hover:bg-slate-200/80 text-sm font-semibold transition">
                <i class="fa-solid fa-arrow-left text-xs"></i>
                Back to Reports
            </a>
            <span class="text-xs font-semibold px-2.5 py-1 bg-violet-100 text-violet-700 border border-violet-200 rounded-full">
                {{ match($reportType) {
                    'expenses'    => 'Expenses Report',
                    'itemnameoff' => 'Offline Sales Itemnamewise',
                    'itemnameon'  => 'Online Sales Itemnamewise',
                    'purchase'    => 'Purchase Namewise',
                    default       => 'Business Report',
                } }}
            </span>
            <span class="text-xs text-slate-500 font-mono">
                {{ $startDate->format('d-m-Y') }} → {{ $endDate->format('d-m-Y') }}
            </span>
        </div>
        <div class="flex items-center gap-2">
            <button onclick="window.print()"
                class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-violet-600 hover:bg-violet-700 text-white font-bold text-sm shadow-lg transition active:scale-95">
                <i class="fa-solid fa-print"></i>
                Print Report
            </button>
        </div>
    </div>

    <!-- Printable Area -->
    <div class="max-w-5xl mx-auto my-8 p-6 print:m-0 print:p-0">
        <div class="print-sheet bg-white border border-slate-200 shadow-xl rounded-3xl p-10 print:bg-white print:border-none print:shadow-none print:rounded-none">

            <!-- Header Slanted Bars -->
            <div class="flex h-6 mb-8 overflow-hidden rounded-lg print:rounded-none print:h-5">
                <div class="bg-violet-600 w-1/4 -skew-x-12 origin-top -ml-2"></div>
                <div class="bg-indigo-600 w-1/4 -skew-x-12 origin-top"></div>
                <div class="bg-amber-500 w-2/4 -skew-x-12 origin-top flex items-center justify-center -mr-2">
                    <span class="text-[10px] font-bold text-white tracking-widest font-outfit skew-x-12">WWW.MTLMART.COM</span>
                </div>
            </div>

            <!-- Company Brand Block -->
            <div class="text-center space-y-2 pb-6 border-b border-slate-200">
                <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight font-outfit uppercase">MTL COMPUTER GARDEN</h1>
                <p class="text-xs text-slate-500 font-medium">1st Floor, SUS Building, Opposite to MRF Tyres, Main Road, Marthandam, TN, India - 629154</p>
                <div class="flex justify-center gap-4 text-[10px] font-bold font-mono">
                    <span class="inline-block px-3 py-1 bg-slate-50 border border-slate-200 text-slate-600 rounded-lg">GSTIN: 33AQQPJ1772L1ZG</span>
                    <span class="inline-block px-3 py-1 bg-slate-50 border border-slate-200 text-slate-600 rounded-lg">Mob: 9944228686</span>
                </div>
            </div>

            <!-- Report Title Block -->
            <div class="my-6 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <div>
                    <h2 class="text-lg font-black text-violet-700 font-outfit uppercase tracking-tight">
                        {{ match($reportType) {
                            'expenses'    => 'EXPENSES REPORT',
                            'itemnameoff' => 'ITEM NAMEWISE OFFLINE SALES REPORT',
                            'itemnameon'  => 'ITEM NAMEWISE ONLINE SALES REPORT',
                            'purchase'    => 'ITEM NAMEWISE PURCHASE REPORT',
                            default       => 'BUSINESS REPORT',
                        } }}
                    </h2>
                    <p class="text-xs text-slate-500 mt-1">
                        {{ ($reportType === 'expenses') ? 'Expense Category' : 'Product' }}:
                        <span class="font-bold text-slate-700">{{ $selectedName }}</span>
                    </p>
                </div>
                <div class="text-right space-y-0.5 text-xs text-slate-500">
                    <p>Period: <span class="font-mono font-bold text-slate-700">{{ $startDate->format('d-m-Y') }}</span> to <span class="font-mono font-bold text-slate-700">{{ $endDate->format('d-m-Y') }}</span></p>
                    <p>Generated: <span class="font-mono text-slate-600">{{ now()->format('d-m-Y H:i') }}</span></p>
                </div>
            </div>

            <!-- ======================== EXPENSES TABLE ======================== -->
            @if($reportType === 'expenses')
                @php $totalAmount = 0; $dataRows = $data->values(); @endphp
                <div class="overflow-x-auto print:overflow-visible">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead>
                            <tr class="bg-slate-50 border-y border-slate-200 text-slate-600 font-bold uppercase tracking-wider">
                                <th class="py-3 px-4 w-10 text-center">#</th>
                                <th class="py-3 px-4">Staff Name</th>
                                <th class="py-3 px-4">Expense Date</th>
                                <th class="py-3 px-4">Expense Category</th>
                                <th class="py-3 px-4 text-right">Amount (₹)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($dataRows as $i => $exp)
                                @php
                                    $totalAmount += floatval($exp->exp_amount);
                                @endphp
                                <tr class="hover:bg-slate-50/50 transition">
                                    <td class="py-2.5 px-4 text-center font-mono text-slate-400">{{ $i + 1 }}</td>
                                    <td class="py-2.5 px-4 font-medium text-slate-800 uppercase">
                                        {{ $exp->staff->username ?? 'N/A' }}
                                    </td>
                                    <td class="py-2.5 px-4 font-mono text-slate-600">
                                        {{ \Carbon\Carbon::parse($exp->exp_date)->format('d-m-Y') }}
                                    </td>
                                    <td class="py-2.5 px-4 text-slate-700">
                                        {{ $exp->category->exp_name ?? 'N/A' }}
                                    </td>
                                    <td class="py-2.5 px-4 text-right font-mono font-semibold text-slate-800">
                                        {{ number_format(floatval($exp->exp_amount), 2) }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-10 text-center text-slate-400 font-medium">No expense records found for the selected period.</td>
                                </tr>
                            @endforelse
                        </tbody>
                        @if($dataRows->count() > 0)
                        <tfoot>
                            <tr class="bg-slate-50 border-t-2 border-slate-300 font-bold">
                                <td colspan="4" class="py-3 px-4 text-right text-sm text-slate-700 uppercase tracking-wide">Total Amount</td>
                                <td class="py-3 px-4 text-right font-mono text-base text-indigo-700">₹ {{ number_format($totalAmount, 2) }}</td>
                            </tr>
                        </tfoot>
                        @endif
                    </table>
                </div>

            <!-- ======================== OFFLINE SALES TABLE ======================== -->
            @elseif($reportType === 'itemnameoff')
                @php $totalAmount = 0; $totalQty = 0; $dataRows = $data->values(); @endphp
                <div class="overflow-x-auto print:overflow-visible">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead>
                            <tr class="bg-slate-50 border-y border-slate-200 text-slate-600 font-bold uppercase tracking-wider">
                                <th class="py-3 px-4 w-10 text-center">#</th>
                                <th class="py-3 px-4">Order Date</th>
                                <th class="py-3 px-4">Customer Name</th>
                                <th class="py-3 px-4">Address / Contact</th>
                                @if($selectedName === 'All')
                                <th class="py-3 px-4">Product Name</th>
                                @endif
                                <th class="py-3 px-4 text-center w-16">Qty</th>
                                <th class="py-3 px-4 text-right">Amount (₹)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($dataRows as $i => $item)
                                @php
                                    $totalAmount += floatval($item->total);
                                    $totalQty += floatval($item->qty);
                                    $order = $item->order;
                                    $user = $order->user ?? null;
                                    $address = $user ? trim(($user->billingaddress ?? '') . ' ' . ($user->contactno ? ', ' . $user->contactno : '')) : ($order->client_contact ?? '');
                                @endphp
                                <tr class="hover:bg-slate-50/50 transition">
                                    <td class="py-2.5 px-4 text-center font-mono text-slate-400">{{ $i + 1 }}</td>
                                    <td class="py-2.5 px-4 font-mono text-slate-600">
                                        {{ \Carbon\Carbon::parse($order->order_date)->format('d-m-Y') }}
                                    </td>
                                    <td class="py-2.5 px-4 font-medium text-slate-800 uppercase">
                                        {{ $order->client_name ?? ($user->uname ?? 'N/A') }}
                                    </td>
                                    <td class="py-2.5 px-4 text-slate-600 max-w-[180px] truncate" title="{{ $address }}">
                                        {{ $address ?: 'N/A' }}
                                    </td>
                                    @if($selectedName === 'All')
                                    <td class="py-2.5 px-4 text-slate-700 uppercase">
                                        {{ $item->product->productname ?? 'N/A' }}
                                    </td>
                                    @endif
                                    <td class="py-2.5 px-4 text-center font-mono font-bold text-slate-700">{{ $item->qty }}</td>
                                    <td class="py-2.5 px-4 text-right font-mono font-semibold text-slate-800">
                                        {{ number_format(floatval($item->total), 2) }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ $selectedName === 'All' ? 7 : 6 }}" class="py-10 text-center text-slate-400 font-medium">No offline sales records found for the selected period.</td>
                                </tr>
                            @endforelse
                        </tbody>
                        @if($dataRows->count() > 0)
                        <tfoot>
                            <tr class="bg-slate-50 border-t-2 border-slate-300 font-bold">
                                <td colspan="{{ $selectedName === 'All' ? 5 : 4 }}" class="py-3 px-4 text-right text-sm text-slate-700 uppercase tracking-wide">Total</td>
                                <td class="py-3 px-4 text-center font-mono text-base text-indigo-700">{{ $totalQty }}</td>
                                <td class="py-3 px-4 text-right font-mono text-base text-indigo-700">₹ {{ number_format($totalAmount, 2) }}</td>
                            </tr>
                        </tfoot>
                        @endif
                    </table>
                </div>

            <!-- ======================== ONLINE SALES TABLE ======================== -->
            @elseif($reportType === 'itemnameon')
                @php $totalAmount = 0; $totalQty = 0; $dataRows = $data->values(); @endphp
                <div class="overflow-x-auto print:overflow-visible">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead>
                            <tr class="bg-slate-50 border-y border-slate-200 text-slate-600 font-bold uppercase tracking-wider">
                                <th class="py-3 px-4 w-10 text-center">#</th>
                                <th class="py-3 px-4">Order Date</th>
                                <th class="py-3 px-4">Customer Name</th>
                                <th class="py-3 px-4">Address / Contact</th>
                                @if($selectedName === 'All')
                                <th class="py-3 px-4">Product Name</th>
                                @endif
                                <th class="py-3 px-4 text-center w-16">Qty</th>
                                <th class="py-3 px-4 text-right">Amount (₹)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($dataRows as $i => $item)
                                @php
                                    $qty   = $item->quantity;
                                    $total = floatval($qty) * floatval($item->cprice);
                                    $totalAmount += $total;
                                    $totalQty += $qty;
                                    $uorder = $item->order;
                                    $user   = $uorder ? $uorder->user : null;
                                    $address = $user
                                        ? trim(implode(', ', array_filter([
                                            $user->billingaddress ?? '',
                                            $user->billingcity ?? '',
                                            $user->billingstate ?? '',
                                            ($user->billingpincode ? 'Pin-'.$user->billingpincode : ''),
                                            ($user->contactno ? 'Mob:'.$user->contactno : ''),
                                          ])))
                                        : 'N/A';
                                @endphp
                                <tr class="hover:bg-slate-50/50 transition">
                                    <td class="py-2.5 px-4 text-center font-mono text-slate-400">{{ $i + 1 }}</td>
                                    <td class="py-2.5 px-4 font-mono text-slate-600">
                                        {{ $uorder ? \Carbon\Carbon::parse($uorder->orderdate)->format('d-m-Y') : 'N/A' }}
                                    </td>
                                    <td class="py-2.5 px-4 font-medium text-slate-800 uppercase">
                                        {{ $user->uname ?? 'N/A' }}
                                    </td>
                                    <td class="py-2.5 px-4 text-slate-600 max-w-[200px] truncate" title="{{ $address }}">
                                        {{ $address }}
                                    </td>
                                    @if($selectedName === 'All')
                                    <td class="py-2.5 px-4 text-slate-700 uppercase">
                                        {{ $item->product->productname ?? 'N/A' }}
                                    </td>
                                    @endif
                                    <td class="py-2.5 px-4 text-center font-mono font-bold text-slate-700">{{ $qty }}</td>
                                    <td class="py-2.5 px-4 text-right font-mono font-semibold text-slate-800">
                                        {{ number_format($total, 2) }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ $selectedName === 'All' ? 7 : 6 }}" class="py-10 text-center text-slate-400 font-medium">No online sales records found for the selected period.</td>
                                </tr>
                            @endforelse
                        </tbody>
                        @if($dataRows->count() > 0)
                        <tfoot>
                            <tr class="bg-slate-50 border-t-2 border-slate-300 font-bold">
                                <td colspan="{{ $selectedName === 'All' ? 5 : 4 }}" class="py-3 px-4 text-right text-sm text-slate-700 uppercase tracking-wide">Total</td>
                                <td class="py-3 px-4 text-center font-mono text-base text-indigo-700">{{ $totalQty }}</td>
                                <td class="py-3 px-4 text-right font-mono text-base text-indigo-700">₹ {{ number_format($totalAmount, 2) }}</td>
                            </tr>
                        </tfoot>
                        @endif
                    </table>
                </div>

            <!-- ======================== PURCHASE TABLE ======================== -->
            @elseif($reportType === 'purchase')
                @php $totalAmount = 0; $totalQty = 0; $dataRows = $data->values(); @endphp
                <div class="overflow-x-auto print:overflow-visible">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead>
                            <tr class="bg-slate-50 border-y border-slate-200 text-slate-600 font-bold uppercase tracking-wider">
                                <th class="py-3 px-4 w-10 text-center">#</th>
                                <th class="py-3 px-4">Purchase Date</th>
                                <th class="py-3 px-4">Staff Name</th>
                                <th class="py-3 px-4">Product Name</th>
                                <th class="py-3 px-4 text-center w-16">Qty</th>
                                <th class="py-3 px-4 text-right">Amount (₹)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($dataRows as $i => $item)
                                @php
                                    $totalAmount += floatval($item->rate);
                                    $totalQty += floatval($item->qty);
                                    $porder = $item->purchaseOrder;
                                    $staffId = $porder->staffname ?? null;
                                    $staffName = 'N/A';
                                    if ($staffId) {
                                        $auser = \App\Models\Auser::where('user_id', $staffId)->first();
                                        $staffName = $auser ? $auser->username : $staffId;
                                    }
                                @endphp
                                <tr class="hover:bg-slate-50/50 transition">
                                    <td class="py-2.5 px-4 text-center font-mono text-slate-400">{{ $i + 1 }}</td>
                                    <td class="py-2.5 px-4 font-mono text-slate-600">
                                        {{ $porder ? \Carbon\Carbon::parse($porder->porder_date)->format('d-m-Y') : 'N/A' }}
                                    </td>
                                    <td class="py-2.5 px-4 font-medium text-slate-800 uppercase">
                                        {{ $staffName }}
                                    </td>
                                    <td class="py-2.5 px-4 text-slate-700 uppercase">
                                        {{ $item->product->productname ?? 'N/A' }}
                                    </td>
                                    <td class="py-2.5 px-4 text-center font-mono font-bold text-slate-700">{{ $item->qty }}</td>
                                    <td class="py-2.5 px-4 text-right font-mono font-semibold text-slate-800">
                                        {{ number_format(floatval($item->rate), 2) }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-10 text-center text-slate-400 font-medium">No purchase records found for the selected period.</td>
                                </tr>
                            @endforelse
                        </tbody>
                        @if($dataRows->count() > 0)
                        <tfoot>
                            <tr class="bg-slate-50 border-t-2 border-slate-300 font-bold">
                                <td colspan="4" class="py-3 px-4 text-right text-sm text-slate-700 uppercase tracking-wide">Total</td>
                                <td class="py-3 px-4 text-center font-mono text-base text-indigo-700">{{ $totalQty }}</td>
                                <td class="py-3 px-4 text-right font-mono text-base text-indigo-700">₹ {{ number_format($totalAmount, 2) }}</td>
                            </tr>
                        </tfoot>
                        @endif
                    </table>
                </div>
            @endif

            <!-- Footer -->
            <div class="flex h-2 mt-12 overflow-hidden rounded-lg print:hidden">
                <div class="bg-amber-500 w-2/4 skew-y-6"></div>
                <div class="bg-violet-600 w-1/4 skew-y-6"></div>
                <div class="bg-indigo-600 w-1/4 skew-y-6"></div>
            </div>

            <div class="text-center text-[10px] text-slate-400 mt-6 border-t border-slate-100 pt-4">
                Report generated by MTL COMPUTER GARDEN Admin Portal &mdash; {{ now()->format('d-m-Y H:i:s') }}
            </div>

        </div>
    </div>

    <!-- Auto print if ?autoprint=1 -->
    <script>
        window.addEventListener('DOMContentLoaded', () => {
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.has('autoprint')) {
                setTimeout(() => { window.print(); }, 600);
            }
        });
    </script>
</body>
</html>
