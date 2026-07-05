@extends('layouts.storefront')

@section('content')
    @php
        $subTotal = 0;
        $taxTotal = 0;
        foreach ($cart as $item) {
            $lineVal = $item['quantity'] * $item['price'];
            $subTotal += $lineVal;
            $taxTotal += $lineVal * ($item['gst'] / 100);
        }
        $grandTotal = $subTotal + $taxTotal;
    @endphp

    <!-- Breadcrumb Trace -->
    <div class="mb-6 font-sans">
        <div class="flex items-center gap-2 text-[11px] font-bold text-slate-500 mb-2 tracking-wide">
            <a href="{{ route('storefront.index') }}" class="hover:text-[#0059e3] transition-colors">Home</a>
            <i class="fa-solid fa-chevron-right text-[8px] text-slate-400"></i>
            <a href="{{ route('storefront.cart') }}" class="hover:text-[#0059e3] transition-colors">Cart</a>
            <i class="fa-solid fa-chevron-right text-[8px] text-slate-400"></i>
            <span class="text-slate-800 dark:text-slate-200">Checkout</span>
        </div>
        <h1 class="text-3xl font-black text-slate-900 dark:text-white uppercase tracking-tight font-sans">Checkout Authorization</h1>
        <p class="text-xs text-slate-500 mt-1 uppercase font-bold tracking-wider">Confirm your billing details and place your order</p>
    </div>

    <!-- Checkout Grid Structure -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start mb-16 font-sans">
        <!-- Left: Forms (7 cols) -->
        <div class="lg:col-span-7 space-y-6">
            <form action="{{ route('storefront.checkout.order') }}" method="POST" class="space-y-6">
                @csrf

                <!-- Client Details -->
                <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 shadow-sm space-y-5">
                    <span class="block text-xs font-black uppercase tracking-wider text-slate-800 dark:text-white border-b border-slate-100 dark:border-slate-800 pb-3">
                        <i class="fa-solid fa-user-shield text-[#0059e3] mr-1.5"></i> Customer &amp; Billing Details
                    </span>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="space-y-2">
                            <label for="clientName" class="block text-[10px] font-black text-slate-500 uppercase tracking-widest">Full Name / Company Name *</label>
                            <input type="text" id="clientName" name="clientName" required
                                   value="{{ old('clientName', Auth::check() ? (Auth::user()->uname ?: Auth::user()->name) : '') }}"
                                   placeholder="e.g. FEJIN RP"
                                   class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-850 bg-slate-50 dark:bg-slate-950 text-sm font-semibold text-slate-900 dark:text-white placeholder-slate-400 outline-none focus:border-[#0059e3] focus:ring-1 focus:ring-[#0059e3] uppercase transition-all">
                        </div>

                        <div class="space-y-2">
                            <label for="mobileno" class="block text-[10px] font-black text-slate-500 uppercase tracking-widest">Contact Phone Number *</label>
                            <input type="text" id="mobileno" name="mobileno" required
                                   value="{{ old('mobileno', Auth::check() ? Auth::user()->contactno : '') }}"
                                   placeholder="e.g. 9944228686"
                                   class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-850 bg-slate-50 dark:bg-slate-950 text-sm font-semibold text-slate-900 dark:text-white placeholder-slate-400 outline-none focus:border-[#0059e3] focus:ring-1 focus:ring-[#0059e3] transition-all">
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label for="gsttin" class="block text-[10px] font-black text-slate-500 uppercase tracking-widest">GSTIN (Optional for corporate input credit)</label>
                        <input type="text" id="gsttin" name="gsttin"
                               value="{{ old('gsttin', Auth::check() ? Auth::user()->gsttin : '') }}"
                               placeholder="e.g. 33AQQPJ1772L1ZG"
                               class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-850 bg-slate-50 dark:bg-slate-950 text-sm font-semibold text-slate-900 dark:text-white placeholder-slate-400 outline-none focus:border-[#0059e3] focus:ring-1 focus:ring-[#0059e3] uppercase transition-all">
                    </div>
                </div>

                <!-- Shipping Address -->
                <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 shadow-sm space-y-5">
                    <span class="block text-xs font-black uppercase tracking-wider text-slate-800 dark:text-white border-b border-slate-100 dark:border-slate-800 pb-3">
                        <i class="fa-solid fa-truck-fast text-[#0059e3] mr-1.5"></i> Delivery &amp; Dispatch Address
                    </span>

                    <div class="space-y-2">
                        <label for="clientContact" class="block text-[10px] font-black text-slate-500 uppercase tracking-widest">Street Address *</label>
                        <textarea id="clientContact" name="clientContact" required rows="3"
                                  placeholder="Provide building number, street address, and landmark details..."
                                  class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-850 bg-slate-50 dark:bg-slate-950 text-sm font-semibold text-slate-900 dark:text-white placeholder-slate-400 outline-none focus:border-[#0059e3] focus:ring-1 focus:ring-[#0059e3] uppercase transition-all">{{ old('clientContact', Auth::check() ? Auth::user()->billingaddress : '') }}</textarea>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div class="space-y-2">
                            <label for="city" class="block text-[10px] font-black text-slate-500 uppercase tracking-widest">City *</label>
                            <input type="text" id="city" name="city" required
                                   value="{{ old('city', Auth::check() ? Auth::user()->billingcity : '') }}"
                                   placeholder="City"
                                   class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-850 bg-slate-50 dark:bg-slate-950 text-sm font-semibold text-slate-900 dark:text-white placeholder-slate-400 outline-none focus:border-[#0059e3] focus:ring-1 focus:ring-[#0059e3] uppercase transition-all">
                        </div>

                        <div class="space-y-2">
                            <label for="state" class="block text-[10px] font-black text-slate-500 uppercase tracking-widest">State *</label>
                            <select id="state" name="state" required
                                    class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-850 bg-slate-50 dark:bg-slate-950 text-sm font-semibold text-slate-900 dark:text-white outline-none focus:border-[#0059e3] focus:ring-1 focus:ring-[#0059e3] uppercase transition-all">
                                <option value="">Select State</option>
                                @foreach ($states as $st)
                                    <option value="{{ $st->sname }}" {{ old('state', Auth::check() ? Auth::user()->billingstate : '') == $st->sname ? 'selected' : '' }}>
                                        {{ $st->sname }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="space-y-2">
                            <label for="pincode" class="block text-[10px] font-black text-slate-500 uppercase tracking-widest">Pincode *</label>
                            <input type="text" id="pincode" name="pincode" required
                                   value="{{ old('pincode', Auth::check() ? Auth::user()->billingpincode : '') }}"
                                   placeholder="e.g. 629154"
                                   class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-850 bg-slate-50 dark:bg-slate-950 text-sm font-semibold text-slate-900 dark:text-white placeholder-slate-400 outline-none focus:border-[#0059e3] focus:ring-1 focus:ring-[#0059e3] transition-all">
                        </div>
                    </div>
                </div>

                <!-- Payment Method -->
                <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-5 shadow-sm space-y-4">
                    <span class="block text-xs font-black uppercase tracking-wider text-slate-800 dark:text-white border-b border-slate-100 dark:border-slate-800 pb-2.5">
                        <i class="fa-solid fa-wallet text-[#0059e3] mr-1.5"></i> Remittance Channels
                    </span>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <label class="relative flex items-start p-4 rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950/50 hover:border-[#0059e3]/20 transition-all cursor-pointer">
                            <input type="radio" name="paymentType" value="2" checked class="h-4 w-4 text-[#0059e3] focus:ring-[#0059e3] bg-white mt-0.5">
                            <div class="ml-2.5 text-xs leading-normal">
                                <span class="block font-black text-slate-800 dark:text-white uppercase tracking-wider">Cash on Delivery</span>
                                <span class="block text-slate-500 text-[10px] mt-0.5">Settle balance upon delivery.</span>
                            </div>
                        </label>

                        <label class="relative flex items-start p-4 rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950/50 hover:border-[#0059e3]/20 transition-all cursor-pointer">
                            <input type="radio" name="paymentType" value="3" class="h-4 w-4 text-[#0059e3] focus:ring-[#0059e3] bg-white mt-0.5">
                            <div class="ml-2.5 text-xs leading-normal">
                                <span class="block font-black text-slate-800 dark:text-white uppercase tracking-wider">UPI / Bank Transfer</span>
                                <span class="block text-slate-500 text-[10px] mt-0.5">Remit directly to target IOB account.</span>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="w-full h-14 text-white font-black uppercase tracking-widest text-xs rounded-xl shadow-lg shadow-blue-500/10 active:scale-[0.98] transition-all flex items-center justify-center gap-2 cursor-pointer" style="background-color: #0059e3;">
                    <i class="fa-solid fa-circle-check text-sm"></i>
                    <span>Authorize Purchase Order</span>
                </button>
            </form>
        </div>

        <!-- Right: Summary Sidebar (5 cols) -->
        <div class="lg:col-span-5">
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 shadow-sm space-y-6 sticky top-24">
                <span class="block text-xs font-black uppercase tracking-wider text-slate-800 dark:text-white border-b border-slate-100 dark:border-slate-800 pb-3">Items Summary</span>

                <!-- Scrollable Item list -->
                <div class="max-h-60 overflow-y-auto divide-y divide-slate-150 dark:divide-slate-800 pr-1.5 custom-scrollbar space-y-3">
                    @foreach ($cart as $id => $item)
                        <div class="flex items-center gap-3 pt-3.5 first:pt-0">
                            <div class="w-10 h-10 rounded border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-955 flex items-center justify-center p-1 flex-shrink-0">
                                @if ($item['image'])
                                    <img src="/storage/productimage/{{ $id }}/{{ $item['image'] }}" alt="{{ $item['name'] }}" class="max-h-full max-w-full object-contain">
                                @else
                                    <i class="fa-solid fa-cube text-slate-400 text-xs"></i>
                                @endif
                            </div>
                            <div class="min-w-0 flex-1">
                                <span class="block font-bold text-slate-900 dark:text-white text-xs uppercase truncate">{{ $item['name'] }}</span>
                                <span class="block text-[10px] text-slate-500 font-mono mt-0.5">{{ $item['quantity'] }} {{ $item['unit'] }} x Rs. {{ \App\Helpers\NumberHelper::indianFormat($item['price']) }}</span>
                            </div>
                            <div class="text-right font-mono font-bold text-slate-900 dark:text-white text-xs shrink-0">
                                Rs. {{ \App\Helpers\NumberHelper::indianFormat($item['price'] * $item['quantity']) }}
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Totals block -->
                <div class="border-t border-slate-150 dark:border-slate-800 pt-5 space-y-3 text-xs">
                    <div class="flex items-center justify-between text-slate-500">
                        <span>Items Subtotal:</span>
                        <span class="font-mono font-bold text-slate-800 dark:text-white">Rs. {{ \App\Helpers\NumberHelper::indianFormat($subTotal) }}</span>
                    </div>
                    <div class="flex items-center justify-between text-slate-500">
                        <span>GST Ledger:</span>
                        <span class="font-mono text-slate-800 dark:text-white">Rs. {{ \App\Helpers\NumberHelper::indianFormat($taxTotal) }}</span>
                    </div>
                    <div class="flex items-center justify-between text-slate-500">
                        <span>Delivery Matrix:</span>
                        <span class="text-emerald-600 font-bold uppercase">Free</span>
                    </div>
                    <div class="border-t border-slate-150 dark:border-slate-800 pt-4 mt-2 flex items-center justify-between text-sm font-bold">
                        <span class="text-slate-800 dark:text-white uppercase tracking-wider text-xs">Grand Total:</span>
                        <span class="font-mono text-base" style="color: #0059e3;">Rs. {{ \App\Helpers\NumberHelper::indianFormat($grandTotal) }}</span>
                    </div>
                </div>

                <!-- UPI / Bank Transfer box -->
                <div class="rounded-xl bg-slate-50 dark:bg-slate-950 p-4 text-[11px] text-slate-500 space-y-2 border border-slate-200 dark:border-slate-850">
                    <span class="block font-black text-slate-800 dark:text-white uppercase tracking-wider">Direct Bank Details (UPI target)</span>
                    <div class="rounded-lg border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-3 font-mono text-[10px] text-slate-650 dark:text-slate-350">
                        <p class="font-bold text-slate-900 dark:text-white">INDIAN OVERSEAS BANK</p>
                        <p>Branch: Kuzhithurai</p>
                        <p>A/C: <span class="text-[#0059e3] font-bold">2869020000000349</span></p>
                        <p>IFSC: <span class="text-[#0059e3] font-bold">IOBA0002869</span></p>
                    </div>
                    <p class="text-[9px] text-slate-400">* WhatsApp checkout reference receipt to +91 99442 28686 for priority logistics dispatch.</p>
                </div>
            </div>
        </div>
    </div>
@endsection
