@extends('layouts.admin', ['title' => 'Delivery Notes & Shipments'])

@section('content')
<div class="space-y-6 animate-fadeIn" x-data="{
    detailsModalOpen: false,
    loading: false,
    
    // Details modal state
    activeNote: {
        dn_number: '',
        type: '',
        dn_date: '',
        purchase_order: { morder_id: '', s_name: '' },
        items: []
    },

    async openDetailsModal(id) {
        this.detailsModalOpen = true;
        this.loading = true;
        try {
            let res = await fetch(`/admin/purchases/delivery-notes/${id}/details`);
            if (res.ok) {
                this.activeNote = await res.json();
            } else {
                alert('Failed to load delivery note details.');
                this.detailsModalOpen = false;
            }
        } catch(err) {
            console.error(err);
            this.detailsModalOpen = false;
        } finally {
            this.loading = false;
        }
    }
}">
    <!-- Header -->
    <x-admin.header 
        title="Delivery Notes Ledger" 
        description="View physical inbound shipments (GRN), partial deliveries, and track received vs. damaged items." 
        glass="false"
    >
        <x-slot:action>
            <a href="{{ route('admin.purchases.delivery_notes.create') }}"
               class="inline-flex items-center gap-1.5 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl shadow-lg shadow-indigo-600/20 transition-all active:scale-95">
                <i class="fa-solid fa-plus-circle"></i>
                <span>Create Delivery Note</span>
            </a>
        </x-slot:action>
    </x-admin.header>

    <!-- Search Bar -->
    <div class="p-4 glassmorphism rounded-2xl flex flex-col md:flex-row gap-4 items-center justify-between">
        <form method="GET" action="{{ route('admin.purchases.delivery_notes.index') }}" class="w-full md:w-96 relative">
            <input type="text"
                   name="search"
                   value="{{ request('search') }}"
                   placeholder="Search by DN number, supplier or PO..."
                   class="w-full pl-11 pr-4 py-2.5 bg-slate-950/60 border border-slate-800 rounded-xl text-slate-200 placeholder-slate-500 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 text-sm transition-all">
            <div class="absolute left-4 top-3 text-slate-500">
                <i class="fa-solid fa-magnifying-glass text-sm"></i>
            </div>
        </form>

        <div class="flex items-center gap-2 text-xs text-slate-400 font-semibold">
            <span>Showing {{ $notes->firstItem() ?? 0 }}-{{ $notes->lastItem() ?? 0 }} of {{ $notes->total() }} recorded delivery notes</span>
        </div>
    </div>

    <!-- Notes Table -->
    <div class="glassmorphism rounded-2xl overflow-hidden shadow-2xl">
        <div class="w-full overflow-x-auto responsive-table-container">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-900/60 border-b border-slate-800/80 text-xs font-semibold text-slate-400 uppercase tracking-wider">
                        <th class="px-6 py-4">No</th>
                        <th class="px-6 py-4">DN Number</th>
                        <th class="px-6 py-4">Delivery Date</th>
                        <th class="px-6 py-4">Supplier</th>
                        <th class="px-6 py-4">PO Number</th>
                        <th class="px-6 py-4 text-center">Total Received</th>
                        <th class="px-6 py-4 text-center">Total Damaged</th>
                        <th class="px-6 py-4 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/40 text-sm text-slate-300">
                    @forelse ($notes as $index => $row)
                        <tr class="hover:bg-slate-900/20 transition-all">
                            <td class="px-6 py-4 font-semibold text-indigo-400">{{ $notes->firstItem() + $index }}</td>
                            <td class="px-6 py-4 font-bold text-slate-200">{{ $row->dn_number }}</td>
                            <td class="px-6 py-4 font-medium">{{ $row->dn_date->format('d-m-Y') }}</td>
                            <td class="px-6 py-4 font-bold text-slate-100 uppercase tracking-wide">
                                {{ $row->purchaseOrder->s_name ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 font-semibold text-indigo-300">
                                PO #{{ $row->purchaseOrder->morder_id ?? $row->porder_id }}
                            </td>
                            <td class="px-6 py-4 text-center font-extrabold text-emerald-400">
                                {{ intval($row->total_received) }} PCS
                            </td>
                            <td class="px-6 py-4 text-center font-extrabold text-rose-400">
                                {{ intval($row->total_damaged) }} PCS
                            </td>
                            <td class="px-6 py-4 text-center">
                                <button type="button"
                                        @click="openDetailsModal({{ $row->id }})"
                                        class="p-2 bg-indigo-500/10 text-indigo-400 rounded-lg hover:bg-indigo-500/20 transition-all"
                                        title="View Details">
                                    <i class="fa-solid fa-eye text-sm"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center text-slate-500 font-medium">
                                <div class="flex flex-col items-center justify-center gap-3">
                                    <i class="fa-solid fa-truck text-4xl text-slate-600"></i>
                                    <span>No delivery notes found in the ledger.</span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($notes->hasPages())
            <div class="px-6 py-4 bg-slate-900/40 border-t border-slate-800/80">
                {{ $notes->links() }}
            </div>
        @endif
    </div>

    <!-- Create Delivery Note Modal -->

    <!-- Details View Modal -->
    <template x-teleport="body">
    <div x-show="detailsModalOpen"
         x-cloak
         class="admin-modal fixed inset-0 z-50 flex items-center justify-center p-4 overflow-y-auto"
         x-transition>
        <div class="absolute inset-0 bg-slate-900/60 dark:bg-slate-950/80 backdrop-blur-sm" @click="detailsModalOpen = false"></div>

        <div x-show="detailsModalOpen"
             class="relative w-full max-w-3xl p-6 bg-white dark:bg-slate-900 rounded-3xl shadow-2xl border border-slate-200 dark:border-slate-800 space-y-6" @click.outside="detailsModalOpen = false">
            
            <div x-show="loading" class="absolute inset-0 bg-white/70 dark:bg-slate-950/60 rounded-3xl z-10 flex items-center justify-center">
                <i class="fa-solid fa-spinner text-3xl text-indigo-500 animate-spin"></i>
            </div>

            <!-- Header -->
            <div class="flex items-center justify-between pb-4 border-b border-slate-200 dark:border-slate-800">
                <div class="flex items-center gap-2 text-indigo-600 dark:text-indigo-400">
                    <i class="fa-solid fa-file-invoice text-xl"></i>
                    <h3 class="text-lg font-bold text-slate-800 dark:text-white uppercase tracking-wide" x-text="'Delivery Note: ' + activeNote.dn_number"></h3>
                </div>
                <button @click="detailsModalOpen = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-white">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <div class="space-y-4">
                <!-- Metadata summary -->
                <div class="grid grid-cols-3 gap-4 p-4 bg-slate-50 dark:bg-slate-950/70 border border-slate-200 dark:border-slate-800 rounded-2xl">
                    <div>
                        <span class="block text-[9px] font-bold text-slate-500 uppercase tracking-wider">Supplier Name</span>
                        <span class="block text-sm font-bold text-slate-200 uppercase" x-text="activeNote.purchase_order ? activeNote.purchase_order.s_name : 'N/A'"></span>
                    </div>
                    <div>
                        <span class="block text-[9px] font-bold text-slate-500 uppercase tracking-wider">Purchase Order</span>
                        <span class="block text-sm font-bold text-indigo-400" x-text="activeNote.purchase_order ? 'PO #' + activeNote.purchase_order.morder_id : 'N/A'"></span>
                    </div>
                    <div>
                        <span class="block text-[9px] font-bold text-slate-500 uppercase tracking-wider">Delivery Date</span>
                        <span class="block text-sm font-bold text-slate-300" x-text="activeNote.dn_date"></span>
                    </div>
                </div>

                <!-- Item details -->
                <div class="border border-slate-800 rounded-xl overflow-hidden">
                    <table class="w-full text-left text-xs text-slate-300">
                        <thead class="bg-slate-950 text-[10px] font-bold text-slate-500 uppercase">
                            <tr>
                                <th class="p-3">Product Name</th>
                                <th class="p-3 text-center">Batch Number</th>
                                <th class="p-3 text-center">Qty Received</th>
                                <th class="p-3 text-center">Qty Damaged</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="item in activeNote.items" :key="item.id">
                                <tr class="border-t border-slate-800 bg-slate-900/10">
                                    <td class="p-3 font-semibold text-slate-100" x-text="item.product ? item.product.productname : 'N/A'"></td>
                                    <td class="p-3 text-center font-bold text-indigo-300" x-text="item.batch ? item.batch.batch_number : 'N/A'"></td>
                                    <td class="p-3 text-center font-bold text-emerald-400" x-text="item.qty_received + ' PCS'"></td>
                                    <td class="p-3 text-center font-bold text-rose-400" x-text="item.qty_damaged + ' PCS'"></td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    </template>
</div>
@endsection
