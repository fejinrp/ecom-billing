@extends('layouts.storefront')

@section('title', 'Lucky Draw — Status Board')

@section('content')

{{-- ── Hero Banner ─────────────────────────────────────────────────────────── --}}
<div class="relative overflow-hidden rounded-[2rem] mb-10"
     style="background: linear-gradient(135deg, #0059e3 0%, #7c3aed 60%, #f59e0b 100%);">
    <div class="relative z-10 px-8 py-12 text-white">
        <div class="flex items-center gap-2 text-[10px] font-black uppercase tracking-[0.35em] text-white/60 mb-4">
            <a href="{{ route('storefront.index') }}" class="hover:text-white transition-colors">Home</a>
            <i class="fa-solid fa-chevron-right text-[8px]"></i>
            <span>Lucky Draw</span>
        </div>
        <h1 class="text-4xl sm:text-5xl font-black uppercase tracking-tight mb-2">🎁 Lucky Draw</h1>
        <p class="text-base text-white/75 max-w-xl leading-relaxed">
            Every qualifying purchase earns you a chance to win! Track live pool progress, see current winners, and check your personal eligibility below.
        </p>
        <div class="mt-6 flex flex-wrap gap-3">
            <div class="flex items-center gap-2 bg-white/10 backdrop-blur-sm rounded-xl px-4 py-2 text-sm font-bold">
                <i class="fa-solid fa-trophy text-amber-400"></i>
                <span>Win Cash Prizes</span>
            </div>
            <div class="flex items-center gap-2 bg-white/10 backdrop-blur-sm rounded-xl px-4 py-2 text-sm font-bold">
                <i class="fa-solid fa-store text-blue-300"></i>
                <span>Walk-in & Online Orders</span>
            </div>
            <div class="flex items-center gap-2 bg-white/10 backdrop-blur-sm rounded-xl px-4 py-2 text-sm font-bold">
                <i class="fa-solid fa-shield-halved text-green-400"></i>
                <span>Fair Random Draw</span>
            </div>
        </div>
    </div>
    {{-- Decorative elements --}}
    <div class="absolute right-0 top-0 h-full w-1/3 opacity-10"
         style="background: radial-gradient(circle at 80% 50%, white 0%, transparent 70%)"></div>
    <i class="fa-solid fa-gift absolute right-10 bottom-4 text-9xl text-white/10 hidden sm:block"></i>
</div>

