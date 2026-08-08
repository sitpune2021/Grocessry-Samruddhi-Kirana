<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\Retailer;

class RetailerAuthController extends Controller
{


    /**
     * Retailer Login
     */
    public function login(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | 1. Validate Request
        |--------------------------------------------------------------------------
        */

        $data = $request->validate([

            'login' => [
                'required',
                'string'
            ],

            'password' => [
                'required',
                'string'
            ],

        ]);


        /*
        |--------------------------------------------------------------------------
        | 2. Log Login Request
        |--------------------------------------------------------------------------
        */

        Log::info('Retailer Login - Request', [
            'login' => $data['login'],
        ]);


        /*
        |--------------------------------------------------------------------------
        | 3. Find User
        |--------------------------------------------------------------------------
        |
        | Login can be done using:
        |
        | Email OR Mobile
        |
        */

        $user = User::where(function ($query) use ($data) {

            $query->where(
                'email',
                $data['login']
            )
            ->orWhere(
                'mobile',
                $data['login']
            );

        })
        ->where(
            'role_id',
            7
        )
        ->first();


        /*
        |--------------------------------------------------------------------------
        | 4. User Not Found
        |--------------------------------------------------------------------------
        */

        if (!$user) {

            Log::warning(
                'Retailer Login - User Not Found',
                [
                    'login' => $data['login'],
                ]
            );

            return response()->json([
                'status' => false,
                'message' => 'Invalid email/mobile or password.'
            ], 401);
        }


        /*
        |--------------------------------------------------------------------------
        | 5. Check Password
        |--------------------------------------------------------------------------
        */

        if (!Hash::check(
            $data['password'],
            $user->password
        )) {

            Log::warning(
                'Retailer Login - Invalid Password',
                [
                    'user_id' => $user->id,
                ]
            );

            return response()->json([
                'status' => false,
                'message' => 'Invalid email/mobile or password.'
            ], 401);
        }


        /*
        |--------------------------------------------------------------------------
        | 6. Check User Status
        |--------------------------------------------------------------------------
        */

        if ((int) $user->status !== 1) {

            Log::warning(
                'Retailer Login - User Inactive',
                [
                    'user_id' => $user->id,
                    'status' => $user->status,
                ]
            );

            return response()->json([
                'status' => false,
                'message' => 'Your account is inactive.'
            ], 403);
        }


        /*
        |--------------------------------------------------------------------------
        | 7. Get Retailer
        |--------------------------------------------------------------------------
        |
        | users.id = retailers.user_id
        |
        */

        $retailer = Retailer::where(
            'user_id',
            $user->id
        )
        ->where(
            'is_active',
            1
        )
        ->first();


        /*
        |--------------------------------------------------------------------------
        | 8. Retailer Not Found
        |--------------------------------------------------------------------------
        */

        if (!$retailer) {

            Log::warning(
                'Retailer Login - Retailer Profile Not Found',
                [
                    'user_id' => $user->id,
                ]
            );

            return response()->json([
                'status' => false,
                'message' => 'Retailer account not found or inactive.'
            ], 403);
        }


        /*
        |--------------------------------------------------------------------------
        | 9. Create Token
        |--------------------------------------------------------------------------
        |
        | Sanctum token
        |
        */

        $token = $user->createToken(
            'retailer-app'
        )->plainTextToken;


        /*
        |--------------------------------------------------------------------------
        | 10. Update Last Login
        |--------------------------------------------------------------------------
        */

        $user->last_login_at = now();

        $user->is_online = 1;

        $user->save();


        /*
        |--------------------------------------------------------------------------
        | 11. Login Success Log
        |--------------------------------------------------------------------------
        */

        Log::info(
            'Retailer Login - Success',
            [
                'user_id' => $user->id,
                'retailer_id' => $retailer->id,
                'shop_id' => $retailer->shop_id,
            ]
        );


        /*
        |--------------------------------------------------------------------------
        | 12. Response
        |--------------------------------------------------------------------------
        */

        return response()->json([

            'status' => true,

            'message' =>
                'Retailer login successful.',

            'token' =>
                $token,

            'token_type' =>
                'Bearer',

            'retailer' => [

                'id' =>
                    $retailer->id,

                'user_id' =>
                    $retailer->user_id,

                'shop_id' =>
                    $retailer->shop_id,

                'name' =>
                    $retailer->name,

                'email' =>
                    $retailer->email,

                'mobile' =>
                    $retailer->mobile,

                'address' =>
                    $retailer->address,

                'shop_name' =>
                    $retailer->shop_name,

                'gst_number' =>
                    $retailer->gst_number,

                'state_id' =>
                    $retailer->state_id,

                'district_id' =>
                    $retailer->district_id,

                'taluka_id' =>
                    $retailer->taluka_id,

            ],

        ], 200);
    }


