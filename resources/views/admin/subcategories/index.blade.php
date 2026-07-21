@extends('layouts.admin')

@section('content')
<div x-data="{ 
    showAddModal: false, 
    showEditModal: false, 
    showMoveModal: false,
    showDeleteModal: false,
    
    categories: @js($categories),
    allSubcategories: @js($allSubcategories),
    
    // Add form states
    addCatId: '',
    addParentSubcatId: '',
    addParentSubcatName: '',
    addSubcatName: '',
    
    // Edit form states
    editSubcatId: '',
    editCatId: '',
    editParentSubcatId: '',
    editSubcatName: '',
    
    // Move form states
    moveSubcatId: '',
    moveSubcatName: '',
    moveTargetParentId: '',
    
    // Delete states
    deleteSubcatId: '',
    deleteSubcatName: '',
    deleteMode: 'branch',
    
    openAddChildModal(parentSub) {
        this.addParentSubcatId = parentSub.id;
        this.addParentSubcatName = parentSub.subcategoryname;
        this.addCatId = parentSub.catid;
        this.addSubcatName = '';
        this.showAddModal = true;
    },
    
    openAddRootModal() {
        this.addParentSubcatId = '';
        this.addParentSubcatName = '';
        this.addCatId = '';
        this.addSubcatName = '';
        this.showAddModal = true;
    },
    
    openEditModal(sub) {
        this.editSubcatId = sub.id;
        this.editCatId = sub.catid;
        this.editParentSubcatId = sub.parent_subcategory_id || '';
        this.editSubcatName = sub.subcategoryname;
        this.showEditModal = true;
    },

    openMoveModal(sub) {
        this.moveSubcatId = sub.id;
        this.moveSubcatName = sub.subcategoryname;
        this.moveTargetParentId = sub.parent_subcategory_id || '';
        this.showMoveModal = true;
    },
    
    openDeleteModal(sub) {
        this.deleteSubcatId = sub.id;
        this.deleteSubcatName = sub.subcategoryname;
        this.deleteMode = 'branch';
        this.showDeleteModal = true;
    }
}" class="space-y-8 animate-fadeIn">

    <x-admin.header title="Manage Subcategory Tree" description="Configure and manage multi-level nested subcategories with unlimited depth." glass="true">
        <x-slot:action>
            <x-admin.button @click="openAddRootModal()" icon="fa-solid fa-plus-circle">
                Add Root Subcategory
            </x-admin.button>
        </x-slot:action>
    </x-admin.header>

    @if(session('error'))
        <div class="p-4 rounded-xl bg-rose-500/10 border border-rose-500/20 text-rose-400 text-sm font-semibold flex items-center gap-3">
            <i class="fa-solid fa-triangle-exclamation text-base"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <!-- Recursive Tree Container -->
    <div class="bg-white dark:bg-slate-900/60 border border-slate-200 dark:border-slate-800/80 rounded-2xl p-6 backdrop-blur-xl space-y-4 shadow-xl">
        <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-800/60 pb-4 mb-4">
            <h3 class="text-lg font-bold text-slate-800 dark:text-slate-100 flex items-center gap-2">
                <i class="fa-solid fa-sitemap text-indigo-600 dark:text-indigo-400"></i>
                <span>Hierarchical Category Structure</span>
            </h3>
            <span class="text-xs text-slate-600 dark:text-slate-400 bg-slate-100 dark:bg-slate-800/80 px-3 py-1 rounded-full border border-slate-200 dark:border-slate-700/50 font-medium">
                Unlimited Nesting Supported
            </span>
        </div>

        @if($subcategories->isEmpty())
            <div class="text-center py-12 text-slate-500">
                <i class="fa-solid fa-folder-open text-4xl mb-3 block text-slate-600"></i>
                <p class="font-medium text-sm">No root subcategories found. Click 'Add Root Subcategory' to start building your tree.</p>
            </div>
        @else
            <div class="space-y-3">
                @foreach($subcategories as $sub)
                    @include('admin.subcategories.partials.tree_item', ['sub' => $sub, 'depth' => 0])
                @endforeach
            </div>
        @endif
    </div>

    <!-- 1. Add Subcategory Modal -->
    <x-admin.modal id="showAddModal" title="Add Subcategory Node" icon="fa-solid fa-plus-circle">
        <form method="POST" action="{{ route('admin.subcategories.store') }}" class="space-y-4">
            @csrf
            
            <template x-if="addParentSubcatId">
                <input type="hidden" name="parent_subcategory_id" :value="addParentSubcatId">
            </template>

            <div x-show="addParentSubcatName" class="p-3 rounded-xl bg-indigo-500/10 border border-indigo-500/20 text-indigo-300 text-xs font-semibold flex items-center gap-2">
                <i class="fa-solid fa-network-wired"></i>
                <span>Parent Node: <strong class="text-white" x-text="addParentSubcatName"></strong></span>
            </div>

            <!-- Top-level Category -->
            <div>
                <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Top-level Category</label>
                <select name="catid" 
                        x-model="addCatId"
                        :required="!addParentSubcatId"
                        class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-sm text-slate-300 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                    <option value="">~ Select Category ~</option>
                    <template x-for="cat in categories" :key="cat.cat_id">
                        <option :value="cat.cat_id" x-text="cat.cat_name"></option>
                    </template>
                </select>
            </div>

            <!-- Direct Parent Subcategory (Optional override) -->
            <div x-show="!addParentSubcatId">
                <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Parent Subcategory (Optional)</label>
                <select name="parent_subcategory_id" 
                        x-model="addParentSubcatId"
                        class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-sm text-slate-300 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                    <option value="">~ Top-level Node (No Parent) ~</option>
                    <template x-for="item in allSubcategories" :key="item.id">
                        <option :value="item.id" x-text="item.formatted_name"></option>
                    </template>
                </select>
            </div>

            <!-- Subcategory Name -->
            <div>
                <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Subcategory Name</label>
                <input type="text" 
                       name="subcategoryname" 
                       required 
                       x-model="addSubcatName"
                       placeholder="Enter subcategory name"
                       class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-sm text-slate-300 focus:outline-none focus:ring-1 focus:ring-indigo-500">
            </div>

            <div class="flex gap-4 pt-4 border-t border-slate-850">
                <button type="button" @click="showAddModal = false" class="flex-1 py-3 px-4 rounded-xl border border-slate-800 hover:bg-slate-800/50 text-slate-300 font-semibold text-sm transition-all">Cancel</button>
                <button type="submit" class="flex-1 py-3 px-4 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-sm shadow-xl shadow-indigo-600/10 hover:shadow-indigo-600/25 transition-all">Save Node</button>
            </div>
        </form>
    </x-admin.modal>

    <!-- 2. Edit Subcategory Modal -->
    <x-admin.modal id="showEditModal" title="Edit Subcategory Node" icon="fa-solid fa-pen-to-square">
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

            <!-- Parent Subcategory -->
            <div>
                <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Parent Subcategory</label>
                <select name="parent_subcategory_id" 
                        x-model="editParentSubcatId"
                        class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-sm text-slate-300 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                    <option value="">~ Root Node (No Parent Subcategory) ~</option>
                    <template x-for="item in allSubcategories" :key="item.id">
                        <option :value="item.id" :disabled="item.id == editSubcatId" x-text="item.formatted_name + (item.id == editSubcatId ? ' (Current Node)' : '')"></option>
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

    <!-- 3. Move Subcategory Modal -->
    <x-admin.modal id="showMoveModal" title="Move Node in Hierarchy" icon="fa-solid fa-arrows-up-down-left-right">
        <form method="POST" :action="'{{ route('admin.subcategories.index') }}/' + moveSubcatId + '/move'" class="space-y-4">
            @csrf

            <div class="p-3 rounded-xl bg-amber-500/10 border border-amber-500/20 text-amber-300 text-xs font-medium space-y-1">
                <div class="font-bold flex items-center gap-2">
                    <i class="fa-solid fa-circle-info"></i>
                    <span>Moving: <span class="text-white" x-text="moveSubcatName"></span></span>
                </div>
                <p class="text-amber-400/80">Select the new parent subcategory. Circular references are automatically validated and rejected.</p>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Target Parent Subcategory</label>
                <select name="parent_subcategory_id" 
                        x-model="moveTargetParentId"
                        class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-sm text-slate-300 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                    <option value="">~ Make Root Node (No Parent) ~</option>
                    <template x-for="item in allSubcategories" :key="item.id">
                        <option :value="item.id" :disabled="item.id == moveSubcatId" x-text="item.formatted_name"></option>
                    </template>
                </select>
            </div>

            <div class="flex gap-4 pt-4 border-t border-slate-850">
                <button type="button" @click="showMoveModal = false" class="flex-1 py-3 px-4 rounded-xl border border-slate-800 hover:bg-slate-800/50 text-slate-300 font-semibold text-sm transition-all">Cancel</button>
                <button type="submit" class="flex-1 py-3 px-4 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-sm shadow-xl shadow-indigo-600/10 hover:shadow-indigo-600/25 transition-all">Confirm Move</button>
            </div>
        </form>
    </x-admin.modal>

    <!-- 4. Delete Confirmation Modal -->
    <x-admin.modal id="showDeleteModal" title="Remove Subcategory Node?" maxWidth="md">
        <form method="POST" :action="'{{ route('admin.subcategories.index') }}/' + deleteSubcatId" class="space-y-4">
            @csrf
            @method('DELETE')

            <div class="text-center space-y-2">
                <div class="inline-flex p-3 rounded-2xl bg-rose-500/10 text-rose-500 mb-1">
                    <i class="fa-solid fa-trash-can text-2xl"></i>
                </div>
                <h4 class="text-base font-bold text-slate-200" x-text="'Deleting ' + deleteSubcatName"></h4>
                <p class="text-xs text-slate-400">Choose how to process child subcategories belonging to this node:</p>
            </div>

            <!-- Mode Selection Radio -->
            <div class="space-y-2 pt-2">
                <label class="flex items-start gap-3 p-3.5 rounded-xl border border-slate-800 bg-slate-950/60 hover:border-slate-700 cursor-pointer transition-all">
                    <input type="radio" name="delete_mode" value="branch" x-model="deleteMode" class="mt-1 text-rose-500 focus:ring-rose-500">
                    <div>
                        <span class="block text-sm font-bold text-slate-200">Delete Entire Branch</span>
                        <span class="block text-xs text-slate-400 mt-0.5">Recursively soft-delete this subcategory and all of its nested child subcategories.</span>
                    </div>
                </label>

                <label class="flex items-start gap-3 p-3.5 rounded-xl border border-slate-800 bg-slate-950/60 hover:border-slate-700 cursor-pointer transition-all">
                    <input type="radio" name="delete_mode" value="reparent" x-model="deleteMode" class="mt-1 text-indigo-500 focus:ring-indigo-500">
                    <div>
                        <span class="block text-sm font-bold text-slate-200">Move Children to Parent</span>
                        <span class="block text-xs text-slate-400 mt-0.5">Promote direct child subcategories up one level to this node's parent.</span>
                    </div>
                </label>
            </div>

            <div class="flex gap-4 pt-4 border-t border-slate-850">
                <button type="button" @click="showDeleteModal = false" class="flex-1 py-3 px-4 rounded-xl border border-slate-800 hover:bg-slate-800/50 text-slate-300 font-semibold text-sm transition-all">Cancel</button>
                <button type="submit" class="flex-1 py-3 px-4 rounded-xl bg-gradient-to-r from-rose-500 to-red-600 text-white font-bold text-sm shadow-xl shadow-rose-500/10 hover:shadow-rose-500/25 transition-all">Confirm Delete</button>
            </div>
        </form>
    </x-admin.modal>

</div>
@endsection
