@extends('layouts.admin', ['title' => 'Manage Expenses'])

@section('content')
<div class="space-y-6" x-data="{ 
    addModalOpen: false, 
    editModalOpen: false,
    activeExpenseId: null, 
    activeExpenseCategory: '',
    activeExpenseAmount: '',
    activeExpenseDate: ''
}">
    <!-- Header -->
    <x-admin.header title="Manage Expenses" description="Track, filter, and log historical daily business expenses.">
        <x-slot:action>
            <x-admin.button type="button" @click="addModalOpen = true" icon="fa-solid fa-plus text-xs">
                Log New Expense
            </x-admin.button>
        </x-slot:action>
    </x-admin.header>

    <!-- Search & Filter Bar -->
    <x-admin.search-bar :action="route('admin.expenses.index')" placeholder="Search by Date (YYYY-MM-DD), Category or Staff username...">
        <x-slot:info>
            <span>Showing {{ $expenses->firstItem() ?? 0 }}-{{ $expenses->lastItem() ?? 0 }} of {{ $expenses->total() }} recorded transactions</span>
        </x-slot:info>
    </x-admin.search-bar>

    <!-- Expenses Table -->
    @php
    $tableHeaders = [
        ['label' => 'Sl No'],
        ['label' => 'Expense Date'],
        ['label' => 'Expense Category'],
        ['label' => 'Amount', 'align' => 'right'],
        ['label' => 'Staff / Operator'],
        ['label' => 'Actions', 'align' => 'center']
    ];
    @endphp

    <x-admin.table :headers="$tableHeaders" :collection="$expenses" type="glass" minWidth="600px">
        @forelse ($expenses as $index => $expense)
            <tr class="hover:bg-slate-900/20 transition-all block lg:table-row w-full bg-slate-900/30 border border-slate-800/60 rounded-2xl p-4 lg:p-0 mb-4 lg:mb-0 relative grid grid-cols-2 gap-x-4 gap-y-3 lg:grid-cols-none lg:bg-transparent lg:border-0 lg:hover:bg-slate-900/20 lg:transition-all">
                
                <!-- Sl No -->
                <td class="col-span-2 bg-indigo-500/10 text-indigo-400 px-3 py-1.5 rounded-lg text-xs font-mono font-bold flex items-center justify-between lg:table-cell lg:col-span-none lg:bg-transparent lg:text-slate-400 lg:px-6 lg:py-4 lg:w-16">
                    <span class="lg:hidden uppercase tracking-wider text-[10px] font-bold text-indigo-400">Index</span>
                    <span>{{ $expenses->firstItem() + $index }}</span>
                </td>

                <!-- Expense Date -->
                <td class="py-2 lg:px-6 lg:py-4 col-span-1 block lg:table-cell lg:col-span-none">
                    <span class="block lg:hidden text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Expense Date</span>
                    <span class="font-medium whitespace-nowrap text-sm text-slate-300">
                        {{ date('d-m-Y', strtotime($expense->exp_date)) }}
                    </span>
                </td>

                <!-- Expense Category -->
                <td class="py-2 lg:px-6 lg:py-4 col-span-2 block lg:table-cell lg:col-span-none text-sm text-slate-200">
                    <span class="block lg:hidden text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Expense Category</span>
                    <span class="font-bold text-slate-200 uppercase">{{ $expense->category->exp_name ?? 'N/A' }}</span>
                </td>

                <!-- Amount -->
                <td class="py-2 lg:px-6 lg:py-4 col-span-1 block lg:table-cell lg:col-span-none lg:text-right font-extrabold text-rose-400">
                    <span class="block lg:hidden text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Amount</span>
                    <span>Rs. {{ number_format($expense->exp_amount, 2) }}</span>
                </td>

                <!-- Staff / Operator -->
                <td class="py-2 lg:px-6 lg:py-4 col-span-1 block lg:table-cell lg:col-span-none text-xs text-slate-400">
                    <span class="block lg:hidden text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Staff</span>
                    <span class="font-semibold text-slate-300">{{ $expense->staff->username ?? 'System Operator' }}</span>
                </td>

                <!-- Actions -->
                <td class="py-3 border-t border-slate-800/40 mt-2 lg:border-t-0 lg:mt-0 col-span-2 block lg:table-cell lg:col-span-none lg:px-6 lg:py-4 lg:text-center whitespace-nowrap">
                    <span class="block lg:hidden text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-2">Actions</span>
                    <div class="inline-flex items-center gap-2">
                        <!-- Edit Button -->
                        <button type="button" 
                                @click="
                                    activeExpenseId = {{ $expense->exp_id }};
                                    activeExpenseCategory = '{{ $expense->exp_name }}';
                                    activeExpenseAmount = '{{ $expense->exp_amount }}';
                                    activeExpenseDate = '{{ date('Y-m-d', strtotime($expense->exp_date)) }}';
                                    editModalOpen = true;
                                "
                                class="p-2 text-slate-400 hover:text-indigo-400 hover:bg-indigo-500/10 rounded-xl transition-all" 
                                title="Edit Expense">
                            <i class="fa-solid fa-pen-to-square text-base"></i>
                        </button>

                        <!-- Delete Button -->
                        <form method="POST" action="{{ route('admin.expenses.destroy', $expense->exp_id) }}" 
                              onsubmit="return confirm('Are you sure you want to deactivate/remove this expense record?')" 
                              class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    class="p-2 text-slate-400 hover:text-rose-500 hover:bg-rose-500/10 rounded-xl transition-all" 
                                    title="Remove Expense">
                                <i class="fa-solid fa-trash-can text-base"></i>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
        @empty
            <tr class="block lg:table-row w-full bg-slate-900/30 border border-slate-800/60 rounded-2xl p-6 lg:p-0">
                <td colspan="6" class="px-6 py-12 text-center text-slate-500 font-medium block lg:table-cell w-full">
                    <div class="flex flex-col items-center justify-center gap-3">
                        <i class="fa-solid fa-coins text-4xl text-slate-650 animate-pulse"></i>
                        <span>No historical expense entries found matching criteria.</span>
                    </div>
                </td>
            </tr>
        @endforelse
    </x-admin.table>

    <!-- Modal 1: Log New Expense -->
    <x-admin.modal id="addModalOpen" title="Log Daily Expense">
        <form action="{{ route('admin.expenses.store') }}" method="POST" class="mt-6 space-y-4">
            @csrf
            
            <div class="space-y-1.5">
                <label for="eDate" class="block text-xs font-bold text-slate-400 uppercase tracking-wider">Expense Date</label>
                <input type="date" 
                       id="eDate" 
                       name="eDate" 
                       value="{{ date('Y-m-d') }}" 
                       required 
                       class="w-full px-4 py-3 bg-slate-950 border border-slate-800 rounded-xl text-slate-200 focus:outline-none focus:border-indigo-500 text-sm font-semibold">
            </div>

            <div class="space-y-1.5">
                <label for="eName" class="block text-xs font-bold text-slate-400 uppercase tracking-wider">Expense Category</label>
                <select id="eName" 
                        name="eName" 
                        required 
                        class="w-full px-4 py-3 bg-slate-950 border border-slate-800 rounded-xl text-slate-200 focus:outline-none focus:border-indigo-500 text-sm font-semibold">
                    <option value="">-- Choose Category --</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->exp_id }}">{{ $category->exp_name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="space-y-1.5">
                <label for="eAmount" class="block text-xs font-bold text-slate-400 uppercase tracking-wider">Expense Amount (Rs.)</label>
                <input type="number" 
                       id="eAmount" 
                       name="eAmount" 
                       step="0.01" 
                       required 
                       placeholder="0.00"
                       class="w-full px-4 py-3 bg-slate-950 border border-slate-800 rounded-xl text-slate-200 focus:outline-none focus:border-rose-500 focus:ring-1 focus:ring-rose-500 font-extrabold text-lg text-right">
            </div>

            <div class="pt-4 flex items-center justify-end gap-3">
                <button type="button" @click="addModalOpen = false" class="px-4 py-2 text-sm font-semibold text-slate-400 hover:text-slate-200">
                    Cancel
                </button>
                <button type="submit" class="px-5 py-2.5 bg-rose-600 hover:bg-rose-700 text-white font-semibold rounded-xl text-sm shadow-lg shadow-rose-600/10 active:scale-95 transition-all">
                    Log Expense
                </button>
            </div>
        </form>
    </x-admin.modal>

    <!-- Modal 2: Edit Expense -->
    <x-admin.modal id="editModalOpen" title="Edit Logged Expense">
        <form :action="'/admin/expenses/' + activeExpenseId" method="POST" class="mt-6 space-y-4">
            @csrf
            @method('PUT')
            
            <div class="space-y-1.5">
                <label for="editDate" class="block text-xs font-bold text-slate-400 uppercase tracking-wider">Expense Date</label>
                <input type="date" 
                       id="editDate" 
                       name="editDate" 
                       x-model="activeExpenseDate"
                       required 
                       class="w-full px-4 py-3 bg-slate-950 border border-slate-800 rounded-xl text-slate-200 focus:outline-none focus:border-indigo-500 text-sm font-semibold">
            </div>

            <div class="space-y-1.5">
                <label for="editName" class="block text-xs font-bold text-slate-400 uppercase tracking-wider">Expense Category</label>
                <select id="editName" 
                        name="editName" 
                        x-model="activeExpenseCategory"
                        required 
                        class="w-full px-4 py-3 bg-slate-950 border border-slate-800 rounded-xl text-slate-200 focus:outline-none focus:border-indigo-500 text-sm font-semibold">
                    <option value="">-- Choose Category --</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->exp_id }}">{{ $category->exp_name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="space-y-1.5">
                <label for="editAmount" class="block text-xs font-bold text-slate-400 uppercase tracking-wider">Expense Amount (Rs.)</label>
                <input type="number" 
                       id="editAmount" 
                       name="editAmount" 
                       x-model="activeExpenseAmount"
                       step="0.01" 
                       required 
                       class="w-full px-4 py-3 bg-slate-950 border border-slate-800 rounded-xl text-slate-200 focus:outline-none focus:border-rose-500 focus:ring-1 focus:ring-rose-500 font-extrabold text-lg text-right">
            </div>

            <div class="pt-4 flex items-center justify-end gap-3">
                <button type="button" @click="editModalOpen = false" class="px-4 py-2 text-sm font-semibold text-slate-400 hover:text-slate-200">
                    Cancel
                </button>
                <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-xl text-sm shadow-lg active:scale-95 transition-all">
                    Apply Changes
                </button>
            </div>
        </form>
    </x-admin.modal>
</div>
@endsection
