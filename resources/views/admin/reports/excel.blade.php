@extends('layouts.admin')

@section('content')
<div class="space-y-8 animate-fadeIn">

    <!-- Header Section -->
    <x-admin.header 
        title="Ledger Export Panel" 
        description="Select specific ledger sheets and stream high-performance spreadsheet records directly to offline spreadsheet formats." 
        icon="fa-solid fa-list" 
        glass="true"
    />

    <!-- Form Panel -->
    <div class="max-w-3xl mx-auto">
        <div class="p-8 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xl space-y-6">
            
            <h3 class="text-sm font-bold text-slate-700 dark:text-slate-200 uppercase tracking-wider border-l-4 border-indigo-500 pl-3 flex items-center gap-2">
                <i class="fa-solid fa-file-excel text-emerald-400"></i>
                Generate Spreadsheet Report
            </h3>
            
            <form action="{{ route('admin.reports.excel') }}" method="POST" class="space-y-5">
                @csrf
                
                <div class="space-y-1.5">
                    <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Select Ledger Sheet</label>
                    <select name="report" required
                        class="w-full bg-slate-100 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl px-4 py-3.5 text-sm text-slate-800 dark:text-slate-300 focus:outline-none focus:ring-1 focus:ring-indigo-500 transition">
                        <option value="" class="bg-white dark:bg-slate-950 text-slate-800 dark:text-slate-300">~Select~</option>
                        <option value="1" class="bg-white dark:bg-slate-950 text-slate-800 dark:text-slate-300">Customer Report</option>
                        <option value="2" class="bg-white dark:bg-slate-950 text-slate-800 dark:text-slate-300">Dealer Report</option>
                        <option value="3" class="bg-white dark:bg-slate-950 text-slate-800 dark:text-slate-300">SDealer Report</option>
                        <option value="4" class="bg-white dark:bg-slate-950 text-slate-800 dark:text-slate-300">Online Customer Report</option>
                        <option value="5" class="bg-white dark:bg-slate-950 text-slate-800 dark:text-slate-300">Online Dealer Report</option>
                    </select>
                </div>

                <div class="pt-2">
                    <x-admin.button type="submit" variant="primary" icon="fa-solid fa-file-arrow-down text-lg" class="w-full">
                        Export to Excel Format
                    </x-admin.button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
