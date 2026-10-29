<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\User;
use Illuminate\Http\Request;
// use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Validator;
use App\Models\Role;
use App\Models\WarehouseServicePincode;
use App\Models\WarehouseStock;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;


class LoginController extends Controller
{


    public function register(Request $request)
    {
        $request->merge([
            'email' => $request->email ?: null
        ]);

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

        // ✅ DYNAMIC CUSTOMER (ID CHANGE SAFE)
        $role = Role::whereRaw('LOWER(name) = ?', ['customer'])
            ->firstOrFail();

        $user = User::create([
            'first_name' => $request->first_name,
            'last_name'  => $request->last_name,
            'mobile'     => $request->mobile,
            'email'      => $request->email,
            'role_id'    => $role->id,
            'password'   => Hash::make($request->password),
            'status'     => 1
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status' => true,
            'message' => 'Customer registration successful',
            'token' => $token,
            'token_type' => 'Bearer',
            'user' => [
                'id' => $user->id,
                'name' => trim($user->first_name . ' ' . $user->last_name),
                'mobile' => $user->mobile,
                'email' => $user->email,
                'role_id' => $role->id,
                'role' => $role->name
            ]
        ], 201);
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthenticated'
            ], 401);
        }

        // ✅ DYNAMIC CUSTOMER CHECK (ID CHANGE SAFE)
        if (!$user->role || strtolower($user->role->name) !== 'customer') {
            return response()->json([
                'status' => false,
                'message' => 'Only customers can update profile'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'first_name'    => 'nullable|string|max:255',
            'last_name'     => 'nullable|string|max:255',
            'email'         => 'nullable|email|unique:users,email,' . $user->id,
            'profile_photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Update profile photo
        if ($request->hasFile('profile_photo')) {
            if ($user->profile_photo) {
                Storage::delete('public/profile/' . $user->profile_photo);
            }
            $imageName = time() . '.' . $request->profile_photo->extension();
            $request->profile_photo->storeAs('public/profile', $imageName);
            $user->profile_photo = $imageName;
        }

        if ($request->filled('first_name')) {
            $user->first_name = $request->first_name;
        }

        if ($request->filled('last_name')) {
            $user->last_name = $request->last_name;
        }

        if ($request->filled('email') && $request->email !== $user->email) {
            $user->email = $request->email;
            $user->email_verified_at = now();
        }

        $user->save();

        return response()->json([
            'status' => true,
            'message' => 'Customer profile updated successfully',
            'user' => [
                'id' => $user->id,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'email' => $user->email,
                'profile_photo' => $user->profile_photo
                    ? asset('storage/profile/' . $user->profile_photo)
                    : null,
            ]
        ], 200);
    }

    public function userprofiledetails(Request $request)
    {
        $user = $request->user(); // logged-in user via token

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthenticated'
            ], 401);
        }

        // Fetch customer role
        $role = Role::whereRaw('LOWER(name) = ?', ['customer'])->first();

        // Optional: check if user is customer
        if ($role && $user->role_id != $role->id) {
            return response()->json([
                'status' => false,
                'message' => 'Access denied'
            ], 403);
        }

        return response()->json([
            'status' => true,
            'data' => [
                'id'                => $user->id,
                'first_name'        => $user->first_name,
                'last_name'         => $user->last_name,
                'email'             => $user->email,
                'mobile'            => $user->mobile,
                'profile_photo'     => $user->profile_photo
                    ? asset('storage/' . $user->profile_photo)
                    : null,
                'status'            => $user->status,
                // 'is_online'         => $user->is_online,
                // 'warehouse_id'      => $user->warehouse_id,
                'role_id'           => $user->role_id,
                // 'last_login_at'     => $user->last_login_at,
                'created_at'        => $user->created_at,
            ]
        ]);
    }

    public function deleteAccount(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthenticated'
            ], 401);
        }

        // Logout user (revoke tokens)
        $user->tokens()->delete();

        // Soft delete account
        $user->delete();

        return response()->json([
            'status' => true,
            'message' => 'Account deleted successfully'
        ]);
    }
    
    // public function login(Request $request, $type)
    // {
    //     /* ================= TYPE VALIDATION ================= */
    //     if (!in_array($type, ['password', 'otp'])) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'Invalid login type'
    //         ], 400);
    //     }

    //     /* ================= VALIDATION ================= */
    //     $rules = [
    //         'mobile' => ['required', 'regex:/^[6-9][0-9]{9}$/'],
    //     ];

    //     if ($type === 'password') {
    //         $rules['password'] = 'required';
    //     }

    //     if ($type === 'otp') {
    //         $rules['password'] = 'prohibited';
    //     }

    //     $validator = Validator::make($request->all(), $rules);

    //     if ($validator->fails()) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'Validation failed',
    //             'errors' => $validator->errors()
    //         ], 422);
    //     }

    //     /* ================= USER CHECK ================= */
    //     $user = User::with('role')
    //         ->where('mobile', $request->mobile)
    //         ->first();

    //     if (!$user || !$user->role) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'User not found'
    //         ], 200);
    //     }

    //     /* ================= ROLE CHECK (DYNAMIC) ================= */
    //     $allowedRoles = [
    //         'customer',
    //         'retailer',
    //         'delivery agent',
    //     ];

    //     if (!in_array(strtolower($user->role->name), $allowedRoles)) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'This role is not allowed to login'
    //         ], 403);
    //     }

    //     /* ================= OTP LOGIN ================= */
    //     if ($type === 'otp') {

    //         $key = 'login-otp-' . $request->mobile;

    //         if (RateLimiter::tooManyAttempts($key, 3)) {
    //             return response()->json([
    //                 'status' => false,
    //                 'message' => 'Too many OTP requests. Try again after 5 minutes'
    //             ], 429);
    //         }

    //         RateLimiter::hit($key, 300);

    //         $otp = random_int(100000, 999999);

    //         $user->update([
    //             'otp' => $otp,
    //             'otp_expires_at' => now()->addMinutes(5)
    //         ]);

    //         $response = Http::asForm()->post(
    //             'http://redirect.ds3.in/submitsms.jsp',
    //             [
    //                 'user'     => env('SMS_USER'),
    //                 'key'      => env('SMS_KEY'),
    //                 'mobile'   => '91' . $user->mobile,
    //                 'message'  => "Your OTP for login is $otp",
    //                 'senderid' => env('SMS_SENDERID'),
    //                 'accusage' => '10',
    //             ]
    //         );

    //         if (!$response->successful()) {
    //             return response()->json([
    //                 'status' => false,
    //                 'message' => 'Failed to send OTP'
    //             ], 500);
    //         }

    //         return response()->json([
    //             'status' => true,
    //             'message' => 'OTP sent successfully'
    //         ], 200);
    //     }

    //     /* ================= PASSWORD LOGIN ================= */
    //     if (!Hash::check($request->password, $user->password)) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'Invalid password'
    //         ], 200);
    //     }

    //     $token = $user->createToken('auth_token')->plainTextToken;

    //     return response()->json([
    //         'status' => true,
    //         'message' => 'Login successful',
    //         'token' => $token,
    //         'token_type' => 'Bearer',
    //         'user' => [
    //             'id' => $user->id,
    //             'name' => trim($user->first_name . ' ' . $user->last_name),
    //             'mobile' => $user->mobile,
    //             'email' => $user->email,
    //             'role_id' => $user->role_id,
    //             'role' => $user->role->name
    //         ]
    //     ], 200);
    // }

    // public function login(Request $request, $type)
    // {
    //     // 1. Type validation
    //     if (!in_array($type, ['password', 'otp'], true)) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'Invalid login type'
    //         ], 400);
    //     }

    //     // 2. Request validation
    //     $rules = [
    //         'mobile' => ['required', 'regex:/^[6-9][0-9]{9}$/'],
    //     ];

    //     if ($type === 'password') {
    //         $rules['password'] = ['required', 'string'];
    //     } else {
    //         $rules['password'] = ['prohibited'];
    //     }

    //     $validator = Validator::make($request->all(), $rules);

    //     if ($validator->fails()) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'Validation failed',
    //             'errors' => $validator->errors()
    //         ], 422);
    //     }

    //     // 3. Find user
    //     $user = User::with('role')
    //         ->where('mobile', $request->mobile)
    //         ->first();

    //     if (!$user || !$user->role) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'User not found'
    //         ], 404);
    //     }

    //     // 4. Allowed role check
    //     $allowedRoles = [
    //         'customer',
    //         'retailer',
    //         'delivery agent',
    //     ];

    //     if (!in_array(
    //         strtolower(trim($user->role->name)),
    //         $allowedRoles,
    //         true
    //     )) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'This role is not allowed to login'
    //         ], 403);
    //     }

    //     // 5. OTP login
    //     if ($type === 'otp') {

    //         $key = 'login-otp-' . $user->mobile;

    //         if (RateLimiter::tooManyAttempts($key, 3)) {
    //             return response()->json([
    //                 'status' => false,
    //                 'message' => 'Too many OTP requests. Try again after '
    //                     . RateLimiter::availableIn($key)
    //                     . ' seconds'
    //             ], 429);
    //         }

    //         // Check SMS configuration
    //         $smsUser = config('services.sms.user');
    //         $smsKey = config('services.sms.key');
    //         $senderId = config('services.sms.senderid');

    //         if (!$smsUser || !$smsKey || !$senderId) {
    //             Log::error('SMS configuration missing');

    //             return response()->json([
    //                 'status' => false,
    //                 'message' => 'SMS gateway configuration missing'
    //             ], 500);
    //         }

    //         // Generate OTP
    //         $otp = (string) random_int(100000, 999999);

    //         try {
    //             $response = Http::asForm()
    //                 ->timeout(30)
    //                 ->post('http://redirect.ds3.in/submitsms.jsp', [
    //                     'user'     => $smsUser,
    //                     'key'      => $smsKey,
    //                     'mobile'   => '91' . $user->mobile,
    //                     'message'  => "Your OTP for login is {$otp}",
    //                     'senderid' => $senderId,
    //                     'accusage' => '10',
    //                 ]);

    //             // Log gateway response; do not log OTP or API key
    //             Log::info('SMS Gateway Response', [
    //                 'http_status' => $response->status(),
    //                 'response_body' => $response->body(),
    //                 'mobile_last4' => substr($user->mobile, -4),
    //             ]);

    //             // HTTP request failed
    //             if (!$response->successful()) {
    //                 return response()->json([
    //                     'status' => false,
    //                     'message' => 'Failed to connect to SMS gateway'
    //                 ], 502);
    //             }

    //             /*
    //             * IMPORTANT:
    //             * HTTP 200 does not guarantee SMS delivery.
    //             * Verify the response body against the DS3 provider's
    //             * documented success response before accepting the OTP.
    //             */

    //             $gatewayResponse = trim($response->body());

    //             if ($gatewayResponse === '') {
    //                 return response()->json([
    //                     'status' => false,
    //                     'message' => 'SMS gateway returned an empty response'
    //                 ], 502);
    //             }

    //             /*
    //             * Save OTP only after confirming that the gateway
    //             * accepted the SMS request. Adapt this check to DS3's
    //             * actual response format.
    //             */

    //             $user->update([
    //                 'otp' => $otp,
    //                 'otp_expires_at' => now()->addMinutes(5),
    //             ]);

    //             RateLimiter::hit($key, 300);

    //             return response()->json([
    //                 'status' => true,
    //                 'message' => 'OTP request submitted to SMS gateway'
    //             ], 200);

    //         } catch (\Illuminate\Http\Client\ConnectionException $e) {

    //             Log::error('SMS Gateway Connection Error', [
    //                 'error' => $e->getMessage(),
    //             ]);

    //             return response()->json([
    //                 'status' => false,
    //                 'message' => 'SMS gateway connection failed'
    //             ], 502);

    //         } catch (\Throwable $e) {

    //             Log::error('OTP Login Error', [
    //                 'error' => $e->getMessage(),
    //             ]);

    //             return response()->json([
    //                 'status' => false,
    //                 'message' => 'Unable to process OTP request'
    //             ], 500);
    //         }
    //     }

    //     // 6. Password login
    //     if (!Hash::check($request->password, $user->password)) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'Invalid password'
    //         ], 401);
    //     }

    //     $token = $user->createToken('auth_token')->plainTextToken;

    //     return response()->json([
    //         'status' => true,
    //         'message' => 'Login successful',
    //         'token' => $token,
    //         'token_type' => 'Bearer',
    //         'user' => [
    //             'id' => $user->id,
    //             'name' => trim(
    //                 $user->first_name . ' ' . $user->last_name
    //             ),
    //             'mobile' => $user->mobile,
    //             'email' => $user->email,
    //             'role_id' => $user->role_id,
    //             'role' => $user->role->name,
    //         ]
    //     ], 200);
    // }

    // proper msg show

    // public function login(Request $request, $type)
    // {
    //     // 1. Validate login type
    //     if (!in_array($type, ['password', 'otp'], true)) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'Invalid login type'
    //         ], 400);
    //     }

    //     // 2. Validate request
    //     $rules = [
    //         'mobile' => ['required', 'regex:/^[6-9][0-9]{9}$/'],
    //     ];

    //     if ($type === 'password') {
    //         $rules['password'] = ['required', 'string'];
    //     } else {
    //         $rules['password'] = ['prohibited'];
    //     }

    //     $validator = Validator::make($request->all(), $rules);

    //     if ($validator->fails()) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'Validation failed',
    //             'errors' => $validator->errors()
    //         ], 422);
    //     }

    //     // 3. Find user
    //     $user = User::with('role')
    //         ->where('mobile', $request->mobile)
    //         ->first();

    //     if (!$user || !$user->role) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'User not found'
    //         ], 404);
    //     }

    //     // 4. Check allowed roles
    //     $allowedRoles = [
    //         'customer',
    //         'retailer',
    //         'delivery agent',
    //     ];

    //     if (!in_array(
    //         strtolower(trim($user->role->name)),
    //         $allowedRoles,
    //         true
    //     )) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'This role is not allowed to login'
    //         ], 403);
    //     }

    //     // 5. OTP LOGIN
    //     if ($type === 'otp') {

    //         $key = 'login-otp-' . $user->mobile;

    //         if (RateLimiter::tooManyAttempts($key, 3)) {
    //             return response()->json([
    //                 'status' => false,
    //                 'message' => 'Too many OTP requests. Try again after '
    //                     . RateLimiter::availableIn($key)
    //                     . ' seconds'
    //             ], 429);
    //         }

    //         $smsUser = config('services.sms.user');
    //         $smsKey = config('services.sms.key');
    //         $senderId = config('services.sms.senderid');

    //         if (!$smsUser || !$smsKey || !$senderId) {
    //             Log::error('SMS gateway configuration missing');

    //             return response()->json([
    //                 'status' => false,
    //                 'message' => 'SMS gateway configuration missing'
    //             ], 503);
    //         }

    //         $otp = (string) random_int(100000, 999999);

    //         try {
    //             $response = Http::asForm()
    //                 ->timeout(30)
    //                 ->post('http://redirect.ds3.in/submitsms.jsp', [
    //                     'user'     => $smsUser,
    //                     'key'      => $smsKey,
    //                     'mobile'   => '91' . $user->mobile,
    //                     'message'  => "Your OTP for login is {$otp}",
    //                     'senderid' => $senderId,
    //                     'accusage' => '10',
    //                 ]);

    //             $gatewayBody = trim($response->body());

    //             // Log response without OTP or API credentials
    //             Log::info('SMS Gateway Response', [
    //                 'http_status' => $response->status(),
    //                 'response_body' => $gatewayBody,
    //                 'mobile_last4' => substr($user->mobile, -4),
    //             ]);

    //             if (!$response->successful()) {
    //                 return response()->json([
    //                     'status' => false,
    //                     'message' => 'SMS gateway HTTP request failed'
    //                 ], 502);
    //             }

    //             // Handle known gateway failures
    //             if (
    //                 $gatewayBody === '' ||
    //                 preg_match('/^\s*fail\s*,/i', $gatewayBody)
    //             ) {
    //                 if (stripos($gatewayBody, 'InsufficientBalance') !== false) {
    //                     return response()->json([
    //                         'status' => false,
    //                         'message' => 'SMS balance is insufficient. Please recharge your SMS account.'
    //                     ], 503);
    //                 }

    //                 return response()->json([
    //                     'status' => false,
    //                     'message' => 'SMS gateway rejected the OTP request'
    //                 ], 502);
    //             }

    //             /*
    //             * IMPORTANT:
    //             * Confirm this success-response rule against DS3's
    //             * documented response format before production use.
    //             * An HTTP 200 response alone does not guarantee delivery.
    //             */

    //             // Save OTP only after gateway response passes the checks
    //             $user->update([
    //                 'otp' => $otp,
    //                 'otp_expires_at' => now()->addMinutes(5),
    //             ]);

    //             RateLimiter::hit($key, 300);

    //             $result = [
    //                 'status' => true,
    //                 'message' => 'OTP request accepted by SMS gateway'
    //             ];

    //             // Never expose OTP in production
    //             if (
    //                 app()->environment('local') &&
    //                 config('services.sms.debug_otp', false)
    //             ) {
    //                 $result['otp'] = $otp;
    //                 $result['debug'] = true;
    //             }

    //             return response()->json($result, 200);

    //         } catch (\Illuminate\Http\Client\ConnectionException $e) {

    //             Log::error('SMS gateway connection failed', [
    //                 'error' => $e->getMessage(),
    //             ]);

    //             return response()->json([
    //                 'status' => false,
    //                 'message' => 'Unable to connect to SMS gateway'
    //             ], 502);

    //         } catch (\Throwable $e) {

    //             Log::error('OTP login error', [
    //                 'error' => $e->getMessage(),
    //             ]);

    //             return response()->json([
    //                 'status' => false,
    //                 'message' => 'Unable to process OTP request'
    //             ], 500);
    //         }
    //     }

    //     // 6. PASSWORD LOGIN
    //     if (!Hash::check($request->password, $user->password)) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'Invalid password'
    //         ], 401);
    //     }

    //     $token = $user->createToken('auth_token')->plainTextToken;

    //     return response()->json([
    //         'status' => true,
    //         'message' => 'Login successful',
    //         'token' => $token,
    //         'token_type' => 'Bearer',
    //         'user' => [
    //             'id' => $user->id,
    //             'name' => trim(
    //                 $user->first_name . ' ' . $user->last_name
    //             ),
    //             'mobile' => $user->mobile,
    //             'email' => $user->email,
    //             'role_id' => $user->role_id,
    //             'role' => $user->role->name,
    //         ]
    //     ], 200);
    // }

    public function login(Request $request, $type)
    {
        // 1. Validate login type
        if (!in_array($type, ['password', 'otp'], true)) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid login type'
            ], 400);
        }

        // 2. Validate request
        $rules = [
            'mobile' => ['required', 'regex:/^[6-9][0-9]{9}$/'],
        ];

        if ($type === 'password') {
            $rules['password'] = ['required', 'string'];
        } else {
            $rules['password'] = ['prohibited'];
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // 3. Find user
        $user = User::with('role')
            ->where('mobile', $request->mobile)
            ->first();

        if (!$user || !$user->role) {
            return response()->json([
                'status' => false,
                'message' => 'User not found'
            ], 404);
        }

        // 4. Allowed roles
        $allowedRoles = [
            'customer',
            'retailer',
            'delivery agent',
        ];

        if (!in_array(
            strtolower(trim($user->role->name)),
            $allowedRoles,
            true
        )) {
            return response()->json([
                'status' => false,
                'message' => 'This role is not allowed to login'
            ], 403);
        }

        // 5. OTP LOGIN with testing otp generate
        // if ($type === 'otp') {

        //     $key = 'login-otp-' . $user->mobile;

        //     if (RateLimiter::tooManyAttempts($key, 3)) {
        //         return response()->json([
        //             'status' => false,
        //             'message' => 'Too many OTP requests. Try again after '
        //                 . RateLimiter::availableIn($key)
        //                 . ' seconds'
        //         ], 429);
        //     }

        //     $otp = (string) random_int(100000, 999999);

        //     $smsUser = config('services.sms.user');
        //     $smsKey = config('services.sms.key');
        //     $senderId = config('services.sms.senderid');

        //     $smsSent = false;
        //     $gatewayBody = '';

        //     try {
        //         if ($smsUser && $smsKey && $senderId) {

        //             $response = Http::asForm()
        //                 ->timeout(30)
        //                 ->post('http://redirect.ds3.in/submitsms.jsp', [
        //                     'user'     => $smsUser,
        //                     'key'      => $smsKey,
        //                     'mobile'   => '91' . $user->mobile,
        //                     'message'  => "Your OTP for login is {$otp}",
        //                     'senderid' => $senderId,
        //                     'accusage' => '10',
        //                 ]);

        //             $gatewayBody = trim($response->body());

        //             Log::info('SMS Gateway Response', [
        //                 'http_status' => $response->status(),
        //                 'response_body' => $gatewayBody,
        //                 'mobile_last4' => substr($user->mobile, -4),
        //             ]);

        //             $smsSent = $response->successful()
        //                 && $gatewayBody !== ''
        //                 && !preg_match('/^\s*fail\s*,/i', $gatewayBody);
        //         }

        //     } catch (\Illuminate\Http\Client\ConnectionException $e) {

        //         Log::warning('SMS gateway unavailable', [
        //             'error' => $e->getMessage(),
        //         ]);

        //     }

        //     // TEMPORARY TEST MODE:
        //     // OTP is saved even when the SMS gateway has no balance.
        //     // Use only in a controlled development/testing environment.

        //     $user->update([
        //         'otp' => $otp,
        //         'otp_expires_at' => now()->addMinutes(5),
        //     ]);

        //     RateLimiter::hit($key, 300);

        //     if (!$smsSent) {
        //         Log::warning('Testing OTP generated without confirmed SMS delivery', [
        //             'mobile_last4' => substr($user->mobile, -4),
        //             'gateway_error' => $gatewayBody,
        //         ]);
        //     }

        //     return response()->json([
        //         'status' => true,
        //         'message' => $smsSent
        //             ? 'OTP request accepted by SMS gateway'
        //             : 'Testing OTP generated. SMS was not confirmed.',
        //         'otp' => $otp,
        //         'otp_expires_in' => 300,
        //         'sms_sent' => $smsSent,
        //     ], 200);
        // }

        // testing otp show in response untile sms recharge for actual otp send
        if ($type === 'otp') {

            $key = 'login-otp-' . $user->mobile;

            if (RateLimiter::tooManyAttempts($key, 3)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Too many OTP requests. Try again after '
                        . RateLimiter::availableIn($key) . ' seconds'
                ], 429);
            }

            $otp = (string) random_int(100000, 999999);

            $smsUser = config('services.sms.user');
            $smsKey = config('services.sms.key');
            $senderId = config('services.sms.senderid');

            $smsSent = false;
            $gatewayBody = '';

            try {
                if ($smsUser && $smsKey && $senderId) {

                    $response = Http::asForm()
                        ->timeout(30)
                        ->post('http://redirect.ds3.in/submitsms.jsp', [
                            'user'     => $smsUser,
                            'key'      => $smsKey,
                            'mobile'   => '91' . $user->mobile,
                            'message'  => "Your OTP for login is {$otp}",
                            'senderid' => $senderId,
                            'accusage' => '10',
                        ]);

                    $gatewayBody = trim($response->body());

                    Log::info('SMS Gateway Response', [
                        'http_status' => $response->status(),
                        'response_body' => $gatewayBody,
                        'mobile_last4' => substr($user->mobile, -4),
                    ]);

                    // Detect insufficient balance and gateway failures
                    $isGatewayFailure = preg_match(
                        '/^\s*fail\s*,/i',
                        $gatewayBody
                    );

                    $smsSent = $response->successful()
                        && $gatewayBody !== ''
                        && !$isGatewayFailure;

                }

            } catch (\Illuminate\Http\Client\ConnectionException $e) {

                Log::warning('SMS gateway connection failed', [
                    'error' => $e->getMessage(),
                ]);
            }

            /*
            * TEMPORARY TESTING:
            * Save OTP even if SMS is not sent.
            * Remove OTP from the API response before production.
            */

            $user->update([
                'otp' => $otp,
                'otp_expires_at' => now()->addMinutes(5),
            ]);

            RateLimiter::hit($key, 300);

            if (!$smsSent) {

                $message = stripos(
                    $gatewayBody,
                    'InsufficientBalance'
                ) !== false
                    ? 'SMS balance is insufficient. Testing OTP generated.'
                    : 'SMS was not confirmed. Testing OTP generated.';

                return response()->json([
                    'status' => true,
                    'message' => $message,
                    'otp' => $otp,
                    'otp_expires_in' => 300,
                    'sms_sent' => false,
                ], 200);
            }

            return response()->json([
                'status' => true,
                'message' => 'OTP request accepted by SMS gateway',
                'otp' => $otp,
                'otp_expires_in' => 300,
                'sms_sent' => true,
            ], 200);
        }

        // 6. PASSWORD LOGIN
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
                'name' => trim(
                    $user->first_name . ' ' . $user->last_name
                ),
                'mobile' => $user->mobile,
                'email' => $user->email,
                'role_id' => $user->role_id,
                'role' => $user->role->name,
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
                'status' => true,
                'message' => 'If an account exists, OTP has been sent to the registered mobile number.'
            ]);
        }

        // ⏱ Rate limiting
        $key = 'forgot-password-' . $request->mobile;
        if (RateLimiter::tooManyAttempts($key, 3)) {
            return response()->json([
                'status' => false,
                'message' => 'Too many requests. Please try again later.'
            ], 200);
        }
        RateLimiter::hit($key, 300); // 5 minutes

        // 🔐 Generate OTP (plain text for verification)
        $otp = rand(100000, 999999);

        $user->update([
            'otp' => $otp, // store plain OTP
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

        return response()->json([
            'status'  => true,
            'message' => 'If an account exists, OTP has been sent to the registered mobile number.'
        ]);
    }

    // public function verifyOtp(Request $request, $type)
    // {
    //     /* ================= VALID OTP TYPE ================= */
    //     if (!in_array($type, ['login_otp', 'forgot_password_otp'])) {
    //         return response()->json([
    //             'status'  => false,
    //             'message' => 'Invalid OTP type'
    //         ], 200);
    //     }

    //     /* ================= VALIDATION ================= */
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

    //     /* ================= USER FETCH ================= */
    //     $user = User::where('mobile', $request->mobile)->first();

    //     if (!$user) {
    //         return response()->json([
    //             'status'  => false,
    //             'message' => 'User not found'
    //         ], 200);
    //     }

    //     /* ================= OTP CHECK ================= */
    //     $otpField     = 'otp';
    //     $expiresField = 'otp_expires_at';

    //     if (!$user->$otpField || !$user->$expiresField) {
    //         return response()->json([
    //             'status'  => false,
    //             'message' => 'OTP not requested'
    //         ], 400);
    //     }

    //     if (now()->greaterThan($user->$expiresField)) {
    //         return response()->json([
    //             'status'  => false,
    //             'message' => 'OTP expired. Please request a new OTP.'
    //         ], 200);
    //     }

    //     if ($request->otp != $user->$otpField) {
    //         return response()->json([
    //             'status'  => false,
    //             'message' => 'Invalid OTP'
    //         ], 200);
    //     }

    //     /* ================= CLEAR OTP ================= */
    //     $user->update([
    //         $otpField     => null,
    //         $expiresField => null,
    //     ]);

    //     /* ================= LOGIN OTP ================= */
    //     if ($type === 'login_otp') {

    //         $user->tokens()->delete();

    //         $token = $user->createToken('auth_token')->plainTextToken;

    //         return response()->json([
    //             'status'      => true,
    //             'message'     => 'Login successful',
    //             'token'       => $token,
    //             'token_type'  => 'Bearer',
    //             'user'        => [
    //                 'id'    => $user->id,
    //                 'name'  => trim($user->first_name . ' ' . $user->last_name),
    //                 'mobile' => $user->mobile,
    //                 'email' => $user->email,
    //                 'role_id' => $user->role_id,
    //                 'role'  => optional($user->role)->name,
    //             ]
    //         ], 200);
    //     }

    //     /* ================= FORGOT PASSWORD OTP ================= */
    //     return response()->json([
    //         'status'  => true,
    //         'message' => 'OTP verified. You may now reset your password.'
    //     ], 200);
    // }

    
    public function verifyOtp(Request $request, $type)
    {
        // 1. Validate OTP type
        if (!in_array($type, ['login_otp', 'forgot_password_otp'], true)) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid OTP type'
            ], 400);
        }

        // 2. Normalize OTP input
        $request->merge([
            'mobile' => trim((string) $request->input('mobile', '')),
            'otp' => trim((string) $request->input('otp', '')),
        ]);

        // 3. Validate request
        $validator = Validator::make($request->all(), [
            'mobile' => ['required', 'regex:/^[6-9][0-9]{9}$/'],
            'otp' => ['required', 'regex:/^[0-9]{6}$/'],
        ], [
            'otp.required' => 'Please enter OTP.',
            'otp.regex' => 'OTP must contain exactly 6 digits.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // 4. Find user
        $user = User::with('role')
            ->where('mobile', $request->mobile)
            ->first();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User not found'
            ], 404);
        }

        // 5. Check saved OTP
        if (
            $user->otp === null ||
            $user->otp_expires_at === null
        ) {
            return response()->json([
                'status' => false,
                'message' => 'OTP not requested or already used'
            ], 400);
        }

        // 6. Check expiry
        if (now()->greaterThanOrEqualTo($user->otp_expires_at)) {
            $user->update([
                'otp' => null,
                'otp_expires_at' => null,
            ]);

            return response()->json([
                'status' => false,
                'message' => 'OTP expired. Please request a new OTP.'
            ], 400);
        }

        // 7. Compare OTP as strings
        if (!hash_equals((string) $user->otp, $request->otp)) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid OTP'
            ], 400);
        }

        // 8. Clear OTP after successful verification
        $user->update([
            'otp' => null,
            'otp_expires_at' => null,
        ]);

        // 9. Login OTP
        if ($type === 'login_otp') {

            $user->tokens()->delete();

            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'status' => true,
                'message' => 'OTP verified. Login successful.',
                'token' => $token,
                'token_type' => 'Bearer',
                'user' => [
                    'id' => $user->id,
                    'name' => trim(
                        $user->first_name . ' ' . $user->last_name
                    ),
                    'mobile' => $user->mobile,
                    'email' => $user->email,
                    'role_id' => $user->role_id,
                    'role' => optional($user->role)->name,
                ]
            ], 200);
        }

        // 10. Forgot password OTP
        return response()->json([
            'status' => true,
            'message' => 'OTP verified. You may now reset your password.'
        ], 200);
    }

    public function resetPassword(Request $request)
    {
        /* ================= VALIDATION ================= */
        $validator = Validator::make($request->all(), [
            'mobile' => 'required|digits:10',
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

        /* ================= USER FETCH ================= */
        $user = User::where('mobile', $request->mobile)->first();

        if (!$user) {
            return response()->json([
                'status'  => false,
                'message' => 'User not found'
            ], 200);
        }

        /* ================= PASSWORD UPDATE ================= */
        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'Password reset successful',
            'user'    => [
                'id'      => $user->id,
                'name'    => trim($user->first_name . ' ' . $user->last_name),
                'mobile'  => $user->mobile,
                'email'   => $user->email,
                'role_id' => $user->role_id,
                'role'    => ucfirst(optional($user->role)->name),
            ]
        ], 200);
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

    public function orderTimeCheck(Request $request)
    {
        $user = $request->user();

        // ✅ Customer check
        if (!$user || !$user->role || strtolower($user->role->name) !== 'customer') {
            return response()->json([
                'status' => false,
                'order_allowed' => false,
                'message' => 'Unauthorized user'
            ], 403);
        }

        // ✅ FORCE IST TIME
        $now = Carbon::now('Asia/Kolkata')->format('H:i');

        $start = '07:00';
        $end   = '19:00';

        if ($now >= $start && $now <= $end) {
            return response()->json([
                'status' => true,
                'order_allowed' => true,
                'message' => 'Order allowed'
            ], 200);
        }

        return response()->json([
            'status' => false,
            'order_allowed' => false,
            'message' => 'Orders allowed only between 7:00 AM and 7:00 PM'
        ], 403);
    }

    public function checkPincode(Request $request)
    {
        $request->validate([
            'pincode' => 'required|digits:6'
        ]);

        $user = $request->user();

        $service = WarehouseServicePincode::where('pincode', $request->pincode)->first();

        if (!$service) {
            return response()->json([
                'status' => false,
                'message' => 'Service not available in this area'
            ]);
        }

        $newWarehouseId = $service->warehouse_id;

        $cart = Cart::with('items')->where('user_id', $user->id)->first();

        $updatedItems = [];
        $removedItems = [];

        if ($cart && $cart->warehouse_id != $newWarehouseId) {

            foreach ($cart->items as $item) {

                $stock = WarehouseStock::where('product_id', $item->product_id)
                    ->where('warehouse_id', $newWarehouseId)
                    ->sum('quantity');

                // Remove item if no stock
                if ($stock <= 0) {
                    $removedItems[] = $item->product_id;
                    $item->delete();
                    continue;
                }

                // 🔧 Adjust quantity
                if ($item->qty > $stock) {
                    $item->update(['qty' => $stock]);
                    $updatedItems[] = [
                        'product_id' => $item->product_id,
                        'new_qty' => $stock
                    ];
                }
            }

            //Update cart warehouse
            $cart->update(['warehouse_id' => $newWarehouseId]);
        }

        return response()->json([
            'status' => true,
            'message' => 'Cart updated based on new location',
            'data' => [
                'warehouse_id' => $newWarehouseId,
                'pincode'       =>$request->pincode,
                'removed_items' => $removedItems,
                'updated_items' => $updatedItems
            ]
        ]);
    }


}
