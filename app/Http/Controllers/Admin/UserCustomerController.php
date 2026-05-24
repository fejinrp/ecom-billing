<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserCustomerController extends Controller
{
    /**
     * Display a listing of storefront customers and dealers (ref: admin/usercustomer.php).
     */
    public function index()
    {
        $users = User::orderBy('uname', 'asc')->get();

        return view('admin.customers.index', compact('users'));
    }

    /**
     * Store a newly created storefront customer or dealer.
     */
    public function store(Request $request)
    {
        $request->validate([
            'uname' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'contactno' => 'required|string|max:20|unique:users,contactno',
            'password' => 'required|string|min:6',
            'usertype' => 'required|string|in:C,D,S', // C = Customer, D = Dealer, S = Super Dealer
        ]);

        User::create([
            'uname' => $request->input('uname'),
            'email' => $request->input('email'),
            'contactno' => $request->input('contactno'),
            'password' => Hash::make($request->input('password')),
            'usertype' => $request->input('usertype'),
            'ustatus' => 1, // Default to Active
            'regdate' => date('Y-m-d H:i:s'),
        ]);

        return redirect()->route('admin.customers.index')->with('success', 'Online Customer/Dealer account created successfully!');
    }

    /**
     * Update the specified storefront customer or dealer.
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'uname' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $id,
            'contactno' => 'required|string|max:20|unique:users,contactno,' . $id,
            'password' => 'nullable|string|min:6',
            'usertype' => 'required|string|in:C,D,S',
        ]);

        $user->uname = $request->input('uname');
        $user->email = $request->input('email');
        $user->contactno = $request->input('contactno');
        $user->usertype = $request->input('usertype');

        if ($request->filled('password')) {
            $user->password = Hash::make($request->input('password'));
        }

        $user->updationdate = date('Y-m-d H:i:s');
        $user->save();

        return redirect()->route('admin.customers.index')->with('success', 'Online Customer/Dealer account updated successfully!');
    }

    /**
     * Toggle status (active/deactive) of the storefront customer or dealer.
     */
    public function toggleStatus($id)
    {
        $user = User::findOrFail($id);

        // Toggle status: 1 = Active, 2 = Inactive
        if ($user->ustatus == 1) {
            $user->ustatus = 2;
            $message = 'Online account deactivated successfully!';
        } else {
            $user->ustatus = 1;
            $message = 'Online account activated successfully!';
        }

        $user->save();

        return redirect()->route('admin.customers.index')->with('success', $message);
    }
}
