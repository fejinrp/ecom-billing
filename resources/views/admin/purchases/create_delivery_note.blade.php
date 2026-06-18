@extends('layouts.admin', ['title' => 'Create Delivery Note'])

@section('content')
<div class="space-y-6 animate-fadeIn" x-data="{
    loading: false,
    pendingOrders: [],
    selectedOrderId: '',
    items: [],

    async init() {
        this.loading = true;
        try {
            let res = await fetch('{{ route('admin.purchases.pending_orders') }}');
            if (res.ok) {
                this.pendingOrders = await res.json();
            } else {
                alert('Failed to load pending purchase orders.');
            }
        } catch(err) {
            console.error(err);
        } finally {
            this.loading = false;
        }
    },

    async handleOrderChange() {
        if (!this.selectedOrderId) {
            this.items = [];
            return;
        }
        this.loading = true;
        try {
            let res = await fetch(`/admin/purchases/pending-orders/${this.selectedOrderId}/items`);
            if (res.ok) {
                let data = await res.json();
                this.items = data.map(item => ({
                    pitem_id: item.pitem_id,
                    product_name: item.product ? item.product.productname : 'N/A',
                    bqty: item.bqty,
                    qty_received: item.bqty,
                    qty_damaged: 0,
                    batch_number: 'BATCH-' + Math.floor(1000 + Math.random() * 9000),
                    mfg_date: '',
                    expiry_date: '',
                    warranty_months: item.product ? item.product.warranty_months : 0,
                    prate: item.rate || (item.product ? item.product.prate : 0),
                    srate: item.product ? item.product.srate : 0,
                    mrp: item.product ? item.product.mrp : 0,
                    cprice: item.product ? item.product.cprice : 0,
                    dprice: item.product ? item.product.dprice : 0,
                    sdprice: item.product ? item.product.sdprice : 0
                }));
            } else {
                alert('Failed to load purchase items.');
            }
        } catch(err) {
            console.error(err);
        } finally {
            this.loading = false;
        }
    },

    async submitDeliveryNote() {
        if (!this.selectedOrderId || this.items.length === 0) return;
        this.loading = true;
        try {
            let token = document.querySelector('meta[name=&quot;csrf-token&quot;]')?.getAttribute('content') || '{{ csrf_token() }}';
            let res = await fetch('{{ route('admin.purchases.delivery_notes.store') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token
                },
                body: JSON.stringify({
                    porder_id: this.selectedOrderId,
                    items: this.items
                })
            });
            let result = await res.json();
            if (res.ok && result.success) {
                window.location.href = '{{ route('admin.purchases.delivery_notes.index') }}';
            } else {
                alert(result.messages || 'Error saving delivery note.');
            }
        } catch(err) {
            console.error(err);
            alert('Failed to submit delivery note.');
        } finally {
            this.loading = false;
        }
    }
}">
    <!-- Header -->
    <x-admin.header 
        title="Record Inbound Delivery Note" 
        description="Verify physical inbound quantities, record damaged items, and set batch pricing." 
        glass="false"
    >
        <x-slot:action>
            <a href="{{ route('admin.purchases.delivery_notes.index') }}"
               class="inline-flex items-center gap-1.5 px-4 py-2.5 bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-bold rounded-xl shadow-lg transition-all active:scale-95">
                <i class="fa-solid fa-arrow-left"></i>
                <span>Back to Ledger</span>
            </a>
        </x-slot:action>
    </x-admin.header>

    <div class="relative glassmorphism rounded-3xl p-6 border border-slate-800 space-y-6">
        <div x-show="loading" class="absolute inset-0 bg-slate-950/60 rounded-3xl z-10 flex items-center justify-center">
            <i class="fa-solid fa-spinner text-3xl text-indigo-500 animate-spin"></i>
        </div>

        <form @submit.prevent="submitDeliveryNote" class="space-y-6">
            <!-- Select Purchase Order -->
            <div class="space-y-1.5">
                <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider">Select Pending Purchase Order</label>
                <select x-model="selectedOrderId" 
                        @change="handleOrderChange" 
                        required
                        class="w-full max-w-xl px-4 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-slate-200 text-sm focus:outline-none focus:border-indigo-500">
                    <option value="">-- Choose PO --</option>
                    <template x-for="order in pendingOrders" :key="order.porder_id">
                        <option :value="order.porder_id" x-text="'PO #' + order.morder_id + ' - ' + order.s_name + ' (' + order.porder_date + ')'"></option>
                    </template>
                </select>
            </div>

            <!-- PO Items list (Desktop View - Horizontal Wide Scroll) -->
            <div x-show="items.length > 0" class="space-y-4">
                <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Delivery Items Details</h4>
                
                <!-- Desktop Table View -->
                <div class="hidden lg:block border border-slate-800 rounded-xl overflow-hidden overflow-x-auto scrollbar-thin">
                    <table class="w-full text-left text-xs text-slate-350 border-collapse">
                        <thead class="bg-slate-950/80 text-[10px] font-bold text-slate-450 uppercase tracking-wider border-b border-slate-800">
                            <tr>
                                <th class="p-4">Product Name</th>
                                <th class="p-4 text-center">Ordered Bal</th>
                                <th class="p-4 text-center">Qty Received</th>
                                <th class="p-4 text-center">Qty Damaged</th>
                                <th class="p-4 text-center">Batch Number / Warranty</th>
                                <th class="p-4 text-center">Mfg / Expiry Date</th>
                                <th class="p-4 text-center">Purchase / Selling Rate</th>
                                <th class="p-4 text-center">MRP / Cust Price</th>
                                <th class="p-4 text-center">Dealer / Super Dealer</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800/40">
                            <template x-for="(item, index) in items" :key="index">
                                <tr class="bg-slate-900/20 hover:bg-slate-900/40 transition-colors">
                                    <td class="p-4 font-bold text-slate-200 min-w-[200px]" x-text="item.product_name"></td>
                                    <td class="p-4 text-center font-bold text-indigo-400 text-sm" x-text="item.bqty + ' PCS'"></td>
                                    <td class="p-4 text-center">
                                        <input type="number" x-model="item.qty_received" @input="if (parseInt(item.qty_received) + parseInt(item.qty_damaged) > item.bqty) { item.qty_damaged = item.bqty - item.qty_received; }" required min="0" :max="item.bqty" class="w-20 px-2 py-1.5 bg-slate-950 border border-slate-800 focus:border-indigo-500 rounded-lg text-center text-slate-100 text-xs font-semibold focus:outline-none focus:ring-1 focus:ring-indigo-500">
                                    </td>
                                    <td class="p-4 text-center">
                                        <input type="number" x-model="item.qty_damaged" @input="if (parseInt(item.qty_received) + parseInt(item.qty_damaged) > item.bqty) { item.qty_received = item.bqty - item.qty_damaged; }" required min="0" :max="item.bqty" class="w-20 px-2 py-1.5 bg-slate-950 border border-slate-800 focus:border-indigo-500 rounded-lg text-center text-slate-100 text-xs font-semibold focus:outline-none focus:ring-1 focus:ring-indigo-500">
                                    </td>
                                    <td class="p-4 text-center">
                                        <div class="flex flex-col gap-1.5 items-center">
                                            <input type="text" x-model="item.batch_number" :required="item.qty_received > 0" placeholder="Batch No" class="w-32 px-2.5 py-1.5 bg-slate-950 border border-slate-800 focus:border-indigo-500 rounded-lg text-center text-slate-100 text-xs font-semibold focus:outline-none focus:ring-1 focus:ring-indigo-500">
                                            <input type="number" x-model="item.warranty_months" placeholder="Warranty (Months)" class="w-32 px-2.5 py-1 bg-slate-950 border border-slate-800 focus:border-indigo-500 rounded-lg text-center text-slate-100 text-xs font-semibold focus:outline-none focus:ring-1 focus:ring-indigo-500">
                                        </div>
                                    </td>
                                    <td class="p-4 text-center">
                                        <div class="flex flex-col gap-1.5 items-center">
                                            <div class="flex items-center gap-1">
                                                <span class="text-[9px] font-bold text-slate-500 w-8">Mfg:</span>
                                                <input type="date" x-model="item.mfg_date" class="w-36 px-2 py-1 bg-slate-950 border border-slate-800 focus:border-indigo-500 rounded-lg text-center text-slate-100 text-xs font-semibold focus:outline-none focus:ring-1 focus:ring-indigo-500">
                                            </div>
                                            <div class="flex items-center gap-1">
                                                <span class="text-[9px] font-bold text-slate-500 w-8">Exp:</span>
                                                <input type="date" x-model="item.expiry_date" class="w-36 px-2 py-1 bg-slate-950 border border-slate-800 focus:border-indigo-500 rounded-lg text-center text-slate-100 text-xs font-semibold focus:outline-none focus:ring-1 focus:ring-indigo-500">
                                            </div>
                                        </div>
                                    </td>
                                    <td class="p-4 text-center">
                                        <div class="flex flex-col gap-1.5 items-center">
                                            <div class="flex items-center gap-1">
                                                <span class="text-[9px] font-bold text-slate-500 w-4">P:</span>
                                                <input type="number" step="0.01" x-model="item.prate" class="w-24 px-2 py-1 bg-slate-950 border border-slate-800 focus:border-indigo-500 rounded-lg text-right text-slate-100 text-xs font-semibold focus:outline-none focus:ring-1 focus:ring-indigo-500">
                                            </div>
                                            <div class="flex items-center gap-1">
                                                <span class="text-[9px] font-bold text-slate-500 w-4">S:</span>
                                                <input type="number" step="0.01" x-model="item.srate" class="w-24 px-2 py-1 bg-slate-950 border border-slate-800 focus:border-indigo-500 rounded-lg text-right text-slate-100 text-xs font-semibold focus:outline-none focus:ring-1 focus:ring-indigo-500">
                                            </div>
                                        </div>
                                    </td>
                                    <td class="p-4 text-center">
                                        <div class="flex flex-col gap-1.5 items-center">
                                            <div class="flex items-center gap-1">
                                                <span class="text-[9px] font-bold text-slate-500 w-4">M:</span>
                                                <input type="number" step="0.01" x-model="item.mrp" class="w-24 px-2 py-1 bg-slate-950 border border-slate-800 focus:border-indigo-500 rounded-lg text-right text-slate-100 text-xs font-semibold focus:outline-none focus:ring-1 focus:ring-indigo-500">
                                            </div>
                                            <div class="flex items-center gap-1">
                                                <span class="text-[9px] font-bold text-slate-500 w-4">C:</span>
                                                <input type="number" step="0.01" x-model="item.cprice" class="w-24 px-2 py-1 bg-slate-950 border border-slate-800 focus:border-indigo-500 rounded-lg text-right text-slate-100 text-xs font-semibold focus:outline-none focus:ring-1 focus:ring-indigo-500">
                                            </div>
                                        </div>
                                    </td>
                                    <td class="p-4 text-center">
                                        <div class="flex flex-col gap-1.5 items-center">
                                            <div class="flex items-center gap-1">
                                                <span class="text-[9px] font-bold text-slate-500 w-4">D:</span>
                                                <input type="number" step="0.01" x-model="item.dprice" class="w-24 px-2 py-1 bg-slate-950 border border-slate-800 focus:border-indigo-500 rounded-lg text-right text-slate-100 text-xs font-semibold focus:outline-none focus:ring-1 focus:ring-indigo-500">
                                            </div>
                                            <div class="flex items-center gap-1">
                                                <span class="text-[9px] font-bold text-slate-500 w-4">SD:</span>
                                                <input type="number" step="0.01" x-model="item.sdprice" class="w-24 px-2 py-1 bg-slate-950 border border-slate-800 focus:border-indigo-500 rounded-lg text-right text-slate-100 text-xs font-semibold focus:outline-none focus:ring-1 focus:ring-indigo-500">
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>

                <!-- Mobile Card-based List View (Responsive) -->
                <div class="lg:hidden space-y-4">
                    <template x-for="(item, index) in items" :key="index">
                        <div class="p-4 bg-slate-900/40 border border-slate-800 rounded-2xl space-y-3">
                            <div class="flex items-start justify-between">
                                <span class="font-bold text-sm text-slate-200" x-text="item.product_name"></span>
                                <span class="text-xs font-semibold px-2 py-0.5 bg-indigo-500/10 text-indigo-400 rounded-md" x-text="'Ordered Bal: ' + item.bqty + ' PCS'"></span>
                            </div>

                            <div class="grid grid-cols-2 gap-3 text-xs">
                                <div class="space-y-1">
                                    <label class="block text-slate-400">Qty Received</label>
                                    <input type="number" x-model="item.qty_received" @input="if (parseInt(item.qty_received) + parseInt(item.qty_damaged) > item.bqty) { item.qty_damaged = item.bqty - item.qty_received; }" required min="0" :max="item.bqty" class="w-full px-2 py-1.5 bg-slate-950 border border-slate-800 rounded text-slate-100">
                                </div>
                                <div class="space-y-1">
                                    <label class="block text-slate-400">Qty Damaged</label>
                                    <input type="number" x-model="item.qty_damaged" @input="if (parseInt(item.qty_received) + parseInt(item.qty_damaged) > item.bqty) { item.qty_received = item.bqty - item.qty_damaged; }" required min="0" :max="item.bqty" class="w-full px-2 py-1.5 bg-slate-950 border border-slate-800 rounded text-slate-100">
                                </div>
                                <div class="space-y-1">
                                    <label class="block text-slate-400">Batch Number</label>
                                    <input type="text" x-model="item.batch_number" :required="item.qty_received > 0" class="w-full px-2 py-1.5 bg-slate-950 border border-slate-800 rounded text-slate-100">
                                </div>
                                <div class="space-y-1">
                                    <label class="block text-slate-400">Warranty (Months)</label>
                                    <input type="number" x-model="item.warranty_months" class="w-full px-2 py-1.5 bg-slate-950 border border-slate-800 rounded text-slate-100">
                                </div>
                                <div class="space-y-1">
                                    <label class="block text-slate-400">Mfg Date</label>
                                    <input type="date" x-model="item.mfg_date" class="w-full px-2 py-1.5 bg-slate-950 border border-slate-800 rounded text-slate-100">
                                </div>
                                <div class="space-y-1">
                                    <label class="block text-slate-400">Expiry Date</label>
                                    <input type="date" x-model="item.expiry_date" class="w-full px-2 py-1.5 bg-slate-950 border border-slate-800 rounded text-slate-100">
                                </div>
                            </div>

                            <div class="border-t border-slate-800/80 pt-2 grid grid-cols-3 gap-2 text-[10px]">
                                <div class="space-y-1">
                                    <label class="block text-slate-400">P. Rate</label>
                                    <input type="number" step="0.01" x-model="item.prate" class="w-full px-1.5 py-1 bg-slate-950 border border-slate-800 rounded text-slate-100">
                                </div>
                                <div class="space-y-1">
                                    <label class="block text-slate-400">S. Rate</label>
                                    <input type="number" step="0.01" x-model="item.srate" class="w-full px-1.5 py-1 bg-slate-950 border border-slate-800 rounded text-slate-100">
                                </div>
                                <div class="space-y-1">
                                    <label class="block text-slate-400">MRP</label>
                                    <input type="number" step="0.01" x-model="item.mrp" class="w-full px-1.5 py-1 bg-slate-950 border border-slate-800 rounded text-slate-100">
                                </div>
                                <div class="space-y-1">
                                    <label class="block text-slate-400">Cust Price</label>
                                    <input type="number" step="0.01" x-model="item.cprice" class="w-full px-1.5 py-1 bg-slate-950 border border-slate-800 rounded text-slate-100">
                                </div>
                                <div class="space-y-1">
                                    <label class="block text-slate-400">Dealer Price</label>
                                    <input type="number" step="0.01" x-model="item.dprice" class="w-full px-1.5 py-1 bg-slate-950 border border-slate-800 rounded text-slate-100">
                                </div>
                                <div class="space-y-1">
                                    <label class="block text-slate-400">Super Dealer</label>
                                    <input type="number" step="0.01" x-model="item.sdprice" class="w-full px-1.5 py-1 bg-slate-950 border border-slate-800 rounded text-slate-100">
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Footer Actions -->
            <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-800">
                <a href="{{ route('admin.purchases.delivery_notes.index') }}"
                   class="px-4 py-2.5 text-xs font-bold text-slate-400 hover:text-slate-200 rounded-lg">
                    Cancel
                </a>
                <button type="submit" 
                        :disabled="items.length === 0"
                        class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-lg text-xs shadow-lg disabled:opacity-50 disabled:cursor-not-allowed">
                    <i class="fa-solid fa-circle-check mr-1"></i> Apply & Post Delivery Note
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