    /**
     * Retailer Profile
     */
    public function profile(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | 1. Get Authenticated User
        |--------------------------------------------------------------------------
        */

        $user = $request->user();


        /*
        |--------------------------------------------------------------------------
        | 2. Log Request
        |--------------------------------------------------------------------------
        */

        Log::info(
            'Retailer Profile - Request',
            [
                'user_id' => $user->id,
            ]
        );


        /*
        |--------------------------------------------------------------------------
        | 3. Get Retailer
        |--------------------------------------------------------------------------
        |
        | users.id = retailers.user_id
        |
        */

        $retailer = Retailer::where(
            'user_id',
            $user->id
        )
        ->where(
            'is_active',
            1
        )
        ->first();


        /*
        |--------------------------------------------------------------------------
        | 4. Retailer Not Found
        |--------------------------------------------------------------------------
        */

        if (!$retailer) {

            Log::warning(
                'Retailer Profile - Retailer Not Found',
                [
                    'user_id' => $user->id,
                ]
            );

            return response()->json([
                'status' => false,
                'message' => 'Retailer profile not found or inactive.'
            ], 404);
        }


        /*
        |--------------------------------------------------------------------------
        | 5. Success Log
        |--------------------------------------------------------------------------
        */

        Log::info(
            'Retailer Profile - Success',
            [
                'user_id' => $user->id,
                'retailer_id' => $retailer->id,
            ]
        );


        /*
        |--------------------------------------------------------------------------
        | 6. Return Profile
        |--------------------------------------------------------------------------
        */

        return response()->json([

            'status' => true,

            'message' =>
                'Retailer profile fetched successfully.',

            'retailer' => [

                'id' =>
                    $retailer->id,

                'user_id' =>
                    $retailer->user_id,

                'shop_id' =>
                    $retailer->shop_id,

                'name' =>
                    $retailer->name,

                'email' =>
                    $retailer->email,

                'mobile' =>
                    $retailer->mobile,

                'address' =>
                    $retailer->address,

                'dob' =>
                    $retailer->dob,

                'gender' =>
                    $retailer->gender,

                'gst_number' =>
                    $retailer->gst_number,

                'shop_name' =>
                    $retailer->shop_name,

                'state_id' =>
                    $retailer->state_id,

                'district_id' =>
                    $retailer->district_id,

                'taluka_id' =>
                    $retailer->taluka_id,

                'is_active' =>
                    $retailer->is_active,

            ],

        ], 200);
    }


    /**
     * Retailer Logout
     */
    public function logout(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | 1. Get Authenticated User
        |--------------------------------------------------------------------------
        */

        $user = $request->user();


        /*
        |--------------------------------------------------------------------------
        | 2. Log Logout Request
        |--------------------------------------------------------------------------
        */

        Log::info(
            'Retailer Logout - Request',
            [
                'user_id' => $user->id,
            ]
        );


        /*
        |--------------------------------------------------------------------------
        | 3. Revoke Current Token
        |--------------------------------------------------------------------------
        */

        $currentToken = $user->currentAccessToken();

        if ($currentToken) {

            $currentToken->delete();
        }


        /*
        |--------------------------------------------------------------------------
        | 4. Update Online Status
        |--------------------------------------------------------------------------
        */

        $user->is_online = 0;

        $user->save();


        /*
        |--------------------------------------------------------------------------
        | 5. Success Log
        |--------------------------------------------------------------------------
        */

        Log::info(
            'Retailer Logout - Success',
            [
                'user_id' => $user->id,
            ]
        );


        /*
        |--------------------------------------------------------------------------
        | 6. Response
        |--------------------------------------------------------------------------
        */

        return response()->json([

            'status' => true,

            'message' =>
                'Retailer logout successful.'

        ], 200);
    }


}