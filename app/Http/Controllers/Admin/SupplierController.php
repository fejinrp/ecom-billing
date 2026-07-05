<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Supplier;

class SupplierController extends Controller
{
    /**
     * Display a listing of suppliers.
     */
    public function index(Request $request)
    {
        $query = Supplier::query();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('contact_person', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $suppliers = $query->orderBy('name', 'asc')->paginate(15);

        return view('admin.suppliers.index', compact('suppliers'));
    }

    /**
     * Store a newly created supplier in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:suppliers,name',
            'contact_person' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
        ]);

        $supplier = Supplier::create([
            'name' => strtoupper(trim($request->input('name'))),
            'contact_person' => strtoupper(trim($request->input('contact_person'))),
            'phone' => $request->input('phone'),
            'email' => strtolower(trim($request->input('email'))),
            'address' => strtoupper(trim($request->input('address'))),
            'status' => 1,
        ]);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'status' => 'success',
                'supplier' => $supplier
            ]);
        }

        return redirect()->route('admin.suppliers.index')->with('success', 'Supplier created successfully!');
    }

    /**
     * Update the specified supplier in storage.
     */
    public function update(Request $request, $id)
    {
        $supplier = Supplier::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255|unique:suppliers,name,' . $id,
            'contact_person' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
        ]);

        $supplier->update([
            'name' => strtoupper(trim($request->input('name'))),
            'contact_person' => strtoupper(trim($request->input('contact_person'))),
            'phone' => $request->input('phone'),
            'email' => strtolower(trim($request->input('email'))),
            'address' => strtoupper(trim($request->input('address'))),
        ]);

        return redirect()->route('admin.suppliers.index')->with('success', 'Supplier updated successfully!');
    }

    /**
     * Toggle status of the supplier.
     */
    public function toggleStatus($id)
    {
        $supplier = Supplier::findOrFail($id);
        $supplier->status = $supplier->status == 1 ? 0 : 1;
        $supplier->save();

        return redirect()->route('admin.suppliers.index')->with('success', 'Supplier status updated successfully!');
    }

    /**
     * Remove the specified supplier from storage.
     */
    public function destroy($id)
    {
        $supplier = Supplier::findOrFail($id);
        $supplier->delete();

        return redirect()->route('admin.suppliers.index')->with('success', 'Supplier deleted successfully!');
    }
}
