<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Expname;

class ExpenseCategoryController extends Controller
{
    /**
     * Display a listing of expense categories.
     */
    public function index()
    {
        $categories = Expname::where('estatus', 1)
            ->orderBy('exp_name', 'asc')
            ->get();

        return view('admin.expenses.categories', compact('categories'));
    }

    /**
     * Store a newly created expense category.
     */
    public function store(Request $request)
    {
        $request->validate([
            'exp_name' => 'required|string|max:255',
        ]);

        Expname::create([
            'exp_name' => $request->input('exp_name'),
            'estatus' => 1,
        ]);

        return redirect()->route('admin.expenses.categories.index')->with('success', 'Expense Category successfully created!');
    }

    /**
     * Update the specified expense category.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'exp_name' => 'required|string|max:255',
        ]);

        $category = Expname::findOrFail($id);
        $category->update([
            'exp_name' => $request->input('exp_name'),
        ]);

        return redirect()->route('admin.expenses.categories.index')->with('success', 'Expense Category successfully updated!');
    }

    /**
     * Remove the specified expense category (soft delete).
     */
    public function destroy($id)
    {
        $category = Expname::findOrFail($id);
        $category->update(['estatus' => 2]); // Status 2 = deleted/inactive in legacy systems

        return redirect()->route('admin.expenses.categories.index')->with('success', 'Expense Category successfully removed!');
    }
}
