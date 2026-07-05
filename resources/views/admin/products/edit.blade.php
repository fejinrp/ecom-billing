@extends('layouts.admin', ['title' => 'Edit Product'])

@section('content')
<!-- Select2 CSS from CDN -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    /* Premium Select2 Styling to match Tailwind Inputs */
    .select2-container .select2-selection--single {
        background-color: #f8fafc !important; /* bg-slate-50 equivalent */
        border: 1px solid #e2e8f0 !important; /* border-slate-200 equivalent */
        border-radius: 0.75rem !important; /* rounded-xl */
        height: 3rem !important;
        padding-left: 0.75rem !important;
        padding-right: 0.75rem !important;
        color: #334155 !important; /* text-slate-700 */
        display: flex;
        align-items: center;
        transition: all 0.2s ease-in-out;
    }
    .dark .select2-container .select2-selection--single {
        background-color: #020617 !important; /* bg-slate-950 equivalent */
        border: 1px solid #1e293b !important; /* border-slate-800 equivalent */
        color: #cbd5e1 !important; /* text-slate-300 */
    }
    .select2-container .select2-selection--single .select2-selection__rendered {
        color: #334155 !important;
        font-size: 0.875rem !important; /* text-sm */
        font-weight: 500;
        padding-left: 0 !important;
    }
    .dark .select2-container .select2-selection--single .select2-selection__rendered {
        color: #cbd5e1 !important;
    }
    .select2-container .select2-selection--single .select2-selection__arrow {
        height: 3rem !important;
        right: 10px !important;
    }
    .select2-dropdown {
        background-color: #ffffff !important;
        border: 1px solid #e2e8f0 !important;
        border-radius: 0.75rem !important;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05) !important;
        overflow: hidden;
    }
    .dark .select2-dropdown {
        background-color: #020617 !important;
        border: 1px solid #1e293b !important;
    }
    .select2-results__option {
        padding: 0.75rem 1rem !important;
        font-size: 0.875rem !important;
        color: #334155 !important;
    }
    .dark .select2-results__option {
        color: #cbd5e1 !important;
    }
    .select2-container .select2-results__option--highlighted[aria-selected] {
        background-color: #4f46e5 !important; /* indigo-600 */
        color: #ffffff !important;
    }
    .select2-container .select2-results__option[aria-selected=true] {
        background-color: rgba(79, 70, 229, 0.1) !important;
        color: #4f46e5 !important;
    }
    .dark .select2-container .select2-results__option[aria-selected=true] {
        color: #818cf8 !important;
    }
</style>

