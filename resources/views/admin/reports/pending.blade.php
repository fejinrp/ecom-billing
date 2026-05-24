@extends('layouts.admin')

@section('content')
<div class="space-y-8 animate-fadeIn">

    <!-- Header Section -->
    <x-admin.header 
        title="Pending Amount Ledger" 
        description="Filter, track, and generate premium printable statements of outstanding balances for offline invoices and online orders." 
        icon="fa-solid fa-download" 
        glass="true"
    />

    <!-- Quick Navigation / Form Panel -->
    <div class="max-w-3xl mx-auto">
        <div class="p-8 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xl space-y-6">
            
            <h3 class="text-sm font-bold text-slate-755 dark:text-slate-200 uppercase tracking-wider border-l-4 border-violet-500 pl-3 flex items-center gap-2">
                <i class="fa-solid fa-file-invoice-dollar text-violet-400"></i>
                Display Outstanding Balances
            </h3>
            
            <form action="{{ route('admin.reports.pending.generate') }}" method="POST" target="_blank" class="space-y-5">
                @csrf
                
                <div class="space-y-1.5">
                    <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Order / Category Type</label>
                    <select name="oname" required
                        class="w-full bg-slate-100 dark:bg-slate-950 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-3.5 text-sm text-slate-800 dark:text-slate-300 focus:outline-none focus:ring-1 focus:ring-violet-500 transition">
                        <option value="" class="bg-white dark:bg-slate-950 text-slate-800 dark:text-slate-300">~ Select Section ~</option>
                        <option value="1" class="bg-white dark:bg-slate-950 text-slate-800 dark:text-slate-300">Offline Customer Orders</option>
                        <option value="3" class="bg-white dark:bg-slate-950 text-slate-800 dark:text-slate-300">Online Customer Orders</option>
                        <option value="4" class="bg-white dark:bg-slate-950 text-slate-800 dark:text-slate-300">Online Dealer Orders</option>
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Start Date</label>
                        <input type="date" name="startDateStr" id="startDateSelect" required onchange="formatDate(this, 'startDate')"
                            class="w-full bg-slate-100 dark:bg-slate-950 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-3 text-sm text-slate-800 dark:text-slate-300 focus:outline-none focus:ring-1 focus:ring-violet-500 transition">
                        <input type="hidden" name="startDate" id="startDate">
                    </div>
                    
                    <div class="space-y-1.5">
                        <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">End Date</label>
                        <input type="date" name="endDateStr" id="endDateSelect" required onchange="formatDate(this, 'endDate')"
                            class="w-full bg-slate-100 dark:bg-slate-950 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-3 text-sm text-slate-800 dark:text-slate-300 focus:outline-none focus:ring-1 focus:ring-violet-500 transition">
                        <input type="hidden" name="endDate" id="endDate">
                    </div>
                </div>

                <div class="pt-2">
                    <x-admin.button type="submit" variant="primary" icon="fa-solid fa-print text-lg" class="w-full">
                        Print Pending Report
                    </x-admin.button>
                </div>
            </form>
        </div>
    </div>

</div>

<script>
    // Helper to format date as d-m-Y matching legacy Fopen/Headers stream exactly
    function formatDate(input, targetId) {
        const value = input.value;
        if (!value) {
            document.getElementById(targetId).value = "";
            return;
        }
        const parts = value.split("-");
        if (parts.length === 3) {
            document.getElementById(targetId).value = parts[2] + "-" + parts[1] + "-" + parts[0];
        }
    }
</script>
@endsection
