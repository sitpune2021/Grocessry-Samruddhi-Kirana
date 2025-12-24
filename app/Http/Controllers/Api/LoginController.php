<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{

    public function login(Request $request, $type)
    {


        $validator = Validator::make(
            array_merge($request->all(), ['login_type' => $type]),
            [
                'mobile'     => 'required|digits:10',
                'role_id'    => 'required|integer',
                'login_type' => 'required|in:otp,password',
                'password'   => 'required_if:login_type,password'
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

        /* ================= OTP LOGIN ================= */
        if ($type === 'otp') {

            $otp = rand(100000, 999999);

            $user->update([
                'otp' => $otp,
                'otp_expires_at' => now()->addMinutes(5)
            ]);

            // Send SMS here

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

        $token = $user->createToken('login-token')->plainTextToken;

        return response()->json([
            'status' => true,
            'message' => 'Login successful',
            'token' => $token,
            'user' => $user
        ]);
    }

    // public function login(Request $request)
    // {
    //     $validate = Validator::make($request->all(), [
    //         'mobile'     => 'required|digits:10',
    //         'role_id'    => 'required',
    //         'login_type' => 'required|in:otp,password',
    //         'password'   => 'required_if:login_type,password|string',
    //     ]);

    //     if ($validate->fails()) {
    //         return response()->json(['errors' => $validate->errors()], 400);
    //     }

    //     $user = User::where('mobile', $request->mobile)
    //         ->where('role_id', $request->role_id)
    //         ->first();

    //     if (!$user) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'User not found'
    //         ], 404);
    //     }

    //     // OTP LOGIN
    //     if ($request->login_type === 'otp') {

    //         $otp = rand(100000, 999999);

    //         $user->otp = $otp;
    //         $user->otp_expires_at = Carbon::now()->addMinutes(5);
    //         $user->save();

    //         $message = "Your OTP for login is $otp";

    //         Http::withoutVerifying()->asForm()->post(
    //             'http://redirect.ds3.in/submitsms.jsp',
    //             [
    //                 'user' => 'SITSol',
    //                 'key' => 'b6b34d1d4dXX',
    //                 'mobile' => $user->mobile,
    //                 'message' => $message,
    //                 'senderid' => 'DALERT',
    //                 'accusage' => '10',
    //             ]
    //         );

    //         return response()->json([
    //             'status' => true,
    //             'message' => 'OTP sent successfully',
    //             'otp' => $otp, // remove return OTP in production
    //         ]);
    //     }

    //     // PASSWORD LOGIN
    //     if (!Hash::check($request->password, $user->password)) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'Invalid password'
    //         ], 401);
    //     }

    //     $token = $user->createToken('login-token')->plainTextToken;

    //     return response()->json([
    //         'status' => true,
    //         'message' => 'Login successful',
    //         'token' => $token,
    //         'user' => $user
    //     ]);
    // }

    public function verifyOtp(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'mobile' => 'required|digits:10',
            'role_id'   => 'required',
            'otp'    => 'required|digits:6'
        ]);

        if ($validate->fails()) {
            return response()->json(['errors' => $validate->errors()], 400);
        }

        $user = User::where('mobile', $request->mobile)
            ->where('role_id', $request->role_id)
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
