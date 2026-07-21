@props([
    'selectedCat' => null,
    'selectedSubcat' => null,
    'catName' => 'catid',
    'subcatName' => 'subcatid',
    'label' => 'Category & Subcategory Hierarchy',
    'required' => true,
    'options' => null
])

@php
    $treeOptions = $options ?? \App\Models\Category::getCombinedTreeOptions();
    
    $initialValue = '';
    if ($selectedSubcat) {
        $initialValue = 'sub_' . $selectedSubcat;
    } elseif ($selectedCat) {
        $initialValue = 'cat_' . $selectedCat;
    }
@endphp

<div x-data="{
    open: false,
    search: '',
    selectedValue: '{{ $initialValue }}',
    catId: '{{ $selectedCat ?? '' }}',
    subcatId: '{{ $selectedSubcat ?? '' }}',
    selectedLabel: '',
    selectedPath: '',
    options: @js($treeOptions),

    get filteredOptions() {
        if (!this.search.trim()) return this.options;
        const q = this.search.toLowerCase();
        return this.options.filter(o => 
            (o.subcategoryname && o.subcategoryname.toLowerCase().includes(q)) ||
            (o.label && o.label.toLowerCase().includes(q)) ||
            (o.path && o.path.toLowerCase().includes(q))
        );
    },

    selectItem(item) {
        if (!item) {
            this.selectedValue = '';
            this.catId = '';
            this.subcatId = '';
            this.selectedLabel = '';
            this.selectedPath = '';
        } else {
            this.selectedValue = item.value;
            this.catId = item.catid || '';
            this.subcatId = item.subcatid || '';
            this.selectedLabel = item.name || item.subcategoryname || item.label;
            this.selectedPath = item.path || '';
        }
        this.open = false;
        this.search = '';
        $dispatch('category-tree-changed', { catId: this.catId, subcatId: this.subcatId, path: this.selectedPath });
    },

    init() {
        if (this.selectedValue) {
            const item = this.options.find(o => o.value === this.selectedValue);
            if (item) {
                this.selectedLabel = item.name || item.subcategoryname || item.label;
                this.selectedPath = item.path || '';
            }
        }
    }
}" class="space-y-1 relative" @click.outside="open = false">
    
    @if($label)
        <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">
            {{ $label }} @if($required)<span class="text-rose-500">*</span>@endif
        </label>
    @endif

    <!-- Trigger Button -->
    <div class="relative">
        <button type="button" 
                @click="open = !open; if(open) $nextTick(() => $refs.searchInput.focus());" 
                class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl px-4 py-3 text-sm text-left flex items-center justify-between focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 transition-all cursor-pointer">
            <span class="truncate font-medium" 
                  :class="selectedLabel ? 'text-slate-800 dark:text-slate-200 font-semibold' : 'text-slate-400 dark:text-slate-500'" 
                  x-text="selectedLabel || '~ Search / Select Category Node ~'">
            </span>
            <div class="flex items-center gap-2">
                <template x-if="selectedValue">
                    <span @click.stop="selectItem(null)" class="text-slate-400 hover:text-rose-500 p-1 text-xs transition-colors" title="Clear selection">
                        <i class="fa-solid fa-xmark"></i>
                    </span>
                </template>
                <i class="fa-solid fa-chevron-down text-xs text-slate-400 transition-transform duration-200" :class="open ? 'rotate-180' : ''"></i>
            </div>
        </button>

        <!-- Searchable Dropdown Popup -->
        <div x-show="open" 
             x-transition:enter="transition ease-out duration-100"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-75"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="absolute z-50 mt-1 w-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-2xl overflow-hidden backdrop-blur-xl"
             style="display: none;">
            
            <!-- Search Bar Input -->
            <div class="p-2 border-b border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-950/50 relative">
                <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                <input type="text" 
                       x-ref="searchInput"
                       x-model="search" 
                       placeholder="Type category or subcategory name..." 
                       class="w-full bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl pl-9 pr-4 py-2 text-xs text-slate-800 dark:text-slate-200 placeholder-slate-400 focus:outline-none focus:ring-1 focus:ring-indigo-500">
            </div>

            <!-- Scrollable Filtered Options List -->
            <div class="max-h-60 overflow-y-auto p-1.5 space-y-0.5 custom-scrollbar">
                <div @click="selectItem(null)" 
                     class="px-3 py-2 rounded-xl text-xs text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800/60 cursor-pointer font-medium italic">
                    ~ Clear Selection (No Node) ~
                </div>

                <template x-for="opt in filteredOptions" :key="opt.value">
                    <div @click="selectItem(opt)" 
                         :class="{
                             'bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 font-bold': selectedValue === opt.value,
                             'font-bold text-slate-800 dark:text-slate-100 bg-slate-50/60 dark:bg-slate-800/40': opt.type === 'category',
                             'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800/50': opt.type === 'subcategory' && selectedValue !== opt.value
                         }"
                         class="px-3 py-2 rounded-xl text-xs cursor-pointer transition-all flex items-center justify-between">
                        <span class="whitespace-pre truncate" x-text="opt.label"></span>
                        <span x-show="selectedValue === opt.value" class="text-indigo-500 text-xs">
                            <i class="fa-solid fa-check"></i>
                        </span>
                    </div>
                </template>

                <div x-show="filteredOptions.length === 0" class="px-4 py-6 text-center text-xs text-slate-400">
                    <i class="fa-solid fa-folder-open block text-xl mb-1 text-slate-400 dark:text-slate-600"></i>
                    <span>No category matching "<span x-text="search" class="font-bold"></span>"</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Full Breadcrumb Trail Display (e.g. Electronics > Mobile Phones > Android Phones) -->
    <template x-if="selectedPath">
        <div class="mt-2 text-xs font-semibold text-slate-600 dark:text-slate-300 bg-indigo-50/80 dark:bg-indigo-500/10 border border-indigo-200 dark:border-indigo-500/20 px-3.5 py-2.5 rounded-xl flex items-center gap-2 animate-fadeIn shadow-sm">
            <i class="fa-solid fa-folder-tree text-indigo-600 dark:text-indigo-400"></i>
            <span>Selected Path: <strong class="text-indigo-700 dark:text-indigo-300 font-bold ml-1" x-text="selectedPath"></strong></span>
        </div>
    </template>

    <!-- Hidden form fields to submit both catid and subcatid automatically -->
    <input type="hidden" name="{{ $catName }}" :value="catId" @if($required) required @endif />
    <input type="hidden" name="{{ $subcatName }}" :value="subcatId" />
</div>
