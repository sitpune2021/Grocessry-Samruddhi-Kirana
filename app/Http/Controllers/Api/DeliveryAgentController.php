<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DeliveryAgent;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;

class DeliveryAgentController extends Controller
{
    public function sendOtp(Request $request)
    {
        $request->validate([
            'mobile' => 'required|digits:10',
        ]);

        $otp      = random_int(100000, 999999); // safer than rand()
        $otpToken = (string) Str::uuid();

        // Create or update delivery agent
        $agent = DeliveryAgent::updateOrCreate(
            ['mobile' => $request->mobile],
            [
                'otp'        => $otp,
                'otp_token'  => $otpToken,
                'otp_expiry' => now()->addMinutes(5), // IMPORTANT
            ]
        );

        try {
            Http::asForm()->post(
                'http://redirect.ds3.in/submitsms.jsp',
                [
                    'user'     => env('SMS_USER'),
                    'key'      => env('SMS_KEY'),
                    'mobile'   => '91' . $agent->mobile,
                    'message'  => "Your OTP for login is {$otp}",
                    'senderid' => env('SMS_SENDERID'),
                    'accusage' => '10',
                ]
            );
        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'OTP send failed',
            ], 500);
        }

        return response()->json([
            'status'   => true,
            'message'  => 'OTP sent successfully',
            'otpToken' => $otpToken, 
        ]);
    }


    /**
     * Verify OTP
     * POST /api/v1/auth/mobile/verify-otp
     */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otpToken' => 'required',
            'otp'      => 'required|digits:6',
        ]);

        $agent = DeliveryAgent::where('otp_token', $request->otpToken)
            ->where('otp', $request->otp)
            ->first();

        if (!$agent) {
            return response()->json([
                'status'  => false,
                'message' => 'Invalid OTP'
            ], 401);
        }

        $agent->update([
            'otp'       => null,
            'otp_token' => null,
        ]);

        $token = $agent->createToken('delivery-agent-token')->plainTextToken;

        return response()->json([
            'status'  => true,
            'message' => 'OTP verified successfully',
            'token'   => $token,
            'agent'   => $agent
        ]);
    }

    /**
     * Resend OTP
     * POST /api/v1/auth/mobile/resend-otp
     */
    public function resendOtp(Request $request)
    {
        $request->validate([
            'otpToken' => 'required',
        ]);

        $agent = DeliveryAgent::where('otp_token', $request->otpToken)->first();

        if (!$agent) {
            return response()->json([
                'status'  => false,
                'message' => 'Invalid OTP token'
            ], 404);
        }

        $agent->update([
            'otp' => rand(100000, 999999)
        ]);

        // TODO: Integrate SMS API here

        return response()->json([
            'status'  => true,
            'message' => 'OTP resent successfully'
        ]);
    }

    /**
     * Email Login
     * POST /api/v1/auth/email/login
     */
    public function emailLogin(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        $agent = DeliveryAgent::where('email', $request->email)->first();

        if (!$agent || !Hash::check($request->password, $agent->password)) {
            return response()->json([
                'status'  => false,
                'message' => 'Invalid email or password'
            ], 401);
        }

        $token = $agent->createToken('delivery-agent-token')->plainTextToken;

        return response()->json([
            'status'  => true,
            'message' => 'Login successful',
            'token'   => $token,
            'agent'   => $agent
        ]);
    }

    /**
     * Logout
     * POST /api/v1/auth/logout
     * Middleware: auth:sanctum
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Logged out successfully'
        ]);
    }
}
