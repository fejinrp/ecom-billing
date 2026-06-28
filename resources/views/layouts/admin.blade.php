@php
    $adminUser = Auth::guard('admin')->user();
    $usercheck = $adminUser ? \App\Models\Usercheck::where('uid', $adminUser->user_id)->first() : null;
    $hasPermission = function ($permName) use ($adminUser, $usercheck) {
        if (!$adminUser) return false;
        if ($adminUser->section == 1) return true; // Super Admin bypass
        if (!$usercheck) return false;
        
        if ($permName === 'quot_or_estm') {
            return ($usercheck->quot == 1 || $usercheck->estm == 1);
        }
        if ($permName === 'mquot_or_mestm') {
            return ($usercheck->mquot == 1 || $usercheck->mestm == 1);
        }
        if ($permName === 'any_online') {
            return ($usercheck->ord == 1 || $usercheck->sord == 1 || $usercheck->dord == 1 || $usercheck->cord == 1);
        }
        
        return isset($usercheck->{$permName}) && $usercheck->{$permName} == 1;
    };
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
    <head>
        <script>
            // Anti-flash theme loader: read theme from localStorage or OS settings
            if (localStorage.getItem('color-theme') === 'dark' || (!('color-theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }

            // Admin surface mode: default to card mode across all admin pages
            var adminUiMode = localStorage.getItem('admin-ui-mode') || 'flat';
            document.documentElement.dataset.adminUiMode = adminUiMode === 'flat' ? 'flat' : 'card';
        </script>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title ?? 'MTL Mart Admin Dashboard' }}</title>

        <!-- Google Fonts: Inter & Outfit -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">

        <!-- FontAwesome Icons -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            [x-cloak] {
                display: none !important;
            }
            body {
                font-family: 'Inter', sans-serif;
            }
            h1, h2, h3, h4, h5, h6 {
                font-family: 'Outfit', sans-serif;
            }
            .glassmorphism {
                background: rgba(255, 255, 255, 0.7);
                backdrop-filter: blur(16px);
                border: 1px solid rgba(0, 0, 0, 0.06);
                box-shadow: 0 4px 30px rgba(0, 0, 0, 0.02);
            }
            .glassmorphism-hover:hover {
                background: rgba(255, 255, 255, 0.95);
                border-color: rgba(0, 0, 0, 0.1);
            }
            /* Custom scrollbar matching light mode */
            .scrollbar-thin::-webkit-scrollbar {
                width: 6px;
                height: 6px;
            }
            .scrollbar-thin::-webkit-scrollbar-track {
                background: transparent;
            }
            .scrollbar-thin::-webkit-scrollbar-thumb {
                background: #e2e8f0;
                border-radius: 3px;
            }
            .scrollbar-thin::-webkit-scrollbar-thumb:hover {
                background: #cbd5e1;
            }
            /* Dark-mode glassmorphism */
            .dark .glassmorphism {
                background: rgba(17, 24, 39, 0.65) !important;
                border-color: rgba(255, 255, 255, 0.08) !important;
                box-shadow: 0 4px 30px rgba(0, 0, 0, 0.3) !important;
            }
            .dark .glassmorphism-hover:hover {
                background: rgba(17, 24, 39, 0.85) !important;
                border-color: rgba(255, 255, 255, 0.12) !important;
            }
            /* Dark-mode scrollbars */
            .dark .scrollbar-thin::-webkit-scrollbar-thumb {
                background: #374151;
            }
            .dark .scrollbar-thin::-webkit-scrollbar-thumb:hover {
                background: #4b5563;
            }
            /* Touch-optimized horizontal table scrolling container */
            .responsive-table-container {
                width: 100%;
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }
            .admin-shell,
            .admin-sidebar,
            .admin-main,
            .admin-header,
            .admin-sidebar-header,
            .admin-sidebar-footer,
            .admin-sidebar-footer-inner,
            .admin-page-surface,
            .admin-page-surface-inner {
                transition: background-color 0.25s ease, color 0.25s ease, border-color 0.25s ease, box-shadow 0.25s ease;
            }
            html.dark .admin-shell {
                background: linear-gradient(180deg, #020617 0%, #0f172a 100%);
                color: #f8fafc;
            }
            html:not(.dark) .admin-shell {
                background:
                    radial-gradient(circle at top left, rgba(28, 63, 206, 0.08), transparent 28%),
                    radial-gradient(circle at top right, rgba(249, 115, 22, 0.06), transparent 24%),
                    linear-gradient(180deg, #f8fafc 0%, #eef2ff 100%);
                color: #0f172a;
            }
            html.dark .admin-sidebar {
                background: #0f172a;
                border-right: 1px solid #1f2937;
            }
            html:not(.dark) .admin-sidebar {
                background: rgba(255, 255, 255, 0.92);
                border-right: 1px solid rgba(148, 163, 184, 0.22);
                box-shadow: 0 20px 50px rgba(15, 23, 42, 0.08);
                backdrop-filter: blur(18px);
            }
            html.dark .admin-sidebar-header,
            html.dark .admin-sidebar-footer {
                background: #0f172a;
                border-color: #1f2937;
            }
            html:not(.dark) .admin-sidebar-header,
            html:not(.dark) .admin-sidebar-footer {
                background: rgba(255, 255, 255, 0.96);
                border-color: rgba(148, 163, 184, 0.18);
            }
            html:not(.dark) .admin-sidebar-footer-inner {
                background: rgba(248, 250, 252, 0.95);
            }
            html.dark .admin-main {
                background: #020617;
            }
            html:not(.dark) .admin-main {
                background: transparent;
            }
            html.dark .admin-header {
                background: rgba(15, 23, 42, 0.72);
                border-color: rgba(148, 163, 184, 0.14);
                color: #f8fafc;
            }
            html:not(.dark) .admin-header {
                background: rgba(255, 255, 255, 0.86);
                border-color: rgba(148, 163, 184, 0.18);
                color: #0f172a;
                box-shadow: 0 10px 30px rgba(15, 23, 42, 0.06);
            }
            html:not(.dark) .admin-page-surface {
                background: rgba(255, 255, 255, 0.82);
                border: 1px solid rgba(148, 163, 184, 0.16);
                box-shadow: 0 24px 60px rgba(15, 23, 42, 0.08);
                backdrop-filter: blur(16px);
            }
            html:not(.dark) .admin-page-surface-inner {
                color: #0f172a;
            }
            html.dark .admin-page-surface {
                background: rgba(15, 23, 42, 0.55);
                border: 1px solid rgba(148, 163, 184, 0.14);
                box-shadow: 0 24px 60px rgba(0, 0, 0, 0.22);
                backdrop-filter: blur(16px);
            }
            html.dark .admin-page-surface-inner {
                color: #f8fafc;
            }
            html:not(.dark) .admin-menu-backdrop {
                background: rgba(15, 23, 42, 0.35);
                backdrop-filter: blur(10px);
            }
            html.dark .admin-menu-backdrop {
                background: rgba(2, 6, 23, 0.8);
                backdrop-filter: blur(10px);
            }
            /* Legacy [class*=] overrides removed — now using proper Tailwind dark: variants */
            html[data-admin-ui-mode="card"] body {
                background:
                    radial-gradient(circle at top left, rgba(79, 70, 229, 0.10), transparent 28%),
                    radial-gradient(circle at top right, rgba(249, 115, 22, 0.07), transparent 24%),
                    linear-gradient(180deg, #020617 0%, #0f172a 100%);
            }
            html[data-admin-ui-mode="card"] .admin-page-surface {
                background: rgba(15, 23, 42, 0.55);
                border: 1px solid rgba(148, 163, 184, 0.14);
                border-radius: 2rem;
                box-shadow: 0 24px 60px rgba(0, 0, 0, 0.22);
                backdrop-filter: blur(16px);
            }
            html[data-admin-ui-mode="card"] .admin-page-surface-inner {
                padding: 1.5rem;
            }
            @media (min-width: 768px) {
                html[data-admin-ui-mode="card"] .admin-page-surface-inner {
                    padding: 2rem;
                }
            }
            html[data-admin-ui-mode="flat"] .admin-page-surface {
                background: transparent;
                border: 0;
                border-radius: 0;
                box-shadow: none;
                backdrop-filter: none;
            }
            html[data-admin-ui-mode="flat"] .admin-page-surface-inner {
                padding: 0;
            }
            /* Prevent input zoom-on-focus on iOS mobile safari by setting base size to 16px, scaling to 14px on sm viewports */
            @media (max-width: 639px) {
                input, select, textarea {
                    font-size: 16px !important;
                }
            }
        </style>
    </head>
    <body class="h-full font-sans antialiased overflow-x-hidden">
        <div class="admin-shell flex h-screen" x-data="{ sidebarOpen: false }">
            <!-- Mobile Sidebar Backdrop -->
            <div x-show="sidebarOpen" 
                 x-transition:enter="transition-opacity ease-linear duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition-opacity ease-linear duration-300"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 @click="sidebarOpen = false" 
                 class="admin-menu-backdrop fixed inset-0 z-40 lg:hidden">
            </div>

            <!-- Sidebar Navigation -->
            <aside class="admin-sidebar fixed inset-y-0 left-0 z-50 flex w-72 flex-col transition-transform duration-300 transform lg:translate-x-0 lg:static lg:inset-0"
                   :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">
                <!-- Logo Header -->
                <div class="admin-sidebar-header flex h-20 items-center justify-between px-6">
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2">
                        <img src="{{ asset('assets/logo.png') }}" class="h-auto max-w-32 dark:brightness-0 dark:invert transition-all" alt="MTL Mart Logo">
                    </a>
                    <button class="lg:hidden p-2 text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-100" @click="sidebarOpen = false">
                        <i class="fa-solid fa-xmark text-lg"></i>
                    </button>
                </div>

                <!-- Navigation Links -->
                <nav class="flex-1 space-y-2 px-4 py-6 overflow-y-auto scrollbar-thin">
                    
                    <!-- 1. Dashboard -->
                    <a href="{{ route('admin.dashboard') }}" 
                       class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('admin.dashboard') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/10' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-200/60 dark:hover:bg-slate-800/50 hover:text-slate-900 dark:hover:text-slate-100' }}">
                        <i class="fa-solid fa-chart-pie text-base"></i>
                        <span>Dashboard</span>
                    </a>

                    <!-- 2. Brand & Category Treeview -->
                    @if($hasPermission('cat') || $hasPermission('scat') || $hasPermission('brand'))
                    <div x-data="{ open: {{ request()->routeIs('admin.categories.*', 'admin.subcategories.*', 'admin.brands.*') ? 'true' : 'false' }} }" class="space-y-1">
                        <button @click="open = !open" 
                                class="w-full flex items-center justify-between px-3 py-2.5 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('admin.categories.*', 'admin.subcategories.*', 'admin.brands.*') ? 'bg-indigo-600/10 text-indigo-400 border border-indigo-500/20' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-200/60 dark:hover:bg-slate-800/50 hover:text-slate-900 dark:hover:text-slate-100' }} focus:outline-none">
                            <div class="flex items-center gap-3">
                                <i class="fa-solid fa-folder-open text-base"></i>
                                <span>Brand & Category</span>
                            </div>
                            <i class="fa-solid fa-chevron-down text-[10px] transition-transform duration-200" :class="open ? 'rotate-180' : ''"></i>
                        </button>
                        <div x-show="open" x-transition class="pl-6 space-y-1 border-l border-slate-300/60 dark:border-slate-800/60 ml-5 mt-1">
                            @if($hasPermission('cat'))
                            <a href="{{ route('admin.categories.index') }}" 
                               class="flex items-center gap-2 py-2 px-3 text-xs font-semibold uppercase tracking-wider rounded-lg transition-all {{ request()->routeIs('admin.categories.index') ? 'text-indigo-400 bg-indigo-500/5' : 'text-slate-500 hover:text-slate-700 dark:hover:text-slate-300 hover:bg-slate-200/40 dark:hover:bg-slate-800/30' }}">
                                <i class="fa-solid fa-chevron-right text-[8px] opacity-60"></i>
                                <span>Category</span>
                            </a>
                            @endif
                            @if($hasPermission('scat'))
                            <a href="{{ route('admin.subcategories.index') }}" 
                               class="flex items-center gap-2 py-2 px-3 text-xs font-semibold uppercase tracking-wider rounded-lg transition-all {{ request()->routeIs('admin.subcategories.index') ? 'text-indigo-400 bg-indigo-500/5' : 'text-slate-500 hover:text-slate-700 dark:hover:text-slate-300 hover:bg-slate-200/40 dark:hover:bg-slate-800/30' }}">
                                <i class="fa-solid fa-chevron-right text-[8px] opacity-60"></i>
                                <span>Sub Category</span>
                            </a>
                            @endif
                            @if($hasPermission('brand'))
                            <a href="{{ route('admin.brands.index') }}" 
                               class="flex items-center gap-2 py-2 px-3 text-xs font-semibold uppercase tracking-wider rounded-lg transition-all {{ request()->routeIs('admin.brands.index') ? 'text-indigo-400 bg-indigo-500/5' : 'text-slate-500 hover:text-slate-700 dark:hover:text-slate-300 hover:bg-slate-200/40 dark:hover:bg-slate-800/30' }}">
                                <i class="fa-solid fa-chevron-right text-[8px] opacity-60"></i>
                                <span>Brand</span>
                            </a>
                            @endif
                        </div>
                    </div>
                    @endif

                    <!-- 3. Product Treeview -->
                    @if($hasPermission('mprod') || $hasPermission('prod') || $hasPermission('slist') || $hasPermission('sprice'))
                    <div x-data="{ open: {{ request()->routeIs('admin.products.*') ? 'true' : 'false' }} }" class="space-y-1">
                        <button @click="open = !open" 
                                class="w-full flex items-center justify-between px-3 py-2.5 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('admin.products.*') ? 'bg-indigo-600/10 text-indigo-400 border border-indigo-500/20' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-200/60 dark:hover:bg-slate-800/50 hover:text-slate-900 dark:hover:text-slate-100' }} focus:outline-none">
                            <div class="flex items-center gap-3">
                                <i class="fa-solid fa-box text-base"></i>
                                <span>Product</span>
                            </div>
                            <i class="fa-solid fa-chevron-down text-[10px] transition-transform duration-200" :class="open ? 'rotate-180' : ''"></i>
                        </button>
                        <div x-show="open" x-transition class="pl-6 space-y-1 border-l border-slate-300/60 dark:border-slate-800/60 ml-5 mt-1">
                            @if($hasPermission('mprod'))
                            <a href="{{ route('admin.products.barcode') }}" 
                               class="flex items-center gap-2 py-2 px-3 text-xs font-semibold uppercase tracking-wider rounded-lg transition-all {{ request()->routeIs('admin.products.barcode') ? 'text-indigo-400 bg-indigo-500/5' : 'text-slate-500 hover:text-slate-300 hover:bg-slate-800/30' }}">
                                <i class="fa-solid fa-barcode text-[10px] opacity-60"></i>
                                <span>Product Barcode</span>
                            </a>
                            @endif
                            @if($hasPermission('prod'))
                            <a href="{{ route('admin.products.create') }}" 
                               class="flex items-center gap-2 py-2 px-3 text-xs font-semibold uppercase tracking-wider rounded-lg transition-all {{ request()->routeIs('admin.products.create') ? 'text-indigo-400 bg-indigo-500/5' : 'text-slate-500 hover:text-slate-300 hover:bg-slate-800/30' }}">
                                <i class="fa-solid fa-plus text-[10px] opacity-60"></i>
                                <span>Add Product</span>
                            </a>
                            @endif
                            @if($hasPermission('mprod'))
                            <a href="{{ route('admin.products.index') }}" 
                               class="flex items-center gap-2 py-2 px-3 text-xs font-semibold uppercase tracking-wider rounded-lg transition-all {{ request()->routeIs('admin.products.index', 'admin.products.edit') && !request()->input('action') ? 'text-indigo-400 bg-indigo-500/5' : 'text-slate-500 hover:text-slate-300 hover:bg-slate-800/30' }}">
                                <i class="fa-solid fa-pen-to-square text-[10px] opacity-60"></i>
                                <span>Manage Product</span>
                            </a>
                            @endif
                            @if($hasPermission('slist'))
                            <a href="{{ route('admin.products.stock_list') }}" 
                               class="flex items-center gap-2 py-2 px-3 text-xs font-semibold uppercase tracking-wider rounded-lg transition-all {{ request()->routeIs('admin.products.stock_list') ? 'text-indigo-400 bg-indigo-500/5' : 'text-slate-500 hover:text-slate-300 hover:bg-slate-800/30' }}">
                                <i class="fa-solid fa-list-check text-[10px] opacity-60"></i>
                                <span>Stock List</span>
                            </a>
                            @endif
                            @if($hasPermission('sprice'))
                            <a href="{{ route('admin.products.price_search') }}" 
                               class="flex items-center gap-2 py-2 px-3 text-xs font-semibold uppercase tracking-wider rounded-lg transition-all {{ request()->routeIs('admin.products.price_search') ? 'text-indigo-400 bg-indigo-500/5' : 'text-slate-500 hover:text-slate-300 hover:bg-slate-800/30' }}">
                                <i class="fa-solid fa-magnifying-glass text-[10px] opacity-60"></i>
                                <span>Price Search</span>
                            </a>
                            @endif
                        </div>
                    </div>
                    @endif

                    <!-- Purchases & Suppliers Treeview -->
                    @if($hasPermission('purc') || $hasPermission('mpurc'))
                    <div x-data="{ open: {{ request()->routeIs('admin.purchases.*', 'admin.suppliers.*') && !request()->routeIs('admin.purchases.stock.*', 'admin.purchases.batches.*', 'admin.purchases.delivery_notes.*') ? 'true' : 'false' }} }" class="space-y-1">
                        <button @click="open = !open" 
                                class="w-full flex items-center justify-between px-3 py-2.5 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('admin.purchases.*', 'admin.suppliers.*') && !request()->routeIs('admin.purchases.stock.*', 'admin.purchases.batches.*', 'admin.purchases.delivery_notes.*') ? 'bg-indigo-600/10 text-indigo-400 border border-indigo-500/20' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-200/60 dark:hover:bg-slate-800/50 hover:text-slate-900 dark:hover:text-slate-100' }} focus:outline-none">
                            <div class="flex items-center gap-3">
                                <i class="fa-solid fa-truck text-base"></i>
                                <span>Purchases & Suppliers</span>
                            </div>
                            <i class="fa-solid fa-chevron-down text-[10px] transition-transform duration-200" :class="open ? 'rotate-180' : ''"></i>
                        </button>
                        <div x-show="open" x-transition class="pl-6 space-y-1 border-l border-slate-300/60 dark:border-slate-800/60 ml-5 mt-1">
                            @if($hasPermission('purc'))
                            <a href="{{ route('admin.purchases.create') }}" 
                               class="flex items-center gap-2 py-2 px-3 text-xs font-semibold uppercase tracking-wider rounded-lg transition-all {{ request()->routeIs('admin.purchases.create') ? 'text-indigo-400 bg-indigo-500/5' : 'text-slate-500 hover:text-slate-300 hover:bg-slate-800/30' }}">
                                <i class="fa-solid fa-plus text-[10px] opacity-60"></i>
                                <span>Add Purchase</span>
                            </a>
                            @endif
                            @if($hasPermission('mpurc'))
                            <a href="{{ route('admin.purchases.index') }}" 
                               class="flex items-center gap-2 py-2 px-3 text-xs font-semibold uppercase tracking-wider rounded-lg transition-all {{ request()->routeIs('admin.purchases.index') && !request()->routeIs('admin.purchases.create') ? 'text-indigo-400 bg-indigo-500/5' : 'text-slate-500 hover:text-slate-300 hover:bg-slate-800/30' }}">
                                <i class="fa-solid fa-pen-to-square text-[10px] opacity-60"></i>
                                <span>Manage Purchase</span>
                            </a>
                            <a href="{{ route('admin.suppliers.index') }}" 
                               class="flex items-center gap-2 py-2 px-3 text-xs font-semibold uppercase tracking-wider rounded-lg transition-all {{ request()->routeIs('admin.suppliers.index') ? 'text-indigo-400 bg-indigo-500/5' : 'text-slate-500 hover:text-slate-300 hover:bg-slate-800/30' }}">
                                <i class="fa-solid fa-user-shield text-[10px] opacity-60"></i>
                                <span>Manage Suppliers</span>
                            </a>
                            @endif
                        </div>
                    </div>
                    @endif

                    <!-- New GRN / Inbound Treeview -->
                    @if($hasPermission('astock'))
                    <div x-data="{ open: {{ request()->routeIs('admin.purchases.stock.*', 'admin.purchases.batches.*', 'admin.purchases.delivery_notes.*') ? 'true' : 'false' }} }" class="space-y-1">
                        <button @click="open = !open" 
                                class="w-full flex items-center justify-between px-3 py-2.5 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('admin.purchases.stock.*', 'admin.purchases.batches.*', 'admin.purchases.delivery_notes.*') ? 'bg-indigo-600/10 text-indigo-400 border border-indigo-500/20' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-200/60 dark:hover:bg-slate-800/50 hover:text-slate-900 dark:hover:text-slate-100' }} focus:outline-none">
                            <div class="flex items-center gap-3">
                                <i class="fa-solid fa-truck-ramp-box text-base"></i>
                                <span>GRN & Inbound</span>
                            </div>
                            <i class="fa-solid fa-chevron-down text-[10px] transition-transform duration-200" :class="open ? 'rotate-180' : ''"></i>
                        </button>
                        <div x-show="open" x-transition class="pl-6 space-y-1 border-l border-slate-300/60 dark:border-slate-800/60 ml-5 mt-1">
                            <a href="{{ route('admin.purchases.stock.index') }}" 
                               class="flex items-center gap-2 py-2 px-3 text-xs font-semibold uppercase tracking-wider rounded-lg transition-all {{ request()->routeIs('admin.purchases.stock.*') ? 'text-indigo-400 bg-indigo-500/5' : 'text-slate-500 hover:text-slate-300 hover:bg-slate-800/30' }}">
                                <i class="fa-solid fa-plus text-[10px] opacity-60"></i>
                                <span>Post Inbound Stock</span>
                            </a>
                            <a href="{{ route('admin.purchases.batches.index') }}" 
                               class="flex items-center gap-2 py-2 px-3 text-xs font-semibold uppercase tracking-wider rounded-lg transition-all {{ request()->routeIs('admin.purchases.batches.*') ? 'text-indigo-400 bg-indigo-500/5' : 'text-slate-500 hover:text-slate-300 hover:bg-slate-800/30' }}">
                                <i class="fa-solid fa-boxes-stacked text-[10px] opacity-60"></i>
                                <span>Product Batches</span>
                            </a>
                            <a href="{{ route('admin.purchases.delivery_notes.index') }}" 
                               class="flex items-center gap-2 py-2 px-3 text-xs font-semibold uppercase tracking-wider rounded-lg transition-all {{ request()->routeIs('admin.purchases.delivery_notes.*') ? 'text-indigo-400 bg-indigo-500/5' : 'text-slate-500 hover:text-slate-300 hover:bg-slate-800/30' }}">
                                <i class="fa-solid fa-file-invoice text-[10px] opacity-60"></i>
                                <span>Delivery Notes Ledger</span>
                            </a>
                        </div>
                    </div>
                    @endif

                    <!-- 4. Offline Treeview -->
                    @if($hasPermission('cinv') || $hasPermission('minv') || $hasPermission('linvc') || $hasPermission('quot_or_estm') || $hasPermission('mquot_or_mestm'))
                    <div x-data="{ open: {{ request()->routeIs('admin.sales.*', 'admin.quotations.*', 'admin.stategst.*') ? 'true' : 'false' }} }" class="space-y-1">
                        <button @click="open = !open" 
                                class="w-full flex items-center justify-between px-3 py-2.5 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('admin.sales.*', 'admin.quotations.*', 'admin.stategst.*') ? 'bg-indigo-600/10 text-indigo-400 border border-indigo-500/20' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-200/60 dark:hover:bg-slate-800/50 hover:text-slate-900 dark:hover:text-slate-100' }} focus:outline-none">
                            <div class="flex items-center gap-3">
                                <i class="fa-solid fa-receipt text-base"></i>
                                <span>Offline</span>
                            </div>
                            <i class="fa-solid fa-chevron-down text-[10px] transition-transform duration-200" :class="open ? 'rotate-180' : ''"></i>
                        </button>
                        <div x-show="open" x-transition class="pl-6 space-y-1 border-l border-slate-300/60 dark:border-slate-800/60 ml-5 mt-1">
                            @if($hasPermission('cinv'))
                            <a href="{{ route('admin.stategst.index') }}" 
                               class="flex items-center gap-2 py-2 px-3 text-xs font-semibold uppercase tracking-wider rounded-lg transition-all {{ request()->routeIs('admin.stategst.*') ? 'text-indigo-400 bg-indigo-500/5' : 'text-slate-500 hover:text-slate-300 hover:bg-slate-800/30' }}">
                                <i class="fa-solid fa-percent text-[10px] opacity-60"></i>
                                <span>State GST</span>
                            </a>
                            @endif
                            @if($hasPermission('cinv'))
                            <a href="{{ route('admin.sales.create') }}" 
                               class="flex items-center gap-2 py-2 px-3 text-xs font-semibold uppercase tracking-wider rounded-lg transition-all {{ request()->routeIs('admin.sales.create') && !request()->input('from_quot') ? 'text-indigo-400 bg-indigo-500/5' : 'text-slate-500 hover:text-slate-300 hover:bg-slate-800/30' }}">
                                <i class="fa-solid fa-plus text-[10px] opacity-60"></i>
                                <span>Customer Invoice</span>
                            </a>
                            @endif
                            @if($hasPermission('minv'))
                            <a href="{{ route('admin.sales.index') }}" 
                               class="flex items-center gap-2 py-2 px-3 text-xs font-semibold uppercase tracking-wider rounded-lg transition-all {{ request()->routeIs('admin.sales.index') && !request()->input('view') ? 'text-indigo-400 bg-indigo-500/5' : 'text-slate-500 hover:text-slate-300 hover:bg-slate-800/30' }}">
                                <i class="fa-solid fa-edit text-[10px] opacity-60"></i>
                                <span>MANAGE CUSTOMER INVOICE</span>
                            </a>
                            @endif
                            @if($hasPermission('linvc'))
                            <a href="{{ route('admin.sales.index') }}?view=list" 
                               class="flex items-center gap-2 py-2 px-3 text-xs font-semibold uppercase tracking-wider rounded-lg transition-all {{ request()->routeIs('admin.sales.index') && request()->input('view') === 'list' ? 'text-indigo-400 bg-indigo-500/5' : 'text-slate-500 hover:text-slate-300 hover:bg-slate-800/30' }}">
                                <i class="fa-solid fa-list text-[10px] opacity-60"></i>
                                <span>CUSTOMER SALES ITEM LIST</span>
                            </a>
                            @endif
                            @if($hasPermission('quot_or_estm'))
                            <a href="{{ route('admin.quotations.create') }}" 
                               class="flex items-center gap-2 py-2 px-3 text-xs font-semibold uppercase tracking-wider rounded-lg transition-all {{ request()->routeIs('admin.quotations.create') ? 'text-indigo-400 bg-indigo-500/5' : 'text-slate-500 hover:text-slate-300 hover:bg-slate-800/30' }}">
                                <i class="fa-solid fa-plus text-[10px] opacity-60"></i>
                                <span>Quotation & Estimation</span>
                            </a>
                            @endif
                            @if($hasPermission('mquot_or_mestm'))
                            <a href="{{ route('admin.quotations.index') }}" 
                               class="flex items-center gap-2 py-2 px-3 text-xs font-semibold uppercase tracking-wider rounded-lg transition-all {{ request()->routeIs('admin.quotations.index') && !request()->routeIs('admin.quotations.create') ? 'text-indigo-400 bg-indigo-500/5' : 'text-slate-500 hover:text-slate-300 hover:bg-slate-800/30' }}">
                                <i class="fa-solid fa-edit text-[10px] opacity-60"></i>
                                <span>Quot & Est Manage</span>
                            </a>
                            @endif
                            @if($hasPermission('cinv'))
                            <a href="{{ route('admin.sales.create') }}?from_quot=1" 
                               class="flex items-center gap-2 py-2 px-3 text-xs font-semibold uppercase tracking-wider rounded-lg transition-all {{ request()->routeIs('admin.sales.create') && request()->input('from_quot') ? 'text-indigo-400 bg-indigo-500/5' : 'text-slate-500 hover:text-slate-300 hover:bg-slate-800/30' }}">
                                <i class="fa-solid fa-plus text-[10px] opacity-60"></i>
                                <span>Quot & Est to Invoice</span>
                            </a>
                            @endif
                            @if($hasPermission('minv'))
                            <a href="{{ route('admin.sales.index') }}?view=manage" 
                               class="flex items-center gap-2 py-2 px-3 text-xs font-semibold uppercase tracking-wider rounded-lg transition-all {{ request()->routeIs('admin.sales.index') && request()->input('view') === 'manage' ? 'text-indigo-400 bg-indigo-500/5' : 'text-slate-500 hover:text-slate-300 hover:bg-slate-800/30' }}">
                                <i class="fa-solid fa-edit text-[10px] opacity-60"></i>
                                <span>MANAGE INVOICE</span>
                            </a>
                            @endif
                        </div>
                    </div>
                    @endif

                    <!-- 5. Online Treeview -->
                    @if($hasPermission('ord') || $hasPermission('sord') || $hasPermission('dord') || $hasPermission('cord'))
                    <div x-data="{ open: {{ request()->routeIs('admin.online_orders.*') ? 'true' : 'false' }} }" class="space-y-1">
                        <button @click="open = !open" 
                                class="w-full flex items-center justify-between px-3 py-2.5 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('admin.online_orders.*') ? 'bg-indigo-600/10 text-indigo-400 border border-indigo-500/20' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-200/60 dark:hover:bg-slate-800/50 hover:text-slate-900 dark:hover:text-slate-100' }} focus:outline-none">
                            <div class="flex items-center gap-3">
                                <i class="fa-solid fa-globe text-base"></i>
                                <span>Online</span>
                            </div>
                            <i class="fa-solid fa-chevron-down text-[10px] transition-transform duration-200" :class="open ? 'rotate-180' : ''"></i>
                        </button>
                        <div x-show="open" x-transition class="pl-6 space-y-1 border-l border-slate-300/60 dark:border-slate-800/60 ml-5 mt-1">
                            @if($hasPermission('ord'))
                            <a href="{{ route('admin.online_orders.index', ['status' => 'all']) }}" 
                               class="flex items-center gap-2 py-2 px-3 text-xs font-semibold uppercase tracking-wider rounded-lg transition-all {{ request()->routeIs('admin.online_orders.index') && request()->input('status') === 'all' ? 'text-indigo-400 bg-indigo-500/5' : 'text-slate-500 hover:text-slate-300 hover:bg-slate-800/30' }}">
                                <i class="fa-solid fa-globe text-[10px] opacity-60"></i>
                                <span>All Orders</span>
                            </a>
                            <a href="{{ route('admin.online_orders.index', ['status' => 'pending']) }}" 
                               class="flex items-center gap-2 py-2 px-3 text-xs font-semibold uppercase tracking-wider rounded-lg transition-all {{ request()->routeIs('admin.online_orders.index') && request()->input('status', 'pending') === 'pending' ? 'text-indigo-400 bg-indigo-500/5' : 'text-slate-500 hover:text-slate-300 hover:bg-slate-800/30' }}">
                                <i class="fa-solid fa-clock text-[10px] opacity-60"></i>
                                <span>Pending Orders</span>
                            </a>
                            @endif
                            @if($hasPermission('sord'))
                            <a href="{{ route('admin.online_orders.index', ['status' => 'sending']) }}" 
                               class="flex items-center gap-2 py-2 px-3 text-xs font-semibold uppercase tracking-wider rounded-lg transition-all {{ request()->routeIs('admin.online_orders.index') && request()->input('status') === 'sending' ? 'text-indigo-400 bg-indigo-500/5' : 'text-slate-500 hover:text-slate-300 hover:bg-slate-800/30' }}">
                                <i class="fa-solid fa-list text-[10px] opacity-60"></i>
                                <span>Sending_Orders</span>
                            </a>
                            @endif
                            @if($hasPermission('dord'))
                            <a href="{{ route('admin.online_orders.index', ['status' => 'delivered']) }}" 
                               class="flex items-center gap-2 py-2 px-3 text-xs font-semibold uppercase tracking-wider rounded-lg transition-all {{ request()->routeIs('admin.online_orders.index') && request()->input('status') === 'delivered' ? 'text-indigo-400 bg-indigo-500/5' : 'text-slate-500 hover:text-slate-300 hover:bg-slate-800/30' }}">
                                <i class="fa-solid fa-road text-[10px] opacity-60"></i>
                                <span>Delivered_Orders</span>
                            </a>
                            @endif
                            @if($hasPermission('cord'))
                            <a href="{{ route('admin.online_orders.index', ['status' => 'cancelled']) }}" 
                               class="flex items-center gap-2 py-2 px-3 text-xs font-semibold uppercase tracking-wider rounded-lg transition-all {{ request()->routeIs('admin.online_orders.index') && request()->input('status') === 'cancelled' ? 'text-indigo-400 bg-indigo-500/5' : 'text-slate-500 hover:text-slate-300 hover:bg-slate-800/30' }}">
                                <i class="fa-solid fa-list text-[10px] opacity-60"></i>
                                <span>Cancel_Orders</span>
                            </a>
                            @endif
                        </div>
                    </div>
                    @endif

                    <!-- 6. Expenses Treeview -->
                    @if($hasPermission('expd') || $hasPermission('expen') || $hasPermission('agent') || $hasPermission('apay'))
                    <div x-data="{ open: {{ request()->routeIs('admin.expenses.categories.*', 'admin.expenses.*', 'admin.agents.*', 'admin.agents_payments.*') ? 'true' : 'false' }} }" class="space-y-1">
                        <button @click="open = !open" 
                                class="w-full flex items-center justify-between px-3 py-2.5 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('admin.expenses.categories.*', 'admin.expenses.*', 'admin.agents.*', 'admin.agents_payments.*') ? 'bg-indigo-600/10 text-indigo-400 border border-indigo-500/20' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-200/60 dark:hover:bg-slate-800/50 hover:text-slate-900 dark:hover:text-slate-100' }} focus:outline-none">
                            <div class="flex items-center gap-3">
                                <i class="fa-solid fa-coins text-base"></i>
                                <span>Expenses</span>
                            </div>
                            <i class="fa-solid fa-chevron-down text-[10px] transition-transform duration-200" :class="open ? 'rotate-180' : ''"></i>
                        </button>
                        <div x-show="open" x-transition class="pl-6 space-y-1 border-l border-slate-300/60 dark:border-slate-800/60 ml-5 mt-1">
                            @if($hasPermission('expd'))
                            <a href="{{ route('admin.expenses.categories.index') }}" 
                               class="flex items-center gap-2 py-2 px-3 text-xs font-semibold uppercase tracking-wider rounded-lg transition-all {{ request()->routeIs('admin.expenses.categories.index') ? 'text-indigo-400 bg-indigo-500/5' : 'text-slate-500 hover:text-slate-300 hover:bg-slate-800/30' }}">
                                <i class="fa-solid fa-plus text-[10px] opacity-60"></i>
                                <span>Expenses Name</span>
                            </a>
                            @endif
                            @if($hasPermission('expen'))
                            <a href="{{ route('admin.expenses.index') }}" 
                               class="flex items-center gap-2 py-2 px-3 text-xs font-semibold uppercase tracking-wider rounded-lg transition-all {{ request()->routeIs('admin.expenses.index') ? 'text-indigo-400 bg-indigo-500/5' : 'text-slate-500 hover:text-slate-300 hover:bg-slate-800/30' }}">
                                <i class="fa-solid fa-plus text-[10px] opacity-60"></i>
                                <span>Expenses</span>
                            </a>
                            @endif
                            @if($hasPermission('agent'))
                            <a href="{{ route('admin.agents.index') }}" 
                               class="flex items-center gap-2 py-2 px-3 text-xs font-semibold uppercase tracking-wider rounded-lg transition-all {{ request()->routeIs('admin.agents.*') ? 'text-indigo-400 bg-indigo-500/5' : 'text-slate-500 hover:text-slate-300 hover:bg-slate-800/30' }}">
                                <i class="fa-solid fa-plus text-[10px] opacity-60"></i>
                                <span>Agent</span>
                            </a>
                            @endif
                            @if($hasPermission('apay'))
                            <a href="{{ route('admin.agents_payments.index') }}" 
                               class="flex items-center gap-2 py-2 px-3 text-xs font-semibold uppercase tracking-wider rounded-lg transition-all {{ request()->routeIs('admin.agents_payments.*') ? 'text-indigo-400 bg-indigo-500/5' : 'text-slate-500 hover:text-slate-300 hover:bg-slate-800/30' }}">
                                <i class="fa-solid fa-plus text-[10px] opacity-60"></i>
                                <span>Add Payment</span>
                            </a>
                            @endif
                        </div>
                    </div>
                    @endif

                    <!-- 7. Report Treeview -->
                    @if($hasPermission('areport') || $hasPermission('breport') || $hasPermission('sreport') || $hasPermission('preport') || $hasPermission('stockr') || $hasPermission('phistory') || $hasPermission('excel'))
                    <div x-data="{ open: {{ request()->routeIs('admin.reports.*') ? 'true' : 'false' }} }" class="space-y-1">
                        <button @click="open = !open" 
                                class="w-full flex items-center justify-between px-3 py-2.5 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('admin.reports.*') ? 'bg-indigo-600/10 text-indigo-400 border border-indigo-500/20' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-200/60 dark:hover:bg-slate-800/50 hover:text-slate-900 dark:hover:text-slate-100' }} focus:outline-none">
                            <div class="flex items-center gap-3">
                                <i class="fa-solid fa-chart-line text-base"></i>
                                <span>Report</span>
                            </div>
                            <i class="fa-solid fa-chevron-down text-[10px] transition-transform duration-200" :class="open ? 'rotate-180' : ''"></i>
                        </button>
                        <div x-show="open" x-transition class="pl-6 space-y-1 border-l border-slate-300/60 dark:border-slate-800/60 ml-5 mt-1">
                            @if($hasPermission('areport'))
                            <a href="{{ route('admin.reports.index') }}" 
                               class="flex items-center gap-2 py-2 px-3 text-xs font-semibold uppercase tracking-wider rounded-lg transition-all {{ request()->routeIs('admin.reports.index') ? 'text-indigo-400 bg-indigo-500/5' : 'text-slate-500 hover:text-slate-300 hover:bg-slate-800/30' }}">
                                <i class="fa-solid fa-list text-[10px] opacity-60"></i>
                                <span>All Report</span>
                            </a>
                            @endif
                            @if($hasPermission('breport'))
                            <a href="{{ route('admin.reports.billwise') }}" 
                               class="flex items-center gap-2 py-2 px-3 text-xs font-semibold uppercase tracking-wider rounded-lg transition-all {{ request()->routeIs('admin.reports.billwise') ? 'text-indigo-400 bg-indigo-500/5' : 'text-slate-500 hover:text-slate-300 hover:bg-slate-800/30' }}">
                                <i class="fa-solid fa-envelope text-[10px] opacity-60"></i>
                                <span>Billwise Report</span>
                            </a>
                            @endif
                            @if($hasPermission('sreport'))
                            <a href="{{ route('admin.reports.sales') }}" 
                               class="flex items-center gap-2 py-2 px-3 text-xs font-semibold uppercase tracking-wider rounded-lg transition-all {{ request()->routeIs('admin.reports.sales') ? 'text-indigo-400 bg-indigo-500/5' : 'text-slate-500 hover:text-slate-300 hover:bg-slate-800/30' }}">
                                <i class="fa-solid fa-eject text-[10px] opacity-60"></i>
                                <span>Sales Report</span>
                            </a>
                            @endif
                            @if($hasPermission('preport'))
                             <a href="{{ route('admin.reports.pending') }}" 
                                class="flex items-center gap-2 py-2 px-3 text-xs font-semibold uppercase tracking-wider rounded-lg transition-all {{ request()->routeIs('admin.reports.pending') ? 'text-indigo-400 bg-indigo-500/5' : 'text-slate-500 hover:text-slate-300 hover:bg-slate-800/30' }}">
                                 <i class="fa-solid fa-download text-[10px] opacity-60"></i>
                                 <span>Pending Amount</span>
                             </a>
                            @endif
                            @if($hasPermission('stockr'))
                            <a href="{{ route('admin.reports.stock') }}" 
                               class="flex items-center gap-2 py-2 px-3 text-xs font-semibold uppercase tracking-wider rounded-lg transition-all {{ request()->routeIs('admin.reports.stock') ? 'text-indigo-400 bg-indigo-500/5' : 'text-slate-500 hover:text-slate-300 hover:bg-slate-800/30' }}">
                                <i class="fa-solid fa-briefcase text-[10px] opacity-60"></i>
                                <span>Stock</span>
                            </a>
                            @endif
                            @if($hasPermission('phistory'))
                            <a href="{{ route('admin.reports.payhistory') }}" 
                               class="flex items-center gap-2 py-2 px-3 text-xs font-semibold uppercase tracking-wider rounded-lg transition-all {{ request()->routeIs('admin.reports.payhistory') ? 'text-indigo-400 bg-indigo-500/5' : 'text-slate-500 hover:text-slate-300 hover:bg-slate-800/30' }}">
                                <i class="fa-solid fa-refresh text-[10px] opacity-60"></i>
                                <span>Pay History</span>
                            </a>
                            <a href="{{ route('admin.reports.supplier_ledger') }}" 
                               class="flex items-center gap-2 py-2 px-3 text-xs font-semibold uppercase tracking-wider rounded-lg transition-all {{ request()->routeIs('admin.reports.supplier_ledger') ? 'text-indigo-400 bg-indigo-500/5' : 'text-slate-500 hover:text-slate-300 hover:bg-slate-800/30' }}">
                                <i class="fa-solid fa-user-tie text-[10px] opacity-60"></i>
                                <span>Supplier Ledger</span>
                            </a>
                            @endif
                            @if($hasPermission('excel'))
                            <a href="{{ route('admin.reports.excel_panel') }}" 
                               class="flex items-center gap-2 py-2 px-3 text-xs font-semibold uppercase tracking-wider rounded-lg transition-all {{ request()->routeIs('admin.reports.excel_panel') ? 'text-indigo-400 bg-indigo-500/5' : 'text-slate-500 hover:text-slate-300 hover:bg-slate-800/30' }}">
                                <i class="fa-solid fa-list text-[10px] opacity-60"></i>
                                <span>Report to Excel</span>
                            </a>
                            @endif
                            @if(Auth::guard('admin')->user()->section == 1)
                                <a href="{{ route('admin.reports.pl') }}" 
                                   class="flex items-center gap-2 py-2 px-3 text-xs font-semibold uppercase tracking-wider rounded-lg transition-all {{ request()->routeIs('admin.reports.pl') ? 'text-indigo-400 bg-indigo-500/5' : 'text-slate-500 hover:text-slate-300 hover:bg-slate-800/30' }}">
                                    <i class="fa-solid fa-upload text-[10px] opacity-60"></i>
                                    <span>Profit & Loss</span>
                                </a>
                            @endif
                        </div>
                    </div>
                    @endif

                    <!-- 8. Setting Treeview -->
                    <div x-data="{ open: {{ request()->routeIs('admin.settings.*', 'admin.users.*', 'admin.usersettings.*', 'admin.customers.*', 'admin.backups.*') ? 'true' : 'false' }} }" class="space-y-1">
                        <button @click="open = !open" 
                                class="w-full flex items-center justify-between px-3 py-2.5 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('admin.settings.*', 'admin.users.*', 'admin.usersettings.*', 'admin.customers.*', 'admin.backups.*') ? 'bg-indigo-600/10 text-indigo-400 border border-indigo-500/20' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-200/60 dark:hover:bg-slate-800/50 hover:text-slate-900 dark:hover:text-slate-100' }} focus:outline-none">
                            <div class="flex items-center gap-3">
                                <i class="fa-solid fa-wrench text-base"></i>
                                <span>Setting</span>
                            </div>
                            <i class="fa-solid fa-chevron-down text-[10px] transition-transform duration-200" :class="open ? 'rotate-180' : ''"></i>
                        </button>
                        <div x-show="open" x-transition class="pl-6 space-y-1 border-l border-slate-300/60 dark:border-slate-800/60 ml-5 mt-1">
                            <a href="{{ route('admin.settings.index') }}" 
                               class="flex items-center gap-2 py-2 px-3 text-xs font-semibold uppercase tracking-wider rounded-lg transition-all {{ request()->routeIs('admin.settings.index') ? 'text-indigo-400 bg-indigo-500/5' : 'text-slate-500 hover:text-slate-300 hover:bg-slate-800/30' }}">
                                <i class="fa-solid fa-wrench text-[10px] opacity-60"></i>
                                <span>Setting Admin</span>
                            </a>
                            @if($hasPermission('auser'))
                            <a href="{{ route('admin.users.index') }}" 
                               class="flex items-center gap-2 py-2 px-3 text-xs font-semibold uppercase tracking-wider rounded-lg transition-all {{ request()->routeIs('admin.users.*') ? 'text-indigo-400 bg-indigo-500/5' : 'text-slate-500 hover:text-slate-300 hover:bg-slate-800/30' }}">
                                <i class="fa-solid fa-user text-[10px] opacity-60"></i>
                                <span>Add User</span>
                            </a>
                            @endif
                            @if($hasPermission('usett'))
                            <a href="{{ route('admin.usersettings.index') }}" 
                               class="flex items-center gap-2 py-2 px-3 text-xs font-semibold uppercase tracking-wider rounded-lg transition-all {{ request()->routeIs('admin.usersettings.*') ? 'text-indigo-400 bg-indigo-500/5' : 'text-slate-500 hover:text-slate-300 hover:bg-slate-800/30' }}">
                                <i class="fa-solid fa-wrench text-[10px] opacity-60"></i>
                                <span>User Setting</span>
                            </a>
                            @endif
                            @if($hasPermission('csett'))
                            <a href="{{ route('admin.customers.index') }}" 
                               class="flex items-center gap-2 py-2 px-3 text-xs font-semibold uppercase tracking-wider rounded-lg transition-all {{ request()->routeIs('admin.customers.*') ? 'text-indigo-400 bg-indigo-500/5' : 'text-slate-500 hover:text-slate-300 hover:bg-slate-800/30' }}">
                                <i class="fa-solid fa-user text-[10px] opacity-60"></i>
                                <span>Customer Setting</span>
                            </a>
                            @endif
                            @if($hasPermission('backup'))
                            <a href="{{ route('admin.backups.index') }}" 
                               class="flex items-center justify-between py-2 px-3 text-xs font-semibold uppercase tracking-wider rounded-lg transition-all {{ request()->routeIs('admin.backups.*') ? 'text-indigo-400 bg-indigo-500/5' : 'text-slate-500 hover:text-slate-300 hover:bg-slate-800/30' }}">
                                <span class="flex items-center gap-2">
                                    <i class="fa-solid fa-copy text-[10px] opacity-60"></i>
                                    <span>Backup Database</span>
                                </span>
                                <span class="text-[8px] bg-indigo-500/10 text-indigo-300 px-1.5 py-0.5 rounded">Secure</span>
                            </a>
                            @endif
                        </div>
                    </div>

                </nav>

                <!-- Footer / Profile Info -->
                <div class="admin-sidebar-footer p-4 border-t">
                    <div class="flex items-center gap-3 p-2 rounded-xl bg-slate-100 dark:bg-slate-950/50 admin-sidebar-footer-inner">
                        <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-gradient-to-tr from-indigo-500 to-purple-600 text-white font-bold text-lg shadow-md shadow-indigo-500/10">
                            {{ strtoupper(substr(Auth::guard('admin')->user()->username, 0, 1)) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <span class="block text-sm font-semibold text-slate-800 dark:text-slate-200 truncate">{{ Auth::guard('admin')->user()->username }}</span>
                            <span class="block text-xs text-slate-500 font-medium">
                                {{ Auth::guard('admin')->user()->section == 1 ? 'Super Admin' : 'Sales Executive' }}
                            </span>
                        </div>
                        <form method="POST" action="{{ route('admin.logout') }}">
                            @csrf
                            <button type="submit" class="p-2 text-slate-400 hover:text-rose-400 transition-all" title="Logout">
                                <i class="fa-solid fa-power-off text-base"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </aside>

            <!-- Main Content Area -->
            <div class="admin-main flex-1 flex flex-col min-w-0 overflow-y-auto">
                <!-- Top Navigation Bar -->
                <header class="admin-header flex h-20 items-center justify-between px-6 backdrop-blur-md border-b sticky top-0 z-30">
                    <!-- Menu Toggle -->
                    <button class="lg:hidden p-2 text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-100 transition-all" @click="sidebarOpen = true">
                        <i class="fa-solid fa-bars text-xl"></i>
                    </button>

                    <!-- Page Title / Quick Search -->
                    <div class="hidden sm:flex items-center gap-3">
                        <h2 class="text-lg font-bold text-slate-800 dark:text-slate-100">{{ $title ?? 'Administration Portal' }}</h2>
                    </div>

                    <!-- System Quick Info / Time & Badges -->
                    <div class="flex items-center gap-4">
                        <div class="hidden md:flex items-center gap-2 px-3 py-1.5 rounded-lg bg-slate-100 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 text-xs font-semibold text-indigo-600 dark:text-indigo-400 admin-clock">
                            <i class="fa-regular fa-clock"></i>
                            <span id="system-clock">{{ now('Asia/Kolkata')->format('d-m-Y h:i A') }}</span>
                        </div>



                        <!-- Theme Toggle Button -->
                        <button id="theme-toggle" type="button" 
                                class="flex items-center justify-center p-2 rounded-xl bg-slate-100 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 text-slate-500 dark:text-slate-400 hover:text-indigo-500 hover:border-indigo-500/20 transition-all duration-300 shadow-md shadow-slate-200/20 dark:shadow-slate-950/20"
                                title="Toggle color theme">
                            <i id="theme-toggle-dark-icon" class="fa-solid fa-moon text-sm hidden"></i>
                            <i id="theme-toggle-light-icon" class="fa-solid fa-sun text-sm hidden"></i>
                        </button>

                        <span class="px-3 py-1 text-xs font-bold rounded-full {{ Auth::guard('admin')->user()->section == 1 ? 'bg-indigo-500/10 text-indigo-400 border border-indigo-500/20' : 'bg-purple-500/10 text-purple-400 border border-purple-500/20' }}">
                            <i class="fa-solid {{ Auth::guard('admin')->user()->section == 1 ? 'fa-shield-halved' : 'fa-tag' }} mr-1.5"></i>
                            {{ Auth::guard('admin')->user()->section == 1 ? 'Super Admin Access' : 'Sales Partner Access' }}
                        </span>
                    </div>
                </header>

                    <!-- Page View -->
                    <main class="flex-1 p-4 md:p-6 max-w-[1600px] w-full mx-auto">
                <div class="admin-page-surface">
                    <div class="admin-page-surface-inner min-h-[calc(100vh-10rem)] flex flex-col gap-6">
                                <!-- Session Feedback -->
                                @if (session('success'))
                                    <div class="p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/20 flex items-start gap-3 shadow-lg shadow-emerald-500/5 animate-pulse">
                                        <i class="fa-solid fa-circle-check text-emerald-400 text-lg mt-0.5 animate-bounce"></i>
                                        <div>
                                            <span class="font-bold text-slate-800 dark:text-slate-200 block">Operation Successful</span>
                                            <span class="text-sm text-slate-600 dark:text-slate-400 block">{{ session('success') }}</span>
                                        </div>
                                    </div>
                                @endif

                                @if (session('error'))
                                    <div class="p-4 rounded-xl bg-rose-500/10 border border-rose-500/20 flex items-start gap-3 shadow-lg shadow-rose-500/5">
                                        <i class="fa-solid fa-triangle-exclamation text-rose-400 text-lg mt-0.5 animate-bounce"></i>
                                        <div>
                                            <span class="font-bold text-slate-800 dark:text-slate-200 block">System Alert</span>
                                            <span class="text-sm text-slate-600 dark:text-slate-400 block">{{ session('error') }}</span>
                                        </div>
                                    </div>
                                @endif

                                @yield('content')
                            </div>
                        </div>
                    </main>
            </div>
        </div>

        <script>
            // Live clock logic
            setInterval(() => {
                const clock = document.getElementById('system-clock');
                if (!clock) return;
                const now = new Date();
                const formatted = now.toLocaleDateString('en-GB').replace(/\//g, '-') + ' ' + 
                                  now.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', hour12: true });
                clock.innerText = formatted;
            }, 30000);

            // Theme Toggle Logic
            const themeToggleDarkIcon = document.getElementById('theme-toggle-dark-icon');
            const themeToggleLightIcon = document.getElementById('theme-toggle-light-icon');

            // Change the icons inside the button based on previous settings
            if (document.documentElement.classList.contains('dark')) {
                themeToggleLightIcon.classList.remove('hidden');
                themeToggleDarkIcon.classList.add('hidden');
            } else {
                themeToggleDarkIcon.classList.remove('hidden');
                themeToggleLightIcon.classList.add('hidden');
            }

            const themeToggleBtn = document.getElementById('theme-toggle');
            const uiModeToggleBtn = document.getElementById('ui-mode-toggle');
            const uiModeCardIcon = document.getElementById('ui-mode-card-icon');
            const uiModeFlatIcon = document.getElementById('ui-mode-flat-icon');

            function syncUiModeIcons() {
                if (!uiModeCardIcon || !uiModeFlatIcon) return;
                const mode = document.documentElement.dataset.adminUiMode === 'flat' ? 'flat' : 'card';
                uiModeCardIcon.classList.toggle('hidden', mode !== 'card');
                uiModeFlatIcon.classList.toggle('hidden', mode !== 'flat');
            }

            syncUiModeIcons();

            if (uiModeToggleBtn) {
                uiModeToggleBtn.addEventListener('click', function() {
                    const nextMode = document.documentElement.dataset.adminUiMode === 'card' ? 'flat' : 'card';
                    document.documentElement.dataset.adminUiMode = nextMode;
                    localStorage.setItem('admin-ui-mode', nextMode);
                    syncUiModeIcons();
                });
            }

            function syncThemeIcons() {
                if (!themeToggleDarkIcon || !themeToggleLightIcon) return;
                if (document.documentElement.classList.contains('dark')) {
                    themeToggleLightIcon.classList.remove('hidden');
                    themeToggleDarkIcon.classList.add('hidden');
                } else {
                    themeToggleDarkIcon.classList.remove('hidden');
                    themeToggleLightIcon.classList.add('hidden');
                }
            }

            syncThemeIcons();

            if (themeToggleBtn) themeToggleBtn.addEventListener('click', function() {
                // toggle icons inside button
                // if set via local storage previously
                if (localStorage.getItem('color-theme')) {
                    if (localStorage.getItem('color-theme') === 'light') {
                        document.documentElement.classList.add('dark');
                        localStorage.setItem('color-theme', 'dark');
                    } else {
                        document.documentElement.classList.remove('dark');
                        localStorage.setItem('color-theme', 'light');
                    }
                // if not set via local storage previously
                } else {
                    if (document.documentElement.classList.contains('dark')) {
                        document.documentElement.classList.remove('dark');
                        localStorage.setItem('color-theme', 'light');
                    } else {
                        document.documentElement.classList.add('dark');
                        localStorage.setItem('color-theme', 'dark');
                    }
                }
                syncThemeIcons();
            });

            // ── Modal helpers: ESC close + body scroll lock ──
            // Watches for any Alpine x-show modal overlay (.fixed.inset-0.z-50)
            const observer = new MutationObserver(function() {
                const anyOpen = document.querySelector('.fixed.inset-0.z-50:not([style*="display: none"])');
                if (anyOpen) {
                    document.body.classList.add('modal-open');
                } else {
                    document.body.classList.remove('modal-open');
                }
            });
            observer.observe(document.body, { childList: true, subtree: true, attributes: true, attributeFilter: ['style'] });

            // Global ESC key to close the topmost Alpine modal
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    // Find all visible modal overlays
                    document.querySelectorAll('.fixed.inset-0.z-50:not([style*="display: none"])').forEach(function(el) {
                        // Dispatch click on backdrop to trigger Alpine @click.outside
                        if (typeof Alpine !== 'undefined' && Alpine.$data) {
                            const alpineData = Alpine.$data(el.closest('[x-data]'));
                            if (alpineData) {
                                // Find which modal boolean is true and close it
                                Object.keys(alpineData).forEach(function(key) {
                                    if ((key.toLowerCase().includes('modal') || key.toLowerCase().includes('search')) && alpineData[key] === true) {
                                        alpineData[key] = false;
                                    }
                                });
                            }
                        }
                    });
                }
            });
        </script>
    </body>
</html>
