<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AdminAuthController extends Controller
{
    /**
     * Show the admin login view.
     */
    public function showLoginForm()
    {
        if (Auth::guard('admin')->check()) {
            return redirect()->route('admin.dashboard');
        }
        return view('admin.auth.login');
    }

    /**
     * Handle administrative login.
     */
    public function login(Request $request)
    {
        $request->validate([
            'login_field' => 'required|string',
            'password' => 'required|string',
        ]);

        $loginField = $request->input('login_field');
        $credentials = [
            'password' => $request->input('password'),
            'ustatus' => 1, // Only active accounts
        ];

        // Determine if logging in via email or username
        if (filter_var($loginField, FILTER_VALIDATE_EMAIL)) {
            $credentials['email'] = $loginField;
        } else {
            $credentials['username'] = $loginField;
        }

        if (Auth::guard('admin')->attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();
            
            // Log the successful login session info (like legacy section)
            $user = Auth::guard('admin')->user();
            $request->session()->put('usermtl', $user->user_id);
            $request->session()->put('section', $user->section);
            $request->session()->put('username', $user->username);

            return redirect()->intended(route('admin.dashboard'));
        }

        throw ValidationException::withMessages([
            'login_field' => __('auth.failed'),
        ]);
    }

    /**
     * Log out administrative user.
     */
    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();

        $request->session()->forget(['usermtl', 'section', 'username']);
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }
}
