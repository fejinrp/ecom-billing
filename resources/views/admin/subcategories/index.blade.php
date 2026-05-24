@extends('layouts.admin')

@section('content')
<div x-data="{ 
    showAddModal: false, 
    showEditModal: false, 
    showDeleteModal: false,
    categories: @js($categories),
    
    // Add form states
    addCatId: '',
    addSubcatName: '',
    
    // Edit form states
    editSubcatId: '',
    editCatId: '',
    editSubcatName: '',
    
    // Delete states
    deleteSubcatId: '',
    
    openEditModal(sub) {
        this.editSubcatId = sub.id;
        this.editCatId = sub.catid;
        this.editSubcatName = sub.subcategoryname;
        this.showEditModal = true;
    },
    
    openDeleteModal(subId) {
        this.deleteSubcatId = subId;
        this.showDeleteModal = true;
    }
}" class="space-y-8 animate-fadeIn">

    <x-admin.header title="Manage Subcategories" description="Configure and manage subcategories linked to parent categories." glass="true">
        <x-slot:action>
            <x-admin.button @click="showAddModal = true" icon="fa-solid fa-plus-circle">
                Add Subcategory
            </x-admin.button>
        </x-slot:action>
    </x-admin.header>

    <!-- Subcategories Table Card -->
    @php
    $tableHeaders = [
        ['label' => 'No'],
        ['label' => 'Category Name'],
        ['label' => 'Sub Category Name'],
        ['label' => 'Creation Date'],
        ['label' => 'Actions', 'align' => 'right']
    ];
    @endphp

    <x-admin.table :headers="$tableHeaders" :collection="$subcategories" type="card" minWidth="650px">
        @forelse($subcategories as $index => $sub)
            <tr class="hover:bg-slate-800/30 transition-all block lg:table-row w-full bg-slate-900/30 border border-slate-800/60 rounded-2xl p-4 lg:p-0 mb-4 lg:mb-0 relative grid grid-cols-2 gap-x-4 gap-y-3 lg:grid-cols-none lg:bg-transparent lg:border-0 lg:hover:bg-slate-900/20 lg:transition-all">
                <!-- No -->
                <td class="col-span-2 bg-indigo-500/10 text-indigo-400 px-3 py-2 rounded-lg text-xs font-mono font-bold flex items-center justify-between lg:table-cell lg:col-span-none lg:bg-transparent lg:px-6 lg:py-4 lg:text-slate-400">
                    <span class="lg:hidden uppercase tracking-wider text-[10px] font-bold text-indigo-400">No</span>
                    <span>#{{ $index + 1 }}</span>
                </td>

                <!-- Category Name -->
                <td class="py-2 lg:px-6 lg:py-4 col-span-1 block lg:table-cell lg:col-span-none">
                    <span class="block lg:hidden text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Category</span>
                    <span class="font-semibold text-slate-200">{{ $sub->category ? $sub->category->cat_name : 'No Category' }}</span>
                </td>

                <!-- Sub Category Name -->
                <td class="py-2 lg:px-6 lg:py-4 col-span-1 block lg:table-cell lg:col-span-none">
                    <span class="block lg:hidden text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Subcategory</span>
                    <span class="font-bold text-indigo-400">{{ $sub->subcategoryname }}</span>
                </td>

                <!-- Creation Date -->
                <td class="py-2 lg:px-6 lg:py-4 col-span-2 block lg:table-cell lg:col-span-none">
                    <span class="block lg:hidden text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Creation Date</span>
                    <span class="text-slate-400">{{ $sub->creationdate ?? 'N/A' }}</span>
                </td>

                <!-- Actions -->
                <td class="py-3 border-t border-slate-800/40 mt-2 lg:border-t-0 lg:mt-0 col-span-2 block lg:table-cell lg:col-span-none lg:px-6 lg:py-4 lg:text-right whitespace-nowrap">
                    <span class="block lg:hidden text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-2">Actions</span>
                    <div class="inline-flex gap-2">
                        <button @click="openEditModal({{ json_encode($sub) }})" class="p-2.5 rounded-xl bg-slate-800 border border-slate-700/50 text-slate-300 hover:text-indigo-400 hover:border-indigo-500/50 transition-all" title="Edit Subcategory">
                            <i class="fa-solid fa-pen-to-square text-sm"></i>
                        </button>
                        <button @click="openDeleteModal({{ $sub->id }})" class="p-2.5 rounded-xl bg-slate-800 border border-slate-700/50 text-slate-400 hover:text-rose-400 hover:border-rose-500/50 transition-all" title="Remove Subcategory">
                            <i class="fa-solid fa-trash-can text-sm"></i>
                        </button>
                    </div>
                </td>
            </tr>
        @empty
            <tr class="block lg:table-row w-full bg-slate-900/30 border border-slate-800/60 rounded-2xl p-6 lg:p-0">
                <td colspan="5" class="px-6 py-8 text-center text-slate-500 font-medium block lg:table-cell w-full">No subcategories found. Click 'Add Subcategory' to create your first item.</td>
            </tr>
        @endforelse
    </x-admin.table>

    <!-- 1. Add Subcategory Modal -->
    <x-admin.modal id="showAddModal" title="Add Subcategory" icon="fa-solid fa-plus-circle">
        <form method="POST" action="{{ route('admin.subcategories.store') }}" class="space-y-4">
            @csrf

            <!-- Category -->
            <div>
                <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Parent Category</label>
                <select name="catid" 
                        x-model="addCatId"
                        required
                        class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-sm text-slate-300 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                    <option value="">~ Select Category ~</option>
                    <template x-for="cat in categories" :key="cat.cat_id">
                        <option :value="cat.cat_id" x-text="cat.cat_name"></option>
                    </template>
                </select>
            </div>

            <!-- Subcategory Name -->
            <div>
                <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Subcategory Name</label>
                <input type="text" 
                       name="subcategoryname" 
                       required 
                       placeholder="Enter subcategory name"
                       class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-sm text-slate-300 focus:outline-none focus:ring-1 focus:ring-indigo-500">
            </div>

            <div class="flex gap-4 pt-4 border-t border-slate-850">
                <button type="button" @click="showAddModal = false" class="flex-1 py-3 px-4 rounded-xl border border-slate-800 hover:bg-slate-800/50 text-slate-300 font-semibold text-sm transition-all">Cancel</button>
                <button type="submit" class="flex-1 py-3 px-4 rounded-xl bg-orange-600 hover:bg-orange-700 text-white font-bold text-sm shadow-xl shadow-orange-600/10 hover:shadow-orange-600/25 transition-all">Save Subcategory</button>
            </div>
        </form>
    </x-admin.modal>

    <!-- 2. Edit Subcategory Modal -->
    <x-admin.modal id="showEditModal" title="Edit Subcategory" icon="fa-solid fa-pen-to-square">
        <form method="POST" :action="'{{ route('admin.subcategories.index') }}/' + editSubcatId" class="space-y-4">
            @csrf
            @method('PUT')

            <!-- Category -->
            <div>
                <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Parent Category</label>
                <select name="catid" 
                        x-model="editCatId"
                        required
                        class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-sm text-slate-300 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                    <option value="">~ Select Category ~</option>
                    <template x-for="cat in categories" :key="cat.cat_id">
                        <option :value="cat.cat_id" x-text="cat.cat_name"></option>
                    </template>
                </select>
            </div>

            <!-- Subcategory Name -->
            <div>
                <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Subcategory Name</label>
                <input type="text" 
                       name="subcategoryname" 
                       required 
                       x-model="editSubcatName"
                       placeholder="Enter subcategory name"
                       class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-sm text-slate-300 focus:outline-none focus:ring-1 focus:ring-indigo-500">
            </div>

            <div class="flex gap-4 pt-4 border-t border-slate-850">
                <button type="button" @click="showEditModal = false" class="flex-1 py-3 px-4 rounded-xl border border-slate-800 hover:bg-slate-800/50 text-slate-300 font-semibold text-sm transition-all">Cancel</button>
                <button type="submit" class="flex-1 py-3 px-4 rounded-xl bg-orange-600 hover:bg-orange-700 text-white font-bold text-sm shadow-xl shadow-orange-600/10 hover:shadow-orange-600/25 transition-all">Save Changes</button>
            </div>
        </form>
    </x-admin.modal>

    <!-- 3. Delete Confirmation Modal -->
    <x-admin.modal id="showDeleteModal" title="Remove Subcategory?" maxWidth="sm">
        <div class="text-center space-y-3">
            <div class="inline-flex p-3 rounded-2xl bg-rose-500/10 text-rose-500 mb-2">
                <i class="fa-solid fa-trash-can text-2xl animate-bounce"></i>
            </div>
            <p class="text-sm text-slate-400">Do you really want to remove this subcategory? This action will set the status to inactive.</p>
        </div>

        <form method="POST" :action="'{{ route('admin.subcategories.index') }}/' + deleteSubcatId">
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
