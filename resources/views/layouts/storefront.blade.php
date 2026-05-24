<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Theme: read from localStorage BEFORE paint to avoid flash of wrong theme -->
    <script>
        (function(){
            var t = localStorage.getItem('sf-theme');
            if (t !== 'dark') {
                t = 'light';
            }
            if (t === 'dark') {
                document.documentElement.classList.add('dark');
                document.documentElement.classList.remove('light');
            } else {
                document.documentElement.classList.remove('dark');
                document.documentElement.classList.add('light');
            }
        })();
    </script>

    <title>{{ $title ?? 'MTL Mart - Premium Electronics & Computers' }}</title>
    <meta name="description" content="{{ $metaDesc ?? 'MTL Mart — Premium electronics, computers, laptops, and accessories. GST compliant, fast delivery across Tamil Nadu.' }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        *, *::before, *::after { box-sizing: border-box; }
        [x-cloak] { display: none !important; }

        body { font-family: 'Inter', sans-serif; }
        h1, h2, h3, h4, h5, h6 { font-family: 'Outfit', sans-serif; }

        body {
            background:
                radial-gradient(circle at top left, rgba(28,63,206,0.12), transparent 28%),
                radial-gradient(circle at top right, rgba(247,107,28,0.08), transparent 24%),
                linear-gradient(180deg, #f7f9ff 0%, #eef2ff 100%);
            color: #0f1b3d;
        }

        body, header, footer, nav, main, .sf-card, .sf-top-bar {
            transition: background-color 0.25s ease, color 0.25s ease, border-color 0.25s ease;
        }

        /* ══ DARK MODE ══ */
        html.dark body {
            background:
                radial-gradient(circle at top left, rgba(28,63,206,0.18), transparent 28%),
                radial-gradient(circle at top right, rgba(247,107,28,0.08), transparent 24%),
                linear-gradient(180deg, #090e17 0%, #0d1421 100%);
            color: #f3f4f6;
        }
        html.dark .sf-top-bar { background-color: rgba(13,20,33,0.96); border-color: transparent; color: #94a3b8; }
        html.dark .sf-header { background-color: rgba(9,14,23,0.94); border-color: rgba(148,163,184,0.10); box-shadow: 0 16px 40px rgba(0,0,0,0.18); }
        html.dark .sf-footer { background-color: #0d1421; border-color: transparent; }
        html.dark .sf-mobile-drawer { background-color: rgba(9,14,23,0.98); border-color: transparent; }
        html.dark .sf-search-input { background-color: #111827; border-color: #374151; color: #d1d5db; }
        html.dark .sf-search-input::placeholder { color: #6b7280; }
        html.dark .glassmorphism { background: rgba(15,23,42,0.58); border: 1px solid rgba(255,255,255,0.06); backdrop-filter: blur(20px); box-shadow: 0 16px 50px rgba(0,0,0,0.18); }
        html.dark .sf-hero { background: linear-gradient(135deg, rgba(17,24,39,0.94) 0%, rgba(9,14,23,0.98) 100%); border-color: rgba(148,163,184,0.10); box-shadow: 0 24px 70px rgba(0,0,0,0.20); }
        html.dark .sf-hero-title { color: #ffffff; }
        html.dark .sf-hero-sub { color: #94a3b8; }
        html.dark .sf-cat-card { background: rgba(17,24,39,0.92); border-color: rgba(148,163,184,0.10); box-shadow: 0 12px 30px rgba(0,0,0,0.14); }
        html.dark .sf-cat-card:hover { border-color: rgba(28,63,206,0.24); box-shadow: 0 18px 40px rgba(0,0,0,0.16); }
        html.dark .sf-cat-name { color: #e2e8f0; }
        html.dark .sf-product-card { background: linear-gradient(to bottom, rgba(17,24,39,0.96), rgba(13,20,33,0.98)); border-color: rgba(148,163,184,0.10); box-shadow: 0 16px 42px rgba(0,0,0,0.16); }
        html.dark .sf-product-card:hover { border-color: rgba(28,63,206,0.22); box-shadow: 0 22px 52px rgba(0,0,0,0.20); }
        html.dark .sf-product-card .sf-img-area { background-color: #090e17; border-color: rgba(148,163,184,0.08); }
        html.dark .sf-prod-name { color: #f1f5f9; }
        html.dark .sf-prod-desc { color: #94a3b8; }
        html.dark .sf-price-main { color: #818cf8; }
        html.dark .sf-nav-btn { background-color: rgba(17,24,39,0.9); border-color: rgba(148,163,184,0.10); color: #9ca3af; }
        html.dark .sf-nav-btn:hover { background-color: #1f2937; color: #ffffff; }
        html.dark .sf-dropdown { background-color: rgba(17,24,39,0.96); border-color: rgba(148,163,184,0.10); box-shadow: 0 24px 70px rgba(0,0,0,0.40); }
        html.dark .sf-dropdown a { color: #94a3b8; }
        html.dark .sf-dropdown a:hover { background-color: #1f2937; color: #ffffff; }
        html.dark .sf-chip { background-color: rgba(17,24,39,0.9); border-color: rgba(148,163,184,0.10); color: #94a3b8; }
        html.dark .sf-chip:hover { background-color: #1f2937; color: #e2e8f0; }
        html.dark .sf-chip-active { background-color: #1c3fce; color: #ffffff; border-color: #1c3fce; }
        html.dark .sf-section-title { color: #f1f5f9; }
        html.dark .sf-section-sub { color: #64748b; }
        html.dark .sf-pagination { background-color: rgba(17,24,39,0.92); border-color: rgba(148,163,184,0.10); }
        html.dark .sf-promo-card { background: linear-gradient(135deg, rgba(17,24,39,0.96), rgba(30,41,59,0.96)); border-color: rgba(148,163,184,0.10); box-shadow: 0 18px 44px rgba(0,0,0,0.18); }
        html.dark .sf-footer h4 { color: #e2e8f0; }
        html.dark .sf-footer p, html.dark .sf-footer a { color: #64748b; }
        html.dark .sf-footer a:hover { color: #e2e8f0; }

        /* ══ LIGHT MODE ══ */
        html.light body {
            background:
                radial-gradient(circle at top left, rgba(28,63,206,0.10), transparent 28%),
                radial-gradient(circle at top right, rgba(247,107,28,0.08), transparent 22%),
                linear-gradient(180deg, #f7f9ff 0%, #eef2ff 100%);
            color: #0f1b3d;
        }
        html.light .sf-top-bar { background-color: #1c3fce; color: #ffffff; border-color: transparent; box-shadow: inset 0 -1px 0 rgba(255,255,255,0.08); }
        html.light .sf-header { background-color: rgba(255,255,255,0.92); border-color: rgba(148,163,184,0.14); box-shadow: 0 10px 30px rgba(15,23,42,0.06); backdrop-filter: blur(16px); }
        html.light .sf-footer { background-color: #0f1b3d; border-color: transparent; }
        html.light .sf-mobile-drawer { background-color: rgba(255,255,255,0.96); border-color: rgba(148,163,184,0.14); box-shadow: 0 20px 50px rgba(15,23,42,0.08); backdrop-filter: blur(16px); }
        html.light .sf-search-input { background-color: #f5f7ff; border-color: #dde3f0; color: #0f1b3d; }
        html.light .sf-search-input::placeholder { color: #94a3b8; }
        html.light .glassmorphism { background: rgba(255,255,255,0.86); border: 1px solid rgba(148,163,184,0.12); backdrop-filter: blur(20px); color: #0f1b3d; box-shadow: 0 14px 36px rgba(15,23,42,0.05); }
        html.light .glassmorphism h3, html.light .glassmorphism p { color: #0f1b3d; }
        html.light .glassmorphism p { color: #4a5568; }
        html.light .sf-hero { background: linear-gradient(135deg, #ffffff 0%, #f4f7ff 50%, #eef3ff 100%); border-color: rgba(148,163,184,0.14); box-shadow: 0 24px 70px rgba(28,63,206,0.08); }
        html.light .sf-hero-title { color: #0f1b3d; }
        html.light .sf-hero-sub { color: #4a5568; }
        html.light .sf-cat-card { background: rgba(255,255,255,0.92); border-color: rgba(148,163,184,0.14); box-shadow: 0 12px 30px rgba(15,23,42,0.05); }
        html.light .sf-cat-card:hover { border-color: rgba(28,63,206,0.18); box-shadow: 0 18px 40px rgba(28,63,206,0.10); }
        html.light .sf-cat-name { color: #1e293b; }
        html.light .sf-product-card { background: rgba(255,255,255,0.94); border-color: rgba(148,163,184,0.14); box-shadow: 0 14px 36px rgba(15,23,42,0.06); }
        html.light .sf-product-card:hover { border-color: rgba(28,63,206,0.18); box-shadow: 0 18px 44px rgba(28,63,206,0.10); }
        html.light .sf-product-card .sf-img-area { background-color: #f8faff; border-color: rgba(148,163,184,0.14); }
        html.light .sf-prod-name { color: #0f1b3d; }
        html.light .sf-prod-desc { color: #4a5568; }
        html.light .sf-price-main { color: #1c3fce; }
        html.light .sf-nav-btn { background-color: #f5f7ff; border-color: rgba(148,163,184,0.14); color: #4a5568; }
        html.light .sf-nav-btn:hover { background-color: #eef1ff; color: #0f1b3d; border-color: rgba(28,63,206,0.18); }
        html.light .sf-dropdown { background-color: rgba(255,255,255,0.98); border-color: rgba(148,163,184,0.14); box-shadow: 0 20px 50px rgba(15,23,42,0.10); }
        html.light .sf-dropdown a { color: #4a5568; }
        html.light .sf-dropdown a:hover { background-color: #f5f7ff; color: #0f1b3d; }
        html.light .sf-chip { background-color: #f5f7ff; border-color: rgba(148,163,184,0.14); color: #4a5568; }
        html.light .sf-chip:hover { background-color: #eef1ff; color: #0f1b3d; border-color: rgba(28,63,206,0.18); }
        html.light .sf-chip-active { background-color: #1c3fce; color: #ffffff; border-color: #1c3fce; }
        html.light .sf-section-title { color: #0f1b3d; }
        html.light .sf-section-sub { color: #64748b; }
        html.light .sf-pagination { background-color: rgba(255,255,255,0.96); border-color: rgba(148,163,184,0.14); box-shadow: 0 10px 30px rgba(15,23,42,0.06); }
        html.light .sf-promo-card { background: linear-gradient(135deg, #ffffff, #eef1ff); border-color: rgba(148,163,184,0.14); box-shadow: 0 18px 40px rgba(28,63,206,0.08); }
        html.dark .sf-home-visual {
            background: linear-gradient(135deg, rgba(2,6,23,0.96) 0%, rgba(15,23,42,0.96) 55%, rgba(30,41,59,0.96) 100%);
            border-color: rgba(148,163,184,0.10);
        }
        html.light .sf-home-visual {
            background: linear-gradient(135deg, rgba(255,255,255,0.96) 0%, rgba(241,245,249,0.94) 55%, rgba(226,232,240,0.92) 100%);
            border-color: rgba(148,163,184,0.14);
        }
        html.dark .sf-home-visual-card {
            background: rgba(15,23,42,0.46);
            border-color: rgba(255,255,255,0.06);
            color: #f8fafc;
        }
        html.light .sf-home-visual-card {
            background: rgba(255,255,255,0.82);
            border-color: rgba(148,163,184,0.16);
            color: #0f1b3d;
        }
        html.dark .sf-home-visual-mini {
            background: rgba(255,255,255,0.10);
            border-color: rgba(255,255,255,0.10);
            color: #ffffff;
        }
        html.light .sf-home-visual-mini {
            background: rgba(255,255,255,0.90);
            border-color: rgba(148,163,184,0.14);
            color: #0f1b3d;
        }
        html.dark .sf-home-campaign-card {
            background: linear-gradient(135deg, rgba(2,6,23,0.98), rgba(15,23,42,0.98));
            border-color: rgba(148,163,184,0.10);
            color: #ffffff;
        }
        html.light .sf-home-campaign-card {
            background: linear-gradient(135deg, #ffffff, #eef3ff);
            border-color: rgba(148,163,184,0.14);
            color: #0f1b3d;
        }
        html.dark .sf-home-campaign-title { color: #ffffff; }
        html.light .sf-home-campaign-title { color: #0f1b3d; }
        html.dark .sf-home-campaign-copy { color: #94a3b8; }
        html.light .sf-home-campaign-copy { color: #4a5568; }
        html.dark .sf-home-campaign-btn {
            background: rgba(255,255,255,0.10);
            border-color: rgba(255,255,255,0.20);
            color: #ffffff;
        }
        html.light .sf-home-campaign-btn {
            background: #ffffff;
            border-color: rgba(148,163,184,0.14);
            color: #0f1b3d;
        }
        html.dark .sf-home-banner {
            background: linear-gradient(135deg, rgba(2,6,23,0.98), rgba(15,23,42,0.98));
            border-color: rgba(148,163,184,0.10);
            color: #ffffff;
        }
        html.light .sf-home-banner {
            background: linear-gradient(135deg, #ffffff, #eef1ff);
            border-color: rgba(148,163,184,0.14);
            color: #0f1b3d;
        }
        html.dark .sf-home-banner-title { color: #ffffff; }
        html.light .sf-home-banner-title { color: #0f1b3d; }
        html.dark .sf-home-banner-copy { color: #94a3b8; }
        html.light .sf-home-banner-copy { color: #475569; }
        html.dark .sf-home-banner-btn {
            background: linear-gradient(90deg, #f97316, #ea580c);
            color: #ffffff;
        }
        html.light .sf-home-banner-btn {
            background: linear-gradient(90deg, #1c3fce, #0033dd);
            color: #ffffff;
        }
        html.dark .sf-home-value-card {
            background: rgba(17,24,39,0.92);
            border-color: rgba(148,163,184,0.10);
        }
        html.light .sf-home-value-card {
            background: rgba(255,255,255,0.86);
            border-color: rgba(148,163,184,0.14);
        }
        html.dark .sf-home-value-title { color: #ffffff; }
        html.light .sf-home-value-title { color: #0f1b3d; }
        html.dark .sf-home-value-copy { color: #94a3b8; }
        html.light .sf-home-value-copy { color: #4a5568; }
        html.light .sf-btn-view { background-color: #f5f7ff; border-color: rgba(148,163,184,0.14); color: #4a5568; }
        html.light .sf-btn-view:hover { background-color: #eef1ff; color: #0f1b3d; }
        html.light .sf-price-area { border-color: rgba(148,163,184,0.14); }

        /* Custom Scrollbar */
        .custom-scrollbar::-webkit-scrollbar { width: 4px; height: 4px; }
        html.dark .custom-scrollbar::-webkit-scrollbar-track { background: #1f2937; }
        html.light .custom-scrollbar::-webkit-scrollbar-track { background: #f5f7ff; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(28,63,206,0.3); border-radius: 9999px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: rgba(28,63,206,0.6); }

        /* Section tab active */
        .sf-tab-active { color: #1c3fce; border-bottom: 2px solid #1c3fce; font-weight: 700; }
        html.dark .sf-tab-active { color: #818cf8; border-color: #818cf8; }

        /* Announcement bar scroll */
        .sf-announcement { animation: marquee 25s linear infinite; white-space: nowrap; display: inline-block; }
        @keyframes marquee { 0% { transform: translateX(0); } 100% { transform: translateX(-50%); } }

        /* Countdown digits */
        .sf-countdown-digit {
            display: inline-flex; flex-direction: column; align-items: center;
            background: #0f1b3d; color: #fff;
            border-radius: 6px; padding: 6px 10px; min-width: 44px;
            font-family: 'Outfit', sans-serif; font-weight: 800; font-size: 20px; line-height: 1;
        }
        .sf-countdown-digit span { font-size: 9px; font-weight: 600; color: #94a3b8; margin-top: 3px; letter-spacing: 1px; text-transform: uppercase; }

        html.light .sf-countdown-digit { background: #0f1b3d; color: #fff; }
        html.dark .sf-countdown-digit { background: #1f2937; color: #fff; }

        @media (prefers-reduced-motion: reduce) {
            *, ::before, ::after { animation-duration: 0.01ms !important; transition-duration: 0.01ms !important; }
        }
    </style>
</head>
<body class="font-sans antialiased overflow-x-hidden flex flex-col min-h-screen">

    <!-- ── Announcement Top Bar ── -->
    <div class="sf-top-bar border-b text-xs font-medium py-2 overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 flex items-center justify-between gap-4">
            <div class="overflow-hidden flex-1">
                <div class="sf-announcement flex items-center gap-10 whitespace-nowrap">
                    <span class="inline-flex items-center gap-2"><i class="fa-solid fa-tag"></i><span>10% OFF when paying by Debit Card</span><span class="ml-6 text-white/60">·</span></span>
                    <span class="inline-flex items-center gap-2"><i class="fa-solid fa-truck-fast"></i><span>Fast Delivery across Tamil Nadu</span><span class="ml-6 text-white/60">·</span></span>
                    <span class="inline-flex items-center gap-2"><i class="fa-solid fa-file-invoice-dollar"></i><span>GST Compliant Invoicing</span><span class="ml-6 text-white/60">·</span></span>
                    <span class="inline-flex items-center gap-2"><i class="fa-solid fa-shield-halved"></i><span>Authentic Imports, Warranty Backed</span><span class="ml-6 text-white/60">·</span></span>
                    <span class="inline-flex items-center gap-2"><i class="fa-solid fa-tag"></i><span>10% OFF when paying by Debit Card</span><span class="ml-6 text-white/60">·</span></span>
                    <span class="inline-flex items-center gap-2"><i class="fa-solid fa-truck-fast"></i><span>Fast Delivery across Tamil Nadu</span><span class="ml-6 text-white/60">·</span></span>
                    <span class="inline-flex items-center gap-2"><i class="fa-solid fa-file-invoice-dollar"></i><span>GST Compliant Invoicing</span><span class="ml-6 text-white/60">·</span></span>
                    <span class="inline-flex items-center gap-2"><i class="fa-solid fa-shield-halved"></i><span>Authentic Imports, Warranty Backed</span><span class="ml-6 text-white/60">·</span></span>
                </div>
            </div>
            <div class="hidden md:flex items-center gap-4 flex-shrink-0 text-[11px]">
                <span><i class="fa-solid fa-phone mr-1"></i>+91 99442 28686</span>
                <button id="sf-theme-toggle" onclick="window.sfToggleTheme()" title="Toggle theme" class="flex items-center gap-1.5 px-2.5 py-1 rounded-lg border border-white/20 cursor-pointer transition-all hover:bg-white/10">
                    <i id="sf-icon-sun" class="fa-solid fa-sun text-xs" style="display:block"></i>
                    <i id="sf-icon-moon" class="fa-solid fa-moon text-xs" style="display:none"></i>
                    <span id="sf-theme-label" class="text-[10px] font-semibold">Light</span>
                </button>
            </div>
        </div>
    </div>

    <!-- ── Main Navigation ── -->
    <header class="sf-header w-full sticky top-0 z-40 border-b" x-data="{ mobileMenuOpen: false }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-18 py-3 gap-4">

                <!-- Logo -->
                <a href="{{ route('storefront.index') }}" class="flex items-center gap-3 flex-shrink-0 group">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-600 to-indigo-700 flex items-center justify-center shadow-lg group-hover:scale-105 transition-transform">
                        <span class="font-black text-white text-lg leading-none font-['Outfit']">M</span>
                    </div>
                    <div>
                        <div class="text-lg font-black tracking-tight leading-none" style="color:#1c3fce">MTL Mart</div>
                        <div class="text-[9px] font-bold uppercase tracking-widest" style="color:#f76b1c">Computer Garden</div>
                    </div>
                </a>

                <!-- Categories Dropdown (Desktop) -->
                <div class="hidden lg:block relative flex-shrink-0" x-data="{ open: false }" @click.away="open = false">
                    <button @click="open = !open" class="sf-nav-btn flex items-center gap-2 px-4 py-2.5 text-sm font-semibold border rounded-xl transition-all duration-200 cursor-pointer">
                        <i class="fa-solid fa-bars-staggered text-sm" style="color:#f76b1c"></i>
                        <span>All Categories</span>
                        <i class="fa-solid fa-chevron-down text-xs transition-transform duration-200" :class="open ? 'rotate-180' : ''"></i>
                    </button>

                    <div x-show="open"
                         x-cloak
                         x-transition:enter="transition ease-out duration-100"
                         x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
                         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-end="opacity-0 scale-95"
                         class="sf-dropdown absolute left-0 mt-2 w-60 border rounded-2xl shadow-2xl p-2 z-50">
                        <div class="max-h-80 overflow-y-auto custom-scrollbar space-y-0.5">
                            <a href="{{ route('storefront.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold transition-all">
                                <i class="fa-solid fa-grid-2 text-xs" style="color:#f76b1c"></i>
                                <span>All Products</span>
                            </a>
                            @if (isset($categories) && count($categories) > 0)
                                @foreach ($categories as $cat)
                                    <a href="{{ route('storefront.category', $cat->cat_name) }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all">
                                        <i class="fa-solid fa-chevron-right text-xs" style="color:#1c3fce;opacity:0.6"></i>
                                        <span class="capitalize">{{ $cat->cat_name }}</span>
                                    </a>
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Search Bar (Desktop) -->
                <div class="hidden md:flex flex-1 max-w-xl">
                    <form action="{{ route('storefront.index') }}" method="GET" class="w-full relative">
                        <input type="text"
                               name="search"
                               id="sf-search-desktop"
                               value="{{ request('search') }}"
                               placeholder="Search products, brands, categories..."
                               class="sf-search-input w-full pl-12 pr-12 py-3 border rounded-xl text-sm font-medium outline-none transition-all duration-300 placeholder-slate-400 dark:placeholder-slate-500 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                        <button type="submit" class="absolute left-0 top-0 h-full px-4 flex items-center cursor-pointer" style="color:#94a3b8">
                            <i class="fa-solid fa-magnifying-glass text-base"></i>
                        </button>
                        @if(request('search'))
                            <a href="{{ route('storefront.index') }}" class="absolute right-4 top-1/2 -translate-y-1/2" style="color:#94a3b8">
                                <i class="fa-solid fa-xmark text-sm"></i>
                            </a>
                        @endif
                    </form>
                </div>

                <!-- Right Actions -->
                <div class="flex items-center gap-2 flex-shrink-0">

                    <!-- Phone (desktop) -->
                    <div class="hidden xl:flex flex-col items-end leading-tight mr-2">
                        <span class="text-[9px] font-semibold" style="color:#94a3b8">Call Us</span>
                        <span class="text-xs font-bold" style="color:#1c3fce">+91 99442 28686</span>
                    </div>

                    <!-- Wishlist placeholder -->
                    <!-- <button class="sf-nav-btn relative p-2.5 border rounded-xl transition-all duration-200 group cursor-pointer" title="Wishlist">
                        <i class="fa-regular fa-heart text-lg group-hover:scale-110 transition-transform"></i>
                    </button> -->

                    <!-- Cart -->
                    @php
                        $cartCount = 0;
                        if (session()->has('cart')) {
                            foreach (session('cart') as $item) { $cartCount += $item['quantity']; }
                        }
                    @endphp
                    <a href="{{ route('storefront.cart') }}" class="sf-nav-btn relative p-2.5 border rounded-xl transition-all duration-200 group" title="Cart">
                        <span class="relative inline-flex items-center justify-center">
                            <i class="fa-solid fa-cart-shopping text-lg group-hover:scale-110 transition-transform"></i>
                            @if ($cartCount > 0)
                                <span class="absolute -top-2 -right-2 flex h-5 w-5 items-center justify-center rounded-full text-[10px] font-black text-white shadow-lg ring-2 ring-white dark:ring-slate-900" style="background:#f76b1c">
                                    {{ $cartCount }}
                                </span>
                            @endif
                        </span>
                    </a>

                    <!-- User Account -->
                    <div class="hidden sm:block relative" x-data="{ userOpen: false }" @click.away="userOpen = false">
                        @if (Auth::check())
                            <button @click="userOpen = !userOpen" class="sf-nav-btn flex items-center gap-2 px-3 py-2 border rounded-xl text-sm font-semibold transition-all cursor-pointer">
                                <div class="w-7 h-7 flex items-center justify-center rounded-lg text-white font-black text-xs uppercase shadow" style="background:#1c3fce">
                                    {{ substr(Auth::user()->uname ?: Auth::user()->name ?: 'C', 0, 1) }}
                                </div>
                                <span class="max-w-[90px] truncate hidden md:block">{{ Auth::user()->uname ?: Auth::user()->name }}</span>
                                <i class="fa-solid fa-chevron-down text-[9px]"></i>
                            </button>
                            <div x-show="userOpen" x-cloak
                                 x-transition:enter="transition ease-out duration-100"
                                 x-transition:enter-start="opacity-0 scale-95"
                                 x-transition:enter-end="opacity-100 scale-100"
                                 x-transition:leave="transition ease-in duration-75"
                                 x-transition:leave-end="opacity-0 scale-95"
                                 class="sf-dropdown absolute right-0 mt-2 w-52 border rounded-2xl shadow-2xl p-2 z-50">
                                <div class="px-3 py-2 border-b mb-1" style="border-color:rgba(0,0,0,0.06)">
                                    <p class="text-xs font-bold sf-section-title">{{ Auth::user()->uname ?: Auth::user()->name }}</p>
                                    <p class="text-[11px] sf-section-sub">Valued Customer</p>
                                </div>
                                <a href="{{ route('storefront.orders') }}" class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl text-sm font-medium transition-all">
                                    <i class="fa-solid fa-receipt text-xs" style="color:#1c3fce"></i>
                                    <span>My Orders</span>
                                </a>
                                <div class="border-t my-1" style="border-color:rgba(0,0,0,0.06)"></div>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full flex items-center gap-2.5 px-3 py-2.5 rounded-xl text-sm font-medium transition-all text-left cursor-pointer" style="color:#ef4444">
                                        <i class="fa-solid fa-right-from-bracket text-xs"></i>
                                        <span>Sign Out</span>
                                    </button>
                                </form>
                            </div>
                        @else
                            <a href="{{ route('login') }}" class="flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-bold text-white transition-all shadow-lg cursor-pointer" style="background:#1c3fce;box-shadow:0 4px 14px rgba(28,63,206,0.3)">
                                <i class="fa-solid fa-user text-xs"></i>
                                <span>Sign In</span>
                            </a>
                        @endif
                    </div>

                    <!-- Mobile Menu Toggle -->
                    <button @click="mobileMenuOpen = !mobileMenuOpen" class="lg:hidden sf-nav-btn p-2.5 border rounded-xl transition-all cursor-pointer">
                        <i class="fa-solid text-lg" :class="mobileMenuOpen ? 'fa-xmark' : 'fa-bars'"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Drawer -->
        <div x-show="mobileMenuOpen" x-cloak
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 -translate-y-3"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-end="opacity-0 -translate-y-3"
             class="sf-mobile-drawer lg:hidden border-t px-4 py-5 space-y-4 z-40 relative">

            <form action="{{ route('storefront.index') }}" method="GET" class="relative">
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Search products..."
                       class="sf-search-input w-full pl-10 pr-4 py-2.5 border rounded-xl text-sm outline-none">
                <div class="absolute left-3.5 top-3 text-slate-400"><i class="fa-solid fa-magnifying-glass text-sm"></i></div>
            </form>

            <div class="space-y-1">
                <span class="block px-3 text-[10px] font-extrabold uppercase tracking-widest mb-2 sf-section-sub">Categories</span>
                <div class="max-h-48 overflow-y-auto custom-scrollbar space-y-0.5">
                    <a href="{{ route('storefront.index') }}" class="block px-3 py-2.5 text-sm font-semibold rounded-xl transition-all sf-section-title hover:bg-blue-50 dark:hover:bg-slate-800">
                        All Products
                    </a>
                    @if (isset($categories) && count($categories) > 0)
                        @foreach ($categories as $cat)
                            <a href="{{ route('storefront.category', $cat->cat_name) }}" class="block px-3 py-2 text-sm font-medium rounded-xl transition-all capitalize sf-section-sub hover:bg-blue-50 dark:hover:bg-slate-800">
                                {{ $cat->cat_name }}
                            </a>
                        @endforeach
                    @endif
                </div>
            </div>

            <div class="border-t pt-4 space-y-2" style="border-color:rgba(0,0,0,0.08)">
                @if (Auth::check())
                    <div class="flex items-center gap-3 px-3 py-2 rounded-xl mb-2" style="background:rgba(28,63,206,0.06)">
                        <div class="w-9 h-9 flex items-center justify-center rounded-xl text-white font-black text-sm uppercase" style="background:#1c3fce">
                            {{ substr(Auth::user()->uname ?: Auth::user()->name ?: 'C', 0, 1) }}
                        </div>
                        <div>
                            <p class="text-sm font-bold sf-section-title">{{ Auth::user()->uname ?: Auth::user()->name }}</p>
                            <p class="text-[10px] sf-section-sub">Valued Customer</p>
                        </div>
                    </div>
                    <a href="{{ route('storefront.orders') }}" class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl text-sm font-semibold transition-all sf-section-title">
                        <i class="fa-solid fa-receipt text-xs" style="color:#1c3fce"></i> My Orders
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full flex items-center gap-2.5 px-3 py-2.5 rounded-xl text-sm font-semibold transition-all text-left cursor-pointer" style="color:#ef4444">
                            <i class="fa-solid fa-right-from-bracket text-xs"></i> Sign Out
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="w-full flex justify-center py-3 rounded-xl text-sm font-bold text-white transition-all" style="background:#1c3fce">
                        Sign In / Register
                    </a>
                @endif
                <!-- Mobile theme toggle -->
                <button onclick="window.sfToggleTheme()" class="w-full flex items-center justify-center gap-2 py-2.5 rounded-xl text-sm font-semibold border sf-nav-btn cursor-pointer">
                    <i id="sf-icon-sun-m" class="fa-solid fa-sun"></i>
                    <i id="sf-icon-moon-m" class="fa-solid fa-moon" style="display:none"></i>
                    <span>Toggle Theme</span>
                </button>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-10">
        @if (session('success'))
            <div class="mb-6 p-4 rounded-2xl flex items-start gap-3 shadow-lg" style="background:rgba(16,185,129,0.08);border:1px solid rgba(16,185,129,0.2)">
                <i class="fa-solid fa-circle-check text-emerald-500 mt-0.5"></i>
                <div>
                    <span class="font-bold text-sm block" style="color:#065f46">Success</span>
                    <span class="text-sm" style="color:#047857">{{ session('success') }}</span>
                </div>
            </div>
        @endif
        @if (session('error'))
            <div class="mb-6 p-4 rounded-2xl flex items-start gap-3 shadow-lg" style="background:rgba(239,68,68,0.08);border:1px solid rgba(239,68,68,0.2)">
                <i class="fa-solid fa-triangle-exclamation text-red-500 mt-0.5"></i>
                <div>
                    <span class="font-bold text-sm block" style="color:#991b1b">Error</span>
                    <span class="text-sm" style="color:#b91c1c">{{ session('error') }}</span>
                </div>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- ── Footer ── -->
    <footer class="sf-footer border-t mt-auto" style="background:#0f1b3d">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 md:py-16">
            <!-- Newsletter -->
            <div class="flex flex-col md:flex-row items-center justify-between gap-6 pb-10 border-b" style="border-color:rgba(255,255,255,0.08)">
                <div>
                    <h4 class="text-lg font-black text-white">Sign up to Newsletter</h4>
                    <p class="text-sm mt-1" style="color:#94a3b8">Get the latest deals delivered to your inbox.</p>
                </div>
                <form class="flex gap-2 w-full md:w-auto">
                    <input type="email" placeholder="Your email address" class="flex-1 md:w-72 px-4 py-2.5 rounded-xl text-sm border outline-none transition-all" style="background:rgba(255,255,255,0.07);border-color:rgba(255,255,255,0.12);color:#fff" onfocus="this.style.borderColor='#f76b1c'" onblur="this.style.borderColor='rgba(255,255,255,0.12)'">
                    <button type="submit" class="px-5 py-2.5 rounded-xl text-sm font-bold text-white transition-all cursor-pointer flex-shrink-0" style="background:#f76b1c">Subscribe</button>
                </form>
            </div>

            <!-- Footer columns -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8 py-10">
                <!-- Brand -->
                <div class="col-span-2 md:col-span-1 space-y-4">
                    <a href="{{ route('storefront.index') }}" class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background:#1c3fce">
                            <span class="font-black text-white text-lg font-['Outfit']">M</span>
                        </div>
                        <div>
                            <div class="text-base font-black text-white">MTL Mart</div>
                            <div class="text-[10px] font-bold uppercase tracking-widest" style="color:#f76b1c">Computer Garden</div>
                        </div>
                    </a>
                    <p class="text-xs leading-relaxed" style="color:#64748b">
                        Premier importer and supplier of premium technology, server arrays, high-grade laptops, and desktop components. Committed to hardware durability and transactional trust.
                    </p>
                    <div class="flex items-center gap-3 pt-1">
                        <a href="#" class="w-8 h-8 rounded-lg flex items-center justify-center transition-all cursor-pointer" style="background:rgba(255,255,255,0.06)" title="Facebook">
                            <i class="fa-brands fa-facebook-f text-xs text-white"></i>
                        </a>
                        <a href="#" class="w-8 h-8 rounded-lg flex items-center justify-center transition-all cursor-pointer" style="background:rgba(255,255,255,0.06)" title="Instagram">
                            <i class="fa-brands fa-instagram text-xs text-white"></i>
                        </a>
                        <a href="#" class="w-8 h-8 rounded-lg flex items-center justify-center transition-all cursor-pointer" style="background:rgba(255,255,255,0.06)" title="WhatsApp">
                            <i class="fa-brands fa-whatsapp text-xs text-white"></i>
                        </a>
                    </div>
                </div>

                <!-- Get Help -->
                <div>
                    <h4 class="text-xs font-extrabold uppercase tracking-widest mb-4 text-white">Get Help</h4>
                    <ul class="space-y-2.5">
                        <li><a href="{{ route('storefront.index') }}" class="text-xs transition-colors hover:text-white" style="color:#64748b">Shop Products</a></li>
                        <li><a href="{{ route('storefront.cart') }}" class="text-xs transition-colors hover:text-white" style="color:#64748b">My Cart</a></li>
                        @if(Auth::check())
                            <li><a href="{{ route('storefront.orders') }}" class="text-xs transition-colors hover:text-white" style="color:#64748b">Track Orders</a></li>
                        @else
                            <li><a href="{{ route('login') }}" class="text-xs transition-colors hover:text-white" style="color:#64748b">Sign In</a></li>
                        @endif
                        <li><a href="#" class="text-xs transition-colors hover:text-white" style="color:#64748b">Returns Policy</a></li>
                        <li><a href="#" class="text-xs transition-colors hover:text-white" style="color:#64748b">Support Center</a></li>
                    </ul>
                </div>

                <!-- Quick Links -->
                <div>
                    <h4 class="text-xs font-extrabold uppercase tracking-widest mb-4 text-white">Categories</h4>
                    <ul class="space-y-2.5">
                        @if (isset($categories) && count($categories) > 0)
                            @foreach ($categories->take(6) as $cat)
                                <li>
                                    <a href="{{ route('storefront.category', $cat->cat_name) }}" class="text-xs capitalize transition-colors hover:text-white" style="color:#64748b">
                                        {{ $cat->cat_name }}
                                    </a>
                                </li>
                            @endforeach
                        @endif
                    </ul>
                </div>

                <!-- Contact -->
                <div>
                    <h4 class="text-xs font-extrabold uppercase tracking-widest mb-4 text-white">Customer Care</h4>
                    <div class="space-y-3 text-xs" style="color:#64748b">
                        <p class="flex items-start gap-2.5">
                            <i class="fa-solid fa-phone mt-0.5 flex-shrink-0" style="color:#f76b1c"></i>
                            <span>+91 99442 28686</span>
                        </p>
                        <p class="flex items-start gap-2.5">
                            <i class="fa-solid fa-envelope mt-0.5 flex-shrink-0" style="color:#f76b1c"></i>
                            <span>support@mtlmart.com</span>
                        </p>
                        <p class="flex items-start gap-2.5">
                            <i class="fa-solid fa-location-dot mt-0.5 flex-shrink-0" style="color:#f76b1c"></i>
                            <span>1st Floor, SUS Building, Opposite MRF Tyres, Main Road, Marthandam, TN – 629154</span>
                        </p>
                    </div>
                    <div class="mt-4 p-3 rounded-xl" style="background:rgba(255,255,255,0.04);border:1px solid rgba(255,255,255,0.08)">
                        <p class="text-[10px] font-bold text-white mb-1.5">Bank Details</p>
                        <p class="text-[10px]" style="color:#64748b">Indian Overseas Bank · Kuzhithurai</p>
                        <p class="text-[10px] font-mono" style="color:#94a3b8">A/C: 2869020000000349</p>
                        <p class="text-[10px] font-mono" style="color:#94a3b8">IFSC: IOBA0002869</p>
                    </div>
                </div>
            </div>

            <!-- Copyright -->
            <div class="border-t pt-8 flex flex-col md:flex-row items-center justify-between gap-4 text-[11px]" style="border-color:rgba(255,255,255,0.08);color:#475569">
                <p>&copy; {{ date('Y') }} MTL Computer Garden & Mart. All rights reserved.</p>
                <div class="flex items-center gap-6">
                    <span class="flex items-center gap-1.5">
                        <i class="fa-solid fa-shield-halved" style="color:#22c55e"></i> Secure Transactions
                    </span>
                    <span class="flex items-center gap-1.5">
                        <i class="fa-solid fa-truck-fast" style="color:#1c3fce"></i> Fast Delivery
                    </span>
                    <span class="flex items-center gap-1.5">
                        <i class="fa-solid fa-indian-rupee-sign" style="color:#f76b1c"></i> Save up to 20%
                    </span>
                </div>
            </div>
        </div>
    </footer>

    <script>
        window.sfToggleTheme = function() {
            var html = document.documentElement;
            var isDark = html.classList.contains('dark');
            if (isDark) {
                html.classList.remove('dark'); html.classList.add('light');
                localStorage.setItem('sf-theme', 'light');
            } else {
                html.classList.remove('light'); html.classList.add('dark');
                localStorage.setItem('sf-theme', 'dark');
            }
            sfSyncThemeButton();
        };

        window.sfSyncThemeButton = function() {
            var isDark = document.documentElement.classList.contains('dark');
            var sun  = document.getElementById('sf-icon-sun');
            var moon = document.getElementById('sf-icon-moon');
            var sunM  = document.getElementById('sf-icon-sun-m');
            var moonM = document.getElementById('sf-icon-moon-m');
            var label = document.getElementById('sf-theme-label');
            if (sun) { sun.style.display  = isDark ? 'block' : 'none'; }
            if (moon){ moon.style.display = isDark ? 'none' : 'block'; }
            if (sunM) { sunM.style.display  = isDark ? 'block' : 'none'; }
            if (moonM){ moonM.style.display = isDark ? 'none' : 'block'; }
            if (label) label.textContent = isDark ? 'Light' : 'Dark';
        };

        document.addEventListener('DOMContentLoaded', sfSyncThemeButton);
    </script>
</body>
</html>
