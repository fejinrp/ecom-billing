@extends('layouts.admin', ['title' => 'Manage Sales Quotations'])

@section('content')
<div class="space-y-6" x-data="{
    cancelModalOpen: false,
    cancelQuoteId: null,
    cancelQuoteBill: ''
}">
    <x-admin.header 
        title="Manage Sales Quotations" 
        description="View and manage custom estimates, proforma invoices, or sales proposal drafts."
    >
        <x-slot:action>
            <x-admin.button href="{{ route('admin.quotations.create') }}" icon="fa-solid fa-plus text-xs">
                Create Quotation
            </x-admin.button>
        </x-slot:action>
    </x-admin.header>

    <!-- Search & Filter Bar -->
    <x-admin.search-bar :action="route('admin.quotations.index')" placeholder="Search by Document #, Client name or mobile...">
        <x-slot:info>
            <span>Showing {{ $quotations->firstItem() ?? 0 }}-{{ $quotations->lastItem() ?? 0 }} of {{ $quotations->total() }} recorded documents</span>
        </x-slot:info>
    </x-admin.search-bar>

    <!-- Responsive Quotations Table -->
    @php
    $tableHeaders = [
        ['label' => 'Document #'],
        ['label' => 'Date'],
        ['label' => 'Client Name'],
        ['label' => 'Contact'],
        ['label' => 'Doc Type', 'align' => 'center'],
        ['label' => 'Status', 'align' => 'center'],
        ['label' => 'Quoted Total', 'align' => 'right'],
        ['label' => 'Actions', 'align' => 'center']
    ];
    @endphp

    <x-admin.table :headers="$tableHeaders" :collection="$quotations" type="glass" minWidth="900px">
        @forelse ($quotations as $quote)
            <tr class="hover:bg-slate-900/20 transition-all block lg:table-row w-full bg-slate-900/30 border border-slate-800/60 rounded-2xl p-4 lg:p-0 mb-4 lg:mb-0 relative grid grid-cols-2 gap-x-4 gap-y-3 lg:grid-cols-none lg:bg-transparent lg:border-0 lg:hover:bg-slate-900/20 lg:transition-all">
                
                <!-- Document # Badge / Mobile Card Header -->
                <td class="col-span-2 bg-indigo-500/10 text-indigo-400 px-3 py-2 rounded-lg text-xs font-mono font-bold flex items-center justify-between lg:table-cell lg:col-span-none lg:bg-transparent lg:px-6 lg:py-4 lg:text-indigo-400">
                    <span class="lg:hidden uppercase tracking-wider text-[10px] font-bold text-indigo-400">Document #</span>
                    <span>#{{ str_pad($quote->morder_id, 4, '0', STR_PAD_LEFT) }}</span>
                </td>

                <!-- Date -->
                <td class="py-2 lg:px-6 lg:py-4 col-span-1 block lg:table-cell lg:col-span-none">
                    <span class="block lg:hidden text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Date</span>
                    <span class="font-medium whitespace-nowrap">{{ date('d-m-Y', strtotime($quote->order_date)) }}</span>
                </td>

                <!-- Client Name -->
                <td class="py-2 lg:px-6 lg:py-4 col-span-2 block lg:table-cell lg:col-span-none">
                    <span class="block lg:hidden text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Client Name</span>
                    <span class="font-semibold text-slate-200 uppercase">{{ $quote->client_name }}</span>
                </td>

                <!-- Contact -->
                <td class="py-2 lg:px-6 lg:py-4 col-span-1 block lg:table-cell lg:col-span-none text-xs font-medium text-slate-400">
                    <span class="block lg:hidden text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Contact</span>
                    <span>{{ $quote->mobile ?: 'N/A' }}</span>
                </td>

                <!-- Document Type Badge -->
                <td class="py-2 lg:px-6 lg:py-4 col-span-1 block lg:table-cell lg:col-span-none lg:text-center whitespace-nowrap">
                    <span class="block lg:hidden text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Doc Type</span>
                    <span>
                        @if ($quote->qtype == 1)
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-sky-500/10 text-sky-400 border border-sky-500/20">
                                <i class="fa-solid fa-file-invoice text-[10px]"></i>
                                <span>Proforma</span>
                            </span>
                        @elseif ($quote->qtype == 2)
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                                <i class="fa-solid fa-calculator text-[10px]"></i>
                                <span>Estimate</span>
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-indigo-500/10 text-indigo-400 border border-indigo-500/20">
                                <i class="fa-solid fa-file-signature text-[10px]"></i>
                                <span>Quotation</span>
                            </span>
                        @endif
                    </span>
                </td>

                <!-- Document Status Badge -->
                <td class="py-2 lg:px-6 lg:py-4 col-span-1 block lg:table-cell lg:col-span-none lg:text-center whitespace-nowrap">
                    <span class="block lg:hidden text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Status</span>
                    <span>
                        @if ($quote->qstate == 1)
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                                <i class="fa-solid fa-circle-check text-[10px]"></i>
                                <span>Active</span>
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-slate-500/10 text-slate-400 border border-slate-500/20">
                                <i class="fa-solid fa-file-dashed-line text-[10px]"></i>
                                <span>Draft</span>
                            </span>
                        @endif
                    </span>
                </td>

                <!-- Quoted Total -->
                <td class="py-2 lg:px-6 lg:py-4 col-span-1 block lg:table-cell lg:col-span-none lg:text-right font-semibold text-slate-200 font-mono">
                    <span class="block lg:hidden text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Quoted Total</span>
                    <span>Rs. {{ number_format($quote->grand_total, 2) }}</span>
                </td>

                <!-- Actions -->
                <td class="py-3 border-t border-slate-800/40 mt-2 lg:border-t-0 lg:mt-0 col-span-2 block lg:table-cell lg:col-span-none lg:px-6 lg:py-4 lg:text-center whitespace-nowrap">
                    <span class="block lg:hidden text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-2">Actions</span>
                    <div class="inline-flex items-center gap-2">
                        <a href="{{ route('admin.quotations.edit', $quote->order_id) }}" 
                           class="p-2 text-slate-400 hover:text-indigo-400 hover:bg-indigo-500/10 rounded-xl transition-all" 
                           title="Edit Quotation">
                            <i class="fa-solid fa-pen-to-square text-base"></i>
                        </a>

                        <!-- Print Quotation Trigger -->
                        <a href="{{ route('admin.quotations.print', $quote->order_id) }}" 
                           target="_blank"
                           class="p-2 text-slate-400 hover:text-sky-400 hover:bg-sky-500/10 rounded-xl transition-all" 
                           title="Print Quotation / PDF">
                            <i class="fa-solid fa-print text-base"></i>
                        </a>

                        <!-- Cancel Quotation trigger -->
                        <button type="button" 
                                @click="
                                    cancelQuoteId = {{ $quote->order_id }};
                                    cancelQuoteBill = '#{{ str_pad($quote->morder_id, 4, '0', STR_PAD_LEFT) }}';
                                    cancelModalOpen = true;
                                "
                                class="p-2 text-slate-400 hover:text-rose-500 hover:bg-rose-500/10 rounded-xl transition-all" 
                                title="Cancel Quotation">
                            <i class="fa-solid fa-trash-can text-base"></i>
                        </button>
                    </div>
                </td>
            </tr>
        @empty
            <tr class="block lg:table-row w-full bg-slate-900/30 border border-slate-800/60 rounded-2xl p-6 lg:p-0">
                <td colspan="8" class="px-6 py-12 text-center text-slate-500 font-medium block lg:table-cell w-full">
                    <div class="flex flex-col items-center justify-center gap-3">
                        <i class="fa-solid fa-file-invoice-dollar text-4xl text-slate-600 animate-bounce"></i>
                        <span>No sales quotations found matching search criteria.</span>
                    </div>
                </td>
            </tr>
        @endforelse
    </x-admin.table>

    <!-- Cancel Quotation Confirmation Modal -->
    <x-admin.modal id="cancelModalOpen" title="Cancel Sales Quotation">
        <form :action="'/admin/quotations/' + cancelQuoteId" method="POST" class="mt-6 space-y-4">
            @csrf
            @method('DELETE')
            <div class="p-4 bg-rose-500/10 border border-rose-500/20 rounded-xl flex items-start gap-3">
                <i class="fa-solid fa-circle-exclamation text-rose-500 text-lg mt-0.5"></i>
                <div class="space-y-1">
                    <span class="block text-sm font-bold text-slate-800 dark:text-slate-200">Are you absolutely sure?</span>
                    <p class="text-xs text-slate-600 dark:text-slate-400 leading-relaxed">
                        You are about to cancel sales quotation <strong class="text-slate-800 dark:text-slate-200" x-text="cancelQuoteBill"></strong>. This action is irreversible.
                    </p>
                </div>
            </div>

            <div class="pt-4 flex items-center justify-end gap-3">
                <button type="button" @click="cancelModalOpen = false" class="px-4 py-2 text-sm font-semibold text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200">
                    Nevermind
                </button>
                <button type="submit" class="px-5 py-2.5 bg-rose-600 hover:bg-rose-700 text-white font-semibold rounded-xl text-sm shadow-lg shadow-rose-600/10 active:scale-95 transition-all">
                    Confirm Cancellation
                </button>
            </div>
        </form>
    </x-admin.modal>
</div>
@endsection
