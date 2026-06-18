@extends('layouts.admin', ['title' => 'Product Batches & Lots'])

@section('content')
<div class="space-y-6 animate-fadeIn">
    <!-- Header -->
    <x-admin.header 
        title="Product Batches Ledger" 
        description="Track active product batches, manufacturing & expiry dates, and remaining quantities in stock." 
        glass="false"
    />

    <!-- Search Bar -->
    <div class="p-4 glassmorphism rounded-2xl flex flex-col md:flex-row gap-4 items-center justify-between">
        <form method="GET" action="{{ route('admin.purchases.batches.index') }}" class="w-full md:w-96 relative">
            <input type="text"
                   name="search"
                   value="{{ request('search') }}"
                   placeholder="Search by Batch Number or Product..."
                   class="w-full pl-11 pr-4 py-2.5 bg-slate-950/60 border border-slate-800 rounded-xl text-slate-200 placeholder-slate-500 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 text-sm transition-all">
            <div class="absolute left-4 top-3 text-slate-500">
                <i class="fa-solid fa-magnifying-glass text-sm"></i>
            </div>
        </form>

        <div class="flex items-center gap-2 text-xs text-slate-400 font-semibold">
            <span>Showing {{ $batches->firstItem() ?? 0 }}-{{ $batches->lastItem() ?? 0 }} of {{ $batches->total() }} recorded batches</span>
        </div>
    </div>

    <!-- Batches Table -->
    <div class="glassmorphism rounded-2xl overflow-hidden shadow-2xl">
        <div class="w-full overflow-x-auto responsive-table-container">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-900/60 border-b border-slate-800/80 text-xs font-semibold text-slate-400 uppercase tracking-wider">
                        <th class="px-6 py-4">No</th>
                        <th class="px-6 py-4">Batch Number</th>
                        <th class="px-6 py-4">Product Name</th>
                        <th class="px-6 py-4">Mfg Date</th>
                        <th class="px-6 py-4">Expiry Date</th>
                        <th class="px-6 py-4 text-center">Initial Qty</th>
                        <th class="px-6 py-4 text-center">Current Qty</th>
                        <th class="px-6 py-4 text-center">Warranty (M)</th>
                        <th class="px-6 py-4 text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/40 text-sm text-slate-300">
                    @forelse ($batches as $index => $row)
                        <tr class="hover:bg-slate-900/20 transition-all">
                            <td class="px-6 py-4 font-semibold text-indigo-400">{{ $batches->firstItem() + $index }}</td>
                            <td class="px-6 py-4 font-bold text-slate-200">{{ $row->batch_number }}</td>
                            <td class="px-6 py-4 font-bold text-slate-100 uppercase tracking-wide">{{ $row->product->productname ?? 'N/A' }}</td>
                            <td class="px-6 py-4 font-medium">{{ $row->mfg_date ? $row->mfg_date->format('d-m-Y') : '-' }}</td>
                            <td class="px-6 py-4 font-medium">
                                @if($row->expiry_date && $row->expiry_date->isPast())
                                    <span class="text-rose-500 font-bold" title="Expired">{{ $row->expiry_date->format('d-m-Y') }} (Expired)</span>
                                @else
                                    {{ $row->expiry_date ? $row->expiry_date->format('d-m-Y') : '-' }}
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center font-semibold text-slate-400">{{ $row->initial_qty }}</td>
                            <td class="px-6 py-4 text-center font-extrabold text-slate-200">{{ $row->current_qty }}</td>
                            <td class="px-6 py-4 text-center font-medium">{{ $row->warranty_months }} Months</td>
                            <td class="px-6 py-4 text-center">
                                @if ($row->status == 1 && $row->current_qty > 0)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">Active</span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-slate-800 text-slate-500">Depleted/Inactive</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-6 py-12 text-center text-slate-500 font-medium">
                                <div class="flex flex-col items-center justify-center gap-3">
                                    <i class="fa-solid fa-boxes-stacked text-4xl text-slate-600"></i>
                                    <span>No product batches found in the ledger.</span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($batches->hasPages())
            <div class="px-6 py-4 bg-slate-900/40 border-t border-slate-800/80">
                {{ $batches->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
