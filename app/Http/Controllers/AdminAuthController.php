<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class AdminAuthController extends Controller
{

    public function loginForm()
    {
        return view('admin-login.auth-login');
    }
    public function login(Request $request)
    {
        try {
            // Validate request
            $request->validate([
                'email'    => 'required|email',
                'password' => 'required',
            ]);

            Log::info('Login attempt for email: ' . $request->email);

            // Check if user exists
            $user = User::where('email', $request->email)->first();

            if (!$user) {
                // Email not found, but let's check if password matches any user for combined error
                $passwordExists = User::where('password', Hash::make($request->password))->exists(); // optional, usually not secure to check
                return redirect()->back()->withErrors([
                    'email' => ' Incorrect email',
                    'password' => $passwordExists ? null : 'Incorrect password'
                ])->withInput();
            }

            // If email exists but password wrong
            if (!Hash::check($request->password, $user->password)) {
                return redirect()->back()->withErrors([
                    'password' => 'Incorrect password'
                ])->withInput();
            }

            // Successful login
            Auth::login($user);
            $request->session()->regenerate();
            Log::info('Successful login for email: ' . $request->email);

            return redirect()->route('dashboard')->with('success', 'Successfully logged in!');
        } catch (\Exception $e) {
            Log::error('LOGIN ERROR: ' . $e->getMessage());
            return back()->with('error', 'Something went wrong!');
        }
    }
    public function logout(Request $request)
    {
        try {
            Auth::logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            Log::info('User logged out');

            return redirect()->route('login.form')->with('success', 'Logged out successfully!');
        } catch (\Exception $e) {
            Log::error("LOGOUT ERROR: " . $e->getMessage());
            return back()->with('error', 'Something went wrong!');
        }
    }
    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->with('error', 'Email not found');
        }

        // Update password
        $user->password = Hash::make($request->password);
        $user->save();

        // Redirect to login page with success message
        return redirect()->route('login.form')
            ->with('success', 'Password reset successfully. Please login.');
    }
    // public function resetPassword(Request $request)
    // {
    //     $request->validate([
    //         'email' => 'required|email|exists:users,email',
    //         'password' => 'required|min:6|confirmed',
    //     ]);

    //     $user = User::where('email', $request->email)->first();

    //     if (! $user) {
    //         return back()->with('error', 'User not found');
    //     }

    //     $user->password = Hash::make($request->password);
    //     $user->save();

    //     return redirect()->route('login.form')
    //         ->with('success', 'Password reset successfully. Please login.');
    // }

    // public function login(Request $request)
    // {
    //     try {
    //         // Validate the incoming request
    //         $request->validate([
    //             'email'    => 'required|email',
    //             'password' => 'required',
    //         ]);

    //         // Log the start of the login attempt
    //         Log::info('Login attempt for email: ' . $request->email);

    //         // Retrieve the admin user by email
    //         $admin = User::where('email', $request->email)->first();

    //         // Check if the admin exists and the password matches
    //         if (!$admin || !Hash::check($request->password, $admin->password)) {
    //             // Log invalid login attempt
    //             Log::warning('Failed login attempt for email: ' . $request->email);

    //             return redirect()->back()->withErrors([
    //                 'email' => 'Invalid email or password',
    //             ]);
    //         }

    //         // Log successful login
    //         Log::info('Successful login for email: ' . $request->email);

    //         // Generate a token (if using Laravel Sanctum or Passport)
    //         $token = $admin->createToken('adminToken')->plainTextToken;

    //         // Log token generation
    //         Log::info('Token generated for email: ' . $request->email);

    //         // Redirect to the dashboard with a success message
    //         return redirect()->route('dashboard')->with('success', 'Successfully logged in!');
    //     } catch (\Exception $e) {
    //         // Log any unexpected errors
    //         Log::error('LOGIN ERROR: ' . $e->getMessage());

    //         return back()->with('error', 'Invalid details!');
    //     }
    // }

    // public function logout(Request $request)
    // {
    //     try {

    //         $request->user()->currentAccessToken()->delete();

    //         return response()->json([
    //             'status' => true,
    //             'message' => 'Logout successfully'
    //         ]);
    //     } catch (\Exception $e) {

    //         Log::error("LOGOUT ERROR : " . $e->getMessage());

    //         return response()->json([
    //             'status' => false,
    //             'message' => 'Something went wrong',
    //             'error' => $e->getMessage()
    //         ], 500);
    //     }
    // }
}
