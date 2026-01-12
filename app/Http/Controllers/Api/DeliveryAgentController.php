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
use Illuminate\Support\Facades\DB;
use App\Models\DeliveryAgent;

class DeliveryAgentController extends Controller
{
    public function login(Request $request, $type)
    {
        if (!in_array($type, ['password', 'otp'])) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid login type'
            ], 400);
        }

        if ($type === 'password') {

            $request->validate([
                'username' => 'required',
                'password' => 'required',
            ]);

            $username = trim($request->username);

            $user = User::with('role')
                ->where(function ($q) use ($username) {
                    is_numeric($username)
                        ? $q->where('mobile', $username)
                        : $q->where('email', $username);
                })
                ->first();

            if (!$user || !Hash::check($request->password, $user->password)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid credentials'
                ], 401);
            }
        }

        if ($type === 'otp') {

            $request->validate([
                'mobile' => 'required|digits:10',
            ]);

            $user = User::with('role')->where('mobile', $request->mobile)->first();

            if (!$user) {
                return response()->json([
                    'status' => false,
                    'message' => 'Mobile number not registered'
                ], 404);
            }

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

            $otp = random_int(100000, 999999);

            $user->update([
                'otp' => $otp,
                'otp_expires_at' => now()->addMinutes(5),
            ]);

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

        $token = $user->createToken('delivery-agent-token')->plainTextToken;

        $user->update([
            'last_login_at' => now(),
            'is_online' => 1,

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

        $request->validate([
            'mobile' => 'required|digits:10',
            'otp'    => 'required|digits:6',
        ]);

        $user = User::with('role')
            ->where('mobile', $request->mobile)
            ->where('otp', $request->otp)
            ->first();

        if (!$user || now()->greaterThan($user->otp_expires_at)) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid or expired OTP'
            ], 401);
        }

        if ($type === 'login') {

            if ($user->status != 1) {
                return response()->json([
                    'status' => false,
                    'message' => 'Account inactive'
                ], 403);
            }

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
            ]);
        }

        // $resetToken = Str::uuid()->toString();

        Cache::put(
            'reset_password_' . $user->mobile,
            // $resetToken,
            now()->addMinutes(10)
        );

        return response()->json([
            'status' => true,
            // 'reset_token' => $resetToken,
            'message' => 'OTP verified. Proceed to reset password'
        ]);
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

        // Prevent inactive delivery agents from resetting password
        if ($user->role && strtolower($user->role->name) === 'delivery agent' && $user->status != 1) {
            return response()->json([
                'status' => false,
                'message' => 'Account inactive'
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
            'password' => 'required|min:8|confirmed',
        ]);

        $user = User::where('mobile', $request->mobile)->first();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Mobile number not found'
            ], 404);
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Password reset successfully'
        ]);
    }
    public function logout(Request $request)
    {
        $user = $request->user();

        $user->update([
            'is_online' => 0, // Set offline on logout
        ]);

        $user->currentAccessToken()->delete();

        return response()->json([
            'status' => true,
            'message' => 'Logged out successfully'
        ]);
    }
    // ================= GO ONLINE =================
    public function goOnline(Request $request)
    {
        $agent = $request->user(); // Authenticated user from token

        if (!$agent) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthenticated. Please login.'
            ], 401);
        }

        // ✅ Dynamic role check (case-insensitive)
        if (!$agent->role || strtolower($agent->role->name) !== 'delivery agent') {
            return response()->json([
                'status' => false,
                'message' => 'Access denied. Only delivery agents can go online.'
            ], 403);
        }

        // Check if account is active
        if ($agent->status != 1) {
            return response()->json([
                'status' => false,
                'message' => 'Account inactive'
            ], 403);
        }

        // Set online
        $agent->update(['is_online' => 1]);

        return response()->json([
            'status' => true,
            'message' => 'Agent is online',
            'data' => [
                'agent_id' => $agent->id,
                'is_online' => $agent->is_online
            ]
        ]);
    }

    public function goOffline(Request $request)
    {
        $agent = $request->user();

        if (!$agent) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthenticated. Please login.'
            ], 401);
        }

        // ✅ Dynamic role check (case-insensitive)
        if (!$agent->role || strtolower($agent->role->name) !== 'delivery agent') {
            return response()->json([
                'status' => false,
                'message' => 'Access denied. Only delivery agents can go offline.'
            ], 403);
        }

        // Set offline
        $agent->update(['is_online' => 0]);

        return response()->json([
            'status' => true,
            'message' => 'Agent is offline',
            'data' => [
                'agent_id' => $agent->id,
                'is_online' => $agent->is_online
            ]
        ]);
    }


    /* ================= CURRENT ORDER ================= */
    public function currentOrder(Request $request)
    {
        $agent = $request->user();

        if (strtolower($agent->role->name) !== 'delivery agent') {
            return response()->json([
                'status' => false,
                'message' => 'Access denied'
            ], 403);
        }

        $order = DB::table('orders')
            ->where('delivery_agent_id', $agent->id)
            ->whereIn('status', ['assigned', 'picked_up'])
            ->first();

        if (!$order) {
            return response()->json([
                'status' => true,
                'message' => 'No active order',
                'data' => null
            ]);
        }

        return response()->json([
            'status' => true,
            'message' => 'Current order fetched',
            'data' => $order
        ]);
    }

    public function profile(Request $request)
    {
        $agent = DeliveryAgent::where('user_id', $request->user()->id)->first();

        return response()->json([
            'status' => true,
            'data' => $agent
        ]);
    }


    public function updateProfileField(Request $request, $type)
    {
        $user = $request->user();

        if ($type === 'phone') {
            $request->validate([
                'phone' => 'required|string'
            ]);

            $user->update([
                'mobile' => $request->phone
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Phone updated successfully'
            ]);
        }

        if ($type === 'email') {
            $request->validate([
                'email' => 'required|email'
            ]);

            $user->update([
                'email' => $request->email
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Email updated successfully'
            ]);
        }

        return response()->json([
            'status' => false,
            'message' => 'Invalid update type'
        ], 400);
    }


    public function updateVehicle(Request $request)
    {
        $request->validate([
            'vehicleType' => 'required|string',
            'vehicleNumber' => 'required|string'
        ]);

        DeliveryAgent::where('user_id', $request->user()->id)->update([
            'vehicle_type' => $request->vehicleType,
            'vehicle_number' => $request->vehicleNumber
        ]);

        return response()->json(['status' => true]);
    }
}