<div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

    {{-- ── Left Column: Category Cards + My Status ─────────────────────────── --}}
    <div class="lg:col-span-8 space-y-6">

        {{-- ── How It Works ──────────────────────────────────────────────────── --}}
        <div class="bg-white/92 dark:bg-slate-900/82 border border-slate-200/70 dark:border-slate-800/70 rounded-[1.5rem] p-6 shadow-sm">
            <h2 class="text-xs font-black uppercase tracking-[0.2em] text-[#0059e3] mb-4">How It Works</h2>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="flex gap-3">
                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-blue-50 dark:bg-blue-900/20 text-[#0059e3] font-black text-sm">1</div>
                    <div>
                        <p class="text-xs font-bold text-slate-800 dark:text-slate-100">Place & Pay</p>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">Make a fully-paid order (walk-in or online).</p>
                    </div>
                </div>
                <div class="flex gap-3">
                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-violet-50 dark:bg-violet-900/20 text-violet-600 font-black text-sm">2</div>
                    <div>
                        <p class="text-xs font-bold text-slate-800 dark:text-slate-100">Enter the Pool</p>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">Your order joins the draw pool for its value tier.</p>
                    </div>
                </div>
                <div class="flex gap-3">
                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-amber-50 dark:bg-amber-900/20 text-amber-600 font-black text-sm">3</div>
                    <div>
                        <p class="text-xs font-bold text-slate-800 dark:text-slate-100">Win!</p>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">When the batch fills, a random winner is drawn.</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── Live Draw Categories ───────────────────────────────────────────── --}}
        <div>
            <h2 class="text-xs font-black uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400 mb-4">
                Live Pool Status
            </h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                @forelse($categories as $cat)
                    @php
                        $setting    = $cat['setting'];
                        $count      = $cat['count'];
                        $needed     = $cat['needed'];
                        $progress   = $cat['progress'];
                        $batchCount = $cat['batchCount'];
                        $isFull     = $cat['isFull'];
                        $justDrawn  = $cat['justDrawn'];

                        $colors = [
                            'bronze'  => ['from-amber-400 to-orange-500', 'bg-amber-500', 'text-amber-700 dark:text-amber-400', 'bg-amber-50 dark:bg-amber-900/20'],
                            'premium' => ['from-violet-500 to-purple-600', 'bg-violet-600', 'text-violet-700 dark:text-violet-400', 'bg-violet-50 dark:bg-violet-900/20'],
                        ];
                        $c = $colors[$setting->category_key] ?? ['from-indigo-500 to-blue-600', 'bg-indigo-600', 'text-indigo-700 dark:text-indigo-400', 'bg-indigo-50 dark:bg-indigo-900/20'];
                    @endphp
                    <div class="bg-white/92 dark:bg-slate-900/82 border border-slate-200/70 dark:border-slate-800/70 rounded-[1.75rem] overflow-hidden shadow-sm">
                        {{-- Card top gradient --}}
                        <div class="bg-gradient-to-r {{ $c[0] }} px-5 py-4 text-white">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-[10px] font-black uppercase tracking-widest opacity-75">
                                        Batch #{{ $batchCount + 1 }} · {{ $setting->amount_range_label }}
                                    </p>
                                    <h3 class="text-xl font-black">{{ $setting->category_label }}</h3>
                                </div>
                                <div class="text-3xl">
                                    @if($setting->category_key === 'bronze') 🥉
                                    @elseif($setting->category_key === 'premium') 🏆
                                    @else <i class="fa-solid fa-star"></i>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="p-5 space-y-4">
                            {{-- Progress bar --}}
                            <div>
                                <div class="flex justify-between text-xs font-bold mb-1.5">
                                    <span class="text-slate-600 dark:text-slate-300">{{ $count }} / {{ $needed }} Entries</span>
                                    <span class="{{ $c[2] }}">{{ $progress }}% Full</span>
                                </div>
                                <div class="h-3 w-full rounded-full bg-slate-100 dark:bg-slate-800">
                                    <div class="h-3 rounded-full {{ $c[1] }} transition-all duration-700"
                                         style="width: {{ $progress }}%"></div>
                                </div>
                            </div>

                            {{-- Prize info --}}
                            <div class="flex items-center justify-between">
                                <div class="{{ $c[3] }} rounded-xl px-3 py-2 text-center flex-1 mr-2">
                                    <p class="text-[10px] font-bold uppercase tracking-wider {{ $c[2] }} opacity-70">Prize</p>
                                    <p class="text-lg font-black {{ $c[2] }}">₹{{ number_format((float)$setting->prize_amount, 0) }}</p>
                                </div>
                                <div class="bg-slate-50 dark:bg-slate-800/60 rounded-xl px-3 py-2 text-center flex-1">
                                    <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Draws Done</p>
                                    <p class="text-lg font-black text-slate-700 dark:text-slate-200">{{ $batchCount }}</p>
                                </div>
                            </div>

                            {{-- Status badge --}}
                            @if($isFull)
                                <div class="flex items-center gap-2 rounded-xl bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-700/30 px-3 py-2">
                                    <span class="flex h-2 w-2 rounded-full bg-green-500 animate-pulse"></span>
                                    <span class="text-xs font-bold text-green-700 dark:text-green-400">Batch Full — Draw Imminent!</span>
                                </div>
                            @elseif($justDrawn)
                                <div class="flex items-center gap-2 rounded-xl bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-700/30 px-3 py-2">
                                    <i class="fa-solid fa-hourglass-start text-blue-500 text-xs"></i>
                                    <span class="text-xs font-bold text-blue-700 dark:text-blue-400">Batch B-{{ $batchCount }} Done — Accumulating Batch B-{{ $batchCount + 1 }}</span>
                                </div>
                            @else
                                <div class="flex items-center gap-2 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700/30 px-3 py-2">
                                    <span class="flex h-2 w-2 rounded-full bg-slate-400"></span>
                                    <span class="text-xs font-semibold text-slate-500 dark:text-slate-400">Need {{ $needed - $count }} more entries to unlock draw</span>
                                </div>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="col-span-2 rounded-2xl border border-dashed border-slate-200 bg-slate-50 dark:border-slate-700 dark:bg-slate-800/30 p-10 text-center">
                        <i class="fa-solid fa-gift mb-3 text-4xl text-slate-300 dark:text-slate-600"></i>
                        <p class="text-sm text-slate-400">No active draw categories at the moment. Check back soon!</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- ── My Eligibility (logged-in customers only) ───────────────────── --}}
        @auth
            @if($myStatus)
                <div class="bg-white/92 dark:bg-slate-900/82 border border-slate-200/70 dark:border-slate-800/70 rounded-[1.75rem] p-6 shadow-sm">
                    <h2 class="text-xs font-black uppercase tracking-[0.2em] text-[#0059e3] mb-5">
                        <i class="fa-solid fa-user-check mr-2"></i> My Draw Status
                    </h2>

                    {{-- Win badge --}}
                    @if($myStatus['wins']->isNotEmpty())
                        <div class="mb-5 rounded-2xl bg-gradient-to-r from-amber-400 to-orange-500 p-5 text-white">
                            <div class="flex items-center gap-3">
                                <i class="fa-solid fa-crown text-3xl"></i>
                                <div>
                                    <p class="text-sm font-black">🎉 You're a Winner!</p>
                                    <p class="text-xs opacity-80">You have {{ $myStatus['wins']->count() }} {{ Str::plural('win', $myStatus['wins']->count()) }} in our lucky draw history.</p>
                                </div>
                            </div>
                            <div class="mt-3 space-y-2">
                                @foreach($myStatus['wins'] as $win)
                                    <div class="flex items-center justify-between bg-white/15 rounded-xl px-3 py-2 text-xs font-bold">
                                        <span>{{ $win->categorySetting->category_label ?? $win->category }} — Batch B-{{ $win->batch_no }}</span>
                                        <span>₹{{ number_format((float)$win->prize_amount, 0) }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- Eligible orders --}}
                    <div class="mb-4">
                        <h3 class="text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-2">Orders in Active Pool</h3>
                        @if($myStatus['is_eligible'])
                            <div class="space-y-2">
                                @foreach($myStatus['eligible_orders'] as $ord)
                                    <div class="flex items-center gap-3 rounded-xl border border-green-100 bg-green-50 dark:border-green-700/30 dark:bg-green-900/10 px-3 py-2.5">
                                        <i class="fa-solid fa-circle-check text-green-500"></i>
                                        <div class="flex-1 text-xs">
                                            <span class="font-bold text-slate-700 dark:text-slate-200">Order #{{ $ord->orderid }}</span>
                                            <span class="text-slate-400 ml-2">₹{{ number_format((float)$ord->total, 0) }}</span>
                                        </div>
                                        <span class="rounded-full bg-green-100 dark:bg-green-900/30 px-2 py-0.5 text-[10px] font-bold text-green-700 dark:text-green-400">In Pool</span>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="rounded-xl border border-dashed border-slate-200 dark:border-slate-700 px-4 py-4 text-center">
                                <p class="text-xs text-slate-400">No eligible orders currently in the draw pool.</p>
                                <a href="{{ route('storefront.shop') }}" class="mt-2 inline-flex items-center gap-1.5 text-xs font-bold text-[#0059e3] hover:underline">
                                    <i class="fa-solid fa-cart-shopping text-[10px]"></i> Shop now to get eligible
                                </a>
                            </div>
                        @endif
                    </div>

                    {{-- Past drawn orders --}}
                    @if($myStatus['drawn_orders']->isNotEmpty())
                        <div>
                            <h3 class="text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-2">My Past Batch Entries</h3>
                            <div class="space-y-1.5">
                                @foreach($myStatus['drawn_orders'] as $ord)
                                    <div class="flex items-center gap-3 rounded-xl border border-slate-100 dark:border-slate-700/50 bg-slate-50 dark:bg-slate-800/40 px-3 py-2">
                                        <i class="fa-solid fa-clock-rotate-left text-slate-400 text-xs"></i>
                                        <div class="flex-1 text-xs">
                                            <span class="font-bold text-slate-600 dark:text-slate-300">Order #{{ $ord->orderid }}</span>
                                            <span class="text-slate-400 ml-2">₹{{ number_format((float)$ord->total, 0) }}</span>
                                        </div>
                                        <span class="rounded-full bg-slate-200 dark:bg-slate-700 px-2 py-0.5 text-[10px] font-bold text-slate-500 dark:text-slate-400">
                                            B-{{ $ord->lucky_draw_batch_no }} · {{ ucfirst($ord->lucky_draw_cat) }}
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            @endif
        @else
            <div class="bg-gradient-to-r from-[#0059e3]/5 to-violet-500/5 border border-[#0059e3]/20 rounded-[1.75rem] p-6 text-center">
                <i class="fa-solid fa-user-lock mb-3 text-3xl text-[#0059e3]/50"></i>
                <p class="text-sm font-bold text-slate-700 dark:text-slate-200 mb-1">Want to check your eligibility?</p>
                <p class="text-xs text-slate-500 dark:text-slate-400 mb-4">Log in to see which of your orders are in the active draw pool and track your past entries.</p>
                <a href="{{ route('login') }}"
                   class="inline-flex items-center gap-2 rounded-xl bg-[#0059e3] px-5 py-2.5 text-sm font-bold text-white hover:bg-[#0040a6] transition-colors">
                    <i class="fa-solid fa-arrow-right-to-bracket"></i> Log In to Check Status
                </a>
            </div>
        @endauth

    </div>

    {{-- ── Right Column: Hall of Winners ──────────────────────────────────── --}}
    <div class="lg:col-span-4 space-y-6">

        {{-- Prize summary cards --}}
        @foreach($categories as $cat)
            @php $s = $cat['setting']; @endphp
            <div class="bg-white/92 dark:bg-slate-900/82 border border-slate-200/70 dark:border-slate-800/70 rounded-2xl p-4 shadow-sm flex items-center gap-4">
                <div class="text-3xl">
                    @if($s->category_key === 'bronze') 🥉
                    @elseif($s->category_key === 'premium') 🏆
                    @else 🎁 @endif
                </div>
                <div>
                    <p class="text-xs font-black uppercase tracking-wider text-slate-500">{{ $s->category_label }}</p>
                    <p class="text-base font-black text-slate-800 dark:text-slate-100">₹{{ number_format((float)$s->prize_amount, 0) }} Prize</p>
                    <p class="text-[10px] text-slate-400">{{ $s->amount_range_label }} · {{ $s->batch_size }} entries/batch</p>
                </div>
            </div>
        @endforeach

        {{-- Hall of Winners --}}
        <div class="bg-white/92 dark:bg-slate-900/82 border border-slate-200/70 dark:border-slate-800/70 rounded-[1.75rem] overflow-hidden shadow-sm">
            <div class="border-b border-slate-100 dark:border-slate-800 px-5 py-4 flex items-center gap-2">
                <i class="fa-solid fa-crown text-amber-500"></i>
                <h2 class="text-sm font-black uppercase tracking-wider text-slate-800 dark:text-slate-100">Hall of Winners</h2>
                <span class="ml-auto rounded-full bg-slate-100 dark:bg-slate-800 px-2 py-0.5 text-[10px] font-bold text-slate-500">
                    {{ $winners->count() }}
                </span>
            </div>

            <div class="divide-y divide-slate-100 dark:divide-slate-800">
                @forelse($winners as $win)
                    <div class="flex items-center gap-3 px-5 py-3.5 hover:bg-slate-50 dark:hover:bg-slate-800/40 transition-colors">
                        {{-- Trophy icon --}}
                        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl
                            @if($win->category === 'bronze') bg-amber-50 dark:bg-amber-900/20 text-amber-600
                            @elseif($win->category === 'premium') bg-violet-50 dark:bg-violet-900/20 text-violet-600
                            @else bg-indigo-50 text-indigo-600 @endif">
                            <i class="fa-solid fa-trophy text-xs"></i>
                        </div>

                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-bold text-slate-800 dark:text-slate-100 truncate">{{ $win->winner_name }}</p>
                            <p class="text-[10px] text-slate-400 flex items-center gap-1.5">
                                {{-- Masked mobile --}}
                                <span class="font-mono">{{ $win->masked_mobile }}</span>
                                <span>·</span>
                                @if($win->source === 'online')
                                    <i class="fa-solid fa-globe text-blue-400"></i>
                                @else
                                    <i class="fa-solid fa-store text-slate-400"></i>
                                @endif
                                <span>{{ $win->created_at->format('d M y') }}</span>
                            </p>
                        </div>

                        <div class="text-right shrink-0">
                            <p class="text-sm font-black text-green-600 dark:text-green-400">
                                ₹{{ number_format((float)$win->prize_amount, 0) }}
                            </p>
                            <p class="text-[10px] font-bold text-slate-400">
                                {{ $win->categorySetting->category_label ?? ucfirst($win->category) }} · B-{{ $win->batch_no }}
                            </p>
                        </div>
                    </div>
                @empty
                    <div class="py-16 text-center">
                        <i class="fa-solid fa-award text-4xl text-slate-200 dark:text-slate-700 mb-3"></i>
                        <p class="text-sm text-slate-400">No winners yet — be the first!</p>
                    </div>
                @endforelse
            </div>
        </div>

    </div>
</div>

@endsection
