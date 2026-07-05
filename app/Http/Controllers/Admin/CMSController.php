<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HomepageBanner;
use App\Models\HomepageSetting;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class CMSController extends Controller
{
    /**
     * Display the CMS page
     */
    public function index()
    {
        $banners = HomepageBanner::orderBy('sort_order', 'asc')->get();
        $categories = Category::orderBy('cat_name', 'asc')->get();
        $products = Product::where('status', 1)->orderBy('productname', 'asc')->get();

        // Get saved settings or return defaults
        $featuredCategories = HomepageSetting::getByKey('homepage_categories', []);
        $promoBanner = HomepageSetting::getByKey('promo_banner', [
            'badge' => 'Limited Campaign',
            'title' => 'Premium Workstation Upgrade Kits',
            'copy' => 'Maximize hardware bandwidth metrics by upgrading with premium dual-channel server memory arrays, certified SSD units, and modular power matrices. Direct consultations available with our hardware advisors.',
            'btn_text' => 'Consult Advisor',
            'btn_url' => 'tel:+919944228686',
            'phone' => '+91 99442 28686'
        ]);
        
        $dealOfTheDay = HomepageSetting::getByKey('deal_of_the_day', [
            'title' => 'Deal Of The Day',
            'subtitle' => 'Top discounts and flash bargains today',
            'product_ids' => []
        ]);

        $trustBadges = HomepageSetting::getByKey('trust_badges', [
            'delivery_title' => 'FREE Delivery',
            'delivery_subtitle' => 'On all hardware imports',
            'returns_title' => '7 Days Returns',
            'returns_subtitle' => 'Hassle-free return policy',
            'quality_title' => 'Great Quality',
            'quality_subtitle' => 'Direct enterprise sourcing',
            'gst_title' => 'GST Compliant',
            'gst_subtitle' => 'Input Credit & Tax Invoices'
        ]);

        $seoSettings = HomepageSetting::getByKey('seo_settings', [
            'meta_title' => 'Enterprise Hardware Store',
            'meta_description' => 'Imported server arrays and high-frequency compute components.'
        ]);

        $categoryShowcase = HomepageSetting::getByKey('homepage_category_products', [
            'category_ids' => [],
            'product_limit' => 4
        ]);

        return view('admin.cms.index', compact(
            'banners',
            'categories',
            'products',
            'featuredCategories',
            'promoBanner',
            'dealOfTheDay',
            'trustBadges',
            'seoSettings',
            'categoryShowcase'
        ));
    }

    /**
     * Store a new homepage banner slide
     */
    public function store(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
            'title' => 'nullable|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'badge_text' => 'nullable|string|max:100',
            'link_url' => 'nullable|string|max:255',
            'sort_order' => 'required|integer',
        ]);

        $imagePath = '';
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $imagePath = Storage::disk('public')->putFile('bannerimage', $file);
        }

        HomepageBanner::create([
            'image_path' => $imagePath,
            'title' => $request->input('title'),
            'subtitle' => $request->input('subtitle'),
            'badge_text' => $request->input('badge_text'),
            'link_url' => $request->input('link_url'),
            'sort_order' => $request->input('sort_order', 0),
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.cms.index')->with('success', 'Banner created successfully!');
    }

    /**
     * Update an existing homepage banner slide
     */
    public function update(Request $request, $id)
    {
        $banner = HomepageBanner::findOrFail($id);

        $request->validate([
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'title' => 'nullable|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'badge_text' => 'nullable|string|max:100',
            'link_url' => 'nullable|string|max:255',
            'sort_order' => 'required|integer',
        ]);

        $data = [
            'title' => $request->input('title'),
            'subtitle' => $request->input('subtitle'),
            'badge_text' => $request->input('badge_text'),
            'link_url' => $request->input('link_url'),
            'sort_order' => $request->input('sort_order', 0),
            'is_active' => $request->has('is_active'),
        ];

        if ($request->hasFile('image')) {
            // Delete old image if exists
            $oldPath = str_replace('storage/', '', $banner->image_path);
            if ($oldPath && Storage::disk('public')->exists($oldPath)) {
                Storage::disk('public')->delete($oldPath);
            }

            $file = $request->file('image');
            $data['image_path'] = Storage::disk('public')->putFile('bannerimage', $file);
        }

        $banner->update($data);

        return redirect()->route('admin.cms.index')->with('success', 'Banner updated successfully!');
    }

    /**
     * Remove a banner slide
     */
    public function destroy($id)
    {
        $banner = HomepageBanner::findOrFail($id);
        
        $oldPath = str_replace('storage/', '', $banner->image_path);
        if ($oldPath && Storage::disk('public')->exists($oldPath)) {
            Storage::disk('public')->delete($oldPath);
        }

        $banner->delete();

        return redirect()->route('admin.cms.index')->with('success', 'Banner deleted successfully!');
    }

    /**
     * Update dynamic homepage configurations (Categories, Promo, Trust, etc)
     */
    public function updateSettings(Request $request)
    {
        $type = $request->input('settings_type');

        if ($type === 'categories') {
            $categoriesInput = $request->input('categories', []);
            $featured = [];
            foreach ($categoriesInput as $catId => $data) {
                if (isset($data['featured'])) {
                    $featured[] = [
                        'cat_id' => intval($catId),
                        'icon' => $data['icon'] ?? 'fa-cubes',
                        'sort_order' => intval($data['sort_order'] ?? 0)
                    ];
                }
            }
            // Sort featured by sort_order
            usort($featured, function($a, $b) {
                return $a['sort_order'] <=> $b['sort_order'];
            });
            HomepageSetting::setByKey('homepage_categories', $featured);
        } 
        
        elseif ($type === 'promo_banner') {
            $promo = [
                'badge' => $request->input('promo_badge'),
                'title' => $request->input('promo_title'),
                'copy' => $request->input('promo_copy'),
                'btn_text' => $request->input('promo_btn_text'),
                'btn_url' => $request->input('promo_btn_url'),
                'phone' => $request->input('promo_phone')
            ];
            HomepageSetting::setByKey('promo_banner', $promo);
        } 
        
        elseif ($type === 'deal_of_the_day') {
            $deal = [
                'title' => $request->input('deal_title'),
                'subtitle' => $request->input('deal_subtitle'),
                'product_ids' => array_map('intval', $request->input('deal_product_ids', []))
            ];
            HomepageSetting::setByKey('deal_of_the_day', $deal);
        } 
        
        elseif ($type === 'trust_badges') {
            $trust = [
                'delivery_title' => $request->input('delivery_title'),
                'delivery_subtitle' => $request->input('delivery_subtitle'),
                'returns_title' => $request->input('returns_title'),
                'returns_subtitle' => $request->input('returns_subtitle'),
                'quality_title' => $request->input('quality_title'),
                'quality_subtitle' => $request->input('quality_subtitle'),
                'gst_title' => $request->input('gst_title'),
                'gst_subtitle' => $request->input('gst_subtitle')
            ];
            HomepageSetting::setByKey('trust_badges', $trust);
        } 
        
        elseif ($type === 'seo') {
            $seo = [
                'meta_title' => $request->input('meta_title'),
                'meta_description' => $request->input('meta_description')
            ];
            HomepageSetting::setByKey('seo_settings', $seo);
        }

        elseif ($type === 'category_showcase') {
            $showcase = [
                'category_ids' => array_map('intval', $request->input('showcase_category_ids', [])),
                'product_limit' => intval($request->input('showcase_product_limit', 4))
            ];
            HomepageSetting::setByKey('homepage_category_products', $showcase);
        }

        return redirect()->route('admin.cms.index')->with('success', 'Homepage settings updated successfully!');
    }
}
