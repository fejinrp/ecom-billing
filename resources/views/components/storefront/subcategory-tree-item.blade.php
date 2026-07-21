@props([
    'sub',
    'selectedSubcatId' => null,
    'depth' => 0
])

@php
    $hasChildren = $sub->allChildren && $sub->allChildren->count() > 0;
    $isSelected = $selectedSubcatId == $sub->id;
@endphp

<li x-data="{ open: {{ $isSelected ? 'true' : 'false' }} }" class="space-y-1">
    <div class="flex items-center justify-between group rounded-lg hover:bg-slate-50 dark:hover:bg-slate-800/40 transition-colors">
        <a href="{{ request()->fullUrlWithQuery(['subcatid' => $sub->id]) }}" 
           class="flex-1 flex items-center gap-2 px-2.5 py-1.5 rounded-lg text-xs font-medium transition-all {{ $isSelected ? 'text-[#0059e3] font-bold bg-[#0059e3]/10' : 'text-slate-600 dark:text-slate-300 hover:text-[#0059e3]' }}">
            @if($depth > 0)
                <span class="text-slate-400 dark:text-slate-600 text-[10px]">└─</span>
            @endif
            <span class="truncate">{{ $sub->subcategoryname }}</span>
        </a>

        @if($hasChildren)
            <button type="button" @click.prevent.stop="open = !open" class="px-2 py-1.5 text-slate-400 hover:text-[#0059e3] dark:hover:text-slate-200 transition-colors flex items-center justify-center">
                <i class="fa-solid" :class="open ? 'fa-chevron-down text-[9px]' : 'fa-chevron-right text-[9px]'"></i>
            </button>
        @endif
    </div>

    @if($hasChildren)
        <ul x-show="open" x-collapse class="ml-3 pl-2 border-l border-slate-200 dark:border-slate-800/80 space-y-1">
            @foreach($sub->allChildren as $child)
                <x-storefront.subcategory-tree-item :sub="$child" :selectedSubcatId="$selectedSubcatId" :depth="$depth + 1" />
            @endforeach
        </ul>
    @endif
</li>
