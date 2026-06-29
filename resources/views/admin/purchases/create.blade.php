@extends('layouts.admin', ['title' => 'Record New Purchase'])

@section('content')
<div class="space-y-6" x-data="purchaseInvoiceBuilder()" @quick-product-created.window="onQuickProductCreated($event.detail)">
    <!-- Header -->
    <x-admin.header 
        description="Add a new supplier purchase order to increment product stock and track pending transaction dues."
    >
        <x-slot:title>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.purchases.index') }}" class="p-2 text-slate-400 hover:text-white hover:bg-slate-900/50 rounded-xl transition-all">
                    <i class="fa-solid fa-arrow-left text-lg"></i>
                </a>
                <span>Record New Purchase</span>
            </div>
        </x-slot:title>
    </x-admin.header>

    <!-- Validation alerts -->
    @if ($errors->any())
        <div class="p-4 rounded-xl bg-rose-500/10 border border-rose-500/20 text-sm text-rose-400 space-y-1">
            <span class="font-bold text-slate-200">Validation Alert:</span>
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.purchases.store') }}" method="POST" x-on:submit="handleSubmit($event)" class="space-y-6">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left Side: Order & Supplier details -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Supplier Information Card -->
                <div class="p-4 sm:p-6 glassmorphism rounded-2xl space-y-4">
                    <h3 class="text-lg font-bold text-white flex items-center gap-2 border-b border-slate-800 pb-3">
                        <i class="fa-solid fa-truck text-indigo-400 text-base"></i>
                        <span>Supplier & Date Profiles</span>
                    </h3>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="sm:col-span-2 space-y-1.5">
                            <label for="supplierSelect" class="block text-xs font-bold text-slate-400 uppercase tracking-wider">Select Preconfigured Supplier</label>
                            <select id="supplierSelect" 
                                    name="supplier_id" 
                                    x-model="supplier_id" 
                                    @change="onSupplierSelect()"
                                    class="w-full px-4 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-slate-200 focus:outline-none focus:border-indigo-500 text-sm">
                                <option value="">-- CUSTOM / NEW SUPPLIER --</option>
                                <template x-for="s in suppliers" :key="s.id">
                                    <option :value="s.id" x-text="s.name"></option>
                                </template>
                            </select>
                        </div>

                        <div class="space-y-1.5">
                            <label for="sName" class="block text-xs font-bold text-slate-400 uppercase tracking-wider">Supplier Name</label>
                            <input type="text" 
                                   id="sName" 
                                   name="sName" 
                                   x-model="supplier.name" 
                                   required 
                                   placeholder="ENTER SUPPLIER NAME" 
                                   class="w-full px-4 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-slate-200 placeholder-slate-650 focus:outline-none focus:border-indigo-500 text-sm uppercase">
                        </div>

                        <div class="space-y-1.5">
                            <label for="pDate" class="block text-xs font-bold text-slate-400 uppercase tracking-wider">Purchase Date</label>
                            <input type="date" 
                                   id="pDate" 
                                   name="pDate" 
                                   value="{{ date('Y-m-d') }}" 
                                   required 
                                   class="w-full px-4 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-slate-200 focus:outline-none focus:border-indigo-500 text-sm">
                        </div>

                        <div class="sm:col-span-2 space-y-1.5">
                            <label for="sContact" class="block text-xs font-bold text-slate-400 uppercase tracking-wider">Supplier Address & Contact Details</label>
                            <textarea id="sContact" 
                                      name="sContact" 
                                      x-model="supplier.contact" 
                                      required 
                                      rows="2"
                                      placeholder="ENTER DETAILED STREET ADDRESS, PHONE NUMBER, AND EMAIL..." 
                                      class="w-full px-4 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-slate-200 placeholder-slate-650 focus:outline-none focus:border-indigo-500 text-sm uppercase"></textarea>
                        </div>
                    </div>
                </div>

                <!-- Purchase Line Items Table -->
                <div class="p-4 sm:p-6 glassmorphism rounded-2xl space-y-4">
                    <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                        <h3 class="text-lg font-bold text-white flex items-center gap-2">
                            <i class="fa-solid fa-boxes-stacked text-indigo-400 text-base"></i>
                            <span>Purchase Stock Items</span>
                        </h3>
                        <button type="button" 
                                @click="addLineItem()" 
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-indigo-500/10 hover:bg-indigo-500/20 text-indigo-400 border border-indigo-500/20 text-xs font-bold rounded-lg transition-all active:scale-95">
                            <i class="fa-solid fa-plus"></i>
                            <span>Add Item Line</span>
                        </button>
                    </div>

                    <!-- Barcode Scanner Block -->
                    <div class="p-4 bg-slate-950/40 border border-slate-850 rounded-xl space-y-3">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div class="flex items-center gap-2.5">
                                <span class="relative flex h-2.5 w-2.5">
                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-emerald-500"></span>
                                </span>
                                <span class="text-xs font-bold uppercase tracking-wider text-slate-300">Scanner Engine Active</span>
                            </div>
                            <span class="text-[10px] text-slate-500 font-mono">Scan barcode directly or enter product code below</span>
                        </div>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-500">
                                <i class="fa-solid fa-barcode text-base"></i>
                            </div>
                            <input type="text" 
                                   x-model="barcodeScanInput"
                                   x-on:keydown.enter.prevent="handleBarcodeScan()"
                                   placeholder="SCAN BARCODE / TYPE PRODUCT CODE AND ENTER..." 
                                   class="w-full pl-11 pr-24 py-3 bg-slate-950/90 border border-slate-800 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 rounded-xl text-sm font-semibold tracking-wider text-slate-200 placeholder-slate-650 transition-all uppercase">
                            <div class="absolute inset-y-1.5 right-1.5 flex items-center">
                                <button type="button"
                                        @click="handleBarcodeScan()"
                                        class="h-full px-4 bg-indigo-500/10 hover:bg-indigo-500/25 border border-indigo-500/20 hover:border-indigo-500/40 text-indigo-400 text-xs font-bold rounded-lg transition-all active:scale-95">
                                    Add Code
                                </button>
                            </div>
                        </div>
                        <!-- Alert messages -->
                        <div x-show="scanNotification" 
                             x-transition:enter="transition ease-out duration-300"
                             x-transition:enter-start="opacity-0 -translate-y-2"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             x-transition:leave="transition ease-in duration-200"
                             x-transition:leave-start="opacity-100 translate-y-0"
                             x-transition:leave-end="opacity-0 -translate-y-2"
                             class="px-3.5 py-2 bg-emerald-500/10 border border-emerald-500/20 rounded-lg flex items-center gap-2 text-xs text-emerald-400"
                             x-cloak>
                            <i class="fa-solid fa-circle-check"></i>
                            <span x-text="scanNotification"></span>
                        </div>
                        <div x-show="scanErrorNotification" 
                             x-transition:enter="transition ease-out duration-300"
                             x-transition:enter-start="opacity-0 -translate-y-2"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             x-transition:leave="transition ease-in duration-200"
                             x-transition:leave-start="opacity-100 translate-y-0"
                             x-transition:leave-end="opacity-0 -translate-y-2"
                             class="px-3.5 py-2 bg-rose-500/10 border border-rose-500/20 rounded-lg flex items-center gap-2 text-xs text-rose-400"
                             x-cloak>
                            <i class="fa-solid fa-circle-xmark"></i>
                            <span x-text="scanErrorNotification"></span>
                        </div>
                    </div>

                    <!-- Line Items list -->
                    <div class="w-full overflow-visible lg:overflow-x-auto responsive-table-container scrollbar-thin">
                        <table class="w-full text-left min-w-0 lg:min-w-[900px] block lg:table">
                            <thead>
                                <tr class="text-xs font-semibold text-slate-500 uppercase tracking-wider border-b border-slate-850 pb-2 hidden lg:table-row">
                                    <th class="w-8 py-2">Sl</th>
                                    <th class="py-2 pl-2">Product Name</th>
                                    <th class="w-20 py-2 pl-2">Unit</th>
                                    <th class="w-24 py-2 pl-2 text-center">P_Qty (Multiplier)</th>
                                    <th class="w-24 py-2 pl-2">Pack Qty</th>
                                    <th class="w-24 py-2 pl-2">Total Pieces</th>
                                    <th class="w-28 py-2 pl-2">Rate (Rs)</th>
                                    <th class="w-32 py-2 text-right">Row Total</th>
                                    <th class="w-10 py-2 text-center"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-800/40 block lg:table-row-group w-full space-y-4 lg:space-y-0 lg:divide-y lg:divide-slate-800/40">
                                <template x-for="(item, index) in items" :key="index">
                                    <tr class="align-middle hover:bg-slate-900/10 transition-all block lg:table-row w-full bg-slate-900/30 border border-slate-800/60 rounded-2xl p-4 lg:p-0 mb-4 lg:mb-0 relative grid grid-cols-2 gap-x-4 gap-y-3 lg:grid-cols-none lg:bg-transparent lg:border-0 lg:hover:bg-slate-900/10 lg:transition-all">
                                        
                                        <!-- Sl No -->
                                        <td class="col-span-2 bg-indigo-500/10 text-indigo-400 px-3 py-1.5 rounded-lg text-xs font-mono font-bold flex items-center justify-between lg:table-cell lg:col-span-none lg:bg-transparent lg:text-slate-500 lg:px-0 lg:py-3 lg:w-8">
                                            <span class="lg:hidden uppercase tracking-wider text-[10px] font-bold text-indigo-400">Line Item #</span>
                                            <span x-text="index + 1" class="text-indigo-300 lg:text-slate-500"></span>
                                            <!-- Mobile delete button -->
                                            <button type="button" 
                                                    @click="removeItemLine(index)" 
                                                    class="lg:hidden p-1 text-rose-400 hover:bg-rose-500/10 rounded-md transition-all">
                                                <i class="fa-solid fa-trash-can text-xs"></i>
                                            </button>
                                        </td>

                                        <!-- Product Selection -->
                                        <td class="py-2 lg:pl-2 col-span-2 block lg:table-cell lg:col-span-none">
                                            <label class="block lg:hidden text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Product Selection</label>
                                            <div class="flex items-center gap-2">
                                                <select name="productName[]" 
                                                        x-model="item.productId" 
                                                        @change="onProductSelect(index)"
                                                        required
                                                        class="flex-1 px-3 py-2 bg-slate-950 border border-slate-850 rounded-lg text-slate-200 text-sm focus:outline-none focus:border-indigo-500">
                                                    <option value="">-- Choose Product --</option>
                                                    <template x-for="p in products" :key="p.id">
                                                        <option :value="p.id" x-text="p.productname + ' [Stock: ' + (p.tqty || 0) + ']'"></option>
                                                    </template>
                                                </select>
                                                <button type="button" 
                                                        @click="$dispatch('open-quick-product-modal', { index: index })" 
                                                        class="px-2.5 py-2 bg-indigo-500/10 hover:bg-indigo-500/25 border border-indigo-500/20 text-indigo-400 hover:text-indigo-300 text-xs font-black rounded-lg transition-all active:scale-95" 
                                                        title="Quick Add New Product">
                                                    <i class="fa-solid fa-plus-circle text-sm"></i>
                                                </button>
                                            </div>
                                            <input type="hidden" name="punit[]" x-model="item.punit">
                                            <input type="hidden" name="slno[]" :value="index + 1">
                                        </td>

                                        <!-- Pack unit details -->
                                        <td class="py-2 lg:pl-2 col-span-1 block lg:table-cell lg:col-span-none">
                                            <label class="block lg:hidden text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Packaging Unit</label>
                                            <div class="flex items-center h-[38px] lg:h-auto pl-1">
                                                <span class="text-xs font-semibold text-slate-400 bg-slate-900/60 px-2.5 py-1.5 rounded-lg border border-slate-800" x-text="item.punit"></span>
                                            </div>
                                        </td>

                                        <!-- Pack size multiplier -->
                                        <td class="py-2 lg:pl-2 col-span-1 block lg:table-cell lg:col-span-none text-center">
                                            <label class="block lg:hidden text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Multiplier (P_Qty)</label>
                                            <div class="flex items-center justify-center h-[38px] lg:h-auto pl-1">
                                                <span class="text-xs font-mono font-bold text-indigo-400 bg-indigo-500/5 px-2.5 py-1.5 rounded-lg border border-indigo-500/10" x-text="item.pqty"></span>
                                                <input type="hidden" name="pqty[]" x-model="item.pqty">
                                            </div>
                                        </td>

                                        <!-- Pack quantity (qty) -->
                                        <td class="py-2 lg:pl-2 col-span-1 block lg:table-cell lg:col-span-none">
                                            <label class="block lg:hidden text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Pack Qty</label>
                                            <input type="number" 
                                                   name="qty[]" 
                                                   x-model.number="item.qty" 
                                                   @input="onPackQtyChange(index)"
                                                   :disabled="item.pqty <= 0"
                                                   required 
                                                   min="0" 
                                                   class="w-full px-2 py-2 bg-slate-950 border border-slate-850 rounded-lg text-slate-200 text-center text-sm focus:outline-none focus:border-indigo-500 disabled:opacity-30">
                                        </td>

                                        <!-- Total pieces (quantity) -->
                                        <td class="py-2 lg:pl-2 col-span-1 block lg:table-cell lg:col-span-none">
                                            <label class="block lg:hidden text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Total Pieces</label>
                                            <input type="number" 
                                                   name="quantity[]" 
                                                   x-model.number="item.quantity" 
                                                   @input="onPieceQtyChange(index)"
                                                   :readonly="item.pqty > 0"
                                                   required 
                                                   min="1" 
                                                   class="w-full px-2 py-2 bg-slate-950 border border-slate-850 rounded-lg text-slate-200 text-center text-sm focus:outline-none focus:border-indigo-500 read-only:bg-slate-900/40 read-only:border-slate-800">
                                        </td>

                                        <!-- Purchase unit rate (rate) -->
                                        <td class="py-2 lg:pl-2 col-span-1 block lg:table-cell lg:col-span-none">
                                            <label class="block lg:hidden text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Rate (Rs)</label>
                                            <input type="number" 
                                                   name="rate[]" 
                                                   x-model.number="item.rate" 
                                                   @input="calculateRowTotal(index)"
                                                   required 
                                                   step="0.01" 
                                                   class="w-full px-2 py-2 bg-slate-950 border border-slate-850 rounded-lg text-slate-200 text-right text-sm focus:outline-none focus:border-indigo-500 font-medium">
                                        </td>

                                        <!-- Row Total -->
                                        <td class="py-2 text-right font-mono font-semibold text-slate-200 lg:pr-1 col-span-2 block lg:table-cell lg:col-span-none pt-3 border-t border-slate-800/40 lg:border-t-0 mt-2 lg:mt-0 flex items-center justify-between lg:block">
                                            <span class="block lg:hidden text-xs font-bold text-slate-500 uppercase tracking-wider">Subtotal</span>
                                            <div class="text-sm lg:text-base font-bold text-slate-200">
                                                Rs. <span x-text="item.total.toFixed(2)"></span>
                                            </div>
                                            <input type="hidden" name="totalValue[]" :value="item.total">
                                        </td>

                                        <!-- Desktop Remove button -->
                                        <td class="py-2 text-center hidden lg:table-cell lg:w-10">
                                            <button type="button" 
                                                    @click="removeItemLine(index)" 
                                                    class="p-1.5 text-slate-500 hover:text-rose-400 hover:bg-rose-500/5 rounded-lg transition-all">
                                                <i class="fa-solid fa-trash-can text-sm"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Right Side: Invoice Financials Summary -->
            <div class="space-y-6">
                <div class="p-4 sm:p-6 glassmorphism rounded-2xl space-y-6 border border-slate-800/60 lg:sticky lg:top-24">
                    <h3 class="text-lg font-bold text-white border-b border-slate-800 pb-3 flex items-center gap-2">
                        <i class="fa-solid fa-calculator text-indigo-400 text-base"></i>
                        <span>Purchase Summary</span>
                    </h3>

                    <!-- Inputs & Settings -->
                    <div class="space-y-4 text-xs font-bold text-slate-400 uppercase tracking-wider">
                        <div class="space-y-1.5">
                            <label for="paymentStatus" class="block">Payment Status</label>
                            <select id="paymentStatus" 
                                    name="paymentStatus" 
                                    x-model.number="financials.paymentStatus" 
                                    @change="onPaymentStatusChange()"
                                    class="w-full px-4 py-2.5 bg-slate-950 border border-slate-850 rounded-xl text-slate-200 focus:outline-none focus:border-indigo-500 font-semibold normal-case">
                                <option value="1">Fully Paid</option>
                                <option value="2">Partially Paid</option>
                                <option value="3">No Paid / Credit</option>
                            </select>
                        </div>
                    </div>

                    <!-- Ledgers Breakdown -->
                    <div class="border-t border-slate-850 pt-4 space-y-3 text-sm text-slate-400">
                        <div class="flex items-center justify-between">
                            <span>Subtotal Amount:</span>
                            <span class="font-mono font-bold text-slate-200">Rs. <span x-text="financials.subTotal.toFixed(2)"></span></span>
                            <input type="hidden" name="subTotalValue" :value="financials.subTotal">
                            <input type="hidden" name="totalAmountValue" :value="financials.subTotal">
                        </div>

                        <div class="space-y-1 pt-2">
                            <label for="discount" class="block text-[10px] font-bold uppercase tracking-wider text-slate-500">Discount Amount (Rs.)</label>
                            <input type="number" 
                                   id="discount" 
                                   name="discount" 
                                   x-model.number="financials.discount" 
                                   @input="calculateTotals()"
                                   step="0.01" 
                                   class="w-full px-3 py-2 bg-slate-950 border border-slate-850 rounded-lg text-slate-200 text-sm font-bold focus:outline-none text-right text-amber-400">
                        </div>

                        <!-- Grand Total -->
                        <div class="flex items-center justify-between pt-4 border-t border-slate-800 text-base font-bold text-white">
                            <span>Grand Total:</span>
                            <span class="font-mono text-xl font-bold text-indigo-400">Rs. <span x-text="financials.grandTotal.toFixed(2)"></span></span>
                            <input type="hidden" name="grandTotalValue" :value="financials.grandTotal">
                        </div>

                        <!-- Paid -->
                        <div class="space-y-1 pt-3">
                            <label for="paid" class="block text-[10px] font-bold uppercase tracking-wider text-slate-400">Paid Amount (Rs.)</label>
                            <input type="number" 
                                   id="paid" 
                                   name="paid" 
                                   x-model.number="financials.paid" 
                                   @input="calculateDue()"
                                   required 
                                   step="0.01" 
                                   class="w-full px-4 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-slate-200 text-lg font-extrabold focus:outline-none text-right text-emerald-400">
                        </div>

                        <!-- Due -->
                        <div class="flex items-center justify-between pt-2 text-sm">
                            <span class="font-semibold">Remaining Due Balance:</span>
                            <span class="font-mono font-bold" :class="financials.due > 0 ? 'text-rose-400' : 'text-slate-500'">Rs. <span x-text="financials.due.toFixed(2)"></span></span>
                            <input type="hidden" name="dueValue" :value="financials.due">
                        </div>
                    </div>

                    <!-- Submit action button -->
                    <button type="submit" 
                            class="w-full py-4 bg-orange-600 hover:bg-orange-700 text-white font-bold rounded-xl text-base shadow-xl shadow-orange-600/10 hover:shadow-orange-600/20 active:scale-[0.98] transition-all flex items-center justify-center gap-2">
                        <i class="fa-solid fa-file-circle-check"></i>
                        <span>Record Purchase</span>
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    function purchaseInvoiceBuilder() {
        return {
            // Data preloaded from PHP
            products: @json($products),
            suppliers: @json($suppliers),
            
            // Component states
            supplier_id: '',
            supplier: {
                name: '',
                contact: ''
            },
            items: [],
            financials: {
                paymentStatus: 1, // Fully paid
                subTotal: 0,
                discount: 0,
                grandTotal: 0,
                paid: 0,
                due: 0
            },

            // Barcode scanner states
            barcodeScanInput: '',
            scanNotification: '',
            scanErrorNotification: '',

            init() {
                // Initialize with one blank line item
                this.addLineItem();
            },

            onSupplierSelect() {
                const s = this.suppliers.find(x => x.id == this.supplier_id);
                if (s) {
                    this.supplier.name = s.name;
                    this.supplier.contact = s.address || '';
                    if (s.phone) {
                        this.supplier.contact += '\nPHONE: ' + s.phone;
                    }
                    if (s.email) {
                        this.supplier.contact += '\nEMAIL: ' + s.email;
                    }
                } else {
                    this.supplier.name = '';
                    this.supplier.contact = '';
                }
            },

            addLineItem() {
                this.items.push({
                    productId: '',
                    punit: 'PCS',
                    pqty: 0,
                    qty: 1,
                    quantity: 1,
                    rate: 0,
                    total: 0
                });
            },

            removeItemLine(index) {
                if (this.items.length > 1) {
                    this.items.splice(index, 1);
                    this.calculateTotals();
                } else {
                    alert('A purchase order must contain at least one line item!');
                }
            },

            onProductSelect(index) {
                const item = this.items[index];
                if (!item.productId) {
                    item.punit = 'PCS';
                    item.pqty = 0;
                    item.qty = 1;
                    item.quantity = 1;
                    item.rate = 0;
                    item.total = 0;
                    this.calculateTotals();
                    return;
                }

                const p = this.products.find(x => x.id == item.productId);
                if (p) {
                    // Set multiplier (pqty)
                    item.pqty = parseInt(p.pqty) || 0;
                    
                    // Set unit label
                    item.punit = p.unit == 2 ? 'BOX' : (p.unit == 3 ? 'PKT' : 'PCS');
                    
                    // Default rates to purchase rate (prate)
                    item.rate = parseFloat(p.prate) || parseFloat(p.mrp) || 0;
                    
                    if (item.pqty > 0) {
                        item.qty = 1;
                        item.quantity = item.qty * item.pqty;
                    } else {
                        item.qty = 0;
                        item.quantity = 1;
                    }
                    this.calculateRowTotal(index);

                    // Automatic Row Creation
                    if (index === this.items.length - 1) {
                        this.addLineItem();
                    }
                }
            },

            onPackQtyChange(index) {
                const item = this.items[index];
                if (item.pqty > 0) {
                    item.quantity = (item.qty || 0) * item.pqty;
                }
                this.calculateRowTotal(index);
            },

            onPieceQtyChange(index) {
                // Should only be triggered if pqty == 0
                this.calculateRowTotal(index);
            },

            calculateRowTotal(index) {
                const item = this.items[index];
                item.total = (item.quantity || 0) * (item.rate || 0);
                this.calculateTotals();
            },

            calculateTotals() {
                // Subtotal
                let subTotal = 0;

                this.items.forEach(item => {
                    subTotal += (item.total || 0);
                });

                this.financials.subTotal = subTotal;

                // Grand total
                const grandTotal = subTotal - (parseFloat(this.financials.discount) || 0);
                this.financials.grandTotal = Math.max(0, grandTotal);

                // Recalculate paid and due
                this.onPaymentStatusChange();
            },

            onPaymentStatusChange() {
                if (this.financials.paymentStatus == 1) { // Fully paid
                    this.financials.paid = this.financials.grandTotal;
                } else if (this.financials.paymentStatus == 3) { // No paid
                    this.financials.paid = 0;
                }
                this.calculateDue();
            },

            calculateDue() {
                const due = this.financials.grandTotal - (this.financials.paid || 0);
                this.financials.due = Math.max(0, due);
            },

            handleBarcodeScan() {
                const code = this.barcodeScanInput.trim();
                if (!code) return;

                // Find product by pcode (case-insensitive)
                const p = this.products.find(x => x.pcode && x.pcode.toLowerCase() === code.toLowerCase());
                if (!p) {
                    this.showScanError(`Product with code "${code}" not found!`);
                    this.barcodeScanInput = '';
                    return;
                }

                // Check if already in list
                const existingIndex = this.items.findIndex(item => item.productId == p.id);
                if (existingIndex !== -1) {
                    const item = this.items[existingIndex];
                    if (item.pqty > 0) {
                        item.qty = (item.qty || 0) + 1;
                        item.quantity = item.qty * item.pqty;
                    } else {
                        item.quantity = (item.quantity || 0) + 1;
                    }
                    this.calculateRowTotal(existingIndex);
                    this.showScanSuccess(`Incremented quantity for "${p.productname}"`);
                } else {
                    // Populate first blank row or append new row
                    let blankIndex = this.items.findIndex(item => !item.productId);
                    if (blankIndex !== -1) {
                        this.items[blankIndex].productId = p.id;
                        this.onProductSelect(blankIndex);
                    } else {
                        this.addLineItem();
                        let newIndex = this.items.length - 1;
                        this.items[newIndex].productId = p.id;
                        this.onProductSelect(newIndex);
                    }
                    this.showScanSuccess(`Added "${p.productname}" to items`);
                }

                // Clear input
                this.barcodeScanInput = '';
            },

            showScanSuccess(msg) {
                this.scanErrorNotification = '';
                this.scanNotification = msg;
                setTimeout(() => {
                    if (this.scanNotification === msg) {
                        this.scanNotification = '';
                    }
                }, 3000);
            },

            showScanError(msg) {
                this.scanNotification = '';
                this.scanErrorNotification = msg;
                setTimeout(() => {
                    if (this.scanErrorNotification === msg) {
                        this.scanErrorNotification = '';
                    }
                }, 3000);
            },

            onQuickProductCreated(detail) {
                this.products.push(detail.product);
                if (detail.index !== null && this.items[detail.index]) {
                    this.items[detail.index].productId = detail.product.id;
                    this.onProductSelect(detail.index);
                }
            },

            handleSubmit(event) {
                // Filter out all empty rows
                this.items = this.items.filter(item => item.productId !== '');
                if (this.items.length === 0) {
                    alert('A purchase order must contain at least one line item!');
                    event.preventDefault();
                    return;
                }
                this.calculateTotals();
            }
        };
    }
</script>
<x-admin.quick-product-modal />
@endsection
