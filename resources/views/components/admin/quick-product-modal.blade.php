@php
    $categories = \App\Models\Category::where('status', 1)->orderBy('cat_name', 'asc')->get();
    $subcategories = \App\Models\Subcategory::where('status', 1)->orderBy('subcategoryname', 'asc')->get();
    $brands = \App\Models\Brand::where('brand_status', 1)->orderBy('brand_name', 'asc')->get();
@endphp

<div x-data="{
    isOpen: false,
    targetIndex: null,
    
    // Form fields
    productname: '',
    pcode: '',
    catid: '',
    subcatid: '',
    brandid: '',
    unit: '',
    pqty: 1,
    prate: 0,
    srate: 0,
    mrp: 0,
    gst: 0,
    cprice: 0,
    dprice: 0,
    sdprice: 0,
    hsnsac: '',

    isSubmitting: false,
    errorMessage: '',

    init() {
        // Listen for open event
        window.addEventListener('open-quick-product-modal', (e) => {
            this.targetIndex = e.detail.index;
            this.resetForm();
            this.isOpen = true;
        });
    },

    resetForm() {
        this.productname = '';
        this.pcode = '';
        this.catid = '';
        this.subcatid = '';
        this.brandid = '';
        this.unit = '';
        this.pqty = 1;
        this.prate = 0;
        this.srate = 0;
        this.mrp = 0;
        this.gst = 0;
        this.cprice = 0;
        this.dprice = 0;
        this.sdprice = 0;
        this.hsnsac = '';
        this.errorMessage = '';
        this.isSubmitting = false;
    },

    submitForm() {
        if (this.isSubmitting) return;
        this.isSubmitting = true;
        this.errorMessage = '';

        fetch('{{ route('admin.products.quick-store') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                productname: this.productname,
                pcode: this.pcode,
                catid: this.catid,
                subcatid: this.subcatid,
                brandid: this.brandid,
                unit: this.unit,
                pqty: this.pqty,
                prate: this.prate,
                srate: this.srate,
                mrp: this.mrp,
                gst: this.gst,
                cprice: this.cprice,
                dprice: this.dprice,
                sdprice: this.sdprice,
                hsnsac: this.hsnsac
            })
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(err => { throw err; });
            }
            return response.json();
        })
        .then(res => {
            if (res.status === 'success') {
                // Dispatch event so parent layout/alpine builder can pick it up
                window.dispatchEvent(new CustomEvent('quick-product-created', {
                    detail: {
                        product: res.product,
                        index: this.targetIndex
                    }
                }));
                this.isOpen = false;
                this.resetForm();
            } else {
                this.errorMessage = res.message || 'Validation failed';
                this.isSubmitting = false;
            }
        })
        .catch(err => {
            console.error(err);
            this.errorMessage = err.message || 'Something went wrong. Please check your inputs.';
            this.isSubmitting = false;
        });
    }
}" x-show="isOpen" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;" x-cloak>
    <!-- Backdrop -->
    <div class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm transition-opacity" @click="isOpen = false"></div>

    <div class="flex min-h-full items-center justify-center p-4">
        <div class="relative w-full max-w-2xl transform overflow-hidden rounded-3xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-6 text-left shadow-2xl transition-all space-y-6"
             @click.outside="isOpen = false"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
            
            <!-- Modal Header -->
            <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-800 pb-4">
                <h3 class="text-lg font-bold text-slate-800 dark:text-white flex items-center gap-2">
                    <i class="fa-solid fa-folder-plus text-indigo-500 dark:text-indigo-400"></i>
                    <span>Quick Add New Product</span>
                </h3>
                <button type="button" @click="isOpen = false" class="text-slate-400 hover:text-slate-900 dark:hover:text-white transition-all">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <!-- Error message alert -->
            <div x-show="errorMessage" class="p-3 bg-rose-500/10 border border-rose-500/20 text-rose-450 rounded-xl text-xs" x-cloak>
                <i class="fa-solid fa-circle-exclamation me-1.5"></i>
                <span x-text="errorMessage"></span>
            </div>

            <!-- Modal Form -->
            <form @submit.prevent="submitForm()" class="space-y-4">
                <!-- Group 1: General Product Details -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <label class="block text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Product Name *</label>
                        <input type="text" x-model="productname" required placeholder="ENTER PRODUCT NAME"
                               class="w-full px-4 py-2.5 bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-slate-800 dark:text-slate-200 placeholder-slate-400 dark:placeholder-slate-650 focus:outline-none focus:border-indigo-500 text-sm uppercase">
                    </div>
                    <div class="space-y-1.5">
                        <label class="block text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Product Code / Barcode *</label>
                        <input type="text" x-model="pcode" required placeholder="UNIQUE BARCODE OR CODE"
                               class="w-full px-4 py-2.5 bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-slate-800 dark:text-slate-200 placeholder-slate-400 dark:placeholder-slate-650 focus:outline-none focus:border-indigo-500 text-sm font-mono uppercase">
                    </div>
                </div>

                <!-- Group 2: Categorization & Brand -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="md:col-span-2">
                        <x-admin.category-tree-select 
                            @category-tree-changed="catid = $event.detail.catId; subcatid = $event.detail.subcatId"
                        />
                    </div>
                    <div class="space-y-1.5">
                        <label class="block text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Brand *</label>
                        <select x-model="brandid" required class="w-full px-3 py-2.5 bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-slate-800 dark:text-slate-200 focus:outline-none focus:border-indigo-500 text-sm">
                            <option value="">-- Choose Brand --</option>
                            @foreach($brands as $b)
                                <option value="{{ $b->brand_id }}">{{ $b->brand_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Group 3: Units & Packing -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="space-y-1.5">
                        <label class="block text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Base Unit *</label>
                        <select x-model="unit" required class="w-full px-3 py-2.5 bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-slate-800 dark:text-slate-200 focus:outline-none focus:border-indigo-500 text-sm">
                            <option value="">-- Choose Base Unit --</option>
                            <option value="1">Number (PCS)</option>
                            <option value="2">Meter (MTR)</option>
                            <option value="3">Packet (PKT)</option>
                            <option value="4">Liter (LTR)</option>
                        </select>
                    </div>
                    <div class="space-y-1.5">
                        <label class="block text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Multiplier (Pqty) *</label>
                        <input type="number" x-model.number="pqty" min="1" required class="w-full px-4 py-2 bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-slate-800 dark:text-slate-200 focus:outline-none focus:border-indigo-500 text-sm">
                    </div>
                    <div class="space-y-1.5">
                        <label class="block text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">HSN/SAC Code (Optional)</label>
                        <input type="text" x-model="hsnsac" placeholder="GST HSN/SAC CODE" class="w-full px-4 py-2 bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-slate-800 dark:text-slate-200 focus:outline-none focus:border-indigo-500 text-sm uppercase">
                    </div>
                </div>

                <!-- Group 4: Rates & Prices -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 p-4 bg-slate-50 dark:bg-slate-950/40 border border-slate-200 dark:border-slate-850 rounded-2xl">
                    <div class="space-y-1">
                        <label class="block text-[9px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Purchase Rate *</label>
                        <input type="number" x-model.number="prate" step="0.01" min="0" required class="w-full px-3 py-1.5 bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-slate-800 dark:text-slate-200 focus:outline-none focus:border-indigo-500 text-xs">
                    </div>
                    <div class="space-y-1">
                        <label class="block text-[9px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Sale Rate *</label>
                        <input type="number" x-model.number="srate" step="0.01" min="0" required class="w-full px-3 py-1.5 bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-slate-800 dark:text-slate-200 focus:outline-none focus:border-indigo-500 text-xs">
                    </div>
                    <div class="space-y-1">
                        <label class="block text-[9px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">MRP *</label>
                        <input type="number" x-model.number="mrp" step="0.01" min="0" required class="w-full px-3 py-1.5 bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-slate-800 dark:text-slate-200 focus:outline-none focus:border-indigo-500 text-xs">
                    </div>
                    <div class="space-y-1">
                        <label class="block text-[9px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">GST (%) *</label>
                        <input type="number" x-model.number="gst" step="0.1" min="0" required class="w-full px-3 py-1.5 bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-slate-800 dark:text-slate-200 focus:outline-none focus:border-indigo-500 text-xs">
                    </div>
                    <div class="space-y-1">
                        <label class="block text-[9px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Cust. Price *</label>
                        <input type="number" x-model.number="cprice" step="0.01" min="0" required class="w-full px-3 py-1.5 bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-slate-800 dark:text-slate-200 focus:outline-none focus:border-indigo-500 text-xs">
                    </div>
                    <div class="space-y-1">
                        <label class="block text-[9px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Dealer Price *</label>
                        <input type="number" x-model.number="dprice" step="0.01" min="0" required class="w-full px-3 py-1.5 bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-slate-800 dark:text-slate-200 focus:outline-none focus:border-indigo-500 text-xs">
                    </div>
                    <div class="space-y-1">
                        <label class="block text-[9px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Super Dealer *</label>
                        <input type="number" x-model.number="sdprice" step="0.01" min="0" required class="w-full px-3 py-1.5 bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-slate-800 dark:text-slate-200 focus:outline-none focus:border-indigo-500 text-xs">
                    </div>
                </div>

                <!-- Footer / Action Buttons -->
                <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-250 dark:border-slate-800">
                    <button type="button" @click="isOpen = false" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-750 dark:text-slate-300 font-bold text-xs uppercase tracking-wider rounded-xl transition border border-slate-200 dark:border-transparent">Cancel</button>
                    <button type="submit" :disabled="isSubmitting" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs uppercase tracking-wider rounded-xl transition shadow-lg shadow-indigo-600/20 flex items-center gap-1.5">
                        <span x-show="!isSubmitting">Save Product</span>
                        <span x-show="isSubmitting"><i class="fa-solid fa-spinner fa-spin"></i> Saving...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
