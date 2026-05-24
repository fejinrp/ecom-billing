<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Agent;

class AgentController extends Controller
{
    /**
     * Display a listing of agents.
     */
    public function index()
    {
        $agents = Agent::where('astatus', 1)
            ->orderBy('adate', 'desc')
            ->orderBy('acode', 'desc')
            ->paginate(15);

        return view('admin.agents.index', compact('agents'));
    }

    /**
     * Store a newly created agent.
     */
    public function store(Request $request)
    {
        $request->validate([
            'aname' => 'required|string|max:255',
            'aplace' => 'required|string|max:255',
            'amobile' => 'required|string|max:20',
            'adate' => 'required|date',
        ]);

        $aname = strtoupper(trim($request->input('aname')));
        $aplace = strtoupper(trim($request->input('aplace')));

        // Check for duplicate agent name
        if (Agent::where('aname', $aname)->where('astatus', 1)->exists()) {
            return redirect()->back()->withInput()->with('error', 'Agent with this name already exists!');
        }

        // Generate acode - in legacy this might be auto-increment or a specific generation logic.
        // Let's check if the DB table has auto-increment. In the migration it was $table->integer('acode') without auto-increment.
        // Let's generate a unique acode just in case, e.g. Max(acode) + 1, or let DB handle if it's auto-incrementing.
        // To be safe, we will calculate next acode if it's not auto-incrementing, or let the database auto-increment if configured.
        $nextAcode = (Agent::max('acode') ?? 0) + 1;

        Agent::create([
            'acode' => $nextAcode,
            'aname' => $aname,
            'aplace' => $aplace,
            'amobile' => trim($request->input('amobile')),
            'adate' => $request->input('adate'),
            'astatus' => 1, // 1 = Active
        ]);

        return redirect()->route('admin.agents.index')->with('success', 'Agent successfully created!');
    }

    /**
     * Update the specified agent.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'aname' => 'required|string|max:255',
            'aplace' => 'required|string|max:255',
            'amobile' => 'required|string|max:20',
            'adate' => 'required|date',
        ]);

        $aname = strtoupper(trim($request->input('aname')));
        $aplace = strtoupper(trim($request->input('aplace')));

        // Check for duplicate agent name excluding current record
        if (Agent::where('aname', $aname)->where('acode', '!=', $id)->where('astatus', 1)->exists()) {
            return redirect()->back()->withInput()->with('error', 'Agent with this name already exists!');
        }

        $agent = Agent::findOrFail($id);
        $agent->update([
            'aname' => $aname,
            'aplace' => $aplace,
            'amobile' => trim($request->input('amobile')),
            'adate' => $request->input('adate'),
        ]);

        return redirect()->route('admin.agents.index')->with('success', 'Agent successfully updated!');
    }

    /**
     * Soft delete/remove the specified agent.
     */
    public function destroy($id)
    {
        $agent = Agent::findOrFail($id);
        $agent->update(['astatus' => 2]); // Set astatus to 2 to mark it as deleted/inactive

        return redirect()->route('admin.agents.index')->with('success', 'Agent successfully removed!');
    }
}
