<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;

class CategoryController extends Controller
{
    /**
     * Display a listing of categories.
     */
    public function index()
    {
        $categories = Category::where('status', 1)
            ->orderBy('cat_name', 'asc')
            ->get();

        return view('admin.categories.index', compact('categories'));
    }

    /**
     * Store a newly created category.
     */
    public function store(Request $request)
    {
        $request->validate([
            'cat_name' => 'required|string|max:255',
        ]);

        $category = Category::create([
            'cat_name' => $request->input('cat_name'),
            'creation_date' => date('Y-m-d H:i:s'),
            'status' => 1,
        ]);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'status' => 'success',
                'category' => $category
            ]);
        }

        return redirect()->route('admin.categories.index')->with('success', 'Category successfully created!');
    }

    /**
     * Update the specified category.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'cat_name' => 'required|string|max:255',
        ]);

        $category = Category::findOrFail($id);
        $category->update([
            'cat_name' => $request->input('cat_name'),
        ]);

        return redirect()->route('admin.categories.index')->with('success', 'Category successfully updated!');
    }

    /**
     * Remove the specified category (soft delete).
     */
    public function destroy($id)
    {
        $category = Category::findOrFail($id);
        $category->update(['status' => 2]); // Status 2 = deleted/inactive in legacy systems

        return redirect()->route('admin.categories.index')->with('success', 'Category successfully removed!');
    }
}
