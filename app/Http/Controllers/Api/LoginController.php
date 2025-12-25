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
    // public function register(Request $request)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'first_name'     => 'required|string|max:255',
    //         'last_name'      => 'nullable|string|max:255',
    //         'mobile'   => 'required|digits:10|unique:users,mobile',
    //         'email'    => 'nullable|email|unique:users,email',
    //         'password' => [
    //             'required',
    //             'confirmed',
    //             'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*#?&]).{8,}$/'
    //         ]
    //     ], [
    //         'password.regex' => 'Password must contain uppercase, lowercase, number & special character.'
    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json([
    //             'status' => false,
    //             'errors' => $validator->errors()
    //         ], 422);
    //     }

    //     $user = User::create([
    //         'first_name' => $request->first_name,
    //         'last_name'  => $request->last_name,
    //         'mobile'   => $request->mobile,
    //         'email'    => $request->email,
    //         'role_id'  => 8,
    //         'password' => Hash::make($request->password)
    //     ]);

    //     return response()->json([
    //         'status'  => true,
    //         'message' => 'Registration successful',
    //         'user'    => [
    //             'id'     => $user->id,
    //             'name'   => $user->first_name . ' ' . $user->last_name,
    //             'mobile' => $user->mobile,
    //             'email'  => $user->email
    //         ]
    //     ], 201);
    // }

    public function register(Request $request, $role)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name'  => 'nullable|string|max:255',
            'mobile'     => 'required|digits:10|unique:users,mobile',
            'email'      => 'nullable|email|unique:users,email',
            'password'   => [
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

        // ✅ Only allow specific roles (NO DB change)
        $allowedRoles = ['customer', 'retailer', 'delivery agent'];

        $normalizedRole = strtolower(str_replace('-', ' ', $role));

        if (!in_array($normalizedRole, $allowedRoles)) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid role'
            ], 403);
        }

        $roleModel = Role::whereRaw('LOWER(name) = ?', [$normalizedRole])->first();

        if (!$roleModel) {
            return response()->json([
                'status' => false,
                'message' => 'Role not found'
            ], 404);
        }

        $user = User::create([
            'first_name' => $request->first_name,
            'last_name'  => $request->last_name,
            'mobile'     => $request->mobile,
            'email'      => $request->email,
            'role_id'    => $roleModel->id,
            'password'   => Hash::make($request->password)
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Registration successful',
            'user' => [
                'id'     => $user->id,
                'name'   => $user->first_name . ' ' . $user->last_name,
                'mobile' => $user->mobile,
                'email'  => $user->email,
                'role'   => $roleModel->name
            ]
        ], 201);
    }



    public function login(Request $request, $type)
    {
        $validator = Validator::make(
            array_merge($request->all(), ['login_type' => $type]),
            [
                'mobile'     => ['required', 'regex:/^[6-9][0-9]{9}$/'],
                'login_type' => 'required|in:otp,password',
                'password'   => 'required_if:login_type,password'
            ]
        );

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        /* 🔹 Fetch user with role */
        $user = User::with('role')
            ->where('mobile', $request->mobile)
            ->first();

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'User not found'
            ], 404);
        }

        /* ✅ Allow ONLY specific role names */
        $allowedRoleNames = ['customer', 'retailer', 'delivery_agent'];

        if (
            !$user->role ||
            !in_array(strtolower($user->role->name), $allowedRoleNames)
        ) {
            return response()->json([
                'status' => 'error',
                'message' => 'This role is not allowed to login'
            ], 403);
        }

        /* ================= OTP LOGIN ================= */
        if ($type === 'otp') {

            $key = 'login-otp-' . $request->mobile;

            if (RateLimiter::tooManyAttempts($key, 3)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Too many OTP requests. Try again after 5 minutes.'
                ], 429);
            }

            RateLimiter::hit($key, 300); // 5 minutes

            $otp = rand(100000, 999999);


            $user->otp = Hash::make($otp);
            $user->otp_expires_at = Carbon::now()->addMinutes(5);
            $user->save();

            $message = "Your OTP for login is $otp";

            $response = Http::asForm()->post(
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

            if (!$response->successful()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Failed to send OTP. Try again.'
                ], 500);
            }

            return response()->json([
                'status'  => 'success',
                'message' => 'OTP sent successfully'
            ]);
        }

        /* ================= PASSWORD LOGIN ================= */
        if (!Hash::check($request->password, $user->password)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid password'
            ], 401);
        }

        /* ✅ FINAL SUCCESS RESPONSE */
        return response()->json([
            'status' => 'success',
            'user' => [
                'id'    => $user->id,
                'name'  => trim($user->first_name . ' ' . $user->last_name),
                'email' => $user->email,
                'UserType' => ucfirst($user->role->name)
            ]
        ]);
    }


    public function forgotPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mobile'  => 'required|digits:10',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::where('mobile', $request->mobile)
            ->first();

        // Do NOT reveal whether user exists (security)
        if (!$user) {
            return response()->json([
                'status' => true,
                'message' => 'If the account exists, OTP has been sent'
            ]);
        }

        // Rate limit
        $key = 'forgot-password-' . $request->mobile;
        if (RateLimiter::tooManyAttempts($key, 3)) {
            return response()->json([
                'status' => false,
                'message' => 'Too many requests. Try later.'
            ], 429);
        }
        RateLimiter::hit($key, 300);

        $otp = rand(100000, 999999);

        $user->otp = Hash::make($otp);
        $user->otp_expires_at = Carbon::now()->addMinutes(5);
        $user->save();

        // Send SMS
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

        return response()->json([
            'status' => true,
            'message' => 'If the account exists, OTP has been sent'
        ]);
    }

    //bavesh
    public function verifyOtp(Request $request, $type)
    {
        // Allow only valid OTP types
        if (!in_array($type, ['login_otp', 'forgot_password_otp'])) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid OTP type'
            ], 400);
        }

        $validator = Validator::make(
            array_merge($request->all(), ['otp_type' => $type]),
            [
                'mobile'  => 'required|digits:10',
                'role_id' => 'required|integer',
                'otp'     => 'required|digits:6',
            ]
        );

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::where('mobile', $request->mobile)
            ->where('role_id', $request->role_id)
            ->first();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User not found'
            ], 404);
        }

        // OTP must exist
        if (!$user->otp || !$user->otp_expires_at) {
            return response()->json([
                'status' => false,
                'message' => 'OTP not requested'
            ], 400);
        }

        /* Rate limiting (per mobile + type) */
        $rateKey = "otp-verify-{$type}-{$request->mobile}";

        if (RateLimiter::tooManyAttempts($rateKey, 5)) {
            return response()->json([
                'status' => false,
                'message' => 'Too many attempts. Try again later.'
            ], 429);
        }

        RateLimiter::hit($rateKey, 300); // 5 minutes

        /* Expired OTP (CHECK FIRST) */
        if (now()->greaterThan($user->otp_expires_at)) {
            return response()->json([
                'status' => false,
                'message' => 'OTP expired'
            ], 400);
        }

        /* Invalid OTP */
        if (!Hash::check($request->otp, $user->otp)) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid OTP'
            ], 400);
        }

        // Clear OTP after successful verification
        $user->otp = null;
        $user->otp_expires_at = null;
        $user->save();

        /* ================= RESPONSE BASED ON TYPE ================= */

        if ($type === 'login_otp') {

            // $user->otp = null;
            // $user->otp_expires_at = null;
            // $user->save();

            $token = $user->createToken('mobile-login')->plainTextToken;

            return response()->json([
                'status' => true,
                'message' => 'Login successfully',
                'token' => $token,
                'user' => $user
            ]);
        }

        // 🔁 FORGOT PASSWORD OTP → allow reset
        if ($type === 'forgot_password_otp') {

            return response()->json([
                'status' => true,
                'message' => 'OTP verified. You may reset your password.'
            ]);
        }
    }

    // public function verifyOtp(Request $request, $type)
    // {
    //     if (!in_array($type, ['login', 'forgot-password'])) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'Invalid OTP type'
    //         ], 400);
    //     }

    //     $validator = Validator::make($request->all(), [
    //         'mobile' => 'required|digits:10',
    //         'otp'    => 'required|digits:6',
    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json([
    //             'status' => false,
    //             'errors' => $validator->errors()
    //         ], 422);
    //     }

    //     $user = User::where('mobile', $request->mobile)->first();

    //     if (!$user) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'User not found'
    //         ], 404);
    //     }

    //     if (!$user->otp || now()->greaterThan($user->otp_expires_at)) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'OTP expired'
    //         ], 400);
    //     }

    //     if (!Hash::check($request->otp, $user->otp)) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'Invalid OTP'
    //         ], 400);
    //     }

    //     // clear OTP
    //     $user->otp = null;
    //     $user->otp_expires_at = null;
    //     $user->save();

    //     if ($type === 'login') {
    //         $token = $user->createToken('login')->plainTextToken;

    //         return response()->json([
    //             'status' => true,
    //             'message' => 'Login successful',
    //             'token' => $token,
    //             'user' => $user
    //         ]);
    //     }

    //     return response()->json([
    //         'status' => true,
    //         'message' => 'OTP verified. You can reset password.'
    //     ]);
    // }

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
