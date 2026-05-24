@extends('layouts.admin', ['title' => 'Edit Product'])

@section('content')
<div class="max-w-4xl mx-auto space-y-8 animate-fadeIn" x-data="{
    categories: @js($categories),
    subcategories: @js($subcategories),
    brands: @js($brands),
    addCatId: '{{ $product->catid }}',
    addScatId: '{{ $product->subcatid }}',
    addBrandId: '{{ $product->brandid }}',

    getFilteredSubcategories(catId) {
        if (!catId) return [];
        return this.subcategories.filter(sub => sub.catid == catId);
    },
    
    getFilteredBrands(catId, scatId) {
        if (!catId || !scatId) return [];
        return this.brands.filter(b => b.catid == catId && b.scatid == scatId);
    }
}">
    <!-- Header Section -->
    <x-admin.header 
        title="Edit Product" 
        description="Update specifications, Category connections, tax rates, price structures, and assets for catalog item #{{ $product->pcode }}." 
        icon="fa-solid fa-pen-to-square"
        glass="true"
    >
        <x-slot:action>
            <x-admin.button href="{{ route('admin.products.index') }}" variant="secondary" icon="fa-solid fa-arrow-left">
                Back to Products
            </x-admin.button>
        </x-slot:action>
    </x-admin.header>

    <!-- Error Alerts -->
    @if(session('error'))
        <div class="p-4 rounded-2xl bg-rose-500/10 border border-rose-500/20 text-rose-600 dark:text-rose-400 text-sm font-semibold flex items-center gap-2">
            <i class="fa-solid fa-circle-exclamation text-base"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.products.update', $product->id) }}" enctype="multipart/form-data" class="space-y-8">
        @csrf
        @method('PUT')

        <!-- SECTION 1: General Details -->
        <div class="p-8 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xl space-y-6">
            <div class="border-b border-slate-200 dark:border-slate-800 pb-3">
                <h3 class="text-lg font-bold text-slate-800 dark:text-slate-100 flex items-center gap-2">
                    <i class="fa-solid fa-circle-info text-indigo-500"></i>
                    <span>General Information</span>
                </h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Specify core descriptive details, barcode labels, and catalog measurements.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Product Name <span class="text-rose-500">*</span></label>
                    <input type="text" name="productname" required value="{{ $product->productname }}" placeholder="Enter product commercial name" 
                           class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl px-4 py-3 text-sm text-slate-700 dark:text-slate-300 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 transition-all">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Product Code (PCODE) <span class="text-rose-500">*</span></label>
                    <input type="text" name="pcode" required maxlength="6" value="{{ $product->pcode }}" placeholder="max 6 characters" 
                           class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl px-4 py-3 text-sm text-slate-700 dark:text-slate-300 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 uppercase font-mono font-bold tracking-wider transition-all">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Product Description <span class="text-rose-500">*</span></label>
                    <input type="text" name="productdes" required value="{{ $product->productdes }}" placeholder="Enter broad specifications and packaging layout details..." 
                           class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl px-4 py-3 text-sm text-slate-700 dark:text-slate-300 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 transition-all">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Unit <span class="text-rose-500">*</span></label>
                    <select name="unit" required 
                            class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl px-4 py-3 text-sm text-slate-700 dark:text-slate-300 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 cursor-pointer transition-all">
                        <option value="">~ Choose Base Unit ~</option>
                        <option value="1" {{ $product->unit == 1 ? 'selected' : '' }}>Number (PCS)</option>
                        <option value="2" {{ $product->unit == 2 ? 'selected' : '' }}>Meter (MTR)</option>
                        <option value="3" {{ $product->unit == 3 ? 'selected' : '' }}>Packet (PKT)</option>
                        <option value="4" {{ $product->unit == 4 ? 'selected' : '' }}>Liter (LTR)</option>
                    </select>
                </div>
                <div class="md:col-span-3">
                    <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">HSN/SAC Code <span class="text-rose-500">*</span></label>
                    <input type="text" name="hsnsac" required value="{{ $product->hsnsac }}" placeholder="GST classification code (HSN/SAC)" 
                           class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl px-4 py-3 text-sm text-slate-700 dark:text-slate-300 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 transition-all">
                </div>
            </div>
        </div>

        <!-- SECTION 2: Category & Branding -->
        <div class="p-8 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xl space-y-6">
            <div class="border-b border-slate-200 dark:border-slate-800 pb-3">
                <h3 class="text-lg font-bold text-slate-800 dark:text-slate-100 flex items-center gap-2">
                    <i class="fa-solid fa-folder-open text-indigo-500"></i>
                    <span>Branding & Category Linkages</span>
                </h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Select the hierarchical category node and assign appropriate structural brands.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                <div>
                    <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Category <span class="text-rose-500">*</span></label>
                    <select name="catid" x-model="addCatId" required 
                            class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl px-4 py-3 text-sm text-slate-700 dark:text-slate-300 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 cursor-pointer transition-all">
                        <option value="">~ Select Category ~</option>
                        <template x-for="cat in categories" :key="cat.cat_id">
                            <option :value="cat.cat_id" x-text="cat.cat_name"></option>
                        </template>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Sub Category <span class="text-rose-500">*</span></label>
                    <select name="subcatid" x-model="addScatId" :disabled="!addCatId" required 
                            class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl px-4 py-3 text-sm text-slate-700 dark:text-slate-300 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 disabled:opacity-40 disabled:cursor-not-allowed cursor-pointer transition-all">
                        <option value="">~ Select Subcategory ~</option>
                        <template x-for="sub in getFilteredSubcategories(addCatId)" :key="sub.id">
                            <option :value="sub.id" x-text="sub.subcategoryname"></option>
                        </template>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Brand <span class="text-rose-500">*</span></label>
                    <select name="brandid" x-model="addBrandId" :disabled="!addScatId" required 
                            class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl px-4 py-3 text-sm text-slate-700 dark:text-slate-300 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 disabled:opacity-40 disabled:cursor-not-allowed cursor-pointer transition-all">
                        <option value="">~ Select Brand ~</option>
                        <template x-for="b in getFilteredBrands(addCatId, addScatId)" :key="b.brand_id">
                            <option :value="b.brand_id" x-text="b.brand_name"></option>
                        </template>
                    </select>
                </div>
            </div>
        </div>

        <!-- SECTION 3: Stock & Tiers -->
        <div class="p-8 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xl space-y-6">
            <div class="border-b border-slate-200 dark:border-slate-800 pb-3">
                <h3 class="text-lg font-bold text-slate-800 dark:text-slate-100 flex items-center gap-2">
                    <i class="fa-solid fa-coins text-indigo-500"></i>
                    <span>Stock Valuation & Multi-Tier Pricing</span>
                </h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Specify packaging capacities, purchase cost, tax liabilities, and margins for retail, dealers, and sub-dealers.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                <div>
                    <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Opening Quantity <span class="text-rose-500">*</span></label>
                    <input type="number" name="tqty" required value="{{ $product->tqty }}" placeholder="0" 
                           class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl px-4 py-3 text-sm text-slate-700 dark:text-slate-300 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 font-bold transition-all">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Packaging Capacity (Per Pack) <span class="text-rose-500">*</span></label>
                    <input type="number" name="pqty" required value="{{ $product->pqty }}" placeholder="1" 
                           class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl px-4 py-3 text-sm text-slate-700 dark:text-slate-300 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 font-bold transition-all">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Vendor / Procurement Source <span class="text-rose-500">*</span></label>
                    <input type="text" name="pfrom" required value="{{ $product->pfrom }}" placeholder="Enter supplier/vendor name" 
                           class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl px-4 py-3 text-sm text-slate-700 dark:text-slate-300 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 transition-all">
                </div>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-5 pt-3 border-t border-slate-100 dark:border-slate-850">
                <div>
                    <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Procurement Cost <span class="text-rose-500">*</span></label>
                    <input type="number" step="0.01" name="prate" required value="{{ $product->prate }}" placeholder="0.00" 
                           class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl px-4 py-3 text-sm text-slate-700 dark:text-slate-300 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 font-bold text-indigo-600 dark:text-indigo-400 transition-all">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Shipping Charge <span class="text-rose-500">*</span></label>
                    <input type="number" step="0.01" name="srate" required value="{{ $product->srate }}" placeholder="0.00" 
                           class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl px-4 py-3 text-sm text-slate-700 dark:text-slate-300 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 font-bold transition-all">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">MRP <span class="text-rose-500">*</span></label>
                    <input type="number" step="0.01" name="mrp" required value="{{ $product->mrp }}" placeholder="0.00" 
                           class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl px-4 py-3 text-sm text-slate-700 dark:text-slate-300 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 font-bold transition-all">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">GST% <span class="text-rose-500">*</span></label>
                    <input type="number" step="0.1" name="gst" required value="{{ $product->gst }}" placeholder="18.0" 
                           class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl px-4 py-3 text-sm text-slate-700 dark:text-slate-300 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 font-bold text-rose-550 dark:text-rose-450 transition-all">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-5 pt-3 border-t border-slate-100 dark:border-slate-850">
                <div>
                    <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Customer Rate <span class="text-rose-500">*</span></label>
                    <input type="number" step="0.01" name="cprice" required value="{{ $product->cprice }}" placeholder="0.00" 
                           class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl px-4 py-3 text-sm text-slate-700 dark:text-slate-300 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 font-bold text-emerald-600 dark:text-emerald-400 transition-all">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Dealer Rate <span class="text-rose-500">*</span></label>
                    <input type="number" step="0.01" name="dprice" required value="{{ $product->dprice }}" placeholder="0.00" 
                           class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl px-4 py-3 text-sm text-slate-700 dark:text-slate-300 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 font-bold text-indigo-650 dark:text-indigo-400 transition-all">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Super Dealer Rate <span class="text-rose-500">*</span></label>
                    <input type="number" step="0.01" name="sdprice" required value="{{ $product->sdprice }}" placeholder="0.00" 
                           class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl px-4 py-3 text-sm text-slate-700 dark:text-slate-300 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 font-bold text-purple-650 dark:text-purple-450 transition-all">
                </div>
            </div>
        </div>

        <!-- SECTION 4: Product Media -->
        <div class="p-8 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xl space-y-6">
            <div class="border-b border-slate-200 dark:border-slate-800 pb-3">
                <h3 class="text-lg font-bold text-slate-800 dark:text-slate-100 flex items-center gap-2">
                    <i class="fa-solid fa-images text-indigo-500"></i>
                    <span>Product Showcase Media</span>
                </h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Upload new images to overwrite existing catalog assets.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                <div class="p-6 rounded-2xl bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 text-center space-y-3">
                    <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Product Showcase Image 1</label>
                    <div class="text-[10px] text-indigo-600 dark:text-indigo-400 truncate">{{ $product->pimagef ?: 'No image file uploaded' }}</div>
                    <input type="file" name="productimagef" 
                           class="block w-full text-xs text-slate-500 dark:text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-[10px] file:font-bold file:bg-indigo-500/10 file:text-indigo-600 dark:file:text-indigo-400 hover:file:bg-indigo-500/20 cursor-pointer">
                    <p class="text-[9px] text-slate-400 dark:text-slate-500 font-medium">GIF, JPG, JPEG, PNG (Max Size: 250KB)</p>
                </div>
                <div class="p-6 rounded-2xl bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 text-center space-y-3">
                    <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Product Showcase Image 2</label>
                    <div class="text-[10px] text-indigo-600 dark:text-indigo-400 truncate">{{ $product->pimages ?: 'No image file uploaded' }}</div>
                    <input type="file" name="productimages" 
                           class="block w-full text-xs text-slate-500 dark:text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-[10px] file:font-bold file:bg-indigo-500/10 file:text-indigo-600 dark:file:text-indigo-400 hover:file:bg-indigo-500/20 cursor-pointer">
                    <p class="text-[9px] text-slate-400 dark:text-slate-500 font-medium">GIF, JPG, JPEG, PNG (Max Size: 250KB)</p>
                </div>
                <div class="p-6 rounded-2xl bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 text-center space-y-3">
                    <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Product Showcase Image 3</label>
                    <div class="text-[10px] text-indigo-600 dark:text-indigo-400 truncate">{{ $product->pimaget ?: 'No image file uploaded' }}</div>
                    <input type="file" name="productimaget" 
                           class="block w-full text-xs text-slate-500 dark:text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-[10px] file:font-bold file:bg-indigo-500/10 file:text-indigo-600 dark:file:text-indigo-400 hover:file:bg-indigo-500/20 cursor-pointer">
                    <p class="text-[9px] text-slate-400 dark:text-slate-500 font-medium">GIF, JPG, JPEG, PNG (Max Size: 250KB)</p>
                </div>
            </div>
        </div>

        <!-- Form Submit Footer -->
        <div class="p-6 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xl flex justify-end">
            <x-admin.button type="submit" variant="primary" icon="fa-solid fa-circle-check text-lg" class="px-8 py-4 shadow-lg shadow-indigo-500/10 uppercase text-xs font-bold tracking-wider">
                Save Product Changes
            </x-admin.button>
        </div>
    </form>
</div>
@endsection
