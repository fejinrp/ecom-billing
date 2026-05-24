@extends('layouts.admin')

@section('content')
<div class="space-y-8 animate-fadeIn">

    <!-- Header Section -->
    <x-admin.header 
        title="Sales Report Panel" 
        description="Filter and generate premium printable tax invoice lists based on order type, date ranges, or customer identities." 
        icon="fa-solid fa-eject" 
        glass="true"
    />

    <!-- Quick Navigation / Stats Bar -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        
        <!-- Form 1: Sales Report by Order Type & Date Range -->
        <div class="p-8 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xl space-y-6">
            <h3 class="text-sm font-bold text-slate-700 dark:text-slate-200 uppercase tracking-wider border-l-4 border-violet-500 pl-3 flex items-center gap-2">
                <i class="fa-solid fa-calendar-days text-violet-400"></i>
                Datewise Sales Report
            </h3>
            
            <form action="{{ route('admin.reports.sales.generate_type') }}" method="POST" target="_blank" class="space-y-5">
                @csrf
                
                <div class="space-y-1.5">
                    <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Order / Category Type</label>
                    <select name="catname" required
                        class="w-full bg-slate-100 dark:bg-slate-950 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-3.5 text-sm text-slate-800 dark:text-slate-300 focus:outline-none focus:ring-1 focus:ring-violet-500 transition">
                        <option value="" class="bg-white dark:bg-slate-950 text-slate-800 dark:text-slate-300">~ Select Order Type ~</option>
                        <option value="1" class="bg-white dark:bg-slate-950 text-slate-800 dark:text-slate-300">Offline Sales</option>
                        <option value="3" class="bg-white dark:bg-slate-950 text-slate-800 dark:text-slate-300">Online Sales (Customer)</option>
                        <option value="4" class="bg-white dark:bg-slate-950 text-slate-800 dark:text-slate-300">Online Sales (Dealer)</option>
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
                        Generate &amp; Print Report
                    </x-admin.button>
                </div>
            </form>
        </div>

        <!-- Form 2: Sales Report by Customer Name -->
        <div x-data="{
                csect: '',
                loading: false,
                customers: [],
                cname: '',
                onSectChange() {
                    this.cname = '';
                    this.customers = [];
                    if (!this.csect) return;
                    this.loading = true;
                    
                    fetch('{{ route('admin.reports.sales.fetch_customers') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({ csect: this.csect }),
                    })
                    .then(res => res.json())
                    .then(data => {
                        this.loading = false;
                        this.customers = data;
                    })
                    .catch(() => {
                        this.loading = false;
                    });
                }
            }"
            class="p-8 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xl space-y-6">
            
            <h3 class="text-sm font-bold text-slate-700 dark:text-slate-200 uppercase tracking-wider border-l-4 border-emerald-500 pl-3 flex items-center gap-2">
                <i class="fa-solid fa-user-tag text-emerald-400"></i>
                Namewise Customer Sales Report
            </h3>
            
            <form action="{{ route('admin.reports.sales.generate_name') }}" method="POST" target="_blank" class="space-y-5">
                @csrf
                
                <div class="space-y-1.5">
                    <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Customer Category</label>
                    <select name="csect" id="csect" required x-model="csect" @change="onSectChange()"
                        class="w-full bg-slate-100 dark:bg-slate-950 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-3.5 text-sm text-slate-850 dark:text-slate-300 focus:outline-none focus:ring-1 focus:ring-emerald-500 transition">
                        <option value="" class="bg-white dark:bg-slate-950 text-slate-800 dark:text-slate-300">~ Select Section ~</option>
                        <option value="1" class="bg-white dark:bg-slate-950 text-slate-800 dark:text-slate-300">Offline Customers</option>
                        <option value="3" class="bg-white dark:bg-slate-950 text-slate-800 dark:text-slate-300">Online Customers</option>
                        <option value="4" class="bg-white dark:bg-slate-950 text-slate-800 dark:text-slate-300">Online Dealers</option>
                    </select>
                </div>

                <div class="space-y-1.5">
                    <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider flex items-center justify-between">
                        <span>Select Customer</span>
                        <span x-show="loading" class="text-[10px] text-emerald-400 animate-pulse">Loading Customers…</span>
                    </label>
                    <select name="cname" required x-model="cname" :disabled="loading || customers.length === 0"
                        class="w-full bg-slate-100 dark:bg-slate-950 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-3.5 text-sm text-slate-850 dark:text-slate-300 focus:outline-none focus:ring-1 focus:ring-emerald-500 transition disabled:opacity-50 disabled:cursor-not-allowed">
                        <option value="" class="bg-white dark:bg-slate-950 text-slate-800 dark:text-slate-300">~ Select Customer Profile ~</option>
                        <template x-for="cust in customers" :key="cust[0]">
                            <option :value="cust[0]" x-text="cust[1] + (cust[3] ? ' (' + cust[3] + ')' : '') + (cust[2] ? ' - ' + cust[2] : '')" class="bg-white dark:bg-slate-950 text-slate-800 dark:text-slate-300"></option>
                        </template>
                    </select>
                </div>

                <div class="pt-2">
                    <x-admin.button type="submit" variant="primary" icon="fa-solid fa-print text-lg" class="w-full" ::disabled="!cname">
                        Generate &amp; Print Report
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
