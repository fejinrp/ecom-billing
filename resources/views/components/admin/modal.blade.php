@props([
    'state' => null,
    'id' => null, 
    'title' => '',
    'icon' => null,
    'maxWidth' => 'md'
])

@php
$stateVar = $state ?? $id;

$maxWidthClass = [
    'sm' => 'max-w-sm',
    'md' => 'max-w-md',
    'lg' => 'max-w-lg',
    'xl' => 'max-w-xl',
    '2xl' => 'max-w-2xl',
][$maxWidth] ?? 'max-w-md';
@endphp

<template x-teleport="body">
<div x-show="{{ $stateVar }}" 
     x-cloak 
     class="admin-modal fixed inset-0 z-50 flex items-center justify-center p-4"
     style="display: none;">
     
    <!-- Backdrop with smooth fade in/out -->
    <div x-show="{{ $stateVar }}"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="absolute inset-0 bg-slate-900/40 dark:bg-slate-950/80 backdrop-blur-sm"
         @click="{{ $stateVar }} = false"></div>
          
    <!-- Modal Card with scale/slide transition -->
    <div x-show="{{ $stateVar }}"
         x-transition:enter="transition ease-out duration-300 delay-75"
         x-transition:enter-start="opacity-0 scale-95 translate-y-4"
         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100 translate-y-0"
         x-transition:leave-end="opacity-0 scale-95 translate-y-4"
         class="relative w-full {{ $maxWidthClass }} rounded-3xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-8 shadow-2xl space-y-6 z-10">
        
        <div class="flex justify-between items-center pb-1">
            <h3 class="text-xl font-bold text-slate-800 dark:text-slate-200 flex items-center gap-2">
                @if($icon)
                    <i class="{{ $icon }} text-indigo-500"></i>
                @endif
                <span>{{ $title }}</span>
            </h3>
            <button type="button" @click="{{ $stateVar }} = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition-all p-1 cursor-pointer">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>

        {{ $slot }}
    </div>
</div>
</template>
