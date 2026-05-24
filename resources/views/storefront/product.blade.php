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
    <nav class="flex items-center gap-2 text-[10px] font-black uppercase tracking-widest mb-8 flex-wrap text-slate-500 dark:text-slate-400">
        <a href="{{ route('storefront.index') }}" class="text-blue-600 dark:text-indigo-400 hover:text-blue-500 dark:hover:text-indigo-300 transition-colors">Store</a>
        <i class="fa-solid fa-chevron-right text-[8px] text-slate-400 dark:text-slate-600"></i>
        @if ($product->category)
            <a href="{{ route('storefront.category', $product->category->cat_name) }}" class="text-blue-600 dark:text-indigo-400 hover:text-blue-500 dark:hover:text-indigo-300 transition-colors">
                {{ $product->category->cat_name }}
            </a>
            <i class="fa-solid fa-chevron-right text-[8px] text-slate-400 dark:text-slate-600"></i>
        @endif
        <span class="text-slate-500 dark:text-slate-400 truncate max-w-[200px]">{{ $productName }}</span>
    </nav>

    <!-- Product Layout Grid -->
    <div class="rounded-[2rem] border border-slate-200/70 dark:border-slate-800/70 bg-gradient-to-br from-white/80 via-white/55 to-slate-50/80 dark:from-slate-950/45 dark:via-slate-950/30 dark:to-slate-900/40 p-4 sm:p-6 lg:p-8 shadow-2xl shadow-slate-900/5 mb-16">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 xl:gap-10 items-start">

        <!-- ============================================================
             LEFT COLUMN — Image + Trust Badges (5 cols on lg)
             ============================================================ -->
        <div class="flex flex-col gap-4 lg:col-span-5 xl:col-span-5 min-w-0">

            <!-- Main Product Image Showcase -->
            <div class="relative rounded-3xl overflow-hidden bg-white/92 dark:bg-slate-900/82 border border-slate-200/70 dark:border-slate-800/70 aspect-square lg:aspect-[4/3] xl:aspect-square flex items-center justify-center p-5 sm:p-8 shadow-2xl shadow-blue-500/5 group">
                <!-- Glow layer -->
                <div class="absolute inset-0 bg-gradient-to-tr from-indigo-500/5 via-transparent to-purple-500/5 opacity-0 group-hover:opacity-100 transition-opacity duration-500 pointer-events-none"></div>
                <div class="absolute -right-16 -top-16 w-48 h-48 bg-indigo-500/5 rounded-full blur-3xl pointer-events-none"></div>

                @if ($product->pimagef)
                    <img src="{{ $product->primary_image_url }}"
                         alt="{{ $productName }}"
                         onerror="this.onerror=null;this.src='data:image/svg+xml;utf8,<svg xmlns=&quot;http://www.w3.org/2000/svg&quot; width=&quot;800&quot; height=&quot;800&quot; viewBox=&quot;0 0 800 800&quot;><rect width=&quot;800&quot; height=&quot;800&quot; fill=&quot;%230f172a&quot;/><rect x=&quot;240&quot; y=&quot;210&quot; width=&quot;320&quot; height=&quot;280&quot; rx=&quot;36&quot; fill=&quot;%231e293b&quot;/><path d=&quot;M400 300l-96 70h192z&quot; fill=&quot;%2394a3b8&quot;/><circle cx=&quot;400&quot; cy=&quot;430&quot; r=&quot;36&quot; fill=&quot;%2394a3b8&quot;/></svg>'"
                        class="max-h-[92%] max-w-[92%] object-contain group-hover:scale-[1.04] transition-transform duration-700 relative z-10">
                @else
                    <div class="flex flex-col items-center gap-4 text-slate-700 dark:text-slate-400">
                        <div class="w-20 h-20 rounded-3xl bg-slate-100 dark:bg-slate-900/80 flex items-center justify-center border border-slate-200 dark:border-slate-800">
                            <i class="fa-solid fa-cubes text-5xl text-slate-500 dark:text-slate-600"></i>
                        </div>
                        <span class="text-xs uppercase tracking-widest font-black text-slate-500 dark:text-slate-400">Image Pending Upload</span>
                    </div>
                @endif

                <!-- Stock Tag -->
                <div class="absolute top-4 left-4 z-20">
                    @if ($product->tqty > 0)
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-[10px] font-black bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20 shadow-lg backdrop-blur-md">
                            <span class="w-2 h-2 bg-emerald-400 rounded-full animate-pulse"></span> IN STOCK &amp; READY TO SHIP
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-[10px] font-black bg-rose-500/10 text-rose-600 dark:text-rose-400 border border-rose-500/20 shadow-lg backdrop-blur-md">
                            <span class="w-2 h-2 bg-rose-400 rounded-full"></span> TEMPORARILY OUT OF STOCK
                        </span>
                    @endif
                </div>
            </div>

            <!-- Trust & Certification Badges -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-1 xl:grid-cols-2 gap-3">
                <div class="flex items-center gap-3 p-4 bg-white/90 dark:bg-slate-900/70 border border-slate-200/70 dark:border-slate-800/70 rounded-2xl shadow-sm">
                    <div class="w-10 h-10 flex-shrink-0 rounded-xl bg-indigo-500/10 border border-indigo-500/15 flex items-center justify-center text-indigo-600 dark:text-indigo-400 text-base">
                        <i class="fa-solid fa-shield-halved"></i>
                    </div>
                    <div>
                        <span class="block text-[10px] font-black text-slate-900 dark:text-slate-100 uppercase tracking-wider">MTL Certified</span>
                        <span class="block text-[9px] text-slate-500 dark:text-slate-400 leading-relaxed">100% genuine import. Real-time serial match.</span>
                    </div>
                </div>
                <div class="flex items-center gap-3 p-4 bg-white/90 dark:bg-slate-900/70 border border-slate-200/70 dark:border-slate-800/70 rounded-2xl shadow-sm">
                    <div class="w-10 h-10 flex-shrink-0 rounded-xl bg-emerald-500/10 border border-emerald-500/15 flex items-center justify-center text-emerald-600 dark:text-emerald-400 text-base">
                        <i class="fa-solid fa-truck-fast"></i>
                    </div>
                    <div>
                        <span class="block text-[10px] font-black text-slate-900 dark:text-slate-100 uppercase tracking-wider">Express Logistics</span>
                        <span class="block text-[9px] text-slate-500 dark:text-slate-400 leading-relaxed">Insured courier. Pan-India dispatch ready.</span>
                    </div>
                </div>
                <div class="flex items-center gap-3 p-4 bg-white/90 dark:bg-slate-900/70 border border-slate-200/70 dark:border-slate-800/70 rounded-2xl shadow-sm">
                    <div class="w-10 h-10 flex-shrink-0 rounded-xl bg-amber-500/10 border border-amber-500/15 flex items-center justify-center text-amber-600 dark:text-amber-400 text-base">
                        <i class="fa-solid fa-rotate-right"></i>
                    </div>
                    <div>
                        <span class="block text-[10px] font-black text-slate-900 dark:text-slate-100 uppercase tracking-wider">1-Year Warranty</span>
                        <span class="block text-[9px] text-slate-500 dark:text-slate-400 leading-relaxed">Corporate warranty backed by import receipt.</span>
                    </div>
                </div>
                <div class="flex items-center gap-3 p-4 bg-white/90 dark:bg-slate-900/70 border border-slate-200/70 dark:border-slate-800/70 rounded-2xl shadow-sm">
                    <div class="w-10 h-10 flex-shrink-0 rounded-xl bg-purple-500/10 border border-purple-500/15 flex items-center justify-center text-purple-600 dark:text-purple-400 text-base">
                        <i class="fa-solid fa-file-invoice-dollar"></i>
                    </div>
                    <div>
                        <span class="block text-[10px] font-black text-slate-900 dark:text-slate-100 uppercase tracking-wider">GST Invoice</span>
                        <span class="block text-[9px] text-slate-500 dark:text-slate-400 leading-relaxed">Full CGST/SGST/IGST credit-eligible bills.</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- ============================================================
             RIGHT COLUMN — Product Details, Price, Form (7 cols on lg)
             ============================================================ -->
        <div class="flex flex-col gap-6 lg:col-span-7 xl:col-span-7 min-w-0">

            <!-- Brand / Subcategory / Rating Row -->
            <div class="flex flex-wrap items-center gap-2.5">
                @if ($product->brand)
                    <span class="px-3 py-1 rounded-lg bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 border border-indigo-500/20 text-[10px] font-black uppercase tracking-widest max-w-full">
                        {{ $product->brand->brand_name }}
                    </span>
                @endif
                @if ($product->subcategory)
                    <span class="px-3 py-1 rounded-lg bg-slate-100/80 dark:bg-slate-900/80 text-slate-600 dark:text-slate-400 border border-slate-200 dark:border-slate-800 text-[10px] font-black uppercase tracking-widest max-w-full">
                        {{ $product->subcategory->sub_cat }}
                    </span>
                @endif
                @if ($product->category)
                    <span class="px-3 py-1 rounded-lg bg-slate-100/80 dark:bg-slate-900/70 text-slate-500 dark:text-slate-400 border border-slate-200 dark:border-slate-800 text-[10px] font-bold uppercase tracking-widest max-w-full">
                        {{ $product->category->cat_name }}
                    </span>
                @endif

                <div class="flex items-center gap-0.5 text-amber-500 text-xs ml-0 sm:ml-auto">
                    <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i>
                    <span class="text-slate-500 dark:text-slate-400 font-bold ml-2 text-[10px] uppercase tracking-wide">5.0 Verified</span>
                </div>
            </div>

            <!-- Product Name -->
            <div>
                <h1 class="text-2xl sm:text-3xl xl:text-4xl font-black text-slate-900 dark:text-white tracking-tight uppercase leading-tight mb-2">
                    {{ $productName }}
                </h1>
                @if ($productDescription)
                    <p class="text-sm text-slate-600 dark:text-slate-400 leading-relaxed">
                        {{ Str::limit($productDescription, 200) }}
                    </p>
                @endif
            </div>

            <!-- Technical Specifications Grid -->
            <div class="bg-white/92 dark:bg-slate-900/82 border border-slate-200/70 dark:border-slate-800/70 rounded-2xl overflow-hidden shadow-sm shadow-slate-900/5">
                <div class="px-5 py-3.5 border-b border-slate-200/70 dark:border-slate-800/70">
                    <h3 class="text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest">Technical Specifications</h3>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 divide-y sm:divide-y-0 sm:divide-x divide-slate-200 dark:divide-slate-800">
                    <div class="p-4 min-w-0">
                        <span class="block text-[8px] text-slate-500 dark:text-slate-500 uppercase tracking-widest font-black mb-1">Unit Type</span>
                        <span class="block font-black text-slate-900 dark:text-slate-100 text-sm uppercase break-words">
                            {{ $product->unit == 2 ? 'BOX' : ($product->unit == 3 ? 'PKT' : 'PCS') }}
                        </span>
                    </div>
                    <div class="p-4 min-w-0">
                        <span class="block text-[8px] text-slate-500 dark:text-slate-500 uppercase tracking-widest font-black mb-1">HSN / SAC Code</span>
                        <span class="block font-black text-slate-900 dark:text-slate-100 text-sm font-mono break-all">{{ $product->hsnsac ?: '84713010' }}</span>
                    </div>
                    <div class="p-4 min-w-0">
                        <span class="block text-[8px] text-slate-500 dark:text-slate-500 uppercase tracking-widest font-black mb-1">Tax Bracket</span>
                        <span class="block font-black text-indigo-400 text-sm font-mono break-words">{{ intval($product->gst) }}% GST</span>
                    </div>
                    <div class="p-4 min-w-0">
                        <span class="block text-[8px] text-slate-500 dark:text-slate-500 uppercase tracking-widest font-black mb-1">Stock Available</span>
                        <span class="block font-black text-sm {{ $product->tqty > 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400' }} break-words">
                            {{ $product->tqty > 0 ? $product->tqty . ' Units' : 'Sold Out' }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Price Breakdown Panel -->
            <div class="rounded-3xl border border-slate-200/70 dark:border-slate-800 bg-white/90 dark:bg-slate-900/80 p-6 shadow-sm shadow-slate-900/5">
                <span class="block text-[10px] font-black uppercase tracking-[0.35em] text-slate-500 dark:text-slate-400 mb-3">{{ $product->display_price_label }}</span>
                <div class="text-4xl sm:text-5xl font-black text-blue-600 dark:text-indigo-400 leading-none">
                    Rs. {{ \App\Helpers\NumberHelper::indianFormat($sellingRate) }}
                </div>
                @if ($product->mrp > $sellingRate)
                    <div class="mt-2 text-sm text-slate-500 dark:text-slate-400 line-through">
                        MRP Rs. {{ \App\Helpers\NumberHelper::indianFormat($product->mrp) }}
                    </div>
                    <div class="mt-3 inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20 text-[10px] font-black uppercase tracking-[0.25em]">
                        <i class="fa-solid fa-tag text-[9px]"></i>
                        Save {{ $discountPercent }}%
                    </div>
                    <div class="mt-2 text-[10px] text-slate-500 dark:text-slate-400">
                        You save Rs. {{ \App\Helpers\NumberHelper::indianFormat($discountAmount, 0) }}
                    </div>
                @endif

                <div class="mt-5 pt-5 border-t border-slate-200/70 dark:border-slate-800 space-y-1.5 text-sm text-slate-600 dark:text-slate-400">
                    <div>GST rate: <span class="font-black text-slate-900 dark:text-white">{{ $gstRate }}%</span></div>
                    <div>{{ strtoupper(request()->has('state') && request('state') == 'TAMIL NADU' ? 'CGST / SGST' : 'IGST') }} applied at checkout.</div>
                </div>
            </div>

            <!-- Full Product Description -->
            @if ($productDescription)
                <div class="bg-white/92 dark:bg-slate-900/82 border border-slate-200/70 dark:border-slate-800/70 rounded-2xl overflow-hidden shadow-sm shadow-slate-900/5">
                    <div class="px-5 py-3.5 border-b border-slate-200/70 dark:border-slate-800/70">
                        <h3 class="text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest">Product Overview &amp; Specifications</h3>
                    </div>
                    <div class="p-5">
                        <p class="text-sm text-slate-600 dark:text-slate-400 leading-relaxed whitespace-pre-line">{{ $productDescription }}</p>
                    </div>
                </div>
            @endif

            <!-- Add to Cart Form -->
            <div class="pt-2">
                @if ($product->tqty > 0)
                    <form action="{{ route('storefront.cart.add', $product->id) }}" method="POST" class="space-y-4">
                        @csrf
                        <!-- Quantity Selector + Cart Button -->
                        <div class="flex flex-col sm:flex-row items-stretch gap-3">
                            <!-- Qty Stepper -->
                            <div class="flex items-center bg-white dark:bg-slate-950/70 border border-slate-200/70 dark:border-slate-800 rounded-xl px-2 h-14 gap-1 shadow-sm"
                                 x-data="{ qty: 1, maxStock: {{ $product->tqty }} }">
                                <button type="button"
                                        @click="if(qty > 1) qty--"
                                        class="w-10 h-10 flex items-center justify-center text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors cursor-pointer">
                                    <i class="fa-solid fa-minus text-xs"></i>
                                </button>
                                <input type="number"
                                       name="quantity"
                                       x-model="qty"
                                       readonly
                                       class="w-14 bg-transparent border-none text-center font-black text-slate-900 dark:text-white outline-none focus:ring-0 text-base font-mono">
                                <button type="button"
                                        @click="if(qty < maxStock) qty++"
                                        class="w-10 h-10 flex items-center justify-center text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors cursor-pointer">
                                    <i class="fa-solid fa-plus text-xs"></i>
                                </button>
                            </div>

                            <!-- Add to Cart Button -->
                            <button type="submit"
                                    class="flex-1 h-14 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-500 hover:to-purple-500 text-white font-black uppercase tracking-widest text-xs rounded-xl shadow-xl shadow-indigo-600/15 active:scale-[0.98] transition-all flex items-center justify-center gap-2.5 cursor-pointer">
                                <i class="fa-solid fa-cart-plus text-base"></i>
                                <span>Add to Cart</span>
                            </button>
                        </div>

                        <!-- Stock Info -->
                        <p class="text-[9px] text-slate-500 dark:text-slate-400 font-bold uppercase tracking-widest text-center">
                            <i class="fa-solid fa-circle-info text-slate-500 dark:text-slate-600 mr-1"></i>
                            {{ $product->tqty }} unit{{ $product->tqty > 1 ? 's' : '' }} available in warehouse stock
                        </p>
                    </form>
                @else
                    <div class="p-5 bg-rose-500/8 border border-rose-500/20 rounded-2xl flex items-center justify-center gap-3 text-rose-600 dark:text-rose-400">
                        <i class="fa-solid fa-triangle-exclamation text-lg"></i>
                        <div>
                            <span class="block text-xs font-black uppercase tracking-wider">Currently Out of Stock</span>
                            <span class="block text-[10px] text-rose-500/70 dark:text-rose-400/70 mt-0.5">Contact us for pre-order or restocking inquiries.</span>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Contact / Remittance Info Block -->
            <div class="bg-white/92 dark:bg-slate-900/82 border border-slate-200/70 dark:border-slate-800/70 rounded-2xl p-5 shadow-sm shadow-slate-900/5">
                <h4 class="text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-4">Direct Purchase &amp; Remittance</h4>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs text-slate-600 dark:text-slate-400">
                    <div class="flex items-start gap-3">
                        <i class="fa-solid fa-phone text-blue-600 dark:text-indigo-400 mt-0.5 text-sm"></i>
                        <div>
                            <span class="block text-[9px] font-black text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-0.5">Call Us</span>
                            <span>+91 99442 28686</span>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <i class="fa-solid fa-envelope text-blue-600 dark:text-indigo-400 mt-0.5 text-sm"></i>
                        <div>
                            <span class="block text-[9px] font-black text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-0.5">Email</span>
                            <span>support@mtlmart.com</span>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <i class="fa-solid fa-building-columns text-blue-600 dark:text-indigo-400 mt-0.5 text-sm"></i>
                        <div>
                            <span class="block text-[9px] font-black text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-0.5">Bank Transfer</span>
                            <span class="font-mono text-[10px]">IOB: 2869020000000349</span>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <i class="fa-solid fa-location-dot text-blue-600 dark:text-indigo-400 mt-0.5 text-sm"></i>
                        <div>
                            <span class="block text-[9px] font-black text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-0.5">Corporate Address</span>
                            <span>SUS Building, Marthandam, TN 629154</span>
                        </div>
                    </div>
                </div>
            </div>

        </div><!-- end details section -->
        </div><!-- end responsive grid -->
    </div><!-- end hero shell -->

    <!-- ============================================================
         RELATED PRODUCTS SECTION
         ============================================================ -->
    @if (count($relatedProducts) > 0)
        <div class="border-t border-slate-200/70 dark:border-slate-800/70 pt-12 mb-4">
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h2 class="text-lg font-black tracking-tight text-slate-900 dark:text-white uppercase">Related Hardware</h2>
                    <p class="text-[10px] text-slate-500 dark:text-slate-400 font-bold uppercase tracking-widest mt-1">More from the same category</p>
                </div>
                @if ($product->category)
                    <a href="{{ route('storefront.category', $product->category->cat_name) }}"
                       class="hidden sm:flex items-center gap-2 px-4 py-2 border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:border-slate-300 dark:hover:border-slate-700 rounded-xl text-xs font-black uppercase tracking-wider transition-all cursor-pointer">
                        View All <i class="fa-solid fa-arrow-right text-[10px]"></i>
                    </a>
                @endif
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach ($relatedProducts as $rel)
                    <x-product-card :product="$rel" />
                @endforeach
            </div>
        </div>
    @endif
@endsection
