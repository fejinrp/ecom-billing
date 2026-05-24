@props([
    'icon' => null,
    'href' => null,
    'variant' => 'primary'
])

@php
$baseClasses = 'inline-flex items-center justify-center gap-2 px-5 py-3 rounded-2xl font-bold text-sm transition-all duration-300 shadow-xl';

$variants = [
    'primary' => 'bg-orange-600 hover:bg-orange-700 text-white shadow-orange-600/10 hover:shadow-orange-600/25 active:scale-95 disabled:opacity-40 disabled:cursor-not-allowed disabled:pointer-events-none disabled:shadow-none',
    'secondary' => 'bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-200 active:scale-95 disabled:opacity-40 disabled:cursor-not-allowed disabled:pointer-events-none disabled:shadow-none',
    'danger' => 'bg-gradient-to-r from-rose-500 to-red-650 hover:from-rose-600 hover:to-red-750 text-white shadow-rose-500/10 hover:shadow-rose-500/25 active:scale-95 disabled:opacity-40 disabled:cursor-not-allowed disabled:pointer-events-none disabled:shadow-none',
    'indigo' => 'bg-gradient-to-r from-indigo-500 to-purple-600 hover:from-indigo-650 hover:to-purple-750 text-white shadow-indigo-500/10 hover:shadow-indigo-500/25 active:scale-95 disabled:opacity-40 disabled:cursor-not-allowed disabled:pointer-events-none disabled:shadow-none',
    'indigo-flat' => 'bg-indigo-600 hover:bg-indigo-700 text-white shadow-indigo-600/10 hover:shadow-indigo-600/25 active:scale-95 disabled:opacity-40 disabled:cursor-not-allowed disabled:pointer-events-none disabled:shadow-none'
];

$classes = $baseClasses . ' ' . ($variants[$variant] ?? $variants['primary']);
@endphp

@if($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        @if($icon)
            <i class="{{ $icon }}"></i>
        @endif
        <span>{{ $slot }}</span>
    </a>
@else
    <button {{ $attributes->merge(['class' => $classes]) }}>
        @if($icon)
            <i class="{{ $icon }}"></i>
        @endif
        <span>{{ $slot }}</span>
    </button>
@endif
