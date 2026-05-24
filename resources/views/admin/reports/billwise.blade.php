@extends('layouts.admin')

@section('content')
<div class="space-y-8 animate-fadeIn">

    {{-- Header --}}
    <x-admin.header
        title="Billwise Report"
        description="Select order type, month, year and bill number to preview and print any invoice or purchase bill."
        icon="fa-solid fa-envelope-open-text"
        glass="true"
    />

    {{-- Panel --}}
    <div class="p-8 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-indigo-800/30 shadow-xl shadow-slate-100 dark:shadow-indigo-900/10 space-y-8">

        <h3 class="text-sm font-bold text-slate-800 dark:text-slate-200 uppercase tracking-wider border-l-4 border-indigo-500 pl-3 flex items-center gap-2">
            <i class="fa-solid fa-file-invoice text-indigo-400"></i>
            Billwise Report
        </h3>

        {{-- Live clock --}}
        <p class="text-right text-xs font-semibold text-indigo-400 font-mono" id="bw-clock"></p>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">

            {{-- 1. Order Type --}}
            <div class="space-y-1.5">
                <label class="block text-xs font-semibold text-slate-550 dark:text-slate-400 uppercase tracking-wider">Order Type</label>
                <select id="bw-sname"
                        class="w-full bg-slate-100 dark:bg-slate-950 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-3 text-sm text-slate-800 dark:text-slate-300 focus:outline-none focus:ring-1 focus:ring-indigo-500 transition">
                    <option value="">Select Order Type</option>
                    <option value="1">Offline</option>
                    <option value="3">Online</option>
                    <option value="5">Purchase</option>
                </select>
            </div>

            {{-- 2. Month --}}
            <div class="space-y-1.5">
                <label class="block text-xs font-semibold text-slate-550 dark:text-slate-400 uppercase tracking-wider">Month</label>
                <select id="bw-month"
                        class="w-full bg-slate-100 dark:bg-slate-950 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-3 text-sm text-slate-800 dark:text-slate-300 focus:outline-none focus:ring-1 focus:ring-indigo-500 transition">
                    <option value="">Select Month</option>
                    <option value="1">Jan</option>
                    <option value="2">Feb</option>
                    <option value="3">Mar</option>
                    <option value="4">Apr</option>
                    <option value="5">May</option>
                    <option value="6">Jun</option>
                    <option value="7">Jul</option>
                    <option value="8">Aug</option>
                    <option value="9">Sep</option>
                    <option value="10">Oct</option>
                    <option value="11">Nov</option>
                    <option value="12">Dec</option>
                </select>
            </div>

            {{-- 3. Year --}}
            <div class="space-y-1.5">
                <label class="block text-xs font-semibold text-slate-550 dark:text-slate-400 uppercase tracking-wider">Year</label>
                <select id="bw-year"
                        class="w-full bg-slate-100 dark:bg-slate-950 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-3 text-sm text-slate-800 dark:text-slate-300 focus:outline-none focus:ring-1 focus:ring-indigo-500 transition">
                    <option value="">Select Year</option>
                    @for ($y = 2020; $y <= date('Y'); $y++)
                        <option value="{{ $y }}">{{ $y }}</option>
                    @endfor
                </select>
            </div>

            {{-- 4. Bill No --}}
            <div class="space-y-1.5">
                <label class="block text-xs font-semibold text-slate-550 dark:text-slate-400 uppercase tracking-wider">Bill No</label>
                <select id="bw-billno"
                        class="w-full bg-slate-100 dark:bg-slate-950 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-3 text-sm text-slate-800 dark:text-slate-300 focus:outline-none focus:ring-1 focus:ring-indigo-500 transition">
                    <option value="">Select Bill No</option>
                </select>
            </div>

        </div>

        {{-- Print Button --}}
        <div class="flex justify-end">
            <x-admin.button id="bw-print-btn" type="button" variant="primary" icon="fa-solid fa-print text-lg">
                Print Report
            </x-admin.button>
        </div>

        {{-- Status message --}}
        <p id="bw-status" class="text-xs text-slate-500 text-center hidden">Loading bill numbers…</p>

    </div>

</div>

<script>
(function () {
    'use strict';

    const snameEl  = document.getElementById('bw-sname');
    const monthEl  = document.getElementById('bw-month');
    const yearEl   = document.getElementById('bw-year');
    const billnoEl = document.getElementById('bw-billno');
    const statusEl = document.getElementById('bw-status');
    const printBtn = document.getElementById('bw-print-btn');

    const FETCH_URL = "{{ route('admin.reports.billwise.fetch') }}";
    const CSRF      = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // Trigger bill number load when any filter changes
    [monthEl, yearEl, snameEl].forEach(el =>
        el.addEventListener('change', loadBillNumbers)
    );

    function loadBillNumbers() {
        const month = monthEl.value;
        const year  = yearEl.value;
        const sname = snameEl.value;

        // Reset
        billnoEl.innerHTML = '<option value="">Select Bill No</option>';

        if (!month || !year || !sname) return;

        statusEl.classList.remove('hidden');
        statusEl.textContent = 'Loading bill numbers…';

        fetch(FETCH_URL, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': CSRF,
                'Accept': 'application/json',
            },
            body: JSON.stringify({ month, year, sname }),
        })
        .then(r => r.json())
        .then(data => {
            statusEl.classList.add('hidden');
            if (!Array.isArray(data) || data.length === 0) {
                statusEl.textContent = 'No bills found for selected filters.';
                statusEl.classList.remove('hidden');
                return;
            }
            data.forEach(([id, label]) => {
                const opt = document.createElement('option');
                opt.value       = id;
                opt.textContent = label;
                billnoEl.appendChild(opt);
            });
        })
        .catch(() => {
            statusEl.textContent = 'Error fetching bill numbers. Please try again.';
            statusEl.classList.remove('hidden');
        });
    }

    // Print
    printBtn.addEventListener('click', function () {
        const orderId = billnoEl.value;
        const sname   = snameEl.value;

        if (!orderId) {
            alert('Please select a bill number first.');
            return;
        }

        let url;
        if (sname == 5) {
            url = "{{ route('admin.reports.billwise.print_purchase') }}"
                + '?orderId=' + encodeURIComponent(orderId);
        } else {
            url = "{{ route('admin.reports.billwise.print_sale') }}"
                + '?orderId=' + encodeURIComponent(orderId)
                + '&sname=' + encodeURIComponent(sname);
        }

        const win = window.open(url, 'BillwisePrint', 'width=' + screen.width + ',height=' + screen.height);
        if (win) {
            win.addEventListener('load', () => {
                setTimeout(() => { win.print(); }, 800);
            });
        }
    });

    // Live clock
    function tick() {
        const now = new Date();
        const days = ['Sun','Mon','Tue','Wed','Thu','Fri','Sat'];
        const dd  = String(now.getDate()).padStart(2,'0');
        const mm  = String(now.getMonth()+1).padStart(2,'0');
        const hh  = String(now.getHours() % 12 || 12).padStart(2,'0');
        const mi  = String(now.getMinutes()).padStart(2,'0');
        const ss  = String(now.getSeconds()).padStart(2,'0');
        document.getElementById('bw-clock').textContent =
            days[now.getDay()] + ' - ' + dd + '-' + mm + '-' + now.getFullYear() +
            ' - ' + hh + ':' + mi + ':' + ss;
    }
    setInterval(tick, 1000);
    tick();
})();
</script>
@endsection
