@extends('layouts.admin', ['title' => 'Modify Purchase Order'])

@section('content')
<div class="space-y-6" x-data="purchaseInvoiceBuilder()">
    <!-- Header -->
    <div class="flex items-center gap-4">
        <a href="{{ route('admin.purchases.index') }}" class="p-2 text-slate-400 hover:text-white hover:bg-slate-900 rounded-xl transition-all">
            <i class="fa-solid fa-arrow-left text-lg"></i>
        </a>
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight">Modify Purchase Order</h1>
            <p class="text-sm text-slate-400">Edit purchase details for Invoice #{{ $purchase->morder_id }}. Changing item configurations will dynamically recalculate stock levels.</p>
        </div>
    </div>

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

    <form action="{{ route('admin.purchases.update', $purchase->porder_id) }}" method="POST" x-on:submit="handleSubmit($event)" class="space-y-6">
        @csrf
        @method('PUT')

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
                        <div class="space-y-1.5">
                            <label for="sName" class="block text-xs font-bold text-slate-400 uppercase tracking-wider">Supplier Name</label>
                            <input type="text" 
                                   id="sName" 
                                   name="sName" 
                                   x-model="supplier.name" 
                                   required 
                                   placeholder="ENTER SUPPLIER NAME" 
                                   class="w-full px-4 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-slate-200 placeholder-slate-600 focus:outline-none focus:border-indigo-500 text-sm uppercase">
                        </div>

                        <div class="space-y-1.5">
                            <label for="pDate" class="block text-xs font-bold text-slate-400 uppercase tracking-wider">Purchase Date</label>
                            <input type="date" 
                                   id="pDate" 
                                   name="pDate" 
                                   x-model="supplier.date"
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
                                      class="w-full px-4 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-slate-200 placeholder-slate-600 focus:outline-none focus:border-indigo-500 text-sm uppercase"></textarea>
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
                                            <select name="productName[]" 
                                                    x-model="item.productId" 
                                                    @change="onProductSelect(index)"
                                                    required
                                                    class="w-full px-3 py-2 bg-slate-950 border border-slate-850 rounded-lg text-slate-200 text-sm focus:outline-none focus:border-indigo-500">
                                                <option value="">-- Choose Product --</option>
                                                <template x-for="p in products" :key="p.id">
                                                    <option :value="p.id" x-text="p.productname + ' [Stock: ' + (p.tqty || 0) + ']'"></option>
                                                </template>
                                            </select>
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
                                                   class="w-full px-2 py-2 bg-slate-950 border border-slate-850 rounded-lg text-slate-200 text-right text-sm focus:outline-none focus:border-indigo-500 font-medium font-mono">
                                        </td>

                                        <!-- Row Total -->
                                        <td class="py-2 text-right font-mono font-semibold text-slate-200 lg:pr-1 col-span-2 block lg:table-cell lg:col-span-none pt-3 border-t border-slate-800/40 lg:border-t-0 mt-2 lg:mt-0 flex items-center justify-between lg:block">
                                            <span class="block lg:hidden text-xs font-bold text-slate-500 uppercase tracking-wider">Subtotal</span>
                                            <div class="text-sm lg:text-base font-bold text-slate-200 font-mono">
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
                                   class="w-full px-4 py-2.5 bg-slate-950 border border-slate-850 rounded-xl text-slate-200 text-lg font-extrabold focus:outline-none text-right text-emerald-400 font-mono">
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
                        <i class="fa-solid fa-save"></i>
                        <span>Save Changes</span>
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
            
            // Component states prefilled with existing DB values
            supplier: {
                name: @json($purchase->s_name),
                date: @json($purchase->porder_date),
                contact: @json($purchase->s_contact)
            },
            items: [],
            financials: {
                paymentStatus: 1,
                subTotal: parseFloat(@json($purchase->sub_total)) || 0,
                discount: parseFloat(@json($purchase->discount)) || 0,
                grandTotal: parseFloat(@json($purchase->g_total)) || 0,
                paid: parseFloat(@json($purchase->ppaid)) || 0,
                due: parseFloat(@json($purchase->pbal)) || 0
            },

            init() {
                // Prefill items array
                const rawItems = @json($items);
                if (rawItems && rawItems.length > 0) {
                    rawItems.forEach(x => {
                        this.items.push({
                            productId: x.prod_id,
                            punit: x.punit,
                            pqty: parseInt(x.pqty) || 0,
                            qty: parseInt(x.tqty) || 0, // tqty in DB is the carton/pack qty
                            quantity: parseInt(x.qty) || 0, // qty in DB is the piece count qty
                            rate: parseFloat(x.rate) || 0,
                            total: parseFloat(x.tamount) || 0
                        });
                    });
                } else {
                    this.addLineItem();
                }

                // Determine initial payment status dropdown value
                if (this.financials.due <= 0) {
                    this.financials.paymentStatus = 1; // Fully paid
                } else if (this.financials.paid > 0 && this.financials.due > 0) {
                    this.financials.paymentStatus = 2; // Partially paid
                } else {
                    this.financials.paymentStatus = 3; // No paid
                }

                this.calculateTotals();
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
                    item.pqty = parseInt(p.pqty) || 0;
                    item.punit = p.unit == 2 ? 'BOX' : (p.unit == 3 ? 'PKT' : 'PCS');
                    item.rate = parseFloat(p.prate) || parseFloat(p.mrp) || 0;
                    
                    if (item.pqty > 0) {
                        item.qty = 1;
                        item.quantity = item.qty * item.pqty;
                    } else {
                        item.qty = 0;
                        item.quantity = 1;
                    }
                    this.calculateRowTotal(index);
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
                this.calculateRowTotal(index);
            },

            calculateRowTotal(index) {
                const item = this.items[index];
                item.total = (item.quantity || 0) * (item.rate || 0);
                this.calculateTotals();
            },

            calculateTotals() {
                let subTotal = 0;

                this.items.forEach(item => {
                    subTotal += (item.total || 0);
                });

                this.financials.subTotal = subTotal;

                const grandTotal = subTotal - (parseFloat(this.financials.discount) || 0);
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
@endsection
