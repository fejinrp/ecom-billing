@extends('layouts.admin', ['title' => 'Generate Sales Quotation'])

@section('content')
<div class="space-y-6" x-data="quotationBuilder()">
    <!-- Header -->
    <div class="flex items-center gap-4">
        <a href="{{ route('admin.quotations.index') }}" class="p-2 text-slate-400 hover:text-white hover:bg-slate-900 rounded-xl transition-all">
            <i class="fa-solid fa-arrow-left text-lg"></i>
        </a>
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight">Create Sales Quotation</h1>
            <p class="text-sm text-slate-400">Generate a custom estimate, proforma invoice, or sales proposal draft.</p>
        </div>
    </div>

    <!-- Error/Validation alert -->
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

    <form action="{{ route('admin.quotations.store') }}" method="POST" enctype="multipart/form-data" x-on:submit="handleSubmit($event)" class="space-y-6">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left Side: Order & Customer Details (2 Columns wide) -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Customer Information Card -->
                <div class="p-4 sm:p-6 glassmorphism rounded-2xl space-y-4">
                    <h3 class="text-lg font-bold text-white flex items-center gap-2 border-b border-slate-800 pb-3">
                        <i class="fa-solid fa-user-tag text-indigo-400 text-base"></i>
                        <span>Client Information</span>
                    </h3>

                    <!-- Customer Select / Add New -->
                    <div class="grid grid-cols-1 gap-4">
                        <div class="space-y-1.5">
                            <label for="clientSelection" class="block text-xs font-bold text-slate-400 uppercase tracking-wider">Select Client Account</label>
                            <select id="clientSelection" 
                                    x-model="clientSelection" 
                                    @change="onClientChange()"
                                    class="w-full px-4 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-slate-200 focus:outline-none focus:border-indigo-500 text-sm">
                                <option value="0">-- Walk-in / Enter Details Manually --</option>
                                <template x-for="c in customers" :key="c.id">
                                    <option :value="c.id" x-text="c.uname + ' (' + (c.contactno || 'No Contact') + ')'"></option>
                                </template>
                            </select>
                        </div>
                    </div>

                    <!-- Client Inputs -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="space-y-1.5">
                            <label for="clientName" class="block text-xs font-bold text-slate-400 uppercase tracking-wider">Client Name</label>
                            <input type="text" 
                                   id="clientName" 
                                   name="clientName" 
                                   x-model="client.name"
                                   required 
                                   placeholder="Full Client / Company Name" 
                                   class="w-full px-4 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-slate-200 placeholder-slate-650 focus:outline-none focus:border-indigo-500 text-sm uppercase">
                        </div>

                        <div class="space-y-1.5">
                            <label for="orderDate" class="block text-xs font-bold text-slate-400 uppercase tracking-wider">Quotation Date</label>
                            <input type="date" 
                                   id="orderDate" 
                                   name="orderDate" 
                                   value="{{ date('Y-m-d') }}" 
                                   required 
                                   class="w-full px-4 py-2.5 bg-slate-950 border border-slate-850 rounded-xl text-slate-200 focus:outline-none focus:border-indigo-500 text-sm">
                        </div>

                        <div class="space-y-1.5">
                            <label for="mobileno" class="block text-xs font-bold text-slate-400 uppercase tracking-wider">Contact Number</label>
                            <input type="text" 
                                   id="mobileno" 
                                   name="mobileno" 
                                   x-model="client.mobile"
                                   required 
                                   placeholder="Client mobile or telephone" 
                                   class="w-full px-4 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-slate-200 placeholder-slate-650 focus:outline-none focus:border-indigo-500 text-sm">
                        </div>

                        <div class="space-y-1.5">
                            <label for="gsttin" class="block text-xs font-bold text-slate-400 uppercase tracking-wider">GSTIN Number (Optional)</label>
                            <input type="text" 
                                   id="gsttin" 
                                   name="gsttin" 
                                   x-model="client.gsttin"
                                   placeholder="GSTIN if applicable" 
                                   class="w-full px-4 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-slate-200 placeholder-slate-650 focus:outline-none focus:border-indigo-500 text-sm uppercase">
                        </div>

                        <div class="sm:col-span-2 space-y-1.5">
                            <label for="clientContact" class="block text-xs font-bold text-slate-400 uppercase tracking-wider">Billing Address / Description</label>
                            <textarea id="clientContact" 
                                      name="clientContact" 
                                      x-model="client.address"
                                      required 
                                      rows="2"
                                      placeholder="Full company or billing address..." 
                                      class="w-full px-4 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-slate-200 placeholder-slate-650 focus:outline-none focus:border-indigo-500 text-sm uppercase"></textarea>
                        </div>
                    </div>
                </div>

                <!-- Invoice Line Items Table -->
                <div class="p-4 sm:p-6 glassmorphism rounded-2xl space-y-4">
                    <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                        <h3 class="text-lg font-bold text-white flex items-center gap-2">
                            <i class="fa-solid fa-file-invoice-dollar text-indigo-400 text-base"></i>
                            <span>Quotation Draft Items</span>
                        </h3>
                        <button type="button" 
                                @click="addLineItem()" 
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-indigo-500/10 hover:bg-indigo-500/20 text-indigo-400 border border-indigo-500/20 text-xs font-bold rounded-lg transition-all active:scale-95">
                            <i class="fa-solid fa-plus"></i>
                            <span>Add Custom Line</span>
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

                    <!-- Line Items List -->
                    <div class="w-full overflow-visible lg:overflow-x-auto responsive-table-container scrollbar-thin">
                        <table class="w-full text-left min-w-0 lg:min-w-[700px] block lg:table">
                            <thead>
                                <tr class="text-xs font-semibold text-slate-500 uppercase tracking-wider border-b border-slate-850 pb-2 hidden lg:table-row">
                                    <th class="w-8 py-2">Sl</th>
                                    <th class="py-2 pl-2">Product / Line Description</th>
                                    <th class="w-20 py-2 pl-2">Qty</th>
                                    <th class="w-28 py-2 pl-2">Rate (Rs)</th>
                                    <th class="w-24 py-2 pl-2">GST %</th>
                                    <th class="w-32 py-2 text-right">Total</th>
                                    <th class="w-10 py-2 text-center"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-800/40 block lg:table-row-group w-full space-y-4 lg:space-y-0 lg:divide-y lg:divide-slate-800/40">
                                <template x-for="(item, index) in items" :key="index">
                                    <tr class="align-middle hover:bg-slate-900/10 transition-all block lg:table-row w-full bg-slate-900/30 border border-slate-800/60 rounded-2xl p-4 lg:p-0 mb-4 lg:mb-0 relative grid grid-cols-2 gap-x-4 gap-y-3 lg:grid-cols-none lg:bg-transparent lg:border-0 lg:hover:bg-slate-900/10 lg:transition-all">
                                        
                                        <!-- Sl No / Mobile Card Header -->
                                        <td class="col-span-2 bg-indigo-500/10 text-indigo-400 px-3 py-1.5 rounded-lg text-xs font-mono font-bold flex items-center justify-between lg:table-cell lg:col-span-none lg:bg-transparent lg:text-slate-500 lg:px-0 lg:py-3 lg:w-8">
                                            <span class="lg:hidden uppercase tracking-wider text-[10px] font-bold text-indigo-400">Line Item #</span>
                                            <span x-text="index + 1" class="text-indigo-300 lg:text-slate-500"></span>
                                            <!-- Mobile delete button (visible only on mobile/tablet) -->
                                            <button type="button" 
                                                    @click="removeItemLine(index)" 
                                                    class="lg:hidden p-1 text-rose-400 hover:bg-rose-500/10 rounded-md transition-all">
                                                <i class="fa-solid fa-trash-can text-xs"></i>
                                            </button>
                                        </td>
                                        
                                        <!-- Product select OR Custom Name -->
                                        <td class="py-2 lg:pl-2 col-span-2 block lg:table-cell lg:col-span-none">
                                            <label class="block lg:hidden text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Product / Line Description</label>
                                            <div class="flex gap-2">
                                                <input type="text" 
                                                       name="productName[]" 
                                                       x-model="item.productName"
                                                       required
                                                       list="quotation-product-suggestions"
                                                       @change="onCustomProductInput(index)"
                                                       placeholder="Type custom or select suggestion..."
                                                       class="w-full px-3 py-2 bg-slate-950 border border-slate-850 rounded-lg text-slate-200 text-sm focus:outline-none focus:border-indigo-500 uppercase">
                                            </div>
                                            <input type="hidden" name="hsnsac[]" x-model="item.hsnsac">
                                            <input type="hidden" name="gst[]" x-model="item.gst">
                                            <input type="hidden" name="unit[]" x-model="item.unit">
                                            <input type="hidden" name="wgst[]" :value="item.wgst">
                                        </td>

                                        <!-- Qty -->
                                        <td class="py-2 lg:pl-2 col-span-1 block lg:table-cell lg:col-span-none">
                                            <label class="block lg:hidden text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Quantity</label>
                                            <input type="number" 
                                                   name="quantity[]" 
                                                   x-model.number="item.qty" 
                                                   @input="calculateRowTotal(index)"
                                                   required 
                                                   min="1" 
                                                   class="w-full px-2 py-2 bg-slate-950 border border-slate-850 rounded-lg text-slate-200 text-center text-sm focus:outline-none focus:border-indigo-500">
                                        </td>

                                        <!-- Rate -->
                                        <td class="py-2 lg:pl-2 col-span-1 block lg:table-cell lg:col-span-none">
                                            <label class="block lg:hidden text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Rate (Rs)</label>
                                            <input type="number" 
                                                   name="rateValue[]" 
                                                   x-model.number="item.rate" 
                                                   @input="calculateRowTotal(index)"
                                                   required 
                                                   step="0.01" 
                                                   class="w-full px-2 py-2 bg-slate-950 border border-slate-850 rounded-lg text-slate-200 text-right text-sm focus:outline-none focus:border-indigo-500 font-medium">
                                        </td>

                                        <!-- GST selection -->
                                        <td class="py-2 lg:pl-2 col-span-1 block lg:table-cell lg:col-span-none">
                                            <label class="block lg:hidden text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">GST %</label>
                                            <select x-model.number="item.gst" 
                                                    @change="calculateRowTotal(index)"
                                                    class="w-full px-2 py-2 bg-slate-950 border border-slate-850 rounded-lg text-slate-200 text-sm focus:outline-none focus:border-indigo-500">
                                                <option value="0">0%</option>
                                                <option value="5">5%</option>
                                                <option value="12">12%</option>
                                                <option value="18">18%</option>
                                                <option value="28">28%</option>
                                            </select>
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
                <!-- Summary Card -->
                <div class="p-4 sm:p-6 glassmorphism rounded-2xl space-y-6 border border-slate-800/60 lg:sticky lg:top-24">
                    <h3 class="text-lg font-bold text-white border-b border-slate-800 pb-3 flex items-center gap-2">
                        <i class="fa-solid fa-calculator text-indigo-400 text-base"></i>
                        <span>Financial Summary</span>
                    </h3>

                    <!-- Settings -->
                    <div class="space-y-4 text-xs font-bold text-slate-400 uppercase tracking-wider">
                        <div class="space-y-1.5">
                            <label for="qtype" class="block">Quotation / Document Type</label>
                            <select id="qtype" 
                                    name="qtype" 
                                    class="w-full px-4 py-2.5 bg-slate-950 border border-slate-850 rounded-xl text-slate-200 focus:outline-none focus:border-indigo-500 font-semibold normal-case">
                                <option value="3">Sales Quotation</option>
                                <option value="1">Proforma Invoice</option>
                                <option value="2">Cost Estimate</option>
                            </select>
                        </div>

                        <div class="space-y-1.5">
                            <label for="qstate" class="block">Document Status</label>
                            <select id="qstate" 
                                    name="qstate" 
                                    class="w-full px-4 py-2.5 bg-slate-950 border border-slate-850 rounded-xl text-slate-200 focus:outline-none focus:border-indigo-500 font-semibold normal-case">
                                <option value="1">Active / Pending Approval</option>
                                <option value="2">Draft</option>
                            </select>
                        </div>

                        <div class="space-y-1.5">
                            <label for="signature" class="block">Cashier Signature (Optional)</label>
                            <input type="file" 
                                   id="signature" 
                                   name="signature" 
                                   accept="image/*"
                                   class="w-full px-3 py-2 bg-slate-950 border border-slate-850 rounded-xl text-slate-400 file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-indigo-600/10 file:text-indigo-400 hover:file:bg-indigo-600/20 text-xs normal-case cursor-pointer">
                        </div>
                    </div>

                    <!-- Ledgers Breakdown -->
                    <div class="border-t border-slate-850 pt-4 space-y-2 text-sm text-slate-400">
                        <div class="flex items-center justify-between">
                            <span>Subtotal Amount:</span>
                            <span class="font-mono font-bold text-slate-200">Rs. <span x-text="financials.subTotal.toFixed(2)"></span></span>
                            <input type="hidden" name="subTotal" :value="financials.subTotal">
                        </div>

                        <div class="flex items-center justify-between text-xs">
                            <span>Tax GST amount:</span>
                            <span class="font-mono font-semibold text-slate-300">Rs. <span x-text="financials.tax.toFixed(2)"></span></span>
                            <input type="hidden" name="igst" :value="financials.tax">
                        </div>

                        <!-- Ext charges -->
                        <div class="grid grid-cols-2 gap-2 pt-2 border-t border-slate-900">
                            <div class="space-y-1">
                                <label for="shipcharge" class="block text-[10px] font-bold uppercase tracking-wider text-slate-500">Shipping (Rs.)</label>
                                <input type="number" 
                                       id="shipcharge" 
                                       name="shipcharge" 
                                       x-model.number="financials.shipping" 
                                       @input="calculateTotals()"
                                       step="0.01" 
                                       class="w-full px-3 py-1.5 bg-slate-950 border border-slate-850 rounded-lg text-slate-200 text-sm font-semibold focus:outline-none text-right">
                            </div>
                            <div class="space-y-1">
                                <label for="intcharge" class="block text-[10px] font-bold uppercase tracking-wider text-slate-500">Installation / Ext (Rs.)</label>
                                <input type="number" 
                                       id="intcharge" 
                                       name="intcharge" 
                                       x-model.number="financials.interest" 
                                       @input="calculateTotals()"
                                       step="0.01" 
                                       class="w-full px-3 py-1.5 bg-slate-950 border border-slate-850 rounded-lg text-slate-200 text-sm font-semibold focus:outline-none text-right">
                            </div>
                        </div>

                        <div class="space-y-1 pt-2">
                            <label for="discount" class="block text-[10px] font-bold uppercase tracking-wider text-slate-500">Special Discount (Rs.)</label>
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
                            <span>Quoted Total:</span>
                            <span class="font-mono text-xl font-bold text-indigo-400">Rs. <span x-text="financials.grandTotal.toFixed(2)"></span></span>
                            <input type="hidden" name="grandTotal" :value="financials.grandTotal">
                            <input type="hidden" name="gTotal" :value="financials.grandTotal">
                        </div>
                    </div>

                    <!-- Submit action button -->
                    <button type="submit" 
                            class="w-full py-4 bg-orange-600 hover:bg-orange-700 text-white font-bold rounded-xl text-base shadow-xl shadow-orange-600/10 hover:shadow-orange-600/20 active:scale-[0.98] transition-all flex items-center justify-center gap-2">
                        <i class="fa-solid fa-file-pdf"></i>
                        <span>Generate Quotation</span>
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Datalist Suggestions for custom product inputs -->
<datalist id="quotation-product-suggestions">
    @foreach ($products as $p)
        <option value="{{ $p->productname }}"></option>
    @endforeach
