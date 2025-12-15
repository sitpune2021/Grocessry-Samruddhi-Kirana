<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AdminAuthController extends Controller
{

    public function loginForm()
    {
        return view('admin-login.auth-login');
    }

    public function login(Request $request)
    {
        try {

            $request->validate([
                'email' => 'required|email',
                'password' => 'required'
            ]);

            $admin = User::where('email', $request->email)->first();

            if (!$admin || !Hash::check($request->password, $admin->password)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid email or password'
                ], 401);
            }

            $token = $admin->createToken('adminToken')->plainTextToken;

            return back()->with('success', 'Successfully logged in!');
        } catch (\Exception $e) {

            Log::error("LOGIN ERROR : " . $e->getMessage());

            return back()->with('error', 'Invalid details!');
        }
    }


    public function logout(Request $request)
    {
        try {

            $request->user()->currentAccessToken()->delete();

            return response()->json([
                'status' => true,
                'message' => 'Logout successfully'
            ]);
        } catch (\Exception $e) {

            Log::error("LOGOUT ERROR : " . $e->getMessage());

            return response()->json([
                'status' => false,
                'message' => 'Something went wrong',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
