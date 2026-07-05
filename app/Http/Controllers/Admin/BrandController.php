<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Subcategory;

class BrandController extends Controller
{
    /**
     * Display a listing of brands.
     */
    public function index()
    {
        $brands = Brand::with(['category', 'subcategory'])
            ->where('brand_status', 1)
            ->orderBy('brand_name', 'asc')
            ->paginate(15);

        $categories = Category::where('status', 1)
            ->orderBy('cat_name', 'asc')
            ->get();

        $subcategories = Subcategory::where('status', 1)
            ->orderBy('subcategoryname', 'asc')
            ->get();

        return view('admin.brands.index', compact('brands', 'categories', 'subcategories'));
    }

    /**
     * Store a newly created brand.
     */
    public function store(Request $request)
    {
        $request->validate([
            'brand_name' => 'required|string|max:255',
            'catid' => 'required|integer',
            'scatid' => 'required|integer',
        ]);

        $brand = Brand::create([
            'brand_name' => $request->input('brand_name'),
            'catid' => $request->input('catid'),
            'scatid' => $request->input('scatid'),
            'brand_status' => 1,
        ]);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'status' => 'success',
                'brand' => $brand
            ]);
        }

        return redirect()->route('admin.brands.index')->with('success', 'Brand successfully created!');
    }

    /**
     * Update the specified brand.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'brand_name' => 'required|string|max:255',
            'catid' => 'required|integer',
            'scatid' => 'required|integer',
        ]);

        $brand = Brand::findOrFail($id);
        $brand->update([
            'brand_name' => $request->input('brand_name'),
            'catid' => $request->input('catid'),
            'scatid' => $request->input('scatid'),
        ]);

        return redirect()->route('admin.brands.index')->with('success', 'Brand successfully updated!');
    }

    /**
     * Soft delete/remove the specified brand.
     */
    public function destroy($id)
    {
        $brand = Brand::findOrFail($id);
        $brand->update(['brand_status' => 2]); // Status 2 = deleted/inactive in legacy systems

        return redirect()->route('admin.brands.index')->with('success', 'Brand successfully removed!');
    }
}
