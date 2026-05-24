@props([
    'title',
    'description' => '',
    'icon' => null,
    'glass' => false
])

@php
$isGlass = filter_var($glass, FILTER_VALIDATE_BOOLEAN);
@endphp

<div {{ $attributes->merge(['class' => $isGlass ? 'flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 p-6 rounded-3xl glassmorphism' : 'flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4']) }}>
    <div>
        <h1 class="text-3xl font-extrabold tracking-tight text-slate-900 dark:text-slate-100 flex items-center gap-3">
    
            <span>{{ $title }}</span>
        </h1>
        @if($description)
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">{{ $description }}</p>
        @endif
    </div>
    @if(isset($action))
        <div>
            {{ $action }}
        </div>
    @endif
</div>

