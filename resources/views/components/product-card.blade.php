@props(['product'])

<div class="group flex flex-col bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 hover:border-[#0059e3]/30 dark:hover:border-[#0059e3]/30 rounded-2xl overflow-hidden transition-all duration-300 hover:shadow-xl flex-1 relative sf-product-card">
    <!-- Product Image Container -->
    <div class="relative aspect-[4/3] w-full bg-slate-50/50 dark:bg-slate-950/60 flex items-center justify-center overflow-hidden border-b border-slate-100 dark:border-slate-900 p-4 sf-img-area">
        @if ($product->pimagef)
            <img src="{{ $product->primary_image_url }}" 
                 alt="{{ html_entity_decode($product->productname, ENT_QUOTES, 'UTF-8') }}" 
                 onerror="this.onerror=null;this.src='data:image/svg+xml;utf8,<svg xmlns=&quot;http://www.w3.org/2000/svg&quot; width=&quot;800&quot; height=&quot;600&quot; viewBox=&quot;0 0 800 600&quot;><rect width=&quot;800&quot; height=&quot;600&quot; fill=&quot;%23f8faff&quot;/><rect x=&quot;270&quot; y=&quot;180&quot; width=&quot;260&quot; height=&quot;180&quot; rx=&quot;28&quot; fill=&quot;%23dbe4f0&quot;/><path d=&quot;M400 230l-75 55h150z&quot; fill=&quot;%2394a3b8&quot;/><circle cx=&quot;400&quot; cy=&quot;340&quot; r=&quot;26&quot; fill=&quot;%2394a3b8&quot;/></svg>'"
                 class="max-h-[95%] max-w-[95%] object-contain group-hover:scale-105 transition-transform duration-300"
                 loading="lazy">
        @else
            <!-- Premium Placeholder SVG -->
            <div class="flex flex-col items-center gap-2 text-slate-400 dark:text-slate-700 group-hover:text-slate-500 dark:group-hover:text-slate-600 transition-colors">
                <div class="w-12 h-12 rounded-xl bg-white dark:bg-slate-900 flex items-center justify-center border border-slate-200 dark:border-slate-800 shadow-sm">
                    <i class="fa-solid fa-cubes text-2xl"></i>
                </div>
                <span class="text-[8px] uppercase tracking-widest font-black text-slate-500">No Image</span>
            </div>
        @endif

        <!-- Stock Badge Overlay (Top Left) -->
        <div class="absolute top-3 left-3 z-10">
            @if ($product->tqty > 0)
                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[9px] font-bold bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/15 backdrop-blur-md shadow-sm">
                    <span class="w-1.5 h-1.5 bg-emerald-500 dark:bg-emerald-400 rounded-full animate-pulse"></span> IN STOCK ({{ $product->tqty }})
                </span>
            @else
                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[9px] font-bold bg-rose-500/10 text-rose-600 dark:text-rose-400 border border-rose-500/15 backdrop-blur-md">
                    <span class="w-1.5 h-1.5 bg-rose-500 dark:bg-rose-400 rounded-full"></span> OUT OF STOCK
                </span>
            @endif
        </div>

        <!-- Category/Subcategory Badge (Bottom Right) -->
        @if ($product->subcategory)
            <div class="absolute bottom-2.5 right-2.5 z-10">
                <span class="px-2 py-0.5 rounded bg-white/90 dark:bg-slate-950/80 text-slate-500 dark:text-slate-400 text-[8px] font-extrabold uppercase tracking-wider border border-slate-200/50 dark:border-slate-800/50 backdrop-blur-sm">
                    {{ $product->subcategory->sub_cat }}
                </span>
            </div>
        @endif
    </div>

    <!-- Product Card Details -->
    <div class="flex-1 p-4 flex flex-col justify-between relative z-10">
        <div class="flex-1 mb-4 min-w-0">
            <!-- Ratings and Brand row -->
            <div class="flex items-center justify-between gap-2 mb-2 min-w-0">
                <div class="flex items-center gap-0.5 text-amber-500 text-[10px] shrink-0">
                    <i class="fa-solid fa-star"></i>
                    <i class="fa-solid fa-star"></i>
                    <i class="fa-solid fa-star"></i>
                    <i class="fa-solid fa-star"></i>
                    <i class="fa-solid fa-star"></i>
                    <span class="text-slate-400 dark:text-slate-500 font-bold ml-1 text-[9px]">5.0</span>
                </div>
                @if ($product->brand)
                    <span class="text-[9px] font-bold text-[#0059e3] dark:text-[#0059e3] uppercase tracking-widest bg-blue-50 dark:bg-slate-900 border border-blue-100 dark:border-blue-900/50 px-2 py-0.5 rounded">
                        {{ $product->brand->brand_name }}
                    </span>
                @endif
            </div>
            
            <a href="{{ route('storefront.product', $product->id) }}" class="block group/title w-full min-w-0">
                <h3 class="w-full text-xs font-bold text-slate-850 dark:text-slate-105 uppercase tracking-tight line-clamp-2 min-h-[2.25rem] group-hover/title:text-[#0059e3] dark:group-hover/title:text-[#0059e3] transition-colors duration-200 sf-prod-name">
                    {{ html_entity_decode($product->productname, ENT_QUOTES, 'UTF-8') }}
                </h3>
            </a>
            <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1.5 line-clamp-2 leading-relaxed sf-prod-desc">
                {{ html_entity_decode($product->productdes ?: 'Premium computing hardware featuring extreme speeds and robust cooling metrics.', ENT_QUOTES, 'UTF-8') }}
            </p>
        </div>

        <!-- Price Matrix and Savings -->
        <div class="pt-3 border-t border-slate-100 dark:border-slate-800 flex flex-wrap items-center justify-between gap-x-2 gap-y-1.5 sf-price-area">
            <div>
                <span class="block text-[8px] text-slate-400 dark:text-slate-500 uppercase tracking-widest font-bold">{{ $product->display_price_label }}</span>
                <div class="flex items-end gap-1 mt-0.5">
                    <span class="text-sm font-extrabold text-[#0059e3] dark:text-blue-400 font-mono sf-price-main">
                        Rs. {{ \App\Helpers\NumberHelper::indianFormat($product->display_price) }}
                    </span>
                    <span class="text-[8px] text-slate-450 dark:text-slate-400 font-medium bg-slate-50 dark:bg-slate-900 px-1 py-0.2 rounded border border-slate-150 dark:border-slate-800">
                        +{{ intval($product->gst) }}% GST
                    </span>
                </div>
            </div>
            
            @if ($product->mrp > $product->display_price)
                <div class="text-right">
                    <span class="block text-[8px] text-slate-400 dark:text-slate-650 uppercase tracking-widest line-through">MRP: Rs. {{ \App\Helpers\NumberHelper::indianFormat($product->mrp) }}</span>
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
        <div class="mt-3.5 grid grid-cols-2 gap-2">
            <a href="{{ route('storefront.product', $product->id) }}" class="w-full text-center py-2 bg-slate-50 dark:bg-slate-950 hover:bg-slate-100 dark:hover:bg-slate-900 border border-slate-200 dark:border-slate-800 hover:border-slate-350 dark:hover:border-slate-700 text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white rounded-lg text-[11px] font-bold transition-all flex items-center justify-center gap-1 cursor-pointer sf-btn-view">
                <span>Specs</span>
                <i class="fa-solid fa-arrow-right text-[9px]"></i>
            </a>
            @if ($product->tqty > 0)
                <form action="{{ route('storefront.cart.add', $product->id) }}" method="POST" class="w-full">
                    @csrf
                    <input type="hidden" name="quantity" value="1">
                    <button type="submit" class="w-full py-2 bg-[#0059e3] hover:bg-[#0040a6] text-white rounded-lg text-[11px] font-extrabold shadow-sm active:scale-[0.98] transition-all flex items-center justify-center gap-1 cursor-pointer">
                        <i class="fa-solid fa-cart-plus text-[10px]"></i> Add
                    </button>
                </form>
            @else
                <button disabled class="w-full py-2 bg-slate-100 dark:bg-slate-900 text-slate-400 dark:text-slate-650 border border-slate-200 dark:border-slate-850 rounded-lg text-[11px] font-bold cursor-not-allowed">
                    Sold Out
                </button>
            @endif
        </div>
    </div>
</div>
