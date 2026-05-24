@props(['product'])

<div class="group flex flex-col bg-white/95 dark:bg-slate-900/90 border border-slate-200/80 dark:border-slate-800/80 hover:border-blue-500/30 dark:hover:border-indigo-500/30 rounded-3xl overflow-hidden transition-all duration-500 shadow-sm hover:shadow-2xl hover:shadow-blue-500/10 dark:hover:shadow-indigo-500/10 flex-1 relative sf-product-card">
    <!-- Subtle hover glow background effect (Dark mode only) -->
    <div class="absolute -inset-px bg-gradient-to-tr from-blue-500/5 dark:from-indigo-500/10 via-transparent to-indigo-500/5 dark:to-purple-500/10 rounded-2xl opacity-0 group-hover:opacity-100 transition-opacity duration-500 pointer-events-none"></div>

    <!-- Product Image Container -->
    <div class="relative aspect-[4/3] w-full bg-slate-50/70 dark:bg-slate-950/60 flex items-center justify-center overflow-hidden border-b border-slate-100/70 dark:border-slate-900/60 p-3.5 sf-img-area">
        @if ($product->pimagef)
            <img src="{{ $product->primary_image_url }}" 
                 alt="{{ html_entity_decode($product->productname, ENT_QUOTES, 'UTF-8') }}" 
                 onerror="this.onerror=null;this.src='data:image/svg+xml;utf8,<svg xmlns=&quot;http://www.w3.org/2000/svg&quot; width=&quot;800&quot; height=&quot;600&quot; viewBox=&quot;0 0 800 600&quot;><rect width=&quot;800&quot; height=&quot;600&quot; fill=&quot;%23f8faff&quot;/><rect x=&quot;270&quot; y=&quot;180&quot; width=&quot;260&quot; height=&quot;180&quot; rx=&quot;28&quot; fill=&quot;%23dbe4f0&quot;/><path d=&quot;M400 230l-75 55h150z&quot; fill=&quot;%2394a3b8&quot;/><circle cx=&quot;400&quot; cy=&quot;340&quot; r=&quot;26&quot; fill=&quot;%2394a3b8&quot;/></svg>'"
                 class="max-h-[90%] max-w-[90%] object-contain group-hover:scale-105 transition-transform duration-500"
                 loading="lazy">
        @else
            <!-- Premium Placeholder SVG -->
            <div class="flex flex-col items-center gap-2.5 text-slate-400 dark:text-slate-700 group-hover:text-slate-500 dark:group-hover:text-slate-600 transition-colors">
                <div class="w-14 h-14 rounded-2xl bg-white dark:bg-slate-900/50 flex items-center justify-center border border-slate-200/80 dark:border-slate-800/80 shadow-sm">
                    <i class="fa-solid fa-cubes text-3xl"></i>
                </div>
                <span class="text-[9px] uppercase tracking-widest font-black text-slate-500">Image Pending</span>
            </div>
        @endif

        <!-- Stock Badge Overlay (Top Left) -->
        <div class="absolute top-3.5 left-3.5 z-10">
            @if ($product->tqty > 0)
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-black bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/15 backdrop-blur-md shadow-sm">
                    <span class="w-1.5 h-1.5 bg-emerald-500 dark:bg-emerald-400 rounded-full animate-pulse"></span> IN STOCK ({{ $product->tqty }})
                </span>
            @else
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-black bg-rose-500/10 text-rose-600 dark:text-rose-400 border border-rose-500/15 backdrop-blur-md">
                    <span class="w-1.5 h-1.5 bg-rose-500 dark:bg-rose-400 rounded-full"></span> OUT OF STOCK
                </span>
            @endif
        </div>

        <!-- Brand/Subcategory Badge (Bottom Right) -->
        @if ($product->subcategory)
            <div class="absolute bottom-3 right-3 z-10">
                <span class="px-2.5 py-0.5 rounded bg-white/90 dark:bg-slate-950/80 text-slate-600 dark:text-slate-400 text-[9px] font-black uppercase tracking-widest border border-slate-200/70 dark:border-slate-800/60 backdrop-blur-sm">
                    {{ $product->subcategory->sub_cat }}
                </span>
            </div>
        @endif
    </div>

    <!-- Product Card Details -->
    <div class="flex-1 p-5 flex flex-col justify-between relative z-10">
        <div class="flex-1 mb-5 min-w-0">
            <!-- Ratings and Brand row -->
            <div class="flex items-center justify-between gap-2 mb-2.5 min-w-0">
                <div class="flex items-center gap-0.5 text-amber-500 text-[10px] shrink-0">
                    <i class="fa-solid fa-star"></i>
                    <i class="fa-solid fa-star"></i>
                    <i class="fa-solid fa-star"></i>
                    <i class="fa-solid fa-star"></i>
                    <i class="fa-solid fa-star"></i>
                    <span class="text-slate-400 dark:text-slate-500 font-bold ml-1 text-[9px]">5.0</span>
                </div>
                @if ($product->brand)
                    <span class="text-[9px] font-black text-blue-600 dark:text-indigo-400 uppercase tracking-widest bg-blue-500/5 dark:bg-indigo-500/5 border border-blue-500/10 dark:border-indigo-500/10 px-2 py-0.5 rounded-md">
                        {{ $product->brand->brand_name }}
                    </span>
                @endif
            </div>
            
            <a href="{{ route('storefront.product', $product->id) }}" class="block group/title w-full min-w-0">
                <h3 class="w-full text-sm font-extrabold text-slate-900 dark:text-slate-100 uppercase tracking-tight line-clamp-2 min-h-[2.5rem] group-hover/title:text-blue-600 dark:group-hover/title:text-indigo-400 transition-colors duration-300 sf-prod-name">
                    {{ html_entity_decode($product->productname, ENT_QUOTES, 'UTF-8') }}
                </h3>
            </a>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-2 line-clamp-2 leading-relaxed sf-prod-desc">
                {{ html_entity_decode($product->productdes ?: 'Premium computing hardware featuring extreme speeds and robust cooling metrics.', ENT_QUOTES, 'UTF-8') }}
            </p>
        </div>

        <!-- Price Matrix and Savings -->
        <div class="pt-4 border-t border-slate-100/70 dark:border-slate-900/60 flex flex-wrap items-center justify-between gap-x-4 gap-y-2 sf-price-area">
            <div>
                <span class="block text-[8px] text-slate-400 dark:text-slate-500 uppercase tracking-widest font-black">{{ $product->display_price_label }}</span>
                <div class="flex items-end gap-1.5 mt-0.5">
                    <span class="text-base font-black text-blue-600 dark:text-indigo-400 font-mono sf-price-main">
                        Rs. {{ \App\Helpers\NumberHelper::indianFormat($product->display_price) }}
                    </span>
                    <span class="text-[9px] text-slate-500 dark:text-slate-400 font-bold bg-slate-100 dark:bg-indigo-500/10 px-1.5 py-0.2 rounded border border-slate-200 dark:border-indigo-500/10">
                        +{{ intval($product->gst) }}% GST
                    </span>
                </div>
            </div>
            
            @if ($product->mrp > $product->display_price)
                <div class="text-right">
                    <span class="block text-[8px] text-slate-400 dark:text-slate-600 uppercase tracking-widest font-bold line-through">MRP: Rs. {{ \App\Helpers\NumberHelper::indianFormat($product->mrp) }}</span>
                    @php
                        $discount = $product->mrp - $product->display_price;
                        $discountPct = round(($discount / $product->mrp) * 100);
                    @endphp
                    <span class="text-[9px] font-black text-emerald-600 dark:text-emerald-400 bg-emerald-500/10 px-1.5 py-0.5 rounded border border-emerald-500/10 mt-0.5 inline-block">
                        SAVE {{ $discountPct }}%
                    </span>
                </div>
            @endif
        </div>

        <!-- Card Action Buttons -->
        <div class="mt-4 grid grid-cols-2 gap-2.5">
            <a href="{{ route('storefront.product', $product->id) }}" class="w-full text-center py-2.5 bg-slate-50 dark:bg-slate-950 hover:bg-slate-100 dark:hover:bg-slate-800 border border-slate-200 dark:border-slate-800 hover:border-slate-300 dark:hover:border-slate-700 text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white rounded-xl text-xs font-extrabold transition-all duration-300 flex items-center justify-center gap-1.5 cursor-pointer sf-btn-view">
                <span>View Specs</span>
                <i class="fa-solid fa-arrow-right text-[10px]"></i>
            </a>
            @if ($product->tqty > 0)
                <form action="{{ route('storefront.cart.add', $product->id) }}" method="POST" class="w-full">
                    @csrf
                    <input type="hidden" name="quantity" value="1">
                    <button type="submit" class="w-full py-2.5 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white rounded-xl text-xs font-black shadow-md shadow-blue-500/10 active:scale-[0.98] transition-all duration-300 flex items-center justify-center gap-1.5 cursor-pointer">
                        <i class="fa-solid fa-cart-plus"></i> Add to Cart
                    </button>
                </form>
            @else
                <button disabled class="w-full py-2.5 bg-slate-100 dark:bg-slate-900 text-slate-400 dark:text-slate-600 border border-slate-200 dark:border-slate-850 rounded-xl text-xs font-black cursor-not-allowed">
                    Sold Out
                </button>
            @endif
        </div>
    </div>
</div>
