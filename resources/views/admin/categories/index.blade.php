@extends('layouts.admin')

@section('content')
<div x-data="{ 
    showAddModal: false, 
    showEditModal: false, 
    showDeleteModal: false,
    
    // Add form states
    addCatName: '',
    
    // Edit form states
    editCatId: '',
    editCatName: '',
    
    // Delete states
    deleteCatId: '',
    
    openEditModal(category) {
        this.editCatId = category.cat_id;
        this.editCatName = category.cat_name;
        this.showEditModal = true;
    },
    
    openDeleteModal(catId) {
        this.deleteCatId = catId;
        this.showDeleteModal = true;
    }
}" class="space-y-8 animate-fadeIn">

    <!-- Header Section -->
    <x-admin.header title="Manage Categories" description="Configure and manage product categories for inventory organization." glass="false">
        <x-slot:action>
            <x-admin.button @click="showAddModal = true" icon="fa-solid fa-plus-circle">
                Add Category
            </x-admin.button>
        </x-slot:action>
    </x-admin.header>

    <!-- Categories Table Card -->
    @php
    $tableHeaders = [
        ['label' => 'No'],
        ['label' => 'Category Name'],
        ['label' => 'Creation Date'],
        ['label' => 'Actions', 'align' => 'right']
    ];
    @endphp

    <x-admin.table :headers="$tableHeaders" :collection="$categories" type="card" minWidth="500px">
        @forelse($categories as $index => $category)
            <tr class="hover:bg-slate-800/30 transition-all block lg:table-row w-full bg-slate-900/30 border border-slate-800/60 rounded-2xl p-4 lg:p-0 mb-4 lg:mb-0 relative grid grid-cols-2 gap-x-4 gap-y-3 lg:grid-cols-none lg:bg-transparent lg:border-0 lg:hover:bg-slate-900/20 lg:transition-all">
                <!-- No -->
                <td class="col-span-2 bg-indigo-500/10 text-indigo-400 px-3 py-2 rounded-lg text-xs font-mono font-bold flex items-center justify-between lg:table-cell lg:col-span-none lg:bg-transparent lg:px-6 lg:py-4 lg:text-slate-400">
                    <span class="lg:hidden uppercase tracking-wider text-[10px] font-bold text-indigo-400">No</span>
                    <span>#{{ $index + 1 }}</span>
                </td>

                <!-- Category Name -->
                <td class="py-2 lg:px-6 lg:py-4 col-span-1 block lg:table-cell lg:col-span-none">
                    <span class="block lg:hidden text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Category Name</span>
                    <span class="font-bold text-indigo-400">{{ $category->cat_name }}</span>
                </td>

                <!-- Creation Date -->
                <td class="py-2 lg:px-6 lg:py-4 col-span-1 block lg:table-cell lg:col-span-none">
                    <span class="block lg:hidden text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Creation Date</span>
                    <span class="text-slate-400">{{ $category->creation_date ?? 'N/A' }}</span>
                </td>

                <!-- Actions -->
                <td class="py-3 border-t border-slate-800/40 mt-2 lg:border-t-0 lg:mt-0 col-span-2 block lg:table-cell lg:col-span-none lg:px-6 lg:py-4 lg:text-right whitespace-nowrap">
                    <span class="block lg:hidden text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-2">Actions</span>
                    <div class="inline-flex gap-2">
                        <button @click="openEditModal({{ json_encode($category) }})" class="p-2.5 rounded-xl bg-slate-800 border border-slate-700/50 text-slate-300 hover:text-indigo-400 hover:border-indigo-500/50 transition-all" title="Edit Category">
                            <i class="fa-solid fa-pen-to-square text-sm"></i>
                        </button>
                        <button @click="openDeleteModal({{ $category->cat_id }})" class="p-2.5 rounded-xl bg-slate-800 border border-slate-700/50 text-slate-400 hover:text-rose-400 hover:border-rose-500/50 transition-all" title="Remove Category">
                            <i class="fa-solid fa-trash-can text-sm"></i>
                        </button>
                    </div>
                </td>
            </tr>
        @empty
            <tr class="block lg:table-row w-full bg-slate-900/30 border border-slate-800/60 rounded-2xl p-6 lg:p-0">
                <td colspan="4" class="px-6 py-8 text-center text-slate-500 font-medium block lg:table-cell w-full">No categories found. Click 'Add Category' to create your first item.</td>
            </tr>
        @endforelse
    </x-admin.table>

    <!-- 1. Add Category Modal -->
    <x-admin.modal id="showAddModal" title="Add Category" icon="fa-solid fa-plus-circle">
        <form method="POST" action="{{ route('admin.categories.store') }}" class="space-y-4">
            @csrf

            <!-- Category Name -->
            <div>
                <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Category Name</label>
                <input type="text" 
                       name="cat_name" 
                       required 
                       placeholder="Enter category name"
                       class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-sm text-slate-300 focus:outline-none focus:ring-1 focus:ring-indigo-500">
            </div>

            <div class="flex gap-4 pt-4 border-t border-slate-850">
                <button type="button" @click="showAddModal = false" class="flex-1 py-3 px-4 rounded-xl border border-slate-800 hover:bg-slate-800/50 text-slate-300 font-semibold text-sm transition-all">Cancel</button>
                <button type="submit" class="flex-1 py-3 px-4 rounded-xl bg-orange-600 hover:bg-orange-700 text-white font-bold text-sm shadow-xl shadow-orange-600/10 hover:shadow-orange-600/25 transition-all">Save Category</button>
            </div>
        </form>
    </x-admin.modal>

    <!-- 2. Edit Category Modal -->
    <x-admin.modal id="showEditModal" title="Edit Category" icon="fa-solid fa-pen-to-square">
        <form method="POST" :action="'{{ route('admin.categories.index') }}/' + editCatId" class="space-y-4">
            @csrf
            @method('PUT')

            <!-- Category Name -->
            <div>
                <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Category Name</label>
                <input type="text" 
                       name="cat_name" 
                       required 
                       x-model="editCatName"
                       placeholder="Enter category name"
                       class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-sm text-slate-300 focus:outline-none focus:ring-1 focus:ring-indigo-500">
            </div>

            <div class="flex gap-4 pt-4 border-t border-slate-850">
                <button type="button" @click="showEditModal = false" class="flex-1 py-3 px-4 rounded-xl border border-slate-800 hover:bg-slate-800/50 text-slate-300 font-semibold text-sm transition-all">Cancel</button>
                <button type="submit" class="flex-1 py-3 px-4 rounded-xl bg-orange-600 hover:bg-orange-700 text-white font-bold text-sm shadow-xl shadow-orange-600/10 hover:shadow-orange-600/25 transition-all">Save Changes</button>
            </div>
        </form>
    </x-admin.modal>

    <!-- 3. Delete Confirmation Modal -->
    <x-admin.modal id="showDeleteModal" title="Remove Category?" maxWidth="sm">
        <div class="text-center space-y-3">
            <div class="inline-flex p-3 rounded-2xl bg-rose-500/10 text-rose-500 mb-2">
                <i class="fa-solid fa-trash-can text-2xl animate-bounce"></i>
            </div>
            <p class="text-sm text-slate-400">Do you really want to remove this category? This action will set the category status to inactive.</p>
        </div>

        <form method="POST" :action="'{{ route('admin.categories.index') }}/' + deleteCatId">
            @csrf
            @method('DELETE')
            
            <div class="flex gap-4 pt-2">
                <button type="button" @click="showDeleteModal = false" class="flex-1 py-3 px-4 rounded-xl border border-slate-800 hover:bg-slate-800/50 text-slate-300 font-semibold text-sm transition-all">Cancel</button>
                <button type="submit" class="flex-1 py-3 px-4 rounded-xl bg-gradient-to-r from-rose-500 to-red-600 text-white font-bold text-sm shadow-xl shadow-rose-500/10 hover:shadow-rose-500/25 transition-all">Delete</button>
            </div>
        </form>
    </x-admin.modal>

</div>
@endsection
