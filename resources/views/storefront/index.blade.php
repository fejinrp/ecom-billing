@extends('layouts.storefront')

@section('container_class', 'w-full')

@section('content')
    <!-- ── Snapdeal Circular Category Row ── -->
    <div id="sf-category-scroll-container" class="mb-4 bg-white dark:bg-slate-950 dark:border-slate-850 rounded-2xl p-4 overflow-x-auto custom-scrollbar">
        <div class="flex items-center gap-8 min-w-max justify-start md:justify-center">
            @if (isset($categories) && count($categories) > 0)
                @foreach ($categories as $index => $cat)
                    @php
                        $icons = ['fa-server', 'fa-laptop-code', 'fa-microchip', 'fa-memory', 'fa-hard-drive', 'fa-keyboard', 'fa-headphones', 'fa-print', 'fa-network-wired'];
                        $icon = $icons[$index % count($icons)];
                        $isActive = Route::current()->parameter('name') === $cat->cat_name;
                    @endphp
                    <a href="{{ route('storefront.category', $cat->cat_name) }}" class="flex flex-col items-center group transition-all duration-300">
                        <div class="w-14 h-14 rounded-full flex items-center justify-center border transition-all duration-350 {{ $isActive ? 'text-white shadow-md' : 'bg-slate-50 dark:bg-slate-900 border-slate-200 dark:border-slate-800 text-slate-700 dark:text-slate-350' }}"
                             style="{{ $isActive ? 'background-color: #0059e3; border-color: #0059e3;' : '' }}">
                            <i class="fa-solid {{ $icon }} text-lg {{ $isActive ? 'text-white' : 'text-[#0059e3]' }}"
                               style="{{ !$isActive ? 'color: #0059e3;' : '' }}"></i>
                        </div>
                        <span class="text-[11px] font-bold uppercase tracking-wider mt-2 {{ $isActive ? '' : 'text-slate-650 dark:text-slate-400 group-hover:text-[#0059e3]' }}"
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

            function startScrolling() {
                if (scrollInterval) return;
                scrollInterval = setInterval(() => {
                    if (isHovered) return;
                    
                    // Check if container overflows
                    if (container.scrollWidth <= container.clientWidth) {
                        return; // Do not scroll if there is no overflow
                    }

                    container.scrollLeft += scrollSpeed * direction;

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
            container.addEventListener('mouseleave', () => { isHovered = false; });
            
            // Touch listeners for mobile
            container.addEventListener('touchstart', () => { isHovered = true; });
            container.addEventListener('touchend', () => { isHovered = false; });
        });
    </script>

    <style>
        .snapdeal-slider {
            height: 280px;
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
                {
                    img: 'https://images.unsplash.com/photo-1531297484001-80022131f5a1?auto=format&fit=crop&w=1600&q=80',
                    title: 'Next-Gen Compute Solutions',
                    desc: 'Procure imported server arrays and high-frequency components.'
                },
                {
                    img: 'https://images.unsplash.com/photo-1542751371-adc38448a05e?auto=format&fit=crop&w=1600&q=80',
                    title: 'Extreme GPU Arrays & AI Compute',
                    desc: 'Maximize computational speed with Liquid-Cooled configurations.'
                },
                {
                    img: 'https://images.unsplash.com/photo-1618005182384-a83a8bd57fbe?auto=format&fit=crop&w=1600&q=80',
                    title: 'Enterprise Workstations',
                    desc: 'AMD Threadripper and Intel Xeon customized platforms.'
                }
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
                    <div class="absolute inset-0 bg-gradient-to-r from-black/80 via-black/40 to-transparent flex items-center p-8 md:p-16">
                        <div class="max-w-lg space-y-4 text-white text-left">
                            <span class="inline-block px-2.5 py-0.5 rounded bg-[#0059e3] text-white text-[9px] font-black uppercase tracking-widest">
                                Exclusive Launch
                            </span>
                            <h2 class="text-2xl md:text-4xl font-black uppercase tracking-tight leading-tight" x-text="slide.title"></h2>
                            <p class="text-xs md:text-sm text-slate-300 font-medium leading-relaxed" x-text="slide.desc"></p>
                            <a href="#catalog" class="inline-flex items-center gap-2 px-5 py-2.5 bg-[#0059e3] hover:bg-[#0040a6] text-white text-xs font-black uppercase tracking-wider rounded-lg transition-all shadow-md">
                                <span>Shop Now</span>
                                <i class="fa-solid fa-chevron-right text-[10px]"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </template>
        </div>

        <!-- Slide Navigation Dots -->
        <div class="absolute bottom-4 left-1/2 -translate-x-1/2 flex items-center gap-2 z-20">
            <template x-for="(slide, index) in slides" :key="index">
                <button @click="activeSlide = index"
                        :class="activeSlide === index ? 'w-6 bg-[#0059e3]' : 'w-2 bg-white/50'"
                        class="h-2 rounded-full transition-all duration-300 cursor-pointer"></button>
            </template>
        </div>

        <!-- Slider Arrows -->
        <button @click="prev()" class="absolute left-4 top-1/2 -translate-y-1/2 w-8 h-8 rounded-full bg-white/30 dark:bg-black/30 backdrop-blur-sm hover:bg-[#0059e3] hover:text-white transition-all text-white flex items-center justify-center z-20 cursor-pointer">
            <i class="fa-solid fa-chevron-left text-xs"></i>
        </button>
        <button @click="next()" class="absolute right-4 top-1/2 -translate-y-1/2 w-8 h-8 rounded-full bg-white/30 dark:bg-black/30 backdrop-blur-sm hover:bg-[#0059e3] hover:text-white transition-all text-white flex items-center justify-center z-20 cursor-pointer">
            <i class="fa-solid fa-chevron-right text-xs"></i>
        </button>
    </div>

    <!-- ── Snapdeal Trust Bar (Redesigned from Black to Soft Gradient) ── -->
    <div class="p-4 justify-center rounded-2xl mb-8 flex flex-col md:flex-row items-center justify-around gap-4 shadow-sm border border-slate-100 dark:border-slate-800/80 bg-gradient-to-r from-blue-50/40 via-slate-50/20 to-indigo-50/40 dark:from-slate-900/60 dark:to-slate-950/60 animate-fade-in">
        <!-- Free Delivery Card -->
        <div class="flex flex-col items-center text-center bg-white dark:bg-slate-900/80 p-4 rounded-xl w-full max-w-sm shadow-sm transition-transform hover:scale-102 border border-slate-100/80 dark:border-slate-800/40">
            <div class="w-12 h-12 rounded-full flex items-center justify-center text-lg mb-2 shadow-inner bg-blue-50 dark:bg-[#0059e3]/10 text-[#0059e3]">
                <i class="fa-solid fa-truck-fast"></i>
            </div>
            <h4 class="text-xs font-black uppercase tracking-wider text-slate-800 dark:text-slate-200">FREE Delivery</h4>
            <p class="text-[10px] text-slate-500 dark:text-slate-400 mt-1">On all hardware imports across India</p>
        </div>

        <!-- Returns Card -->
        <div class="flex flex-col items-center text-center bg-white dark:bg-slate-900/80 p-4 rounded-xl w-full max-w-sm shadow-sm transition-transform hover:scale-102 border border-slate-100/80 dark:border-slate-800/40">
            <div class="w-12 h-12 rounded-full flex items-center justify-center text-lg mb-2 shadow-inner bg-blue-50 dark:bg-[#0059e3]/10 text-[#0059e3]">
                <i class="fa-solid fa-rotate-left"></i>
            </div>
            <h4 class="text-xs font-black uppercase tracking-wider text-slate-800 dark:text-slate-200">7 Days Easy Returns</h4>
            <p class="text-[10px] text-slate-500 dark:text-slate-400 mt-1">Hassle-free return policy parameters</p>
        </div>

        <!-- Quality Card -->
        <div class="flex flex-col items-center text-center bg-white dark:bg-slate-900/80 p-4 rounded-xl w-full max-w-sm shadow-sm transition-transform hover:scale-102 border border-slate-100/80 dark:border-slate-800/40">
            <div class="w-12 h-12 rounded-full flex items-center justify-center text-lg mb-2 shadow-inner bg-blue-50 dark:bg-[#0059e3]/10 text-[#0059e3]">
                <i class="fa-solid fa-ribbon"></i>
            </div>
            <h4 class="text-xs font-black uppercase tracking-wider text-slate-800 dark:text-slate-200">Great Quality</h4>
            <p class="text-[10px] text-slate-500 dark:text-slate-400 mt-1">At unmatched enterprise pricing</p>
        </div>
    </div>

    <!-- ── Deal Of The Day Section ── -->
    <div class="mb-12">
        <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-800 pb-4 mb-6">
            <div>
                <h2 class="text-xl md:text-2xl font-black tracking-tight uppercase text-slate-900 dark:text-white">Deal Of The Day</h2>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 uppercase font-bold tracking-wider">Top discounts and flash bargains today</p>
            </div>
        </div>
        
        <div id="sf-deals-scroll-container" class="overflow-x-auto custom-scrollbar pb-3">
            <div class="flex items-center gap-4 min-w-max">
                @if (isset($products) && count($products) > 0)
                    @foreach ($products->take(6) as $index => $prod)
                        @php
                            $deals = ['UNDER Rs. 299', 'MIN. 40% OFF', 'UNDER Rs. 999', 'MIN. 50% OFF', 'UNDER Rs. 1,499', 'MIN. 30% OFF'];
                            $deal = $deals[$index % count($deals)];
                            $cats = ['Accessories', 'Compute', 'Storage', 'Memory', 'Cables', 'Peripherals'];
                            $catLabel = $cats[$index % count($cats)];
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

            function startScrolling() {
                if (scrollInterval) return;
                scrollInterval = setInterval(() => {
                    if (isHovered) return;
                    
                    // Check if container overflows
                    if (container.scrollWidth <= container.clientWidth) {
                        return; // Do not scroll if there is no overflow
                    }

                    container.scrollLeft += scrollSpeed * direction;

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
            container.addEventListener('mouseleave', () => { isHovered = false; });
            
            // Touch listeners for mobile
            container.addEventListener('touchstart', () => { isHovered = true; });
            container.addEventListener('touchend', () => { isHovered = false; });
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
    <div class="sf-home-banner mt-12 relative overflow-hidden rounded-[2rem] border p-8 md:p-12 shadow-2xl bg-white dark:bg-slate-900 border-slate-200/60 dark:border-slate-800">
        <div class="absolute -right-24 -top-24 w-80 h-80 bg-[#0059e3]/5 rounded-full blur-3xl pointer-events-none"></div>
        
        <div class="relative z-10 max-w-lg space-y-4">
            <span class="px-2.5 py-0.5 rounded bg-[#0059e3] text-white text-[9px] font-black tracking-widest uppercase">Limited Campaign</span>
            <h2 class="sf-home-banner-title text-2xl md:text-3xl font-black uppercase tracking-tight font-outfit">Premium Workstation Upgrade Kits</h2>
            <p class="sf-home-banner-copy text-xs leading-relaxed">
                Maximize hardware bandwidth metrics by upgrading with premium dual-channel server memory arrays, certified SSD units, and modular power matrices. Direct consultations available with our hardware advisors.
            </p>
            <div class="flex items-center gap-4 pt-2">
                <a href="tel:+919944228686" class="sf-home-banner-btn px-5 py-2.5 text-white text-xs font-black uppercase tracking-widest rounded-xl shadow-lg transition-all bg-[#0059e3] hover:bg-[#0040a6]">Consult Advisor</a>
                <span class="sf-home-banner-copy text-xs font-mono font-bold">+91 99442 28686</span>
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
