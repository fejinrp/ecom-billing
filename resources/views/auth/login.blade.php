@extends('layouts.storefront')

@section('content')
<div class="min-h-[70vh] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8 bg-white dark:bg-slate-900 p-8 sm:p-10 rounded-3xl border border-slate-100 dark:border-slate-800 shadow-xl transition-all duration-300">
        
        <!-- Header -->
        <div class="text-center">
            <h2 class="text-3xl font-black tracking-tight text-slate-900 dark:text-white uppercase font-outfit">
                Welcome Back
            </h2>
            <p class="mt-2 text-xs text-slate-500 dark:text-slate-400">
                Please enter your verified credentials to access your customer account
            </p>
        </div>

        <!-- Session Status -->
        @if (session('status'))
            <div class="p-4 rounded-2xl text-xs bg-emerald-500/10 border border-emerald-500/20 text-emerald-600 dark:text-emerald-400 font-medium">
                {{ session('status') }}
            </div>
        @endif

        <!-- Validation Errors -->
        @if ($errors->any())
            <div class="p-4 rounded-2xl text-xs bg-rose-500/10 border border-rose-500/20 text-rose-600 dark:text-rose-400 space-y-1">
                <div class="font-extrabold uppercase tracking-wider text-[10px]">Verification Failed:</div>
                <ul class="list-disc list-inside text-[11px] space-y-0.5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Form Block -->
        <form method="POST" action="{{ route('login') }}" class="mt-8 space-y-6">
            @csrf

            <!-- Email Input -->
            <div class="space-y-1.5">
                <label for="email" class="block text-[10px] font-black uppercase tracking-wider text-slate-500 dark:text-slate-400">
                    Email Address
                </label>
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
                           class="block w-full pl-9 pr-4 py-3 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs font-semibold outline-none transition-all focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 text-slate-900 dark:text-slate-100">
                </div>
            </div>

            <!-- Password Input -->
            <div class="space-y-1.5">
                <div class="flex items-center justify-between">
                    <label for="password" class="block text-[10px] font-black uppercase tracking-wider text-slate-500 dark:text-slate-400">
                        Security Password
                    </label>
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
                           class="block w-full pl-9 pr-4 py-3 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs font-semibold outline-none transition-all focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 text-slate-900 dark:text-slate-100">
                </div>
            </div>

            <!-- Remember Me -->
            <div class="flex items-center justify-between pt-1">
                <label for="remember_me" class="inline-flex items-center cursor-pointer">
                    <input id="remember_me" 
                           type="checkbox" 
                           name="remember"
                           class="w-4 h-4 rounded border-slate-300 dark:border-slate-700 text-blue-600 focus:ring-blue-500/20 focus:ring-offset-0 bg-slate-50 dark:bg-slate-950 cursor-pointer">
                    <span class="ml-2 text-[11px] font-semibold text-slate-500 dark:text-slate-400">Keep me logged in</span>
                </label>
            </div>

            <!-- CTA Action Button -->
            <button type="submit" class="w-full py-3.5 bg-gradient-to-r from-blue-600 to-indigo-700 hover:from-blue-500 hover:to-indigo-600 text-white rounded-xl text-xs font-black uppercase tracking-widest shadow-lg shadow-blue-500/10 active:scale-[0.98] transition-all cursor-pointer">
                Secure Sign In
            </button>
        </form>

        <!-- Footer -->
        <div class="text-center pt-6 border-t border-slate-100 dark:border-slate-800 text-[11px] space-y-2">
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
</div>
@endsection
