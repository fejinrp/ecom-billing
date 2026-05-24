@extends('layouts.admin', ['title' => 'Agent Payments'])

@section('content')
<div class="space-y-6" x-data="{ 
    showAddModal: (new URLSearchParams(window.location.search)).get('action') === 'add' || (new URLSearchParams(window.location.search)).get('o') === 'add', 
    showEditModal: false, 
    showDeleteModal: false,
    selectedAgentId: '',
    agentAddress: '',
    agentMobile: '',
    agentsMap: {{ $agents->mapWithKeys(fn($a) => [$a->acode => ['place' => $a->aplace, 'mobile' => $a->amobile]])->toJson() }},
    activePayment: { payid: '', acode: '', aname: '', aplace: '', amobile: '', pamount: '', pdate: '' },
    
    updateAgentInfo() {
        const info = this.agentsMap[this.selectedAgentId];
        if (info) {
            this.agentAddress = info.place;
            this.agentMobile = info.mobile && info.mobile !== '0' ? info.mobile : 'N/A';
        } else {
            this.agentAddress = '';
            this.agentMobile = '';
        }
    }
}">

    <!-- Header Section -->
    <x-admin.header title="Manage Agent Payments" description="Record, track, and manage commission or payroll distributions made to registered MTL sales agents." glass="false">
        <x-slot:action>
            <x-admin.button @click="showAddModal = true" icon="fa-solid fa-file-invoice-dollar">
                Add Agent Payment
            </x-admin.button>
        </x-slot:action>
    </x-admin.header>

    <!-- Visual Dashboard Metrics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Card 1 -->
        <div class="p-6 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 flex items-center justify-between shadow-xl shadow-slate-950/5 dark:shadow-slate-950/20">
            <div>
                <span class="block text-xs font-bold text-slate-500 uppercase tracking-widest">Total Payments Count</span>
                <span class="block text-3xl font-extrabold text-slate-800 dark:text-slate-200 mt-1">{{ $totalCount ?? 0 }}</span>
            </div>
            <div class="h-12 w-12 rounded-xl bg-indigo-500/10 text-indigo-400 flex items-center justify-center text-xl">
                <i class="fa-solid fa-calculator"></i>
            </div>
        </div>

        <!-- Card 2 -->
        <div class="p-6 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 flex items-center justify-between shadow-xl shadow-slate-950/5 dark:shadow-slate-950/20">
            <div>
                <span class="block text-xs font-bold text-slate-500 uppercase tracking-widest">Total Amount Disbursed</span>
                <span class="block text-3xl font-extrabold text-emerald-600 dark:text-emerald-400 mt-1">
                    ₹{{ number_format($totalAmount ?? 0, 2) }}
                </span>
            </div>
            <div class="h-12 w-12 rounded-xl bg-emerald-500/10 text-emerald-400 flex items-center justify-center text-xl">
                <i class="fa-solid fa-indian-rupee-sign"></i>
            </div>
        </div>

        <!-- Card 3 -->
        <div class="p-6 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 flex items-center justify-between shadow-xl shadow-slate-950/5 dark:shadow-slate-950/20">
            <div>
                <span class="block text-xs font-bold text-slate-500 uppercase tracking-widest">Last Recorded Payment</span>
                <span class="block text-sm font-semibold text-slate-700 dark:text-slate-200 mt-2 truncate max-w-[200px]">
                    @if($payments->first())
                        ₹{{ number_format($payments->first()->pamount, 2) }} to {{ $payments->first()->agent?->aname ?? 'Unknown' }}
                    @else
                        None
                    @endif
                </span>
            </div>
            <div class="h-12 w-12 rounded-xl bg-purple-500/10 text-purple-400 flex items-center justify-center text-xl">
                <i class="fa-solid fa-receipt"></i>
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

    <!-- Payments Table Card -->
    @php
    $tableHeaders = [
        ['label' => 'No'],
        ['label' => 'Agent Name'],
        ['label' => 'Address / Place'],
        ['label' => 'Mobile No'],
        ['label' => 'Payment Date'],
        ['label' => 'Amount Paid', 'align' => 'right'],
        ['label' => 'Actions', 'align' => 'right']
    @endphp

    <x-admin.table :headers="$tableHeaders" :collection="$payments" type="card" minWidth="750px">
        @forelse($payments as $index => $payment)
            <tr class="hover:bg-slate-100 dark:hover:bg-slate-800/30 transition-all block lg:table-row w-full bg-white dark:bg-slate-900/30 border border-slate-200 dark:border-slate-800/60 rounded-2xl p-4 lg:p-0 mb-4 lg:mb-0 relative grid grid-cols-2 gap-x-4 gap-y-3 lg:grid-cols-none lg:bg-transparent lg:border-0 lg:hover:bg-slate-900/20 lg:transition-all">
                <!-- No -->
                <td class="col-span-2 bg-indigo-500/10 text-indigo-400 px-3 py-2 rounded-lg text-xs font-mono font-bold flex items-center justify-between lg:table-cell lg:col-span-none lg:bg-transparent lg:px-6 lg:py-4 lg:text-slate-400">
                    <span class="lg:hidden uppercase tracking-wider text-[10px] font-bold text-indigo-400">No</span>
                    <span>#{{ ($payments->currentPage() - 1) * $payments->perPage() + $index + 1 }}</span>
                </td>

                <!-- Agent Name -->
                <td class="py-2 lg:px-6 lg:py-4 col-span-2 lg:col-span-none block lg:table-cell">
                    <span class="block lg:hidden text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Agent Name</span>
                    <span class="font-bold text-slate-800 dark:text-white tracking-wide text-sm">{{ $payment->agent?->aname ?? 'N/A' }}</span>
                </td>

                <!-- Address -->
                <td class="py-2 lg:px-6 lg:py-4 col-span-2 lg:col-span-none block lg:table-cell">
                    <span class="block lg:hidden text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Address / Place</span>
                    <span class="text-slate-700 dark:text-slate-400 text-sm font-medium">{{ $payment->agent?->aplace ?? 'N/A' }}</span>
                </td>

                <!-- Mobile -->
                <td class="py-2 lg:px-6 lg:py-4 col-span-1 block lg:table-cell lg:col-span-none">
                    <span class="block lg:hidden text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Mobile No</span>
                    @if($payment->agent?->amobile && $payment->agent?->amobile != '0')
                        <span class="text-slate-755 dark:text-slate-200 bg-slate-100 dark:bg-slate-800 px-2.5 py-1 rounded-md border border-slate-200 dark:border-slate-700/50 text-xs font-bold font-mono">
                            {{ $payment->agent?->amobile }}
                        </span>
                    @else
                        <span class="text-slate-500 text-xs italic">N/A</span>
                    @endif
                </td>

                <!-- Date -->
                <td class="py-2 lg:px-6 lg:py-4 col-span-1 block lg:table-cell lg:col-span-none font-mono text-sm">
                    <span class="block lg:hidden text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Payment Date</span>
                    <span class="text-slate-600 dark:text-slate-400">{{ \Carbon\Carbon::parse($payment->pdate)->format('d-m-Y') }}</span>
                </td>

                <!-- Amount -->
                <td class="py-2 lg:px-6 lg:py-4 col-span-1 block lg:table-cell lg:col-span-none font-mono lg:text-right font-extrabold text-emerald-600 dark:text-emerald-400">
                    <span class="block lg:hidden text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Amount Paid</span>
                    <span>₹{{ number_format($payment->pamount, 2) }}</span>
                </td>

                <!-- Actions -->
                <td class="py-3 border-t border-slate-250 dark:border-slate-800/40 mt-2 lg:border-t-0 lg:mt-0 col-span-2 block lg:table-cell lg:col-span-none lg:px-6 lg:py-4 lg:text-right whitespace-nowrap">
                    <span class="block lg:hidden text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-2">Actions</span>
                    <div class="inline-flex gap-2">
                        <!-- Edit -->
                        <button @click="
                                activePayment = { 
                                    payid: '{{ $payment->payid }}', 
                                    acode: '{{ $payment->acode }}', 
                                    aname: '{{ $payment->agent?->aname }}', 
                                    aplace: '{{ $payment->agent?->aplace }}', 
                                    amobile: '{{ $payment->agent?->amobile }}', 
                                    pamount: '{{ $payment->pamount }}', 
                                    pdate: '{{ $payment->pdate }}' 
                                };
                                showEditModal = true;
                                " 
                                class="p-2.5 rounded-xl bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700/50 text-slate-700 dark:text-slate-300 hover:text-indigo-600 dark:hover:text-indigo-400 hover:border-indigo-500/50 transition-all" 
                                title="Edit Payment">
                            <i class="fa-solid fa-pen-to-square text-sm"></i>
                        </button>
                        <!-- Remove -->
                        <button @click="
                                activePayment = { 
                                    payid: '{{ $payment->payid }}', 
                                    aname: '{{ $payment->agent?->aname }}',
                                    pamount: '{{ $payment->pamount }}'
                                };
                                showDeleteModal = true;
                                " 
                                class="p-2.5 rounded-xl bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700/50 text-slate-700 dark:text-slate-400 hover:text-rose-600 dark:hover:text-rose-400 hover:border-rose-500/5 transition-all" 
                                title="Delete Payment Log">
                            <i class="fa-solid fa-trash-can text-sm"></i>
                        </button>
                    </div>
                </td>
            </tr>
        @empty
            <tr class="block lg:table-row w-full bg-white dark:bg-slate-900/30 border border-slate-200 dark:border-slate-800/60 rounded-2xl p-6 lg:p-0">
                <td colspan="7" class="px-6 py-8 text-center text-slate-500 font-medium block lg:table-cell w-full">No payment records found. Click 'Add Agent Payment' to log your first transaction.</td>
            </tr>
        @endforelse
    </x-admin.table>

    <!-- 1. Add Payment Modal -->
    <x-admin.modal id="showAddModal" title="Log Agent Payment" icon="fa-solid fa-file-invoice-dollar">
        <form method="POST" action="{{ route('admin.agents_payments.store') }}" class="space-y-4">
            @csrf

            <div>
                <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Select Sales Agent</label>
                <select name="acode" 
                        required 
                        x-model="selectedAgentId" 
                        @change="updateAgentInfo()"
                        class="w-full px-4 py-3 bg-slate-950 border border-slate-800 rounded-xl text-slate-200 focus:outline-none focus:border-indigo-500 text-sm">
                    <option value="">Select Agent Name</option>
                    @foreach($agents as $agent)
                        <option value="{{ $agent->acode }}">{{ $agent->aname }} [{{ $agent->aplace }}]</option>
                    @endforeach
                </select>
            </div>

            <!-- Dynamic Fields -->
            <div x-show="selectedAgentId" x-transition class="p-4 rounded-xl bg-indigo-500/5 border border-indigo-500/10 grid grid-cols-2 gap-4 text-xs">
                <div>
                    <span class="block text-slate-500 font-bold uppercase tracking-wider">Agent Location</span>
                    <span class="block text-slate-200 mt-1 font-semibold" x-text="agentAddress"></span>
                </div>
                <div>
                    <span class="block text-slate-500 font-bold uppercase tracking-wider">Mobile Number</span>
                    <span class="block text-slate-200 mt-1 font-mono font-semibold" x-text="agentMobile"></span>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Payment Amount (₹)</label>
                    <input type="number" 
                           name="pamount" 
                           required 
                           step="0.01"
                           min="0.01"
                           placeholder="e.g. 5000.00" 
                           class="w-full px-4 py-3 bg-slate-950 border border-slate-800 rounded-xl text-slate-200 placeholder-slate-700 focus:outline-none focus:border-indigo-500 text-sm font-mono">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Payment Date</label>
                    <input type="date" 
                           name="pdate" 
                           required 
                           value="{{ date('Y-m-d') }}"
                           class="w-full px-4 py-3 bg-slate-950 border border-slate-800 rounded-xl text-slate-200 placeholder-slate-700 focus:outline-none focus:border-indigo-500 text-sm font-mono">
                </div>
            </div>

            <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-800/40">
                <button type="button" @click="showAddModal = false; selectedAgentId = ''; updateAgentInfo();" class="flex-1 py-3 px-4 rounded-xl border border-slate-800 hover:bg-slate-800/50 text-slate-400 font-semibold text-sm transition-all">Cancel</button>
                <button type="submit" class="flex-1 py-3 px-4 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-sm shadow-xl shadow-indigo-600/10 active:scale-95 transition-all">Record Payment</button>
            </div>
        </form>
    </x-admin.modal>

    <!-- 2. Edit Payment Modal -->
    <x-admin.modal id="showEditModal" title="Edit Payment Log" icon="fa-solid fa-user-pen">
        <form method="POST" :action="'/admin/agents-payments/' + activePayment.payid" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Sales Agent</label>
                <input type="text" 
                       readonly
                       disabled
                       x-model="activePayment.aname"
                       class="w-full px-4 py-3 bg-slate-900 border border-slate-800 rounded-xl text-slate-500 cursor-not-allowed text-sm uppercase">
            </div>

            <div class="p-4 rounded-xl bg-slate-900/50 border border-slate-800 grid grid-cols-2 gap-4 text-xs">
                <div>
                    <span class="block text-slate-500 font-bold uppercase tracking-wider">Agent Location</span>
                    <span class="block text-slate-350 mt-1 font-semibold" x-text="activePayment.aplace"></span>
                </div>
                <div>
                    <span class="block text-slate-500 font-bold uppercase tracking-wider">Mobile Number</span>
                    <span class="block text-slate-350 mt-1 font-mono font-semibold" x-text="activePayment.amobile && activePayment.amobile !== '0' ? activePayment.amobile : 'N/A'"></span>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Payment Amount (₹)</label>
                    <input type="number" 
                           name="pamount" 
                           required 
                           step="0.01"
                           min="0.01"
                           x-model="activePayment.pamount"
                           placeholder="e.g. 5000.00" 
                           class="w-full px-4 py-3 bg-slate-950 border border-slate-800 rounded-xl text-slate-200 placeholder-slate-700 focus:outline-none focus:border-indigo-500 text-sm font-mono">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Payment Date</label>
                    <input type="date" 
                           name="pdate" 
                           required 
                           x-model="activePayment.pdate"
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
    <x-admin.modal id="showDeleteModal" title="Remove Payment Entry" icon="fa-solid fa-triangle-exclamation">
        <form method="POST" :action="'/admin/agents-payments/' + activePayment.payid" class="space-y-4">
            @csrf
            @method('DELETE')

            <div class="text-sm text-slate-400 space-y-2">
                <p>Are you sure you want to remove the payment entry of <span class="font-bold text-emerald-400">₹<span x-text="parseFloat(activePayment.pamount).toFixed(2)"></span></span> for <span class="font-bold text-slate-200" x-text="activePayment.aname"></span>?</p>
                <p class="text-xs text-rose-400/90 font-medium">Warning: This operation will permanently delete this ledger transaction entry. This action cannot be undone.</p>
            </div>

            <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-800/40">
                <button type="button" @click="showDeleteModal = false" class="flex-1 py-3 px-4 rounded-xl border border-slate-800 hover:bg-slate-800/50 text-slate-400 font-semibold text-sm transition-all">Cancel</button>
                <button type="submit" class="flex-1 py-3 px-4 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-bold text-sm shadow-xl shadow-rose-600/10 active:scale-95 transition-all">Remove Entry</button>
            </div>
        </form>
    </x-admin.modal>
</div>
@endsection
