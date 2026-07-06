@extends('layouts.storefront')

@section('content')
    @php
        $sellingRate = $product->display_price;
        $productName = html_entity_decode($product->productname, ENT_QUOTES, 'UTF-8');
        $productDescription = $product->productdes ? html_entity_decode($product->productdes, ENT_QUOTES, 'UTF-8') : null;
        $discountAmount = $product->mrp > $sellingRate ? ($product->mrp - $sellingRate) : 0;
        $discountPercent = $product->mrp > $sellingRate && $product->mrp > 0 ? round(($discountAmount / $product->mrp) * 100) : 0;
        $gstRate = intval($product->gst ?: 0);
    @endphp

    <!-- Breadcrumbs -->
    <nav class="flex items-center gap-2 text-[11px] font-medium tracking-wide mb-6 flex-wrap text-slate-500">
        <a href="{{ route('storefront.index') }}" class="hover:text-[#0059e3] transition-colors">Home</a>
        <i class="fa-solid fa-chevron-right text-[8px] text-slate-400"></i>
        @if ($product->category)
            <a href="{{ route('storefront.category', $product->category->cat_name) }}" class="hover:text-[#0059e3] transition-colors">
                {{ $product->category->cat_name }}
            </a>
            <i class="fa-solid fa-chevron-right text-[8px] text-slate-400"></i>
        @endif
        <span class="text-slate-700 font-bold truncate max-w-[200px]">{{ $productName }}</span>
    </nav>

    <!-- Product Details Main Layout (Snapdeal Grid Structure) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start mb-16">

        <!-- ============================================================
             LEFT SIDE: Product Media, Delivery Check & Actions
             ============================================================ -->
        <div class="lg:col-span-5 space-y-5">
            <!-- Main Showcase Image Container -->
            <div class="relative bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl aspect-square flex items-center justify-center p-6 shadow-sm">
                @if ($product->pimagef)
                    <img src="{{ $product->primary_image_url }}"
                         alt="{{ $productName }}"
                         class="max-h-[90%] max-w-[90%] object-contain"
                         id="main-product-img">
                @else
                    <div class="flex flex-col items-center gap-2 text-slate-400">
                        <i class="fa-solid fa-box-open text-5xl"></i>
                        <span class="text-[10px] uppercase font-bold tracking-wider">No Image Available</span>
                    </div>
                @endif

                <!-- Discount percentage overlay badge -->
                @if ($discountPercent > 0)
                    <div class="absolute top-4 left-4 bg-[#0059e3] text-white text-xs font-black px-2.5 py-1 rounded shadow-sm">
                        {{ $discountPercent }}% OFF
                    </div>
                @endif
            </div>

            <!-- Form & Buy Actions -->
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-5 shadow-sm space-y-4">
                @if ($product->tqty > 0)
                    <form action="{{ route('storefront.cart.add', $product->id) }}" method="POST" class="space-y-4">
                        @csrf
                        <div class="flex items-center gap-3">
                            <label class="text-xs font-bold text-slate-700 dark:text-slate-330">Quantity:</label>
                            <div class="flex items-center bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-lg px-2 h-9 shadow-inner"
                                 x-data="{ qty: 1, maxStock: {{ $product->tqty }} }">
                                <button type="button" @click="if(qty > 1) qty--" class="w-6 h-6 text-slate-500 hover:text-slate-800 cursor-pointer"><i class="fa-solid fa-minus text-[10px]"></i></button>
                                <input type="number" name="quantity" x-model="qty" readonly class="w-10 bg-transparent border-none text-center font-bold text-slate-900 dark:text-white outline-none focus:ring-0 text-sm py-0">
                                <button type="button" @click="if(qty < maxStock) qty++" class="w-6 h-6 text-slate-500 hover:text-slate-800 cursor-pointer"><i class="fa-solid fa-plus text-[10px]"></i></button>
                            </div>
                            <span class="text-[11px] text-slate-500 font-medium">({{ $product->tqty }} units left)</span>
                        </div>

                        <!-- Action Buttons -->
                        <div class="grid grid-cols-2 gap-3 pt-2">
                            <!-- ADD TO CART -->
                            <button type="submit" class="h-12 border border-slate-850 dark:border-slate-700 hover:bg-slate-800 hover:text-white text-slate-800 dark:text-slate-200 text-xs font-black uppercase tracking-wider rounded-lg transition-all flex items-center justify-center gap-2 cursor-pointer bg-transparent">
                                <i class="fa-solid fa-cart-shopping"></i>
                                <span>Add to Cart</span>
                            </button>

                            <!-- BUY NOW -->
                            <button type="submit" name="checkout_direct" value="1" class="h-12 text-white text-xs font-black uppercase tracking-wider rounded-lg transition-all flex items-center justify-center gap-2 cursor-pointer shadow-md" style="background-color: #0059e3;">
                                <i class="fa-solid fa-bolt"></i>
                                <span>Buy Now</span>
                            </button>
                        </div>
                    </form>
                @else
                    <div class="p-4 bg-rose-50 border border-rose-200 text-rose-600 rounded-lg flex items-center gap-2.5 text-xs font-bold justify-center">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                        <span>OUT OF STOCK / SOLD OUT</span>
                    </div>
                @endif
            </div>

            <!-- Delivery & Pinchecker widget -->
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-5 shadow-sm space-y-3">
                <span class="block text-xs font-black uppercase tracking-wider text-slate-700 dark:text-slate-300">Delivery Services</span>
                <div class="flex items-center gap-2">
                    <div class="relative flex-1">
                        <i class="fa-solid fa-location-dot absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                        <input type="text" placeholder="Enter Delivery Pincode" class="w-full pl-9 pr-4 py-2 border border-slate-200 dark:border-slate-800 rounded-lg text-xs outline-none bg-slate-50 dark:bg-slate-950 text-slate-800 dark:text-slate-200">
                    </div>
                    <button class="px-4 py-2 border border-[#0059e3] text-[#0059e3] hover:bg-[#0059e3] hover:text-white transition-all rounded-lg text-xs font-bold cursor-pointer bg-transparent">Check</button>
                </div>
                <div class="text-[10px] text-slate-500 space-y-1">
                    <p class="flex items-center gap-1.5"><i class="fa-solid fa-circle-check text-emerald-500"></i> Free Shipping on all orders</p>
                    <p class="flex items-center gap-1.5"><i class="fa-solid fa-circle-check text-emerald-500"></i> 7 Days Easy Returns available</p>
                </div>
            </div>
        </div>

        <!-- ============================================================
             RIGHT SIDE: Product Specifications, Pricing, Overview
             ============================================================ -->
        <div class="lg:col-span-7 space-y-6">

            <!-- Title & Rating Info -->
            <div class="space-y-2.5">
                @if ($product->brand)
                    <span class="text-[10px] font-black uppercase tracking-widest text-[#0059e3] bg-blue-50 dark:bg-blue-950/20 px-2 py-0.5 rounded border border-blue-150 dark:border-blue-900/40">
                        {{ $product->brand->brand_name }}
                    </span>
                @endif
                <h1 class="text-xl sm:text-2xl lg:text-3xl font-black text-slate-900 dark:text-white uppercase tracking-tight leading-tight">
                    {{ $productName }}
                </h1>
                <div class="flex items-center gap-1.5 text-xs">
                    <div class="flex items-center gap-0.5 text-amber-500">
                        <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i>
                    </div>
                    <span class="text-slate-400 dark:text-slate-500">|</span>
                    <span class="text-slate-600 dark:text-slate-400 font-semibold">5.0 Verified Ratings</span>
                </div>
            </div>

            <!-- Pricing Box -->
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-6 shadow-sm space-y-4">
                <div class="flex flex-wrap items-baseline gap-3">
                    <span class="text-3xl font-black text-[#0059e3] font-mono">
                        Rs. {{ \App\Helpers\NumberHelper::indianFormat($sellingRate) }}
                    </span>
                    @if ($product->mrp > $sellingRate)
                        <span class="text-sm text-slate-400 line-through">
                            MRP Rs. {{ \App\Helpers\NumberHelper::indianFormat($product->mrp) }}
                        </span>
                        <span class="text-xs font-bold text-emerald-600 bg-emerald-500/10 px-2.5 py-1 rounded border border-emerald-500/10">
                            {{ $discountPercent }}% OFF
                        </span>
                    @endif
                </div>

                <div class="text-[11px] text-slate-500 space-y-1 border-t border-slate-100 dark:border-slate-800 pt-3">
                    <p>Inclusive of {{ $gstRate }}% GST (CGST/SGST/IGST credit invoice available)</p>
                    <p>Free Delivery across all Indian states and pin codes.</p>
                </div>
            </div>

            <!-- Offers & Promotions -->
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-5 shadow-sm space-y-3">
                <span class="block text-xs font-black uppercase tracking-wider text-slate-700 dark:text-slate-350">Offers & Discounts</span>
                <div class="space-y-2 text-xs">
                    <div class="flex items-start gap-2.5 p-2 bg-slate-50 dark:bg-slate-950 border border-slate-150 dark:border-slate-800/80 rounded-lg">
                        <i class="fa-solid fa-percent text-blue-500 mt-0.5"></i>
                        <div>
                            <span class="font-bold block">Direct Bank Remittance Offer</span>
                            <span class="text-slate-500">Pay directly via bank transfer / UPI to get instant order verification and dispatch priority.</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Specs Table -->
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl shadow-sm overflow-hidden">
                <div class="px-5 py-3 border-b border-slate-150 dark:border-slate-800 bg-slate-50 dark:bg-slate-950">
                    <span class="text-xs font-black uppercase tracking-wider text-slate-700 dark:text-slate-300">Specifications & Details</span>
                </div>
                <table class="w-full text-xs text-left divide-y divide-slate-150 dark:divide-slate-800">
                    <tbody class="divide-y divide-slate-150 dark:divide-slate-800">
                        <tr class="grid grid-cols-2 p-3.5">
                            <td class="font-bold text-slate-500 uppercase tracking-wider text-[10px]">Brand / Manufacturer</td>
                            <td class="font-semibold text-slate-900 dark:text-slate-100">{{ $product->brand ? $product->brand->brand_name : 'Import' }}</td>
                        </tr>
                        <tr class="grid grid-cols-2 p-3.5">
                            <td class="font-bold text-slate-500 uppercase tracking-wider text-[10px]">HSN / SAC Code</td>
                            <td class="font-semibold text-slate-900 dark:text-slate-100 font-mono">{{ $product->hsnsac ?: '84713010' }}</td>
                        </tr>
                        <tr class="grid grid-cols-2 p-3.5">
                            <td class="font-bold text-slate-500 uppercase tracking-wider text-[10px]">Tax Rate (GST)</td>
                            <td class="font-semibold text-slate-900 dark:text-slate-100 font-mono">{{ $gstRate }}%</td>
                        </tr>
                        <tr class="grid grid-cols-2 p-3.5">
                            <td class="font-bold text-slate-500 uppercase tracking-wider text-[10px]">Packaging Unit</td>
                            <td class="font-semibold text-slate-900 dark:text-slate-100 uppercase">{{ $product->unit == 2 ? 'BOX' : ($product->unit == 3 ? 'PKT' : 'PCS') }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Product Description Overview -->
            @if ($productDescription)
                <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-5 shadow-sm space-y-3">
                    <span class="block text-xs font-black uppercase tracking-wider text-slate-700 dark:text-slate-300">Product Description</span>
                    <p class="text-xs leading-relaxed text-slate-600 dark:text-slate-400 whitespace-pre-line">{{ $productDescription }}</p>
                </div>
            @endif

        </div>
    </div>

    <!-- Related Products -->
    @if (count($relatedProducts) > 0)
        <div class="border-t border-slate-200 dark:border-slate-800 pt-10 mb-8">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-base font-black uppercase tracking-tight text-slate-900 dark:text-white">Related Products</h3>
                    <p class="text-[10px] text-slate-500 uppercase font-bold tracking-wider mt-0.5">Explore matches from the same collection</p>
                </div>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                @foreach ($relatedProducts as $rel)
                    <x-product-card :product="$rel" />
                @endforeach
            </div>
        </div>
    @endif
@endsection
