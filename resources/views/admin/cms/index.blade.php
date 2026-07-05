@extends('layouts.admin')

@section('content')
<!-- CropperJS CSS & JS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.2/cropper.min.css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.2/cropper.min.js"></script>

<div class="container mx-auto px-4 py-8" x-data="{ activeTab: 'banners' }">
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-8">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white uppercase font-outfit">Homepage CMS Panel</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Manage and configure storefront slides, categories, and dynamic content layouts.</p>
        </div>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
    <div class="mb-6 p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-600 dark:text-emerald-400 text-sm font-semibold flex items-center gap-3">
        <i class="fa-solid fa-circle-check text-base"></i>
        <span>{{ session('success') }}</span>
    </div>
    @endif

    @if(session('error'))
    <div class="mb-6 p-4 rounded-xl bg-rose-500/10 border border-rose-500/20 text-rose-600 dark:text-rose-400 text-sm font-semibold flex items-center gap-3">
        <i class="fa-solid fa-triangle-exclamation text-base"></i>
        <span>{{ session('error') }}</span>
    </div>
    @endif

    @if ($errors->any())
    <div class="mb-6 p-4 rounded-xl bg-rose-500/10 border border-rose-500/20 text-rose-600 dark:text-rose-400 text-sm">
        <ul class="list-disc list-inside space-y-1 font-semibold">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <!-- Navigation Tabs -->
    <div class="flex items-center gap-2 overflow-x-auto border-b border-slate-200 dark:border-slate-800 pb-px mb-8">
        <button @click="activeTab = 'banners'" 
                :class="activeTab === 'banners' ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-slate-500 hover:text-slate-700 dark:hover:text-slate-300'"
                class="px-4 py-3 border-b-2 font-semibold text-sm transition-all whitespace-nowrap">
            <i class="fa-solid fa-images mr-2"></i>Banner Slider
        </button>
        <button @click="activeTab = 'categories'" 
                :class="activeTab === 'categories' ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-slate-500 hover:text-slate-700 dark:hover:text-slate-300'"
                class="px-4 py-3 border-b-2 font-semibold text-sm transition-all whitespace-nowrap">
            <i class="fa-solid fa-list mr-2"></i>Featured Categories
        </button>
        <button @click="activeTab = 'deal_of_the_day'" 
                :class="activeTab === 'deal_of_the_day' ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-slate-500 hover:text-slate-700 dark:hover:text-slate-300'"
                class="px-4 py-3 border-b-2 font-semibold text-sm transition-all whitespace-nowrap">
            <i class="fa-solid fa-tag mr-2"></i>Deal of the Day
        </button>
        <button @click="activeTab = 'promo_banner'" 
                :class="activeTab === 'promo_banner' ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-slate-500 hover:text-slate-700 dark:hover:text-slate-300'"
                class="px-4 py-3 border-b-2 font-semibold text-sm transition-all whitespace-nowrap">
            <i class="fa-solid fa-rectangle-ad mr-2"></i>Promo Banner
        </button>
        <button @click="activeTab = 'trust_badges'" 
                :class="activeTab === 'trust_badges' ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-slate-500 hover:text-slate-700 dark:hover:text-slate-300'"
                class="px-4 py-3 border-b-2 font-semibold text-sm transition-all whitespace-nowrap">
            <i class="fa-solid fa-shield-halved mr-2"></i>Trust Highlights
        </button>
        <button @click="activeTab = 'seo'" 
                :class="activeTab === 'seo' ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-slate-500 hover:text-slate-700 dark:hover:text-slate-300'"
                class="px-4 py-3 border-b-2 font-semibold text-sm transition-all whitespace-nowrap">
            <i class="fa-solid fa-globe mr-2"></i>SEO Settings
        </button>
    </div>

    <!-- ── TAB 1: BANNER SLIDER ── -->
    <div x-show="activeTab === 'banners'" class="space-y-8" x-transition>
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Create Banner Slide -->
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-880 rounded-2xl p-6 shadow-sm">
                <h3 class="text-lg font-bold text-slate-900 dark:text-white uppercase mb-4 font-outfit">Add New Banner Slide</h3>
                
                <form action="{{ route('admin.cms.banners.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4" id="banner-create-form">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1.5">Banner Image</label>
                        <div class="space-y-3">
                            <input type="file" id="create-image-input" name="image" required accept="image/*" class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 dark:file:bg-slate-800 dark:file:text-indigo-400" />
                            <div class="hidden" id="create-preview-wrapper">
                                <span class="text-[10px] uppercase font-bold text-slate-400 block mb-1">Cropped Preview:</span>
                                <img id="create-preview" class="w-full h-24 object-cover rounded-xl border border-slate-200 dark:border-slate-850" />
                            </div>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1.5">Title Text</label>
                        <input type="text" name="title" value="{{ old('title') }}" placeholder="e.g. Next-Gen Compute Solutions" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-880 dark:bg-slate-950 text-slate-800 dark:text-slate-100 text-sm focus:outline-none focus:border-indigo-500" />
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1.5">Subtitle/Description Text</label>
                        <textarea name="subtitle" rows="3" placeholder="Procure imported server arrays and high-frequency components." class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-880 dark:bg-slate-950 text-slate-800 dark:text-slate-100 text-sm focus:outline-none focus:border-indigo-500">{{ old('subtitle') }}</textarea>
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1.5">Badge Text</label>
                        <input type="text" name="badge_text" value="{{ old('badge_text') }}" placeholder="e.g. Exclusive Launch" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-880 dark:bg-slate-950 text-slate-800 dark:text-slate-100 text-sm focus:outline-none focus:border-indigo-500" />
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1.5">Link URL</label>
                        <input type="text" name="link_url" value="{{ old('link_url') }}" placeholder="e.g. #catalog" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-880 dark:bg-slate-950 text-slate-800 dark:text-slate-100 text-sm focus:outline-none focus:border-indigo-500" />
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1.5">Sort Order</label>
                            <input type="number" name="sort_order" value="{{ old('sort_order', 0) }}" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-880 dark:bg-slate-950 text-slate-800 dark:text-slate-100 text-sm focus:outline-none focus:border-indigo-500" />
                        </div>
                        <div class="flex items-center pt-6">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="is_active" value="1" checked class="rounded text-indigo-600 focus:ring-indigo-500" />
                                <span class="text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400">Is Active</span>
                            </label>
                        </div>
                    </div>
                    <button type="submit" class="w-full py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs uppercase tracking-widest transition-all">Create Banner</button>
                </form>
            </div>

            <!-- Existing Banners list -->
            <div class="lg:col-span-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-880 rounded-2xl p-6 shadow-sm">
                <h3 class="text-lg font-bold text-slate-900 dark:text-white uppercase mb-6 font-outfit">Active Banner Slides</h3>

                @if($banners->count() > 0)
                <div class="space-y-4">
                    @foreach($banners as $banner)
                    <div class="flex flex-col md:flex-row gap-4 p-4 border border-slate-150 dark:border-slate-800/80 rounded-2xl dark:bg-slate-950/20" x-data="{ editMode: false }">
                        <!-- Banner Preview -->
                        <div class="w-full md:w-48 h-28 rounded-xl overflow-hidden bg-slate-100 dark:bg-slate-800 shrink-0 relative border border-slate-200 dark:border-slate-850">
                            <img src="{{ asset($banner->image_path) }}" class="w-full h-full object-cover" />
                            @if($banner->badge_text)
                            <span class="absolute top-2 left-2 px-1.5 py-0.5 rounded bg-indigo-600 text-white text-[8px] font-black uppercase tracking-widest">{{ $banner->badge_text }}</span>
                            @endif
                        </div>

                        <!-- Info & Forms -->
                        <div class="flex-1 min-w-0 flex flex-col justify-between">
                            <!-- View mode -->
                            <div x-show="!editMode" class="space-y-1">
                                <h4 class="text-sm font-bold text-slate-800 dark:text-slate-100 capitalize">{{ $banner->title ?: 'No Title' }}</h4>
                                <p class="text-xs text-slate-500 dark:text-slate-400 line-clamp-2">{{ $banner->subtitle ?: 'No Subtitle' }}</p>
                                <div class="flex flex-wrap items-center gap-2 pt-2">
                                    <span class="px-2 py-0.5 text-[9px] font-bold uppercase tracking-wider rounded {{ $banner->is_active ? 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20' : 'bg-rose-500/10 text-rose-600 dark:text-rose-400 border border-rose-500/20' }}">
                                        {{ $banner->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                    <span class="px-2 py-0.5 text-[9px] font-bold uppercase tracking-wider rounded bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 border border-slate-200 dark:border-slate-700">
                                        Order: {{ $banner->sort_order }}
                                    </span>
                                    @if($banner->link_url)
                                    <span class="px-2 py-0.5 text-[9px] font-bold uppercase tracking-wider rounded bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 border border-indigo-500/20">
                                        Link: {{ $banner->link_url }}
                                    </span>
                                    @endif
                                </div>
                            </div>

                            <!-- Edit Form Mode -->
                            <div x-show="editMode" style="display: none;">
                                <form action="{{ route('admin.cms.banners.update', $banner->id) }}" method="POST" enctype="multipart/form-data" class="space-y-3" id="banner-edit-form-{{ $banner->id }}">
                                    @csrf
                                    @method('PUT')
                                    <div class="grid grid-cols-2 gap-3">
                                        <input type="text" name="title" value="{{ old('title', $banner->title) }}" placeholder="Title" class="px-3 py-1.5 rounded-lg border border-slate-200 dark:border-slate-850 dark:bg-slate-950 text-xs text-slate-800 dark:text-slate-100 focus:outline-none" />
                                        <input type="text" name="badge_text" value="{{ old('badge_text', $banner->badge_text) }}" placeholder="Badge" class="px-3 py-1.5 rounded-lg border border-slate-200 dark:border-slate-850 dark:bg-slate-950 text-xs text-slate-800 dark:text-slate-100 focus:outline-none" />
                                    </div>
                                    <textarea name="subtitle" rows="2" placeholder="Subtitle" class="w-full px-3 py-1.5 rounded-lg border border-slate-200 dark:border-slate-850 dark:bg-slate-950 text-xs text-slate-800 dark:text-slate-100 focus:outline-none">{{ old('subtitle', $banner->subtitle) }}</textarea>
                                    <div class="grid grid-cols-2 gap-3">
                                        <input type="text" name="link_url" value="{{ old('link_url', $banner->link_url) }}" placeholder="Link URL" class="px-3 py-1.5 rounded-lg border border-slate-200 dark:border-slate-850 dark:bg-slate-950 text-xs text-slate-800 dark:text-slate-100 focus:outline-none" />
                                        <input type="number" name="sort_order" value="{{ old('sort_order', $banner->sort_order) }}" placeholder="Sort Order" class="px-3 py-1.5 rounded-lg border border-slate-200 dark:border-slate-850 dark:bg-slate-950 text-xs text-slate-800 dark:text-slate-100 focus:outline-none" />
                                    </div>
                                    <div class="flex flex-col gap-2">
                                        <label class="block text-[10px] font-bold uppercase text-slate-500">Update Banner Image</label>
                                        <input type="file" id="edit-image-input-{{ $banner->id }}" data-banner-id="{{ $banner->id }}" name="image" accept="image/*" class="text-[10px] text-slate-500" />
                                        <div class="hidden" id="edit-preview-wrapper-{{ $banner->id }}">
                                            <span class="text-[9px] uppercase font-bold text-slate-400 block mb-0.5">Cropped Preview:</span>
                                            <img id="edit-preview-{{ $banner->id }}" class="w-full h-16 object-cover rounded-lg border border-slate-200 dark:border-slate-850" />
                                        </div>
                                    </div>
                                    <div class="flex items-center justify-between pt-2">
                                        <label class="flex items-center gap-1 cursor-pointer">
                                            <input type="checkbox" name="is_active" value="1" {{ $banner->is_active ? 'checked' : '' }} class="rounded text-indigo-600 focus:ring-indigo-500 scale-75" />
                                            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500">Active</span>
                                        </label>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <button type="submit" class="px-3 py-1.5 rounded-lg bg-indigo-600 text-white font-bold text-[10px] uppercase tracking-wider">Save</button>
                                        <button type="button" @click="editMode = false" class="px-3 py-1.5 rounded-lg bg-slate-200 dark:bg-slate-800 text-slate-600 dark:text-slate-400 font-bold text-[10px] uppercase tracking-wider">Cancel</button>
                                    </div>
                                </form>
                            </div>

                            <!-- Actions -->
                            <div x-show="!editMode" class="flex items-center gap-3 mt-4 md:mt-0 md:justify-end">
                                <button type="button" @click="editMode = true" class="flex items-center gap-1 text-[11px] font-bold uppercase tracking-wider text-indigo-600 dark:text-indigo-400 hover:underline">
                                    <i class="fa-solid fa-pen-to-square"></i>Edit
                                </button>
                                <form action="{{ route('admin.cms.banners.destroy', $banner->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this slide?')" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="flex items-center gap-1 text-[11px] font-bold uppercase tracking-wider text-rose-600 dark:text-rose-400 hover:underline">
                                        <i class="fa-solid fa-trash-can"></i>Delete
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="text-center py-12 text-slate-500">
                    <i class="fa-solid fa-images text-4xl opacity-20 mb-3 block"></i>
                    <span>No banner slides uploaded. The storefront will use fallback mock slides.</span>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- ── TAB 2: FEATURED CATEGORIES ── -->
    <div x-show="activeTab === 'categories'" class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 shadow-sm" x-transition style="display: none;">
        <div class="mb-6">
            <h3 class="text-lg font-bold text-slate-900 dark:text-white uppercase font-outfit">Featured Categories configuration</h3>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Select which categories show on the homepage circular scrolling row, configure their icons, and order them.</p>
        </div>

        <form action="{{ route('admin.cms.settings.update') }}" method="POST">
            @csrf
            <input type="hidden" name="settings_type" value="categories" />
            
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-slate-200 dark:border-slate-800">
                            <th class="py-3 px-4 text-xs font-bold uppercase tracking-wider text-slate-500">Featured</th>
                            <th class="py-3 px-4 text-xs font-bold uppercase tracking-wider text-slate-500">Category Name</th>
                            <th class="py-3 px-4 text-xs font-bold uppercase tracking-wider text-slate-500">FontAwesome Icon Class</th>
                            <th class="py-3 px-4 text-xs font-bold uppercase tracking-wider text-slate-500">Sort Order</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-850">
                        @foreach($categories as $cat)
                            @php
                                $cfg = collect($featuredCategories)->firstWhere('cat_id', $cat->cat_id);
                                $isFeatured = !empty($cfg);
                                $iconVal = $cfg['icon'] ?? 'fa-server';
                                $sortOrder = $cfg['sort_order'] ?? 0;
                            @endphp
                            <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-950/20">
                                <td class="py-4 px-4">
                                    <input type="checkbox" name="categories[{{ $cat->cat_id }}][featured]" value="1" {{ $isFeatured ? 'checked' : '' }} class="rounded text-indigo-600 focus:ring-indigo-500" />
                                </td>
                                <td class="py-4 px-4 text-sm font-bold text-slate-800 dark:text-slate-100 uppercase">{{ $cat->cat_name }}</td>
                                <td class="py-4 px-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-lg bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-[#0059e3] dark:text-indigo-400">
                                            <i class="fa-solid {{ $iconVal }} text-sm"></i>
                                        </div>
                                        <input type="text" name="categories[{{ $cat->cat_id }}][icon]" value="{{ $iconVal }}" placeholder="fa-server" class="px-3 py-1.5 rounded-lg border border-slate-200 dark:border-slate-800 dark:bg-slate-950 text-xs text-slate-800 dark:text-slate-100 focus:outline-none focus:border-indigo-500" />
                                    </div>
                                </td>
                                <td class="py-4 px-4">
                                    <input type="number" name="categories[{{ $cat->cat_id }}][sort_order]" value="{{ $sortOrder }}" class="w-20 px-3 py-1.5 rounded-lg border border-slate-200 dark:border-slate-800 dark:bg-slate-950 text-xs text-slate-800 dark:text-slate-100 focus:outline-none focus:border-indigo-500" />
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-6 flex justify-end">
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs uppercase tracking-widest transition-all">Save Categories Config</button>
            </div>
        </form>
    </div>

    <!-- ── TAB 3: DEAL OF THE DAY ── -->
    <div x-show="activeTab === 'deal_of_the_day'" class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 shadow-sm" x-transition style="display: none;">
        <div class="mb-6">
            <h3 class="text-lg font-bold text-slate-900 dark:text-white uppercase font-outfit">Deal of the Day config</h3>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Select the hardware configurations to show in the deal row and edit header tags.</p>
        </div>

        <form action="{{ route('admin.cms.settings.update') }}" method="POST" class="space-y-6">
            @csrf
            <input type="hidden" name="settings_type" value="deal_of_the_day" />

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1.5">Section Title</label>
                    <input type="text" name="deal_title" value="{{ old('deal_title', $dealOfTheDay['title']) }}" placeholder="Deal Of The Day" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-800 dark:bg-slate-950 text-slate-800 dark:text-slate-100 text-sm focus:outline-none focus:border-indigo-500" />
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1.5">Section Subtitle</label>
                    <input type="text" name="deal_subtitle" value="{{ old('deal_subtitle', $dealOfTheDay['subtitle']) }}" placeholder="Top discounts and flash bargains today" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-800 dark:bg-slate-950 text-slate-800 dark:text-slate-100 text-sm focus:outline-none focus:border-indigo-500" />
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-2">Select Products (Select multiple)</label>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3 max-h-72 overflow-y-auto p-4 rounded-xl border border-slate-200 dark:border-slate-800 dark:bg-slate-950/20 custom-scrollbar">
                    @foreach($products as $prod)
                    <label class="flex items-center gap-2 p-2 border border-slate-100 dark:border-slate-800 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-900 cursor-pointer">
                        <input type="checkbox" name="deal_product_ids[]" value="{{ $prod->id }}" {{ in_array($prod->id, old('deal_product_ids', $dealOfTheDay['product_ids'])) ? 'checked' : '' }} class="rounded text-indigo-600 focus:ring-indigo-500" />
                        <div class="flex flex-col min-w-0">
                            <span class="text-xs font-bold text-slate-800 dark:text-slate-100 truncate uppercase">{{ $prod->productname }}</span>
                            <span class="text-[10px] text-slate-400">Rs. {{ number_format($prod->display_price, 2) }}</span>
                        </div>
                    </label>
                    @endforeach
                </div>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs uppercase tracking-widest transition-all">Save Deals Config</button>
            </div>
        </form>
    </div>

    <!-- ── TAB 4: PROMO BANNER ── -->
    <div x-show="activeTab === 'promo_banner'" class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 shadow-sm" x-transition style="display: none;">
        <div class="mb-6">
            <h3 class="text-lg font-bold text-slate-900 dark:text-white uppercase font-outfit">Full-Width Promotional Banner</h3>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Configure copy content and action buttons for the lower homepage promo section.</p>
        </div>

        <form action="{{ route('admin.cms.settings.update') }}" method="POST" class="space-y-4">
            @csrf
            <input type="hidden" name="settings_type" value="promo_banner" />

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1.5">Badge/Label</label>
                    <input type="text" name="promo_badge" value="{{ old('promo_badge', $promoBanner['badge']) }}" placeholder="Limited Campaign" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-800 dark:bg-slate-950 text-slate-800 dark:text-slate-100 text-sm focus:outline-none focus:border-indigo-500" />
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1.5">Promo Title</label>
                    <input type="text" name="promo_title" value="{{ old('promo_title', $promoBanner['title']) }}" placeholder="Premium Workstation Upgrade Kits" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-800 dark:bg-slate-950 text-slate-800 dark:text-slate-100 text-sm focus:outline-none focus:border-indigo-500" />
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1.5">Promo Text Copy</label>
                <textarea name="promo_copy" rows="4" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-800 dark:bg-slate-950 text-slate-800 dark:text-slate-100 text-sm focus:outline-none focus:border-indigo-500">{{ old('promo_copy', $promoBanner['copy']) }}</textarea>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1.5">Button Text</label>
                    <input type="text" name="promo_btn_text" value="{{ old('promo_btn_text', $promoBanner['btn_text']) }}" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-800 dark:bg-slate-950 text-slate-800 dark:text-slate-100 text-sm focus:outline-none focus:border-indigo-500" />
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1.5">Button URL</label>
                    <input type="text" name="promo_btn_url" value="{{ old('promo_btn_url', $promoBanner['btn_url']) }}" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-800 dark:bg-slate-950 text-slate-800 dark:text-slate-100 text-sm focus:outline-none focus:border-indigo-500" />
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1.5">Advisor Phone text</label>
                    <input type="text" name="promo_phone" value="{{ old('promo_phone', $promoBanner['phone']) }}" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-800 dark:bg-slate-950 text-slate-800 dark:text-slate-100 text-sm focus:outline-none focus:border-indigo-500" />
                </div>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs uppercase tracking-widest transition-all">Save Promo Config</button>
            </div>
        </form>
    </div>

    <!-- ── TAB 5: TRUST HIGHLIGHTS ── -->
    <div x-show="activeTab === 'trust_badges'" class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 shadow-sm" x-transition style="display: none;">
        <div class="mb-6">
            <h3 class="text-lg font-bold text-slate-900 dark:text-white uppercase font-outfit">Trust / Feature highlights</h3>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Configure text values for the trust columns at the bottom of the homepage.</p>
        </div>

        <form action="{{ route('admin.cms.settings.update') }}" method="POST" class="space-y-6">
            @csrf
            <input type="hidden" name="settings_type" value="trust_badges" />

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Highlight 1 -->
                <div class="p-4 border border-slate-150 dark:border-slate-800 rounded-2xl space-y-3 bg-slate-50/50 dark:bg-slate-950/20">
                    <h4 class="text-xs font-black uppercase text-indigo-600 dark:text-indigo-400 tracking-wider">Highlight 1</h4>
                    <div>
                        <label class="block text-[10px] font-bold uppercase text-slate-500 mb-1">Title</label>
                        <input type="text" name="delivery_title" value="{{ old('delivery_title', $trustBadges['delivery_title']) }}" class="w-full px-3 py-2 rounded-lg border border-slate-200 dark:border-slate-855 dark:bg-slate-950 text-slate-800 dark:text-slate-100 text-xs focus:outline-none" />
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold uppercase text-slate-500 mb-1">Subtitle</label>
                        <input type="text" name="delivery_subtitle" value="{{ old('delivery_subtitle', $trustBadges['delivery_subtitle']) }}" class="w-full px-3 py-2 rounded-lg border border-slate-200 dark:border-slate-855 dark:bg-slate-950 text-slate-800 dark:text-slate-100 text-xs focus:outline-none" />
                    </div>
                </div>

                <!-- Highlight 2 -->
                <div class="p-4 border border-slate-150 dark:border-slate-800 rounded-2xl space-y-3 bg-slate-50/50 dark:bg-slate-950/20">
                    <h4 class="text-xs font-black uppercase text-indigo-600 dark:text-indigo-400 tracking-wider">Highlight 2</h4>
                    <div>
                        <label class="block text-[10px] font-bold uppercase text-slate-500 mb-1">Title</label>
                        <input type="text" name="returns_title" value="{{ old('returns_title', $trustBadges['returns_title']) }}" class="w-full px-3 py-2 rounded-lg border border-slate-200 dark:border-slate-855 dark:bg-slate-950 text-slate-800 dark:text-slate-100 text-xs focus:outline-none" />
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold uppercase text-slate-500 mb-1">Subtitle</label>
                        <input type="text" name="returns_subtitle" value="{{ old('returns_subtitle', $trustBadges['returns_subtitle']) }}" class="w-full px-3 py-2 rounded-lg border border-slate-200 dark:border-slate-855 dark:bg-slate-950 text-slate-800 dark:text-slate-100 text-xs focus:outline-none" />
                    </div>
                </div>

                <!-- Highlight 3 -->
                <div class="p-4 border border-slate-150 dark:border-slate-800 rounded-2xl space-y-3 bg-slate-50/50 dark:bg-slate-950/20">
                    <h4 class="text-xs font-black uppercase text-indigo-600 dark:text-indigo-400 tracking-wider">Highlight 3</h4>
                    <div>
                        <label class="block text-[10px] font-bold uppercase text-slate-500 mb-1">Title</label>
                        <input type="text" name="quality_title" value="{{ old('quality_title', $trustBadges['quality_title']) }}" class="w-full px-3 py-2 rounded-lg border border-slate-200 dark:border-slate-855 dark:bg-slate-950 text-slate-800 dark:text-slate-100 text-xs focus:outline-none" />
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold uppercase text-slate-500 mb-1">Subtitle</label>
                        <input type="text" name="quality_subtitle" value="{{ old('quality_subtitle', $trustBadges['quality_subtitle']) }}" class="w-full px-3 py-2 rounded-lg border border-slate-200 dark:border-slate-855 dark:bg-slate-950 text-slate-800 dark:text-slate-100 text-xs focus:outline-none" />
                    </div>
                </div>

                <!-- Highlight 4 -->
                <div class="p-4 border border-slate-150 dark:border-slate-800 rounded-2xl space-y-3 bg-slate-50/50 dark:bg-slate-950/20">
                    <h4 class="text-xs font-black uppercase text-indigo-600 dark:text-indigo-400 tracking-wider">Highlight 4</h4>
                    <div>
                        <label class="block text-[10px] font-bold uppercase text-slate-500 mb-1">Title</label>
                        <input type="text" name="gst_title" value="{{ old('gst_title', $trustBadges['gst_title']) }}" class="w-full px-3 py-2 rounded-lg border border-slate-200 dark:border-slate-855 dark:bg-slate-950 text-slate-800 dark:text-slate-100 text-xs focus:outline-none" />
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold uppercase text-slate-500 mb-1">Subtitle</label>
                        <input type="text" name="gst_subtitle" value="{{ old('gst_subtitle', $trustBadges['gst_subtitle']) }}" class="w-full px-3 py-2 rounded-lg border border-slate-200 dark:border-slate-855 dark:bg-slate-950 text-slate-800 dark:text-slate-100 text-xs focus:outline-none" />
                    </div>
                </div>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs uppercase tracking-widest transition-all">Save Trust Highlights</button>
            </div>
        </form>
    </div>

    <!-- ── TAB 6: SEO METADATA ── -->
    <div x-show="activeTab === 'seo'" class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 shadow-sm" x-transition style="display: none;">
        <div class="mb-6">
            <h3 class="text-lg font-bold text-slate-900 dark:text-white uppercase font-outfit">SEO Settings</h3>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Configure metadata headers for the storefront homepage to optimize indexing rank.</p>
        </div>

        <form action="{{ route('admin.cms.settings.update') }}" method="POST" class="space-y-4">
            @csrf
            <input type="hidden" name="settings_type" value="seo" />

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1.5">Homepage Meta Title</label>
                <input type="text" name="meta_title" value="{{ old('meta_title', $seoSettings['meta_title']) }}" placeholder="Enterprise Hardware Store" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-800 dark:bg-slate-950 text-slate-800 dark:text-slate-100 text-sm focus:outline-none focus:border-indigo-500" />
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1.5">Homepage Meta Description</label>
                <textarea name="meta_description" rows="3" placeholder="Imported server arrays and high-frequency compute components." class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-800 dark:bg-slate-950 text-slate-800 dark:text-slate-100 text-sm focus:outline-none focus:border-indigo-500">{{ old('meta_description', $seoSettings['meta_description']) }}</textarea>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs uppercase tracking-widest transition-all">Save SEO Config</button>
            </div>
        </form>
    </div>

</div>

<!-- Image Cropper Modal (Alpine/JS Overlay) -->
<div id="cropper-modal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/80 backdrop-blur-sm hidden">
    <div class="bg-white dark:bg-slate-900 rounded-2xl max-w-2xl w-full mx-4 overflow-hidden border border-slate-200 dark:border-slate-800 flex flex-col max-h-[85vh]">
        <div class="px-6 py-4 border-b border-slate-150 dark:border-slate-800 flex items-center justify-between">
            <h3 class="text-sm font-bold uppercase tracking-wider text-slate-800 dark:text-white">Crop & Adjust Banner Image</h3>
            <button type="button" onclick="closeCropperModal()" class="text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-white">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>
        <div class="p-6 overflow-hidden flex items-center justify-center bg-slate-950/40 relative min-h-[300px]">
            <img id="cropper-target-image" class="max-h-[50vh] max-w-full" />
        </div>
        <div class="px-6 py-4 border-t border-slate-150 dark:border-slate-800 flex items-center justify-between bg-slate-50 dark:bg-slate-950/40">
            <span class="text-xs text-slate-500 dark:text-slate-400 font-medium">Drag to crop. Ratio locked to snapdeal slide aspect (approx 4.1:1).</span>
            <div class="flex items-center gap-3">
                <button type="button" onclick="closeCropperModal()" class="px-4 py-2 rounded-xl border border-slate-200 dark:border-slate-800 text-xs font-semibold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800">Cancel</button>
                <button type="button" id="crop-confirm-btn" class="px-5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs uppercase tracking-wider">Crop & Use</button>
            </div>
        </div>
    </div>
</div>

<script>
    let cropperInstance = null;
    let activeInputId = null;
    let activeBannerId = null;

    document.addEventListener('DOMContentLoaded', function() {
        const createInput = document.getElementById('create-image-input');
        if (createInput) {
            createInput.addEventListener('change', function(e) {
                if (this.files && this.files[0]) {
                    openCropper(this.files[0], 'create-image-input', null);
                }
            });
        }

        // Register edit inputs dynamic change listeners
        document.querySelectorAll('input[id^="edit-image-input-"]').forEach(input => {
            input.addEventListener('change', function() {
                if (this.files && this.files[0]) {
                    const bannerId = this.getAttribute('data-banner-id');
                    openCropper(this.files[0], this.id, bannerId);
                }
            });
        });
    });

    function openCropper(file, inputId, bannerId) {
        activeInputId = inputId;
        activeBannerId = bannerId;
        
        const reader = new FileReader();
        reader.onload = function(e) {
            const modal = document.getElementById('cropper-modal');
            const targetImage = document.getElementById('cropper-target-image');
            
            targetImage.src = e.target.result;
            modal.classList.remove('hidden');
            
            if (cropperInstance) {
                cropperInstance.destroy();
            }

            // Aspect ratio constraint to match the widescreen snapdeal slider style (1600x390, ~4.1:1 ratio)
            cropperInstance = new Cropper(targetImage, {
                aspectRatio: 1600 / 390,
                viewMode: 1,
                dragMode: 'move',
                autoCropArea: 1,
                restore: false,
                guides: true,
                center: true,
                highlight: false,
                cropBoxMovable: true,
                cropBoxResizable: true,
                toggleDragModeOnDblclick: false,
            });
        };
        reader.readAsDataURL(file);
    }

    function closeCropperModal() {
        document.getElementById('cropper-modal').classList.add('hidden');
        if (cropperInstance) {
            cropperInstance.destroy();
            cropperInstance = null;
        }
        // Reset inputs if they cancelled without cropping
        if (activeInputId) {
            document.getElementById(activeInputId).value = '';
        }
    }

    document.getElementById('crop-confirm-btn').addEventListener('click', function() {
        if (!cropperInstance) return;
        
        cropperInstance.getCroppedCanvas({
            width: 1600,
            height: 390
        }).toBlob((blob) => {
            // Create cropped File object
            const file = new File([blob], "cropped_banner.jpg", { type: "image/jpeg" });
            const dataTransfer = new DataTransfer();
            dataTransfer.items.add(file);
            
            const originalInput = document.getElementById(activeInputId);
            originalInput.files = dataTransfer.files;

            // Update client preview image
            if (activeBannerId) {
                // Edit form preview
                const wrapper = document.getElementById(`edit-preview-wrapper-${activeBannerId}`);
                const preview = document.getElementById(`edit-preview-${activeBannerId}`);
                if (wrapper && preview) {
                    preview.src = URL.createObjectURL(blob);
                    wrapper.classList.remove('hidden');
                }
            } else {
                // Create form preview
                const wrapper = document.getElementById('create-preview-wrapper');
                const preview = document.getElementById('create-preview');
                if (wrapper && preview) {
                    preview.src = URL.createObjectURL(blob);
                    wrapper.classList.remove('hidden');
                }
            }
            
            document.getElementById('cropper-modal').classList.add('hidden');
            cropperInstance.destroy();
            cropperInstance = null;
        }, 'image/jpeg', 0.9);
    });
</script>
@endsection
