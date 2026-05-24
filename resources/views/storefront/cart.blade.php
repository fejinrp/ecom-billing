@extends('layouts.storefront')

@section('content')
    <!-- Header -->
    <div class="mb-10 space-y-2">
        <h1 class="text-3xl font-black text-slate-900 dark:text-white uppercase tracking-tight">Shopping Cart</h1>
        <p class="text-xs text-slate-500 dark:text-slate-400 max-w-2xl">
            Review quantities, check totals, and continue when the order is ready.
        </p>
    </div>

    @if (count($cart) > 0)
        <!-- Main Layout: 12 Cols -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 md:gap-10">
            
            <!-- Left: Cart Items List (8 cols) -->
            <div class="lg:col-span-8">
                <form action="{{ route('storefront.cart.update') }}" method="POST">
                    @csrf
                    
                    <div class="sf-cart-panel p-6 bg-white/92 dark:bg-slate-900/82 border border-slate-200/70 dark:border-slate-800/70 rounded-[2rem] space-y-6 shadow-2xl shadow-blue-500/5">
                        <div class="hidden sm:grid grid-cols-12 gap-4 text-[10px] font-extrabold text-slate-500 dark:text-slate-400 uppercase tracking-widest border-b border-slate-200/70 dark:border-slate-800/70 pb-3">
                            <span class="col-span-6">Hardware Item Spec</span>
                            <span class="col-span-2 text-right">Selling Rate</span>
                            <span class="col-span-2 text-center">Quantity</span>
                            <span class="col-span-2 text-right">Line Total</span>
                        </div>

                        <div class="divide-y divide-slate-200/70 dark:divide-slate-800/70 space-y-6">
                            @foreach ($cart as $id => $item)
                                <div class="grid grid-cols-1 sm:grid-cols-12 gap-4 items-center pt-6 first:pt-0">
                                    <!-- Spec details (6 cols) -->
                                    <div class="col-span-1 sm:col-span-6 flex items-center gap-4">
                                        <div class="w-16 h-16 bg-slate-50 dark:bg-slate-950/70 rounded-2xl flex items-center justify-center p-2 border border-slate-200/70 dark:border-slate-800/70 flex-shrink-0 shadow-sm">
                                            @if ($item['image'])
                                                <img src="/productimage/{{ $id }}/{{ $item['image'] }}" alt="{{ $item['name'] }}" class="max-h-full max-w-full object-contain">
                                            @else
                                                <i class="fa-solid fa-cube text-slate-400 dark:text-slate-700 text-2xl"></i>
                                            @endif
                                        </div>
                                        <div class="min-w-0">
                                            <a href="{{ route('storefront.product', $id) }}" class="block font-bold text-slate-900 dark:text-slate-100 text-sm uppercase truncate hover:text-indigo-500 dark:hover:text-indigo-400 transition-colors">
                                                {{ $item['name'] }}
                                            </a>
                                            <div class="flex flex-wrap items-center gap-2 mt-1.5 text-[10px]">
                                                <span class="px-2 py-0.5 rounded-full bg-slate-100 dark:bg-slate-950 text-slate-500 dark:text-slate-400 font-mono">HSN: {{ $item['hsnsac'] ?: '84713010' }}</span>
                                                <span class="px-2 py-0.5 rounded-full bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 font-bold uppercase">{{ $item['unit'] }}</span>
                                                <span class="px-2 py-0.5 rounded-full bg-purple-500/10 text-purple-600 dark:text-purple-400 font-semibold">{{ intval($item['gst']) }}% GST</span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Price (2 cols) -->
                                    <div class="col-span-1 sm:col-span-2 text-left sm:text-right">
                                        <span class="block sm:hidden text-[9px] text-slate-500 uppercase font-semibold">Unit Price</span>
                                        <span class="font-mono font-bold text-slate-700 dark:text-slate-300 text-sm">
                                            Rs. {{ \App\Helpers\NumberHelper::indianFormat($item['price']) }}
                                        </span>
                                    </div>

                                    <!-- Qty Selector (2 cols) -->
                                    <div class="col-span-1 sm:col-span-2 flex items-center justify-center">
                                        <div class="flex items-center bg-slate-50 dark:bg-slate-950/70 border border-slate-200/70 dark:border-slate-800/70 rounded-xl px-1 h-10 shadow-sm">
                                            <input type="number" 
                                                   name="quantities[{{ $id }}]" 
                                                   value="{{ $item['quantity'] }}" 
                                                   min="1" 
                                                   class="w-12 bg-transparent border-none text-center font-mono font-bold text-slate-900 dark:text-slate-100 focus:ring-0 text-xs py-0">
                                        </div>
                                    </div>

                                    <!-- Total (2 cols) -->
                                    <div class="col-span-1 sm:col-span-12 lg:col-span-2 flex items-center justify-between sm:justify-end gap-3 text-right">
                                        <div>
                                            <span class="block sm:hidden text-[9px] text-slate-500 uppercase font-semibold">Item Total</span>
                                            <span class="font-mono font-extrabold text-indigo-600 dark:text-indigo-400 text-sm">
                                                Rs. {{ \App\Helpers\NumberHelper::indianFormat($item['price'] * $item['quantity']) }}
                                            </span>
                                        </div>
                                        
                                        <!-- Remove anchor -->
                                        <a href="{{ route('storefront.cart.remove', $id) }}" class="p-2 text-slate-400 hover:text-rose-500 rounded-lg hover:bg-rose-500/10 transition-all ml-4" title="Remove Item">
                                            <i class="fa-solid fa-trash-can text-sm"></i>
                                        </a>
                                    </div>

                                </div>
                            @endforeach
                        </div>

                        <!-- Card Action row -->
                        <div class="pt-6 border-t border-slate-200/70 dark:border-slate-800/70 flex items-center justify-between gap-4">
                            <a href="{{ route('storefront.index') }}" class="px-4 py-2 text-xs font-bold text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white uppercase transition-colors">
                                <i class="fa-solid fa-arrow-left mr-1.5"></i> Continue Browsing
                            </a>
                            <button type="submit" class="px-5 py-2.5 bg-slate-950 dark:bg-slate-900 hover:bg-slate-800 border border-slate-800/70 dark:border-slate-700 hover:border-slate-700 text-white rounded-xl text-xs font-bold transition-all shadow-sm">
                                <i class="fa-solid fa-arrows-rotate mr-1.5 animate-spin-slow"></i> Update Cart Quantities
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Right: Transaction Summary (4 cols) -->
            <div class="lg:col-span-4">
                <div class="sf-cart-summary p-6 bg-white/92 dark:bg-slate-900/82 border border-slate-200/70 dark:border-slate-800/70 rounded-[2rem] space-y-6 sticky top-24 shadow-2xl shadow-blue-500/5 backdrop-blur-sm">
                    <h3 class="text-xs font-extrabold text-slate-700 dark:text-slate-300 uppercase tracking-widest border-b border-slate-200/70 dark:border-slate-800/70 pb-3">Order Summary</h3>

                    <!-- Calculation Details -->
                    @php
                        $subTotal = 0;
                        $taxTotal = 0;
                        foreach($cart as $item) {
                            $lineVal = $item['quantity'] * $item['price'];
                            $subTotal += $lineVal;
                            $taxTotal += $lineVal * ($item['gst'] / 100);
                        }
                        $grandTotal = $subTotal + $taxTotal;
                    @endphp
                    <div class="space-y-3.5 text-xs">
                        <div class="flex items-center justify-between text-slate-500 dark:text-slate-400">
                            <span>Subtotal Amount:</span>
                            <span class="font-mono font-semibold text-slate-900 dark:text-slate-200">Rs. {{ \App\Helpers\NumberHelper::indianFormat($subTotal) }}</span>
                        </div>
                        
                        <div class="flex items-center justify-between text-slate-500 dark:text-slate-400">
                            <span>Computed GST Ledger:</span>
                            <span class="font-mono text-slate-900 dark:text-slate-300">Rs. {{ \App\Helpers\NumberHelper::indianFormat($taxTotal) }}</span>
                        </div>

                        <div class="flex items-center justify-between text-slate-500 dark:text-slate-400">
                            <span>Logistics / Shipping:</span>
                            <span class="font-mono text-emerald-400 font-bold uppercase">Free Delivery</span>
                        </div>

                        <div class="border-t border-slate-200/70 dark:border-slate-800/70 pt-4 mt-2 flex items-center justify-between text-sm font-bold text-slate-900 dark:text-white">
                            <span class="font-outfit text-base">Grand Total (Incl. Tax):</span>
                            <span class="font-mono text-lg text-indigo-600 dark:text-indigo-400">Rs. {{ \App\Helpers\NumberHelper::indianFormat($grandTotal) }}</span>
                        </div>
                    </div>

                    <!-- Secure Checkout Action -->
                    <div class="pt-4 mt-2 border-t border-slate-200/70 dark:border-slate-800/70">
                        <a href="{{ route('storefront.checkout') }}" class="w-full h-14 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-500 hover:to-purple-500 text-white font-bold rounded-xl shadow-xl shadow-indigo-600/10 active:scale-95 transition-all flex items-center justify-center gap-2">
                            <span>Proceed to Secure Checkout</span>
                            <i class="fa-solid fa-circle-arrow-right text-base"></i>
                        </a>
                        <p class="text-[10px] text-slate-500 dark:text-slate-400 text-center mt-3 leading-relaxed">
                            <i class="fa-solid fa-lock text-indigo-400 mr-1"></i> Transaction processes are transaction-safe, cryptographically secure and comply with corporate banking audits.
                        </p>
                    </div>

                </div>
            </div>

        </div>
    @else
        <!-- Empty Cart -->
        <div class="text-center py-20 bg-white/92 dark:bg-slate-900/82 border border-slate-200/70 dark:border-slate-800/70 rounded-[2rem] p-8 max-w-md mx-auto shadow-2xl shadow-blue-500/5">
            <div class="h-20 w-20 bg-slate-50 dark:bg-slate-950/70 rounded-2xl flex items-center justify-center text-slate-400 dark:text-slate-600 text-4xl mx-auto mb-4 border border-slate-200/70 dark:border-slate-800/70">
                <i class="fa-solid fa-basket-shopping"></i>
            </div>
            <h3 class="text-lg font-bold text-slate-900 dark:text-slate-200 uppercase">Cart is Empty</h3>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-2 leading-relaxed">
                Start exploring to add hardware components and build your order.
            </p>
            <a href="{{ route('storefront.index') }}" class="inline-block mt-6 px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl transition-all shadow-lg shadow-indigo-600/10">
                Return to Product Browser
            </a>
        </div>
    @endif
@endsection
