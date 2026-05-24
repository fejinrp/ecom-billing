<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Auser;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of administrative users (ref: admin/user.php).
     */
    public function index()
    {
        // Exclude the currently authenticated super admin to prevent self-deletion or self-lockout
        $users = Auser::orderBy('username', 'asc')->get();

        return view('admin.users.index', compact('users'));
    }

    /**
     * Store a newly created administrative user.
     */
    public function store(Request $request)
    {
        $request->validate([
            'username' => 'required|string|max:255|unique:ausers,username',
            'email' => 'required|email|max:255|unique:ausers,email',
            'mobile' => 'required|string|max:20|unique:ausers,mobile',
            'password' => 'required|string|min:6',
            'section' => 'required|integer|in:1,2', // 1 = Executive (Super Admin/Executive), etc.
        ]);

        Auser::create([
            'username' => $request->input('username'),
            'email' => $request->input('email'),
            'mobile' => $request->input('mobile'),
            'password' => Hash::make($request->input('password')),
            'section' => $request->input('section'),
            'ustatus' => 1, // Default to Active
        ]);

        return redirect()->route('admin.users.index')->with('success', 'User successfully created!');
    }

    /**
     * Update the specified administrative user.
     */
    public function update(Request $request, $id)
    {
        $user = Auser::findOrFail($id);

        $request->validate([
            'username' => 'required|string|max:255|unique:ausers,username,' . $id . ',user_id',
            'email' => 'required|email|max:255|unique:ausers,email,' . $id . ',user_id',
            'mobile' => 'required|string|max:20|unique:ausers,mobile,' . $id . ',user_id',
            'password' => 'nullable|string|min:6',
            'section' => 'required|integer|in:1,2',
        ]);

        $user->username = $request->input('username');
        $user->email = $request->input('email');
        $user->mobile = $request->input('mobile');
        $user->section = $request->input('section');

        if ($request->filled('password')) {
            $user->password = Hash::make($request->input('password'));
        }

        $user->save();

        return redirect()->route('admin.users.index')->with('success', 'User successfully updated!');
    }

    /**
     * Toggle active/inactive status of the administrative user (ref: active/deactive user).
     */
    public function toggleStatus($id)
    {
        $user = Auser::findOrFail($id);

        // Toggle status: 1 = Active, 2 = Inactive
        if ($user->ustatus == 1) {
            $user->ustatus = 2;
            $message = 'User account deactivated successfully!';
        } else {
            $user->ustatus = 1;
            $message = 'User account activated successfully!';
        }

        $user->save();

        return redirect()->route('admin.users.index')->with('success', $message);
    }
}
