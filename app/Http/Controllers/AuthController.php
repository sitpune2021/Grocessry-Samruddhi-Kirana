<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('website.auth.login');
    }

    public function showRegister()
    {
        return view('website.auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'first_name' => 'required',
            'last_name'  => 'required',
            'email'      => 'required|email|unique:users,email',
            'mobile'     => 'required|digits:10|unique:users,mobile',
            'password'   => 'required|min:6'
        ]);

        $customerRoleId = Role::where('name', 'Customer')->value('id');

        $user = User::create([
            'first_name' => $request->first_name,
            'last_name'  => $request->last_name,
            'email'      => $request->email,
            'mobile'     => $request->mobile,
            'password'   => Hash::make($request->password),
            'role_id'    => $customerRoleId,
            'status'     => 1,
        ]);

        Auth::login($user);

        return redirect()->route('home');
    }

    public function login(Request $request)
    {
        // ✅ Validation
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|min:6',
        ], [
            'email.required' => 'Email address is required.',
            'email.email'    => 'Please enter a valid email address.',
            'password.required' => 'Password is required.',
            'password.min' => 'Password must be at least 6 characters long.',
        ]);


        $customerRoleId = Role::where('name', 'Customer')->value('id');

        // Safety check
        if (!$customerRoleId) {
            return back()->with('error', 'Customer role Not Found');
        }

        // ✅ Only Customer login allowed
        if (Auth::guard('web')->attempt([
            'email'    => $request->email,
            'password' => $request->password,
            'role_id'  => $customerRoleId,
            'status'   => 1,
        ])) {
            $request->session()->regenerate();
            return redirect()->route('home');
        }

        return back()->with('error', 'Invalid credentials & Customer account Not Found');
    }
    public function websitelogout()
    {
        Auth::guard('web')->logout();
        return redirect('/');
    }
}
