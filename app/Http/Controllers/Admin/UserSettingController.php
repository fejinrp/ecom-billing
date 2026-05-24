<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Usercheck;
use App\Models\Auser;

class UserSettingController extends Controller
{
    /**
     * Display the permissions matrix grid (ref: admin/usersetting.php).
     */
    public function index()
    {
        // Fetch all active admin/executive users joined with their permission checkers
        // If a user doesn't have a usercheck record, we create it dynamically to keep settings functional
        $activeUsers = Auser::where('ustatus', 1)->orderBy('username', 'asc')->get();

        foreach ($activeUsers as $user) {
            $exists = Usercheck::where('uid', $user->user_id)->exists();
            if (!$exists) {
                Usercheck::create([
                    'uid' => $user->user_id,
                    'cat' => 0, 'scat' => 0, 'brand' => 0, 'prod' => 0, 'mprod' => 0,
                    'purc' => 0, 'mpurc' => 0, 'astock' => 0, 'slist' => 0, 'sprice' => 0,
                    'cinv' => 0, 'minv' => 0, 'linvc' => 0, 'quot' => 0, 'mquot' => 0,
                    'estm' => 0, 'mestm' => 0, 'ord' => 0, 'sord' => 0, 'dord' => 0,
                    'cord' => 0, 'expen' => 0, 'expd' => 0, 'agent' => 0, 'apay' => 0,
                    'areport' => 0, 'breport' => 0, 'sreport' => 0, 'preport' => 0,
                    'stockr' => 0, 'phistory' => 0, 'excel' => 0, 'auser' => 0,
                    'usett' => 0, 'csett' => 0, 'backup' => 0, 'restore' => 0
                ]);
            }
        }

        $userchecks = Usercheck::select('usercheck.*', 'ausers.username', 'ausers.section')
            ->join('ausers', 'ausers.user_id', '=', 'usercheck.uid')
            ->where('ausers.ustatus', 1)
            ->orderBy('ausers.username', 'asc')
            ->get();

        return view('admin.usersettings.index', compact('userchecks'));
    }

    /**
     * Update/toggle a specific user's module permission (ref: php_action/editUserCheck.php).
     */
    public function updatePermission(Request $request)
    {
        $request->validate([
            'id' => 'required|integer',
            'column' => 'required|string',
            'value' => 'required|integer'
        ]);

        $column = $request->input('column');
        
        // If updating the user role (section)
        if ($column === 'section') {
            $usercheck = Usercheck::findOrFail($request->input('id'));
            $user = Auser::where('user_id', $usercheck->uid)->firstOrFail();
            $user->section = $request->input('value');
            $user->save();
            return response()->json([
                'success' => true,
                'message' => 'Role updated successfully!'
            ]);
        }

        $validColumns = [
            'cat', 'scat', 'brand', 'prod', 'mprod', 'purc', 'mpurc', 'astock', 'slist',
            'sprice', 'cinv', 'minv', 'linvc', 'quot', 'mquot', 'estm', 'mestm', 'ord',
            'sord', 'dord', 'cord', 'expen', 'expd', 'agent', 'apay', 'areport', 'breport',
            'sreport', 'preport', 'stockr', 'phistory', 'excel', 'auser', 'usett', 'csett',
            'backup', 'restore'
        ];

        if (!in_array($column, $validColumns)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid permission field requested.'
            ], 400);
        }

        $usercheck = Usercheck::findOrFail($request->input('id'));
        $usercheck->$column = $request->input('value');
        $usercheck->save();

        return response()->json([
            'success' => true,
            'message' => 'Permission updated successfully!'
        ]);
    }
}
