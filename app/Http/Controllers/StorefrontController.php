<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\Subcategory;
use App\Models\Uorder;
use App\Models\UorderItem;
use App\Models\Uorderbal;
use App\Models\Stategst;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class StorefrontController extends Controller
{
    /**
     * Storefront Homepage
     */
    public function index(Request $request)
    {
        $categories = Category::orderBy('cat_name', 'asc')->get();
        $subcategories = Subcategory::with('category')->where('status', 1)->orderBy('subcategoryname', 'asc')->get();
        
        $query = Product::where('status', 1);
        
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('productname', 'like', "%{$search}%")
                  ->orWhere('productdes', 'like', "%{$search}%");
        }
        
        $products = $query->orderBy('id', 'desc')->paginate(12);

        // Load Homepage CMS Content
        $banners = \App\Models\HomepageBanner::where('is_active', true)->orderBy('sort_order', 'asc')->get();
        
        $featuredCategoriesConfig = \App\Models\HomepageSetting::getByKey('homepage_categories', []);
        $featuredCategories = [];
        if (!empty($featuredCategoriesConfig)) {
            foreach ($featuredCategoriesConfig as $cfg) {
                $cat = $categories->firstWhere('cat_id', $cfg['cat_id']);
                if ($cat) {
                    $cat->homepage_icon = $cfg['icon'] ?? 'fa-server';
                    $featuredCategories[] = $cat;
                }
            }
        } else {
            // Fallback: use first 8 categories with default icons
            $icons = ['fa-server', 'fa-laptop-code', 'fa-microchip', 'fa-memory', 'fa-hard-drive', 'fa-keyboard', 'fa-headphones', 'fa-print', 'fa-network-wired'];
            foreach ($categories->take(8) as $index => $cat) {
                $cat->homepage_icon = $icons[$index % count($icons)];
                $featuredCategories[] = $cat;
            }
        }

        $dealConfig = \App\Models\HomepageSetting::getByKey('deal_of_the_day', [
            'title' => 'Deal Of The Day',
            'subtitle' => 'Top discounts and flash bargains today',
            'product_ids' => []
        ]);
        $dealProducts = [];
        if (!empty($dealConfig['product_ids'])) {
            $dealProducts = Product::where('status', 1)
                ->whereIn('id', $dealConfig['product_ids'])
                ->get();
        } else {
            // Fallback to first 6 products
            $dealProducts = Product::where('status', 1)->orderBy('id', 'desc')->limit(6)->get();
        }

        $promoBanner = \App\Models\HomepageSetting::getByKey('promo_banner', [
            'badge' => 'Limited Campaign',
            'title' => 'Premium Workstation Upgrade Kits',
            'copy' => 'Maximize hardware bandwidth metrics by upgrading with premium dual-channel server memory arrays, certified SSD units, and modular power matrices. Direct consultations available with our hardware advisors.',
            'btn_text' => 'Consult Advisor',
            'btn_url' => 'tel:+919944228686',
            'phone' => '+91 99442 28686'
        ]);

        $trustBadges = \App\Models\HomepageSetting::getByKey('trust_badges', [
            'delivery_title' => 'FREE Delivery',
            'delivery_subtitle' => 'On all hardware imports',
            'returns_title' => '7 Days Returns',
            'returns_subtitle' => 'Hassle-free return policy',
            'quality_title' => 'Great Quality',
            'quality_subtitle' => 'Direct enterprise sourcing',
            'gst_title' => 'GST Compliant',
            'gst_subtitle' => 'Input Credit & Tax Invoices'
        ]);

        $seoSettings = \App\Models\HomepageSetting::getByKey('seo_settings', [
            'meta_title' => 'Enterprise Hardware Store',
            'meta_description' => 'Imported server arrays and high-frequency compute components.'
        ]);
        $title = $seoSettings['meta_title'] ?? null;
        $metaDesc = $seoSettings['meta_description'] ?? null;
        
        return view('storefront.index', compact(
            'categories', 
            'subcategories', 
            'products', 
            'banners', 
            'featuredCategories', 
            'dealConfig', 
            'dealProducts', 
            'promoBanner', 
            'trustBadges',
            'seoSettings',
            'title',
            'metaDesc'
        ));
    }

    /**
     * Get real-time search suggestions
     */
    public function searchSuggestions(Request $request)
    {
        $search = $request->input('query');
        if (empty($search) || strlen($search) < 2) {
            return response()->json([]);
        }

        $products = Product::where('status', 1)
            ->where(function($q) use ($search) {
                $q->where('productname', 'like', "%{$search}%")
                  ->orWhere('productdes', 'like', "%{$search}%");
            })
            ->orderBy('id', 'desc')
            ->limit(6)
            ->get();

        $suggestions = $products->map(function($product) {
            return [
                'id' => $product->id,
                'name' => html_entity_decode($product->productname, ENT_QUOTES, 'UTF-8'),
                'price' => number_format($product->display_price, 2),
                'image' => $product->primary_image_url ?: 'data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="100" height="100" viewBox="0 0 100 100"><rect width="100" height="100" fill="%23f3f4f6"/><circle cx="50" cy="50" r="20" fill="%23d1d5db"/></svg>',
                'url' => route('storefront.product', $product->id)
            ];
        });

        return response()->json($suggestions);
    }

    /**
     * Category Products List
     */
    public function category($catName, Request $request)
    {
        $categories = Category::orderBy('cat_name', 'asc')->get();
        
        // Find category
        $category = Category::where('cat_name', $catName)->firstOrFail();
        
        $query = Product::where('status', 1)->where('catid', $category->cat_id);
        
        if ($request->filled('price_range')) {
            $range = $request->input('price_range');
            if ($range == 'under_1000') {
                $query->where('display_price', '<', 1000);
            } elseif ($range == '1000_5000') {
                $query->whereBetween('display_price', [1000, 5000]);
            } elseif ($range == 'over_5000') {
                $query->where('display_price', '>', 5000);
            }
        }

        $sort = $request->input('sort', 'relevance');
        if ($sort == 'price_low_high') {
            $query->orderBy('display_price', 'asc');
        } elseif ($sort == 'price_high_low') {
            $query->orderBy('display_price', 'desc');
        } else {
            $query->orderBy('id', 'desc');
        }
        
        $products = $query->paginate(16);
            
        return view('storefront.category', compact('categories', 'category', 'products'));
    }

    /**
     * General Shop / Search Listing Page
     */
    public function shop(Request $request)
    {
        $categories = Category::orderBy('cat_name', 'asc')->get();
        
        $query = Product::where('status', 1);
        
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('productname', 'like', "%{$search}%")
                  ->orWhere('productdes', 'like', "%{$search}%");
            });
        }
        
        if ($request->filled('price_range')) {
            $range = $request->input('price_range');
            if ($range == 'under_1000') {
                $query->where('display_price', '<', 1000);
            } elseif ($range == '1000_5000') {
                $query->whereBetween('display_price', [1000, 5000]);
            } elseif ($range == 'over_5000') {
                $query->where('display_price', '>', 5000);
            }
        }

        $sort = $request->input('sort', 'relevance');
        if ($sort == 'price_low_high') {
            $query->orderBy('display_price', 'asc');
        } elseif ($sort == 'price_high_low') {
            $query->orderBy('display_price', 'desc');
        } else {
            $query->orderBy('id', 'desc');
        }
        
        $products = $query->paginate(16);
        
        return view('storefront.shop', compact('categories', 'products'));
    }

    /**
     * Product Details Page
     */
    public function product($id)
    {
        $product = Product::findOrFail($id);
        $categories = Category::orderBy('cat_name', 'asc')->get();
        
        // Related products from same category
        $relatedProducts = Product::where('status', 1)
            ->where('catid', $product->catid)
            ->where('id', '!=', $product->id)
            ->limit(4)
            ->get();
            
        return view('storefront.product', compact('categories', 'product', 'relatedProducts'));
    }

    /**
     * View Shopping Cart
     */
    public function cart()
    {
        $categories = Category::orderBy('cat_name', 'asc')->get();
        $cart = $this->refreshCartPrices();
        
        return view('storefront.cart', compact('categories', 'cart'));
    }

    /**
     * Refresh Cart Prices based on role
     */
    private function refreshCartPrices()
    {
        $cart = session()->get('cart', []);
        if (empty($cart)) return $cart;
        
        $updated = false;
        foreach ($cart as $id => &$item) {
            $product = Product::find($id);
            if ($product) {
                $currentPrice = floatval($product->display_price);
                if ($item['price'] != $currentPrice) {
                    $item['price'] = $currentPrice;
                    $updated = true;
                }
            }
        }
        
        if ($updated) {
            session()->put('cart', $cart);
        }
        
        return $cart;
    }

    /**
     * Add Item to Cart
     */
    public function addToCart(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        $qty = intval($request->input('quantity', 1));
        
        if ($qty <= 0) $qty = 1;
        
        if ($product->tqty < $qty) {
            return redirect()->back()->with('error', 'Only ' . $product->tqty . ' items left in stock!');
        }
        
        $cart = session()->get('cart', []);
        
        // If product already in cart, update quantity
        if (isset($cart[$id])) {
            $newQty = $cart[$id]['quantity'] + $qty;
            if ($product->tqty < $newQty) {
                return redirect()->back()->with('error', 'Cannot add more. Stock limit reached!');
            }
            $cart[$id]['quantity'] = $newQty;
            $cart[$id]['price'] = floatval($product->display_price);
        } else {
            // Add new product to cart
            $cart[$id] = [
                "id" => $product->id,
                "name" => $product->productname,
                "quantity" => $qty,
                "price" => floatval($product->display_price),
                "gst" => floatval($product->gst ?: 0),
                "hsnsac" => $product->hsnsac ?: '',
                "unit" => $product->unit == 2 ? 'BOX' : ($product->unit == 3 ? 'PKT' : 'PCS'),
                "image" => $product->pimagef
            ];
        }
        
        session()->put('cart', $cart);
        
        session()->flash('added_item', [
            'id' => $product->id,
            'name' => $product->productname,
            'quantity' => $qty,
            'price' => floatval($product->display_price),
            'image' => $product->pimagef,
            'unit' => $product->unit == 2 ? 'BOX' : ($product->unit == 3 ? 'PKT' : 'PCS')
        ]);
        
        return redirect()->route('storefront.cart')->with('success', 'Product added to cart!');
    }

    /**
     * Update Cart Quantity
     */
    public function updateCart(Request $request)
    {
        $request->validate([
            'quantities' => 'required|array'
        ]);
        
        $cart = session()->get('cart', []);
        $quantities = $request->input('quantities');
        
        foreach ($quantities as $id => $qty) {
            $qty = intval($qty);
            if ($qty <= 0) {
                unset($cart[$id]);
            } else {
                $product = Product::find($id);
                if ($product) {
                    if ($product->tqty < $qty) {
                        return redirect()->back()->with('error', 'Requested quantity for ' . $product->productname . ' exceeds available stock!');
                    }
                    $cart[$id]['quantity'] = $qty;
                    $cart[$id]['price'] = floatval($product->display_price);
                }
            }
        }
        
        session()->put('cart', $cart);
        return redirect()->route('storefront.cart')->with('success', 'Cart successfully updated!');
    }

    /**
     * Remove Item from Cart
     */
    public function removeFromCart($id)
    {
        $cart = session()->get('cart', []);
        
        if (isset($cart[$id])) {
            unset($cart[$id]);
            session()->put('cart', $cart);
        }
        
        return redirect()->route('storefront.cart')->with('success', 'Item removed from cart!');
    }

    /**
     * Checkout Form
     */
    public function checkout()
    {
        $cart = $this->refreshCartPrices();
        if (empty($cart)) {
            return redirect()->route('storefront.cart')->with('error', 'Your shopping cart is empty!');
        }
        
        $categories = Category::orderBy('cat_name', 'asc')->get();
        $states = Stategst::orderBy('sname', 'asc')->get();
        
        return view('storefront.checkout', compact('categories', 'cart', 'states'));
    }

    /**
     * Place Customer Order
     */
    public function placeOrder(Request $request)
    {
        $cart = $this->refreshCartPrices();
        if (empty($cart)) {
            return redirect()->route('storefront.cart')->with('error', 'Your shopping cart is empty!');
        }
        
        $request->validate([
            'clientName' => 'required|string|max:255',
            'mobileno' => 'required|string|max:25',
            'clientContact' => 'required|string',
            'city' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'pincode' => 'required|string',
            'paymentType' => 'required|integer',
        ]);
        
        $orderDate = date('Y-m-d');
        $month = date('m');
        $year = date('Y');
        
        // Calculate invoice number (morderid)
        $maxMorderId = Uorder::whereMonth('orderdate', $month)
            ->whereYear('orderdate', $year)
            ->max('morderid');
            
        $morder_id = ($maxMorderId ?? 0) + 1;
        $gsttin = trim((string) $request->input('gsttin', ''));
        
        try {
            DB::beginTransaction();
            
            // Get or create user
            $uid = Auth::id() ?: 0;
            
            if ($uid > 0) {
                $user = User::findOrFail($uid);
                $user->update([
                    'uname' => strtoupper($request->input('clientName')),
                    'billingaddress' => strtoupper($request->input('clientContact')),
                    'contactno' => $request->input('mobileno'),
                    'billingstate' => strtoupper($request->input('state')),
                    'billingcity' => strtoupper($request->input('city')),
                    'billingpincode' => $request->input('pincode'),
                    'gsttin' => $gsttin !== '' ? $gsttin : ($user->gsttin ?? '')
                ]);
            } else {
                // If not logged in, create a temporary guest user account
                $user = User::create([
                    'uname' => strtoupper($request->input('clientName')),
                    'contactno' => $request->input('mobileno'),
                    'usertype' => 'C',
                    'ustatus' => 1,
                    'billingaddress' => strtoupper($request->input('clientContact')),
                    'billingstate' => strtoupper($request->input('state')),
                    'billingcity' => strtoupper($request->input('city')),
                    'billingpincode' => $request->input('pincode'),
                    'shippingaddress' => strtoupper($request->input('clientContact')),
                    'shippingstate' => strtoupper($request->input('state')),
                    'shippingcity' => strtoupper($request->input('city')),
                    'shippingpincode' => $request->input('pincode'),
                    'regdate' => date('Y-m-d H:i:s'),
                    'gsttin' => $gsttin
                ]);
                $uid = $user->id;
            }
            
            // Calculate totals
            $subTotal = 0;
            $taxTotal = 0;
            
            foreach ($cart as $id => $item) {
                $lineVal = $item['quantity'] * $item['price'];
                $subTotal += $lineVal;
                $taxTotal += $lineVal * ($item['gst'] / 100);
            }
            
            $grandTotal = $subTotal + $taxTotal;
            
            // Insert Uorder
            $order = Uorder::create([
                'orderdate' => date('Y-m-d H:i:s'),
                'userid' => $uid,
                'utype' => 'C',
                'paymethod' => $request->input('paymentType') == 1 ? 'h' : 'I', // 'h' for Cash, 'I' for UPI/etc
                'total' => $subTotal,
                'gamount' => $grandTotal,
                'tship' => 0,
                'pamount' => 0,
                'bamount' => $grandTotal,
                'discount' => 0,
                'gsta' => $taxTotal,
                'ostatus' => 'p', // 'p' for pending processing
                'morderid' => $morder_id,
                'install' => 0,
                'gsttin' => $gsttin,
                'username' => strtoupper($request->input('clientName'))
            ]);
            
            // Insert Uorderbal
            Uorderbal::create([
                'orderid' => $order->orderid,
                'gtotal' => $grandTotal,
                'pamount' => 0,
                'bamount' => $grandTotal,
                'ptype' => $order->paymethod,
                'pdate' => date('Y-m-d H:i:s')
            ]);
            
            // Save Items & Update Stock
            $slno = 1;
            foreach ($cart as $id => $item) {
                $product = Product::findOrFail($id);
                
                // Subtract stock
                $product->update([
                    'tqty' => $product->tqty - $item['quantity']
                ]);
                
                // Create Item record
                UorderItem::create([
                    'orderid' => $order->orderid,
                    'productId' => $id,
                    'hsnsan' => $item['hsnsac'] ?: '',
                    'gst' => $item['gst'] ?: 0,
                    'quantity' => $item['quantity'],
                    'rate' => $item['price'],
                    'unit' => $item['unit'] ?: 'PCS',
                    'cprice' => $item['quantity'] * $item['price'],
                    'srate' => $item['price'],
                    'userId' => $uid,
                    'price' => $item['price'],
                    'slno' => $slno++
                ]);
            }
            
            DB::commit();
            
            // Clear cart
            session()->forget('cart');
            
            return redirect()->route('storefront.order.success', $order->orderid)->with('success', 'Order placed successfully!');
            
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'Error placing order: ' . $e->getMessage());
        }
    }
    
    /**
     * Order Placement Success Page
     */
    public function orderSuccess($orderId)
    {
        $categories = Category::orderBy('cat_name', 'asc')->get();
        $order = Uorder::findOrFail($orderId);
        
        return view('storefront.success', compact('categories', 'order'));
    }

    /**
     * Customer Orders History
     */
    public function orderHistory()
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please log in to view order history!');
        }
        
        $categories = Category::orderBy('cat_name', 'asc')->get();
        $orders = Uorder::where('userid', Auth::id())
            ->orderBy('orderid', 'desc')
            ->paginate(10);
            
        return view('storefront.orders', compact('categories', 'orders'));
    }
    
    /**
     * Specific Order details
     */
    public function orderDetails($id)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please log in to view order details!');
        }
        
        $categories = Category::orderBy('cat_name', 'asc')->get();
        $order = Uorder::with('items.product')->where('userid', Auth::id())->findOrFail($id);
        
        return view('storefront.order_details', compact('categories', 'order'));
    }

    /**
     * Printable customer invoice
     */
    public function orderPrint($id)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please log in to print your invoice!');
        }

        $categories = Category::orderBy('cat_name', 'asc')->get();
        $order = Uorder::with('items.product')->where('userid', Auth::id())->findOrFail($id);

        return view('storefront.order_print', compact('categories', 'order'));
    }
}
