<div x-data="{ open: true }" class="space-y-2">
    <!-- Node Row -->
    <div class="flex items-center justify-between p-3.5 rounded-xl border border-slate-200 dark:border-slate-800/80 bg-slate-50/80 dark:bg-slate-900/40 hover:bg-slate-100 dark:hover:bg-slate-800/40 transition-all group shadow-sm dark:shadow-none"
         style="margin-left: {{ $depth * 24 }}px;">
        
        <div class="flex items-center gap-3">
            @if($sub->allChildren && $sub->allChildren->count() > 0)
                <button @click="open = !open" class="w-6 h-6 rounded-lg bg-slate-200 dark:bg-slate-800 border border-slate-300 dark:border-slate-700/60 flex items-center justify-center text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white transition-all">
                    <i class="fa-solid" :class="open ? 'fa-chevron-down text-xs' : 'fa-chevron-right text-xs'"></i>
                </button>
            @else
                <div class="w-6 h-6 flex items-center justify-center text-slate-400 dark:text-slate-600">
                    <i class="fa-solid fa-circle-dot text-[8px]"></i>
                </div>
            @endif

            <div class="flex items-center gap-2">
                <span class="font-bold text-slate-800 dark:text-slate-200 text-sm group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">
                    {{ $sub->subcategoryname }}
                </span>
                
                <span class="text-[10px] font-mono font-semibold px-2 py-0.5 rounded-md bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 border border-indigo-200 dark:border-indigo-500/20">
                    ID: #{{ $sub->id }}
                </span>

                @if($depth === 0 && $sub->category)
                    <span class="text-[10px] font-semibold px-2 py-0.5 rounded-md bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 border border-slate-200 dark:border-slate-700/50">
                        {{ $sub->category->cat_name }}
                    </span>
                @endif

                @if($sub->allChildren && $sub->allChildren->count() > 0)
                    <span class="text-[10px] font-semibold px-2 py-0.5 rounded-md bg-amber-50 dark:bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-200 dark:border-amber-500/20">
                        {{ $sub->allChildren->count() }} {{ Str::plural('child', $sub->allChildren->count()) }}
                    </span>
                @endif
            </div>
        </div>

        <!-- Node Action Buttons -->
        <div class="flex items-center gap-1.5 opacity-90 group-hover:opacity-100 transition-opacity">
            <!-- Add Child -->
            <button @click="openAddChildModal({{ json_encode($sub) }})" 
                    class="p-2 rounded-lg bg-indigo-50 dark:bg-indigo-500/10 border border-indigo-200 dark:border-indigo-500/20 text-indigo-600 dark:text-indigo-400 hover:bg-indigo-600 hover:text-white dark:hover:bg-indigo-500 dark:hover:text-white transition-all text-xs flex items-center gap-1 font-semibold"
                    title="Add Child Subcategory">
                <i class="fa-solid fa-plus"></i>
                <span class="hidden sm:inline">Add Child</span>
            </button>

            <!-- Edit -->
            <button @click="openEditModal({{ json_encode($sub) }})" 
                    class="p-2 rounded-lg bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700/50 text-slate-600 dark:text-slate-300 hover:text-amber-600 dark:hover:text-amber-400 hover:border-amber-300 dark:hover:border-amber-500/50 transition-all text-xs"
                    title="Edit Node">
                <i class="fa-solid fa-pen-to-square"></i>
            </button>

            <!-- Move -->
            <button @click="openMoveModal({{ json_encode($sub) }})" 
                    class="p-2 rounded-lg bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700/50 text-slate-600 dark:text-slate-300 hover:text-indigo-600 dark:hover:text-indigo-400 hover:border-indigo-300 dark:hover:border-indigo-500/50 transition-all text-xs"
                    title="Move Node">
                <i class="fa-solid fa-arrows-up-down-left-right"></i>
            </button>

            <!-- Delete -->
            <button @click="openDeleteModal({{ json_encode($sub) }})" 
                    class="p-2 rounded-lg bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700/50 text-slate-600 dark:text-slate-400 hover:text-rose-600 dark:hover:text-rose-400 hover:border-rose-300 dark:hover:border-rose-500/50 transition-all text-xs"
                    title="Delete Node">
                <i class="fa-solid fa-trash-can"></i>
            </button>
        </div>
    </div>

    <!-- Recursive Sub-tree rendering -->
    @if($sub->allChildren && $sub->allChildren->count() > 0)
        <div x-show="open" x-collapse class="space-y-2 border-l border-indigo-200 dark:border-indigo-500/20 ml-3 pl-1">
            @foreach($sub->allChildren as $child)
                @include('admin.subcategories.partials.tree_item', ['sub' => $child, 'depth' => $depth + 1])
            @endforeach
        </div>
    @endif
</div>
