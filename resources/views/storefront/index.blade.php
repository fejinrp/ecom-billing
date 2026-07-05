@extends('layouts.storefront')

@section('container_class', 'w-full')

@section('content')
    <style>
        .snapdeal-slider {
            height: 160px;
        }
        @media (min-width: 768px) {
            .snapdeal-slider {
                height: 390px;
            }
        }
    </style>

    <!-- ── Dynamic Slider Hero Banner (Single Image Snapdeal Style) ── -->
    <div class="relative overflow-hidden rounded-2xl border shadow-md bg-white dark:bg-slate-900 border-slate-200 dark:border-slate-850 mb-4 snapdeal-slider"
         x-data="{
            activeSlide: 0,
            slides: [
                @if(isset($banners) && count($banners) > 0)
                    @foreach($banners as $b)
                        {
                            img: '{{ asset($b->image_path) }}',
                            title: '{{ addslashes($b->title) }}',
                            desc: '{{ addslashes($b->subtitle) }}',
                            badge: '{{ addslashes($b->badge_text) }}',
                            link: '{{ $b->link_url ?: "#catalog" }}'
                        },
                    @endforeach
                @else
                    {
                        img: 'https://images.unsplash.com/photo-1531297484001-80022131f5a1?auto=format&fit=crop&w=1600&q=80',
                        title: 'Next-Gen Compute Solutions',
                        desc: 'Procure imported server arrays and high-frequency components.',
                        badge: 'Exclusive Launch',
                        link: '#catalog'
                    },
                    {
                        img: 'https://images.unsplash.com/photo-1542751371-adc38448a05e?auto=format&fit=crop&w=1600&q=80',
                        title: 'Extreme GPU Arrays & AI Compute',
                        desc: 'Maximize computational speed with Liquid-Cooled configurations.',
                        badge: 'Exclusive Launch',
                        link: '#catalog'
                    },
                    {
                        img: 'https://images.unsplash.com/photo-1618005182384-a83a8bd57fbe?auto=format&fit=crop&w=1600&q=80',
                        title: 'Enterprise Workstations',
                        desc: 'AMD Threadripper and Intel Xeon customized platforms.',
                        badge: 'Exclusive Launch',
                        link: '#catalog'
                    }
                @endif
            ],
            next() { this.activeSlide = (this.activeSlide + 1) % this.slides.length },
            prev() { this.activeSlide = (this.activeSlide - 1 + this.slides.length) % this.slides.length },
            init() { setInterval(() => this.next(), 6000) }
         }">
        
        <!-- Slides Container -->
        <div class="relative w-full h-full">
            <template x-for="(slide, index) in slides" :key="index">
                <div x-show="activeSlide === index"
                     x-transition:enter="transition ease-out duration-500"
                     x-transition:enter-start="opacity-0 scale-102"
                     x-transition:enter-end="opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-300 absolute inset-0"
                     x-transition:leave-end="opacity-0 scale-98"
                     class="w-full h-full relative">
                     <!-- Banner Background Image -->
                     <img :src="slide.img" class="w-full h-full object-cover" />
                     <!-- Translucent Gradient Overlay for readability -->
                     <div class="absolute inset-0 bg-gradient-to-r from-black/80 via-black/40 to-transparent flex items-center p-4 md:p-16">
                        <div class="max-w-lg space-y-1.5 md:space-y-4 text-white text-left font-outfit">
                            <span class="inline-block px-2 py-0.5 rounded bg-[#0059e3] text-white text-[8px] md:text-[9px] font-black uppercase tracking-widest"
                                  x-show="slide.badge" x-text="slide.badge">
                            </span>
                            <h2 class="text-xs sm:text-sm md:text-4xl font-black uppercase tracking-tight leading-tight" x-text="slide.title"></h2>
                            <p class="hidden sm:block text-xs md:text-sm text-slate-300 font-medium leading-relaxed" x-text="slide.desc"></p>
                            <a :href="slide.link" class="inline-flex items-center gap-1 px-3 py-1.5 md:px-5 md:py-2.5 bg-[#0059e3] hover:bg-[#0040a6] text-white text-[9px] md:text-xs font-black uppercase tracking-wider rounded-lg transition-all shadow-md">
                                <span>Shop Now</span>
                                <i class="fa-solid fa-chevron-right text-[8px] md:text-[10px]"></i>
                            </a>
                        </div>
                     </div>
                </div>
            </template>
        </div>

        <!-- Slide Navigation Dots -->
        <div class="absolute bottom-3 md:bottom-4 left-1/2 -translate-x-1/2 flex items-center gap-1.5 md:gap-2 z-20">
            <template x-for="(slide, index) in slides" :key="index">
                <button @click="activeSlide = index"
                        :class="activeSlide === index ? 'w-4 md:w-6 bg-[#0059e3]' : 'w-1.5 md:w-2 bg-white/50'"
                        class="h-1.5 md:h-2 rounded-full transition-all duration-300 cursor-pointer"></button>
            </template>
        </div>

        <!-- Slider Arrows -->
        <button @click="prev()" class="absolute left-2 md:left-4 top-1/2 -translate-y-1/2 w-6 h-6 md:w-8 md:h-8 rounded-full bg-white/30 dark:bg-black/30 backdrop-blur-sm hover:bg-[#0059e3] hover:text-white transition-all text-white flex items-center justify-center z-20 cursor-pointer">
            <i class="fa-solid fa-chevron-left text-[10px] md:text-xs"></i>
        </button>
        <button @click="next()" class="absolute right-2 md:right-4 top-1/2 -translate-y-1/2 w-6 h-6 md:w-8 md:h-8 rounded-full bg-white/30 dark:bg-black/30 backdrop-blur-sm hover:bg-[#0059e3] hover:text-white transition-all text-white flex items-center justify-center z-20 cursor-pointer">
            <i class="fa-solid fa-chevron-right text-[10px] md:text-xs"></i>
        </button>
    </div>

    <!-- ── Snapdeal Circular Category Row ── -->
    <div id="sf-category-scroll-container" class="mb-4 bg-white dark:bg-slate-950 dark:border-slate-850 rounded-2xl p-4 overflow-x-auto custom-scrollbar">
        <div class="grid grid-rows-2 grid-flow-col md:flex md:items-center md:gap-8 md:min-w-max md:justify-center gap-x-6 gap-y-4 justify-start">
            @if (isset($featuredCategories) && count($featuredCategories) > 0)
                @foreach ($featuredCategories as $index => $cat)
                    @php
                        $icon = $cat->homepage_icon ?? 'fa-server';
                        $isActive = Route::current()->parameter('name') === $cat->cat_name;
                    @endphp
                    <a href="{{ route('storefront.category', $cat->cat_name) }}" class="flex flex-col items-center group transition-all duration-300">
                        <div class="w-12 h-12 md:w-14 md:h-14 rounded-full flex items-center justify-center border transition-all duration-355 {{ $isActive ? 'text-white shadow-md' : 'bg-slate-50 dark:bg-slate-900 border-slate-200 dark:border-slate-880 text-slate-750 dark:text-slate-350' }}"
                             style="{{ $isActive ? 'background-color: #0059e3; border-color: #0059e3;' : '' }}">
                            <i class="fa-solid {{ $icon }} text-base md:text-lg {{ $isActive ? 'text-white' : 'text-[#0059e3]' }}"
                               style="{{ !$isActive ? 'color: #0059e3;' : '' }}"></i>
                        </div>
                        <span class="text-[10px] md:text-[11px] font-bold uppercase tracking-wider mt-1.5 md:mt-2 {{ $isActive ? '' : 'text-slate-650 dark:text-slate-400 group-hover:text-[#0059e3]' }}"
                               style="{{ $isActive ? 'color: #0059e3;' : '' }}">{{ $cat->cat_name }}</span>
                    </a>
                @endforeach
            @endif
        </div>
    </div>

    <!-- Category Auto-Scroll Controller -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const container = document.getElementById('sf-category-scroll-container');
            if (!container) return;

            let scrollSpeed = 0.4; // Pixels per step (slow motion)
            let intervalTime = 20; // Step interval in milliseconds (~50fps)
            let scrollInterval = null;
            let isHovered = false;
            let direction = 1; // 1 = right, -1 = left
            let scrollPos = container.scrollLeft;

            function startScrolling() {
                if (scrollInterval) return;
                // Only auto-scroll on desktop or when not in grid layout
                if (window.innerWidth < 768) return; 
                scrollPos = container.scrollLeft;
                scrollInterval = setInterval(() => {
                    if (isHovered) return;
                    
                    // Check if container overflows
                    if (container.scrollWidth <= container.clientWidth) {
                        return; // Do not scroll if there is no overflow
                    }

                    scrollPos += scrollSpeed * direction;
                    container.scrollLeft = Math.round(scrollPos);

                    // Bounce back and forth when reaching either end
                    if (container.scrollLeft + container.clientWidth >= container.scrollWidth - 1) {
                        direction = -1;
                    } else if (container.scrollLeft <= 0) {
                        direction = 1;
                    }
                }, intervalTime);
            }

            function stopScrolling() {
                if (scrollInterval) {
                    clearInterval(scrollInterval);
                    scrollInterval = null;
                }
            }

            // Start loop
            startScrolling();

            // Hover listeners
            container.addEventListener('mouseenter', () => { isHovered = true; });
            container.addEventListener('mouseleave', () => { isHovered = false; scrollPos = container.scrollLeft; });
            
            // Touch listeners for mobile
            container.addEventListener('touchstart', () => { isHovered = true; });
            container.addEventListener('touchend', () => { isHovered = false; scrollPos = container.scrollLeft; });

            // Disable autoscroll on resize to mobile
            window.addEventListener('resize', () => {
                if (window.innerWidth < 768) {
                    stopScrolling();
                } else {
                    startScrolling();
                }
            });
        });
    </script>

    <!-- ── Snapdeal Subcategory Row (Premium Card layout matched to categories container width) ── -->
    @if (isset($subcategories) && count($subcategories) > 0)
    <div class="mb-6 bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-850 rounded-2xl p-4 overflow-hidden">
        <div id="sf-subcategories-scroll-container" class="overflow-x-auto custom-scrollbar">
            <div class="flex items-center gap-4 min-w-max pb-1 md:justify-center md:min-w-0 md:w-full md:flex-wrap">
                @foreach ($subcategories as $index => $sub)
                    @php
                        $catNameLower = strtolower($sub->category ? $sub->category->cat_name : '');
                        $iconMap = [
                            'laptop' => 'fa-laptop',
                            'computer' => 'fa-desktop',
                            'processor' => 'fa-microchip',
                            'cpu' => 'fa-microchip',
                            'motherboard' => 'fa-server',
                            'board' => 'fa-server',
                            'memory' => 'fa-memory',
                            'ram' => 'fa-memory',
                            'storage' => 'fa-hard-drive',
                            'ssd' => 'fa-hard-drive',
                            'hdd' => 'fa-hard-drive',
                            'keyboard' => 'fa-keyboard',
                            'mouse' => 'fa-computer-mouse',
                            'headphone' => 'fa-headphones',
                            'audio' => 'fa-headphones',
                            'printer' => 'fa-print',
                            'network' => 'fa-network-wired',
                            'switch' => 'fa-network-wired',
                            'router' => 'fa-wifi',
                            'cable' => 'fa-ethernet',
                            'accessory' => 'fa-plug',
                            'power' => 'fa-plug-circle-bolt',
                        ];
                        $icon = 'fa-cubes'; // fallback icon
                        foreach ($iconMap as $key => $val) {
                            if (str_contains($catNameLower, $key)) {
                                $icon = $val;
                                break;
                            }
                        }
                    @endphp
                    <a href="{{ route('storefront.index', ['search' => $sub->subcategoryname]) }}" class="bg-gradient-to-br from-slate-50 to-slate-100/30 dark:from-slate-900 dark:to-slate-950/40 border border-slate-150 dark:border-slate-800/80 hover:border-[#0059e3]/35 dark:hover:border-[#3b82f6]/35 p-3 rounded-2xl w-44 h-16 flex items-center gap-3 transition-all duration-250 hover:shadow-md hover:scale-[1.02] shrink-0 group">
                        <!-- Centered Category Icon Badge -->
                        <div class="w-8 h-8 rounded-full bg-slate-100/80 dark:bg-slate-800/60 flex items-center justify-center text-slate-400 dark:text-slate-500 group-hover:bg-[#0059e3]/10 dark:group-hover:bg-[#3b82f6]/10 group-hover:text-[#0059e3] dark:group-hover:text-[#3b82f6] transition-all duration-250 shrink-0">
                            <i class="fa-solid {{ $icon }} text-xs"></i>
                        </div>

                        <!-- Text Details -->
                        <div class="flex flex-col min-w-0 justify-center">
                            <span class="text-[11px] font-extrabold text-slate-800 dark:text-slate-100 tracking-tight leading-tight line-clamp-1 capitalize">{{ strtolower($sub->subcategoryname) }}</span>
                            @if ($sub->category)
                                <span class="text-[8px] text-[#0059e3]/85 dark:text-blue-400/85 font-black uppercase tracking-wider mt-0.5 truncate">{{ $sub->category->cat_name }}</span>
                            @endif
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Subcategories Auto-Scroll Controller -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const container = document.getElementById('sf-subcategories-scroll-container');
            if (!container) return;

            let scrollSpeed = 0.35; // Slow motion scroll
            let intervalTime = 25;
            let scrollInterval = null;
            let isHovered = false;
            let direction = 1;
            let scrollPos = container.scrollLeft;

            function startScrolling() {
                if (scrollInterval) return;
                if (window.innerWidth >= 768) return;
                scrollPos = container.scrollLeft;
                scrollInterval = setInterval(() => {
                    if (isHovered) return;
                    if (container.scrollWidth <= container.clientWidth) return;

                    scrollPos += scrollSpeed * direction;
                    container.scrollLeft = Math.round(scrollPos);

                    if (container.scrollLeft + container.clientWidth >= container.scrollWidth - 1) {
                        direction = -1;
                    } else if (container.scrollLeft <= 0) {
                        direction = 1;
                    }
                }, intervalTime);
            }

            function stopScrolling() {
                if (scrollInterval) {
                    clearInterval(scrollInterval);
                    scrollInterval = null;
                }
            }

            startScrolling();

            container.addEventListener('mouseenter', () => { isHovered = true; });
            container.addEventListener('mouseleave', () => { isHovered = false; scrollPos = container.scrollLeft; });
            container.addEventListener('touchstart', () => { isHovered = true; });
            container.addEventListener('touchend', () => { isHovered = false; scrollPos = container.scrollLeft; });

            window.addEventListener('resize', () => {
                if (window.innerWidth >= 768) {
                    stopScrolling();
                } else {
                    startScrolling();
                }
            });
        });
    </script>
    @endif



    <!-- ── Deal Of The Day Section ── -->
    <div class="mb-12">
        <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-800 pb-4 mb-6">
            <div>
                <h2 class="text-xl md:text-2xl font-black tracking-tight uppercase text-slate-900 dark:text-white">{{ $dealConfig['title'] ?? 'Deal Of The Day' }}</h2>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 uppercase font-bold tracking-wider">{{ $dealConfig['subtitle'] ?? 'Top discounts and flash bargains today' }}</p>
            </div>
        </div>
        
        <div id="sf-deals-scroll-container" class="overflow-x-auto custom-scrollbar pb-3">
            <div class="flex items-center gap-4 min-w-max">
                @if (isset($dealProducts) && count($dealProducts) > 0)
                    @foreach ($dealProducts as $index => $prod)
                        @php
                            $deals = ['UNDER Rs. 299', 'MIN. 40% OFF', 'UNDER Rs. 999', 'MIN. 50% OFF', 'UNDER Rs. 1,499', 'MIN. 30% OFF'];
                            $deal = $deals[$index % count($deals)];
                            $catLabel = $prod->category ? $prod->category->cat_name : 'Hardware';
                        @endphp
                        <a href="{{ route('storefront.product', $prod->id) }}" class="group flex flex-col bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl overflow-hidden hover:border-[#0059e3]/40 transition-all duration-300 hover:shadow-xl relative w-44 md:w-52 h-64 md:h-72 shrink-0">
                            <!-- Image container -->
                            <div class="relative flex-1 bg-slate-50 dark:bg-slate-950 p-3 flex items-center justify-center overflow-hidden border-b border-slate-150 dark:border-slate-800">
                                @if ($prod->pimagef)
                                    <img src="{{ $prod->primary_image_url }}" alt="{{ $prod->productname }}" class="max-h-[85%] max-w-[85%] object-contain group-hover:scale-105 transition-transform duration-300">
                                @else
                                    <i class="fa-solid fa-cube text-slate-400 text-3xl"></i>
                                @endif
                                
                                <!-- Translucent overlay tag at the bottom of the image -->
                                <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-r from-[#0059e3] to-[#0040a6] text-white text-[10px] font-black text-center py-2 uppercase tracking-widest shadow-md">
                                    {{ $deal }}
                                </div>
                            </div>
                            
                            <!-- Caption wrapper below image -->
                            <div class="p-3 bg-white dark:bg-slate-900 text-center">
                                <span class="text-xs font-bold text-slate-700 dark:text-slate-350 uppercase tracking-wide group-hover:text-[#0059e3] transition-colors truncate block">{{ $catLabel }}</span>
                            </div>
                        </a>
                    @endforeach
                @endif
            </div>
        </div>
    </div>

    <!-- Deals Auto-Scroll Controller -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const container = document.getElementById('sf-deals-scroll-container');
            if (!container) return;

            let scrollSpeed = 0.4; // Pixels per step (slow motion)
            let intervalTime = 25; // Step interval in milliseconds
            let scrollInterval = null;
            let isHovered = false;
            let direction = 1; // 1 = right, -1 = left
            let scrollPos = container.scrollLeft;

            function startScrolling() {
                if (scrollInterval) return;
                scrollPos = container.scrollLeft;
                scrollInterval = setInterval(() => {
                    if (isHovered) return;
                    
                    // Check if container overflows
                    if (container.scrollWidth <= container.clientWidth) {
                        return; // Do not scroll if there is no overflow
                    }

                    scrollPos += scrollSpeed * direction;
                    container.scrollLeft = Math.round(scrollPos);

                    // Bounce back and forth when reaching either end
                    if (container.scrollLeft + container.clientWidth >= container.scrollWidth - 1) {
                        direction = -1;
                    } else if (container.scrollLeft <= 0) {
                        direction = 1;
                    }
                }, intervalTime);
            }

            // Start loop
            startScrolling();

            // Hover listeners
            container.addEventListener('mouseenter', () => { isHovered = true; });
            container.addEventListener('mouseleave', () => { isHovered = false; scrollPos = container.scrollLeft; });
            
            // Touch listeners for mobile
            container.addEventListener('touchstart', () => { isHovered = true; });
            container.addEventListener('touchend', () => { isHovered = false; scrollPos = container.scrollLeft; });
        });
    </script>

    <!-- ── Main Hardware Catalog Header ── -->
    <div id="catalog" class="scroll-mt-24 flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
        <div>
            @if(request('search'))
                <h2 class="sf-section-title text-xl md:text-2xl font-black flex items-center gap-3">
                    <span class="text-slate-400 text-xs font-bold uppercase tracking-wider">Search Match:</span>
                    <span class="text-[#0059e3] font-outfit uppercase" style="color: #0059e3;">"{{ request('search') }}"</span>
                </h2>
                <p class="sf-section-sub text-[10px] mt-1.5 uppercase font-bold tracking-wider text-slate-500 dark:text-slate-400">Discovered {{ $products->total() }} premium configurations</p>
            @else
                <h2 class="sf-section-title text-xl md:text-2xl font-black tracking-tight uppercase text-slate-900 dark:text-white">Premium Hardware Fleet</h2>
                <p class="sf-section-sub text-[10px] mt-1.5 uppercase font-bold tracking-wider text-slate-500 dark:text-slate-400">Directly imported components optimized for enterprise and workstation builds</p>
            @endif
        </div>

        <!-- Filter Chips -->
        <div class="flex items-center gap-2 overflow-x-auto custom-scrollbar pb-2 max-w-full">
            <a href="{{ route('storefront.index') }}" class="sf-chip {{ !Route::current()->parameter('name') ? 'sf-chip-active' : '' }} px-4 py-2 rounded-xl text-xs font-black transition-all whitespace-nowrap">
                ALL HARDWARE
            </a>
            @if (isset($categories) && count($categories) > 0)
                @foreach ($categories->take(8) as $cat)
                    <a href="{{ route('storefront.category', $cat->cat_name) }}" class="sf-chip px-4 py-2 rounded-xl text-xs font-black uppercase transition-all whitespace-nowrap {{ Route::current()->parameter('name') === $cat->cat_name ? 'sf-chip-active' : '' }}">
                        {{ $cat->cat_name }}
                    </a>
                @endforeach
            @endif
        </div>
    </div>

    <!-- ── Products Grid ── -->
    @if (count($products) > 0)
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            @foreach ($products as $prod)
                <x-product-card :product="$prod" />
            @endforeach
        </div>

        <!-- Paginate Links -->
        <div class="flex justify-center mt-8">
            <div class="sf-pagination px-5 py-3 rounded-2xl border border-slate-200/70 dark:border-slate-800/70 flex items-center gap-2 bg-white/92 dark:bg-slate-900/82 shadow-lg shadow-blue-500/5">
                {{ $products->links() }}
            </div>
        </div>
    @else
        <!-- Empty State -->
        <div class="sf-cat-card text-center py-16 border border-slate-200/70 dark:border-slate-850/80 bg-white/92 dark:bg-slate-900/92 rounded-[2rem] p-8 max-w-md mx-auto shadow-2xl shadow-blue-500/5 relative overflow-hidden">
            <div class="absolute -right-12 -top-12 w-32 h-32 bg-blue-500/5 rounded-full blur-2xl"></div>
            <div class="h-16 w-16 bg-blue-50 dark:bg-slate-950 rounded-2xl flex items-center justify-center text-[#0059e3] text-3xl mx-auto mb-5 border border-blue-100 dark:border-slate-800 shadow-inner" style="color: #0059e3;">
                <i class="fa-solid fa-cube animate-bounce"></i>
            </div>
            <h3 class="sf-section-title text-sm font-black uppercase tracking-widest text-slate-800 dark:text-slate-100">No hardware models registered</h3>
            <p class="sf-section-sub text-xs mt-2.5 leading-relaxed text-slate-500 dark:text-slate-400">
                There are currently no products corresponding to your search terms or active categories. Expand your filtering parameters to find custom configurations.
            </p>
            <a href="{{ route('storefront.index') }}" class="inline-flex mt-6 px-5 py-2.5 text-white text-xs font-black uppercase tracking-widest rounded-xl transition-all shadow-lg shadow-blue-600/15 cursor-pointer" style="background-color: #0059e3;">
                Reset Storefront Filters
            </a>
        </div>
    @endif

    <!-- ── Full-Width Premium Promo Banner ── -->
    <div class="sf-home-banner mt-12 relative overflow-hidden rounded-[2rem] border p-8 md:p-12 shadow-2xl bg-white dark:bg-slate-900 border-slate-200/60 dark:border-slate-880">
        <div class="absolute -right-24 -top-24 w-80 h-80 bg-[#0059e3]/5 rounded-full blur-3xl pointer-events-none"></div>
        
        <div class="relative z-10 max-w-lg space-y-4">
            @if(!empty($promoBanner['badge']))
            <span class="px-2.5 py-0.5 rounded bg-[#0059e3] text-white text-[9px] font-black tracking-widest uppercase">{{ $promoBanner['badge'] }}</span>
            @endif
            <h2 class="sf-home-banner-title text-2xl md:text-3xl font-black uppercase tracking-tight font-outfit">{{ $promoBanner['title'] ?? '' }}</h2>
            <p class="sf-home-banner-copy text-xs leading-relaxed">
                {{ $promoBanner['copy'] ?? '' }}
            </p>
            <div class="flex items-center gap-4 pt-2">
                @if(!empty($promoBanner['btn_text']))
                <a href="{{ $promoBanner['btn_url'] ?? '#' }}" class="sf-home-banner-btn px-5 py-2.5 text-white text-xs font-black uppercase tracking-widest rounded-xl shadow-lg transition-all bg-[#0059e3] hover:bg-[#0040a6]">{{ $promoBanner['btn_text'] }}</a>
                @endif
                @if(!empty($promoBanner['phone']))
                <span class="sf-home-banner-copy text-xs font-mono font-bold">{{ $promoBanner['phone'] }}</span>
                @endif
            </div>
        </div>
    </div>

    <!-- ── Trust / Feature Highlights Section (Styled to match the attached layout) ── -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-6 mt-12 mb-4">
        <!-- FREE Delivery -->
        <div class="flex flex-col items-center justify-between text-center p-6 rounded-[1.5rem] bg-[#e0f2fe] dark:bg-sky-950/20 border border-sky-100/50 dark:border-sky-900/30 transition-transform hover:scale-[1.03] duration-300">
            <div class="w-16 h-16 flex items-center justify-center mb-3">
                <svg class="w-14 h-14" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <rect x="6" y="24" width="34" height="22" rx="3" fill="#38bdf8"/>
                    <path d="M40 28H50L56 36V46H40V28Z" fill="#0284c7"/>
                    <circle cx="18" cy="48" r="6" fill="#475569"/>
                    <circle cx="18" cy="48" r="2.5" fill="#f8fafc"/>
                    <circle cx="48" cy="48" r="6" fill="#475569"/>
                    <circle cx="48" cy="48" r="2.5" fill="#f8fafc"/>
                    <rect x="44" y="31" width="7" height="5" rx="1" fill="#e2e8f0"/>
                    <path d="M12 30H24" stroke="#f8fafc" stroke-width="2" stroke-linecap="round"/>
                    <path d="M12 36H20" stroke="#f8fafc" stroke-width="2" stroke-linecap="round"/>
                </svg>
            </div>
            <div>
                <h4 class="text-sm md:text-base font-bold text-slate-800 dark:text-slate-100 tracking-tight leading-tight">{{ $trustBadges['delivery_title'] ?? 'FREE Delivery' }}</h4>
                <p class="text-[10px] md:text-xs text-slate-550 dark:text-slate-400 mt-1 font-medium">{{ $trustBadges['delivery_subtitle'] ?? 'On all hardware imports' }}</p>
            </div>
        </div>

        <!-- 7 Days Easy Returns -->
        <div class="flex flex-col items-center justify-between text-center p-6 rounded-[1.5rem] bg-[#dcfce7] dark:bg-emerald-950/20 border border-emerald-100/50 dark:border-emerald-900/30 transition-transform hover:scale-[1.03] duration-300">
            <div class="w-16 h-16 flex items-center justify-center mb-3">
                <svg class="w-14 h-14" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <rect x="12" y="18" width="40" height="30" rx="4" fill="#4ade80"/>
                    <path d="M22 18V12C22 10.8954 22.8954 10 24 10H40C41.1046 10 42 10.8954 42 12V18" stroke="#16a34a" stroke-width="3"/>
                    <path d="M32 26C27.5817 26 24 29.5817 24 34C24 38.4183 27.5817 42 32 42C35.5 42 38.5 39.5 39.5 36.5" stroke="#16a34a" stroke-width="3" stroke-linecap="round"/>
                    <path d="M36 33L40 37L44 33" stroke="#16a34a" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            <div>
                <h4 class="text-sm md:text-base font-bold text-slate-800 dark:text-slate-100 tracking-tight leading-tight">{{ $trustBadges['returns_title'] ?? '7 Days Returns' }}</h4>
                <p class="text-[10px] md:text-xs text-slate-550 dark:text-slate-400 mt-1 font-medium">{{ $trustBadges['returns_subtitle'] ?? 'Hassle-free return policy' }}</p>
            </div>
        </div>

        <!-- Great Quality -->
        <div class="flex flex-col items-center justify-between text-center p-6 rounded-[1.5rem] bg-[#fef9c3] dark:bg-amber-950/20 border border-amber-100/50 dark:border-amber-900/30 transition-transform hover:scale-[1.03] duration-300">
            <div class="w-16 h-16 flex items-center justify-center mb-3">
                <svg class="w-14 h-14" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M18 36L24 54L32 48L40 54L46 36" fill="#f59e0b"/>
                    <circle cx="32" cy="26" r="18" fill="#facc15"/>
                    <circle cx="32" cy="26" r="13" fill="#f59e0b"/>
                    <path d="M28 26L31 29L37 23" stroke="#f8fafc" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            <div>
                <h4 class="text-sm md:text-base font-bold text-slate-800 dark:text-slate-100 tracking-tight leading-tight">{{ $trustBadges['quality_title'] ?? 'Great Quality' }}</h4>
                <p class="text-[10px] md:text-xs text-slate-550 dark:text-slate-400 mt-1 font-medium">{{ $trustBadges['quality_subtitle'] ?? 'Direct enterprise sourcing' }}</p>
            </div>
        </div>

        <!-- GST Compliant -->
        <div class="flex flex-col items-center justify-between text-center p-6 rounded-[1.5rem] bg-[#fee2e2] dark:bg-rose-950/20 border border-rose-100/50 dark:border-rose-900/30 transition-transform hover:scale-[1.03] duration-300">
            <div class="w-16 h-16 flex items-center justify-center mb-3">
                <svg class="w-14 h-14" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <rect x="14" y="8" width="36" height="48" rx="4" fill="#fb7185"/>
                    <rect x="20" y="16" width="24" height="6" rx="1" fill="#f43f5e"/>
                    <line x1="20" y1="28" x2="44" y2="28" stroke="#f8fafc" stroke-width="3" stroke-linecap="round"/>
                    <line x1="20" y1="36" x2="44" y2="36" stroke="#f8fafc" stroke-width="3" stroke-linecap="round"/>
                    <line x1="20" y1="44" x2="34" y2="44" stroke="#f8fafc" stroke-width="3" stroke-linecap="round"/>
                    <circle cx="46" cy="46" r="8" fill="#10b981"/>
                    <path d="M43 46L45 48L49 44" stroke="#f8fafc" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            <div>
                <h4 class="text-sm md:text-base font-bold text-slate-800 dark:text-slate-100 tracking-tight leading-tight">{{ $trustBadges['gst_title'] ?? 'GST Compliant' }}</h4>
                <p class="text-[10px] md:text-xs text-slate-555 dark:text-slate-400 mt-1 font-medium">{{ $trustBadges['gst_subtitle'] ?? 'Input Credit & Tax Invoices' }}</p>
            </div>
        </div>
    </div>

    <!-- Countdown Timer Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            function getNextReset() {
                var now = new Date();
                var resultDate = new Date();
                resultDate.setDate(now.getDate() + (7 - now.getDay()) % 7);
                resultDate.setHours(23, 59, 59, 999);
                if (resultDate <= now) {
                    resultDate.setDate(resultDate.getDate() + 7);
                }
                return resultDate.getTime();
            }

            var countdownTarget = getNextReset();

            function updateTimer() {
                var now = new Date().getTime();
                var distance = countdownTarget - now;

                if (distance < 0) {
                    countdownTarget = getNextReset();
                    distance = countdownTarget - now;
                }

                var days = Math.floor(distance / (1000 * 60 * 60 * 24));
                var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                var seconds = Math.floor((distance % (1000 * 60)) / 1000);

                days = (days < 10) ? '0' + days : days;
                hours = (hours < 10) ? '0' + hours : hours;
                minutes = (minutes < 10) ? '0' + minutes : minutes;
                seconds = (seconds < 10) ? '0' + seconds : seconds;

                document.getElementById('sf-cd-days').textContent = days;
                document.getElementById('sf-cd-hours').textContent = hours;
                document.getElementById('sf-cd-mins').textContent = minutes;
                document.getElementById('sf-cd-secs').textContent = seconds;
            }

            updateTimer();
            setInterval(updateTimer, 1000);
        });
    </script>
@endsection
