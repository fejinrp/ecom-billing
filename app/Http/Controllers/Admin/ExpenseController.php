<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Expdetail;
use App\Models\Expname;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ExpenseController extends Controller
{
    /**
     * Display a listing of daily expenses.
     */
    public function index(Request $request)
    {
        $query = Expdetail::with(['category', 'staff'])->where('estatus', 1);

        // Search filter
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                // Try parsing date
                try {
                    $date = Carbon::parse($search)->format('Y-m-d');
                    $q->whereDate('exp_date', $date);
                } catch (\Exception $e) {
                    // Fail silently to search text instead
                }

                $q->orWhereHas('category', function($cq) use ($search) {
                    $cq->where('exp_name', 'like', "%{$search}%");
                })->orWhereHas('staff', function($sq) use ($search) {
                    $sq->where('username', 'like', "%{$search}%");
                });
            });
        }

        $expenses = $query->orderBy('exp_id', 'desc')->paginate(15);
        $categories = Expname::where('estatus', 1)->orderBy('exp_name', 'asc')->get();

        return view('admin.expenses.index', compact('expenses', 'categories'));
    }

    /**
     * Store a newly created expense in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'eDate' => 'required|date',
            'eName' => 'required|integer',
            'eAmount' => 'required|numeric|min:0.01',
        ]);

        $user = Auth::guard('admin')->user();

        Expdetail::create([
            'exp_date' => Carbon::parse($request->input('eDate'))->format('Y-m-d'),
            'exp_name' => $request->input('eName'),
            'exp_amount' => $request->input('eAmount'),
            'sname' => $user->user_id,
            'mexp_id' => 0,
            'estatus' => 1
        ]);

        return redirect()->route('admin.expenses.index')->with('success', 'Expense successfully logged!');
    }

    /**
     * Update the specified expense in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'editDate' => 'required|date',
            'editName' => 'required|integer',
            'editAmount' => 'required|numeric|min:0.01',
        ]);

        $expense = Expdetail::findOrFail($id);

        $expense->update([
            'exp_date' => Carbon::parse($request->input('editDate'))->format('Y-m-d'),
            'exp_name' => $request->input('editName'),
            'exp_amount' => $request->input('editAmount')
        ]);

        return redirect()->route('admin.expenses.index')->with('success', 'Expense successfully updated!');
    }

    /**
     * Soft delete/deactivate the specified expense from database.
     */
    public function destroy($id)
    {
        $expense = Expdetail::findOrFail($id);

        // Soft delete matching legacy behavior (estatus = 2)
        $expense->update([
            'estatus' => 2
        ]);

        return redirect()->route('admin.expenses.index')->with('success', 'Expense successfully removed!');
    }
}
