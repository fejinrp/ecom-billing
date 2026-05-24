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

    <div class="mb-8">
        <div class="flex items-center gap-2 text-[10px] font-black uppercase tracking-[0.35em] text-blue-600 dark:text-indigo-400 mb-3">
            <a href="{{ route('storefront.index') }}" class="hover:underline">Home</a>
            <i class="fa-solid fa-chevron-right text-[8px] text-slate-400 dark:text-slate-600"></i>
            <a href="{{ route('storefront.cart') }}" class="hover:underline">Cart</a>
            <i class="fa-solid fa-chevron-right text-[8px] text-slate-400 dark:text-slate-600"></i>
            <span class="text-slate-500 dark:text-slate-400">Checkout</span>
        </div>
        <h1 class="text-3xl sm:text-4xl font-black text-slate-900 dark:text-white uppercase tracking-tight">Checkout Authorization</h1>
        <p class="text-sm text-slate-500 dark:text-slate-400 mt-2 max-w-3xl leading-relaxed">
            Provide delivery details and choose a remittance channel to place your order. The page now follows the same storefront light/dark system as the rest of the site.
        </p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 md:gap-10 items-start">
        <div class="lg:col-span-7 space-y-6">
            <form action="{{ route('storefront.checkout.order') }}" method="POST" class="space-y-6">
                @csrf

                <div class="rounded-[1.75rem] border border-slate-200/70 dark:border-slate-800 bg-white/92 dark:bg-slate-900/82 p-6 shadow-lg shadow-slate-900/5 space-y-6">
                    <div class="pb-3 border-b border-slate-200/70 dark:border-slate-800">
                        <h3 class="flex items-center gap-2 text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-[0.35em]">
                            <i class="fa-solid fa-user-shield text-blue-600 dark:text-indigo-400"></i> Buyer / Client Details
                        </h3>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="space-y-1.5">
                            <label for="clientName" class="block text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-[0.25em]">Full Name / Corporate Entity *</label>
                            <input type="text"
                                   id="clientName"
                                   name="clientName"
                                   required
                                   value="{{ old('clientName', Auth::check() ? (Auth::user()->uname ?: Auth::user()->name) : '') }}"
                                   placeholder="e.g. JOHN DOE"
                                   class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50/90 dark:bg-slate-950/60 text-sm uppercase text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                        </div>

                        <div class="space-y-1.5">
                            <label for="mobileno" class="block text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-[0.25em]">Contact Phone Number *</label>
                            <input type="text"
                                   id="mobileno"
                                   name="mobileno"
                                   required
                                   value="{{ old('mobileno', Auth::check() ? Auth::user()->contactno : '') }}"
                                   placeholder="e.g. 9944228686"
                                   class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50/90 dark:bg-slate-950/60 text-sm text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                        </div>
                    </div>

                    <div class="space-y-1.5">
                        <label for="gsttin" class="block text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-[0.25em]">GSTIN (Optional for corporate input credit)</label>
                        <input type="text"
                               id="gsttin"
                               name="gsttin"
                               value="{{ old('gsttin', Auth::check() ? Auth::user()->gsttin : '') }}"
                               placeholder="e.g. 33AQQPJ1772L1ZG"
                               class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50/90 dark:bg-slate-950/60 text-sm uppercase text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                    </div>
                </div>

                <div class="rounded-[1.75rem] border border-slate-200/70 dark:border-slate-800 bg-white/92 dark:bg-slate-900/82 p-6 shadow-lg shadow-slate-900/5 space-y-6">
                    <div class="pb-3 border-b border-slate-200/70 dark:border-slate-800">
                        <h3 class="flex items-center gap-2 text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-[0.35em]">
                            <i class="fa-solid fa-truck-fast text-blue-600 dark:text-indigo-400"></i> Logistics & Dispatch Address
                        </h3>
                    </div>

                    <div class="space-y-1.5">
                        <label for="clientContact" class="block text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-[0.25em]">Street Address (Billing & Shipping) *</label>
                        <textarea id="clientContact"
                                  name="clientContact"
                                  required
                                  rows="3"
                                  placeholder="Provide building number, street, landmark details..."
                                  class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50/90 dark:bg-slate-950/60 text-sm uppercase text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">{{ old('clientContact', Auth::check() ? Auth::user()->billingaddress : '') }}</textarea>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div class="space-y-1.5">
                            <label for="city" class="block text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-[0.25em]">City *</label>
                            <input type="text"
                                   id="city"
                                   name="city"
                                   required
                                   value="{{ old('city', Auth::check() ? Auth::user()->billingcity : '') }}"
                                   placeholder="e.g. MARTHANDAM"
                                   class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50/90 dark:bg-slate-950/60 text-sm uppercase text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                        </div>

                        <div class="space-y-1.5">
                            <label for="state" class="block text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-[0.25em]">State *</label>
                            <select id="state"
                                    name="state"
                                    required
                                    class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50/90 dark:bg-slate-950/60 text-sm uppercase text-slate-900 dark:text-white outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                                <option value="">Select State</option>
                                @foreach ($states as $st)
                                    <option value="{{ $st->sname }}" {{ old('state', Auth::check() ? Auth::user()->billingstate : '') == $st->sname ? 'selected' : '' }}>
                                        {{ $st->sname }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="space-y-1.5">
                            <label for="pincode" class="block text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-[0.25em]">Pincode *</label>
                            <input type="text"
                                   id="pincode"
                                   name="pincode"
                                   required
                                   value="{{ old('pincode', Auth::check() ? Auth::user()->billingpincode : '') }}"
                                   placeholder="e.g. 629154"
                                   class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50/90 dark:bg-slate-950/60 text-sm text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                        </div>
                    </div>
                </div>

                <div class="rounded-[1.75rem] border border-slate-200/70 dark:border-slate-800 bg-white/92 dark:bg-slate-900/82 p-6 shadow-lg shadow-slate-900/5 space-y-6">
                    <div class="pb-3 border-b border-slate-200/70 dark:border-slate-800">
                        <h3 class="flex items-center gap-2 text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-[0.35em]">
                            <i class="fa-solid fa-wallet text-blue-600 dark:text-indigo-400"></i> Remittance & Payment Type
                        </h3>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <label class="relative flex items-start p-4 rounded-2xl border border-slate-200 dark:border-slate-800 bg-slate-50/90 dark:bg-slate-950/50 hover:border-blue-500/20 transition-all cursor-pointer">
                            <div class="flex items-center h-5">
                                <input type="radio"
                                       name="paymentType"
                                       value="2"
                                       checked
                                       class="h-4 w-4 border-slate-300 text-blue-600 focus:ring-blue-500 focus:ring-offset-0 bg-white dark:bg-slate-950">
                            </div>
                            <div class="ml-3 text-xs leading-relaxed">
                                <span class="block font-black text-slate-900 dark:text-white uppercase tracking-widest">Cash on Delivery</span>
                                <span class="block text-slate-500 dark:text-slate-400 mt-0.5">Settle balances via cash or cheque on dispatch delivery.</span>
                            </div>
                        </label>

                        <label class="relative flex items-start p-4 rounded-2xl border border-slate-200 dark:border-slate-800 bg-slate-50/90 dark:bg-slate-950/50 hover:border-blue-500/20 transition-all cursor-pointer">
                            <div class="flex items-center h-5">
                                <input type="radio"
                                       name="paymentType"
                                       value="3"
                                       class="h-4 w-4 border-slate-300 text-blue-600 focus:ring-blue-500 focus:ring-offset-0 bg-white dark:bg-slate-950">
                            </div>
                            <div class="ml-3 text-xs leading-relaxed">
                                <span class="block font-black text-slate-900 dark:text-white uppercase tracking-widest">UPI / Online Remittance</span>
                                <span class="block text-slate-500 dark:text-slate-400 mt-0.5">Remit directly to the corporate account and verify instantly.</span>
                            </div>
                        </label>
                    </div>
                </div>

                <button type="submit" class="w-full h-14 rounded-xl bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-500 hover:to-purple-500 text-white font-black uppercase tracking-widest text-xs shadow-xl shadow-indigo-600/15 active:scale-[0.98] transition-all flex items-center justify-center gap-2.5">
                    <i class="fa-solid fa-circle-check text-base"></i>
                    <span>Authorize Checkout Transaction & Order Placement</span>
                </button>
            </form>
        </div>

        <div class="lg:col-span-5">
            <div class="rounded-[1.75rem] border border-slate-200/70 dark:border-slate-800 bg-white/92 dark:bg-slate-900/82 p-6 shadow-lg shadow-slate-900/5 space-y-6 sticky top-24">
                <div class="pb-3 border-b border-slate-200/70 dark:border-slate-800">
                    <h3 class="text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-[0.35em]">Hardware Purchase Recap</h3>
                </div>

                <div class="max-h-64 overflow-y-auto pr-2 divide-y divide-slate-200/70 dark:divide-slate-800/70 custom-scrollbar space-y-3.5">
                    @foreach ($cart as $id => $item)
                        <div class="flex items-center gap-3 pt-3.5 first:pt-0">
                            <div class="w-10 h-10 rounded-lg flex items-center justify-center p-1 border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950/50 flex-shrink-0">
                                @if ($item['image'])
                                    <img src="/productimage/{{ $id }}/{{ $item['image'] }}" alt="{{ $item['name'] }}" class="max-h-full max-w-full object-contain">
                                @else
                                    <i class="fa-solid fa-cube text-slate-400 dark:text-slate-600 text-sm"></i>
                                @endif
                            </div>
                            <div class="min-w-0 flex-1">
                                <span class="block font-black text-slate-900 dark:text-white text-xs uppercase truncate">{{ $item['name'] }}</span>
                                <span class="block text-[10px] text-slate-500 dark:text-slate-400 font-mono mt-0.5">{{ $item['quantity'] }} {{ $item['unit'] }} x Rs. {{ \App\Helpers\NumberHelper::indianFormat($item['price']) }}</span>
                            </div>
                            <div class="text-right flex-shrink-0 font-mono font-black text-slate-900 dark:text-white text-xs">
                                Rs. {{ \App\Helpers\NumberHelper::indianFormat($item['price'] * $item['quantity']) }}
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="border-t border-slate-200/70 dark:border-slate-800 pt-6 space-y-3 text-xs">
                    <div class="flex items-center justify-between text-slate-500 dark:text-slate-400">
                        <span>Items Subtotal</span>
                        <span class="font-mono font-semibold text-slate-900 dark:text-white">Rs. {{ \App\Helpers\NumberHelper::indianFormat($subTotal) }}</span>
                    </div>
                    <div class="flex items-center justify-between text-slate-500 dark:text-slate-400">
                        <span>Estimated Tax (GST)</span>
                        <span class="font-mono text-slate-900 dark:text-white">Rs. {{ \App\Helpers\NumberHelper::indianFormat($taxTotal) }}</span>
                    </div>
                    <div class="flex items-center justify-between text-slate-500 dark:text-slate-400">
                        <span>Delivery Matrix</span>
                        <span class="font-mono text-emerald-600 dark:text-emerald-400 font-black uppercase">Free Shipping</span>
                    </div>

                    <div class="border-t border-slate-200/70 dark:border-slate-800 pt-4 mt-2 flex items-center justify-between text-sm font-black">
                        <span class="text-slate-900 dark:text-white uppercase tracking-[0.25em]">Grand Total Outstanding</span>
                        <span class="font-mono text-xl text-blue-600 dark:text-indigo-400">Rs. {{ \App\Helpers\NumberHelper::indianFormat($grandTotal) }}</span>
                    </div>
                </div>

                <div class="rounded-[1.5rem] border border-slate-200/70 dark:border-slate-800 bg-slate-50/90 dark:bg-slate-950/50 p-4 text-[10px] text-slate-500 dark:text-slate-400 leading-relaxed space-y-2">
                    <span class="block font-black text-slate-900 dark:text-white uppercase tracking-wider">Direct Bank Remittance Target</span>
                    <p>For instant dispatch, select the UPI/Online channel, place the order, and remit the grand total to our corporate account.</p>
                    <div class="rounded-2xl border border-slate-200/70 dark:border-slate-800 bg-white/90 dark:bg-slate-900/70 p-3 font-mono text-slate-700 dark:text-slate-300">
                        <p class="font-black text-slate-900 dark:text-white">INDIAN OVERSEAS BANK</p>
                        <p>Branch: Kuzhithurai</p>
                        <p>A/C: <span class="text-blue-600 dark:text-indigo-400">2869020000000349</span></p>
                        <p>IFSC: <span class="text-blue-600 dark:text-indigo-400">IOBA0002869</span></p>
                    </div>
                    <p class="text-[9px] text-slate-400 dark:text-slate-500">* Once payment is remitted, WhatsApp your checkout Receipt ID to +91 99442 28686 for verification.</p>
                </div>
            </div>
        </div>
    </div>
@endsection
