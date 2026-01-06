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

class DeliveryAgentController extends Controller
{

    public function emailLogin(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        // Fetch user with role
        $user = User::with('role')
            ->where('email', $request->email)
            ->first();

        // User not found or password incorrect
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'status'  => false,
                'message' => 'Invalid email or password'
            ], 401);
        }

        // ❌ Role check (dynamic)
        if (!$user->role || strtolower($user->role->name) !== 'delivery agent') {
            return response()->json([
                'status'  => false,
                'message' => 'Access denied. Not a delivery agent.'
            ], 403);
        }

        // ✅ Login allowed
        $token = $user->createToken('delivery-agent-token')->plainTextToken;

        // Update last login
        $user->update([
            'last_login_at' => now()
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'Delivery agent login successful',
            'token'   => $token,
            'user'    => [
                'id'         => $user->id,
                'name'       => $user->first_name . ' ' . $user->last_name,
                'email'      => $user->email,
                'mobile'     => $user->mobile,
                'role'       => $user->role->name,
                // 'warehouse'  => $user->warehouse_id,
            ]
        ]);
    }
    public function sendOtp(Request $request)
    {
        $request->validate([
            'mobile' => 'required|digits:10',
        ]);

        // Get Delivery Agent role safely
        $role = Role::where('name', 'Delivery Agent')->first();
        if (!$role) {
            return response()->json([
                'status' => false,
                'message' => 'Delivery Agent role not found'
            ], 500);
        }

        $otp = random_int(100000, 999999);
        $otpToken = (string) Str::uuid();

        // Create / Update user
        $agent = User::updateOrCreate(
            [
                'mobile'  => $request->mobile,
                'role_id' => $role->id
            ],
            [
                'otp'        => $otp,
                'remember_token'  => $otpToken,
                'otp_expires_at' => now()->addMinutes(5),
                'status'     => 1,
            ]
        );

        try {
            Http::asForm()->post('http://redirect.ds3.in/submitsms.jsp', [
                'user'     => env('SMS_USER'),
                'key'      => env('SMS_KEY'),
                'mobile'   => '91' . $agent->mobile,
                'message'  => "Your OTP for login is {$otp}",
                'senderid' => env('SMS_SENDERID'),
                'accusage' => '10',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'OTP send failed'
            ], 500);
        }

        return response()->json([
            'status'   => true,
            'message'  => 'OTP sent successfully',
            'otpToken' => $otpToken,
        ]);
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'remember_token' => 'required',
            'otp' => 'required|digits:6',
        ]);

        $agent = User::with('role')
            ->where('remember_token', $request->remember_token)
            ->where('otp', $request->otp)
            ->first();

        if (!$agent) {
            return response()->json([
                'status'  => false,
                'message' => 'Invalid OTP'
            ], 401);
        }

        if (!$agent->otp_expires_at || now()->greaterThan($agent->otp_expires_at)) {
            return response()->json([
                'status'  => false,
                'message' => 'OTP expired'
            ], 401);
        }

        if (!$agent->role || strtolower($agent->role->name) !== 'delivery agent') {
            return response()->json([
                'status'  => false,
                'message' => 'Access denied'
            ], 403);
        }

        $agent->update([
            'otp'             => null,
            'remember_token'  => null,
            'otp_expires_at'  => null,
            'last_login_at'   => now(),
        ]);

        $token = $agent->createToken('delivery-agent-token')->plainTextToken;

        return response()->json([
            'status'  => true,
            'message' => 'OTP verified successfully',
            'token'   => $token,
            'agent'   => [
                'id'     => $agent->id,
                'name'   => trim($agent->first_name . ' ' . $agent->last_name),
                'email'  => $agent->email,
                'mobile' => $agent->mobile,
                'role'   => $agent->role->name,
            ]
        ]);
    }

    public function resendOtp(Request $request)
    {
        $request->validate([
            'otpToken' => 'required',
        ]);

        $agent = User::where('otp_token', $request->otpToken)->first();

        if (!$agent) {
            return response()->json([
                'status'  => false,
                'message' => 'Invalid OTP token'
            ], 404);
        }

        $otp = random_int(100000, 999999);

        $agent->update([
            'otp'        => $otp,
            'otp_expiry' => now()->addMinutes(5),
        ]);

        try {
            Http::asForm()->post('http://redirect.ds3.in/submitsms.jsp', [
                'user'     => env('SMS_USER'),
                'key'      => env('SMS_KEY'),
                'mobile'   => '91' . $agent->mobile,
                'message'  => "Your new OTP for login is {$otp}",
                'senderid' => env('SMS_SENDERID'),
                'accusage' => '10',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'OTP resend failed'
            ], 500);
        }

        return response()->json([
            'status'  => true,
            'message' => 'OTP resent successfully'
        ]);
    }
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Logged out successfully'
        ]);
    }
}
