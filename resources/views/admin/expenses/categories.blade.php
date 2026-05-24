@extends('layouts.admin')

@section('content')
<div x-data="{ 
    showAddModal: false, 
    showEditModal: false, 
    showDeleteModal: false,
    
    // Add form states
    addExpName: '',
    
    // Edit form states
    editExpId: '',
    editExpName: '',
    
    // Delete states
    deleteExpId: '',
    
    openEditModal(category) {
        this.editExpId = category.exp_id;
        this.editExpName = category.exp_name;
        this.showEditModal = true;
    },
    
    openDeleteModal(expId) {
        this.deleteExpId = expId;
        this.showDeleteModal = true;
    }
}" class="space-y-8 animate-fadeIn">

    <x-admin.header 
        title="Expense Categories" 
        description="Configure and manage expense names / categories for financial ledgers." 
        glass="true"
    >
        <x-slot:action>
            <x-admin.button @click="showAddModal = true" icon="fa-solid fa-plus-circle">
                Add Expense Name
            </x-admin.button>
        </x-slot:action>
    </x-admin.header>

    <!-- Alert Messages -->
    @if(session('success'))
        <div class="p-4 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-600 dark:text-emerald-400 text-sm font-semibold flex items-center gap-2">
            <i class="fa-solid fa-circle-check text-base"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <!-- Expense Categories Table Card -->
    <div class="p-6 rounded-3xl bg-white/80 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-800/80 shadow-lg shadow-slate-200/50 dark:shadow-none space-y-4">
        <div class="responsive-table-container scrollbar-thin rounded-2xl border border-slate-200 dark:border-slate-800">
            <table class="w-full text-left text-sm text-slate-600 dark:text-slate-300 min-w-0 lg:min-w-[600px] block lg:table">
                <thead class="bg-slate-50 dark:bg-slate-950 text-xs font-semibold uppercase text-slate-500 dark:text-slate-400 border-b border-slate-200 dark:border-slate-800 hidden lg:table-header-group">
                    <tr>
                        <th class="px-6 py-4">No</th>
                        <th class="px-6 py-4">Expense Name</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-800 block lg:table-row-group w-full space-y-4 lg:space-y-0 lg:divide-y lg:divide-slate-200/60 dark:lg:divide-slate-800/40 p-4 lg:p-0">
                    @forelse($categories as $index => $category)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/30 transition-all block lg:table-row w-full bg-white dark:bg-slate-900/30 border border-slate-200 dark:border-slate-800/60 rounded-2xl p-4 lg:p-0 mb-4 lg:mb-0 relative grid grid-cols-2 gap-x-4 gap-y-3 lg:grid-cols-none lg:bg-transparent lg:border-0 lg:hover:bg-slate-50 dark:lg:hover:bg-slate-900/20 lg:transition-all">
                            <!-- No -->
                            <td class="col-span-2 bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 px-3 py-2 rounded-lg text-xs font-mono font-bold flex items-center justify-between lg:table-cell lg:col-span-none lg:bg-transparent lg:px-6 lg:py-4 lg:text-slate-500 dark:lg:text-slate-400">
                                <span class="lg:hidden uppercase tracking-wider text-[10px] font-bold text-indigo-600 dark:text-indigo-400">No</span>
                                <span>#{{ $index + 1 }}</span>
                            </td>

                            <!-- Expense Name -->
                            <td class="py-2 lg:px-6 lg:py-4 col-span-2 block lg:table-cell lg:col-span-none">
                                <span class="block lg:hidden text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Expense Name</span>
                                <span class="font-bold text-indigo-600 dark:text-indigo-400">{{ $category->exp_name }}</span>
                            </td>

                            <!-- Actions -->
                            <td class="py-3 border-t border-slate-200/60 dark:border-slate-800/40 mt-2 lg:border-t-0 lg:mt-0 col-span-2 block lg:table-cell lg:col-span-none lg:text-right whitespace-nowrap">
                                <span class="block lg:hidden text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-2">Actions</span>
                                <div class="inline-flex gap-2">
                                    <button @click="openEditModal({{ json_encode($category) }})" class="p-2.5 rounded-xl bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700/50 text-slate-600 dark:text-slate-300 hover:text-indigo-500 hover:border-indigo-500/50 transition-all cursor-pointer" title="Edit Category">
                                        <i class="fa-solid fa-pen-to-square text-sm"></i>
                                    </button>
                                    <button @click="openDeleteModal({{ $category->exp_id }})" class="p-2.5 rounded-xl bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700/50 text-slate-500 dark:text-slate-400 hover:text-rose-500 hover:border-rose-500/50 transition-all cursor-pointer" title="Remove Category">
                                        <i class="fa-solid fa-trash-can text-sm"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr class="block lg:table-row w-full bg-white dark:bg-slate-900/30 border border-slate-200 dark:border-slate-800/60 rounded-2xl p-6 lg:p-0">
                            <td colspan="3" class="px-6 py-8 text-center text-slate-500 font-medium block lg:table-cell w-full font-medium">No expense categories found. Click 'Add Expense Name' to create one.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- 1. Add Category Modal -->
    <template x-teleport="body">
    <div x-show="showAddModal" 
         x-cloak 
         class="admin-modal fixed inset-0 z-50 flex items-center justify-center p-4"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
        
        <!-- Backdrop -->
        <div class="absolute inset-0 bg-slate-900/60 dark:bg-slate-950/80 backdrop-blur-sm" @click="showAddModal = false"></div>

        <div x-show="showAddModal"
             x-transition:enter="transition ease-out duration-300 delay-75"
             x-transition:enter-start="opacity-0 scale-95 translate-y-4"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100 translate-y-0"
             x-transition:leave-end="opacity-0 scale-95 translate-y-4"
             class="relative w-full max-w-md rounded-3xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-8 shadow-2xl space-y-6">
            <div class="flex justify-between items-center">
                <h3 class="text-xl font-bold text-slate-800 dark:text-slate-200 flex items-center gap-2">
                    <i class="fa-solid fa-plus-circle text-indigo-500"></i>
                    <span>Add Expense Category</span>
                </h3>
                <button @click="showAddModal = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition-all cursor-pointer">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <form method="POST" action="{{ route('admin.expenses.categories.store') }}" class="space-y-4">
                @csrf

                <!-- Expense Name -->
                <div>
                    <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Expense Name</label>
                    <input type="text" 
                           name="exp_name" 
                           required 
                           placeholder="Enter expense category name"
                           class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl px-4 py-3 text-sm text-slate-700 dark:text-slate-300 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                </div>

                <div class="flex gap-4 pt-4 border-t border-slate-200 dark:border-slate-800">
                    <button type="button" @click="showAddModal = false" class="flex-1 py-3 px-4 rounded-xl border border-slate-200 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-800/50 text-slate-600 dark:text-slate-300 font-semibold text-sm transition-all cursor-pointer">Cancel</button>
                    <button type="submit" class="flex-1 py-3 px-4 rounded-xl bg-orange-600 hover:bg-orange-700 text-white font-bold text-sm shadow-xl shadow-orange-600/10 hover:shadow-orange-600/25 transition-all cursor-pointer">Save Category</button>
                </div>
            </form>
        </div>
    </div>
    </template>

    <!-- 2. Edit Category Modal -->
    <template x-teleport="body">
    <div x-show="showEditModal" 
         x-cloak 
         class="admin-modal fixed inset-0 z-50 flex items-center justify-center p-4"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
        
        <!-- Backdrop -->
        <div class="absolute inset-0 bg-slate-900/60 dark:bg-slate-950/80 backdrop-blur-sm" @click="showEditModal = false"></div>

        <div x-show="showEditModal"
             x-transition:enter="transition ease-out duration-300 delay-75"
             x-transition:enter-start="opacity-0 scale-95 translate-y-4"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100 translate-y-0"
             x-transition:leave-end="opacity-0 scale-95 translate-y-4"
             class="relative w-full max-w-md rounded-3xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-8 shadow-2xl space-y-6">
            <div class="flex justify-between items-center">
                <h3 class="text-xl font-bold text-slate-800 dark:text-slate-200 flex items-center gap-2">
                    <i class="fa-solid fa-pen-to-square text-indigo-500"></i>
                    <span>Edit Expense Category</span>
                </h3>
                <button @click="showEditModal = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition-all cursor-pointer">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <form method="POST" :action="'{{ route('admin.expenses.categories.index') }}/' + editExpId" class="space-y-4">
                @csrf
                @method('PUT')

                <!-- Expense Name -->
                <div>
                    <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Expense Name</label>
                    <input type="text" 
                           name="exp_name" 
                           required 
                           x-model="editExpName"
                           placeholder="Enter expense category name"
                           class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl px-4 py-3 text-sm text-slate-700 dark:text-slate-300 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                </div>

                <div class="flex gap-4 pt-4 border-t border-slate-200 dark:border-slate-800">
                    <button type="button" @click="showEditModal = false" class="flex-1 py-3 px-4 rounded-xl border border-slate-200 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-800/50 text-slate-600 dark:text-slate-300 font-semibold text-sm transition-all cursor-pointer">Cancel</button>
                    <button type="submit" class="flex-1 py-3 px-4 rounded-xl bg-orange-600 hover:bg-orange-700 text-white font-bold text-sm shadow-xl shadow-orange-600/10 hover:shadow-orange-600/25 transition-all cursor-pointer">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
    </template>

    <!-- 3. Delete Confirmation Modal -->
    <template x-teleport="body">
    <div x-show="showDeleteModal" 
         x-cloak 
         class="admin-modal fixed inset-0 z-50 flex items-center justify-center p-4"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
        
        <!-- Backdrop -->
        <div class="absolute inset-0 bg-slate-900/60 dark:bg-slate-950/80 backdrop-blur-sm" @click="showDeleteModal = false"></div>

        <div x-show="showDeleteModal"
             x-transition:enter="transition ease-out duration-300 delay-75"
             x-transition:enter-start="opacity-0 scale-95 translate-y-4"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100 translate-y-0"
             x-transition:leave-end="opacity-0 scale-95 translate-y-4"
             class="relative w-full max-w-sm rounded-3xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-8 shadow-2xl space-y-6">
            <div class="text-center space-y-3">
                <div class="inline-flex p-3 rounded-2xl bg-rose-500/10 text-rose-500 mb-2">
                    <i class="fa-solid fa-trash-can text-2xl animate-bounce"></i>
                </div>
                <h3 class="text-lg font-bold text-slate-800 dark:text-slate-200">Remove Expense Name?</h3>
                <p class="text-sm text-slate-500 dark:text-slate-400">Do you really want to remove this expense category? This will mark it as inactive.</p>
            </div>

            <form method="POST" :action="'{{ route('admin.expenses.categories.index') }}/' + deleteExpId">
                @csrf
                @method('DELETE')
                
                <div class="flex gap-4 pt-2">
                    <button type="button" @click="showDeleteModal = false" class="flex-1 py-3 px-4 rounded-xl border border-slate-200 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-800/50 text-slate-600 dark:text-slate-300 font-semibold text-sm transition-all cursor-pointer">Cancel</button>
                    <button type="submit" class="flex-1 py-3 px-4 rounded-xl bg-gradient-to-r from-rose-500 to-red-600 text-white font-bold text-sm shadow-xl shadow-rose-500/10 hover:shadow-rose-500/25 transition-all cursor-pointer">Delete</button>
                </div>
            </form>
        </div>
    </div>
    </template>

</div>
@endsection
