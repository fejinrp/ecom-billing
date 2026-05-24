@extends('layouts.admin', ['title' => 'Manage Agents'])

@section('content')
<div class="space-y-6" x-data="{ 
    showAddModal: (new URLSearchParams(window.location.search)).get('action') === 'add' || (new URLSearchParams(window.location.search)).get('o') === 'add', 
    showEditModal: false, 
    showDeleteModal: false,
    activeAgent: { acode: '', aname: '', aplace: '', amobile: '', adate: '' }
}">

    <!-- Header Section -->
    <x-admin.header title="Manage Agents" description="View, register, and configure distribution partners and sales agents linked to MTL Mart." glass="false">
        <x-slot:action>
            <x-admin.button @click="showAddModal = true" icon="fa-solid fa-plus-circle">
                Add Agent
            </x-admin.button>
        </x-slot:action>
    </x-admin.header>

    <!-- Visual Dashboard Metrics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Card 1 -->
        <div class="p-6 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 flex items-center justify-between shadow-xl shadow-slate-950/5 dark:shadow-slate-950/20">
            <div>
                <span class="block text-xs font-bold text-slate-500 uppercase tracking-widest">Total Active Agents</span>
                <span class="block text-3xl font-extrabold text-slate-800 dark:text-slate-200 mt-1">{{ $agents->total() }}</span>
            </div>
            <div class="h-12 w-12 rounded-xl bg-indigo-500/10 text-indigo-400 flex items-center justify-center text-xl">
                <i class="fa-solid fa-users"></i>
            </div>
        </div>

        <!-- Card 2 -->
        <div class="p-6 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 flex items-center justify-between shadow-xl shadow-slate-950/5 dark:shadow-slate-950/20">
            <div>
                <span class="block text-xs font-bold text-slate-500 uppercase tracking-widest">Mobile Contact Coverage</span>
                <span class="block text-3xl font-extrabold text-emerald-500 dark:text-emerald-400 mt-1">
                    {{ $agents->where('amobile', '!=', '')->where('amobile', '!=', '0')->count() }}
                </span>
            </div>
            <div class="h-12 w-12 rounded-xl bg-emerald-500/10 text-emerald-400 flex items-center justify-center text-xl">
                <i class="fa-solid fa-phone"></i>
            </div>
        </div>

        <!-- Card 3 -->
        <div class="p-6 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 flex items-center justify-between shadow-xl shadow-slate-950/5 dark:shadow-slate-950/20">
            <div>
                <span class="block text-xs font-bold text-slate-500 uppercase tracking-widest">Last Updated Agent</span>
                <span class="block text-sm font-semibold text-slate-700 dark:text-slate-200 mt-2 truncate max-w-[200px]">
                    {{ $agents->first()?->aname ?? 'None' }}
                </span>
            </div>
            <div class="h-12 w-12 rounded-xl bg-purple-500/10 text-purple-400 flex items-center justify-center text-xl">
                <i class="fa-solid fa-user-check"></i>
            </div>
        </div>
    </div>

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

    <!-- Agents Table Card -->
    @php
    $tableHeaders = [
        ['label' => 'No'],
        ['label' => 'Agent Name'],
        ['label' => 'Address / Place'],
        ['label' => 'Mobile No'],
        ['label' => 'Registered Date'],
        ['label' => 'Actions', 'align' => 'right']
    ];
    @endphp

    <x-admin.table :headers="$tableHeaders" :collection="$agents" type="card" minWidth="600px">
        @forelse($agents as $index => $agent)
            <tr class="hover:bg-slate-100 dark:hover:bg-slate-800/30 transition-all block lg:table-row w-full bg-white dark:bg-slate-900/30 border border-slate-200 dark:border-slate-800/60 rounded-2xl p-4 lg:p-0 mb-4 lg:mb-0 relative grid grid-cols-2 gap-x-4 gap-y-3 lg:grid-cols-none lg:bg-transparent lg:border-0 lg:hover:bg-slate-900/20 lg:transition-all">
                <!-- No -->
                <td class="col-span-2 bg-indigo-500/10 text-indigo-400 px-3 py-2 rounded-lg text-xs font-mono font-bold flex items-center justify-between lg:table-cell lg:col-span-none lg:bg-transparent lg:px-6 lg:py-4 lg:text-slate-400">
                    <span class="lg:hidden uppercase tracking-wider text-[10px] font-bold text-indigo-400">No</span>
                    <span>#{{ ($agents->currentPage() - 1) * $agents->perPage() + $index + 1 }}</span>
                </td>

                <!-- Agent Name -->
                <td class="py-2 lg:px-6 lg:py-4 col-span-2 lg:col-span-none block lg:table-cell">
                    <span class="block lg:hidden text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Agent Name</span>
                    <span class="font-bold text-slate-800 dark:text-white tracking-wide text-sm">{{ $agent->aname }}</span>
                </td>

                <!-- Address -->
                <td class="py-2 lg:px-6 lg:py-4 col-span-2 lg:col-span-none block lg:table-cell">
                    <span class="block lg:hidden text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Address / Place</span>
                    <span class="text-slate-700 dark:text-slate-300 text-sm font-medium">{{ $agent->aplace }}</span>
                </td>

                <!-- Mobile -->
                <td class="py-2 lg:px-6 lg:py-4 col-span-1 block lg:table-cell lg:col-span-none">
                    <span class="block lg:hidden text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Mobile No</span>
                    @if($agent->amobile && $agent->amobile != '0')
                        <span class="text-slate-755 dark:text-slate-200 bg-slate-100 dark:bg-slate-800 px-2.5 py-1 rounded-md border border-slate-200 dark:border-slate-700/50 text-xs font-bold font-mono">
                            <i class="fa-solid fa-mobile-retro mr-1 opacity-50"></i>{{ $agent->amobile }}
                        </span>
                    @else
                        <span class="text-slate-500 text-xs italic">N/A</span>
                    @endif
                </td>

                <!-- Date -->
                <td class="py-2 lg:px-6 lg:py-4 col-span-1 block lg:table-cell lg:col-span-none font-mono text-sm">
                    <span class="block lg:hidden text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Registered Date</span>
                    <span class="text-slate-600 dark:text-slate-400">{{ \Carbon\Carbon::parse($agent->adate)->format('d-m-Y') }}</span>
                </td>

                <!-- Actions -->
                <td class="py-3 border-t border-slate-250 dark:border-slate-800/40 mt-2 lg:border-t-0 lg:mt-0 col-span-2 block lg:table-cell lg:col-span-none lg:px-6 lg:py-4 lg:text-right whitespace-nowrap">
                    <span class="block lg:hidden text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-2">Actions</span>
                    <div class="inline-flex gap-2">
                        <!-- Edit -->
                        <button @click="
                                activeAgent = { 
                                    acode: '{{ $agent->acode }}', 
                                    aname: '{{ $agent->aname }}', 
                                    aplace: '{{ $agent->aplace }}', 
                                    amobile: '{{ $agent->amobile }}', 
                                    adate: '{{ $agent->adate }}' 
                                };
                                showEditModal = true;
                                " 
                                class="p-2.5 rounded-xl bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700/50 text-slate-700 dark:text-slate-300 hover:text-indigo-600 dark:hover:text-indigo-400 hover:border-indigo-500/50 transition-all" 
                                title="Edit Agent">
                            <i class="fa-solid fa-pen-to-square text-sm"></i>
                        </button>
                        <!-- Remove -->
                        <button @click="
                                activeAgent = { 
                                    acode: '{{ $agent->acode }}', 
                                    aname: '{{ $agent->aname }}'
                                };
                                showDeleteModal = true;
                                " 
                                class="p-2.5 rounded-xl bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700/50 text-slate-700 dark:text-slate-400 hover:text-rose-600 dark:hover:text-rose-400 hover:border-rose-500/5 transition-all" 
                                title="Remove Agent">
                            <i class="fa-solid fa-trash-can text-sm"></i>
                        </button>
                    </div>
                </td>
            </tr>
        @empty
            <tr class="block lg:table-row w-full bg-white dark:bg-slate-900/30 border border-slate-200 dark:border-slate-800/60 rounded-2xl p-6 lg:p-0">
                <td colspan="6" class="px-6 py-8 text-center text-slate-500 font-medium block lg:table-cell w-full">No active agents found. Click 'Add Agent' to register your first partner.</td>
            </tr>
        @endforelse
    </x-admin.table>

    <!-- 1. Add Agent Modal -->
    <x-admin.modal id="showAddModal" title="Register New Agent" icon="fa-solid fa-user-plus">
        <form method="POST" action="{{ route('admin.agents.store') }}" class="space-y-4">
            @csrf

            <div>
                <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Agent Full Name</label>
                <input type="text" 
                       name="aname" 
                       required 
                       placeholder="e.g. JOHN DOE" 
                       class="w-full px-4 py-3 bg-slate-950 border border-slate-800 rounded-xl text-slate-200 placeholder-slate-700 focus:outline-none focus:border-indigo-500 text-sm uppercase">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Address / Base Location</label>
                <input type="text" 
                       name="aplace" 
                       required 
                       placeholder="e.g. COCHIN, KERALA" 
                       class="w-full px-4 py-3 bg-slate-950 border border-slate-800 rounded-xl text-slate-200 placeholder-slate-700 focus:outline-none focus:border-indigo-500 text-sm uppercase">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Mobile Number</label>
                    <input type="text" 
                           name="amobile" 
                           required 
                           placeholder="e.g. 9876543210" 
                           class="w-full px-4 py-3 bg-slate-950 border border-slate-800 rounded-xl text-slate-200 placeholder-slate-700 focus:outline-none focus:border-indigo-500 text-sm font-mono">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Registration Date</label>
                    <input type="date" 
                           name="adate" 
                           required 
                           value="{{ date('Y-m-d') }}"
                           class="w-full px-4 py-3 bg-slate-950 border border-slate-800 rounded-xl text-slate-200 placeholder-slate-700 focus:outline-none focus:border-indigo-500 text-sm font-mono">
                </div>
            </div>

            <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-800/40">
                <button type="button" @click="showAddModal = false" class="flex-1 py-3 px-4 rounded-xl border border-slate-800 hover:bg-slate-800/50 text-slate-400 font-semibold text-sm transition-all">Cancel</button>
                <button type="submit" class="flex-1 py-3 px-4 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-sm shadow-xl shadow-indigo-600/10 active:scale-95 transition-all">Register Agent</button>
            </div>
        </form>
    </x-admin.modal>

    <!-- 2. Edit Agent Modal -->
    <x-admin.modal id="showEditModal" title="Edit Agent Details" icon="fa-solid fa-user-pen">
        <form method="POST" :action="'/admin/agents/' + activeAgent.acode" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Agent Full Name</label>
                <input type="text" 
                       name="aname" 
                       required 
                       x-model="activeAgent.aname"
                       placeholder="e.g. JOHN DOE" 
                       class="w-full px-4 py-3 bg-slate-950 border border-slate-800 rounded-xl text-slate-200 placeholder-slate-700 focus:outline-none focus:border-indigo-500 text-sm uppercase">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Address / Base Location</label>
                <input type="text" 
                       name="aplace" 
                       required 
                       x-model="activeAgent.aplace"
                       placeholder="e.g. COCHIN, KERALA" 
                       class="w-full px-4 py-3 bg-slate-950 border border-slate-800 rounded-xl text-slate-200 placeholder-slate-700 focus:outline-none focus:border-indigo-500 text-sm uppercase">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Mobile Number</label>
                    <input type="text" 
                           name="amobile" 
                           required 
                           x-model="activeAgent.amobile"
                           placeholder="e.g. 9876543210" 
                           class="w-full px-4 py-3 bg-slate-950 border border-slate-800 rounded-xl text-slate-200 placeholder-slate-700 focus:outline-none focus:border-indigo-500 text-sm font-mono">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Registration Date</label>
                    <input type="date" 
                           name="adate" 
                           required 
                           x-model="activeAgent.adate"
                           class="w-full px-4 py-3 bg-slate-950 border border-slate-800 rounded-xl text-slate-200 placeholder-slate-700 focus:outline-none focus:border-indigo-500 text-sm font-mono">
                </div>
            </div>

            <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-800/40">
                <button type="button" @click="showEditModal = false" class="flex-1 py-3 px-4 rounded-xl border border-slate-800 hover:bg-slate-800/50 text-slate-400 font-semibold text-sm transition-all">Cancel</button>
                <button type="submit" class="flex-1 py-3 px-4 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-sm shadow-xl shadow-indigo-600/10 active:scale-95 transition-all">Save Changes</button>
            </div>
        </form>
    </x-admin.modal>

    <!-- 3. Delete Confirmation Modal -->
    <x-admin.modal id="showDeleteModal" title="Remove Distribution Agent" icon="fa-solid fa-triangle-exclamation">
        <form method="POST" :action="'/admin/agents/' + activeAgent.acode" class="space-y-4">
            @csrf
            @method('DELETE')

            <div class="text-sm text-slate-400 space-y-2">
                <p>Are you sure you want to remove the agent <span class="font-bold text-slate-200" x-text="activeAgent.aname"></span>?</p>
                <p class="text-xs text-rose-400/90 font-medium">Warning: This operation soft-deletes the agent from the active directory. Historical payment and transaction details will remain intact for ledger integrity.</p>
            </div>

            <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-800/40">
                <button type="button" @click="showDeleteModal = false" class="flex-1 py-3 px-4 rounded-xl border border-slate-800 hover:bg-slate-800/50 text-slate-400 font-semibold text-sm transition-all">Cancel</button>
                <button type="submit" class="flex-1 py-3 px-4 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-bold text-sm shadow-xl shadow-rose-600/10 active:scale-95 transition-all">Remove Agent</button>
            </div>
        </form>
    </x-admin.modal>
</div>
@endsection
