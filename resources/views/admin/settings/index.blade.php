@extends('layouts.admin')

@section('content')
<div class="space-y-8 animate-fadeIn">
    <!-- Header Section -->
    <x-admin.header 
        title="Setting Admin" 
        description="Manage your administrative account credentials, contact information, and security preferences. (ref: admin/setting.php)" 
        glass="false">
    </x-admin.header>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        
        <!-- Form 1: Profile Information -->
        <div class="bg-slate-900/40 backdrop-blur-md border border-slate-800/80 rounded-2xl p-6 lg:p-8 shadow-xl space-y-6 flex flex-col justify-between">
            <div>
                <div class="flex items-center gap-3 pb-4 border-b border-slate-800/60">
                    <div class="p-3 bg-orange-500/10 text-orange-400 rounded-xl">
                        <i class="fa-solid fa-user-gear text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-slate-100">Profile Information</h3>
                        <p class="text-xs text-slate-400">Update your administrator details and contact options.</p>
                    </div>
                </div>

                <form method="POST" action="{{ route('admin.settings.username') }}" class="mt-6 space-y-5">
                    @csrf

                    <!-- Username Input -->
                    <div class="space-y-2">
                        <label for="username" class="block text-xs font-semibold text-slate-400 uppercase tracking-wider">Username</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-slate-500">
                                <i class="fa-solid fa-user text-sm"></i>
                            </span>
                            <input type="text" 
                                   id="username" 
                                   name="username" 
                                   required 
                                   value="{{ old('username', $user->username) }}"
                                   placeholder="Enter username"
                                   class="w-full bg-slate-950/60 border border-slate-800 rounded-xl pl-11 pr-4 py-3.5 text-sm text-slate-200 placeholder-slate-600 focus:outline-none focus:ring-1 focus:ring-orange-500 focus:border-orange-500/60 transition-all @error('username') border-rose-500/50 focus:ring-rose-500 @enderror">
                        </div>
                        @error('username')
                            <p class="text-xs font-medium text-rose-400 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Mobile Input -->
                    <div class="space-y-2">
                        <label for="mobile" class="block text-xs font-semibold text-slate-400 uppercase tracking-wider">Mobile Number</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-slate-500">
                                <i class="fa-solid fa-phone text-sm"></i>
                            </span>
                            <input type="text" 
                                   id="mobile" 
                                   name="mobile" 
                                   required 
                                   value="{{ old('mobile', $user->mobile) }}"
                                   placeholder="Enter mobile number"
                                   class="w-full bg-slate-950/60 border border-slate-800 rounded-xl pl-11 pr-4 py-3.5 text-sm text-slate-200 placeholder-slate-600 focus:outline-none focus:ring-1 focus:ring-orange-500 focus:border-orange-500/60 transition-all @error('mobile') border-rose-500/50 focus:ring-rose-500 @enderror">
                        </div>
                        @error('mobile')
                            <p class="text-xs font-medium text-rose-400 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email Input -->
                    <div class="space-y-2">
                        <label for="email" class="block text-xs font-semibold text-slate-400 uppercase tracking-wider">Email Address</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-slate-500">
                                <i class="fa-solid fa-envelope text-sm"></i>
                            </span>
                            <input type="email" 
                                   id="email" 
                                   name="email" 
                                   required 
                                   value="{{ old('email', $user->email) }}"
                                   placeholder="Enter email address"
                                   class="w-full bg-slate-950/60 border border-slate-800 rounded-xl pl-11 pr-4 py-3.5 text-sm text-slate-200 placeholder-slate-600 focus:outline-none focus:ring-1 focus:ring-orange-500 focus:border-orange-500/60 transition-all @error('email') border-rose-500/50 focus:ring-rose-500 @enderror">
                        </div>
                        @error('email')
                            <p class="text-xs font-medium text-rose-400 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="pt-4 border-t border-slate-800/40">
                        <button type="submit" class="w-full flex items-center justify-center gap-2 py-3.5 px-6 rounded-xl bg-orange-600 hover:bg-orange-700 text-white font-bold text-sm shadow-xl shadow-orange-600/10 hover:shadow-orange-600/25 transition-all">
                            <i class="fa-solid fa-circle-check"></i>
                            Save Profile Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Form 2: Security Credentials -->
        <div class="bg-slate-900/40 backdrop-blur-md border border-slate-800/80 rounded-2xl p-6 lg:p-8 shadow-xl space-y-6 flex flex-col justify-between">
            <div>
                <div class="flex items-center gap-3 pb-4 border-b border-slate-800/60">
                    <div class="p-3 bg-indigo-500/10 text-indigo-400 rounded-xl">
                        <i class="fa-solid fa-shield-halved text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-slate-100">Security Credentials</h3>
                        <p class="text-xs text-slate-400">Change your account password securely using default encryption.</p>
                    </div>
                </div>

                <form method="POST" action="{{ route('admin.settings.password') }}" class="mt-6 space-y-5">
                    @csrf

                    <!-- Current Password Input -->
                    <div class="space-y-2">
                        <label for="current_password" class="block text-xs font-semibold text-slate-400 uppercase tracking-wider">Current Password</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-slate-500">
                                <i class="fa-solid fa-lock text-sm"></i>
                            </span>
                            <input type="password" 
                                   id="current_password" 
                                   name="current_password" 
                                   required 
                                   placeholder="Enter current password"
                                   class="w-full bg-slate-950/60 border border-slate-800 rounded-xl pl-11 pr-4 py-3.5 text-sm text-slate-200 placeholder-slate-600 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500/60 transition-all @error('current_password') border-rose-500/50 focus:ring-rose-500 @enderror">
                        </div>
                        @error('current_password')
                            <p class="text-xs font-medium text-rose-400 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- New Password Input -->
                    <div class="space-y-2">
                        <label for="password" class="block text-xs font-semibold text-slate-400 uppercase tracking-wider">New Password</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-slate-500">
                                <i class="fa-solid fa-key text-sm"></i>
                            </span>
                            <input type="password" 
                                   id="password" 
                                   name="password" 
                                   required 
                                   placeholder="Enter new password"
                                   class="w-full bg-slate-950/60 border border-slate-800 rounded-xl pl-11 pr-4 py-3.5 text-sm text-slate-200 placeholder-slate-600 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500/60 transition-all @error('password') border-rose-500/50 focus:ring-rose-500 @enderror">
                        </div>
                        @error('password')
                            <p class="text-xs font-medium text-rose-400 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Confirm Password Input -->
                    <div class="space-y-2">
                        <label for="password_confirmation" class="block text-xs font-semibold text-slate-400 uppercase tracking-wider">Confirm Password</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-slate-500">
                                <i class="fa-solid fa-circle-check text-sm"></i>
                            </span>
                            <input type="password" 
                                   id="password_confirmation" 
                                   name="password_confirmation" 
                                   required 
                                   placeholder="Confirm new password"
                                   class="w-full bg-slate-950/60 border border-slate-800 rounded-xl pl-11 pr-4 py-3.5 text-sm text-slate-200 placeholder-slate-600 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500/60 transition-all">
                        </div>
                    </div>

                    <div class="pt-4 border-t border-slate-800/40">
                        <button type="submit" class="w-full flex items-center justify-center gap-2 py-3.5 px-6 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-sm shadow-xl shadow-indigo-600/10 hover:shadow-indigo-600/25 transition-all">
                            <i class="fa-solid fa-lock-open"></i>
                            Update Password Security
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>
@endsection
