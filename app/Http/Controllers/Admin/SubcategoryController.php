<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Subcategory;

class SubcategoryController extends Controller
{
    /**
     * Display a listing of subcategories.
     */
    public function index()
    {
        $subcategories = Subcategory::with('category')
            ->where('status', 1)
            ->orderBy('subcategoryname', 'asc')
            ->get();

        $categories = Category::where('status', 1)
            ->orderBy('cat_name', 'asc')
            ->get();

        return view('admin.subcategories.index', compact('subcategories', 'categories'));
    }

    /**
     * Store a newly created subcategory.
     */
    public function store(Request $request)
    {
        $request->validate([
            'subcategoryname' => 'required|string|max:255',
            'catid' => 'required|integer',
        ]);

        $subcategory = Subcategory::create([
            'subcategoryname' => $request->input('subcategoryname'),
            'catid' => $request->input('catid'),
            'creationdate' => date('Y-m-d H:i:s'),
            'status' => 1,
        ]);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'status' => 'success',
                'subcategory' => $subcategory
            ]);
        }

        return redirect()->route('admin.subcategories.index')->with('success', 'Subcategory successfully created!');
    }

    /**
     * Update the specified subcategory.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'subcategoryname' => 'required|string|max:255',
            'catid' => 'required|integer',
        ]);

        $subcategory = Subcategory::findOrFail($id);
        $subcategory->update([
            'subcategoryname' => $request->input('subcategoryname'),
            'catid' => $request->input('catid'),
        ]);

        return redirect()->route('admin.subcategories.index')->with('success', 'Subcategory successfully updated!');
    }

    /**
     * Remove the specified subcategory (soft delete).
     */
    public function destroy($id)
    {
        $subcategory = Subcategory::findOrFail($id);
        $subcategory->update(['status' => 2]); // Status 2 = deleted/inactive in legacy systems

        return redirect()->route('admin.subcategories.index')->with('success', 'Subcategory successfully removed!');
    }
}
