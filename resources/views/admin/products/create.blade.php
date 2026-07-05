@extends('layouts.admin', ['title' => 'Add New Product'])

@section('content')
<!-- Select2 CSS from CDN -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    /* Premium Select2 Styling to match Tailwind Inputs */
    .select2-container--default .select2-selection--single {
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
    .dark .select2-container--default .select2-selection--single {
        background-color: #020617 !important; /* bg-slate-950 equivalent */
        border: 1px solid #1e293b !important; /* border-slate-800 equivalent */
        color: #cbd5e1 !important; /* text-slate-300 */
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        color: #334155 !important;
        font-size: 0.875rem !important; /* text-sm */
        font-weight: 500;
        padding-left: 0 !important;
    }
    .dark .select2-container--default .select2-selection--single .select2-selection__rendered {
        color: #cbd5e1 !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
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
    .select2-container--default .select2-results__option--highlighted[aria-selected] {
        background-color: #4f46e5 !important; /* indigo-600 */
        color: #ffffff !important;
    }
    .select2-container--default .select2-results__option[aria-selected=true] {
        background-color: rgba(79, 70, 229, 0.1) !important;
        color: #4f46e5 !important;
    }
    .dark .select2-container--default .select2-results__option[aria-selected=true] {
        color: #818cf8 !important;
    }
</style>

<div class="max-w-5xl mx-auto space-y-6 animate-fadeIn" 
     @close-category-modal.window="showAddCategory = false"
     @close-subcategory-modal.window="showAddSubcategory = false"
     @close-brand-modal.window="showAddBrand = false"
     @close-supplier-modal.window="showAddSupplier = false"
     x-data="{
    showAddCategory: false,
    showAddSubcategory: false,
    showAddBrand: false,
    showAddSupplier: false,
    hasDraft: false,
    categories: @js($categories),
    subcategories: @js($subcategories),
    brands: @js($brands),
    selectedCat: '',
    selectedSubcat: '',

    init() {
        if (localStorage.getItem('product_create_draft')) {
            this.hasDraft = true;
        }
        // Auto-save setup every 8 seconds
        setInterval(() => {
            this.saveDraft();
        }, 8000);
    },

    saveDraft() {
        const form = document.getElementById('productForm');
        if (form) {
            const formData = new FormData(form);
            const data = {};
            formData.forEach((value, key) => {
                if (key !== '_token' && !(value instanceof File)) {
                    data[key] = value;
                }
            });
            if (Object.keys(data).length > 0) {
                localStorage.setItem('product_create_draft', JSON.stringify(data));
            }
        }
    },

    loadDraft() {
        const draft = JSON.parse(localStorage.getItem('product_create_draft'));
        if (draft) {
            Object.keys(draft).forEach(key => {
                const el = document.getElementsByName(key)[0];
                if (el) {
                    el.value = draft[key];
                    if (el.tagName === 'SELECT') {
                        $(el).val(draft[key]).trigger('change');
                    }
                }
            });
            this.hasDraft = false;
        }
    },

    discardDraft() {
        localStorage.removeItem('product_create_draft');
        this.hasDraft = false;
    }
}">
    <!-- Header Section -->
    <x-admin.header 
        title="Create New Product" 
        description="Provide catalog specifications, dynamic brand linkages, procurement details, tax metrics, pricing tiers, and showcase media assets." 
        icon="fa-solid fa-circle-plus"
        glass="true"
    >
        <x-slot:action>
            <x-admin.button href="{{ route('admin.products.index') }}" variant="secondary" icon="fa-solid fa-arrow-left">
                Back to Products
            </x-admin.button>
        </x-slot:action>
    </x-admin.header>

    <!-- Draft Alert -->
    <div x-show="hasDraft" x-transition class="p-4 rounded-2xl bg-indigo-500/10 border border-indigo-500/20 text-indigo-600 dark:text-indigo-400 text-sm font-semibold flex items-center justify-between gap-2">
        <div class="flex items-center gap-2">
            <i class="fa-solid fa-file-invoice text-base"></i>
            <span>We recovered an unsaved product draft. Do you want to load it?</span>
        </div>
        <div class="flex gap-2">
            <button type="button" @click="loadDraft()" class="bg-indigo-600 text-white px-3 py-1.5 rounded-lg text-xs font-bold hover:bg-indigo-700 transition-all">Load Draft</button>
            <button type="button" @click="discardDraft()" class="bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-300 px-3 py-1.5 rounded-lg text-xs font-bold transition-all">Discard</button>
        </div>
    </div>

    <!-- Error Alerts -->
    @if(session('error'))
        <div class="p-4 rounded-2xl bg-rose-500/10 border border-rose-500/20 text-rose-600 dark:text-rose-400 text-sm font-semibold flex items-center gap-2">
            <i class="fa-solid fa-circle-exclamation text-base"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <form method="POST" id="productForm" action="{{ route('admin.products.store') }}" enctype="multipart/form-data" class="space-y-6">
        @csrf

        <!-- SECTION 1: General Details -->
        <div class="p-8 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xl space-y-6" x-data="{ open: true }">
            <div class="flex justify-between items-center cursor-pointer border-b border-slate-200 dark:border-slate-800 pb-3" @click="open = !open">
                <h3 class="text-lg font-bold text-slate-800 dark:text-slate-100 flex items-center gap-2">
                    <i class="fa-solid fa-circle-info text-indigo-500"></i>
                    <span>1. General Information</span>
                </h3>
                <i class="fa-solid" :class="open ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
            </div>
            
            <div x-show="open" x-transition class="grid grid-cols-1 md:grid-cols-3 gap-5">
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Product Name <span class="text-rose-500">*</span></label>
                    <input type="text" name="productname" id="productname" required placeholder="Enter product commercial name" 
                           class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl px-4 py-3 text-sm text-slate-700 dark:text-slate-300 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 transition-all">
                    <div id="name-warning" class="text-xs text-rose-500 mt-1 hidden"></div>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Product Code (PCODE) <span class="text-rose-500">*</span></label>
                    <div class="flex">
                        <input type="text" name="pcode" id="pcode" required maxlength="50" placeholder="Product Code" value="{{ $nextCode }}"
                               class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-l-xl px-4 py-3 text-sm text-slate-700 dark:text-slate-300 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 uppercase font-mono font-bold tracking-wider transition-all">
                        <button type="button" onclick="generateBarcode()" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 rounded-r-xl text-xs font-bold transition-all">Generate</button>
                    </div>
                    <div id="code-warning" class="text-xs text-rose-500 mt-1 hidden"></div>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Product Description <span class="text-rose-500">*</span></label>
                    <input type="text" name="productdes" required placeholder="Enter broad specifications and packaging layout details..." 
                           class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl px-4 py-3 text-sm text-slate-700 dark:text-slate-300 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 transition-all">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Unit <span class="text-rose-500">*</span></label>
                    <select name="unit" required 
                            class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl px-4 py-3 text-sm text-slate-700 dark:text-slate-300 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 cursor-pointer transition-all">
                        <option value="">~ Choose Base Unit ~</option>
                        <option value="1">Number (PCS)</option>
                        <option value="2">Meter (MTR)</option>
                        <option value="3">Packet (PKT)</option>
                        <option value="4">Liter (LTR)</option>
                    </select>
                </div>
                <div class="md:col-span-3">
                    <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">HSN/SAC Code <span class="text-rose-500">*</span></label>
                    <input type="text" name="hsnsac" required placeholder="GST classification code (HSN/SAC)" 
                           class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl px-4 py-3 text-sm text-slate-700 dark:text-slate-300 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 transition-all">
                </div>
            </div>
        </div>

        <!-- SECTION 2: Category & Branding -->
        <div class="p-8 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xl space-y-6" x-data="{ open: true }">
            <div class="flex justify-between items-center cursor-pointer border-b border-slate-200 dark:border-slate-800 pb-3" @click="open = !open">
                <h3 class="text-lg font-bold text-slate-800 dark:text-slate-100 flex items-center gap-2">
                    <i class="fa-solid fa-folder-open text-indigo-500"></i>
                    <span>2. Branding & Category Linkages</span>
                </h3>
                <i class="fa-solid" :class="open ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
            </div>

            <div x-show="open" x-transition class="grid grid-cols-1 md:grid-cols-3 gap-5">
                <div>
                    <div class="flex justify-between items-center mb-2">
                        <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Category <span class="text-rose-500">*</span></label>
                        <button type="button" @click="showAddCategory = true" class="text-indigo-500 text-xs font-bold">+ Add Category</button>
                    </div>
                    <select name="catid" id="catid_select" x-model="selectedCat" required class="select2-select w-full">
                        <option value="">~ Select Category ~</option>
                        <template x-for="cat in categories" :key="cat.cat_id">
                            <option :value="cat.cat_id" x-text="cat.cat_name"></option>
                        </template>
                    </select>
                </div>
                <div>
                    <div class="flex justify-between items-center mb-2">
                        <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Sub Category <span class="text-rose-500">*</span></label>
                        <button type="button" @click="showAddSubcategory = true" class="text-indigo-500 text-xs font-bold" :disabled="!selectedCat" :class="!selectedCat ? 'opacity-50 cursor-not-allowed' : ''">+ Add Subcategory</button>
                    </div>
                    <select name="subcatid" id="subcatid_select" x-model="selectedSubcat" :disabled="!selectedCat" required class="select2-select w-full">
                        <option value="">~ Select Subcategory ~</option>
                        <template x-for="sub in subcategories.filter(s => s.catid == selectedCat)" :key="sub.id">
                            <option :value="sub.id" x-text="sub.subcategoryname"></option>
                        </template>
                    </select>
                </div>
                <div>
                    <div class="flex justify-between items-center mb-2">
                        <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Brand <span class="text-rose-500">*</span></label>
                        <button type="button" @click="showAddBrand = true" class="text-indigo-500 text-xs font-bold" :disabled="!selectedSubcat" :class="!selectedSubcat ? 'opacity-50 cursor-not-allowed' : ''">+ Add Brand</button>
                    </div>
                    <select name="brandid" id="brandid_select" :disabled="!selectedSubcat" required class="select2-select w-full">
                        <option value="">~ Select Brand ~</option>
                        <template x-for="b in brands.filter(br => br.catid == selectedCat && br.scatid == selectedSubcat)" :key="b.brand_id">
                            <option :value="b.brand_id" x-text="b.brand_name"></option>
                        </template>
                    </select>
                </div>
            </div>
        </div>

        <!-- SECTION 3: Stock & Tiers -->
        <div class="p-8 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xl space-y-6" x-data="{ open: false }">
            <div class="flex justify-between items-center cursor-pointer border-b border-slate-200 dark:border-slate-800 pb-3" @click="open = !open">
                <h3 class="text-lg font-bold text-slate-800 dark:text-slate-100 flex items-center gap-2">
                    <i class="fa-solid fa-coins text-indigo-500"></i>
                    <span>3. Stock Valuation & Pricing Tiers</span>
                </h3>
                <i class="fa-solid" :class="open ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
            </div>

            <div x-show="open" x-transition class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Opening Quantity <span class="text-rose-500">*</span></label>
                        <input type="number" name="tqty" required placeholder="0" value="0"
                               class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl px-4 py-3 text-sm text-slate-700 dark:text-slate-300 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 font-bold transition-all">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Packaging Capacity (Per Pack) <span class="text-rose-500">*</span></label>
                        <input type="number" name="pqty" required placeholder="1" value="1"
                               class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl px-4 py-3 text-sm text-slate-700 dark:text-slate-300 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 font-bold transition-all">
                    </div>
                    <div>
                        <div class="flex justify-between items-center mb-2">
                            <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Vendor / Procurement Source <span class="text-rose-500">*</span></label>
                            <button type="button" @click="showAddSupplier = true" class="text-indigo-500 text-xs font-bold">+ Add Supplier</button>
                        </div>
                        <select name="pfrom" id="supplier_select" required class="select2-select w-full">
                            <option value="">~ Select Supplier ~</option>
                            @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->name }}">{{ $supplier->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-4 gap-5 pt-3 border-t border-slate-100 dark:border-slate-800">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Procurement Cost <span class="text-rose-500">*</span></label>
                        <input type="number" step="0.01" name="prate" required placeholder="0.00" 
                               class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl px-4 py-3 text-sm text-slate-700 dark:text-slate-300 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 font-bold text-indigo-600 dark:text-indigo-400 transition-all">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Shipping Charge <span class="text-rose-500">*</span></label>
                        <input type="number" step="0.01" name="srate" required placeholder="0.00" 
                               class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl px-4 py-3 text-sm text-slate-700 dark:text-slate-300 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 font-bold transition-all">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">MRP <span class="text-rose-500">*</span></label>
                        <input type="number" step="0.01" name="mrp" required placeholder="0.00" 
                               class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl px-4 py-3 text-sm text-slate-700 dark:text-slate-300 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 font-bold transition-all">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">GST% <span class="text-rose-500">*</span></label>
                        <select name="gst" required class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl px-4 py-3 text-sm text-slate-700 dark:text-slate-300 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 cursor-pointer transition-all">
                            <option value="0">GST 0%</option>
                            <option value="5">GST 5%</option>
                            <option value="12">GST 12%</option>
                            <option value="18" selected>GST 18%</option>
                            <option value="28">GST 28%</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-5 pt-3 border-t border-slate-100 dark:border-slate-800">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Customer Rate <span class="text-rose-500">*</span></label>
                        <input type="number" step="0.01" name="cprice" required placeholder="0.00" 
                               class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl px-4 py-3 text-sm text-slate-700 dark:text-slate-300 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 font-bold text-emerald-600 dark:text-emerald-400 transition-all">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Dealer Rate <span class="text-rose-500">*</span></label>
                        <input type="number" step="0.01" name="dprice" required placeholder="0.00" 
                               class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl px-4 py-3 text-sm text-slate-700 dark:text-slate-300 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 font-bold text-indigo-650 dark:text-indigo-400 transition-all">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Super Dealer Rate <span class="text-rose-500">*</span></label>
                        <input type="number" step="0.01" name="sdprice" required placeholder="0.00" 
                               class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl px-4 py-3 text-sm text-slate-700 dark:text-slate-300 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 font-bold text-purple-650 dark:text-purple-450 transition-all">
                    </div>
                </div>
            </div>
        </div>

        <!-- SECTION 4: Product Media -->
        <div class="p-8 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xl space-y-6" x-data="{ open: false }">
            <div class="flex justify-between items-center cursor-pointer border-b border-slate-200 dark:border-slate-800 pb-3" @click="open = !open">
                <h3 class="text-lg font-bold text-slate-800 dark:text-slate-100 flex items-center gap-2">
                    <i class="fa-solid fa-images text-indigo-500"></i>
                    <span>4. Product Showcase Media</span>
                </h3>
                <i class="fa-solid" :class="open ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
            </div>

            <div x-show="open" x-transition class="grid grid-cols-1 md:grid-cols-3 gap-5">
                <div class="p-6 rounded-2xl bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 text-center space-y-3">
                    <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Product Showcase Image 1</label>
                    <input type="file" name="productimagef" 
                           class="block w-full text-xs text-slate-500 dark:text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-[10px] file:font-bold file:bg-indigo-500/10 file:text-indigo-600 dark:file:text-indigo-400 hover:file:bg-indigo-500/20 cursor-pointer">
                    <p class="text-[9px] text-slate-400 dark:text-slate-500 font-medium">GIF, JPG, JPEG, PNG (Max Size: 250KB)</p>
                </div>
                <div class="p-6 rounded-2xl bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 text-center space-y-3">
                    <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Product Showcase Image 2</label>
                    <input type="file" name="productimages" 
                           class="block w-full text-xs text-slate-500 dark:text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-[10px] file:font-bold file:bg-indigo-500/10 file:text-indigo-600 dark:file:text-indigo-400 hover:file:bg-indigo-500/20 cursor-pointer">
                    <p class="text-[9px] text-slate-400 dark:text-slate-500 font-medium">GIF, JPG, JPEG, PNG (Max Size: 250KB)</p>
                </div>
                <div class="p-6 rounded-2xl bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 text-center space-y-3">
                    <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Product Showcase Image 3</label>
                    <input type="file" name="productimaget" 
                           class="block w-full text-xs text-slate-500 dark:text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-[10px] file:font-bold file:bg-indigo-500/10 file:text-indigo-600 dark:file:text-indigo-400 hover:file:bg-indigo-500/20 cursor-pointer">
                    <p class="text-[9px] text-slate-400 dark:text-slate-500 font-medium">GIF, JPG, JPEG, PNG (Max Size: 250KB)</p>
                </div>
            </div>
        </div>

        <!-- SECTION 5: Warranty Settings -->
        <div class="p-8 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xl space-y-6" x-data="{ open: false }">
            <div class="flex justify-between items-center cursor-pointer border-b border-slate-200 dark:border-slate-800 pb-3" @click="open = !open">
                <h3 class="text-lg font-bold text-slate-800 dark:text-slate-100 flex items-center gap-2">
                    <i class="fa-solid fa-gears text-indigo-500"></i>
                    <span>5. Advanced & Warranty Settings</span>
                </h3>
                <i class="fa-solid" :class="open ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
            </div>

            <div x-show="open" x-transition class="grid grid-cols-1 gap-5">
                <div>
                    <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Warranty Period (Months)</label>
                    <input type="number" name="warranty_months" placeholder="0 for no warranty" value="0"
                           class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl px-4 py-3 text-sm text-slate-700 dark:text-slate-300 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 transition-all">
                </div>
            </div>
        </div>

        <!-- Form Submit Footer -->
        <div class="p-6 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xl flex justify-end gap-3">
            <button type="button" @click="saveDraft()" class="px-6 py-4 rounded-xl border border-slate-200 dark:border-slate-800 text-slate-700 dark:text-slate-300 uppercase text-xs font-bold tracking-wider hover:bg-slate-100 dark:hover:bg-slate-800 transition-all">
                Save Draft
            </button>
            <x-admin.button type="submit" variant="primary" icon="fa-solid fa-circle-check text-lg" class="px-8 py-4 shadow-lg shadow-indigo-500/10 uppercase text-xs font-bold tracking-wider">
                Save Product Details
            </x-admin.button>
        </div>
    </form>

    <!-- ==========================================
         MODALS FOR QUICK CREATION
         ========================================== -->

    <!-- 1. Category Modal -->
    <x-admin.modal id="showAddCategory" title="Create Category" icon="fa-solid fa-plus-circle">
        <form id="ajaxCategoryForm" onsubmit="submitAjaxForm(event, '{{ route('admin.categories.store') }}', 'catid_select', 'showAddCategory')">
            <div class="mb-4">
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Category Name</label>
                <input type="text" name="cat_name" required class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-sm text-slate-300 focus:outline-none focus:ring-1 focus:ring-indigo-500">
            </div>
            <div class="flex gap-4 pt-4 border-t border-slate-800">
                <button type="button" @click="showAddCategory = false" class="flex-1 py-3 px-4 rounded-xl border border-slate-850 hover:bg-slate-800/50 text-slate-300 font-semibold text-sm transition-all">Cancel</button>
                <button type="submit" class="flex-1 py-3 px-4 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-sm shadow-xl shadow-indigo-600/10 transition-all">Save Category</button>
            </div>
        </form>
    </x-admin.modal>

    <!-- 2. Subcategory Modal -->
    <x-admin.modal id="showAddSubcategory" title="Create Subcategory" icon="fa-solid fa-plus-circle">
        <form id="ajaxSubcategoryForm" onsubmit="submitAjaxSubcategory(event)">
            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Category</label>
                    <select name="catid" id="modal_subcat_cat_select" required class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-sm text-slate-300 focus:outline-none focus:ring-1 focus:ring-indigo-500"></select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Subcategory Name</label>
                    <input type="text" name="subcategoryname" required class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-sm text-slate-300 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                </div>
            </div>
            <div class="flex gap-4 pt-4 border-t border-slate-800 mt-4">
                <button type="button" @click="showAddSubcategory = false" class="flex-1 py-3 px-4 rounded-xl border border-slate-850 hover:bg-slate-800/50 text-slate-300 font-semibold text-sm transition-all">Cancel</button>
                <button type="submit" class="flex-1 py-3 px-4 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-sm shadow-xl shadow-indigo-600/10 transition-all">Save Subcategory</button>
            </div>
        </form>
    </x-admin.modal>

    <!-- 3. Brand Modal -->
    <x-admin.modal id="showAddBrand" title="Create Brand" icon="fa-solid fa-plus-circle">
        <form id="ajaxBrandForm" onsubmit="submitAjaxBrand(event)">
            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Category</label>
                    <select id="modal_brand_cat_select" required class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-sm text-slate-300 focus:outline-none focus:ring-1 focus:ring-indigo-500" onchange="modalLoadSubcategories()"></select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Subcategory</label>
                    <select name="scatid" id="modal_brand_subcat_select" required class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-sm text-slate-300 focus:outline-none focus:ring-1 focus:ring-indigo-500" disabled>
                        <option value="">~ Select Subcategory ~</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Brand Name</label>
                    <input type="text" name="brand_name" required class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-sm text-slate-300 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                </div>
            </div>
            <div class="flex gap-4 pt-4 border-t border-slate-800 mt-4">
                <button type="button" @click="showAddBrand = false" class="flex-1 py-3 px-4 rounded-xl border border-slate-850 hover:bg-slate-800/50 text-slate-300 font-semibold text-sm transition-all">Cancel</button>
                <button type="submit" class="flex-1 py-3 px-4 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-sm shadow-xl shadow-indigo-600/10 transition-all">Save Brand</button>
            </div>
        </form>
    </x-admin.modal>

    <!-- 4. Supplier Modal -->
    <x-admin.modal id="showAddSupplier" title="Create Supplier" icon="fa-solid fa-plus-circle">
        <form id="ajaxSupplierForm" onsubmit="submitAjaxForm(event, '{{ route('admin.suppliers.store') }}', 'supplier_select', 'showAddSupplier', 'supplier')">
            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Supplier / Company Name</label>
                    <input type="text" name="name" required class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-sm text-slate-300 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Contact Person</label>
                    <input type="text" name="contact_person" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-sm text-slate-300 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Phone</label>
                        <input type="text" name="phone" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-sm text-slate-300 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Email</label>
                        <input type="email" name="email" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-sm text-slate-300 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Address</label>
                    <textarea name="address" rows="2" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-sm text-slate-300 focus:outline-none focus:ring-1 focus:ring-indigo-500"></textarea>
                </div>
            </div>
            <div class="flex gap-4 pt-4 border-t border-slate-800 mt-4">
                <button type="button" @click="showAddSupplier = false" class="flex-1 py-3 px-4 rounded-xl border border-slate-850 hover:bg-slate-800/50 text-slate-300 font-semibold text-sm transition-all">Cancel</button>
                <button type="submit" class="flex-1 py-3 px-4 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-sm shadow-xl shadow-indigo-600/10 transition-all">Save Supplier</button>
            </div>
        </form>
    </x-admin.modal>

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

        // Sync dropdown selectors with Alpine state when select2 changes value
        $('#catid_select').on('change', function(e) {
            // Trigger native change so Alpine x-model updates
            if (e.originalEvent) return;
            this.dispatchEvent(new Event('change'));

            // Reset dependent subcategory and brand select2s
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

        // Real-time duplicate validation calls
        let debounceTimer;
        $('#productname').on('input', function() {
            clearTimeout(debounceTimer);
            const val = $(this).val();
            if (val.length < 3) {
                $('#name-warning').addClass('hidden');
                return;
            }
            debounceTimer = setTimeout(() => {
                checkDuplicateField('productname', val, '#name-warning', 'Product name already exists!');
            }, 500);
        });

        $('#pcode').on('input', function() {
            clearTimeout(debounceTimer);
            const val = $(this).val();
            if (!val) {
                $('#code-warning').addClass('hidden');
                return;
            }
            debounceTimer = setTimeout(() => {
                checkDuplicateField('pcode', val, '#code-warning', 'Product code already exists!');
            }, 500);
        });

        // Setup options lists in subcategory/brand creation modals
        setupModalOptions();
    });

    function setupModalOptions() {
        const modalSubcatSelect = $('#modal_subcat_cat_select');
        const modalBrandSelect = $('#modal_brand_cat_select');

        modalSubcatSelect.html('<option value="">~ Select Category ~</option>');
        modalBrandSelect.html('<option value="">~ Select Category ~</option>');

        const categories = @js($categories);
        categories.forEach(cat => {
            modalSubcatSelect.append(`<option value="${cat.cat_id}">${cat.cat_name}</option>`);
            modalBrandSelect.append(`<option value="${cat.cat_id}">${cat.cat_name}</option>`);
        });
    }

    function modalLoadSubcategories() {
        const catId = $('#modal_brand_cat_select').val();
        const subSelect = $('#modal_brand_subcat_select');
        subSelect.html('<option value="">~ Select Subcategory ~</option>');

        if (!catId) {
            subSelect.prop('disabled', true);
            return;
        }

        const subcategories = @js($subcategories);
        const filtered = subcategories.filter(sub => sub.catid == catId);
        filtered.forEach(sub => {
            subSelect.append(`<option value="${sub.id}">${sub.subcategoryname}</option>`);
        });

        subSelect.prop('disabled', false);
    }

    // Duplicate Check AJAX
    function checkDuplicateField(field, value, warningSelector, message) {
        $.ajax({
            url: '{{ route("admin.products.check_duplicate") }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                field: field,
                value: value
            },
            success: function(response) {
                let html = '';
                
                // If there are similar matches, list up to 3
                if (response.matches && response.matches.length > 0) {
                    html += `<div class="mt-2 p-3 bg-slate-100 dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300">`;
                    html += `<div class="font-bold text-xs uppercase tracking-wider mb-1"><i class="fa-solid fa-list-check mr-1 text-indigo-500"></i> Matching Products (Max 3):</div><ul class="list-disc pl-4 space-y-1">`;
                    response.matches.forEach(p => {
                        html += `<li class="text-xs"><strong>${p.productname}</strong> (${p.pcode})</li>`;
                    });
                    html += `</ul></div>`;
                }

                // If exact match exists, block saving
                if (response.exists) {
                    html += `<div class="mt-2 p-3 bg-rose-500/10 border border-rose-500/25 text-rose-600 dark:text-rose-400 rounded-xl font-bold text-xs flex items-center gap-2"><i class="fa-solid fa-ban"></i> Exact Match Blocked: This product is already registered!</div>`;
                    // Block form save buttons
                    $('button[type="submit"]').prop('disabled', true).addClass('opacity-50 cursor-not-allowed');
                } else {
                    // Unblock form save buttons if no other field is blocked
                    if ($('.bg-rose-500\\/10').length === 0) {
                        $('button[type="submit"]').prop('disabled', false).removeClass('opacity-50 cursor-not-allowed');
                    }
                }

                if (html) {
                    $(warningSelector).html(html).removeClass('hidden');
                } else {
                    $(warningSelector).addClass('hidden');
                }
            }
        });
    }

    // AJAX Form submissions for inline models
    function submitAjaxForm(event, url, targetSelectId, modalVarName, customObjectKey = null) {
        event.preventDefault();
        const form = $(event.target);
        const data = form.serialize();

        $.ajax({
            url: url,
            type: 'POST',
            data: data + `&_token={{ csrf_token() }}`,
            success: function(response) {
                if (response.status === 'success') {
                    let id, text;
                    if (customObjectKey) {
                        const obj = response[customObjectKey];
                        id = obj.id;
                        text = obj.name || obj.brand_name || obj.subcategoryname;
                    } else if (response.category) {
                        id = response.category.cat_id;
                        text = response.category.cat_name;
                    }

                    const select = $(`#${targetSelectId}`);
                    select.append(`<option value="${id}">${text}</option>`);
                    select.val(id).trigger('change');

                    // Close Modal using window custom event triggers (bound to parent Alpine scope)
                    if (modalVarName === 'showAddCategory') {
                        window.dispatchEvent(new CustomEvent('close-category-modal'));
                        
                        // Push new category into modal lists dynamically
                        const categories = @js($categories);
                        categories.push({ cat_id: id, cat_name: text });
                        setupModalOptions();
                    } else if (modalVarName === 'showAddSupplier') {
                        window.dispatchEvent(new CustomEvent('close-supplier-modal'));
                    }

                    form[0].reset();
                }
            },
            error: function(err) {
                alert('Validation failed: Name might already be registered!');
            }
        });
    }

    function submitAjaxSubcategory(event) {
        event.preventDefault();
        const form = $(event.target);
        $.ajax({
            url: '{{ route("admin.subcategories.store") }}',
            type: 'POST',
            data: form.serialize() + `&_token={{ csrf_token() }}`,
            success: function(response) {
                alert('Subcategory added successfully! Page will refresh to update datasets.');
                window.dispatchEvent(new CustomEvent('close-subcategory-modal'));
                form[0].reset();
                window.location.reload();
            }
        });
    }

    function submitAjaxBrand(event) {
        event.preventDefault();
        const form = $(event.target);
        $.ajax({
            url: '{{ route("admin.brands.store") }}',
            type: 'POST',
            data: form.serialize() + `&_token={{ csrf_token() }}`,
            success: function(response) {
                alert('Brand added successfully! Page will refresh to update datasets.');
                window.dispatchEvent(new CustomEvent('close-brand-modal'));
                form[0].reset();
                window.location.reload();
            }
        });
    }

    // Barcode Generator helper
    function generateBarcode() {
        const rand = '890' + Math.floor(100000000 + Math.random() * 900000000);
        $('#pcode').val(rand).trigger('input');
    }
</script>
@endsection
