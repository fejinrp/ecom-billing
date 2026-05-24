@extends('layouts.admin', ['title' => 'Purchase Stock Approvals'])

@section('content')
<div class="space-y-6 animate-fadeIn" x-data="{
    modalOpen: false,
    loading: false,
    item: {
        pitem_id: null,
        productname: '',
        brand_name: '',
        cat_name: '',
        s_name: '',
        punit: '',
        tqty: 0,
        pqty: 0,
        bqty: 0
    },
    async openAddStockModal(pitem_id) {
        this.loading = true;
        this.modalOpen = true;
        try {
            let response = await fetch(`/admin/purchases/stock/${pitem_id}/detail`);
            if (response.ok) {
                this.item = await response.json();
            } else {
                alert('Error fetching item details');
                this.modalOpen = false;
            }
        } catch (error) {
            console.error(error);
            alert('Network error occurred');
            this.modalOpen = false;
        } finally {
            this.loading = false;
        }
    },
    async submitStockApproval() {
        this.loading = true;
        try {
            let token = document.querySelector('meta[name=&quot;csrf-token&quot;]')?.getAttribute('content') || '{{ csrf_token() }}';
            let response = await fetch(`/admin/purchases/stock/${this.item.pitem_id}/update`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token
                },
                body: JSON.stringify({
                    quantity: this.item.bqty
                })
            });
            let result = await response.json();
            if (response.ok && result.success) {
                // Success message notification banner
                window.location.reload();
            } else {
                alert(result.messages || 'Error updating stock info');
            }
        } catch (error) {
            console.error(error);
            alert('Failed to process approval request');
        } finally {
            this.loading = false;
        }
    }
}">
    <!-- Header Controls -->
    <x-admin.header 
        title="Purchase Stock Ledger" 
        description="Approve pending item shipments into the live warehouse and record acquisitions safely." 
        glass="false"
    >
        <x-slot:action>
            <div class="flex items-center gap-2">
                <span class="px-3 py-1.5 bg-slate-900/60 border border-slate-800 text-xs font-semibold text-slate-400 rounded-lg">
                    <i class="fa-solid fa-clock-rotate-left mr-1"></i> Live stock room ledger
                </span>
            </div>
        </x-slot:action>
    </x-admin.header>

    <!-- Stat boxes -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
        <!-- Pending Approvals count -->
        <div class="p-6 rounded-3xl bg-slate-900/50 border border-slate-800/80 shadow-lg flex items-center justify-between group hover:border-amber-500/30 transition-all duration-300">
            <div class="space-y-2">
                <span class="block text-xs font-semibold text-slate-500 uppercase tracking-wider">Pending Approvals</span>
                <span class="block text-3xl font-extrabold text-slate-200 group-hover:text-amber-400 transition-colors">
                    {{ $totalPending }} items
                </span>
                <span class="block text-xs text-amber-500/80 font-medium">
                    <i class="fa-solid fa-triangle-exclamation mr-1 animate-pulse"></i> Outstanding line items to receive
                </span>
            </div>
            <div class="p-4 rounded-2xl bg-amber-500/10 text-amber-400 group-hover:bg-amber-500/20 transition-all">
                <i class="fa-solid fa-boxes-packing text-2xl"></i>
            </div>
        </div>

        <!-- Approved Items count -->
        <div class="p-6 rounded-3xl bg-slate-900/50 border border-slate-800/80 shadow-lg flex items-center justify-between group hover:border-emerald-500/30 transition-all duration-300">
            <div class="space-y-2">
                <span class="block text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Received</span>
                <span class="block text-3xl font-extrabold text-slate-200 group-hover:text-emerald-400 transition-colors">
                    {{ $totalApproved }} items
                </span>
                <span class="block text-xs text-emerald-500/80 font-medium">
                    <i class="fa-solid fa-circle-check mr-1"></i> Fully transferred to inventory
                </span>
            </div>
            <div class="p-4 rounded-2xl bg-emerald-500/10 text-emerald-400 group-hover:bg-emerald-500/20 transition-all">
                <i class="fa-solid fa-warehouse text-2xl"></i>
            </div>
        </div>
    </div>

    <!-- Search & Filter Bar -->
    <div class="p-4 glassmorphism rounded-2xl flex flex-col md:flex-row gap-4 items-center justify-between">
        <form method="GET" action="{{ route('admin.purchases.stock.index') }}" class="w-full md:w-96 relative">
            <input type="text"
                   name="search"
                   value="{{ request('search') }}"
                   placeholder="Search by Product Name or Supplier..."
                   class="w-full pl-11 pr-4 py-2.5 bg-slate-950/60 border border-slate-800 rounded-xl text-slate-200 placeholder-slate-500 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 text-sm transition-all">
            <div class="absolute left-4 top-3 text-slate-500">
                <i class="fa-solid fa-magnifying-glass text-sm"></i>
            </div>
        </form>

        <div class="flex items-center gap-2 text-xs text-slate-400 font-semibold">
            <span>Showing {{ $items->firstItem() ?? 0 }}-{{ $items->lastItem() ?? 0 }} of {{ $items->total() }} recorded purchase items</span>
        </div>
    </div>

    <!-- Responsive Stock Table -->
    <div class="glassmorphism rounded-2xl overflow-hidden shadow-2xl">
        <div class="w-full overflow-visible lg:overflow-x-auto responsive-table-container scrollbar-thin">
            <table class="w-full text-left border-collapse min-w-0 lg:min-w-[900px] block lg:table">
                <thead>
                    <tr class="bg-slate-900/60 border-b border-slate-800/80 text-xs font-semibold text-slate-400 uppercase tracking-wider hidden lg:table-row">
                        <th class="px-6 py-4">No</th>
                        <th class="px-6 py-4">Purchase Date</th>
                        <th class="px-6 py-4">Supplier</th>
                        <th class="px-6 py-4">Product Name</th>
                        <th class="px-6 py-4">Brand</th>
                        <th class="px-6 py-4">Category</th>
                        <th class="px-6 py-4 text-center">Package Qty</th>
                        <th class="px-6 py-4 text-center">Total Qty (PCS)</th>
                        <th class="px-6 py-4 text-center">Status</th>
                        <th class="px-6 py-4 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/40 text-sm text-slate-300 block lg:table-row-group w-full space-y-4 lg:space-y-0 lg:divide-y lg:divide-slate-800/40 p-4 lg:p-0">
                    @forelse ($items as $index => $row)
                        <tr class="hover:bg-slate-900/20 transition-all block lg:table-row w-full bg-slate-900/30 border border-slate-800/60 rounded-2xl p-4 lg:p-0 mb-4 lg:mb-0 relative grid grid-cols-2 gap-x-4 gap-y-3 lg:grid-cols-none lg:bg-transparent lg:border-0 lg:hover:bg-slate-900/20 lg:transition-all">
                            
                            <!-- Index No -->
                            <td class="col-span-2 bg-indigo-500/10 text-indigo-400 px-3 py-1.5 rounded-lg text-xs font-bold flex items-center justify-between lg:table-cell lg:col-span-none lg:bg-transparent lg:px-6 lg:py-4 lg:font-semibold">
                                <span class="lg:hidden uppercase tracking-wider text-[10px] font-bold text-indigo-400">No</span>
                                <span>{{ $items->firstItem() + $index }}</span>
                            </td>

                            <!-- Purchase Date -->
                            <td class="py-1.5 lg:px-6 lg:py-4 col-span-1 block lg:table-cell lg:col-span-none">
                                <span class="block lg:hidden text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">P_Date</span>
                                <span class="font-medium whitespace-nowrap">{{ date('d-m-Y', strtotime($row->purchaseOrder->porder_date)) }}</span>
                            </td>

                            <!-- Supplier Name -->
                            <td class="py-1.5 lg:px-6 lg:py-4 col-span-1 block lg:table-cell lg:col-span-none">
                                <span class="block lg:hidden text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Supplier</span>
                                <span class="font-semibold text-slate-200 uppercase whitespace-nowrap line-clamp-1 max-w-[150px] lg:max-w-none" title="{{ $row->purchaseOrder->s_name }}">{{ $row->purchaseOrder->s_name }}</span>
                            </td>

                            <!-- Product Name -->
                            <td class="py-1.5 lg:px-6 lg:py-4 col-span-2 block lg:table-cell lg:col-span-none">
                                <span class="block lg:hidden text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Product_Name</span>
                                <span class="font-bold text-slate-100 uppercase tracking-wide">{{ $row->product->productname }}</span>
                            </td>

                            <!-- Brand Name -->
                            <td class="py-1.5 lg:px-6 lg:py-4 col-span-1 block lg:table-cell lg:col-span-none">
                                <span class="block lg:hidden text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Brand</span>
                                <span class="text-xs font-semibold text-slate-400 uppercase bg-slate-800/60 px-2 py-1 rounded-md">{{ $row->product->brand->brand_name ?? 'N/A' }}</span>
                            </td>

                            <!-- Category Name -->
                            <td class="py-1.5 lg:px-6 lg:py-4 col-span-1 block lg:table-cell lg:col-span-none">
                                <span class="block lg:hidden text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Category</span>
                                <span class="text-xs font-semibold text-slate-400 uppercase bg-slate-800/60 px-2 py-1 rounded-md">{{ $row->product->category->cat_name ?? 'N/A' }}</span>
                            </td>

                            <!-- Package Qty -->
                            <td class="py-1.5 lg:px-6 lg:py-4 col-span-1 block lg:table-cell lg:col-span-none lg:text-center font-semibold text-slate-300">
                                <span class="block lg:hidden text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Package Qty</span>
                                <span>{{ $row->qty }} x {{ $row->pqty }}</span>
                            </td>

                            <!-- Total Pieces Count -->
                            <td class="py-1.5 lg:px-6 lg:py-4 col-span-1 block lg:table-cell lg:col-span-none lg:text-center font-bold text-slate-200">
                                <span class="block lg:hidden text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Total Qty</span>
                                <span>{{ $row->qty * $row->pqty }} {{ $row->punit }}</span>
                            </td>

                            <!-- Status -->
                            <td class="py-1.5 lg:px-6 lg:py-4 col-span-1 block lg:table-cell lg:col-span-none lg:text-center">
                                <span class="block lg:hidden text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Status</span>
                                <span>
                                    @if ($row->status == 1)
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-amber-500/10 text-amber-400 border border-amber-500/20">
                                            <i class="fa-solid fa-clock text-[10px]"></i>
                                            <span>Pending</span>
                                        </span>
                                    @elseif ($row->status == 2)
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                                            <i class="fa-solid fa-circle-check text-[10px]"></i>
                                            <span>Added</span>
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-slate-800 text-slate-500">
                                            <span>Unknown</span>
                                        </span>
                                    @endif
                                </span>
                            </td>

                            <!-- Actions -->
                            <td class="py-3 border-t border-slate-800/40 mt-2 lg:border-t-0 lg:mt-0 col-span-2 block lg:table-cell lg:col-span-none lg:text-center">
                                <span class="block lg:hidden text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-2">Actions</span>
                                <div>
                                    @if ($row->status == 1)
                                        <button type="button"
                                                @click="openAddStockModal({{ $row->pitem_id }})"
                                                class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-orange-600 hover:bg-orange-700 text-white text-xs font-bold rounded-lg shadow transition-all active:scale-95">
                                            <i class="fa-solid fa-plus-circle"></i>
                                            <span>Add Stock</span>
                                        </button>
                                    @else
                                        <span class="text-xs text-slate-500 italic flex items-center justify-center gap-1">
                                            <i class="fa-solid fa-check text-emerald-500"></i> Stocked
                                        </span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr class="block lg:table-row w-full bg-slate-900/30 border border-slate-800/60 rounded-2xl p-6 lg:p-0">
                            <td colspan="10" class="px-6 py-12 text-center text-slate-500 font-medium block lg:table-cell w-full">
                                <div class="flex flex-col items-center justify-center gap-3">
                                    <i class="fa-solid fa-boxes-stacked text-4xl text-slate-600 animate-bounce"></i>
                                    <span>No pending purchase items found in stock room ledger.</span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($items->hasPages())
            <div class="px-6 py-4 bg-slate-900/40 border-t border-slate-800/80">
                {{ $items->links() }}
            </div>
        @endif
    </div>

    <!-- Alpine Approval Modal -->
    <template x-teleport="body">
    <div x-show="modalOpen"
         x-cloak
         class="admin-modal fixed inset-0 z-50 flex items-center justify-center p-4 overflow-y-auto"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
        
        <!-- Backdrop -->
        <div class="absolute inset-0 bg-slate-900/60 dark:bg-slate-950/80 backdrop-blur-sm" @click="modalOpen = false"></div>

        <div x-show="modalOpen"
             x-transition:enter="transition ease-out duration-300 delay-75"
             x-transition:enter-start="opacity-0 scale-95 translate-y-4"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100 translate-y-0"
             x-transition:leave-end="opacity-0 scale-95 translate-y-4"
             class="relative w-full max-w-lg p-6 bg-white dark:bg-slate-900 rounded-3xl shadow-2xl border border-slate-200 dark:border-slate-800 space-y-6" @click.outside="modalOpen = false">
            
            <!-- Loading Indicator Overlay -->
            <div x-show="loading" class="absolute inset-0 bg-white/70 dark:bg-slate-950/60 rounded-3xl z-10 flex items-center justify-center">
                <div class="flex flex-col items-center justify-center gap-2">
                    <i class="fa-solid fa-spinner text-3xl text-indigo-500 animate-spin"></i>
                    <span class="text-xs font-semibold text-slate-700 dark:text-slate-400 uppercase tracking-wider">Processing Stock...</span>
                </div>
            </div>

            <!-- Modal Header -->
            <div class="flex items-center justify-between pb-4 border-b border-slate-200 dark:border-slate-800">
                <div class="flex items-center gap-2 text-indigo-600 dark:text-indigo-400">
                    <i class="fa-solid fa-plus-circle text-xl"></i>
                    <h3 class="text-lg font-bold text-slate-800 dark:text-white uppercase tracking-wide">Acquisition Approval</h3>
                </div>
                <button @click="modalOpen = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-white p-1 hover:bg-slate-100 dark:hover:bg-slate-800/40 rounded-lg transition-all">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <!-- Modal Content -->
            <form @submit.prevent="submitStockApproval" class="mt-6 space-y-4">
                
                <!-- Product & Meta Details Box -->
                <div class="p-4 bg-slate-50 dark:bg-slate-950/70 border border-slate-200 dark:border-slate-800/80 rounded-2xl space-y-3">
                    <div class="space-y-0.5">
                        <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Product Name</span>
                        <span class="block text-base font-extrabold text-indigo-650 dark:text-indigo-300 uppercase tracking-wide" x-text="item.productname"></span>
                    </div>

                    <div class="grid grid-cols-2 gap-4 pt-2 border-t border-slate-200 dark:border-slate-900">
                        <div class="space-y-0.5">
                            <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Brand Name</span>
                            <span class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase" x-text="item.brand_name || 'N/A'"></span>
                        </div>
                        <div class="space-y-0.5">
                            <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Supplier Name</span>
                            <span class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase" x-text="item.s_name || 'N/A'"></span>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4 pt-2 border-t border-slate-200 dark:border-slate-900">
                        <div class="space-y-0.5">
                            <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Category</span>
                            <span class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase" x-text="item.cat_name || 'N/A'"></span>
                        </div>
                        <div class="space-y-0.5">
                            <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Default Unit</span>
                            <span class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase" x-text="item.punit || 'PCS'"></span>
                        </div>
                    </div>
                </div>

                <!-- Quantities Summary Row -->
                <div class="grid grid-cols-3 gap-3">
                    <div class="p-3 bg-slate-50 dark:bg-slate-900/40 border border-slate-200 dark:border-slate-800/40 rounded-xl text-center">
                        <span class="block text-[9px] font-bold text-slate-500 uppercase tracking-wider">Cartons Count</span>
                        <span class="block text-sm font-extrabold text-slate-700 dark:text-slate-200 mt-1" x-text="item.tqty"></span>
                    </div>
                    <div class="p-3 bg-slate-50 dark:bg-slate-900/40 border border-slate-200 dark:border-slate-800/40 rounded-xl text-center">
                        <span class="block text-[9px] font-bold text-slate-500 uppercase tracking-wider">Pieces Per Ctn</span>
                        <span class="block text-sm font-extrabold text-slate-700 dark:text-slate-200 mt-1" x-text="item.pqty"></span>
                    </div>
                    <div class="p-3 bg-indigo-50 dark:bg-indigo-500/5 border border-indigo-100 dark:border-indigo-500/20 rounded-xl text-center">
                        <span class="block text-[9px] font-bold text-indigo-650 dark:text-indigo-400 uppercase tracking-wider">Approval Qty</span>
                        <span class="block text-sm font-extrabold text-indigo-600 dark:text-indigo-300 mt-1" x-text="item.bqty + ' ' + (item.punit || 'PCS')"></span>
                    </div>
                </div>

                <!-- Input Quantity approved -->
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Confirm Quantity To Add</label>
                    <div class="relative">
                        <input type="number"
                               :value="item.bqty"
                               readonly
                               class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-850 rounded-xl text-slate-800 dark:text-slate-300 font-extrabold text-lg focus:outline-none cursor-not-allowed">
                        <span class="absolute right-4 top-3 text-xs font-bold text-indigo-600 dark:text-indigo-400 uppercase" x-text="item.punit || 'PCS'"></span>
                    </div>
                    <span class="text-[10px] text-slate-500 font-medium">Approval processes the full remaining outstanding package quantity directly to warehouse shelves.</span>
                </div>

                <!-- Footer Actions -->
                <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-200 dark:border-slate-800">
                    <button type="button" @click="modalOpen = false" class="px-4 py-2.5 text-xs font-bold text-slate-550 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800/30 rounded-lg transition-all">
                        Cancel
                    </button>
                    <button type="submit" class="px-5 py-2.5 bg-orange-600 hover:bg-orange-700 text-white font-bold rounded-lg text-xs shadow-lg shadow-orange-600/20 active:scale-95 transition-all">
                        <i class="fa-solid fa-circle-check mr-1"></i> Approve & Stock
                    </button>
                </div>
            </form>
        </div>
    </div>
    </template>
</div>
@endsection
