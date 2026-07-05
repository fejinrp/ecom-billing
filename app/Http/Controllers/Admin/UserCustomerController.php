<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserCustomerController extends Controller
{
    /**
     * Display a listing of storefront customers and dealers (ref: admin/usercustomer.php).
     */
    public function index(Request $request)
    {
        $query = User::query();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('uname', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('contactno', 'like', "%{$search}%");
            });
        }

        $users = $query->orderBy('uname', 'asc')->paginate(15)->withQueryString();

        return view('admin.customers.index', compact('users'));
    }

    /**
     * Store a newly created storefront customer or dealer.
     */
    public function store(Request $request)
    {
        $messages = [
            'uname.required'     => 'Customer name is required.',
            'uname.unique'       => 'This username is already taken. Please choose a different name.',
            'email.required'     => 'Email address is required.',
            'email.email'        => 'Please enter a valid email address.',
            'email.unique'       => 'This email address is already registered to another account.',
            'contactno.required' => 'Mobile number is required.',
            'contactno.unique'   => 'This mobile number is already registered to another account.',
            'password.required'  => 'Password is required.',
            'password.min'       => 'Password must be at least 6 characters.',
            'usertype.required'  => 'Please select a user type.',
        ];

        $validator = Validator::make($request->all(), [
            'uname'     => 'required|string|max:255|unique:users,uname',
            'email'     => 'required|email|max:255|unique:users,email',
            'contactno' => 'required|string|max:20|unique:users,contactno',
            'password'  => 'required|string|min:6',
            'usertype'  => 'required|string|in:C,D,S',
        ], $messages);

        if ($validator->fails()) {
            return redirect()->route('admin.customers.index')
                ->withErrors($validator)
                ->withInput()
                ->with('reopen_modal', 'add');
        }

        User::create([
            'uname'     => $request->input('uname'),
            'email'     => $request->input('email'),
            'contactno' => $request->input('contactno'),
            'password'  => Hash::make($request->input('password')),
            'usertype'  => $request->input('usertype'),
            'ustatus'   => 1,
            'regdate'   => date('Y-m-d H:i:s'),
        ]);

        return redirect()->route('admin.customers.index')->with('success', 'Online Customer/Dealer account created successfully!');
    }

    /**
     * Update the specified storefront customer or dealer.
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $messages = [
            'uname.required'     => 'Customer name is required.',
            'uname.unique'       => 'This username is already taken. Please choose a different name.',
            'email.required'     => 'Email address is required.',
            'email.email'        => 'Please enter a valid email address.',
            'email.unique'       => 'This email address is already registered to another account.',
            'contactno.required' => 'Mobile number is required.',
            'contactno.unique'   => 'This mobile number is already registered to another account.',
            'password.min'       => 'Password must be at least 6 characters.',
            'usertype.required'  => 'Please select a user type.',
        ];

        $validator = Validator::make($request->all(), [
            'uname'     => 'required|string|max:255|unique:users,uname,' . $id,
            'email'     => 'required|email|max:255|unique:users,email,' . $id,
            'contactno' => 'required|string|max:20|unique:users,contactno,' . $id,
            'password'  => 'nullable|string|min:6',
            'usertype'  => 'required|string|in:C,D,S',
        ], $messages);

        if ($validator->fails()) {
            return redirect()->route('admin.customers.index')
                ->withErrors($validator)
                ->withInput()
                ->with('reopen_modal', 'edit');
        }

        $user->uname     = $request->input('uname');
        $user->email     = $request->input('email');
        $user->contactno = $request->input('contactno');
        $user->usertype  = $request->input('usertype');

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
