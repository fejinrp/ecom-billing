<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\Subcategory;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Orderbal;
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
        
        $query = Product::where('status', 1);
        
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('productname', 'like', "%{$search}%")
                  ->orWhere('productdes', 'like', "%{$search}%");
        }
        
        $products = $query->orderBy('id', 'desc')->paginate(12);
        
        return view('storefront.index', compact('categories', 'products'));
    }

    /**
     * Category Products List
     */
    public function category($catName, Request $request)
    {
        $categories = Category::orderBy('cat_name', 'asc')->get();
        
        // Find category
        $category = Category::where('cat_name', $catName)->firstOrFail();
        
        $products = Product::where('status', 1)
            ->where('catid', $category->cat_id)
            ->orderBy('id', 'desc')
            ->paginate(12);
            
        return view('storefront.category', compact('categories', 'category', 'products'));
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
        
        // Calculate invoice number (morder_id)
        $maxMorderId = Order::whereMonth('order_date', $month)
            ->whereYear('order_date', $year)
            ->where('section', 1)
            ->max('morder_id');
            
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
            
            // Determine Intra-State vs Inter-State
            // Tamil Nadu (33) state place of sale code
            $paymentPlace = (strtoupper($request->input('state')) == 'TAMIL NADU') ? 1 : 2;
            
            // Insert Order
            $order = Order::create([
                'order_date' => $orderDate,
                'client_name' => strtoupper($request->input('clientName')),
                'client_contact' => strtoupper($request->input('clientContact')),
                'sub_total' => $subTotal,
                'total_amount' => $subTotal,
                'discount' => 0,
                'grand_total' => $grandTotal,
                'paid' => 0, // Customer places order, paid is updated on actual gateway integration or delivery
                'due' => $grandTotal,
                'payment_type' => $request->input('paymentType'),
                'payment_status' => 3, // No paid / pending checkout
                'payment_place' => $paymentPlace,
                'gstn' => $taxTotal,
                'order_status' => 1,
                'user_id' => $uid,
                'paymentname' => 'ONLINE STORE',
                'morder_id' => $morder_id,
                'mobile' => $request->input('mobileno'),
                'gsttin' => $gsttin,
                'section' => 1,
                'instamt' => 0,
                'shipamt' => 0
            ]);
            
            // Insert Orderbal
            Orderbal::create([
                'order_id' => $order->order_id,
                'gtotal' => $grandTotal,
                'pamount' => 0,
                'bamount' => $grandTotal,
                'pdate' => $orderDate
            ]);
            
            // Save Items & Update Stock
            foreach ($cart as $id => $item) {
                $product = Product::findOrFail($id);
                
                // Subtract stock
                $product->update([
                    'tqty' => $product->tqty - $item['quantity']
                ]);
                
                // Create Item record
                OrderItem::create([
                    'order_id' => $order->order_id,
                    'product_id' => $id,
                    'hsnsan' => $item['hsnsac'],
                    'gst' => $item['gst'],
                    'qty' => $item['quantity'],
                    'rate' => $item['price'],
                    'unit' => $item['unit'],
                    'total' => $item['quantity'] * $item['price'],
                    'status' => 1
                ]);
            }
            
            DB::commit();
            
            // Clear cart
            session()->forget('cart');
            
            return redirect()->route('storefront.order.success', $order->order_id)->with('success', 'Order placed successfully!');
            
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
        $order = Order::findOrFail($orderId);
        
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
        $orders = Order::where('user_id', Auth::id())
            ->orderBy('order_id', 'desc')
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
        $order = Order::with('items.product')->where('user_id', Auth::id())->findOrFail($id);
        
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
        $order = Order::with('items.product')->where('user_id', Auth::id())->findOrFail($id);

        return view('storefront.order_print', compact('categories', 'order'));
    }
}
