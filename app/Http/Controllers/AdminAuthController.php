<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;

class AdminAuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);
        $admin = User::where('email', $request->email)->first();

        if (!$admin || !Hash::check($request->password, $admin->password)) {
            return response()->json([
                'message' => 'Invalid email or password'
            ], 401);
        }

        $token = $admin->createToken('adminToken')->plainTextToken;


        return response()->json([
            'message' => 'Login successful',
            'token' => $token,
            'admin' => $admin
        ], 200);
    }

     public function logout(Request $request)
    {
        // Delete the token used in this request
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status' => true,
            'message' => 'Logout successfully'
        ]);
    }

}
