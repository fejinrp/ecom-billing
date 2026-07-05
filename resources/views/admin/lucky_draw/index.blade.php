@extends('layouts.admin')

@section('title', 'Lucky Draw — Draw Board')

@section('content')
<div class="space-y-6">

    {{-- ── Header ─────────────────────────────────────────────────────────── --}}
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-violet-600 via-purple-600 to-indigo-600 p-6 text-white shadow-xl">
        <div class="relative z-10">
            <div class="flex items-center gap-3 mb-1">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-white/20 backdrop-blur-sm">
                    <i class="fa-solid fa-gift text-xl"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-bold tracking-tight">Lucky Draw — Draw Board</h1>
                    <p class="text-sm text-white/70">Automated draw from fully-paid orders. Supports offline (walk-in) &amp; online customers.</p>
                </div>
            </div>
        </div>
        {{-- Decorative circles --}}
        <div class="absolute -right-8 -top-8 h-48 w-48 rounded-full bg-white/5"></div>
        <div class="absolute -right-4 -bottom-8 h-32 w-32 rounded-full bg-white/5"></div>
        <i class="fa-solid fa-trophy absolute right-12 bottom-4 text-8xl text-white/10"></i>
    </div>

    {{-- ── Flash Messages ───────────────────────────────────────────────────── --}}
    @if(session('success'))
        <div class="flex items-start gap-3 rounded-xl border border-green-200 bg-green-50 p-4 text-green-800 dark:border-green-700/40 dark:bg-green-900/20 dark:text-green-300">
            <i class="fa-solid fa-circle-check mt-0.5 shrink-0 text-green-500"></i>
            <p class="text-sm font-medium">{{ session('success') }}</p>
        </div>
    @endif
    @if(session('error'))
        <div class="flex items-start gap-3 rounded-xl border border-red-200 bg-red-50 p-4 text-red-800 dark:border-red-700/40 dark:bg-red-900/20 dark:text-red-300">
            <i class="fa-solid fa-circle-xmark mt-0.5 shrink-0 text-red-500"></i>
            <p class="text-sm font-medium">{{ session('error') }}</p>
        </div>
    @endif

    {{-- ── Draw Categories Grid ─────────────────────────────────────────────── --}}
    @if(count($poolData) === 0)
        <div class="rounded-2xl border border-dashed border-slate-300 bg-slate-50 p-10 text-center dark:border-slate-700 dark:bg-slate-900/30">
            <i class="fa-solid fa-sliders mb-3 text-4xl text-slate-300 dark:text-slate-600"></i>
            <p class="text-sm text-slate-500 dark:text-slate-400">No active draw categories configured.</p>
            @if(Auth::guard('admin')->user()->section == 1)
                <a href="{{ route('admin.lucky_draw.settings') }}" class="mt-3 inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">
                    <i class="fa-solid fa-plus"></i> Configure Categories
                </a>
            @endif
        </div>
    @else
        <div class="grid grid-cols-1 gap-5 md:grid-cols-2 xl:grid-cols-3">
            @foreach($poolData as $key => $data)
                @php
                    $setting     = $data['setting'];
                    $eligible    = $data['eligible'];
                    $batchCount  = $data['batchCount'];
                    $count       = $eligible->count();
                    $needed      = $setting->batch_size;
                    $progress    = $needed > 0 ? min(($count / $needed) * 100, 100) : 0;
                    $isFull      = $count >= $needed;
                    $justDrawn   = $batchCount > 0 && $count === 0; // pool empty after at least one draw

                    $gradients = [
                        'bronze'  => ['from-amber-500 to-orange-500',  'bg-amber-50 dark:bg-amber-900/20',  'text-amber-700 dark:text-amber-300',  'bg-amber-500'],
                        'premium' => ['from-violet-500 to-purple-600', 'bg-violet-50 dark:bg-violet-900/20','text-violet-700 dark:text-violet-300','bg-violet-500'],
                    ];
                    $g = $gradients[$key] ?? ['from-indigo-500 to-blue-600','bg-indigo-50 dark:bg-indigo-900/20','text-indigo-700 dark:text-indigo-300','bg-indigo-500'];
                @endphp
                <div class="flex flex-col rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-900">

                    {{-- Card Header --}}
                    <div class="rounded-t-2xl bg-gradient-to-r {{ $g[0] }} px-5 py-4 text-white">
                        <div class="flex items-center justify-between">
                            <div>
                                <span class="text-xs font-semibold uppercase tracking-widest opacity-80">Batch #{{ $batchCount + 1 }}</span>
                                <h3 class="text-xl font-bold">{{ $setting->category_label }}</h3>
                                <p class="text-sm opacity-75">{{ $setting->amount_range_label }}</p>
                            </div>
                            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-white/20 backdrop-blur-sm text-2xl">
                                @if($key === 'bronze') 🥉
                                @elseif($key === 'premium') 🏆
                                @else <i class="fa-solid fa-star"></i>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-1 flex-col gap-4 p-5">

                        {{-- Progress --}}
                        <div>
                            <div class="mb-1.5 flex items-center justify-between text-sm">
                                <span class="font-semibold text-slate-700 dark:text-slate-200">{{ $count }} / {{ $needed }} Entries</span>
                                <span class="{{ $g[2] }} font-bold">{{ round($progress) }}%</span>
                            </div>
                            <div class="h-2.5 w-full rounded-full bg-slate-100 dark:bg-slate-800">
                                <div class="h-2.5 rounded-full {{ $g[3] }} transition-all duration-500"
                                     style="width: {{ $progress }}%"></div>
                            </div>
                            <p class="mt-1 text-xs text-slate-400 dark:text-slate-500">
                                Prize: <strong class="text-slate-600 dark:text-slate-300">₹{{ number_format((float)$setting->prize_amount, 0) }}</strong>
                                &nbsp;·&nbsp; Needs {{ $needed }} entries to unlock
                            </p>
                        </div>

                        {{-- Draw Button --}}
                        @if($isFull)
                            <form action="{{ route('admin.lucky_draw.draw') }}" method="POST"
                                  onsubmit="return confirm('Start draw for {{ $setting->category_label }}? This cannot be undone.')">
                                @csrf
                                <input type="hidden" name="category_key" value="{{ $setting->category_key }}">
                                <button type="submit"
                                        class="w-full rounded-xl bg-gradient-to-r {{ $g[0] }} px-4 py-3 text-sm font-bold text-white shadow-md transition-all hover:shadow-lg hover:brightness-110 active:scale-95">
                                    <i class="fa-solid fa-shuffle mr-2"></i> Draw Winner Now!
                                </button>
                            </form>
                        @elseif($justDrawn)
                            {{-- Post-draw waiting state --}}
                            <div class="rounded-xl border border-green-200 bg-green-50 px-4 py-3 dark:border-green-700/30 dark:bg-green-900/10">
                                <div class="flex items-center gap-2 text-green-700 dark:text-green-400">
                                    <i class="fa-solid fa-circle-check text-green-500"></i>
                                    <span class="text-sm font-bold">Batch B-{{ $batchCount }} Completed!</span>
                                </div>
                                <p class="mt-1 text-xs text-green-600 dark:text-green-500">
                                    All {{ $needed }} entries are preserved in history. Waiting for new orders to fill Batch B-{{ $batchCount + 1 }}.
                                </p>
                            </div>
                            <button disabled
                                    class="w-full cursor-not-allowed rounded-xl bg-slate-100 px-4 py-3 text-sm font-bold text-slate-400 dark:bg-slate-800 dark:text-slate-600">
                                <i class="fa-solid fa-hourglass-start mr-2"></i>
                                Waiting for Batch B-{{ $batchCount + 1 }} entries…
                            </button>
                        @else
                            <button disabled
                                    class="w-full cursor-not-allowed rounded-xl bg-slate-100 px-4 py-3 text-sm font-bold text-slate-400 dark:bg-slate-800 dark:text-slate-600">
                                <i class="fa-solid fa-lock mr-2"></i>
                                Need {{ $needed - $count }} more {{ Str::plural('entry', $needed - $count) }}
                            </button>
                        @endif

                        {{-- Eligibility Pool Preview --}}
                        <div>
                            <h4 class="mb-2 text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">
                                Eligibility Pool — Batch #{{ $batchCount + 1 }}
                            </h4>
                            <div class="max-h-52 space-y-1.5 overflow-y-auto pr-1">
                                @forelse($eligible as $entry)
                                    <div class="flex items-center gap-2.5 rounded-lg border border-slate-100 bg-slate-50 p-2 dark:border-slate-700/60 dark:bg-slate-800/40">
                                        <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-md {{ $g[3] }} text-[10px] font-bold text-white">
                                            @if($entry['source'] === 'online')
                                                <i class="fa-solid fa-globe"></i>
                                            @else
                                                <i class="fa-solid fa-store"></i>
                                            @endif
                                        </span>
                                        <div class="min-w-0 flex-1">
                                            <p class="truncate text-xs font-semibold text-slate-700 dark:text-slate-200">{{ $entry['name'] }}</p>
                                            <p class="text-[10px] text-slate-400">
                                                {{ $entry['source'] === 'online' ? 'Online' : 'Walk-in' }}
                                                &nbsp;·&nbsp; ₹{{ number_format((float)$entry['amount'], 0) }}
                                            </p>
                                        </div>
                                        <span class="shrink-0 rounded-md bg-white px-1.5 py-0.5 text-[10px] font-mono font-bold text-slate-500 shadow-sm dark:bg-slate-800 dark:text-slate-400">
                                            #{{ $entry['id'] }}
                                        </span>
                                    </div>
                                @empty
                                    @if($justDrawn)
                                        {{-- Post-draw empty state: data preserved, waiting for next batch --}}
                                        <div class="flex flex-col items-center gap-1.5 py-5 text-center">
                                            <i class="fa-solid fa-clock-rotate-left text-2xl text-slate-300 dark:text-slate-600"></i>
                                            <p class="text-xs font-semibold text-slate-500 dark:text-slate-400">Batch B-{{ $batchCount }} entries are safely stored.</p>
                                            <p class="text-[10px] text-slate-400 dark:text-slate-500">New qualifying orders will appear here for Batch B-{{ $batchCount + 1 }}.</p>
                                        </div>
                                    @else
                                        <p class="py-4 text-center text-xs text-slate-400">No eligible orders yet.</p>
                                    @endif
                                @endforelse
                            </div>
                        </div>

                    </div>{{-- /card body --}}
                </div>
            @endforeach
        </div>
    @endif

    {{-- ── Hall of Winners ─────────────────────────────────────────────────── --}}
    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-900">
        <div class="flex items-center gap-3 border-b border-slate-100 px-6 py-4 dark:border-slate-700/60">
            <i class="fa-solid fa-crown text-amber-500"></i>
            <h2 class="text-base font-bold text-slate-800 dark:text-slate-100">Hall of Winners</h2>
            <span class="ml-auto rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-semibold text-slate-500 dark:bg-slate-800 dark:text-slate-400">
                {{ $winners->count() }} {{ Str::plural('draw', $winners->count()) }}
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 text-xs uppercase tracking-wider text-slate-500 dark:bg-slate-800/60 dark:text-slate-400">
                    <tr>
                        <th class="px-5 py-3">Draw Date</th>
                        <th class="px-5 py-3">Winner</th>
                        <th class="px-5 py-3">Mobile</th>
                        <th class="px-5 py-3">Category</th>
                        <th class="px-5 py-3">Batch</th>
                        <th class="px-5 py-3">Source</th>
                        <th class="px-5 py-3">Coupon</th>
                        <th class="px-5 py-3 text-right">Prize</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700/50">
                    @forelse($winners as $win)
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-colors">
                            <td class="px-5 py-3 text-slate-500 dark:text-slate-400 whitespace-nowrap">
                                {{ $win->created_at->format('d M Y, h:i A') }}
                            </td>
                            <td class="px-5 py-3 font-semibold text-slate-800 dark:text-slate-100">{{ $win->winner_name }}</td>
                            <td class="px-5 py-3 font-mono text-xs text-slate-500 dark:text-slate-400">{{ $win->masked_mobile }}</td>
                            <td class="px-5 py-3">
                                <span class="inline-flex items-center gap-1 rounded-full border px-2.5 py-0.5 text-xs font-semibold
                                    @if($win->category === 'bronze') border-amber-200 bg-amber-50 text-amber-700 dark:border-amber-700/40 dark:bg-amber-900/20 dark:text-amber-300
                                    @elseif($win->category === 'premium') border-violet-200 bg-violet-50 text-violet-700 dark:border-violet-700/40 dark:bg-violet-900/20 dark:text-violet-300
                                    @else border-indigo-200 bg-indigo-50 text-indigo-700 dark:border-indigo-700/40 dark:bg-indigo-900/20 dark:text-indigo-300 @endif">
                                    {{ $win->categorySetting->category_label ?? ucfirst($win->category) }}
                                </span>
                            </td>
                            <td class="px-5 py-3 font-bold text-slate-700 dark:text-slate-200">B-{{ $win->batch_no }}</td>
                            <td class="px-5 py-3">
                                @if($win->source === 'online')
                                    <span class="inline-flex items-center gap-1 rounded-md bg-blue-50 px-2 py-0.5 text-xs font-semibold text-blue-700 dark:bg-blue-900/20 dark:text-blue-300">
                                        <i class="fa-solid fa-globe text-[9px]"></i> Online
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 rounded-md bg-slate-100 px-2 py-0.5 text-xs font-semibold text-slate-600 dark:bg-slate-800 dark:text-slate-300">
                                        <i class="fa-solid fa-store text-[9px]"></i> Walk-in
                                    </span>
                                @endif
                            </td>
                            <td class="px-5 py-3">
                                <span class="rounded border border-slate-200 bg-slate-50 px-2 py-0.5 font-mono text-xs text-slate-600 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300">
                                    {{ $win->coupon_reference }}
                                </span>
                            </td>
                            <td class="px-5 py-3 text-right font-bold text-green-600 dark:text-green-400">
                                ₹{{ number_format((float)$win->prize_amount, 0) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-16 text-center">
                                <i class="fa-solid fa-award mb-3 text-4xl text-slate-200 dark:text-slate-700"></i>
                                <p class="text-sm text-slate-400 dark:text-slate-500">No winners yet. Complete a batch to start drawing!</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
