<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    // public function login(Request $request)
    // {
    //     dd("hi");
    //     // Validate input
    //     $request->validate([
    //         'email' => 'required|email',
    //         'password' => 'required',
    //     ]);
    //     $details=User::all();
    //     dd( $details);
    // }



    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $token = $user->createToken('API_TOKEN')->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'token' => $token,
            'user' => $user
        ]);
    }

    public function logout(Request $request)
    {
        // Delete the token used in this request
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status' => true,
            'message' => 'Logged out successfully'
        ]);
    }


}