<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Customer Login — MTL Mart</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

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

    <style>
        body { font-family: 'Inter', sans-serif; }
        h1, h2, h3, h4, h5, h6 { font-family: 'Outfit', sans-serif; }
        
        .auth-gradient {
            background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 50%, #0c0a09 100%);
        }
        html.light .auth-gradient {
            background: linear-gradient(135deg, #1c3fce 0%, #2563eb 60%, #1d4ed8 100%);
        }
        
        body, input, button {
            transition: background-color 0.25s ease, border-color 0.25s ease, color 0.25s ease;
        }
    </style>
</head>
<body class="h-full bg-slate-50 dark:bg-[#090e17] text-slate-900 dark:text-slate-100 flex overflow-hidden">

    <!-- ── Dynamic Responsive Login Split Layout ── -->
    <div class="w-full h-full flex flex-col md:flex-row">
        
        <!-- ── Left Visual Panel (Desktop Only) ── -->
        <div class="hidden md:flex md:w-1/2 lg:w-3/5 h-full auth-gradient flex-col justify-between p-12 lg:p-16 relative overflow-hidden">
            <!-- Neon Orb Background Elements -->
            <div class="absolute -right-24 -top-24 w-96 h-96 bg-blue-500/10 dark:bg-purple-500/10 rounded-full blur-3xl animate-pulse pointer-events-none"></div>
            <div class="absolute -left-24 -bottom-24 w-96 h-96 bg-indigo-500/10 dark:bg-indigo-500/20 rounded-full blur-3xl animate-pulse pointer-events-none" style="animation-delay: 2s;"></div>

            <!-- Brand Logo Header -->
            <a href="{{ route('storefront.index') }}" class="flex items-center gap-3 relative z-10 self-start group">
                <div class="w-11 h-11 rounded-xl bg-white flex items-center justify-center shadow-lg group-hover:scale-105 transition-transform">
                    <span class="font-black text-blue-600 text-xl font-['Outfit']">M</span>
                </div>
                <div>
                    <div class="text-xl font-black tracking-tight text-white leading-none">MTL Mart</div>
                    <div class="text-[9px] font-black uppercase tracking-widest text-orange-400 dark:text-indigo-300">Computer Garden</div>
                </div>
            </a>

            <!-- Creative Selling Proposition -->
            <div class="space-y-6 max-w-lg relative z-10 my-auto">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-black bg-white/10 text-white uppercase tracking-widest border border-white/20">
                    <i class="fa-solid fa-lock text-[9px] text-orange-400"></i> Secure Customer Portal
                </span>
                
                <h2 class="text-3xl lg:text-5xl font-black text-white leading-tight uppercase font-outfit">
                    PREMIUM HARDWARE,<br>
                    <span class="text-orange-400 dark:text-indigo-300">STREAMLINED TRANSACTIONS.</span>
                </h2>
                
                <p class="text-xs lg:text-sm text-slate-200/90 leading-relaxed font-medium">
                    Log in to unlock custom checkout channels, track express dispatch statuses, print compliant tax invoices, and access exclusive partner catalogs.
                </p>

                <!-- Key benefits grid -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-6">
                    <div class="p-4 rounded-xl bg-white/5 border border-white/10 backdrop-blur-sm">
                        <i class="fa-solid fa-truck-fast text-orange-400 mb-2.5 text-base"></i>
                        <h4 class="text-[10px] font-black uppercase text-white tracking-wider">Fast Courier</h4>
                        <p class="text-[9px] text-slate-300 leading-snug mt-1 font-medium">Priority shipping across regions.</p>
                    </div>
                    <div class="p-4 rounded-xl bg-white/5 border border-white/10 backdrop-blur-sm">
                        <i class="fa-solid fa-file-invoice-dollar text-orange-400 mb-2.5 text-base"></i>
                        <h4 class="text-[10px] font-black uppercase text-white tracking-wider">GST Inputs</h4>
                        <p class="text-[9px] text-slate-300 leading-snug mt-1 font-medium">Seamless B2B billing records.</p>
                    </div>
                    <div class="p-4 rounded-xl bg-white/5 border border-white/10 backdrop-blur-sm">
                        <i class="fa-solid fa-headset text-orange-400 mb-2.5 text-base"></i>
                        <h4 class="text-[10px] font-black uppercase text-white tracking-wider">Advisor Care</h4>
                        <p class="text-[9px] text-slate-300 leading-snug mt-1 font-medium">Direct live telephone support.</p>
                    </div>
                </div>
            </div>

            <!-- Footer Meta -->
            <div class="text-[10px] text-slate-300 relative z-10 flex items-center gap-4">
                <span>&copy; {{ date('Y') }} MTL Computer Garden</span>
                <span class="w-1.5 h-1.5 bg-emerald-400 rounded-full animate-pulse"></span>
                <span>Secure SSL Integration</span>
            </div>
        </div>

        <!-- ── Right Form Panel (Universal) ── -->
        <div class="w-full md:w-1/2 lg:w-2/5 h-full flex flex-col justify-between p-6 sm:p-10 lg:p-14 overflow-y-auto bg-white dark:bg-slate-900 border-l border-slate-100 dark:border-slate-850">
            <!-- Mobile Brand Header -->
            <div class="flex items-center justify-between md:hidden mb-10 pb-4 border-b border-slate-100 dark:border-slate-800">
                <a href="{{ route('storefront.index') }}" class="flex items-center gap-2 group">
                    <div class="w-8 h-8 rounded-lg bg-blue-600 flex items-center justify-center shadow shadow-blue-500/20">
                        <span class="font-black text-white text-sm font-['Outfit']">M</span>
                    </div>
                    <span class="text-base font-black tracking-tight text-slate-900 dark:text-white font-outfit uppercase">MTL Mart</span>
                </a>
                
                <!-- Simple Mode Toggle -->
                <button onclick="window.sfToggleAuthTheme()" class="w-8 h-8 rounded-lg bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-650 dark:text-slate-400 flex items-center justify-center cursor-pointer">
                    <i class="fa-solid fa-circle-half-stroke text-xs"></i>
                </button>
            </div>

            <div class="my-auto max-w-[360px] w-full mx-auto space-y-7">
                <!-- Title Header -->
                <div>
                    <h1 class="text-2xl font-black uppercase text-slate-900 dark:text-white tracking-tight">Access Customer Account</h1>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Please enter your verified email and security credentials</p>
                </div>

                <!-- Session Status (Custom Layout) -->
                @if (session('status'))
                    <div class="p-3.5 rounded-xl text-xs bg-emerald-500/10 border border-emerald-500/20 text-emerald-600 dark:text-emerald-400 font-medium">
                        {{ session('status') }}
                    </div>
                @endif

                <!-- Validation Errors (Custom Layout) -->
                @if ($errors->any())
                    <div class="p-3.5 rounded-xl text-xs bg-rose-500/10 border border-rose-500/20 text-rose-600 dark:text-rose-400 space-y-1">
                        <div class="font-extrabold uppercase tracking-wider text-[10px]">Verification Failed:</div>
                        <ul class="list-disc list-inside text-[11px] space-y-0.5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Form Block -->
                <form method="POST" action="{{ route('login') }}" class="space-y-4">
                    @csrf

                    <!-- Email Input -->
                    <div class="space-y-1.5">
                        <label for="email" class="block text-[10px] font-black uppercase tracking-wider text-slate-500 dark:text-slate-400">Email Address</label>
                        <div class="relative rounded-xl shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400 dark:text-slate-500">
                                <i class="fa-solid fa-envelope text-xs"></i>
                            </div>
                            <input id="email" 
                                   type="email" 
                                   name="email" 
                                   value="{{ old('email') }}" 
                                   required 
                                   autofocus 
                                   placeholder="customer@domain.com"
                                   class="block w-full pl-9 pr-4 py-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs font-semibold outline-none transition-all focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                        </div>
                    </div>

                    <!-- Password Input -->
                    <div class="space-y-1.5">
                        <div class="flex items-center justify-between">
                            <label for="password" class="block text-[10px] font-black uppercase tracking-wider text-slate-500 dark:text-slate-400">Security Password</label>
                            @if (Route::has('password.request'))
                                <a class="text-[10px] font-bold text-blue-600 dark:text-indigo-400 hover:underline" href="{{ route('password.request') }}">
                                    Forgot Password?
                                </a>
                            @endif
                        </div>
                        <div class="relative rounded-xl shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400 dark:text-slate-500">
                                <i class="fa-solid fa-lock text-xs"></i>
                            </div>
                            <input id="password" 
                                   type="password" 
                                   name="password" 
                                   required 
                                   placeholder="••••••••••••"
                                   class="block w-full pl-9 pr-4 py-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs font-semibold outline-none transition-all focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                        </div>
                    </div>

                    <!-- Remember Me Option -->
                    <div class="flex items-center justify-between pt-1">
                        <label for="remember_me" class="inline-flex items-center cursor-pointer">
                            <input id="remember_me" 
                                   type="checkbox" 
                                   name="remember"
                                   class="w-4 h-4 rounded border-slate-350 dark:border-slate-700 text-blue-600 focus:ring-blue-500/20 focus:ring-offset-0 bg-slate-50 dark:bg-slate-950 cursor-pointer">
                            <span class="ml-2 text-[11px] font-semibold text-slate-500 dark:text-slate-400">Keep me logged in</span>
                        </label>
                    </div>

                    <!-- CTA Action Button -->
                    <button type="submit" class="w-full py-3 mt-2 bg-gradient-to-r from-blue-600 to-indigo-700 hover:from-blue-500 hover:to-indigo-600 text-white rounded-xl text-xs font-black uppercase tracking-widest shadow-lg shadow-blue-500/10 active:scale-[0.98] transition-all cursor-pointer">
                        Secure Sign In
                    </button>
                </form>

                <!-- Registration Disabled Notice -->
                <div class="text-center pt-3 border-t border-slate-100 dark:border-slate-800 text-[11px] space-y-2">
                    <div>
                        <span class="text-slate-400 dark:text-slate-500">New hardware customer?</span>
                        <span class="ml-1 inline-flex items-center gap-1 px-2 py-0.5 rounded-full border border-slate-200 dark:border-slate-700 text-slate-500 dark:text-slate-400 font-extrabold uppercase tracking-wider bg-slate-50 dark:bg-slate-950/60">
                            Create an Account
                            <i class="fa-solid fa-lock text-[9px]"></i>
                        </span>
                    </div>
                    <p class="text-[10px] text-slate-400 dark:text-slate-500 leading-relaxed">
                        Account creation is temporarily disabled and will be enabled in a future update.
                    </p>
                </div>
            </div>

            <!-- Standalone Layout Desktop Switch -->
            <div class="hidden md:flex items-center justify-between text-[10px] text-slate-450 pt-10 border-t border-slate-100 dark:border-slate-850">
                <a href="{{ route('storefront.index') }}" class="hover:text-blue-500 dark:hover:text-indigo-400 transition-colors font-bold uppercase"><i class="fa-solid fa-arrow-left mr-1"></i> Back to Store</a>
                
                <!-- Desktop mode switch button -->
                <button onclick="window.sfToggleAuthTheme()" class="flex items-center gap-1.5 px-2.5 py-1 rounded-lg border border-slate-200 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-800 cursor-pointer transition-all">
                    <i class="fa-solid fa-circle-half-stroke text-[10px]"></i>
                    <span class="font-extrabold uppercase text-[9px] tracking-wider">Switch Mode</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Theme Toggling Logic Standalone -->
    <script>
        window.sfToggleAuthTheme = function() {
            var html = document.documentElement;
            var isDark = html.classList.contains('dark');
            if (isDark) {
                html.classList.remove('dark'); html.classList.add('light');
                localStorage.setItem('sf-theme', 'light');
            } else {
                html.classList.remove('light'); html.classList.add('dark');
                localStorage.setItem('sf-theme', 'dark');
            }
        };
    </script>
</body>
</html>
