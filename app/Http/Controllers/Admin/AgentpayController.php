<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Agent;
use App\Models\Agentpay;

class AgentpayController extends Controller
{
    /**
     * Display a listing of agent payments.
     */
    public function index()
    {
        $agents = Agent::where('astatus', 1)
            ->orderBy('aname', 'asc')
            ->get();

        $paymentsQuery = Agentpay::with('agent');

        $totalCount = (clone $paymentsQuery)->count();
        $totalAmount = (clone $paymentsQuery)->sum('pamount');

        // Log count for verification in storage/logs/laravel.log
        \Illuminate\Support\Facades\Log::info("Agent payments loaded: Count = {$totalCount}, Sum = {$totalAmount}");

        $payments = $paymentsQuery
            ->orderBy('pdate', 'desc')
            ->orderBy('payid', 'desc')
            ->paginate(15);

        return view('admin.agents.payments', compact('payments', 'agents', 'totalCount', 'totalAmount'));
    }

    /**
     * Store a newly created agent payment.
     */
    public function store(Request $request)
    {
        $request->validate([
            'acode' => 'required|exists:agent,acode',
            'pamount' => 'required|numeric|min:0.01',
            'pdate' => 'required|date',
        ]);

        $nextPayid = (Agentpay::max('payid') ?? 0) + 1;

        Agentpay::create([
            'payid' => $nextPayid,
            'acode' => $request->input('acode'),
            'pamount' => $request->input('pamount'),
            'pdate' => $request->input('pdate'),
        ]);

        return redirect()->route('admin.agents_payments.index')->with('success', 'Agent payment successfully recorded!');
    }

    /**
     * Update the specified agent payment.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'pamount' => 'required|numeric|min:0.01',
            'pdate' => 'required|date',
        ]);

        $payment = Agentpay::findOrFail($id);
        $payment->update([
            'pamount' => $request->input('pamount'),
            'pdate' => $request->input('pdate'),
        ]);

        return redirect()->route('admin.agents_payments.index')->with('success', 'Agent payment successfully updated!');
    }

    /**
     * Remove the specified agent payment.
     */
    public function destroy($id)
    {
        $payment = Agentpay::findOrFail($id);
        $payment->delete();

        return redirect()->route('admin.agents_payments.index')->with('success', 'Agent payment successfully removed!');
    }
}
