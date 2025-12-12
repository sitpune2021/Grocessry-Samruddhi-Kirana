<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{

    // Mobile login with otp
    public function sendOtp(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'mobile' => 'required|digits:10',
            'role'   => 'required' // seller / driver / admin
        ]);

        if ($validate->fails()) {
            return response()->json(['errors' => $validate->errors()], 400);
        }

        $user = User::where('mobile', $request->mobile)
            ->where('role', $request->role)
            ->first();

        if (!$user) {
            return response()->json(['message' => 'User not found', 'status' => false], 404);
        }

        $otp = rand(100000, 999999);

        $user->otp = $otp;
        $user->otp_expires_at = Carbon::now()->addMinutes(5);
        $user->save();

        $message = "Your OTP for login is $otp";

        // SMS API (optional)
        Http::withoutVerifying()->asForm()->post('http://redirect.ds3.in/submitsms.jsp', [
            'user' => 'SITSol',
            'key' => 'b6b34d1d4dXX',
            'mobile' => $request->mobile,
            'message' => $message,
            'senderid' => 'DALERT',
            'accusage' => '10',
        ]);

        return response()->json([
            'message' => 'OTP sent successfully',
            'status' => true,
            'otp' => $otp   // remove in production
        ]);
    }

    public function verifyOtp(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'mobile' => 'required|digits:10',
            'role'   => 'required',
            'otp'    => 'required|digits:6'
        ]);

        if ($validate->fails()) {
            return response()->json(['errors' => $validate->errors()], 400);
        }

        $user = User::where('mobile', $request->mobile)
            ->where('role', $request->role)
            ->first();

        if (!$user) {
            return response()->json(['message' => 'User not found', 'status' => false], 404);
        }

        if ($user->otp != $request->otp) {
            return response()->json(['message' => 'Invalid OTP', 'status' => false], 400);
        }

        if (Carbon::now()->greaterThan($user->otp_expires_at)) {
            return response()->json(['message' => 'OTP expired', 'status' => false], 400);
        }

        // Clear OTP after success
        $user->otp = null;
        $user->otp_expires_at = null;
        $user->save();

        // Issue login token (Sanctum)
        $token = $user->createToken('mobile-login')->plainTextToken;

        return response()->json([
            'message' => 'OTP verified successfully',
            'status' => true,
            'token' => $token,
            'user' => $user
        ]);
    }


    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status' => true,
            'message' => 'Logged out successfully'
        ]);
    }
}
