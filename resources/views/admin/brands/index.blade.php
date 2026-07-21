@extends('layouts.admin')

@section('content')
<div x-data="{ 
    showAddModal: false, 
    showEditModal: false, 
    showDeleteModal: false,
    categories: @js($categories),
    subcategories: @js($subcategories),
    
    // Add form states
    addCatId: '',
    addScatId: '',
    addBrandName: '',
    
    // Edit form states
    editBrandId: '',
    editCatId: '',
    editScatId: '',
    editBrandName: '',
    
    // Delete states
    deleteBrandId: '',
    
    // Dependent dropdown helper
    getFilteredSubcategories(catId) {
        if (!catId) return [];
        return this.subcategories.filter(sub => sub.catid == catId);
    },
    
    openEditModal(brand) {
        this.editBrandId = brand.brand_id;
        this.editCatId = brand.catid || '';
        this.editScatId = brand.scatid || '';
        this.editBrandName = brand.brand_name;
        this.showEditModal = true;
    },
    
    openDeleteModal(brandId) {
        this.deleteBrandId = brandId;
        this.showDeleteModal = true;
    }
}" class="space-y-8 animate-fadeIn">

    <x-admin.header title="Manage Brands" description="Configure master brand registry with global or category-specific scope." glass="true">
        <x-slot:action>
            <x-admin.button @click="showAddModal = true" icon="fa-solid fa-plus-circle">
                Add Brand
            </x-admin.button>
        </x-slot:action>
    </x-admin.header>

    <!-- Brands Table Card -->
    @php
    $tableHeaders = [
        ['label' => 'No'],
        ['label' => 'Brand Name'],
        ['label' => 'Category Scope'],
        ['label' => 'Sub Category Scope'],
        ['label' => 'Actions', 'align' => 'right']
    ];
    @endphp

    <x-admin.table :headers="$tableHeaders" :collection="$brands" type="card" minWidth="650px">
        @forelse($brands as $index => $brand)
            <tr class="hover:bg-slate-800/30 transition-all block lg:table-row w-full bg-slate-900/30 border border-slate-800/60 rounded-2xl p-4 lg:p-0 mb-4 lg:mb-0 relative grid grid-cols-2 gap-x-4 gap-y-3 lg:grid-cols-none lg:bg-transparent lg:border-0 lg:hover:bg-slate-900/20 lg:transition-all">
                <!-- No -->
                <td class="col-span-2 bg-indigo-500/10 text-indigo-400 px-3 py-2 rounded-lg text-xs font-mono font-bold flex items-center justify-between lg:table-cell lg:col-span-none lg:bg-transparent lg:px-6 lg:py-4 lg:text-slate-400">
                    <span class="lg:hidden uppercase tracking-wider text-[10px] font-bold text-indigo-400">No</span>
                    <span>#{{ $brands->firstItem() + $index }}</span>
                </td>

                <!-- Brand Name -->
                <td class="py-2 lg:px-6 lg:py-4 col-span-1 block lg:table-cell lg:col-span-none">
                    <span class="block lg:hidden text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Brand Name</span>
                    <span class="font-bold text-slate-100 text-sm">{{ $brand->brand_name }}</span>
                </td>

                <!-- Category Scope -->
                <td class="py-2 lg:px-6 lg:py-4 col-span-1 block lg:table-cell lg:col-span-none">
                    <span class="block lg:hidden text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Category</span>
                    @if($brand->category)
                        <span class="font-semibold text-slate-300 text-xs">{{ $brand->category->cat_name }}</span>
                    @else
                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-md bg-indigo-500/10 text-indigo-400 border border-indigo-500/20">Global (All Categories)</span>
                    @endif
                </td>

                <!-- Sub Category Scope -->
                <td class="py-2 lg:px-6 lg:py-4 col-span-2 block lg:table-cell lg:col-span-none">
                    <span class="block lg:hidden text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Sub Category</span>
                    @if($brand->subcategory)
                        <span class="text-slate-300 text-xs">{{ $brand->subcategory->subcategoryname }}</span>
                    @else
                        <span class="text-[10px] font-semibold text-slate-500">All Subcategories</span>
                    @endif
                </td>

                <!-- Actions -->
                <td class="py-3 border-t border-slate-800/40 mt-2 lg:border-t-0 lg:mt-0 col-span-2 block lg:table-cell lg:col-span-none lg:px-6 lg:py-4 lg:text-right whitespace-nowrap">
                    <span class="block lg:hidden text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-2">Actions</span>
                    <div class="inline-flex gap-2">
                        <button @click="openEditModal({{ json_encode($brand) }})" class="p-2.5 rounded-xl bg-slate-800 border border-slate-700/50 text-slate-300 hover:text-indigo-400 hover:border-indigo-500/50 transition-all" title="Edit Brand">
                            <i class="fa-solid fa-pen-to-square text-sm"></i>
                        </button>
                        <button @click="openDeleteModal({{ $brand->brand_id }})" class="p-2.5 rounded-xl bg-slate-800 border border-slate-700/50 text-slate-400 hover:text-rose-400 hover:border-rose-500/50 transition-all" title="Remove Brand">
                            <i class="fa-solid fa-trash-can text-sm"></i>
                        </button>
                    </div>
                </td>
            </tr>
        @empty
            <tr class="block lg:table-row w-full bg-slate-900/30 border border-slate-800/60 rounded-2xl p-6 lg:p-0">
                <td colspan="5" class="px-6 py-8 text-center text-slate-500 font-medium block lg:table-cell w-full">No brands found. Click 'Add Brand' to create your first item.</td>
            </tr>
        @endforelse
    </x-admin.table>

    <!-- 1. Add Brand Modal -->
    <x-admin.modal id="showAddModal" title="Add Master Brand" icon="fa-solid fa-plus-circle">
        <form method="POST" action="{{ route('admin.brands.store') }}" class="space-y-4">
            @csrf

            <!-- Brand Name -->
            <div>
                <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Brand Name <span class="text-rose-500">*</span></label>
                <input type="text" 
                       name="brand_name" 
                       required 
                       x-model="addBrandName"
                       placeholder="e.g. Samsung, Apple, Sony"
                       class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-sm text-slate-300 focus:outline-none focus:ring-1 focus:ring-indigo-500">
            </div>

            <!-- Optional Category Scope -->
            <div>
                <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Category Scope (Optional)</label>
                <select name="catid" 
                        x-model="addCatId"
                        class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-sm text-slate-300 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                    <option value="">~ Global (Available Across All Categories) ~</option>
                    <template x-for="cat in categories" :key="cat.cat_id">
                        <option :value="cat.cat_id" x-text="cat.cat_name"></option>
                    </template>
                </select>
            </div>

            <!-- Optional Subcategory Scope -->
            <div>
                <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Sub Category Scope (Optional)</label>
                <select name="scatid" 
                        x-model="addScatId"
                        :disabled="!addCatId"
                        class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-sm text-slate-300 focus:outline-none focus:ring-1 focus:ring-indigo-500 disabled:opacity-40 disabled:cursor-not-allowed">
                    <option value="">~ All Subcategories ~</option>
                    <template x-for="sub in getFilteredSubcategories(addCatId)" :key="sub.id">
                        <option :value="sub.id" x-text="sub.subcategoryname"></option>
                    </template>
                </select>
            </div>

            <div class="flex gap-4 pt-4 border-t border-slate-850">
                <button type="button" @click="showAddModal = false" class="flex-1 py-3 px-4 rounded-xl border border-slate-800 hover:bg-slate-800/50 text-slate-300 font-semibold text-sm transition-all">Cancel</button>
                <button type="submit" class="flex-1 py-3 px-4 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-sm shadow-xl shadow-indigo-600/10 hover:shadow-indigo-600/25 transition-all">Save Brand</button>
            </div>
        </form>
    </x-admin.modal>

    <!-- 2. Edit Brand Modal -->
    <x-admin.modal id="showEditModal" title="Edit Brand" icon="fa-solid fa-pen-to-square">
        <form method="POST" :action="'{{ route('admin.brands.index') }}/' + editBrandId" class="space-y-4">
            @csrf
            @method('PUT')

            <!-- Brand Name -->
            <div>
                <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Brand Name <span class="text-rose-500">*</span></label>
                <input type="text" 
                       name="brand_name" 
                       required 
                       x-model="editBrandName"
                       placeholder="Enter brand name"
                       class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-sm text-slate-300 focus:outline-none focus:ring-1 focus:ring-indigo-500">
            </div>

            <!-- Optional Category Scope -->
            <div>
                <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Category Scope (Optional)</label>
                <select name="catid" 
                        x-model="editCatId"
                        class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-sm text-slate-300 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                    <option value="">~ Global (Available Across All Categories) ~</option>
                    <template x-for="cat in categories" :key="cat.cat_id">
                        <option :value="cat.cat_id" x-text="cat.cat_name"></option>
                    </template>
                </select>
            </div>

            <!-- Optional Subcategory Scope -->
            <div>
                <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Sub Category Scope (Optional)</label>
                <select name="scatid" 
                        x-model="editScatId"
                        :disabled="!editCatId"
                        class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-sm text-slate-300 focus:outline-none focus:ring-1 focus:ring-indigo-500 disabled:opacity-40 disabled:cursor-not-allowed">
                    <option value="">~ All Subcategories ~</option>
                    <template x-for="sub in getFilteredSubcategories(editCatId)" :key="sub.id">
                        <option :value="sub.id" x-text="sub.subcategoryname"></option>
                    </template>
                </select>
            </div>

            <div class="flex gap-4 pt-4 border-t border-slate-850">
                <button type="button" @click="showEditModal = false" class="flex-1 py-3 px-4 rounded-xl border border-slate-800 hover:bg-slate-800/50 text-slate-300 font-semibold text-sm transition-all">Cancel</button>
                <button type="submit" class="flex-1 py-3 px-4 rounded-xl bg-orange-600 hover:bg-orange-700 text-white font-bold text-sm shadow-xl shadow-orange-600/10 hover:shadow-orange-600/25 transition-all">Save Changes</button>
            </div>
        </form>
    </x-admin.modal>

    <!-- 3. Delete Confirmation Modal -->
    <x-admin.modal id="showDeleteModal" title="Remove Brand?" maxWidth="sm">
        <div class="text-center space-y-3">
            <div class="inline-flex p-3 rounded-2xl bg-rose-500/10 text-rose-500 mb-2">
                <i class="fa-solid fa-trash-can text-2xl animate-bounce"></i>
            </div>
            <p class="text-sm text-slate-400">Do you really want to remove this brand? This action will set the brand status to inactive.</p>
        </div>

        <form method="POST" :action="'{{ route('admin.brands.index') }}/' + deleteBrandId">
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