</datalist>

<script>
    function quotationBuilder() {
        return {
            // Data preloaded from PHP
            products: @json($products),
            customers: @json($customers),
            
            // Component states
            clientSelection: '0',
            client: {
                name: '',
                mobile: '',
                gsttin: '',
                address: ''
            },
            items: [],
            financials: {
                subTotal: 0,
                tax: 0,
                shipping: 0,
                interest: 0,
                discount: 0,
                grandTotal: 0
            },

            // Barcode scanner states
            barcodeScanInput: '',
            scanNotification: '',
            scanErrorNotification: '',

            onClientChange() {
                if (this.clientSelection == '0') {
                    this.client = { name: '', mobile: '', gsttin: '', address: '' };
                } else {
                    const c = this.customers.find(x => x.id == this.clientSelection);
                    if (c) {
                        this.client = {
                            name: c.uname || '',
                            mobile: c.contactno || '',
                            gsttin: c.gsttin || '',
                            address: c.billingaddress || c.shippingaddress || ''
                        };
                    }
                }
            },

            init() {
                // Initialize with one blank line item
                this.addLineItem();
            },

            addLineItem() {
                this.items.push({
                    productName: '',
                    qty: 1,
                    rate: 0,
                    gst: 18,
                    unit: 'PCS',
                    hsnsac: '',
                    wgst: 0,
                    total: 0
                });
            },

            removeItemLine(index) {
                if (this.items.length > 1) {
                    this.items.splice(index, 1);
                    this.calculateTotals();
                } else {
                    alert('A quotation must contain at least one line item!');
                }
            },

            onCustomProductInput(index) {
                const item = this.items[index];
                const queryName = item.productName.toUpperCase().trim();
                
                if (!queryName) {
                    item.rate = 0;
                    item.gst = 18;
                    item.unit = 'PCS';
                    item.hsnsac = '';
                    item.wgst = 0;
                    item.total = 0;
                    this.calculateTotals();
                    return;
                }

                // Check if typed name matches an existing product in suggestions
                const p = this.products.find(x => x.productname.toUpperCase() === queryName);
                if (p) {
                    item.rate = p.srate || p.mrp || 0;
                    item.gst = p.gst || 18;
                    item.hsnsac = p.hsnsac || '';
                    item.unit = p.unit == 2 ? 'BOX' : (p.unit == 3 ? 'PKT' : 'PCS');
                } else {
                    // Custom non-inventory line defaults
                    item.unit = 'PCS';
                    item.hsnsac = '9987'; // General service code
                }
                this.calculateRowTotal(index);

                // Automatic Row Creation when product name is set on the last line
                if (index === this.items.length - 1) {
                    this.addLineItem();
                }
            },

            calculateRowTotal(index) {
                const item = this.items[index];
                item.total = (item.qty || 0) * (item.rate || 0);
                
                // Calculate tax component for this item
                // wgst/gstr is the exact GST tax portion of the total line value
                item.wgst = item.total * ((item.gst || 0) / 100);
                
                this.calculateTotals();
            },

            calculateTotals() {
                let subTotal = 0;
                let taxSum = 0;

                this.items.forEach(item => {
                    const lineVal = (item.qty || 0) * (item.rate || 0);
                    subTotal += lineVal;

                    const lineGst = lineVal * ((item.gst || 0) / 100);
                    taxSum += lineGst;
                });

                this.financials.subTotal = subTotal;
                this.financials.tax = taxSum;

                const grandTotal = subTotal + taxSum + 
                                   (parseFloat(this.financials.shipping) || 0) + 
                                   (parseFloat(this.financials.interest) || 0) - 
                                   (parseFloat(this.financials.discount) || 0);
                
                this.financials.grandTotal = Math.max(0, grandTotal);
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

                // Check if already in list (using productName)
                const existingIndex = this.items.findIndex(item => item.productName.toUpperCase() === p.productname.toUpperCase());
                if (existingIndex !== -1) {
                    const item = this.items[existingIndex];
                    item.qty = (item.qty || 0) + 1;
                    this.calculateRowTotal(existingIndex);
                    this.showScanSuccess(`Incremented quantity for "${p.productname}"`);
                } else {
                    // Populate first blank row or append new row
                    let blankIndex = this.items.findIndex(item => !item.productName.trim());
                    if (blankIndex !== -1) {
                        this.items[blankIndex].productName = p.productname;
                        this.onCustomProductInput(blankIndex);
                    } else {
                        this.addLineItem();
                        let newIndex = this.items.length - 1;
                        this.items[newIndex].productName = p.productname;
                        this.onCustomProductInput(newIndex);
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

            handleSubmit(event) {
                if (this.items.length > 1 && !this.items[this.items.length - 1].productName) {
                    this.items.pop();
                }
            }
        };
    }
</script>
@endsection
