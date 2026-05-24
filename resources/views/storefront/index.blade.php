@extends('layouts.storefront')

@section('content')
    <!-- ── Premium Hero Countdown Banner ── -->
    <div class="sf-hero relative overflow-hidden rounded-[2rem] border mb-12 p-8 md:p-14 shadow-2xl relative">
        <!-- Ambient Radial Background Gradients -->
        <div class="absolute -right-24 -top-24 w-[500px] h-[500px] bg-blue-600/10 dark:bg-indigo-600/10 rounded-full blur-3xl animate-pulse pointer-events-none"></div>
        <div class="absolute -left-24 -bottom-24 w-[400px] h-[400px] bg-orange-600/5 dark:bg-purple-600/10 rounded-full blur-3xl animate-pulse pointer-events-none" style="animation-delay: 2s;"></div>

        <div class="relative z-10 grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
            <!-- Left Info Panel -->
            <div class="lg:col-span-7 space-y-6 lg:pr-2">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-black bg-blue-500/10 dark:bg-indigo-500/10 text-blue-600 dark:text-indigo-400 border border-blue-500/20 dark:border-indigo-500/20 uppercase tracking-widest">
                    <i class="fa-solid fa-bolt text-[9px] text-orange-500 dark:text-indigo-400 animate-bounce"></i> Limited Stock Flash Deals
                </span>
                
                <h1 class="sf-hero-title text-3xl md:text-5xl lg:text-6xl font-black leading-none tracking-tight text-slate-900 dark:text-white">
                    NEXT-GEN COMPUTE.<br>
                    <span class="bg-gradient-to-r from-blue-600 via-indigo-600 to-orange-500 dark:from-indigo-400 dark:via-indigo-500 dark:to-purple-500 bg-clip-text text-transparent uppercase">UNMATCHED INTEGRITY.</span>
                </h1>
                
                <p class="sf-hero-sub text-xs md:text-sm leading-relaxed text-slate-700 dark:text-slate-400 max-w-xl">
                    Procure imported server arrays, custom gaming builds, ultra-performance workstations, and high-frequency components. Fully GST compliant, backed by direct local brand warranty and fast courier dispatch options.
                </p>

                <!-- Countdown Timer Card (Gizmos Inspired) -->
                <div class="inline-flex flex-col sm:flex-row items-center gap-4 bg-white/76 dark:bg-slate-900/78 border border-slate-200/70 dark:border-slate-800/70 backdrop-blur-xl p-4 rounded-2xl shadow-lg shadow-blue-500/5">
                    <div class="flex items-center gap-2 flex-shrink-0">
                        <i class="fa-solid fa-clock text-orange-500 animate-pulse text-lg"></i>
                        <span class="text-[10px] font-black uppercase tracking-widest text-slate-700 dark:text-slate-300">Flash Sales End In</span>
                    </div>
                    
                    <div class="flex items-center gap-2.5">
                        <div class="sf-countdown-digit">
                            <span id="sf-cd-days">00</span>
                            <span>days</span>
                        </div>
                        <div class="text-slate-400 dark:text-slate-600 font-extrabold">:</div>
                        <div class="sf-countdown-digit">
                            <span id="sf-cd-hours">00</span>
                            <span>hours</span>
                        </div>
                        <div class="text-slate-400 dark:text-slate-600 font-extrabold">:</div>
                        <div class="sf-countdown-digit">
                            <span id="sf-cd-mins">00</span>
                            <span>mins</span>
                        </div>
                        <div class="text-slate-400 dark:text-slate-600 font-extrabold">:</div>
                        <div class="sf-countdown-digit" style="color: #f76b1c">
                            <span id="sf-cd-secs">00</span>
                            <span>secs</span>
                        </div>
                    </div>
                </div>

                <div class="flex flex-wrap gap-4 pt-2">
                    <a href="#catalog" class="px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-700 hover:from-blue-500 hover:to-indigo-600 text-white text-xs font-black uppercase tracking-widest rounded-xl shadow-xl shadow-blue-600/25 active:scale-[0.98] transition-all flex items-center gap-2 cursor-pointer">
                        <span>Explore Fleet</span>
                        <i class="fa-solid fa-arrow-down text-xs"></i>
                    </a>
                    <a href="{{ route('admin.login') }}" class="sf-hero-btn sf-nav-btn px-6 py-3 border border-slate-200/70 dark:border-slate-800/70 text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-900 text-xs font-black uppercase tracking-widest rounded-xl active:scale-[0.98] transition-all cursor-pointer">
                        Executive Portal
                    </a>
                </div>
            </div>

            <!-- Right Image Showcase (Aesthetic mockup container) -->
            <div class="lg:col-span-5 relative hidden lg:block">
                <div class="sf-home-visual relative w-full aspect-square rounded-[2rem] flex items-center justify-center p-8 border overflow-hidden shadow-inner">
                    <!-- Glass Orb -->
                    <div class="absolute w-56 h-56 rounded-full bg-gradient-to-r from-blue-600 to-indigo-700 blur-3xl opacity-20 dark:opacity-30 pointer-events-none"></div>
                    <div class="absolute -left-12 top-8 w-24 h-24 rounded-full bg-blue-500/20 blur-2xl pointer-events-none"></div>
                    <div class="absolute right-0 bottom-0 w-40 h-40 rounded-full bg-orange-500/10 blur-3xl pointer-events-none"></div>
                    
                    <div class="sf-home-visual-card relative z-10 w-full max-w-[310px] p-6 rounded-2xl border shadow-2xl backdrop-blur-md">
                        <div class="flex items-center justify-between mb-4 border-b border-white/15 dark:border-white/5 pb-2">
                            <span class="text-[9px] font-black text-blue-600 dark:text-indigo-400 uppercase tracking-widest">Aesthetic Workstations</span>
                            <span class="px-2 py-0.5 rounded bg-orange-500 text-white text-[8px] font-extrabold">HOT</span>
                        </div>
                        <div class="w-full aspect-[4/3] rounded-lg bg-slate-950/80 mb-4 flex items-center justify-center border border-slate-800/60 shadow-inner">
                            <i class="fa-solid fa-laptop-code text-slate-600 text-4xl group-hover:scale-105 transition-transform duration-500"></i>
                        </div>
                        <div class="text-xs font-extrabold text-slate-800 dark:text-slate-100 uppercase tracking-tight truncate mb-1">MTL Pro Server Rack v4</div>
                        <div class="text-[10px] text-slate-500 dark:text-slate-400">128-Core Compute, Liquid Cooled</div>
                        <div class="text-sm font-black text-blue-600 dark:text-indigo-400 mt-2 font-mono">Rs. 1,48,500</div>
                    </div>

                    <div class="absolute left-6 right-6 bottom-6 grid grid-cols-3 gap-3">
                        <div class="sf-home-visual-mini rounded-2xl border backdrop-blur-md p-3">
                            <i class="fa-solid fa-shield-halved text-amber-300 text-sm mb-2"></i>
                            <p class="text-[9px] font-black uppercase tracking-widest">Certified</p>
                        </div>
                        <div class="sf-home-visual-mini rounded-2xl border backdrop-blur-md p-3">
                            <i class="fa-solid fa-truck-fast text-amber-300 text-sm mb-2"></i>
                            <p class="text-[9px] font-black uppercase tracking-widest">Fast Dispatch</p>
                        </div>
                        <div class="sf-home-visual-mini rounded-2xl border backdrop-blur-md p-3">
                            <i class="fa-solid fa-file-invoice-dollar text-amber-300 text-sm mb-2"></i>
                            <p class="text-[9px] font-black uppercase tracking-widest">GST Ready</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ── Category Icon Row ── -->
    <div class="mb-16">
        <div class="flex items-center justify-between gap-4 mb-6">
            <h3 class="sf-section-sub text-[10px] font-black uppercase tracking-widest">Trending Category Catalogs</h3>
            <span class="w-full h-px bg-slate-200 dark:bg-slate-800 flex-1 ml-4"></span>
        </div>
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4">
            @if(isset($categories) && count($categories) > 0)
                @foreach ($categories->take(6) as $index => $cat)
                    @php
                        $icons = ['fa-server', 'fa-laptop-code', 'fa-microchip', 'fa-memory', 'fa-hard-drive', 'fa-keyboard'];
                        $icon = $icons[$index % count($icons)];
                    @endphp
                    <a href="{{ route('storefront.category', $cat->cat_name) }}" class="sf-cat-card group relative overflow-hidden border border-slate-200/80 dark:border-slate-800/80 rounded-2xl p-5 text-center transition-all duration-300 hover:-translate-y-1 hover:border-blue-500/20 dark:hover:border-indigo-500/25 bg-white/90 dark:bg-slate-900/60 shadow-sm">
                        <div class="sf-cat-icon w-14 h-14 rounded-2xl flex items-center justify-center text-blue-600 dark:text-indigo-400 bg-blue-50 dark:bg-slate-900 border border-slate-100/80 dark:border-slate-800 group-hover:bg-blue-600 dark:group-hover:bg-indigo-600 group-hover:text-white transition-all duration-300 mx-auto mb-3 shadow-sm">
                            <i class="fa-solid {{ $icon }} text-lg"></i>
                        </div>
                        <span class="sf-cat-name block text-xs font-extrabold uppercase tracking-wider text-slate-800 dark:text-slate-300 group-hover:text-blue-600 dark:group-hover:text-indigo-400 transition-colors duration-300 truncate">{{ $cat->cat_name }}</span>
                        <span class="block text-[8px] text-slate-400 dark:text-slate-500 font-bold uppercase mt-1 tracking-wide">Explore Inventory</span>
                    </a>
                @endforeach
            @endif
        </div>
    </div>

    <!-- ── Two-Column Promo Campaigns ── -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-16">
        <!-- Campaign Card 1 -->
        <div class="sf-promo-card sf-home-campaign-card group relative overflow-hidden rounded-3xl border p-8 flex flex-col justify-between aspect-[16/9] shadow-lg shadow-blue-500/5">
            <div class="absolute -right-12 -top-12 w-48 h-48 bg-blue-600/10 rounded-full blur-3xl pointer-events-none"></div>
            
            <div class="space-y-3 max-w-xs relative z-10">
                <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded bg-orange-600 text-white text-[8px] font-black tracking-widest uppercase">Server Arrays</span>
                <h3 class="sf-home-campaign-title text-xl md:text-2xl font-black uppercase tracking-tight font-outfit">Extreme AI & GPU Clusters</h3>
                <p class="sf-home-campaign-copy text-[10px] leading-relaxed font-medium">Configure deep-learning clusters and workstation layouts directly backed by corporate input GST tax credit claims.</p>
            </div>
            
            <div class="mt-4 flex items-center justify-between relative z-10">
                <div class="text-xs font-black text-orange-400 tracking-wider">Save up to 15%</div>
                <a href="#catalog" class="sf-home-campaign-btn px-4 py-2 border text-[10px] font-extrabold uppercase tracking-widest rounded-lg transition-all active:scale-[0.98] shadow-sm">Explore Rigs</a>
            </div>
        </div>

        <!-- Campaign Card 2 -->
        <div class="sf-promo-card sf-home-campaign-card group relative overflow-hidden rounded-3xl border p-8 flex flex-col justify-between aspect-[16/9] shadow-lg shadow-indigo-500/5">
            <div class="absolute -right-12 -top-12 w-48 h-48 bg-purple-600/10 rounded-full blur-3xl pointer-events-none"></div>
            
            <div class="space-y-3 max-w-xs relative z-10">
                <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded bg-blue-600 text-white text-[8px] font-black tracking-widest uppercase">Workstations</span>
                <h3 class="sf-home-campaign-title text-xl md:text-2xl font-black uppercase tracking-tight font-outfit">Enterprise Workstation Laptops</h3>
                <p class="sf-home-campaign-copy text-[10px] leading-relaxed font-medium">Procure premium computing setups, ultra-definition monitors, and high-frequency RAM with direct local warranties.</p>
            </div>
            
            <div class="mt-4 flex items-center justify-between relative z-10">
                <div class="text-xs font-black text-blue-400 tracking-wider">Save up to Rs. 10,000</div>
                <a href="#catalog" class="sf-home-campaign-btn px-4 py-2 border text-[10px] font-extrabold uppercase tracking-widest rounded-lg transition-all active:scale-[0.98] shadow-sm">View Models</a>
            </div>
        </div>
    </div>

    <!-- ── Active Search / Category Header ── -->
    <div id="catalog" class="scroll-mt-24 mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            @if(request('search'))
                <h2 class="sf-section-title text-xl md:text-2xl font-black flex items-center gap-3">
                    <span class="text-slate-400 text-xs font-bold uppercase tracking-wider">Search Match:</span>
                    <span class="text-blue-600 dark:text-indigo-400 font-outfit uppercase">"{{ request('search') }}"</span>
                </h2>
                <p class="sf-section-sub text-[10px] mt-1.5 uppercase font-bold tracking-wider text-slate-500 dark:text-slate-400">Discovered {{ $products->total() }} premium configurations</p>
            @else
                <h2 class="sf-section-title text-xl md:text-2xl font-black tracking-tight uppercase text-slate-900 dark:text-white">Premium Hardware Fleet</h2>
                <p class="sf-section-sub text-[10px] mt-1.5 uppercase font-bold tracking-wider text-slate-500 dark:text-slate-400">Directly imported components optimized for enterprise and workstation builds</p>
            @endif
        </div>

        <!-- Filter Chips (Horizontal scrollable) -->
        <div class="flex items-center gap-2 overflow-x-auto custom-scrollbar pb-2 max-w-full">
            <a href="{{ route('storefront.index') }}" class="sf-chip {{ !Route::current()->parameter('name') ? 'sf-chip-active' : '' }} px-4 py-2 rounded-xl text-xs font-black transition-all whitespace-nowrap">
                ALL HARDWARE
            </a>
            @if (isset($categories) && count($categories) > 0)
                @foreach ($categories->take(8) as $cat)
                    <a href="{{ route('storefront.category', $cat->cat_name) }}" class="sf-chip px-4 py-2 rounded-xl text-xs font-black uppercase transition-all whitespace-nowrap">
                        {{ $cat->cat_name }}
                    </a>
                @endforeach
            @endif
        </div>
    </div>

    <!-- ── Products Grid ── -->
    @if (count($products) > 0)
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 mb-12">
            @foreach ($products as $prod)
                <x-product-card :product="$prod" />
            @endforeach
        </div>

        <!-- Paginate Links -->
        <div class="mt-12 flex justify-center">
            <div class="sf-pagination px-5 py-3 rounded-2xl border border-slate-200/70 dark:border-slate-800/70 flex items-center gap-2 bg-white/92 dark:bg-slate-900/82 shadow-lg shadow-blue-500/5">
                {{ $products->links() }}
            </div>
        </div>
    @else
        <!-- Empty State -->
        <div class="sf-cat-card text-center py-16 border border-slate-200/70 dark:border-slate-850/80 bg-white/92 dark:bg-slate-900/92 rounded-[2rem] p-8 max-w-md mx-auto shadow-2xl shadow-blue-500/5 relative overflow-hidden">
            <div class="absolute -right-12 -top-12 w-32 h-32 bg-blue-600/5 dark:bg-indigo-600/5 rounded-full blur-2xl"></div>
            <div class="h-16 w-16 bg-blue-50 dark:bg-indigo-950 rounded-2xl flex items-center justify-center text-blue-600 dark:text-indigo-400 text-3xl mx-auto mb-5 border border-blue-100 dark:border-indigo-900 shadow-inner">
                <i class="fa-solid fa-cube animate-bounce"></i>
            </div>
            <h3 class="sf-section-title text-sm font-black uppercase tracking-widest text-slate-800 dark:text-slate-100">No hardware models registered</h3>
            <p class="sf-section-sub text-xs mt-2.5 leading-relaxed text-slate-500 dark:text-slate-400">
                There are currently no products corresponding to your search terms or active categories. Expand your filtering parameters to find custom configurations.
            </p>
            <a href="{{ route('storefront.index') }}" class="inline-flex mt-6 px-5 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-700 hover:from-blue-500 hover:to-indigo-600 text-white text-xs font-black uppercase tracking-widest rounded-xl transition-all shadow-lg shadow-blue-600/15 cursor-pointer">
                Reset Storefront Filters
            </a>
        </div>
    @endif

    <!-- ── Full-Width Premium Promo Banner ── -->
    <div class="sf-home-banner mt-16 relative overflow-hidden rounded-[2rem] border p-8 md:p-12 shadow-2xl">
        <div class="absolute -right-24 -top-24 w-80 h-80 bg-blue-600/10 rounded-full blur-3xl pointer-events-none"></div>
        
        <div class="relative z-10 max-w-lg space-y-4">
            <span class="px-2.5 py-0.5 rounded bg-orange-600 text-white text-[9px] font-black tracking-widest uppercase">Limited Campaign</span>
            <h2 class="sf-home-banner-title text-2xl md:text-3xl font-black uppercase tracking-tight font-outfit">Premium Workstation Upgrade Kits</h2>
            <p class="sf-home-banner-copy text-xs leading-relaxed">
                Maximize hardware bandwidth metrics by upgrading with premium dual-channel server memory arrays, certified SSD units, and modular power matrices. Direct consultations available with our hardware advisors.
            </p>
            <div class="flex items-center gap-4 pt-2">
                <a href="tel:+919944228686" class="sf-home-banner-btn px-5 py-2.5 text-white text-xs font-black uppercase tracking-widest rounded-xl shadow-lg transition-all">Consult Advisor</a>
                <span class="sf-home-banner-copy text-xs font-mono font-bold">+91 99442 28686</span>
            </div>
        </div>
    </div>

    <!-- ── Value Propositions (MTL Trust Grid) ── -->
    <div class="mt-16 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="glassmorphism sf-home-value-card rounded-2xl p-6 transition-all duration-300 hover:-translate-y-1 hover:border-blue-500/15 dark:hover:border-indigo-500/15 shadow-sm">
            <div class="w-14 h-14 bg-blue-500/10 border border-blue-500/20 rounded-2xl flex items-center justify-center text-blue-600 dark:text-indigo-400 mb-4 text-2xl">
                <i class="fa-solid fa-shield-halved"></i>
            </div>
            <h3 class="sf-home-value-title text-sm font-extrabold uppercase tracking-wider mb-2">Authentic Imports</h3>
            <p class="sf-home-value-copy text-xs leading-relaxed">
                Direct sourcing channels from global technology suppliers. Authenticated serial numbers for structural integrity and corporate compliance.
            </p>
        </div>
        <div class="glassmorphism sf-home-value-card rounded-2xl p-6 transition-all duration-300 hover:-translate-y-1 hover:border-blue-500/15 dark:hover:border-indigo-500/15 shadow-sm">
            <div class="w-14 h-14 bg-blue-500/10 border border-blue-500/20 rounded-2xl flex items-center justify-center text-blue-600 dark:text-indigo-400 mb-4 text-2xl">
                <i class="fa-solid fa-truck-fast"></i>
            </div>
            <h3 class="sf-home-value-title text-sm font-extrabold uppercase tracking-wider mb-2">Insured Logistics</h3>
            <p class="sf-home-value-copy text-xs leading-relaxed">
                Priority courier network spanning Tamil Nadu and interstate destinations. Safe transit packaging preserves micro-circuitry metrics.
            </p>
        </div>
        <div class="glassmorphism sf-home-value-card rounded-2xl p-6 transition-all duration-300 hover:-translate-y-1 hover:border-blue-500/15 dark:hover:border-indigo-500/15 shadow-sm">
            <div class="w-14 h-14 bg-blue-500/10 border border-blue-500/20 rounded-2xl flex items-center justify-center text-blue-600 dark:text-indigo-400 mb-4 text-2xl">
                <i class="fa-solid fa-file-invoice-dollar"></i>
            </div>
            <h3 class="sf-home-value-title text-sm font-extrabold uppercase tracking-wider mb-2">GST Compliant</h3>
            <p class="sf-home-value-copy text-xs leading-relaxed">
                B2B invoicing models. Seamlessly claim CGST and SGST inputs. Detailed tax splits computed automatically during order checkout.
            </p>
        </div>
        <div class="glassmorphism sf-home-value-card rounded-2xl p-6 transition-all duration-300 hover:-translate-y-1 hover:border-blue-500/15 dark:hover:border-indigo-500/15 shadow-sm">
            <div class="w-14 h-14 bg-blue-500/10 border border-blue-500/20 rounded-2xl flex items-center justify-center text-blue-600 dark:text-indigo-400 mb-4 text-2xl">
                <i class="fa-solid fa-headset"></i>
            </div>
            <h3 class="sf-home-value-title text-sm font-extrabold uppercase tracking-wider mb-2">System Advisors</h3>
            <p class="sf-home-value-copy text-xs leading-relaxed">
                Direct phone consultation for bulk system requirements, customized server rigs, RAM matching ratios, and architecture planning.
            </p>
        </div>
    </div>

    <!-- Countdown Timer Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Set dynamic countdown to the next upcoming Sunday 23:59:59
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
                    countdownTarget = getNextReset(); // Recalculate
                    distance = countdownTarget - now;
                }

                var days = Math.floor(distance / (1000 * 60 * 60 * 24));
                var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                var seconds = Math.floor((distance % (1000 * 60)) / 1000);

                // Format leading zeros
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
