@extends('layouts.admin', ['title' => 'State GST Codes'])

@section('content')
<div class="space-y-6" x-data="{ 
    showAddModal: false, 
    showEditModal: false, 
    showDeleteModal: false,
    activeState: { sid: '', sname: '', scode: '' }
}">

    <!-- Header Section -->
    <x-admin.header title="State GST Settings" description="Configure and map Indian states and union territories with their regional GST state codes." glass="false">
        <x-slot:action>
            <x-admin.button @click="showAddModal = true" icon="fa-solid fa-plus-circle">
                Add State Code
            </x-admin.button>
        </x-slot:action>
    </x-admin.header>

    <!-- Error/Validation alerts -->
    @if ($errors->any())
        <div class="p-4 rounded-xl bg-rose-500/10 border border-rose-500/20 text-sm text-rose-400 space-y-1">
            <span class="font-bold text-slate-200">Validation Alert:</span>
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- State GST Table Card -->
    @php
    $tableHeaders = [
        ['label' => 'No'],
        ['label' => 'State Name'],
        ['label' => 'State Code (GST)'],
        ['label' => 'Actions', 'align' => 'right']
    ];
    @endphp

    <x-admin.table :headers="$tableHeaders" :collection="$stategsts" type="card" minWidth="500px">
        @forelse($stategsts as $index => $state)
            <tr class="hover:bg-slate-100 dark:hover:bg-slate-800/30 transition-all block lg:table-row w-full bg-white dark:bg-slate-900/30 border border-slate-200 dark:border-slate-800/60 rounded-2xl p-4 lg:p-0 mb-4 lg:mb-0 relative grid grid-cols-2 gap-x-4 gap-y-3 lg:grid-cols-none lg:bg-transparent lg:border-0 lg:hover:bg-slate-100 dark:lg:hover:bg-slate-900/20 lg:transition-all">
                <!-- No -->
                <td class="col-span-2 bg-indigo-500/10 text-indigo-650 dark:text-indigo-400 px-3 py-2 rounded-lg text-xs font-mono font-bold flex items-center justify-between lg:table-cell lg:col-span-none lg:bg-transparent lg:px-6 lg:py-4 lg:text-slate-500 dark:lg:text-slate-400">
                    <span class="lg:hidden uppercase tracking-wider text-[10px] font-bold text-indigo-600 dark:text-indigo-400">No</span>
                    <span>#{{ ($stategsts->currentPage() - 1) * $stategsts->perPage() + $index + 1 }}</span>
                </td>

                <!-- State Name -->
                <td class="py-2 lg:px-6 lg:py-4 col-span-1 block lg:table-cell lg:col-span-none">
                    <span class="block lg:hidden text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">State Name</span>
                    <span class="font-bold text-indigo-600 dark:text-indigo-400">{{ $state->sname }}</span>
                </td>

                <!-- State Code -->
                <td class="py-2 lg:px-6 lg:py-4 col-span-1 block lg:table-cell lg:col-span-none font-mono">
                    <span class="block lg:hidden text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">State Code (GST)</span>
                    <span class="text-slate-750 dark:text-slate-200 bg-slate-100 dark:bg-slate-800 px-2.5 py-1 rounded-md border border-slate-250 dark:border-slate-700/50 text-xs font-bold">{{ $state->scode }}</span>
                </td>

                <!-- Actions -->
                <td class="py-3 border-t border-slate-855 mt-2 lg:border-t-0 lg:mt-0 col-span-2 block lg:table-cell lg:col-span-none lg:px-6 lg:py-4 lg:text-right whitespace-nowrap">
                    <span class="block lg:hidden text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-2">Actions</span>
                    <div class="inline-flex gap-2">
                        <!-- Edit -->
                        <button @click="
                                activeState = { sid: '{{ $state->sid }}', sname: '{{ $state->sname }}', scode: '{{ $state->scode }}' };
                                showEditModal = true;
                                " 
                                class="p-2.5 rounded-xl bg-slate-100 dark:bg-slate-800 border border-slate-250 dark:border-slate-700/50 text-slate-650 dark:text-slate-300 hover:text-indigo-650 dark:hover:text-indigo-400 hover:border-indigo-500/50 transition-all" 
                                title="Edit State GST">
                            <i class="fa-solid fa-pen-to-square text-sm"></i>
                        </button>
                        <!-- Remove -->
                        <button @click="
                                activeState = { sid: '{{ $state->sid }}', sname: '{{ $state->sname }}', scode: '{{ $state->scode }}' };
                                showDeleteModal = true;
                                " 
                                class="p-2.5 rounded-xl bg-slate-100 dark:bg-slate-800 border border-slate-250 dark:border-slate-700/50 text-slate-500 dark:text-slate-400 hover:text-rose-600 dark:hover:text-rose-450 hover:border-rose-500/50 transition-all" 
                                title="Remove State GST">
                            <i class="fa-solid fa-trash-can text-sm"></i>
                        </button>
                    </div>
                </td>
            </tr>
        @empty
            <tr class="block lg:table-row w-full bg-white dark:bg-slate-900/30 border border-slate-200 dark:border-slate-800/60 rounded-2xl p-6 lg:p-0">
                <td colspan="4" class="px-6 py-8 text-center text-slate-500 font-medium block lg:table-cell w-full">No state GST mappings found. Click 'Add State Code' to map your first record.</td>
            </tr>
        @endforelse
    </x-admin.table>

    <!-- 1. Add State Modal -->
    <x-admin.modal id="showAddModal" title="Add State GST Map" icon="fa-solid fa-plus-circle">
        <form method="POST" action="{{ route('admin.stategst.store') }}" class="space-y-4">
            @csrf

            <div>
                <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">State / UT Name</label>
                <input type="text" 
                       name="sname" 
                       required 
                       placeholder="e.g. KERALA, MAHARASHTRA" 
                       class="w-full px-4 py-3 bg-slate-950 border border-slate-800 rounded-xl text-slate-200 placeholder-slate-650 focus:outline-none focus:border-indigo-500 text-sm uppercase">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">State Code (2-Digit GST Code)</label>
                <input type="text" 
                       name="scode" 
                       required 
                       maxlength="10"
                       placeholder="e.g. 32, 27" 
                       class="w-full px-4 py-3 bg-slate-950 border border-slate-800 rounded-xl text-slate-200 placeholder-slate-650 focus:outline-none focus:border-indigo-500 text-sm font-mono">
            </div>

            <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-800/40">
                <button type="button" @click="showAddModal = false" class="flex-1 py-3 px-4 rounded-xl border border-slate-800 hover:bg-slate-800/50 text-slate-400 font-semibold text-sm transition-all">Cancel</button>
                <button type="submit" class="flex-1 py-3 px-4 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-sm shadow-xl shadow-indigo-600/10 active:scale-95 transition-all">Add Record</button>
            </div>
        </form>
    </x-admin.modal>

    <!-- 2. Edit State Modal -->
    <x-admin.modal id="showEditModal" title="Edit State GST Map" icon="fa-solid fa-pen-to-square">
        <form method="POST" :action="'/admin/stategst/' + activeState.sid" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">State / UT Name</label>
                <input type="text" 
                       name="sname" 
                       required 
                       x-model="activeState.sname"
                       placeholder="e.g. KERALA, MAHARASHTRA" 
                       class="w-full px-4 py-3 bg-slate-950 border border-slate-800 rounded-xl text-slate-200 placeholder-slate-650 focus:outline-none focus:border-indigo-500 text-sm uppercase">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">State Code (2-Digit GST Code)</label>
                <input type="text" 
                       name="scode" 
                       required 
                       maxlength="10"
                       x-model="activeState.scode"
                       placeholder="e.g. 32, 27" 
                       class="w-full px-4 py-3 bg-slate-950 border border-slate-800 rounded-xl text-slate-200 placeholder-slate-650 focus:outline-none focus:border-indigo-500 text-sm font-mono">
            </div>

            <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-800/40">
                <button type="button" @click="showEditModal = false" class="flex-1 py-3 px-4 rounded-xl border border-slate-800 hover:bg-slate-800/50 text-slate-400 font-semibold text-sm transition-all">Cancel</button>
                <button type="submit" class="flex-1 py-3 px-4 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-sm shadow-xl shadow-indigo-600/10 active:scale-95 transition-all">Save Changes</button>
            </div>
        </form>
    </x-admin.modal>

    <!-- 3. Delete Confirmation Modal -->
    <x-admin.modal id="showDeleteModal" title="Remove State GST Record" icon="fa-solid fa-triangle-exclamation">
        <form method="POST" :action="'/admin/stategst/' + activeState.sid" class="space-y-4">
            @csrf
            @method('DELETE')

            <div class="text-sm text-slate-400 space-y-2">
                <p>Are you sure you want to remove the GST record mapping for <span class="font-bold text-slate-200" x-text="activeState.sname"></span> (<span class="font-mono font-bold text-indigo-400" x-text="activeState.scode"></span>)?</p>
                <p class="text-xs text-rose-400/90 font-medium">Warning: This operation soft-removes the mapping but preserves past financial invoices mapped to this state.</p>
            </div>

            <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-800/40">
                <button type="button" @click="showDeleteModal = false" class="flex-1 py-3 px-4 rounded-xl border border-slate-800 hover:bg-slate-800/50 text-slate-400 font-semibold text-sm transition-all">Cancel</button>
                <button type="submit" class="flex-1 py-3 px-4 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-bold text-sm shadow-xl shadow-rose-600/10 active:scale-95 transition-all">Remove Mapping</button>
            </div>
        </form>
    </x-admin.modal>
</div>
@endsection