<div class="max-w-4xl mx-auto space-y-8 animate-fadeIn" x-data="{
    categories: @js($categories),
    subcategories: @js($subcategories),
    brands: @js($brands),
    selectedCat: '{{ $product->catid }}',
    selectedSubcat: '{{ $product->subcatid }}',
    selectedBrand: '{{ $product->brandid }}'
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
    @if($errors->any())
        <div class="p-4 rounded-2xl bg-rose-500/10 border border-rose-500/20 text-rose-600 dark:text-rose-400 text-sm font-semibold space-y-2 animate-fadeIn">
            <div class="flex items-center gap-2">
                <i class="fa-solid fa-circle-exclamation text-base"></i>
                <span class="font-bold">Please correct the following errors:</span>
            </div>
            <ul class="list-disc pl-5 text-xs space-y-1 font-medium">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" id="productForm" action="{{ route('admin.products.update', $product->id) }}" enctype="multipart/form-data" class="space-y-8">
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
                    <input type="text" name="productname" required value="{{ old('productname', $product->productname) }}" placeholder="Enter product commercial name" 
                           class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl px-4 py-3 text-sm text-slate-700 dark:text-slate-300 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 transition-all">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Product Code (PCODE) <span class="text-rose-500">*</span></label>
                    <input type="text" name="pcode" required maxlength="50" value="{{ old('pcode', $product->pcode) }}" placeholder="max 50 characters" 
                           class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl px-4 py-3 text-sm text-slate-700 dark:text-slate-300 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 uppercase font-mono font-bold tracking-wider transition-all">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Product Description <span class="text-rose-500">*</span></label>
                    <input type="text" name="productdes" required value="{{ old('productdes', $product->productdes) }}" placeholder="Enter broad specifications and packaging layout details..." 
                           class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl px-4 py-3 text-sm text-slate-700 dark:text-slate-300 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 transition-all">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Unit <span class="text-rose-500">*</span></label>
                    <select name="unit" required 
                            class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl px-4 py-3 text-sm text-slate-700 dark:text-slate-300 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 cursor-pointer transition-all">
                        <option value="">~ Choose Base Unit ~</option>
                        <option value="1" {{ old('unit', $product->unit) == 1 ? 'selected' : '' }}>Number (PCS)</option>
                        <option value="2" {{ old('unit', $product->unit) == 2 ? 'selected' : '' }}>Meter (MTR)</option>
                        <option value="3" {{ old('unit', $product->unit) == 3 ? 'selected' : '' }}>Packet (PKT)</option>
                        <option value="4" {{ old('unit', $product->unit) == 4 ? 'selected' : '' }}>Liter (LTR)</option>
                    </select>
                </div>
                <div class="md:col-span-3">
                    <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">HSN/SAC Code</label>
                    <input type="text" name="hsnsac" value="{{ old('hsnsac', $product->hsnsac) }}" placeholder="GST classification code (HSN/SAC)" 
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
                    <select name="catid" id="catid_select" x-model="selectedCat" required class="select2-select w-full">
                        <option value="">~ Select Category ~</option>
                        <template x-for="cat in categories" :key="cat.cat_id">
                            <option :value="cat.cat_id" x-text="cat.cat_name"></option>
                        </template>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Sub Category <span class="text-rose-500">*</span></label>
                    <select name="subcatid" id="subcatid_select" x-model="selectedSubcat" :disabled="!selectedCat" required class="select2-select w-full">
                        <option value="">~ Select Subcategory ~</option>
                        <template x-for="sub in subcategories.filter(s => s.catid == selectedCat)" :key="sub.id">
                            <option :value="sub.id" x-text="sub.subcategoryname"></option>
                        </template>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Brand <span class="text-rose-500">*</span></label>
                    <select name="brandid" id="brandid_select" x-model="selectedBrand" :disabled="!selectedSubcat" required class="select2-select w-full">
                        <option value="">~ Select Brand ~</option>
                        <template x-for="b in brands.filter(br => br.catid == selectedCat && br.scatid == selectedSubcat)" :key="b.brand_id">
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
                    <input type="number" name="tqty" required value="{{ old('tqty', $product->tqty) }}" placeholder="0" 
                           class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl px-4 py-3 text-sm text-slate-700 dark:text-slate-300 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 font-bold transition-all">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Packaging Capacity (Per Pack) <span class="text-rose-500">*</span></label>
                    <input type="number" name="pqty" required value="{{ old('pqty', $product->pqty) }}" placeholder="1" 
                           class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl px-4 py-3 text-sm text-slate-700 dark:text-slate-300 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 font-bold transition-all">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Vendor / Procurement Source <span class="text-rose-500">*</span></label>
                    <input type="text" name="pfrom" required value="{{ old('pfrom', $product->pfrom) }}" placeholder="Enter supplier/vendor name" 
                           class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl px-4 py-3 text-sm text-slate-700 dark:text-slate-300 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 transition-all">
                </div>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-5 pt-3 border-t border-slate-100 dark:border-slate-850">
                <div>
                    <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Procurement Cost <span class="text-rose-500">*</span></label>
                    <input type="number" step="0.01" name="prate" required value="{{ old('prate', $product->prate) }}" placeholder="0.00" 
                           class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl px-4 py-3 text-sm text-slate-700 dark:text-slate-300 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 font-bold text-indigo-600 dark:text-indigo-400 transition-all">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Shipping Charge <span class="text-rose-500">*</span></label>
                    <input type="number" step="0.01" name="srate" required value="{{ old('srate', $product->srate) }}" placeholder="0.00" 
                           class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl px-4 py-3 text-sm text-slate-700 dark:text-slate-300 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 font-bold transition-all">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">MRP <span class="text-rose-500">*</span></label>
                    <input type="number" step="0.01" name="mrp" required value="{{ old('mrp', $product->mrp) }}" placeholder="0.00" 
                           class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl px-4 py-3 text-sm text-slate-700 dark:text-slate-300 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 font-bold transition-all">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">GST%</label>
                    <select name="gst" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl px-4 py-3 text-sm text-slate-700 dark:text-slate-300 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 cursor-pointer transition-all">
                        <option value="0" {{ old('gst', $product->gst) == 0 ? 'selected' : '' }}>GST 0%</option>
                        <option value="5" {{ old('gst', $product->gst) == 5 ? 'selected' : '' }}>GST 5%</option>
                        <option value="12" {{ old('gst', $product->gst) == 12 ? 'selected' : '' }}>GST 12%</option>
                        <option value="18" {{ old('gst', $product->gst) == 18 ? 'selected' : '' }}>GST 18%</option>
                        <option value="28" {{ old('gst', $product->gst) == 28 ? 'selected' : '' }}>GST 28%</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-5 pt-3 border-t border-slate-100 dark:border-slate-855">
                <div>
                    <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Customer Rate <span class="text-rose-500">*</span></label>
                    <input type="number" step="0.01" name="cprice" required value="{{ old('cprice', $product->cprice) }}" placeholder="0.00" 
                           class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl px-4 py-3 text-sm text-slate-700 dark:text-slate-300 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 font-bold text-emerald-600 dark:text-emerald-400 transition-all">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Dealer Rate <span class="text-rose-500">*</span></label>
                    <input type="number" step="0.01" name="dprice" required value="{{ old('dprice', $product->dprice) }}" placeholder="0.00" 
                           class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl px-4 py-3 text-sm text-slate-700 dark:text-slate-300 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 font-bold text-indigo-650 dark:text-indigo-400 transition-all">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Super Dealer Rate <span class="text-rose-500">*</span></label>
                    <input type="number" step="0.01" name="sdprice" required value="{{ old('sdprice', $product->sdprice) }}" placeholder="0.00" 
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

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        // Initialize Select2 dropdowns
        $('.select2-select').select2({
            width: '100%'
        });

        // Initialize presets for edit values
        setTimeout(() => {
            $('#catid_select').val('{{ $product->catid }}').trigger('change');
            setTimeout(() => {
                $('#subcatid_select').val('{{ $product->subcatid }}').trigger('change');
                setTimeout(() => {
                    $('#brandid_select').val('{{ $product->brandid }}').trigger('change');
                }, 100);
            }, 100);
        }, 100);

        // Sync dropdown selectors with Alpine state when select2 changes value
        $('#catid_select').on('change', function(e) {
            if (e.originalEvent) return;
            this.dispatchEvent(new Event('change'));

            setTimeout(() => {
                $('#subcatid_select').val('').trigger('change');
            }, 50);
        });

        $('#subcatid_select').on('change', function(e) {
            if (e.originalEvent) return;
            this.dispatchEvent(new Event('change'));

            setTimeout(() => {
                $('#brandid_select').val('').trigger('change');
            }, 50);
        });

        $('#brandid_select').on('change', function(e) {
            if (e.originalEvent) return;
            this.dispatchEvent(new Event('change'));
        });
    });
</script>
@endsection
