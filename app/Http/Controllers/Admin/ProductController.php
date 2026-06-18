<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\Subcategory;
use App\Models\Brand;
use Illuminate\Support\Facades\File;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Product::with(['category', 'subcategory', 'brand'])
            ->where('status', 1)
            ->orderBy('id', 'desc')
            ->get();

        $categories = Category::where('status', 1)->orderBy('cat_name', 'asc')->get();
        $subcategories = Subcategory::where('status', 1)->orderBy('subcategoryname', 'asc')->get();
        $brands = Brand::where('brand_status', 1)->orderBy('brand_name', 'asc')->get();

        return view('admin.products.index', compact('products', 'categories', 'subcategories', 'brands'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::where('status', 1)->orderBy('cat_name', 'asc')->get();
        $subcategories = Subcategory::where('status', 1)->orderBy('subcategoryname', 'asc')->get();
        $brands = Brand::where('brand_status', 1)->orderBy('brand_name', 'asc')->get();

        return view('admin.products.create', compact('categories', 'subcategories', 'brands'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $product = Product::findOrFail($id);
        $categories = Category::where('status', 1)->orderBy('cat_name', 'asc')->get();
        $subcategories = Subcategory::where('status', 1)->orderBy('subcategoryname', 'asc')->get();
        $brands = Brand::where('brand_status', 1)->orderBy('brand_name', 'asc')->get();

        return view('admin.products.edit', compact('product', 'categories', 'subcategories', 'brands'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'productname' => 'required|string|max:255',
            'productdes' => 'required|string',
            'catid' => 'required|integer',
            'subcatid' => 'required|integer',
            'brandid' => 'required|integer',
            'unit' => 'required|integer',
            'pqty' => 'required|numeric',
            'tqty' => 'required|numeric',
            'pcode' => 'required|string|max:50',
            'pfrom' => 'required|string|max:255',
            'prate' => 'required|numeric',
            'srate' => 'required|numeric',
            'mrp' => 'required|numeric',
            'gst' => 'required|numeric',
            'cprice' => 'required|numeric',
            'dprice' => 'required|numeric',
            'sdprice' => 'required|numeric',
            'hsnsac' => 'required|string',
            'productimagef' => 'nullable|image|max:250',
            'productimages' => 'nullable|image|max:250',
            'productimaget' => 'nullable|image|max:250',
        ]);

        // Check for duplicate product code
        if (Product::where('pcode', $request->input('pcode'))->exists()) {
            return redirect()->back()->withInput()->with('error', 'Already Existing Product Code!');
        }

        // Get max ID to determine product directory
        $nextId = (Product::max('id') ?? 0) + 1;
        $legacyImageDir = public_path('productimage/' . $nextId . '/');

        if (!File::exists($legacyImageDir)) {
            File::makeDirectory($legacyImageDir, 0755, true);
        }

        $urlff = '';
        $urlss = '';
        $urltt = '';

        if ($request->hasFile('productimagef')) {
            $file = $request->file('productimagef');
            $ext = $file->getClientOriginalExtension();
            $urlff = 'first.' . $ext;
            $file->move($legacyImageDir, $urlff);
        }

        if ($request->hasFile('productimages')) {
            $file = $request->file('productimages');
            $ext = $file->getClientOriginalExtension();
            $urlss = 'second.' . $ext;
            $file->move($legacyImageDir, $urlss);
        }

        if ($request->hasFile('productimaget')) {
            $file = $request->file('productimaget');
            $ext = $file->getClientOriginalExtension();
            $urltt = 'third.' . $ext;
            $file->move($legacyImageDir, $urltt);
        }

        Product::create([
            'id' => $nextId,
            'brandid' => $request->input('brandid'),
            'catid' => $request->input('catid'),
            'subcatid' => $request->input('subcatid'),
            'productname' => strtoupper($request->input('productname')),
            'unit' => $request->input('unit'),
            'productdes' => strtoupper($request->input('productdes')),
            'pqty' => $request->input('pqty'),
            'tqty' => $request->input('tqty'),
            'pfrom' => strtoupper($request->input('pfrom')),
            'prate' => $request->input('prate'),
            'srate' => $request->input('srate'), // mappings map to srate
            'mrp' => $request->input('mrp'),
            'gst' => $request->input('gst'),
            'cprice' => $request->input('cprice'),
            'dprice' => $request->input('dprice'),
            'sdprice' => $request->input('sdprice'),
            'pcode' => $request->input('pcode'),
            'status' => 1, // In Stock
            'hsnsac' => $request->input('hsnsac'),
            'slno' => 0,
            'pimagef' => $urlff,
            'pimages' => $urlss,
            'pimaget' => $urltt,
            'postingdate' => date('d-m-Y h:i:s A'),
            'updationdate' => 'NULL',
        ]);

        return redirect()->route('admin.products.index')->with('success', 'Product successfully created!');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'productname' => 'required|string|max:255',
            'productdes' => 'required|string',
            'catid' => 'required|integer',
            'subcatid' => 'required|integer',
            'brandid' => 'required|integer',
            'unit' => 'required|integer',
            'pqty' => 'required|numeric',
            'tqty' => 'required|numeric',
            'pcode' => 'required|string|max:50',
            'pfrom' => 'required|string|max:255',
            'prate' => 'required|numeric',
            'srate' => 'required|numeric',
            'mrp' => 'required|numeric',
            'gst' => 'required|numeric',
            'cprice' => 'required|numeric',
            'dprice' => 'required|numeric',
            'sdprice' => 'required|numeric',
            'hsnsac' => 'required|string',
            'productimagef' => 'nullable|image|max:250',
            'productimages' => 'nullable|image|max:250',
            'productimaget' => 'nullable|image|max:250',
        ]);

        $product = Product::findOrFail($id);

        // Check for duplicate product code excluding this product
        if (Product::where('pcode', $request->input('pcode'))->where('id', '!=', $id)->exists()) {
            return redirect()->back()->withInput()->with('error', 'Already Existing Product Code!');
        }

        $legacyImageDir = public_path('productimage/' . $id . '/');

        if (!File::exists($legacyImageDir)) {
            File::makeDirectory($legacyImageDir, 0755, true);
        }

        $updates = [
            'brandid' => $request->input('brandid'),
            'catid' => $request->input('catid'),
            'subcatid' => $request->input('subcatid'),
            'productname' => strtoupper($request->input('productname')),
            'unit' => $request->input('unit'),
            'productdes' => strtoupper($request->input('productdes')),
            'pqty' => $request->input('pqty'),
            'tqty' => $request->input('tqty'),
            'pfrom' => strtoupper($request->input('pfrom')),
            'prate' => $request->input('prate'),
            'srate' => $request->input('srate'),
            'mrp' => $request->input('mrp'),
            'gst' => $request->input('gst'),
            'cprice' => $request->input('cprice'),
            'dprice' => $request->input('dprice'),
            'sdprice' => $request->input('sdprice'),
            'pcode' => $request->input('pcode'),
            'hsnsac' => $request->input('hsnsac'),
            'updationdate' => date('d-m-Y h:i:s A'),
        ];

        if ($request->hasFile('productimagef')) {
            // Delete old first.* files
            foreach (glob($legacyImageDir . 'first.*') as $oldFile) {
                @unlink($oldFile);
            }
            $file = $request->file('productimagef');
            $ext = $file->getClientOriginalExtension();
            $urlff = 'first.' . $ext;
            $file->move($legacyImageDir, $urlff);
            $updates['pimagef'] = $urlff;
        }

        if ($request->hasFile('productimages')) {
            // Delete old second.* files
            foreach (glob($legacyImageDir . 'second.*') as $oldFile) {
                @unlink($oldFile);
            }
            $file = $request->file('productimages');
            $ext = $file->getClientOriginalExtension();
            $urlss = 'second.' . $ext;
            $file->move($legacyImageDir, $urlss);
            $updates['pimages'] = $urlss;
        }

        if ($request->hasFile('productimaget')) {
            // Delete old third.* files
            foreach (glob($legacyImageDir . 'third.*') as $oldFile) {
                @unlink($oldFile);
            }
            $file = $request->file('productimaget');
            $ext = $file->getClientOriginalExtension();
            $urltt = 'third.' . $ext;
            $file->move($legacyImageDir, $urltt);
            $updates['pimaget'] = $urltt;
        }

        $product->update($updates);

        return redirect()->route('admin.products.index')->with('success', 'Product successfully updated!');
    }

    /**
     * Remove the specified resource from storage (soft delete).
     */
    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $product->update(['status' => 2]); // Status 2 = Out of stock/soft-deleted in legacy systems

        return redirect()->route('admin.products.index')->with('success', 'Product successfully removed!');
    }

    /**
     * Display a comprehensive stock list/inventory summary of active products.
     */
    public function stockList(Request $request)
    {
        $search = $request->input('search');
        $catId = $request->input('catid');
        $brandId = $request->input('brandid');
        $stockStatus = $request->input('stock_status');

        $query = Product::with(['category', 'subcategory', 'brand'])
            ->where('status', 1);

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('productname', 'like', "%{$search}%")
                  ->orWhere('pcode', 'like', "%{$search}%")
                  ->orWhere('productdes', 'like', "%{$search}%");
            });
        }

        if ($catId) {
            $query->where('catid', $catId);
        }

        if ($brandId) {
            $query->where('brandid', $brandId);
        }

        if ($stockStatus) {
            if ($stockStatus === 'high') {
                $query->where('tqty', '>', 15);
            } elseif ($stockStatus === 'medium') {
                $query->whereBetween('tqty', [6, 15]);
            } elseif ($stockStatus === 'low') {
                $query->whereBetween('tqty', [1, 5]);
            } elseif ($stockStatus === 'out') {
                $query->where('tqty', '<=', 0);
            }
        }

        $products = $query->orderBy('tqty', 'asc')->get();

        // Calculate summary cards metrics
        $allActiveCount = Product::where('status', 1)->count();
        $highStockCount = Product::where('status', 1)->where('tqty', '>', 15)->count();
        $mediumStockCount = Product::where('status', 1)->whereBetween('tqty', [6, 15])->count();
        $lowStockCount = Product::where('status', 1)->whereBetween('tqty', [1, 5])->count();
        $outOfStockCount = Product::where('status', 1)->where('tqty', '<=', 0)->count();

        $categories = Category::where('status', 1)->orderBy('cat_name', 'asc')->get();
        $brands = Brand::where('brand_status', 1)->orderBy('brand_name', 'asc')->get();

        return view('admin.products.stock_list', compact(
            'products', 
            'categories', 
            'brands', 
            'search', 
            'catId', 
            'brandId', 
            'stockStatus',
            'allActiveCount',
            'highStockCount',
            'mediumStockCount',
            'lowStockCount',
            'outOfStockCount'
        ));
    }

    /**
     * Display the Product Price Search page.
     */
    public function priceSearch(Request $request)
    {
        // Load all active products with stock > 0 sorted alphabetically by name
        $products = Product::where('status', 1)
            ->where('tqty', '>', 0)
            ->orderBy('productname', 'asc')
            ->get(['id', 'productname', 'pcode']);

        return view('admin.products.price_search', compact('products'));
    }

    /**
     * Get price tiers for a selected product as JSON.
     */
    public function getPriceDetails($id)
    {
        $product = Product::with(['category', 'brand', 'batches'])
            ->where('status', 1)
            ->find($id);

        if (!$product) {
            return response()->json(['error' => 'Product not found.'], 404);
        }

        // Filter active batches manually or reload with filter
        $activeBatches = $product->batches()
            ->where('status', 1)
            ->where('current_qty', '>', 0)
            ->orderBy('id', 'asc')
            ->get();

        return response()->json([
            'id' => $product->id,
            'pcode' => $product->pcode,
            'productname' => $product->productname,
            'productdes' => $product->productdes,
            'brand_name' => $product->brand ? $product->brand->brand_name : 'No Brand',
            'cat_name' => $product->category ? $product->category->cat_name : 'No Category',
            'prate' => $product->prate,
            'mrp' => $product->mrp,
            'cprice' => $product->cprice,
            'dprice' => $product->dprice,
            'sdprice' => $product->sdprice,
            'gst' => $product->gst,
            'tqty' => $product->tqty,
            'batches' => $activeBatches
        ]);
    }
}
