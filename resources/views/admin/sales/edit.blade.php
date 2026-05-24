@extends('layouts.admin', ['title' => 'Update Sales Invoice'])

@section('content')
<div class="space-y-6" x-data="salesInvoiceUpdater()">
    <!-- Header -->
    <x-admin.header 
        description="Modifying Bill #{{ str_pad($sale->morder_id, 4, '0', STR_PAD_LEFT) }} with stock-safe rollback protection."
    >
        <x-slot:title>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.sales.index') }}" class="p-2 text-slate-400 hover:text-white hover:bg-slate-900 rounded-xl transition-all">
                    <i class="fa-solid fa-arrow-left text-lg"></i>
                </a>
                <span>Update Sales Invoice</span>
            </div>
        </x-slot:title>
    </x-admin.header>

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

    <form action="{{ route('admin.sales.update', $sale->order_id) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left Side: Order & Customer Details (2 Columns wide) -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Customer Information Card -->
                <div class="p-4 sm:p-6 glassmorphism rounded-2xl space-y-4">
                    <h3 class="text-lg font-bold text-slate-800 dark:text-white flex items-center gap-2 border-b border-slate-200 dark:border-slate-800 pb-3">
                        <i class="fa-solid fa-user-tag text-indigo-400 text-base"></i>
                        <span>Customer Profile</span>
                    </h3>

                    <!-- Customer Info (View Only / Linked) -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="space-y-1.5">
                            <span class="block text-xs font-bold text-slate-500 uppercase tracking-wider">Client Profile</span>
                            <span class="block text-sm font-semibold text-slate-700 dark:text-slate-200 py-2.5 px-4 bg-slate-100 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl">
                                {{ $sale->client_name }} (Registered Profile)
                            </span>
                        </div>

                        <div class="space-y-1.5">
                            <label for="orderDate" class="block text-xs font-bold text-slate-400 uppercase tracking-wider">Order Date</label>
                            <input type="date" 
                                   id="orderDate" 
                                   name="orderDate" 
                                   value="{{ date('Y-m-d', strtotime($sale->order_date)) }}" 
                                   required 
                                   class="w-full px-4 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-slate-200 focus:outline-none focus:border-indigo-500 text-sm">
                        </div>
                    </div>

                    <!-- Client Inputs -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                        <div class="space-y-1.5">
                            <label for="clientName" class="block text-xs font-bold text-slate-400 uppercase tracking-wider">Customer Name</label>
                            <input type="text" 
                                   id="clientName" 
                                   name="clientName" 
                                   x-model="client.name" 
                                   required 
                                   placeholder="Full customer name" 
                                   class="w-full px-4 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-slate-200 placeholder-slate-600 focus:outline-none focus:border-indigo-500 text-sm uppercase">
                        </div>

                        <div class="space-y-1.5">
                            <label for="mobileno" class="block text-xs font-bold text-slate-400 uppercase tracking-wider">Mobile Number</label>
                            <input type="text" 
                                   id="mobileno" 
                                   name="mobileno" 
                                   x-model="client.mobile" 
                                   required 
                                   placeholder="10-digit mobile" 
                                   class="w-full px-4 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-slate-200 placeholder-slate-600 focus:outline-none focus:border-indigo-500 text-sm">
                        </div>

                        <div class="sm:col-span-2 space-y-1.5">
                            <label for="clientContact" class="block text-xs font-bold text-slate-400 uppercase tracking-wider">Billing / Shipping Address</label>
                            <textarea id="clientContact" 
                                      name="clientContact" 
                                      x-model="client.address" 
                                      required 
                                      rows="2"
                                      placeholder="Detailed street and locality address..." 
                                      class="w-full px-4 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-slate-200 placeholder-slate-600 focus:outline-none focus:border-indigo-500 text-sm uppercase"></textarea>
                        </div>

                        <div class="space-y-1.5">
                            <label for="city" class="block text-xs font-bold text-slate-400 uppercase tracking-wider">City</label>
                            <input type="text" 
                                   id="city" 
                                   name="city" 
                                   x-model="client.city" 
                                   required 
                                   placeholder="City" 
                                   class="w-full px-4 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-slate-200 placeholder-slate-600 focus:outline-none focus:border-indigo-500 text-sm uppercase">
                        </div>

                        <div class="space-y-1.5">
                            <label for="state" class="block text-xs font-bold text-slate-400 uppercase tracking-wider">State</label>
                            <select id="state" 
                                    name="state" 
                                    x-model="client.state" 
                                    required 
                                    class="w-full px-4 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-slate-200 focus:outline-none focus:border-indigo-500 text-sm">
                                <option value="">-- Choose State --</option>
                                @foreach ($states as $s)
                                    <option value="{{ $s->sname }}">{{ $s->sname }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="space-y-1.5">
                            <label for="pincode" class="block text-xs font-bold text-slate-400 uppercase tracking-wider">Pincode</label>
                            <input type="text" 
                                   id="pincode" 
                                   name="pincode" 
                                   x-model="client.pincode" 
                                   required 
                                   placeholder="6-digit pincode" 
                                   class="w-full px-4 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-slate-200 placeholder-slate-600 focus:outline-none focus:border-indigo-500 text-sm">
                        </div>

                        <div class="space-y-1.5">
                            <label for="gsttin" class="block text-xs font-bold text-slate-400 uppercase tracking-wider">GSTIN Number (Optional)</label>
                            <input type="text" 
                                   id="gsttin" 
                                   name="gsttin" 
                                   x-model="client.gsttin" 
                                   placeholder="GSTIN if applicable" 
                                   class="w-full px-4 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-slate-200 placeholder-slate-600 focus:outline-none focus:border-indigo-500 text-sm uppercase">
                        </div>
                    </div>
                </div>

                <!-- Invoice Line Items Table -->
                <div class="p-4 sm:p-6 glassmorphism rounded-2xl space-y-4">
                    <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-800 pb-3">
                        <h3 class="text-lg font-bold text-slate-800 dark:text-white flex items-center gap-2">
                            <i class="fa-solid fa-file-invoice-dollar text-indigo-400 text-base"></i>
                            <span>Invoice Items</span>
                        </h3>
                        <button type="button" 
                                @click="addLineItem()" 
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-indigo-500/10 hover:bg-indigo-500/20 text-indigo-400 border border-indigo-500/20 text-xs font-bold rounded-lg transition-all active:scale-95">
                            <i class="fa-solid fa-plus"></i>
                            <span>Add Item Line</span>
                        </button>
                    </div>

                    <!-- Line Items List -->
                    <div class="w-full overflow-visible lg:overflow-x-auto responsive-table-container scrollbar-thin">
                        <table class="w-full text-left min-w-0 lg:min-w-[700px] block lg:table">
                            <thead>
                                <tr class="text-xs font-bold text-slate-500 dark:text-slate-300 uppercase tracking-wider border-b border-slate-200 dark:border-slate-800 pb-3 hidden lg:table-row">
                                    <th class="w-8 py-3">Sl</th>
                                    <th class="py-3 pl-2">Product Name</th>
                                    <th class="w-24 py-3 pl-2">Stock + Prior</th>
                                    <th class="w-20 py-3 pl-2">Qty</th>
                                    <th class="w-28 py-3 pl-2">Rate (Rs)</th>
                                    <th class="w-20 py-3 pl-2">GST %</th>
                                    <th class="w-32 py-3 text-right">Total</th>
                                    <th class="w-10 py-3 text-center"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-800/40 block lg:table-row-group w-full space-y-4 lg:space-y-0 lg:divide-y lg:divide-slate-800/40">
                                <template x-for="(item, index) in items" :key="index">
                                    <tr class="align-middle hover:bg-slate-900/10 transition-all block lg:table-row w-full bg-slate-900/30 border border-slate-800/60 rounded-2xl p-4 lg:p-0 mb-4 lg:mb-0 relative grid grid-cols-2 gap-x-4 gap-y-3 lg:grid-cols-none lg:bg-transparent lg:border-0 lg:hover:bg-slate-900/10 lg:transition-all">
                                        
                                        <!-- Sl No / Mobile Card Header -->
                                        <td class="col-span-2 bg-indigo-500/10 text-indigo-400 px-3 py-1.5 rounded-lg text-xs font-mono font-bold flex items-center justify-between lg:table-cell lg:col-span-none lg:bg-transparent lg:text-slate-450 lg:px-0 lg:py-3 lg:w-8">
                                            <span class="lg:hidden uppercase tracking-wider text-[10px] font-bold text-indigo-400">Line Item #</span>
                                            <span x-text="index + 1" class="text-indigo-300 lg:text-slate-455"></span>
                                            <!-- Mobile delete button (visible only on mobile/tablet) -->
                                            <button type="button" 
                                                    @click="removeItemLine(index)" 
                                                    class="lg:hidden p-1 text-rose-400 hover:bg-rose-500/10 rounded-md transition-all">
                                                <i class="fa-solid fa-trash-can text-xs"></i>
                                            </button>
                                        </td>
                                        
                                        <!-- Product select -->
                                        <td class="py-2 lg:pl-2 col-span-2 block lg:table-cell lg:col-span-none">
                                            <label class="block lg:hidden text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Product Selection</label>
                                            <select name="productName[]" 
                                                    x-model="item.productId" 
                                                    @change="onProductSelect(index)"
                                                    required
                                                    class="w-full px-3 py-2 bg-slate-950 border border-slate-850 rounded-lg text-slate-200 text-sm focus:outline-none focus:border-indigo-500">
                                                <option value="">-- Choose Product --</option>
                                                <template x-for="p in products" :key="p.id">
                                                    <option :value="p.id" x-text="p.productname"></option>
                                                </template>
                                            </select>
                                            <input type="hidden" name="hsnsac[]" x-model="item.hsnsac">
                                            <input type="hidden" name="gst[]" x-model="item.gst">
                                            <input type="hidden" name="unit[]" x-model="item.unit">
                                            <input type="hidden" name="slno[]" :value="index + 1">
                                        </td>
 
                                        <!-- Stock display -->
                                        <td class="py-2 lg:pl-2 col-span-1 block lg:table-cell lg:col-span-none">
                                            <label class="block lg:hidden text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Stock + Prior</label>
                                            <div class="flex items-center h-[38px] lg:h-auto">
                                                <span class="text-xs font-semibold px-2 py-1 rounded bg-slate-100 dark:bg-slate-900 border border-slate-200 dark:border-slate-800" 
                                                      :class="item.stock <= 0 ? 'text-rose-450 bg-rose-500/10' : 'text-slate-700 dark:text-slate-400'"
                                                      x-text="item.stock + ' ' + item.unit"></span>
                                            </div>
                                        </td>
 
                                        <!-- Qty -->
                                        <td class="py-2 lg:pl-2 col-span-1 block lg:table-cell lg:col-span-none">
                                            <label class="block lg:hidden text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Quantity</label>
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
                                            <label class="block lg:hidden text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Rate (Rs)</label>
                                            <input type="number" 
                                                   name="rateValue[]" 
                                                   x-model.number="item.rate" 
                                                   @input="calculateRowTotal(index)"
                                                   required 
                                                   step="0.01" 
                                                   class="w-full px-2 py-2 bg-slate-950 border border-slate-850 rounded-lg text-slate-200 text-right text-sm focus:outline-none focus:border-indigo-500 font-medium">
                                        </td>
 
                                        <!-- GST rate -->
                                        <td class="py-2 lg:pl-2 col-span-1 block lg:table-cell lg:col-span-none">
                                            <label class="block lg:hidden text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">GST Rate</label>
                                            <div class="flex items-center h-[38px] lg:h-auto pl-1 lg:pl-3">
                                                <span class="text-xs font-mono font-bold text-slate-400" x-text="item.gst + '%'"></span>
                                            </div>
                                        </td>
 
                                        <!-- Row Total -->
                                        <td class="py-2 text-right font-mono font-semibold text-slate-200 lg:pr-1 col-span-2 block lg:table-cell lg:col-span-none pt-3 border-t border-slate-800/40 lg:border-t-0 mt-2 lg:mt-0 flex items-center justify-between lg:block">
                                            <span class="block lg:hidden text-xs font-bold text-slate-400 uppercase tracking-wider">Subtotal</span>
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
                <div class="p-4 sm:p-6 glassmorphism rounded-2xl space-y-6 border border-slate-200 dark:border-slate-800/60 lg:sticky lg:top-24">
                    <h3 class="text-lg font-bold text-slate-800 dark:text-white border-b border-slate-200 dark:border-slate-800 pb-3 flex items-center gap-2">
                        <i class="fa-solid fa-calculator text-indigo-400 text-base"></i>
                        <span>Invoice Summary</span>
                    </h3>

                    <!-- Inputs & Settings -->
                    <div class="space-y-4 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                        <div class="space-y-1.5">
                            <label for="paymentPlace" class="block">GST Tax Mode</label>
                            <select id="paymentPlace" 
                                    name="paymentPlace" 
                                    x-model.number="financials.paymentPlace" 
                                    @change="calculateTotals()"
                                    class="w-full px-4 py-2.5 bg-slate-950 border border-slate-850 rounded-xl text-slate-200 focus:outline-none focus:border-indigo-500 font-semibold normal-case">
                                <option value="1">Intra-State (CGST + SGST)</option>
                                <option value="2">Inter-State (IGST)</option>
                            </select>
                        </div>

                        <div class="space-y-1.5">
                            <label for="paymentType" class="block">Payment Mode</label>
                            <select id="paymentType" 
                                    name="paymentType" 
                                    x-model.number="financials.paymentType"
                                    class="w-full px-4 py-2.5 bg-slate-950 border border-slate-850 rounded-xl text-slate-200 focus:outline-none focus:border-indigo-500 font-semibold normal-case">
                                <option value="2">Cash Payment</option>
                                <option value="1">Cheque Payment</option>
                                <option value="3">Online / UPI Transfer</option>
                            </select>
                        </div>

                        <div class="space-y-1.5">
                            <label for="paymentName" class="block">Cashier / Account tag</label>
                            <input type="text" 
                                    id="paymentName" 
                                    name="paymentName" 
                                    x-model="financials.paymentName"
                                    required 
                                    class="w-full px-4 py-2.5 bg-slate-950 border border-slate-850 rounded-xl text-slate-200 focus:outline-none focus:border-indigo-500 text-sm normal-case">
                        </div>

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
                    <div class="border-t border-slate-200 dark:border-slate-850 pt-4 space-y-2 text-sm text-slate-500 dark:text-slate-400">
                        <div class="flex items-center justify-between">
                            <span>Subtotal Amount:</span>
                            <span class="font-mono font-bold text-slate-800 dark:text-slate-200">Rs. <span x-text="financials.subTotal.toFixed(2)"></span></span>
                            <input type="hidden" name="subTotalValue" :value="financials.subTotal">
                            <input type="hidden" name="totalAmountValue" :value="financials.subTotal">
                        </div>

                        <div class="flex items-center justify-between text-xs">
                            <span x-text="financials.paymentPlace == 1 ? 'CGST + SGST amount:' : 'IGST Tax amount:'"></span>
                            <span class="font-mono font-semibold text-slate-700 dark:text-slate-300">Rs. <span x-text="financials.tax.toFixed(2)"></span></span>
                            <input type="hidden" name="igst" :value="financials.tax">
                        </div>

                        <!-- Ext charges -->
                        <div class="grid grid-cols-2 gap-2 pt-2 border-t border-slate-200 dark:border-slate-900">
                            <div class="space-y-1">
                                <label for="shipcharge" class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Shipping (Rs.)</label>
                                <input type="number" 
                                       id="shipcharge" 
                                       name="shipcharge" 
                                       x-model.number="financials.shipping" 
                                       @input="calculateTotals()"
                                       step="0.01" 
                                       class="w-full px-3 py-1.5 bg-slate-950 border border-slate-850 rounded-lg text-slate-200 text-sm font-semibold focus:outline-none text-right">
                            </div>
                            <div class="space-y-1">
                                <label for="intcharge" class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Interest / Ext (Rs.)</label>
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
                            <label for="discount" class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Discount (Rs.)</label>
                            <input type="number" 
                                   id="discount" 
                                   name="discount" 
                                   x-model.number="financials.discount" 
                                   @input="calculateTotals()"
                                   step="0.01" 
                                   class="w-full px-3 py-2 bg-slate-950 border border-slate-850 rounded-lg text-slate-200 text-sm font-bold focus:outline-none text-right text-amber-400">
                        </div>

                        <!-- Grand Total -->
                        <div class="flex items-center justify-between pt-4 border-t border-slate-200 dark:border-slate-800 text-base font-bold text-slate-850 dark:text-white">
                            <span>Grand Total:</span>
                            <span class="font-mono text-xl font-bold text-indigo-500 dark:text-indigo-400">Rs. <span x-text="financials.grandTotal.toFixed(2)"></span></span>
                            <input type="hidden" name="grandTotalValue" :value="financials.grandTotal">
                        </div>

                        <!-- Paid -->
                        <div class="space-y-1 pt-3">
                            <label for="paid" class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Paid Amount (Rs.)</label>
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
                        <span>Apply & Update Invoice</span>
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    function salesInvoiceUpdater() {
        return {
            // Data preloaded from PHP
            products: @json($products),
            sale: @json($sale),
            
            // Component states
            client: {
                name: '',
                mobile: '',
                address: '',
                city: '',
                state: '',
                pincode: '',
                gsttin: ''
            },
            items: [],
            financials: {
                paymentPlace: 1, 
                paymentType: 2,  
                paymentStatus: 1, 
                paymentName: 'MTL',
                subTotal: 0,
                tax: 0,
                shipping: 0,
                interest: 0,
                discount: 0,
                grandTotal: 0,
                paid: 0,
                due: 0
            },

            init() {
                // Prepopulate customer details
                this.client = {
                    name: this.sale.client_name || '',
                    mobile: this.sale.mobile || '',
                    address: this.sale.client_contact || '',
                    city: this.sale.user ? (this.sale.user.billingcity || '') : '',
                    state: this.sale.user ? (this.sale.user.billingstate || '') : '',
                    pincode: this.sale.user ? (this.sale.user.billingpincode || '') : '',
                    gsttin: this.sale.gsttin || ''
                };

                // Prepopulate financial details
                this.financials = {
                    paymentPlace: parseInt(this.sale.payment_place || 1),
                    paymentType: parseInt(this.sale.payment_type || 2),
                    paymentStatus: parseInt(this.sale.payment_status || 1),
                    paymentName: this.sale.paymentname || 'MTL',
                    subTotal: parseFloat(this.sale.sub_total || 0),
                    tax: parseFloat(this.sale.gstn || 0),
                    shipping: parseFloat(this.sale.shipamt || 0),
                    interest: parseFloat(this.sale.instamt || 0),
                    discount: parseFloat(this.sale.discount || 0),
                    grandTotal: parseFloat(this.sale.grand_total || 0),
                    paid: parseFloat(this.sale.paid || 0),
                    due: parseFloat(this.sale.due || 0)
                };

                // Prepopulate line items from sale relationship
                if (this.sale.items && this.sale.items.length > 0) {
                    this.items = this.sale.items.map(item => {
                        const product = this.products.find(p => p.id == item.product_id);
                        const baseStock = product ? (product.tqty || 0) : 0;
                        const unitName = product ? (product.unit == 2 ? 'BOX' : (product.unit == 3 ? 'PKT' : 'PCS')) : 'PCS';
                        
                        return {
                            productId: item.product_id,
                            // Adding existing item qty back to available stock display since updating allows re-spending it
                            stock: baseStock + parseInt(item.qty || 0),
                            qty: parseInt(item.qty || 0),
                            rate: parseFloat(item.rate || 0),
                            gst: parseInt(item.gst || 0),
                            unit: unitName,
                            hsnsac: item.hsnsan || '',
                            total: parseFloat(item.total || 0)
                        };
                    });
                } else {
                    this.addLineItem();
                }
            },

            addLineItem() {
                this.items.push({
                    productId: '',
                    stock: 0,
                    qty: 1,
                    rate: 0,
                    gst: 0,
                    unit: 'PCS',
                    hsnsac: '',
                    total: 0
                });
            },

            removeItemLine(index) {
                if (this.items.length > 1) {
                    this.items.splice(index, 1);
                    this.calculateTotals();
                } else {
                    alert('An invoice must contain at least one line item!');
                }
            },

            onProductSelect(index) {
                const item = this.items[index];
                if (!item.productId) {
                    item.stock = 0;
                    item.qty = 1;
                    item.rate = 0;
                    item.gst = 0;
                    item.unit = 'PCS';
                    item.hsnsac = '';
                    item.total = 0;
                    this.calculateTotals();
                    return;
                }

                const p = this.products.find(x => x.id == item.productId);
                if (p) {
                    // Check if this product was already in this order originally to restore its stock in display
                    const originalItem = this.sale.items.find(oi => oi.product_id == item.productId);
                    const offset = originalItem ? parseInt(originalItem.qty || 0) : 0;

                    item.stock = (p.tqty || 0) + offset;
                    item.rate = p.srate || p.mrp || 0;
                    item.gst = p.gst || 0;
                    item.hsnsac = p.hsnsac || '';
                    item.unit = p.unit == 2 ? 'BOX' : (p.unit == 3 ? 'PKT' : 'PCS');
                    item.qty = 1;
                    this.calculateRowTotal(index);
                }
            },

            calculateRowTotal(index) {
                const item = this.items[index];
                item.total = (item.qty || 0) * (item.rate || 0);
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
            }
        };
    }
</script>
@endsection
