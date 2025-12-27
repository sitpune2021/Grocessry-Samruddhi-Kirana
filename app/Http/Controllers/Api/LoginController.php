<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Validator;
use App\Models\Role;


class LoginController extends Controller
{
    public function register(Request $request)
    {
        if ($request->filled('email') === false) {
            $request->merge(['email' => null]);
        }
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name'  => 'nullable|string|max:255',
            'mobile'     => 'required|digits:10|unique:users,mobile',
            'email'      => 'nullable|email|unique:users,email',
            'password'   => [
                'required',
                'confirmed',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*#?&]).{8,}$/'
            ],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        /* ================= ROLE TYPE ================= */
        $type = 'customer';

        /* ================= FETCH ROLE FROM DB ================= */
        $role = Role::where('name', $type)->first();

        if (!$role) {
            return response()->json([
                'status' => false,
                'message' => ucfirst($type) . ' role not found'
            ], 404);
        }

        /* ================= CREATE USER ================= */
        $user = User::create([
            'first_name' => $request->first_name,
            'last_name'  => $request->last_name,
            'mobile'     => $request->mobile,
            'email'      => $request->email,
            'role_id'    => $role->id,
            'password'   => Hash::make($request->password),
            'status'     => 1
        ]);

        /* ================= TOKEN ================= */
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status' => true,
            'message' => 'Customer registration successful',
            'token' => $token,
            'token_type' => 'Bearer',
            'user' => [
                'id' => $user->id,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'mobile' => $user->mobile,
                'email' => $user->email,
                'role_id' => $role->id,
                'role' => $role->name
            ]
        ], 201);
    }


    public function login(Request $request, $type)
    {
        /* ================= VALIDATION ================= */
        $rules = [
            'mobile' => ['required', 'regex:/^[6-9][0-9]{9}$/'],
        ];

        if ($type === 'password') {
            $rules['password'] = 'required';
        }

        if ($type === 'otp') {
            $rules['password'] = 'prohibited'; // 🔥 KEY FIX
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        /* ================= USER CHECK ================= */
        $user = User::with('role')
            ->where('mobile', $request->mobile)
            ->first();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User not found'
            ], 404);
        }

        /* ================= ROLE CHECK ================= */
        if (!in_array(strtolower($user->role->name), ['customer', 'retailer', 'delivery agent'])) {
            return response()->json([
                'status' => false,
                'message' => 'This role is not allowed to login'
            ], 403);
        }

        /* ================= OTP LOGIN ================= */
        if ($type === 'otp') {

            $key = 'login-otp-' . $request->mobile;

            if (RateLimiter::tooManyAttempts($key, 3)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Too many OTP requests. Try again after 5 minutes'
                ], 429);
            }

            RateLimiter::hit($key, 300);

            $otp = rand(100000, 999999);

            $user->update([
                'otp' => $otp,
                'otp_expires_at' => Carbon::now()->addMinutes(5)
            ]);


            $response = Http::asForm()->post(
                'http://redirect.ds3.in/submitsms.jsp',
                [
                    'user'     => env('SMS_USER'),
                    'key'      => env('SMS_KEY'),
                    'mobile'   => '91' . $user->mobile,
                    'message'  => "Your OTP for login is $otp",
                    'senderid' => env('SMS_SENDERID'),
                    'accusage' => '10',
                ]
            );

            if (!$response->successful()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Failed to send OTP'
                ], 500);
            }

            return response()->json([
                'status' => true,
                'message' => 'OTP sent successfully'
            ]);
        }

        /* ================= PASSWORD LOGIN ================= */
        if (!Hash::check($request->password, $user->password)) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid password'
            ], 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status' => true,
            'message' => 'Login successful',
            'token' => $token,
            'token_type' => 'Bearer',
            'user' => [
                'id' => $user->id,
                'name' => trim($user->first_name . ' ' . $user->last_name),
                'mobile' => $user->mobile,
                'email' => $user->email,
                'role_id' => $user->role_id,
                'role' => $user->role->name
            ]
        ], 200);
    }


    public function forgotPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mobile' => 'required|digits:10',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::where('mobile', $request->mobile)->first();

        // 🔒 Do NOT reveal whether user exists
        if (!$user) {
            return response()->json([
                'status'  => true,
                'message' => 'If an account exists, OTP has been sent to the registered mobile number.'
            ]);
        }

        // ⏱ Rate limiting
        $key = 'forgot-password-' . $request->mobile;

        if (RateLimiter::tooManyAttempts($key, 3)) {
            return response()->json([
                'status'  => false,
                'message' => 'Too many requests. Please try again later.'
            ], 429);
        }

        RateLimiter::hit($key, 300); // 5 minutes

        // 🔐 Generate OTP
        $otp = rand(100000, 999999);

        $user->update([
            'otp' => Hash::make($otp),
            'otp_expires_at' => Carbon::now()->addMinutes(5),
        ]);

        // 📲 Send SMS
        $message = "Your password reset OTP is $otp";

        Http::asForm()->post(
            'http://redirect.ds3.in/submitsms.jsp',
            [
                'user'     => env('SMS_USER'),
                'key'      => env('SMS_KEY'),
                'mobile'   => '91' . $user->mobile,
                'message'  => $message,
                'senderid' => env('SMS_SENDERID'),
                'accusage' => '10',
            ]
        );

        // ✅ Same response whether user exists or not
        return response()->json([
            'status'  => true,
            'message' => 'If an account exists,OTP has been sent to the registered mobile number.'
        ]);
    }

    public function verifyOtp(Request $request, $type)
    {
        if (!in_array($type, ['login_otp', 'forgot_password_otp'])) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid OTP type'
            ], 400);
        }

        $validator = Validator::make($request->all(), [
            'mobile' => 'required|digits:10',
            'otp'    => 'required|digits:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::where('mobile', $request->mobile)->first();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User not found'
            ], 404);
        }

        if (!$user->otp || !$user->otp_expires_at) {
            return response()->json([
                'status' => false,
                'message' => 'OTP not requested'
            ], 400);
        }

        if (now()->greaterThan($user->otp_expires_at)) {
            return response()->json([
                'status' => false,
                'message' => 'OTP expired'
            ], 400);
        }

        if (!Hash::check($request->otp, $user->otp)) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid OTP'
            ], 400);
        }

        // ✅ Clear OTP
        $user->update([
            'otp' => null,
            'otp_expires_at' => null
        ]);

        /* ================= LOGIN OTP ================= */
        if ($type === 'login_otp') {

            // ❌ Delete old tokens (optional but recommended)
            $user->tokens()->delete();

            // ✅ Create token (SESSION)
            $token = $user->createToken('grocery-mobile')->plainTextToken;

            return response()->json([
                'status'  => true,
                'message' => 'Login successful',
                'token'   => $token,
                'user'    => [
                    'id'    => $user->id,
                    'name'  => trim($user->first_name . ' ' . $user->last_name),
                    'mobile' => $user->mobile,
                    'role'  => $user->role->name ?? null
                ]
            ]);
        }

        /* ================= FORGOT PASSWORD OTP ================= */
        return response()->json([
            'status' => true,
            'message' => 'OTP verified. You may reset your password.'
        ]);
    }


    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mobile'   => 'required|digits:10',
            // 'otp'      => 'required|digits:6',
            'password' => [
                'required',
                'confirmed',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*#?&]).{8,}$/'
            ]
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::where('mobile', $request->mobile)->first();

        //  User / OTP existence check
        // if (!$user || !$user->otp || !$user->otp_expires_at) {
        //     return response()->json([
        //         'status' => false,
        //         'message' => 'OTP invalid or expired'
        //     ], 400);
        // }

        //  OTP expiry check
        // if (now()->greaterThan($user->otp_expires_at)) {
        //     return response()->json([
        //         'status' => false,
        //         'message' => 'OTP expired'
        //     ], 400);
        // }

        //  OTP mismatch
        // if (!Hash::check($request->otp, $user->otp)) {
        //     return response()->json([
        //         'status' => false,
        //         'message' => 'Invalid OTP'
        //     ], 400);
        // }

        // Update password & clear OTP

        $user->password = Hash::make($request->password);
        // $user->otp = null;
        // $user->otp_expires_at = null;
        $user->save();

        return response()->json([
            'status' => true,
            'message' => 'Password reset successful',
            'user' => $user
        ]);
    }

    public function logout(Request $request)
    {
        if (!$request->user()) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthenticated'
            ], 401);
        }

        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status' => true,
            'message' => 'Logged out successfully'
        ]);
    }
}
