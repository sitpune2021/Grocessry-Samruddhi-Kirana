<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function showLogin() {
        return view('website.auth.login');
    }

    public function showRegister() {
        return view('website.auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'mobile' => 'required|digits:10|unique:users,mobile',
            'password' => 'required|min:6'
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'mobile' => $request->mobile,
            'password' => Hash::make($request->password),
        ]);

        Auth::login($user);

        return redirect()->route('checkout');
    }

    public function login(Request $request)
    {
        if (Auth::attempt($request->only('email','password'))) {
            return redirect()->route('shop');
        }

        return back()->with('error', 'Invalid login details');
    }

    public function websitelogout()
    {
        Auth::logout();
        return redirect('/');
    }
    
}
