<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Stategst;

class StategstController extends Controller
{
    /**
     * Display a listing of State GST records.
     */
    public function index()
    {
        $stategsts = Stategst::where('status', 0)
            ->orderBy('sname', 'asc')
            ->paginate(15);

        return view('admin.stategst.index', compact('stategsts'));
    }

    /**
     * Store a newly created State GST record.
     */
    public function store(Request $request)
    {
        $request->validate([
            'sname' => 'required|string|max:255',
            'scode' => 'required|string|max:10',
        ]);

        $sname = strtoupper(trim($request->input('sname')));

        // Check for duplicate state name
        if (Stategst::where('sname', $sname)->where('status', 0)->exists()) {
            return redirect()->back()->withInput()->with('error', 'Already Existing State Name!');
        }

        Stategst::create([
            'sname' => $sname,
            'scode' => trim($request->input('scode')),
            'status' => 0, // 0 = Active in legacy schema
        ]);

        return redirect()->route('admin.stategst.index')->with('success', 'State GST successfully created!');
    }

    /**
     * Update the specified State GST record.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'sname' => 'required|string|max:255',
            'scode' => 'required|string|max:10',
        ]);

        $sname = strtoupper(trim($request->input('sname')));

        // Check for duplicate state name excluding current record
        if (Stategst::where('sname', $sname)->where('sid', '!=', $id)->where('status', 0)->exists()) {
            return redirect()->back()->withInput()->with('error', 'Already Existing State Name!');
        }

        $stategst = Stategst::findOrFail($id);
        $stategst->update([
            'sname' => $sname,
            'scode' => trim($request->input('scode')),
        ]);

        return redirect()->route('admin.stategst.index')->with('success', 'State GST successfully updated!');
    }

    /**
     * Soft delete/remove the specified State GST record.
     */
    public function destroy($id)
    {
        $stategst = Stategst::findOrFail($id);
        $stategst->update(['status' => 1]); // Set status to 1 to mark it as inactive

        return redirect()->route('admin.stategst.index')->with('success', 'State GST successfully removed!');
    }
}
