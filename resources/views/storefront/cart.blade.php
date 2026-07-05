@extends('layouts.storefront')

@section('content')
    <!-- Header -->
    <div class="mb-8 space-y-1">
        <h1 class="text-2xl font-black text-slate-900 dark:text-white uppercase tracking-tight">Shopping Cart</h1>
        <p class="text-xs text-slate-500">Review quantities, check totals, and proceed to checkout.</p>
    </div>

    @if (session()->has('added_item'))
        @php $added = session('added_item'); @endphp
        <!-- ── Snapdeal-Style Added to Cart Success Panel ── -->
        <div class="mb-6 bg-emerald-50 dark:bg-emerald-950/20 border border-emerald-200 dark:border-emerald-900 rounded-xl p-4 shadow-sm flex flex-col md:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-3.5">
                <div class="w-10 h-10 rounded-full bg-emerald-500 text-white flex items-center justify-center text-lg shrink-0">
                    <i class="fa-solid fa-circle-check"></i>
                </div>
                <div class="min-w-0">
                    <span class="block text-xs font-black uppercase tracking-wider text-emerald-800 dark:text-emerald-400">1 Item Added to Cart</span>
                    <div class="flex items-center gap-3 mt-1.5 flex-wrap">
                        <div class="w-8 h-8 bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded p-0.5 flex-shrink-0 flex items-center justify-center">
                            @if ($added['image'])
                                <img src="/storage/productimage/{{ $added['id'] }}/{{ $added['image'] }}" alt="{{ $added['name'] }}" class="max-h-full max-w-full object-contain">
                            @else
                                <i class="fa-solid fa-cube text-slate-400 text-[10px]"></i>
                            @endif
                        </div>
                        <span class="text-xs font-bold text-slate-850 dark:text-white uppercase truncate max-w-[200px]">{{ $added['name'] }}</span>
                        <span class="text-[10px] text-slate-550 dark:text-slate-450 font-mono font-bold">Qty: {{ $added['quantity'] }} {{ $added['unit'] }}</span>
                        <span class="text-[11px] font-mono text-[#0059e3] font-black">Rs. {{ \App\Helpers\NumberHelper::indianFormat($added['price'] * $added['quantity']) }}</span>
                    </div>
                </div>
            </div>
            
            <div class="flex items-center gap-3 shrink-0">
                <a href="{{ route('storefront.checkout') }}" class="px-5 py-2.5 text-white text-xs font-black uppercase tracking-wider rounded-lg transition-all shadow-sm" style="background-color: #0059e3;">
                    Proceed to Checkout
                </a>
            </div>
        </div>
    @endif

    @if (count($cart) > 0)
        <!-- Main Grid Layout -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            
            <!-- Left Side: Cart Items List (8 cols) -->
            <div class="lg:col-span-8">
                <form action="{{ route('storefront.cart.update') }}" method="POST" class="space-y-4">
                    @csrf
                    
                    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl shadow-sm p-5 space-y-4">
                        <div class="hidden sm:grid grid-cols-12 gap-4 text-[10px] font-extrabold text-slate-400 uppercase tracking-wider border-b border-slate-100 dark:border-slate-800 pb-3">
                            <span class="col-span-6">Item Description</span>
                            <span class="col-span-2 text-right">Unit Price</span>
                            <span class="col-span-2 text-center">Quantity</span>
                            <span class="col-span-2 text-right font-bold" style="color: #0059e3;">Line Total</span>
                        </div>

                        <div class="divide-y divide-slate-150 dark:divide-slate-800 space-y-4">
                            @foreach ($cart as $id => $item)
                                <div class="grid grid-cols-1 sm:grid-cols-12 gap-4 items-center pt-4 first:pt-0">
                                    <!-- Thumbnail & details (6 cols) -->
                                    <div class="col-span-1 sm:col-span-6 flex items-center gap-3.5">
                                        <div class="w-14 h-14 bg-slate-50 dark:bg-slate-950 rounded-lg flex items-center justify-center p-1.5 border border-slate-200 dark:border-slate-800 flex-shrink-0">
                                            @if ($item['image'])
                                                <img src="/storage/productimage/{{ $id }}/{{ $item['image'] }}" alt="{{ $item['name'] }}" class="max-h-full max-w-full object-contain">
                                            @else
                                                <i class="fa-solid fa-cube text-slate-400 text-xl"></i>
                                            @endif
                                        </div>
                                        <div class="min-w-0">
                                            <a href="{{ route('storefront.product', $id) }}" class="block font-bold text-slate-900 dark:text-slate-100 text-xs uppercase truncate hover:text-[#0059e3] transition-colors">
                                                {{ $item['name'] }}
                                            </a>
                                            <div class="flex items-center gap-2 mt-1 text-[9px] font-medium text-slate-500">
                                                <span class="font-mono">HSN: {{ $item['hsnsac'] ?: '84713010' }}</span>
                                                <span>•</span>
                                                <span class="uppercase text-[#0059e3] font-bold">{{ $item['unit'] }}</span>
                                                <span>•</span>
                                                <span>{{ intval($item['gst']) }}% GST</span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Price (2 cols) -->
                                    <div class="col-span-1 sm:col-span-2 text-left sm:text-right">
                                        <span class="block sm:hidden text-[9px] text-slate-400 uppercase font-bold">Unit Price</span>
                                        <span class="font-mono font-bold text-slate-700 dark:text-slate-350 text-xs">
                                            Rs. {{ \App\Helpers\NumberHelper::indianFormat($item['price']) }}
                                        </span>
                                    </div>

                                    <!-- Qty (2 cols) -->
                                    <div class="col-span-1 sm:col-span-2 flex items-center justify-center">
                                        <input type="number" 
                                               name="quantities[{{ $id }}]" 
                                               value="{{ $item['quantity'] }}" 
                                               min="1" 
                                               class="w-12 text-center font-mono font-bold text-slate-800 dark:text-slate-200 border border-slate-200 dark:border-slate-800 rounded-lg px-1 py-1 text-xs bg-slate-50 dark:bg-slate-950 focus:ring-1 focus:ring-[#0059e3] outline-none">
                                    </div>

                                    <!-- Total (2 cols) -->
                                    <div class="col-span-1 sm:col-span-12 lg:col-span-2 flex items-center justify-between sm:justify-end gap-2 text-right">
                                        <div>
                                            <span class="block sm:hidden text-[9px] text-slate-400 uppercase font-bold">Total</span>
                                            <span class="font-mono font-black text-xs" style="color: #0059e3;">
                                                Rs. {{ \App\Helpers\NumberHelper::indianFormat($item['price'] * $item['quantity']) }}
                                            </span>
                                        </div>
                                        
                                        <!-- Remove item -->
                                        <a href="{{ route('storefront.cart.remove', $id) }}" class="p-1.5 text-slate-400 hover:text-blue-500 rounded transition-colors ml-3" title="Remove Item">
                                            <i class="fa-solid fa-trash-can text-xs"></i>
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Update row -->
                        <div class="pt-4 border-t border-slate-150 dark:border-slate-800 flex items-center justify-between gap-4">
                            <a href="{{ route('storefront.index') }}" class="text-[11px] font-bold text-slate-500 hover:text-[#0059e3] uppercase transition-colors">
                                <i class="fa-solid fa-chevron-left mr-1"></i> Continue Shopping
                            </a>
                            <button type="submit" class="px-4 py-2 border border-slate-800 dark:border-slate-700 text-slate-800 dark:text-slate-200 hover:bg-slate-800 hover:text-white rounded-lg text-xs font-bold transition-all cursor-pointer bg-transparent">
                                Update Cart
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Right Side: Order Summary Card (4 cols) -->
            <div class="lg:col-span-4">
                <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-5 shadow-sm space-y-4 sticky top-24">
                    <span class="block text-xs font-black uppercase tracking-wider text-slate-700 dark:text-slate-300 border-b border-slate-100 dark:border-slate-800 pb-2.5">Order Summary</span>

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

                    <div class="space-y-3 text-xs">
                        <div class="flex items-center justify-between text-slate-500">
                            <span>Subtotal:</span>
                            <span class="font-mono font-bold text-slate-800 dark:text-slate-200">Rs. {{ \App\Helpers\NumberHelper::indianFormat($subTotal) }}</span>
                        </div>
                        <div class="flex items-center justify-between text-slate-500">
                            <span>GST Ledger:</span>
                            <span class="font-mono text-slate-800 dark:text-slate-200">Rs. {{ \App\Helpers\NumberHelper::indianFormat($taxTotal) }}</span>
                        </div>
                        <div class="flex items-center justify-between text-slate-500">
                            <span>Shipping charges:</span>
                            <span class="text-emerald-600 font-bold uppercase">Free</span>
                        </div>
                        <div class="border-t border-slate-100 dark:border-slate-800 pt-3 mt-1 flex items-center justify-between text-sm font-bold">
                            <span class="text-slate-800 dark:text-white">Grand Total:</span>
                            <span class="font-mono text-base" style="color: #0059e3;">Rs. {{ \App\Helpers\NumberHelper::indianFormat($grandTotal) }}</span>
                        </div>
                    </div>

                    <!-- Checkout Button -->
                    <div class="pt-3 mt-1 border-t border-slate-100 dark:border-slate-800">
                        <a href="{{ route('storefront.checkout') }}" class="w-full h-11 text-white font-bold rounded-lg shadow-sm transition-all flex items-center justify-center gap-1.5 cursor-pointer text-xs uppercase tracking-wider" style="background-color: #0059e3;">
                            <span>Proceed to Checkout</span>
                            <i class="fa-solid fa-chevron-right text-[10px]"></i>
                        </a>
                        <p class="text-[10px] text-slate-400 text-center mt-2.5">
                            <i class="fa-solid fa-lock text-[#0059e3] mr-1"></i> Payments and details are transaction-safe
                        </p>
                    </div>
                </div>
            </div>
        </div>
    @else
        <!-- Empty Cart -->
        <div class="text-center py-16 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl max-w-md mx-auto shadow-sm p-6">
            <div class="h-14 w-14 bg-slate-50 dark:bg-slate-950 rounded-full flex items-center justify-center text-slate-400 text-3xl mx-auto mb-4">
                <i class="fa-solid fa-shopping-basket"></i>
            </div>
            <h3 class="text-sm font-black uppercase text-slate-900 dark:text-slate-200">Your Cart is Empty</h3>
            <p class="text-xs text-slate-500 mt-2">Explore the storefront catalog to add items to your cart.</p>
            <a href="{{ route('storefront.index') }}" class="inline-block mt-5 px-5 py-2.5 text-white text-xs font-bold rounded-lg shadow-sm transition-all" style="background-color: #0059e3;">
                Return to Shop
            </a>
        </div>
    @endif
@endsection
