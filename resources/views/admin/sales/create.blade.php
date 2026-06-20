@extends('layouts.admin', ['title' => 'Record Sales Invoice'])

@section('content')
<script src="https://unpkg.com/html5-qrcode" defer></script>
<div class="space-y-6" x-data="salesInvoiceBuilder()">
    <!-- Header -->
    <x-admin.header 
        description="Record a new customer order with multi-row item checkout and instant billing calculations."
    >
        <x-slot:title>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.sales.index') }}" class="p-2 text-slate-400 hover:text-white hover:bg-slate-900/50 rounded-xl transition-all">
                    <i class="fa-solid fa-arrow-left text-lg"></i>
                </a>
                <span>Create Sales Invoice</span>
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

    <form action="{{ route('admin.sales.store') }}" method="POST" x-on:submit="handleSubmit($event)" class="space-y-6">
        @csrf

        <!-- Invoice Line Items Table -->
            <div class="p-4 sm:p-6 glassmorphism rounded-2xl space-y-4">
                <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-800 pb-3">
                    <h3 class="text-lg font-bold text-slate-800 dark:text-white flex items-center gap-2">
                        <i class="fa-solid fa-file-invoice-dollar text-indigo-600 dark:text-indigo-400 text-base"></i>
                        <span>Invoice Items</span>
                    </h3>
                    <div class="flex items-center gap-2">
                        <button type="button" 
                                @click="catalogDrawerOpen = true"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-indigo-500/10 hover:bg-indigo-500/25 text-indigo-600 dark:text-indigo-400 border border-indigo-500/20 text-xs font-bold rounded-lg transition-all active:scale-95">
                            <i class="fa-solid fa-store"></i>
                            <span class="hidden sm:inline">Browse Catalog</span>
                        </button>
                        <button type="button" 
                                @click="addLineItem()" 
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-indigo-500/10 hover:bg-indigo-500/25 text-indigo-600 dark:text-indigo-400 border border-indigo-500/20 text-xs font-bold rounded-lg transition-all active:scale-95">
                            <i class="fa-solid fa-plus"></i>
                            <span class="hidden sm:inline">Add Item Line</span>
                        </button>
                    </div>
                </div>

                <!-- Barcode Scanner Block -->
                <div class="p-4 bg-slate-50 dark:bg-slate-950/40 border border-slate-200 dark:border-slate-800 rounded-xl space-y-3">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div class="flex items-center gap-2.5">
                            <span class="relative flex h-2.5 w-2.5">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-emerald-500"></span>
                            </span>
                            <span class="text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">Scanner Engine Active</span>
                        </div>
                        <span class="text-[10px] text-slate-500 font-mono">Scan barcode directly or enter product code below</span>
                    </div>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400 dark:text-slate-500">
                            <i class="fa-solid fa-barcode text-base"></i>
                        </div>
                        <input type="text" 
                               x-model="barcodeScanInput"
                               x-on:keydown.prevent.enter="handleBarcodeScan()"
                               placeholder="SCAN BARCODE / TYPE PRODUCT CODE AND ENTER..." 
                               class="w-full pl-11 pr-36 py-3 bg-white dark:bg-slate-950/90 border border-slate-200 dark:border-slate-800 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 rounded-xl text-sm font-semibold tracking-wider text-slate-800 dark:text-slate-200 placeholder-slate-400 dark:placeholder-slate-500 transition-all uppercase">
                        <div class="absolute inset-y-1.5 right-1.5 flex items-center gap-1.5">
                            <button type="button"
                                    @click="startCameraScanner()"
                                    class="h-full px-3.5 bg-purple-500/10 hover:bg-purple-555/25 border border-purple-500/20 hover:border-purple-500/40 text-purple-600 dark:text-purple-400 text-xs font-bold rounded-lg transition-all active:scale-95 flex items-center gap-1">
                                <i class="fa-solid fa-camera text-[10px]"></i>
                                <span>Scan</span>
                            </button>
                            <button type="button"
                                    @click="handleBarcodeScan()"
                                    class="h-full px-3 bg-indigo-500/10 hover:bg-indigo-500/25 border border-indigo-500/20 hover:border-indigo-500/40 text-indigo-600 dark:text-indigo-400 text-xs font-bold rounded-lg transition-all active:scale-95">
                                Add
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
                                <tr class="text-xs font-bold text-slate-500 dark:text-slate-300 uppercase tracking-wider border-b border-slate-200 dark:border-slate-800 pb-3 hidden lg:table-row">
                                    <th class="w-8 py-3">Sl</th>
                                    <th class="py-3 pl-2">Product Name</th>
                                    <th class="w-24 py-3 pl-2">Stock</th>
                                    <th class="w-20 py-3 pl-2">Qty</th>
                                    <th class="w-28 py-3 pl-2">Rate (Rs)</th>
                                    <th class="w-20 py-3 pl-2">GST %</th>
                                    <th class="w-32 py-3 text-right">Total</th>
                                    <th class="w-10 py-3 text-center"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200 dark:divide-slate-800/40 block lg:table-row-group w-full space-y-4 lg:space-y-0 lg:divide-y lg:divide-slate-800/40">
                                <template x-for="(item, index) in items" :key="index">
                                    <tr class="align-middle hover:bg-slate-100/40 dark:hover:bg-slate-900/10 transition-all block lg:table-row w-full bg-slate-50/50 dark:bg-slate-900/30 border border-slate-200 dark:border-slate-800/60 rounded-2xl p-4 lg:p-0 mb-4 lg:mb-0 relative grid grid-cols-2 gap-x-4 gap-y-3 lg:grid-cols-none lg:bg-transparent lg:border-0 lg:hover:bg-slate-100/40 lg:dark:hover:bg-slate-900/10 lg:transition-all">
                                        
                                        <!-- Sl No / Mobile Card Header -->
                                        <td class="col-span-2 bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 px-3 py-1.5 rounded-lg text-xs font-mono font-bold flex items-center justify-between lg:table-cell lg:col-span-none lg:bg-transparent lg:text-slate-500 lg:dark:text-slate-400 lg:px-0 lg:py-3 lg:w-8">
                                            <span class="lg:hidden uppercase tracking-wider text-[10px] font-bold text-indigo-600 dark:text-indigo-400">Line Item #</span>
                                            <span x-text="index + 1" class="text-indigo-650 dark:text-indigo-300 lg:text-slate-500 lg:dark:text-slate-400"></span>
                                            <!-- Mobile delete button (visible only on mobile/tablet) -->
                                            <button type="button" 
                                                    @click="removeItemLine(index)" 
                                                    class="lg:hidden p-1 text-rose-500 hover:bg-rose-500/10 rounded-md transition-all">
                                                <i class="fa-solid fa-trash-can text-xs"></i>
                                            </button>
                                        </td>
                                        
                                        <!-- Product select -->
                                        <td class="py-2 lg:pl-2 col-span-2 block lg:table-cell lg:col-span-none">
                                            <label class="block lg:hidden text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">Product Selection</label>
                                            <select name="productName[]" 
                                                    x-model="item.productId" 
                                                    @change="onProductSelect(index)"
                                                    class="w-full px-3 py-2 bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-lg text-slate-800 dark:text-slate-200 text-sm focus:outline-none focus:border-indigo-500">
                                                <option value="">-- Choose Product --</option>
                                                <template x-for="p in products" :key="p.id">
                                                    <option :value="p.id" x-text="p.productname"></option>
                                                </template>
                                            </select>
                                            <!-- Batch Selection Dropdown -->
                                            <div class="mt-1.5" x-show="item.batches && item.batches.length > 0">
                                                <label class="block text-[9px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-0.5">Select Batch</label>
                                                <select name="batchId[]"
                                                        x-model="item.batchId"
                                                        @change="onBatchSelect(index)"
                                                        class="w-full px-2.5 py-1 bg-white dark:bg-slate-950 border border-indigo-200 dark:border-indigo-950 rounded text-indigo-600 dark:text-indigo-300 text-xs focus:outline-none focus:border-indigo-500">
                                                    <option value="">-- Auto FIFO Batch --</option>
                                                    <template x-for="b in item.batches" :key="b.id">
                                                        <option :value="b.id" x-text="b.batch_number + ' (Stock: ' + b.current_qty + ' | MRP: ' + b.mrp + ')'"></option>
                                                    </template>
                                                </select>
                                            </div>
                                            <input type="hidden" name="hsnsac[]" x-model="item.hsnsac">
                                            <input type="hidden" name="gst[]" x-model="item.gst">
                                            <input type="hidden" name="unit[]" x-model="item.unit">
                                            <input type="hidden" name="slno[]" :value="index + 1">
                                        </td>
 
                                        <!-- Stock display -->
                                        <td class="py-2 lg:pl-2 col-span-1 block lg:table-cell lg:col-span-none">
                                            <label class="block lg:hidden text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">Available Stock</label>
                                            <div class="flex flex-col justify-center h-[38px] lg:h-auto">
                                                <span class="text-xs font-semibold px-2 py-2 rounded-lg bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-center block w-full" 
                                                      :class="item.stock <= 0 ? 'text-rose-600 dark:text-rose-400 bg-rose-500/5 border-rose-500/10' : 'text-slate-700 dark:text-slate-400'"
                                                      x-text="item.stock + ' ' + item.unit"></span>
                                                <span x-show="item.batchId" class="text-[9px] font-mono text-indigo-500 dark:text-indigo-400 mt-1 text-center" x-text="'Batch stock: ' + item.batchStock"></span>
                                            </div>
                                        </td>
 
                                        <!-- Qty -->
                                        <td class="py-2 lg:pl-2 col-span-1 block lg:table-cell lg:col-span-none">
                                            <label class="block lg:hidden text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">Quantity</label>
                                            <input type="number" 
                                                   name="quantity[]" 
                                                   x-model.number="item.qty" 
                                                   @input="calculateRowTotal(index)"
                                                   min="1" 
                                                   class="w-full px-2 py-2 bg-white dark:bg-slate-950 border border-slate-250 dark:border-slate-850 rounded-lg text-slate-800 dark:text-slate-200 text-center text-sm focus:outline-none focus:border-indigo-500">
                                        </td>
 
                                        <!-- Rate -->
                                        <td class="py-2 lg:pl-2 col-span-1 block lg:table-cell lg:col-span-none">
                                            <label class="block lg:hidden text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">Rate (Rs)</label>
                                            <input type="number" 
                                                   name="rateValue[]" 
                                                   x-model.number="item.rate" 
                                                   @input="calculateRowTotal(index)"
                                                   step="0.01" 
                                                   class="w-full px-2 py-2 bg-white dark:bg-slate-950 border border-slate-250 dark:border-slate-850 rounded-lg text-slate-800 dark:text-slate-200 text-right text-sm focus:outline-none focus:border-indigo-500 font-medium">
                                        </td>
 
                                        <!-- GST rate -->
                                        <td class="py-2 lg:pl-2 col-span-1 block lg:table-cell lg:col-span-none">
                                            <label class="block lg:hidden text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">GST Rate</label>
                                            <div class="flex items-center h-[38px] lg:h-auto">
                                                <span class="text-xs font-mono font-bold text-slate-700 dark:text-slate-400 px-2 py-2 rounded-lg bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-center block w-full" x-text="item.gst + '%'"></span>
                                            </div>
                                        </td>
 
                                        <!-- Row Total -->
                                        <td class="py-2 text-right font-mono font-semibold text-slate-800 dark:text-slate-200 lg:pr-1 col-span-2 block lg:table-cell lg:col-span-none pt-3 border-t border-slate-200 dark:border-slate-800/40 lg:border-t-0 mt-2 lg:mt-0 flex items-center justify-between lg:block">
                                            <span class="block lg:hidden text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Subtotal</span>
                                            <div class="text-sm lg:text-base font-bold text-slate-800 dark:text-slate-200">
                                                Rs. <span x-text="item.total.toFixed(2)"></span>
                                            </div>
                                            <input type="hidden" name="totalValue[]" :value="item.total">
                                        </td>
 
                                        <!-- Desktop Remove button -->
                                        <td class="py-2 text-center hidden lg:table-cell lg:w-10">
                                            <button type="button" 
                                                    @click="removeItemLine(index)" 
                                                    class="p-1.5 text-slate-400 hover:text-rose-500 hover:bg-rose-500/5 rounded-lg transition-all">
                                                <i class="fa-solid fa-trash-can text-sm"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>
 
                <!-- Sticky Bottom Bar -->
                <div class="fixed bottom-0 left-0 lg:left-72 right-0 z-40 bg-white/95 dark:bg-slate-900/95 backdrop-blur-md border-t border-slate-200 dark:border-slate-800/85 px-4 py-3 sm:px-6 sm:py-4 flex items-center justify-between gap-3 shadow-2xl">
                    <div class="flex items-center gap-1.5 sm:gap-3">
                        <span class="text-[10px] sm:text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Grand Total:</span>
                        <span class="font-mono text-lg sm:text-2xl font-black text-indigo-650 dark:text-indigo-400 leading-none">Rs. <span x-text="financials.grandTotal.toFixed(2)"></span></span>
                    </div>
                    <button type="button" 
                            @click="pricingDrawerOpen = true"
                            class="px-4 py-2.5 sm:px-6 sm:py-3 bg-orange-600 hover:bg-orange-700 text-white font-bold rounded-xl text-xs sm:text-sm transition-all flex items-center gap-1.5 sm:gap-2 shadow-lg shadow-orange-600/20 active:scale-95 whitespace-nowrap">
                        <i class="fa-solid fa-calculator"></i>
                        <span class="hidden sm:inline">Summary & Checkout</span>
                    </button>
                </div>
                
                <!-- Bottom spacing for sticky bar -->
                <div class="h-24"></div>

                <!-- Pricing Summary Drawer -->
                <div x-show="pricingDrawerOpen" 
                    style="margin-top:0 !important"
                     class="fixed inset-0 z-50 overflow-hidden" 
                     x-cloak>
                    <!-- Backdrop -->
                    <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-md" 
                         @click="pricingDrawerOpen = false"
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0"
                         x-transition:enter-end="opacity-100"
                         x-transition:leave="transition ease-in duration-200"
                         x-transition:leave-start="opacity-100"
                         x-transition:leave-end="opacity-0"></div>

                    <!-- Drawer Panel -->
                    <div class="absolute inset-y-0 right-0 w-full bg-white dark:bg-slate-900 border-l border-slate-200 dark:border-slate-800/80 shadow-2xl flex flex-col"
                          x-transition:enter="transition ease-out duration-300 transform"
                          x-transition:enter-start="translate-x-full"
                          x-transition:enter-end="translate-x-0"
                          x-transition:leave="transition ease-in duration-200 transform"
                          x-transition:leave-start="translate-x-0"
                          x-transition:leave-end="translate-x-full">
                         
                         <!-- Header -->
                         <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-800 flex flex-wrap items-center justify-between gap-4">
                             <div class="flex items-center gap-2.5">
                                 <button type="button" @click="pricingDrawerOpen = false" class="p-1.5 text-slate-400 dark:text-slate-400 shadow-sm w-10 h-10 border border-slate-200 dark:border-slate-800 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 transition-all">
                                     <i class="fa-solid fa-xmark text-lg"></i>
                                 </button>
                                 <i class="fa-solid fa-calculator text-indigo-600 dark:text-indigo-400 text-lg"></i>
                                 <h3 class="text-base font-bold text-slate-800 dark:text-white uppercase tracking-wider">Invoice Summary</h3>
                             </div>
                             <div class="flex items-center gap-3">
                                 <!-- Submit button moved to top (Desktop only) -->
                                 <button type="submit" 
                                         class="hidden md:flex px-5 py-2 bg-orange-600 hover:bg-orange-700 text-white font-bold rounded-xl text-sm transition-all items-center gap-2 shadow-lg shadow-orange-600/20 active:scale-95">
                                     <i class="fa-solid fa-file-circle-check"></i>
                                     <span>Complete & Issue Bill</span>
                                 </button>
                                 
                             </div>
                         </div>
 
                         <!-- Split Content: Customer Profile & Return Helper (Left) and Billing Form (Right) -->
                         <div class="flex-1 overflow-y-auto md:overflow-hidden flex flex-col md:flex-row">
                             
                             <!-- Left Panel: Customer Profile & Cash Return Calculation -->
                             <div class="w-full md:w-1/2 border-b md:border-b-0 md:border-r border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-6 flex flex-col justify-between md:overflow-y-auto space-y-6">
                                 <div class="space-y-4">
                                     <div class="flex items-center gap-2 border-b border-slate-100 dark:border-slate-800 pb-2.5">
                                         <i class="fa-solid fa-user-tag text-indigo-500 text-sm"></i>
                                         <h4 class="text-xs font-bold text-slate-800 dark:text-slate-300 uppercase tracking-wider">Customer Profile</h4>
                                     </div>
                                    
                                    <!-- Customer Selection & Form Fields -->
                                    <div class="space-y-4">
                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                            <div class="space-y-1.5">
                                                <label for="customername" class="block text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Select Client Account</label>
                                                <select id="customername" 
                                                        name="customername" 
                                                        x-model="customerSelection" 
                                                        @change="onCustomerChange()"
                                                        class="w-full px-3 py-2 bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-slate-800 dark:text-slate-200 focus:outline-none focus:border-indigo-500 text-sm">
                                                    <option value="0">-- Walk-in / New Customer --</option>
                                                    @foreach ($customers as $c)
                                                        <option value="{{ $c->id }}">{{ $c->uname }} ({{ $c->contactno }})</option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div class="space-y-1.5">
                                                <label for="orderDate" class="block text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Order Date</label>
                                                <input type="date" 
                                                       id="orderDate" 
                                                       name="orderDate" 
                                                       value="{{ date('Y-m-d') }}" 
                                                       required 
                                                       class="w-full px-3 py-2 bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-slate-800 dark:text-slate-200 focus:outline-none focus:border-indigo-500 text-sm">
                                            </div>
                                        </div>

                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                            <div class="space-y-1.5">
                                                <label for="clientName" class="block text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Customer Name</label>
                                                <input type="text" 
                                                       id="clientName" 
                                                       name="clientName" 
                                                       x-model="client.name" 
                                                       required 
                                                       placeholder="Full customer name" 
                                                       class="w-full px-3 py-2 bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-slate-800 dark:text-slate-200 placeholder-slate-400 dark:placeholder-slate-600 focus:outline-none focus:border-indigo-500 text-sm uppercase">
                                            </div>

                                            <div class="space-y-1.5">
                                                <label for="mobileno" class="block text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Mobile Number</label>
                                                <input type="text" 
                                                       id="mobileno" 
                                                       name="mobileno" 
                                                       x-model="client.mobile" 
                                                       required 
                                                       placeholder="10-digit mobile" 
                                                       class="w-full px-3 py-2 bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-slate-800 dark:text-slate-200 placeholder-slate-400 dark:placeholder-slate-600 focus:outline-none focus:border-indigo-500 text-sm">
                                            </div>

                                            <div class="sm:col-span-2 space-y-1.5">
                                                <label for="clientContact" class="block text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Billing / Shipping Address</label>
                                                <textarea id="clientContact" 
                                                          name="clientContact" 
                                                          x-model="client.address" 
                                                          required 
                                                          rows="2"
                                                          placeholder="Detailed street and locality address..." 
                                                          class="w-full px-3 py-2 bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-slate-800 dark:text-slate-200 placeholder-slate-400 dark:placeholder-slate-600 focus:outline-none focus:border-indigo-500 text-sm uppercase"></textarea>
                                            </div>

                                            <div class="space-y-1.5">
                                                <label for="city" class="block text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">City</label>
                                                <input type="text" 
                                                       id="city" 
                                                       name="city" 
                                                       x-model="client.city" 
                                                       required 
                                                       placeholder="City" 
                                                       class="w-full px-3 py-2 bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-slate-800 dark:text-slate-200 placeholder-slate-400 dark:placeholder-slate-600 focus:outline-none focus:border-indigo-500 text-sm uppercase">
                                            </div>

                                            <div class="space-y-1.5">
                                                <label for="state" class="block text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">State</label>
                                                <select id="state" 
                                                        name="state" 
                                                        x-model="client.state" 
                                                        required 
                                                        class="w-full px-3 py-2 bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-slate-800 dark:text-slate-200 focus:outline-none focus:border-indigo-500 text-sm">
                                                    <option value="">-- Choose State --</option>
                                                    @foreach ($states as $s)
                                                        <option value="{{ $s->sname }}">{{ $s->sname }}</option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div class="space-y-1.5">
                                                <label for="pincode" class="block text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Pincode</label>
                                                <input type="text" 
                                                       id="pincode" 
                                                       name="pincode" 
                                                       x-model="client.pincode" 
                                                       required 
                                                       placeholder="6-digit pincode" 
                                                       class="w-full px-3 py-2 bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-slate-800 dark:text-slate-200 placeholder-slate-400 dark:placeholder-slate-600 focus:outline-none focus:border-indigo-500 text-sm">
                                            </div>

                                            <div class="space-y-1.5">
                                                <label for="gsttin" class="block text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">GSTIN Number (Optional)</label>
                                                <input type="text" 
                                                       id="gsttin" 
                                                       name="gsttin" 
                                                       x-model="client.gsttin" 
                                                       placeholder="GSTIN if applicable" 
                                                       class="w-full px-3 py-2 bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-slate-800 dark:text-slate-200 placeholder-slate-400 dark:placeholder-slate-600 focus:outline-none focus:border-indigo-500 text-sm uppercase">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Change Return Helper Section -->
                                <div class="pt-6 border-t border-slate-200 dark:border-slate-800/80 space-y-4">
                                    <div class="flex items-center gap-2">
                                        <i class="fa-solid fa-hand-holding-dollar text-emerald-500"></i>
                                        <h4 class="text-xs font-bold text-slate-800 dark:text-slate-300 uppercase tracking-wider">Return Calculator (Helper Only)</h4>
                                    </div>
                                    
                                    <div class="grid grid-cols-2 gap-4">
                                        <div class="bg-slate-100 dark:bg-slate-900 border border-slate-200 dark:border-slate-800/80 rounded-xl p-3 flex flex-col justify-center">
                                            <span class="text-[9px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Total Bill</span>
                                            <span class="font-mono text-base font-bold text-slate-800 dark:text-white mt-0.5">
                                                Rs. <span x-text="financials.grandTotal.toFixed(2)"></span>
                                            </span>
                                        </div>

                                        <div class="space-y-1">
                                            <label class="block text-[9px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Customer Cash Given</label>
                                            <input type="number" 
                                                   x-model.number="returnCalcCustomerCash" 
                                                   placeholder="Cash given"
                                                   step="0.01"
                                                   class="w-full px-3 py-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-slate-800 dark:text-slate-200 text-sm font-bold focus:outline-none text-right">
                                        </div>
                                    </div>

                                    <div class="flex items-center justify-between bg-emerald-500/5 dark:bg-emerald-500/10 border border-emerald-500/10 dark:border-emerald-550/20 rounded-xl p-3">
                                        <span class="text-xs font-bold text-slate-600 dark:text-slate-400">Return to Customer:</span>
                                        <span class="font-mono text-lg font-black text-emerald-600 dark:text-emerald-450"
                                              x-text="'Rs. ' + Math.max(0, (returnCalcCustomerCash || 0) - financials.grandTotal).toFixed(2)">
                                        </span>
                                    </div>
                                </div>
                            </div>

                             <!-- Right Panel: Billing Config Form -->
                             <div class="w-full md:w-1/2 md:overflow-y-auto p-6 space-y-6 custom-scrollbar text-slate-700 dark:text-slate-300 bg-slate-50 dark:bg-slate-950/40">
                                 
                                 <!-- Card 1: Payment settings -->
                                 <div class="p-5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-sm space-y-4">
                                     <h4 class="text-xs font-bold text-slate-800 dark:text-slate-200 uppercase tracking-wider flex items-center gap-1.5 border-b border-slate-100 dark:border-slate-800 pb-2.5">
                                         <i class="fa-solid fa-credit-card text-indigo-500 text-sm"></i>
                                         <span>Payment Settings</span>
                                     </h4>
                                     
                                     <!-- Inputs & Settings -->
                                     <div class="space-y-4 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                                         <div class="space-y-1.5">
                                             <label for="paymentPlace" class="block">GST Tax Mode</label>
                                             <select id="paymentPlace" 
                                                     name="paymentPlace" 
                                                     x-model.number="financials.paymentPlace" 
                                                     @change="calculateTotals()"
                                                     class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-slate-800 dark:text-slate-200 focus:outline-none focus:border-indigo-500 font-semibold normal-case">
                                                 <option value="1">Intra-State (CGST + SGST)</option>
                                                 <option value="2">Inter-State (IGST)</option>
                                             </select>
                                         </div>

                                         <div class="space-y-1.5">
                                             <label for="paymentType" class="block">Payment Mode</label>
                                             <select id="paymentType" 
                                                     name="paymentType" 
                                                     x-model.number="financials.paymentType"
                                                     class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-slate-800 dark:text-slate-200 focus:outline-none focus:border-indigo-500 font-semibold normal-case">
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
                                                    value="MTL" 
                                                    required 
                                                    class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-slate-800 dark:text-slate-200 focus:outline-none focus:border-indigo-500 text-sm normal-case">
                                         </div>

                                         <div class="space-y-1.5">
                                             <label for="paymentStatus" class="block">Payment Status</label>
                                             <select id="paymentStatus" 
                                                     name="paymentStatus" 
                                                     x-model.number="financials.paymentStatus" 
                                                     @change="onPaymentStatusChange()"
                                                     class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-slate-800 dark:text-slate-200 focus:outline-none focus:border-indigo-500 font-semibold normal-case">
                                                 <option value="1">Fully Paid</option>
                                                 <option value="2">Partially Paid</option>
                                                 <option value="3">No Paid / Credit</option>
                                             </select>
                                         </div>
                                     </div>
                                 </div>

                                 <!-- Card 2: Billing Ledger calculations -->
                                 <div class="p-5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-sm space-y-4">
                                     <h4 class="text-xs font-bold text-slate-800 dark:text-slate-200 uppercase tracking-wider flex items-center gap-1.5 border-b border-slate-100 dark:border-slate-800 pb-2.5">
                                         <i class="fa-solid fa-receipt text-indigo-500 text-sm"></i>
                                         <span>Ledger Calculations</span>
                                     </h4>
                                     
                                     <!-- Ledgers Breakdown -->
                                     <div class="space-y-2.5 text-sm text-slate-600 dark:text-slate-400">
                                         <div class="flex items-center justify-between">
                                             <span class="font-medium text-slate-500">Subtotal Amount:</span>
                                             <span class="font-mono font-bold text-slate-800 dark:text-slate-200">Rs. <span x-text="financials.subTotal.toFixed(2)"></span></span>
                                             <input type="hidden" name="subTotalValue" :value="financials.subTotal">
                                             <input type="hidden" name="totalAmountValue" :value="financials.subTotal">
                                         </div>

                                         <div class="flex items-center justify-between text-xs">
                                             <span class="font-medium text-slate-550" x-text="financials.paymentPlace == 1 ? 'CGST + SGST Amount:' : 'IGST Tax Amount:'"></span>
                                             <span class="font-mono font-semibold text-slate-700 dark:text-slate-300">Rs. <span x-text="financials.tax.toFixed(2)"></span></span>
                                             <input type="hidden" name="igst" :value="financials.tax">
                                         </div>

                                         <!-- Ext charges -->
                                         <div class="grid grid-cols-2 gap-3 pt-2.5 border-t border-slate-100 dark:border-slate-800">
                                             <div class="space-y-1">
                                                 <label for="shipcharge" class="block text-[10px] font-bold uppercase tracking-wider text-slate-550 dark:text-slate-450">Shipping (Rs.)</label>
                                                 <input type="number" 
                                                        id="shipcharge" 
                                                        name="shipcharge" 
                                                        x-model.number="financials.shipping" 
                                                        @input="calculateTotals()"
                                                        step="0.01" 
                                                        class="w-full px-3 py-1.5 bg-slate-55 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-lg text-slate-800 dark:text-slate-200 text-sm font-semibold focus:outline-none text-right">
                                             </div>
                                             <div class="space-y-1">
                                                 <label for="intcharge" class="block text-[10px] font-bold uppercase tracking-wider text-slate-550 dark:text-slate-450">Interest / Ext (Rs.)</label>
                                                 <input type="number" 
                                                        id="intcharge" 
                                                        name="intcharge" 
                                                        x-model.number="financials.interest" 
                                                        @input="calculateTotals()"
                                                        step="0.01" 
                                                        class="w-full px-3 py-1.5 bg-slate-55 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-lg text-slate-800 dark:text-slate-200 text-sm font-semibold focus:outline-none text-right">
                                             </div>
                                         </div>

                                         <div class="space-y-1 pt-1.5">
                                             <label for="discount" class="block text-[10px] font-bold uppercase tracking-wider text-slate-550 dark:text-slate-450">Discount (Rs.)</label>
                                             <input type="number" 
                                                    id="discount" 
                                                    name="discount" 
                                                    x-model.number="financials.discount" 
                                                    @input="calculateTotals()"
                                                    step="0.01" 
                                                    class="w-full px-3 py-2 bg-slate-55 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-lg text-slate-800 dark:text-slate-200 text-sm font-bold focus:outline-none text-right text-amber-600 dark:text-amber-450">
                                         </div>
                                     </div>
                                 </div>

                                 <!-- Card 3: Financial Totals (The Main Focus Card) -->
                                 <div class="p-5 bg-indigo-50/50 dark:bg-indigo-950/10 border border-indigo-100 dark:border-indigo-900/30 rounded-2xl shadow-sm space-y-4">
                                     <!-- Grand Total -->
                                     <div class="flex items-center justify-between text-base font-bold text-slate-800 dark:text-white">
                                         <span class="text-slate-700 dark:text-slate-350">Grand Total:</span>
                                         <span class="font-mono text-2xl font-black text-indigo-650 dark:text-indigo-400">Rs. <span x-text="financials.grandTotal.toFixed(2)"></span></span>
                                         <input type="hidden" name="grandTotalValue" :value="financials.grandTotal">
                                     </div>

                                     <!-- Paid Input -->
                                     <div class="space-y-1.5">
                                         <label for="paid" class="block text-[10px] font-bold uppercase tracking-wider text-indigo-700 dark:text-indigo-400">Paid Amount (Rs.)</label>
                                         <input type="number" 
                                                id="paid" 
                                                name="paid" 
                                                x-model.number="financials.paid" 
                                                @input="calculateDue()"
                                                required 
                                                step="0.01" 
                                                class="w-full px-4 py-2.5 bg-white dark:bg-slate-950 border border-indigo-200 dark:border-indigo-900 rounded-xl text-slate-800 dark:text-slate-200 text-xl font-extrabold focus:outline-none text-right text-emerald-600 dark:text-emerald-450 focus:ring-1 focus:ring-indigo-550">
                                     </div>

                                     <!-- Due Display -->
                                     <div class="flex items-center justify-between pt-3 border-t border-indigo-100 dark:border-indigo-900/30 text-sm font-semibold">
                                         <span class="text-slate-700 dark:text-slate-350">Remaining Due Balance:</span>
                                         <span class="font-mono text-base font-bold" :class="financials.due > 0 ? 'text-rose-600 dark:text-rose-450' : 'text-slate-500'">Rs. <span x-text="financials.due.toFixed(2)"></span></span>
                                         <input type="hidden" name="dueValue" :value="financials.due">
                                     </div>
                                 </div>

                                 <!-- Loyalty Coins Card (Subtle section) -->
                                 <div class="p-4 bg-amber-500/5 dark:bg-slate-900/60 border border-amber-500/10 dark:border-slate-800 rounded-xl space-y-2 text-xs text-slate-600 dark:text-slate-400">
                                     <span class="font-bold text-amber-800 dark:text-slate-350 block uppercase tracking-wide">Loyalty Coins Ledger</span>
                                     <div class="flex justify-between">
                                         <span>Available Coins:</span>
                                         <span class="font-bold text-indigo-600 dark:text-indigo-400" x-text="coins.tmcoin"></span>
                                     </div>
                                     <div class="flex justify-between">
                                         <span>Redeeming Coins (Points):</span>
                                         <input type="number" 
                                                name="mcoinp" 
                                                x-model.number="coins.mcoinp" 
                                                @input="onCoinRedemptionChange()"
                                                class="w-20 px-2 py-0.5 bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded text-right text-slate-800 dark:text-slate-300 focus:outline-none font-bold">
                                     </div>
                                     <div class="flex justify-between">
                                         <span>Coins Earned:</span>
                                         <span class="font-bold text-emerald-600 dark:text-emerald-400" x-text="coins.mcoin"></span>
                                     </div>
                                     <div class="flex justify-between border-t border-slate-200 dark:border-slate-950 pt-1.5 font-semibold">
                                         <span>Net Balance:</span>
                                         <span class="font-bold text-slate-800 dark:text-slate-200" x-text="coins.bmcoin"></span>
                                         <!-- Hidden fields for coins -->
                                         <input type="hidden" name="mcoin" :value="coins.mcoin">
                                         <input type="hidden" name="bmcoin" :value="coins.bmcoin">
                                         <input type="hidden" name="tmcoin" :value="coins.tmcoin">
                                     </div>
                                 </div>

                                <!-- Mobile-only checkout submit button -->
                                <div class="pt-4 md:hidden pb-6">
                                    <button type="submit" 
                                            class="w-full py-4 bg-orange-600 hover:bg-orange-700 text-white font-bold rounded-xl text-base shadow-xl shadow-orange-600/10 active:scale-[0.98] transition-all flex items-center justify-center gap-2">
                                        <i class="fa-solid fa-file-circle-check"></i>
                                        <span>Complete & Issue Bill</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
    </form>

    <!-- Camera Scanner Modal -->
    <div x-show="cameraScannerOpen" 
         class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-205"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         x-cloak>
        <div class="bg-slate-900 border border-slate-800 rounded-3xl w-full max-w-md overflow-hidden shadow-2xl relative">
            <div class="px-6 py-4 border-b border-slate-800 flex items-center justify-between">
                <h3 class="text-md font-bold text-white flex items-center gap-2">
                    <i class="fa-solid fa-barcode text-purple-400"></i>
                    <span>Scan Product Barcode</span>
                </h3>
                <button type="button" @click="stopCameraScanner()" class="p-1.5 text-slate-400 hover:text-white rounded-lg hover:bg-slate-800 transition-all">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>
            <div class="p-6 space-y-4">
                <div id="camera-reader" class="overflow-hidden rounded-2xl border border-slate-800 bg-slate-950 relative" style="min-height: 250px;">
                    <!-- A loading spinner or instruction placeholder -->
                    <div id="camera-placeholder" class="absolute inset-0 flex flex-col items-center justify-center text-slate-500 gap-2 font-semibold text-sm">
                        <i class="fa-solid fa-spinner fa-spin text-2xl text-purple-400"></i>
                        <span>Initializing Camera Engine...</span>
                    </div>
                </div>
                <!-- Scanner Success / Error Notification inside the popup -->
                <div x-show="scanNotification" 
                     x-transition:enter="transition ease-out duration-300"
                     class="px-3.5 py-2.5 bg-emerald-500/10 border border-emerald-500/20 rounded-xl flex items-center gap-2 text-xs text-emerald-400"
                     x-cloak>
                    <i class="fa-solid fa-circle-check"></i>
                    <span x-text="scanNotification"></span>
                </div>
                <div x-show="scanErrorNotification" 
                     x-transition:enter="transition ease-out duration-300"
                     class="px-3.5 py-2.5 bg-rose-500/10 border border-rose-500/20 rounded-xl flex items-center gap-2 text-xs text-rose-405"
                     x-cloak>
                    <i class="fa-solid fa-circle-xmark"></i>
                    <span x-text="scanErrorNotification"></span>
                </div>

                <div class="text-center text-xs text-slate-500">
                    Align the barcode inside the camera view area to scan automatically.
                </div>
            </div>
        </div>
    </div>

    <!-- Catalog Drawer Overlay -->
    <div x-show="catalogDrawerOpen" 
         class="fixed inset-0 z-50 overflow-hidden mt-0" 
         style="margin-top:0 !important"
         x-cloak>
        <!-- Backdrop -->
        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-md" 
             @click="catalogDrawerOpen = false"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"></div>

        <!-- Drawer Panel (Full Screen) -->
        <div class="absolute inset-y-0 right-0 top-0 w-full bg-white dark:bg-slate-900 border-l border-slate-200 dark:border-slate-800/80 shadow-2xl flex flex-col h-full"
             x-transition:enter="transition ease-out duration-300 transform"
             x-transition:enter-start="translate-x-full"
             x-transition:enter-end="translate-x-0"
             x-transition:leave="transition ease-in duration-200 transform"
             x-transition:leave-start="translate-x-0"
             x-transition:leave-end="translate-x-full">
            
            <!-- Header -->
            <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-800/60 flex items-center justify-between flex-shrink-0">
                <div class="flex items-center gap-2.5">
                    <i class="fa-solid fa-store text-indigo-600 dark:text-indigo-400 text-lg"></i>
                    <h3 class="text-base font-bold text-slate-800 dark:text-white uppercase tracking-wider">Product Catalog</h3>
                </div>
                <button type="button" @click="catalogDrawerOpen = false" class="px-4 py-2 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 text-xs font-bold rounded-xl transition-all flex items-center gap-1.5 active:scale-95 border border-slate-200 dark:border-slate-700/60">
                    <i class="fa-solid fa-check mr-0.5"></i>
                    <span>Done / Apply to Bill</span>
                </button>
            </div>

            <!-- Split Layout Body -->
            <div class="flex-1 grid grid-cols-1 lg:grid-cols-3 overflow-hidden">
                <!-- Left Column: Selected Items currently in Invoice -->
                <div class="lg:col-span-1 border-r border-slate-200 dark:border-slate-800 flex flex-col h-full bg-slate-50 dark:bg-slate-950/20 overflow-hidden">
                    <div class="p-4 border-b border-slate-250 dark:border-slate-800/80 flex items-center justify-between flex-shrink-0 bg-slate-100/40 dark:bg-slate-950/30">
                        <h4 class="text-xs font-black uppercase tracking-wider text-slate-500 dark:text-slate-400 flex items-center gap-2">
                            <i class="fa-solid fa-list-check text-emerald-500"></i>
                            <span>Selected Items in Invoice</span>
                        </h4>
                        <span class="px-2.5 py-0.5 rounded-full bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 text-[10px] font-bold" x-text="items.filter(i => i.productId).length + ' Items'"></span>
                    </div>

                    <!-- Selected items list scroll -->
                    <div class="flex-1 overflow-y-auto p-4 space-y-3 scrollbar-thin">
                        <template x-for="(item, index) in items" :key="index">
                            <template x-if="item.productId">
                                <div class="p-3 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl flex items-center justify-between gap-3 shadow-sm hover:border-slate-300 dark:hover:border-slate-700 transition-all">
                                    <div class="min-w-0 flex-1">
                                        <span class="block font-bold text-slate-850 dark:text-slate-200 text-xs truncate" x-text="products.find(x => x.id == item.productId)?.productname || 'Unknown Product'"></span>
                                        <div class="flex items-center gap-1.5 mt-0.5">
                                            <span class="text-[10px] text-slate-500 font-semibold" x-text="'Rate: Rs. ' + (parseFloat(item.rate) || 0).toFixed(2)"></span>
                                            <span class="text-[9px] text-slate-405">&bull;</span>
                                            <span class="text-[10px] text-indigo-600 dark:text-indigo-400 font-mono font-bold" x-text="'Total: Rs. ' + (parseFloat(item.total) || 0).toFixed(2)"></span>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-1.5 flex-shrink-0">
                                        <button type="button" 
                                                @click="if (item.qty > 1) { item.qty--; calculateRowTotal(index); } else { removeItemLine(index); }" 
                                                class="w-6.5 h-6.5 rounded-lg bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 flex items-center justify-center text-xs font-bold transition-all active:scale-90">-</button>
                                        <span class="text-xs font-mono font-black w-6 text-center text-slate-800 dark:text-slate-100" x-text="item.qty"></span>
                                        <button type="button" 
                                                @click="item.qty++; calculateRowTotal(index);" 
                                                class="w-6.5 h-6.5 rounded-lg bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 flex items-center justify-center text-xs font-bold transition-all active:scale-90">+</button>
                                    </div>
                                </div>
                            </template>
                        </template>

                        <div x-show="items.filter(i => i.productId).length === 0" class="text-center py-16 text-slate-400 dark:text-slate-500 text-xs font-medium space-y-2">
                            <i class="fa-solid fa-cart-shopping text-2xl text-slate-300 dark:text-slate-700 block mx-auto"></i>
                            <span>No items added to invoice yet.</span>
                        </div>
                    </div>

                    <!-- Split Bottom Mini Summary -->
                    <div class="p-4 border-t border-slate-250 dark:border-slate-800/80 bg-slate-100/60 dark:bg-slate-950/40 flex-shrink-0 flex items-center justify-between">
                        <span class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wide">Invoice Subtotal:</span>
                        <span class="font-mono text-sm font-black text-indigo-650 dark:text-indigo-400">Rs. <span x-text="financials.subTotal.toFixed(2)"></span></span>
                    </div>
                </div>

                <!-- Right Column: Catalog Grid & Search -->
                <div class="lg:col-span-2 flex flex-col h-full overflow-hidden">
                    <!-- Search Input -->
                    <div class="p-4 bg-slate-50 dark:bg-slate-950/40 border-b border-slate-200 dark:border-slate-800/60 space-y-3 flex-shrink-0">
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400 dark:text-slate-500">
                                <i class="fa-solid fa-magnifying-glass text-xs"></i>
                            </div>
                            <input type="text" 
                                   x-model="catalogSearch"
                                   placeholder="Search catalog by name or code..." 
                                   class="w-full pl-10 pr-4 py-2.5 bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-800 dark:text-slate-200 placeholder-slate-450 dark:placeholder-slate-500 focus:outline-none focus:border-indigo-500">
                        </div>
                    </div>

                    <!-- Catalog scroll grid -->
                    <div class="flex-1 overflow-y-auto p-6 space-y-3.5 custom-scrollbar">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <template x-for="p in filteredCatalogProducts()" :key="p.id">
                                <div class="p-3.5 rounded-2xl border transition-all flex items-center justify-between gap-4"
                                     :class="getProductInvoiceQty(p.id) > 0 
                                        ? 'border-emerald-500/30 dark:border-emerald-500/35 bg-emerald-500/5 dark:bg-emerald-500/5' 
                                        : 'border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-950/40 hover:bg-slate-100 dark:hover:bg-slate-950/80'">
                                    <div class="space-y-1 min-w-0 flex-1">
                                        <div class="flex flex-wrap items-center gap-1.5">
                                            <span class="block font-bold text-slate-800 dark:text-slate-200 text-xs uppercase truncate" x-text="p.productname"></span>
                                            <template x-if="getProductInvoiceQty(p.id) > 0">
                                                <span class="inline-flex items-center px-1.5 py-0.5 text-[9px] font-bold bg-emerald-500/10 text-emerald-600 dark:text-emerald-450 border border-emerald-500/20 rounded">
                                                    <i class="fa-solid fa-circle-check mr-1"></i> Added (<span x-text="getProductInvoiceQty(p.id)"></span>)
                                                </span>
                                            </template>
                                        </div>
                                        <div class="flex items-center gap-2 font-mono text-[9px] text-slate-500">
                                            <span x-text="'CODE: ' + (p.pcode || 'N/A')"></span>
                                            <span>&bull;</span>
                                            <span class="px-1.5 py-0.5 rounded bg-indigo-500/10 text-indigo-650 dark:text-indigo-400 border border-indigo-500/10" x-text="p.tqty + ' ' + (p.unit == 2 ? 'BOX' : (p.unit == 3 ? 'PKT' : 'PCS'))"></span>
                                        </div>
                                        <div class="text-[10px] text-indigo-650 dark:text-indigo-400 font-bold mt-1">
                                            Rs. <span x-text="getProductTierPrice(p).toFixed(2)"></span>
                                            <span class="text-[9px] text-slate-500 font-normal ml-1" x-text="'(MRP: ' + p.mrp + ')'"></span>
                                        </div>
                                    </div>
                                    <button type="button" 
                                            @click="addProductFromCatalog(p)"
                                            class="flex-shrink-0 w-8 h-8 rounded-xl transition-all flex items-center justify-center active:scale-90"
                                            :class="getProductInvoiceQty(p.id) > 0
                                                ? 'bg-emerald-500/10 hover:bg-emerald-600 text-emerald-600 dark:text-emerald-450 hover:text-white border border-emerald-500/20'
                                                : 'bg-indigo-500/10 hover:bg-indigo-600 dark:bg-indigo-500/15 text-indigo-600 dark:text-indigo-400 hover:text-white border border-indigo-500/20'"
                                            :title="getProductInvoiceQty(p.id) > 0 ? 'Increase quantity in Invoice' : 'Add to Invoice'">
                                        <i class="fa-solid text-xs" :class="getProductInvoiceQty(p.id) > 0 ? 'fa-check' : 'fa-plus'"></i>
                                    </button>
                                </div>
                            </template>
                        </div>

                        <div x-show="filteredCatalogProducts().length === 0" class="text-center py-12 text-slate-500 text-xs font-semibold">
                            No products found matching your search.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function salesInvoiceBuilder() {
        return {
            // Data preloaded from PHP
            products: @json($products),
            customers: @json($customers),
            
            // Component states
            customerSelection: '0',
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
                paymentPlace: 1, // Intra state
                paymentType: 2,  // Cash
                paymentStatus: 1, // Fully paid
                subTotal: 0,
                tax: 0,
                shipping: 0,
                interest: 0,
                discount: 0,
                grandTotal: 0,
                paid: 0,
                due: 0
            },
            coins: {
                tmcoin: 0,
                mcoinp: 0,
                mcoin: 0,
                bmcoin: 0
            },

            // Barcode scanner states
            barcodeScanInput: '',
            scanNotification: '',
            scanErrorNotification: '',

            // Camera scanner & Catalog states
            html5Qrcode: null,
            cameraScannerOpen: false,
            catalogDrawerOpen: false,
            catalogSearch: '',
            showCustomerDetails: false,
            pricingDrawerOpen: false,
            returnCalcCustomerCash: 0,

            init() {
                // Initialize with one blank line item
                this.addLineItem();
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
                    total: 0,
                    batches: [],
                    batchId: '',
                    batchStock: 0
                });
            },

            getProductInvoiceQty(productId) {
                let qty = 0;
                this.items.forEach(item => {
                    if (item.productId && Number(item.productId) === Number(productId)) {
                        qty += Number(item.qty || 0);
                    }
                });
                return qty;
            },

            removeItemLine(index) {
                if (this.items.length > 1) {
                    this.items.splice(index, 1);
                    this.calculateTotals();
                } else {
                    alert('An invoice must contain at least one line item!');
                }
            },

            async onProductSelect(index) {
                const item = this.items[index];
                if (!item.productId) {
                    item.stock = 0;
                    item.qty = 1;
                    item.rate = 0;
                    item.gst = 0;
                    item.unit = 'PCS';
                    item.hsnsac = '';
                    item.total = 0;
                    item.batches = [];
                    item.batchId = '';
                    item.batchStock = 0;
                    this.calculateTotals();
                    return;
                }

                try {
                    let res = await fetch(`/admin/products/price-search/${item.productId}`);
                    if (res.ok) {
                        let p = await res.json();
                        item.stock = p.tqty || 0;
                        item.gst = p.gst || 0;
                        item.hsnsac = p.hsnsac || '';
                        item.unit = p.unit == 2 ? 'BOX' : (p.unit == 3 ? 'PKT' : 'PCS');
                        item.qty = 1;
                        item.batches = p.batches || [];
                        item.batchId = '';
                        item.batchStock = 0;
                        
                        // Default price according to customer tier
                        this.updateItemPricingAccordingToTier(index, p);
                        this.calculateRowTotal(index);
                    }
                } catch (err) {
                    console.error(err);
                }

                // Automatic Row Creation
                if (index === this.items.length - 1) {
                    this.addLineItem();
                }
            },

            onBatchSelect(index) {
                const item = this.items[index];
                if (!item.batchId) {
                    // Fall back to product default prices
                    const p = this.products.find(x => x.id == item.productId);
                    if (p) {
                        this.updateItemPricingAccordingToTier(index, p);
                    }
                    item.batchStock = 0;
                    this.calculateRowTotal(index);
                    return;
                }

                const batch = item.batches.find(b => b.id == item.batchId);
                if (batch) {
                    item.batchStock = batch.current_qty || 0;
                    
                    // Determine price from batch based on customer tier
                    const tier = this.getCustomerTier();
                    if (tier === 'S' && batch.sdprice !== null) {
                        item.rate = parseFloat(batch.sdprice);
                    } else if (tier === 'D' && batch.dprice !== null) {
                        item.rate = parseFloat(batch.dprice);
                    } else if (tier === 'C' && batch.cprice !== null) {
                        item.rate = parseFloat(batch.cprice);
                    } else {
                        item.rate = parseFloat(batch.srate || batch.mrp || 0);
                    }
                    this.calculateRowTotal(index);
                }
            },

            getCustomerTier() {
                if (this.customerSelection == '0' || this.customerSelection == 'new') {
                    return 'C'; // default customer for walk-in
                }
                const c = this.customers.find(x => x.id == this.customerSelection);
                return c ? (c.usertype || 'C') : 'C';
            },

            updateItemPricingAccordingToTier(index, productOrBatch) {
                const item = this.items[index];
                const tier = this.getCustomerTier();
                if (tier === 'S' && productOrBatch.sdprice !== null) {
                    item.rate = parseFloat(productOrBatch.sdprice);
                } else if (tier === 'D' && productOrBatch.dprice !== null) {
                    item.rate = parseFloat(productOrBatch.dprice);
                } else if (tier === 'C' && productOrBatch.cprice !== null) {
                    item.rate = parseFloat(productOrBatch.cprice);
                } else {
                    item.rate = parseFloat(productOrBatch.srate || productOrBatch.mrp || 0);
                }
            },

            onCustomerChange() {
                if (this.customerSelection == '0' || this.customerSelection == 'new') {
                    this.client = { name: '', mobile: '', address: '', city: '', state: '', pincode: '', gsttin: '' };
                    this.coins = { tmcoin: 0, mcoinp: 0, mcoin: 0, bmcoin: 0 };
                } else {
                    const c = this.customers.find(x => x.id == this.customerSelection);
                    if (c) {
                        this.client = {
                            name: c.uname || '',
                            mobile: c.contactno || '',
                            address: c.billingaddress || c.shippingaddress || '',
                            city: c.billingcity || c.shippingcity || '',
                            state: c.billingstate || c.shippingstate || '',
                            pincode: c.billingpincode || c.shippingpincode || '',
                            gsttin: c.gsttin || ''
                        };
                        
                        // Extract loyalty coins
                        const totalCoins = parseFloat(c.mcoinb || 0);
                        this.coins.tmcoin = totalCoins;
                        this.coins.bmcoin = totalCoins;
                        this.coins.mcoinp = 0;
                    }
                }

                // Recalculate rates of all items according to new customer tier
                this.items.forEach((item, idx) => {
                    if (item.productId) {
                        if (item.batchId) {
                            this.onBatchSelect(idx);
                        } else {
                            const p = this.products.find(x => x.id == item.productId);
                            if (p) {
                                this.updateItemPricingAccordingToTier(idx, p);
                                this.calculateRowTotal(idx);
                            }
                        }
                    }
                });
            },

            calculateRowTotal(index) {
                const item = this.items[index];
                item.total = (item.qty || 0) * (item.rate || 0);
                this.calculateTotals();
            },

            calculateTotals() {
                // Subtotal
                let subTotal = 0;
                let taxSum = 0;

                this.items.forEach(item => {
                    const lineVal = (item.qty || 0) * (item.rate || 0);
                    subTotal += lineVal;

                    // GST calculate
                    // Standard formula: GST amount = line value * (gst% / 100)
                    const lineGst = lineVal * ((item.gst || 0) / 100);
                    taxSum += lineGst;
                });

                this.financials.subTotal = subTotal;
                this.financials.tax = taxSum;

                // Grand total
                const grandTotal = subTotal + taxSum + 
                                   (parseFloat(this.financials.shipping) || 0) + 
                                   (parseFloat(this.financials.interest) || 0) - 
                                   (parseFloat(this.financials.discount) || 0);
                
                this.financials.grandTotal = Math.max(0, grandTotal);

                // Auto coins earned calculation (Rs. 100 spent = 1 Coin earned)
                this.coins.mcoin = Math.floor(this.financials.grandTotal / 100);
                this.updateCoinsBalance();

                // Recalculate paid and due based on current status
                this.onPaymentStatusChange();
            },

            onPaymentStatusChange() {
                if (this.financials.paymentStatus == 1) { // Fully paid
                    // Discount is already subtracted. Paid amount is exactly Grand Total minus any loyalty coins redeemed!
                    // Let's assume 1 coin = Rs. 1 discount
                    const coinReduction = parseFloat(this.coins.mcoinp) || 0;
                    this.financials.paid = Math.max(0, this.financials.grandTotal - coinReduction);
                } else if (this.financials.paymentStatus == 3) { // No paid
                    this.financials.paid = 0;
                }
                this.calculateDue();
            },

            calculateDue() {
                const coinReduction = parseFloat(this.coins.mcoinp) || 0;
                const grandTotalWithCoins = Math.max(0, this.financials.grandTotal - coinReduction);
                const due = grandTotalWithCoins - (this.financials.paid || 0);
                this.financials.due = Math.max(0, due);
            },

            onCoinRedemptionChange() {
                if (this.coins.mcoinp > this.coins.tmcoin) {
                    alert('Redeeming points cannot exceed total available customer coins!');
                    this.coins.mcoinp = this.coins.tmcoin;
                }
                if (this.coins.mcoinp > this.financials.grandTotal) {
                    this.coins.mcoinp = Math.floor(this.financials.grandTotal);
                }
                this.updateCoinsBalance();
                this.onPaymentStatusChange();
            },

            updateCoinsBalance() {
                this.coins.bmcoin = this.coins.tmcoin - (this.coins.mcoinp || 0) + (this.coins.mcoin || 0);
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
                    item.qty = (item.qty || 0) + 1;
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

            validateCustomerSelected() {
                if (this.customerSelection == '0') {
                    if (!this.client.name.trim() || !this.client.mobile.trim()) {
                        alert("Please select a customer account or enter customer name & mobile number first.");
                        return false;
                    }
                } else {
                    const c = this.customers.find(x => x.id == this.customerSelection);
                    if (!c) {
                        alert("Please select a valid customer account first.");
                        return false;
                    }
                }
                return true;
            },

            handleSubmit(event) {
                if (!this.validateCustomerSelected()) {
                    event.preventDefault();
                    return;
                }
                
                // First filter out completely empty rows (where productId is empty and rate is 0/empty)
                this.items = this.items.filter(item => item.productId || parseFloat(item.rate) > 0);

                // If no items remain, block the submit
                if (this.items.length === 0) {
                    alert("Please select at least one product before submitting.");
                    event.preventDefault();
                    return;
                }

                // Validate each remaining active line item
                for (let i = 0; i < this.items.length; i++) {
                    const item = this.items[i];
                    if (!item.productId) {
                        alert(`Product selection is missing for Line Item #${i + 1}. You can remove or select the product.`);
                        event.preventDefault();
                        return;
                    }
                    if (!item.qty || item.qty < 1) {
                        alert(`Quantity must be at least 1 for Line Item #${i + 1}.`);
                        event.preventDefault();
                        return;
                    }
                    if (item.rate === '' || item.rate === null || item.rate === undefined) {
                        alert(`Rate (Rs) is required for Line Item #${i + 1}.`);
                        event.preventDefault();
                        return;
                    }
                }
            },

            filteredCatalogProducts() {
                const search = this.catalogSearch.trim().toLowerCase();
                if (!search) return this.products;
                return this.products.filter(p => 
                    (p.productname && p.productname.toLowerCase().includes(search)) || 
                    (p.pcode && p.pcode.toLowerCase().includes(search))
                );
            },

            getProductTierPrice(p) {
                const tier = this.getCustomerTier();
                if (tier === 'S' && p.sdprice !== null) {
                    return parseFloat(p.sdprice);
                } else if (tier === 'D' && p.dprice !== null) {
                    return parseFloat(p.dprice);
                } else if (tier === 'C' && p.cprice !== null) {
                    return parseFloat(p.cprice);
                } else {
                    return parseFloat(p.srate || p.mrp || 0);
                }
            },

            addProductFromCatalog(product) {
                if (product.tqty <= 0) {
                    alert('This product is out of stock!');
                    return;
                }

                // Check if already in items
                const existingIndex = this.items.findIndex(item => item.productId == product.id);
                if (existingIndex !== -1) {
                    const item = this.items[existingIndex];
                    if (item.qty >= product.tqty) {
                        alert('Cannot add more. Exceeds available stock!');
                        return;
                    }
                    item.qty = (item.qty || 0) + 1;
                    this.calculateRowTotal(existingIndex);
                    this.showScanSuccess(`Incremented quantity for "${product.productname}"`);
                } else {
                    // Try to populate first empty row
                    let blankIndex = this.items.findIndex(item => !item.productId);
                    if (blankIndex !== -1) {
                        this.items[blankIndex].productId = product.id;
                        this.onProductSelect(blankIndex);
                    } else {
                        this.addLineItem();
                        let newIndex = this.items.length - 1;
                        this.items[newIndex].productId = product.id;
                        this.onProductSelect(newIndex);
                    }
                    this.showScanSuccess(`Added "${product.productname}" to items`);
                }
            },

            startCameraScanner() {
                this.cameraScannerOpen = true;
                this.$nextTick(() => {
                    const placeholder = document.getElementById('camera-placeholder');
                    if (placeholder) placeholder.style.display = 'flex';
                    
                    setTimeout(() => {
                        this.html5Qrcode = new Html5Qrcode("camera-reader", {
                            verbose: false,
                            formatsToSupport: [
                                Html5QrcodeSupportedFormats.EAN_13,
                                Html5QrcodeSupportedFormats.EAN_8,
                                Html5QrcodeSupportedFormats.CODE_128,
                                Html5QrcodeSupportedFormats.UPC_A,
                                Html5QrcodeSupportedFormats.UPC_E,
                                Html5QrcodeSupportedFormats.QR_CODE
                            ]
                        });
                        let lastScanTime = 0;
                        const qrCodeSuccessCallback = (decodedText, decodedResult) => {
                            const now = Date.now();
                            if (now - lastScanTime < 2000) return; // 2 seconds debounce to prevent rapid scans
                            lastScanTime = now;

                            this.playBeep();
                            this.barcodeScanInput = decodedText;
                            this.handleBarcodeScan();
                        };
                        const config = { 
                            fps: 10, 
                            qrbox: { width: 260, height: 160 },
                            aspectRatio: 1.777778
                        };
                        
                        this.html5Qrcode.start({ facingMode: "environment" }, config, qrCodeSuccessCallback)
                            .then(() => {
                                if (placeholder) placeholder.style.display = 'none';
                            })
                            .catch((err) => {
                                console.error("Unable to start scanning", err);
                                alert("Failed to access camera: " + err);
                                this.cameraScannerOpen = false;
                            });
                    }, 300);
                });
            },

            stopCameraScanner() {
                if (this.html5Qrcode) {
                    this.html5Qrcode.stop().then(() => {
                        this.html5Qrcode = null;
                        this.cameraScannerOpen = false;
                    }).catch((err) => {
                        console.error("Failed to stop scanner", err);
                        this.html5Qrcode = null;
                        this.cameraScannerOpen = false;
                    });
                } else {
                    this.cameraScannerOpen = false;
                }
            },

            playBeep() {
                try {
                    const audioCtx = new (window.AudioContext || window.webkitAudioContext)();
                    const oscillator = audioCtx.createOscillator();
                    const gainNode = audioCtx.createGain();
                    oscillator.connect(gainNode);
                    gainNode.connect(audioCtx.destination);
                    oscillator.type = 'sine';
                    oscillator.frequency.value = 1200;
                    gainNode.gain.setValueAtTime(0.2, audioCtx.currentTime);
                    oscillator.start();
                    oscillator.stop(audioCtx.currentTime + 0.1);
                } catch (e) {
                    console.log("AudioContext blocked or failed:", e);
                }
            }
        };
    }
</script>
@endsection
