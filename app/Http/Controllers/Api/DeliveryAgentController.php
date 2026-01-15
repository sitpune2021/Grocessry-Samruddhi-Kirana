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
use App\Models\DriverVehicle;
use Illuminate\Support\Facades\Log;
use App\Models\Order;

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

    public function getProfileImage(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized'
            ], 401);
        }

        // Profile image path
        if (!$user->profile_photo) {
            return response()->json([
                'status' => true,
                'imageUrl' => null
            ]);
        }

        return response()->json([
            'status' => true,
            'imageUrl' => asset('storage/' . $user->profile_photo)
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

    public function goOnline(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthenticated'
            ], 401);
        }

        // Role check
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

        // ✅ Store login time
        $user->update([
            'is_online'     => 1,
            'last_login_at' => now()
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Agent is online'
        ]);
    }

    public function goOffline(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthenticated'
            ], 401);
        }

        if (!$user->role || strtolower($user->role->name) !== 'delivery agent') {
            return response()->json([
                'status' => false,
                'message' => 'Access denied'
            ], 403);
        }

        $user->update([
            'is_online'     => 0,
            'last_login_at' => null
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Agent is offline'
        ]);
    }

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
        $agent = DeliveryAgent::with('user')
            ->where('user_id', $request->user()->id)
            ->first();

        if (!$agent) {
            return response()->json([
                'status' => false,
                'message' => 'Agent profile not found'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data' => [
                'agent_id' => $agent->id,
                'shop_id' => $agent->shop_id,
                'dob' => $agent->dob,
                'gender' => $agent->gender,
                'aadhaar_card' => $agent->aadhaar_card,
                'driving_license' => $agent->driving_license,
                'vehicle_type' => $agent->vehicle_type,
                'vehicle_number' => $agent->vehicle_number,
                'address' => $agent->address,
                'status' => $agent->status,

                // 👇 from users table
                'user' => [
                    'id' => $agent->user->id,
                    'first_name' => $agent->user->first_name,
                    'last_name' => $agent->user->last_name,
                    'email' => $agent->user->email,
                    'mobile' => $agent->user->mobile,
                    'profile_photo' => $agent->user->profile_photo,
                    'is_online' => $agent->user->is_online,
                ]
            ]
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
    public function updateAddress(Request $request)
    {
        $user = $request->user(); // logged-in partner

        $request->validate([
            'address'  => 'required|string|max:255',
        ]);

        $agent = DeliveryAgent::where('user_id', $user->id)->first();

        if (!$agent) {
            return response()->json([
                'status' => false,
                'message' => 'Delivery agent not found'
            ], 404);
        }

        $agent->update([
            'address'  => $request->address,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Address updated successfully',
            'data' => $agent
        ]);
    }
    public function updateProfileImage(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'profile_photo' => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        if ($request->hasFile('profile_photo')) {

            $file = $request->file('profile_photo'); // ✅ correct key
            $filename = time() . '_' . $file->getClientOriginalName();

            $path = $file->storeAs('profile_photos', $filename, 'public');

            $user->profile_photo = $path;
            $user->save();
        }

        return response()->json([
            'status' => true,
            'message' => 'Profile image updated successfully',
            'profile_photo' => $user->profile_photo
        ]);
    }

    public function updateVehicle(Request $request)
    {
        $request->validate([
            'vehicleType'   => 'required|string',
            'vehicleNumber' => 'required|string'
        ]);

        // Find delivery agent
        $agent = DeliveryAgent::where('user_id', $request->user()->id)->first();

        if (!$agent) {
            return response()->json([
                'status' => false,
                'message' => 'Delivery agent not found'
            ], 404);
        }

        // Update delivery_agents table (optional if you still need it)
        $agent->update([
            'vehicle_type'   => $request->vehicleType,
            'vehicle_number' => $request->vehicleNumber
        ]);

        // Update driver_vehicles table
        $driverVehicle = DriverVehicle::where('driver_id', $request->user()->id)->first();

        if ($driverVehicle) {
            // Update existing vehicle record
            $driverVehicle->update([
                'vehicle_type' => $request->vehicleType,
                'vehicle_no'   => $request->vehicleNumber,
                'updated_at'   => now()
            ]);
        } else {
            // If no record exists, create one
            DriverVehicle::create([
                'driver_id'     => $request->user()->id,
                'vehicle_type'  => $request->vehicleType,
                'vehicle_no'    => $request->vehicleNumber,
                'active'        => 1,
                'created_at'    => now(),
                'updated_at'    => now()
            ]);
        }

        return response()->json([
            'status' => true,
            'message' => 'Vehicle updated successfully'
        ]);
    }

    public function loginHours(Request $request)
    {
        $user = $request->user();

        if (!$user->is_online || !$user->last_login_at) {
            return response()->json([
                'status' => true,
                'totalHours' => 0
            ]);
        }

        $hours = Carbon::parse($user->last_login_at)
            ->diffInMinutes(now()) / 60;

        return response()->json([
            'status' => true,
            'totalHours' => round($hours, 2)
        ]);
    }

    public function onlineStatus(Request $request)
    {
        $isOnline = (bool) $request->user()->is_online;

        return response()->json([
            'status' => true,
            'message' => $isOnline ? 'Agent is online' : 'Agent is offline',
            // 'isOnline' => $isOnline
        ]);
    }

    public function deliveryOtp(Request $request, $orderId, $type)
    {
        try {
            $agent = $request->user();

            // Only allow send or resend
            if (!in_array($type, ['send', 'resend'])) {
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid OTP type'
                ], 422);
            }

            $order = Order::where('id', $orderId)
                ->where('delivery_agent_id', $agent->id)
                ->firstOrFail();

            $customer = User::findOrFail($order->user_id);

            $otp = rand(1000, 9999);

            // Store OTP in customer (users table)
            $customer->update([
                'otp' => $otp,
                'otp_expires_at' => now()->addMinutes(5),
            ]);

            $message = $type === 'send'
                ? "Your delivery OTP is {$otp}. Please share with delivery agent."
                : "Your delivery OTP is {$otp}. (Resent) Please share with delivery agent.";

            // Send OTP to CUSTOMER
            Http::asForm()->post('http://redirect.ds3.in/submitsms.jsp', [
                'user'     => env('SMS_USER'),
                'key'      => env('SMS_KEY'),
                'mobile'   => '91' . $customer->mobile,
                'message'  => $message,
                'senderid' => env('SMS_SENDERID'),
                'accusage' => '10',
            ]);

            return response()->json([
                'status' => true,
                'message' => "OTP {$type} successfully"
            ]);
        } catch (\Exception $e) {

            Log::error('Delivery OTP Error', [
                'order_id' => $orderId,
                'type'     => $type,
                'error'    => $e->getMessage(),
            ]);

            return response()->json([
                'status' => false,
                'message' => 'Failed to process delivery OTP'
            ], 500);
        }
    }
    public function verifyDeliveryOtp(Request $request, $orderId)
    {
        try {
            $request->validate([
                'otp' => 'required|digits:4'
            ]);

            $agent = $request->user();

            $order = Order::where('id', $orderId)
                ->where('delivery_agent_id', $agent->id)
                ->firstOrFail();

            $customer = User::findOrFail($order->user_id);

            if ($customer->otp !== $request->otp) {
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid OTP'
                ], 422);
            }

            if (now()->gt($customer->otp_expires_at)) {
                return response()->json([
                    'status' => false,
                    'message' => 'OTP expired'
                ], 422);
            }

            // ✅ Mark order delivered
            $order->update([
                'status' => 'delivered'
            ]);

            // ✅ Clear OTP from customer
            $customer->update([
                'otp' => null,
                'otp_expires_at' => null
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Order delivered successfully'
            ]);
        } catch (\Exception $e) {

            Log::error('Verify Delivery OTP Error', [
                'order_id' => $orderId,
                'error'    => $e->getMessage()
            ]);

            return response()->json([
                'status' => false,
                'message' => 'Failed to verify delivery OTP'
            ], 500);
        }
    }

    public function getCurrentTask(Request $request)
    {
        $partner = $request->user();

        // Optional: save partner live location
        if ($request->latitude && $request->longitude) {
            $partner->update([
                'latitude' => $request->latitude,
                'longitude' => $request->longitude
            ]);
        }

        // Get current active order
        $order = Order::with([
            'customer:id,first_name,last_name,mobile',
            'deliveryAddress'
        ])
            ->where('delivery_agent_id', $partner->id)
            ->whereIn('status', [
                'assigned',
                'accepted',
                'picked_up',
                'out_for_delivery'
            ])
            ->orderBy('created_at', 'asc')
            ->first();

        // ❌ No active task
        if (!$order) {
            return response()->json([
                'status' => true,
                'message' => 'Not Yet Started',
                'data' => null
            ]);
        }

        // ✅ Active task found
        return response()->json([
            'status' => true,
            'data' => [
                'orderId' => $order->id,
                'status'  => ucfirst(str_replace('_', ' ', $order->status)),

                // ✅ Partner live location
                // 'partnerLocation' => [
                //     'latitude'  => $partner->latitude,
                //     'longitude' => $partner->longitude,
                // ],

                'customer' => [
                    'name'   => $order->customer->first_name . ' ' . $order->customer->last_name,
                    'mobile' => $order->customer->mobile
                ],

                // ✅ Delivery address with coordinates
                'deliveryAddress' => [
                    'address'   => $order->deliveryAddress->address ?? null,
                    'area'      => $order->deliveryAddress->area ?? null,
                    'city'      => $order->deliveryAddress->city ?? null,
                    'pincode'   => $order->deliveryAddress->pincode ?? null,
                    'latitude'  => $order->deliveryAddress->latitude ?? null,
                    'longitude' => $order->deliveryAddress->longitude ?? null,
                ]
            ]
        ]);
    }
    public function profileSummary(Request $request)
    {
        $user = $request->user();

        if (!$user->role || strtolower($user->role->name) !== 'delivery agent') {
            return response()->json([
                'status' => false,
                'message' => 'Access denied'
            ], 403);
        }

        $totalDutyTime = Order::where('delivery_agent_id', $user->id)
            ->where('status', 'delivered')
            ->count() * 30;

        $todayDutyTime = Order::where('delivery_agent_id', $user->id)
            ->where('status', 'delivered')
            ->whereDate('delivered_at', today())
            ->count() * 30;

        return response()->json([
            'status' => true,
            'data' => [
                'delivery_agent_id' => $user->id,
                'name'              => $user->first_name . ' ' . $user->last_name,
                // 'imageUrl'          => $user->profile_photo
                //     ? asset('storage/' . $user->profile_photo)
                //     : null,
                'totalDutyTime'     => $totalDutyTime,
                'todayDutyTime'     => $todayDutyTime
            ]
        ]);
    }

    public function performanceGraph(Request $request)
    {
        $user = $request->user();

        if (!$user->role || strtolower($user->role->name) !== 'delivery agent') {
            return response()->json([
                'status' => false,
                'message' => 'Access denied'
            ], 403);
        }

        $range = $request->range ?? 'daily';

        $query = Order::where('delivery_agent_id', $user->id)
            ->where('status', 'delivered')
            ->whereNotNull('delivered_at');

        if ($request->fromDate && $request->toDate) {
            $query->whereBetween('delivered_at', [
                $request->fromDate,
                $request->toDate
            ]);
        }

        if ($range === 'daily') {
            $data = $query
                ->select(
                    DB::raw('DATE(delivered_at) as date'),
                    DB::raw('COUNT(*) as totalOrders')
                )
                ->groupBy(DB::raw('DATE(delivered_at)'))
                ->orderBy('date')
                ->get();
        } elseif ($range === 'weekly') {
            $data = $query
                ->select(
                    DB::raw('YEARWEEK(delivered_at) as date'),
                    DB::raw('COUNT(*) as totalOrders')
                )
                ->groupBy(DB::raw('YEARWEEK(delivered_at)'))
                ->orderBy('date')
                ->get();
        } else {
            $data = $query
                ->select(
                    DB::raw('DATE_FORMAT(delivered_at, "%Y-%m") as date'),
                    DB::raw('COUNT(*) as totalOrders')
                )
                ->groupBy(DB::raw('DATE_FORMAT(delivered_at, "%Y-%m")'))
                ->orderBy('date')
                ->get();
        }

        $graph = $data->map(function ($row) {
            return [
                'date'        => $row->date,
                'totalOrders' => (int) $row->totalOrders,
                'totalHours'  => round($row->totalOrders * 0.5, 2)
            ];
        });

        return response()->json([
            'status' => true,
            'delivery_agent_id' => $user->id,
            'data' => $graph
        ]);
    }
}
