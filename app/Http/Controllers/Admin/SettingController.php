<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class SettingController extends Controller
{
    /**
     * Display the settings dashboard (ref: admin/setting.php).
     */
    public function index()
    {
        $user = Auth::guard('admin')->user();
        return view('admin.settings.index', compact('user'));
    }

    /**
     * Update the authenticated administrator's profile information.
     */
    public function updateUsername(Request $request)
    {
        $user = Auth::guard('admin')->user();

        $request->validate([
            'username' => 'required|string|max:255|unique:ausers,username,' . $user->user_id . ',user_id',
            'email' => 'required|email|max:255|unique:ausers,email,' . $user->user_id . ',user_id',
            'mobile' => 'required|string|max:20',
        ]);

        $user->username = $request->input('username');
        $user->email = $request->input('email');
        $user->mobile = $request->input('mobile');
        $user->save();

        // Update username in session if required (like AdminAuthController did)
        $request->session()->put('username', $user->username);

        return redirect()->route('admin.settings.index')->with('success', 'Profile information updated successfully!');
    }

    /**
     * Change the authenticated administrator's password securely.
     */
    public function updatePassword(Request $request)
    {
        $user = Auth::guard('admin')->user();

        $request->validate([
            'current_password' => 'required|string',
            'password' => 'required|string|min:6|confirmed',
        ]);

        // Verify current password
        // Supports legacy MD5 checks as a fallback just in case some legacy admin accounts still have MD5 passwords
        $currentPasswordInput = $request->input('current_password');
        $isCorrect = Hash::check($currentPasswordInput, $user->password) || (md5($currentPasswordInput) === $user->password);

        if (!$isCorrect) {
            return redirect()->route('admin.settings.index')->with('error', 'The provided current password does not match our records.');
        }

        // Save using secure Laravel default bcrypt hashing
        $user->password = Hash::make($request->input('password'));
        $user->save();

        return redirect()->route('admin.settings.index')->with('success', 'Password updated successfully!');
    }
}
