<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;
use App\Models\Role;
use Illuminate\Support\Facades\Cache;

class DeliveryAgentController extends Controller
{
    public function login(Request $request, $type)
    {
        /* ================= TYPE VALIDATION ================= */
        if (!in_array($type, ['password', 'otp'])) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid login type'
            ], 400);
        }

        /* ================= PASSWORD LOGIN ================= */
        if ($type === 'password') {

            $request->validate([
                'username' => 'required',
                'password' => 'required',
            ]);

            $username = trim($request->username);

            $user = User::with('role')
                ->where(function ($q) use ($username) {
                    if (is_numeric($username)) {
                        $q->where('mobile', $username);
                    } else {
                        $q->where('email', $username);
                    }
                })
                ->first();

            if (!$user || !Hash::check($request->password, $user->password)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid credentials'
                ], 401);
            }
        }

        /* ================= OTP LOGIN (SEND OTP) ================= */
        if ($type === 'otp') {

            $request->validate([
                'mobile' => 'required|digits:10',
            ]);

            $user = User::with('role')
                ->where('mobile', $request->mobile)
                ->first();

            if (!$user) {
                return response()->json([
                    'status' => false,
                    'message' => 'Mobile number not registered'
                ], 404);
            }

            // Role check
            if (!$user->role || strtolower($user->role->name) !== 'delivery agent') {
                return response()->json([
                    'status' => false,
                    'message' => 'Access denied'
                ], 403);
            }

            $otp = random_int(100000, 999999);

            $user->update([
                'otp' => $otp,
                'otp_expires_at' => now()->addMinutes(5),
            ]);

            // Send SMS
            Http::asForm()->post('http://redirect.ds3.in/submitsms.jsp', [
                'user' => env('SMS_USER'),
                'key' => env('SMS_KEY'),
                'mobile' => '91' . $user->mobile,
                'message' => "Your OTP is {$otp}",
                'senderid' => env('SMS_SENDERID'),
                'accusage' => '10',
            ]);

            return response()->json([
                'status' => true,
                'message' => 'OTP sent successfully'
            ]);
        }

        /* ================= COMMON CHECKS ================= */
        if (!$user->role || strtolower($user->role->name) !== 'delivery agent') {
            return response()->json([
                'status' => false,
                'message' => 'Access denied'
            ], 403);
        }

        if ($user->status != 1) {
            return response()->json([
                'status' => false,
                'message' => 'Account inactive'
            ], 403);
        }

        /* ================= TOKEN ================= */
        $token = $user->createToken('delivery-agent-token')->plainTextToken;

        $user->update([
            'last_login_at' => now()
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Login successful',
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => trim($user->first_name . ' ' . $user->last_name),
                'email' => $user->email,
                'mobile' => $user->mobile,
                'role' => $user->role->name,
            ]
        ]);
    }
    public function verifyOtp(Request $request, $type)
    {
        if (!in_array($type, ['login', 'forgot'])) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid verification type'
            ], 400);
        }

        // Validate request
        $request->validate([
            'mobile' => 'required|digits:10',
            'otp'    => 'required|digits:6',
        ]);

        $user = User::with('role')
            ->where('mobile', $request->mobile)
            ->where('otp', $request->otp)
            ->first();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid OTP'
            ], 401);
        }

        if (now()->greaterThan($user->otp_expires_at)) {
            return response()->json([
                'status' => false,
                'message' => 'OTP expired'
            ], 401);
        }

        /* ================= LOGIN OTP ================= */
        if ($type === 'login') {

            // Role check (only for login)
            if (!$user->role || strtolower($user->role->name) !== 'delivery agent') {
                return response()->json([
                    'status' => false,
                    'message' => 'Access denied'
                ], 403);
            }

            $token = $user->createToken('delivery-agent-token')->plainTextToken;

            $user->update([
                'otp' => null,
                'otp_expires_at' => null,
                'last_login_at' => now(),
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Login successful',
                'token' => $token,
                'user' => [
                    'id' => $user->id,
                    'name' => trim($user->first_name . ' ' . $user->last_name),
                    'email' => $user->email,
                    'mobile' => $user->mobile,
                    'role' => $user->role->name,
                ]
            ]);
        }

        if ($type === 'forgot') {

            // DO NOT clear OTP here (needed for reset password)
            return response()->json([
                'status' => true,
                'message' => 'OTP verified. Proceed to reset password'
            ]);
        }
    }

    public function resendOtp(Request $request)
    {
        $request->validate([
            'mobile' => 'required|digits:10',
        ]);

        $user = User::where('mobile', $request->mobile)->first();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Mobile number not found'
            ], 404);
        }

        $otp = random_int(100000, 999999);

        $user->update([
            'otp' => $otp,
            'otp_expires_at' => now()->addMinutes(5),
        ]);

        // Send SMS
        Http::asForm()->post('http://redirect.ds3.in/submitsms.jsp', [
            'user' => env('SMS_USER'),
            'key' => env('SMS_KEY'),
            'mobile' => '91' . $user->mobile,
            'message' => "Your new OTP is {$otp}",
            'senderid' => env('SMS_SENDERID'),
            'accusage' => '10',
        ]);

        return response()->json([
            'status' => true,
            'message' => 'OTP resent successfully'
        ]);
    }
    public function forgotPasswordSendOtp(Request $request)
    {
        $request->validate([
            'mobile' => 'required|digits:10',
        ]);

        $user = User::where('mobile', $request->mobile)->first();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Mobile number not registered'
            ], 404);
        }

        $otp = random_int(100000, 999999);

        $user->update([
            'otp' => $otp,
            'otp_expires_at' => now()->addMinutes(5),
        ]);

        // Send SMS
        Http::asForm()->post('http://redirect.ds3.in/submitsms.jsp', [
            'user' => env('SMS_USER'),
            'key' => env('SMS_KEY'),
            'mobile' => '91' . $user->mobile,
            'message' => "Your password reset OTP is {$otp}",
            'senderid' => env('SMS_SENDERID'),
            'accusage' => '10',
        ]);

        return response()->json([
            'status' => true,
            'message' => 'OTP sent for password reset'
        ]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'mobile' => 'required|digits:10',
            'reset_token' => 'required',
            'password' => 'required|min:8|confirmed',
        ]);

        $cachedToken = Cache::get('reset_password_' . $request->mobile);

        if (!$cachedToken || $cachedToken !== $request->reset_token) {
            return response()->json([
                'status' => false,
                'message' => 'Reset authorization failed'
            ], 403);
        }

        $user = User::where('mobile', $request->mobile)->firstOrFail();

        $user->update([
            'password' => Hash::make($request->password),
            'otp' => null,
            'otp_expires_at' => null,
        ]);

        Cache::forget('reset_password_' . $request->mobile);

        return response()->json([
            'status' => true,
            'message' => 'Password reset successfully'
        ]);
    }



    // ---------------- LOGOUT ----------------
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status' => true,
            'message' => 'Logged out successfully'
        ]);
    }
}
