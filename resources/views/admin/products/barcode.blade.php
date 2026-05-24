@extends('layouts.admin')

@section('content')
<div x-data="{
    products: @js($products),
    selectedProductId: '',
    pcode: '',
    pname: '',
    mrp: '0.00',
    cprice: '0.00',
    dprice: '0.00',
    sdprice: '0.00',
    qty: 6,

    updateFields() {
        if (!this.selectedProductId) {
            this.pcode = '';
            this.pname = '';
            this.mrp = '0.00';
            this.cprice = '0.00';
            this.dprice = '0.00';
            this.sdprice = '0.00';
            return;
        }

        const prod = this.products.find(p => p.id == this.selectedProductId);
        if (prod) {
            this.pcode = prod.pcode;
            this.pname = prod.productname;
            this.mrp = parseFloat(prod.mrp).toFixed(2);
            this.cprice = parseFloat(prod.cprice).toFixed(2);
            this.dprice = parseFloat(prod.dprice).toFixed(2);
            this.sdprice = parseFloat(prod.sdprice || 0).toFixed(2);
        }
    }
}" class="space-y-8 animate-fadeIn">

    <x-admin.header 
        title="Product Barcodes" 
        description="Select products to generate vector laser-sharp Code39 barcode sticker sheets." 
        icon="fa-solid fa-barcode"
        glass="true"
    />

    <!-- Generator Panel -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Left Selection Form Panel -->
        <div class="lg:col-span-2 p-6 md:p-8 rounded-3xl bg-slate-900/50 border border-slate-800/80 shadow-xl space-y-6">
            <h3 class="text-lg font-bold text-slate-200 border-l-4 border-indigo-500 pl-3 uppercase tracking-wider text-sm">Configure Label Generator</h3>
            
            <form method="POST" action="{{ route('admin.products.barcode.print') }}" target="_blank" class="space-y-6">
                @csrf
                
                <!-- Product Selector -->
                <div class="space-y-2">
                    <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider">Search Code or Product Name</label>
                    <select x-model="selectedProductId" @change="updateFields()" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3.5 text-sm text-slate-300 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                        <option value="">~ Select a Product (Searchable by Typing) ~</option>
                        <template x-for="prod in products" :key="prod.id">
                            <option :value="prod.id" x-text="prod.pcode + ' - ' + prod.productname"></option>
                        </template>
                    </select>
                </div>

                <!-- Bound Product Specs (Two Column Grid) -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider">Product Code</label>
                        <input type="text" name="pcode" x-model="pcode" readonly required placeholder="Auto-populated" class="w-full bg-slate-950/40 border border-slate-800/60 rounded-xl px-4 py-3 text-sm text-slate-500 cursor-not-allowed focus:outline-none">
                    </div>
                    <div class="space-y-2">
                        <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider">Product Name</label>
                        <input type="text" name="pname" x-model="pname" readonly required placeholder="Auto-populated" class="w-full bg-slate-950/40 border border-slate-800/60 rounded-xl px-4 py-3 text-sm text-slate-500 cursor-not-allowed focus:outline-none">
                    </div>
                </div>

                <!-- Pricing Specs -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="space-y-2">
                        <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider">MRP (INR)</label>
                        <input type="text" name="mrp" x-model="mrp" readonly required placeholder="0.00" class="w-full bg-slate-950/40 border border-slate-800/60 rounded-xl px-4 py-3 text-sm text-slate-500 cursor-not-allowed focus:outline-none">
                    </div>
                    <div class="space-y-2">
                        <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider">Customer Price</label>
                        <input type="text" name="cprice" x-model="cprice" readonly required placeholder="0.00" class="w-full bg-slate-950/40 border border-slate-800/60 rounded-xl px-4 py-3 text-sm text-slate-500 cursor-not-allowed focus:outline-none">
                    </div>
                    <div class="space-y-2">
                        <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider">Dealer Price</label>
                        <input type="text" name="dprice" x-model="dprice" readonly required placeholder="0.00" class="w-full bg-slate-950/40 border border-slate-800/60 rounded-xl px-4 py-3 text-sm text-slate-500 cursor-not-allowed focus:outline-none">
                    </div>
                    <div class="space-y-2">
                        <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider">S-Dealer Price</label>
                        <input type="text" name="sdprice" x-model="sdprice" readonly required placeholder="0.00" class="w-full bg-slate-950/40 border border-slate-800/60 rounded-xl px-4 py-3 text-sm text-slate-500 cursor-not-allowed focus:outline-none">
                    </div>
                </div>

                <!-- Quantity and Generator Actions -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-end pt-4 border-t border-slate-800/50">
                    <div class="space-y-2">
                        <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider">Sticker Quantity</label>
                        <div class="relative">
                            <input type="number" name="qty" x-model="qty" required min="1" max="120" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-sm text-slate-300 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                            <span class="absolute right-4 top-1/2 -translate-y-1/2 text-xs font-bold text-slate-500">labels</span>
                        </div>
                    </div>
                    <x-admin.button type="submit" x-bind:disabled="!selectedProductId" variant="primary" icon="fa-solid fa-barcode" class="w-full py-3.5">
                        Generate printable labels
                    </x-admin.button>
                </div>
            </form>
        </div>

        <!-- Right Informative/Preview Panel -->
        <div class="p-6 md:p-8 rounded-3xl glassmorphism space-y-6 flex flex-col justify-between">
            <div class="space-y-4">
                <h3 class="text-lg font-bold text-slate-200 uppercase tracking-wider text-xs flex items-center gap-2">
                    <i class="fa-solid fa-circle-info text-indigo-400"></i>
                    <span>Sticker Specifications</span>
                </h3>
                <p class="text-xs text-slate-400 leading-relaxed">
                    Labels render directly using crisp, clean Vector SVGs optimized for high-resolution thermal and standard desktop printers. This system completely avoids rendering fuzziness typical of PNG/JPG barcode renders.
                </p>
                <div class="p-4 rounded-2xl bg-slate-950/60 border border-slate-850 space-y-3 text-xs">
                    <div class="flex justify-between">
                        <span class="text-slate-500">Paper Width:</span>
                        <span class="font-mono text-slate-300">101.6 mm</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Paper Height:</span>
                        <span class="font-mono text-slate-300">34.4 mm</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Grid Format:</span>
                        <span class="font-mono text-slate-300">3 labels landscape row</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Barcode Standard:</span>
                        <span class="font-mono text-slate-300">Code 39 Extended</span>
                    </div>
                </div>
            </div>

            <!-- Visual Representation Mockup -->
            <div class="p-4 rounded-2xl bg-slate-950 border border-slate-800 text-center space-y-3">
                <span class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest">Tag Obfuscation Key</span>
                <div class="p-3 bg-white text-black rounded-lg text-left font-mono space-y-1 shadow-inner select-none scale-95 origin-center">
                    <div class="flex justify-between text-[8px] font-bold">
                        <span>PCODE</span>
                        <span>MRP: 150</span>
                    </div>
                    <div class="text-[9px] font-bold">₹ 120 M4 110</div>
                    <div class="border-t border-black/40 my-1 py-1 flex items-center justify-center font-serif tracking-widest text-[11px]">
                        ||||||||||||||||||||||
                    </div>
                    <div class="flex justify-between text-[7px] font-bold">
                        <span class="uppercase">PROD_NAME</span>
                        <span>SD83 95</span>
                    </div>
                </div>
                <p class="text-[10px] text-slate-500 leading-tight">
                    Customer Price is printed openly. Dealer Price (<span class="text-indigo-400 font-bold">110</span>) is obfuscated after `M` prefix. S-Dealer (<span class="text-indigo-400 font-bold">95</span>) is obfuscated after `SD` prefix.
                </p>
            </div>
        </div>

    </div>

</div>
@endsection
