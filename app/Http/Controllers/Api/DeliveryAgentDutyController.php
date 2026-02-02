<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DeliveryAgentDutyController extends Controller
{
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

        // ❌ Already online
        if ($user->is_online == 1) {
            return response()->json([
                'status' => false,
                'message' => 'Agent already online'
            ], 400);
        }

        // ✅ Go online & start duty
        $user->update([
            'is_online'       => 1,
            'duty_start_time' => now()
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Agent is online',
            'dutyStartTime' => now()->toDateTimeString()
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

        // ❌ Already offline
        if ($user->is_online == 0) {
            return response()->json([
                'status' => false,
                'message' => 'Agent already offline'
            ], 400);
        }

        // 🛑 FALLBACK: no duty start time
        if (!$user->duty_start_time) {
            $user->update([
                'is_online' => 0,
                'duty_start_time' => null
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Agent is offline (no duty time recorded)',
                'sessionDutyMinutes' => 0,
                'totalDutyMinutes' => $user->total_duty_minutes ?? 0
            ]);
        }

        // ✅ NORMAL FLOW
        $dutyEndTime = now();

        $sessionMinutes = $dutyEndTime->diffInMinutes(
            \Carbon\Carbon::parse($user->duty_start_time)
        );

        $totalDutyMinutes = ($user->total_duty_minutes ?? 0) + $sessionMinutes;

        $user->update([
            'is_online' => 0,
            'duty_start_time' => null,
            'total_duty_minutes' => $totalDutyMinutes
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Agent is offline',
            'dutyEndTime' => $dutyEndTime->toDateTimeString(),
            'sessionDutyMinutes' => $sessionMinutes,
            'totalDutyMinutes' => $totalDutyMinutes
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

            // 1️⃣ OTP must exist
            if (!$customer->otp || !$customer->otp_expires_at) {
                return response()->json([
                    'status' => false,
                    'message' => 'OTP not generated'
                ], 422);
            }

            // 2️⃣ Expiry check
            if (now()->gt($customer->otp_expires_at)) {
                return response()->json([
                    'status' => false,
                    'message' => 'OTP expired'
                ], 422);
            }

            // 3️⃣ Match OTP
            if ((string)$customer->otp !== (string)$request->otp) {
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid OTP'
                ], 422);
            }

            // ✅ Mark delivered
            $order->update([
                'status' => 'delivered'
            ]);

            // ✅ Clear OTP
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
                'latitude'  => $request->latitude,
                'longitude' => $request->longitude
            ]);
        }

        $order = Order::with([
            'customer',
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

        if (!$order) {
            return response()->json([
                'status'  => true,
                'message' => 'Not Yet Started',
                'data'    => null
            ], 200);
        }

        return response()->json([
            'status' => true,
            'data'   => [
                'orderId' => $order->id,
                'status'  => ucfirst(str_replace('_', ' ', $order->status)),

                'customer' => [
                    'name'   => trim(
                        optional($order->customer)->first_name . ' ' .
                            optional($order->customer)->last_name
                    ),
                    'mobile' => optional($order->customer)->mobile
                ],

                'deliveryAddress' => [
                    'address'   => optional($order->deliveryAddress)->address,
                    'area'      => optional($order->deliveryAddress)->area,
                    'city'      => optional($order->deliveryAddress)->city,
                    'pincode'   => optional($order->deliveryAddress)->postcode,
                    'latitude'  => optional($order->deliveryAddress)->latitude,
                    'longitude' => optional($order->deliveryAddress)->longitude,
                ]
            ]
        ], 200);
    }

    public function profileSummary(Request $request)
    {
        $user = $request->user();

        // 🔐 Delivery agent only
        if (!$user->role || strtolower($user->role->name) !== 'delivery agent') {
            return response()->json([
                'status' => false,
                'message' => 'Access denied'
            ], 403);
        }

        // ⏱️ 30 minutes per order
        $totalOrders = Order::where('delivery_agent_id', $user->id)
            ->where('status', 'delivered')
            ->whereNotNull('delivered_at')
            ->count();

        $todayOrders = Order::where('delivery_agent_id', $user->id)
            ->where('status', 'delivered')
            ->whereDate('delivered_at', today())
            ->count();

        return response()->json([
            'status' => true,
            'data' => [
                'totalDutyTime' => round(($totalOrders * 30) / 60, 2), // hours
                'todayDutyTime' => round(($todayOrders * 30) / 60, 2)  // hours
            ]
        ]);
    }

    public function performanceGraph(Request $request)
    {
        $user = $request->user();

        // 🔐 Delivery agent only
        if (!$user->role || strtolower($user->role->name) !== 'delivery agent') {
            return response()->json([
                'status' => false,
                'message' => 'Access denied'
            ], 403);
        }

        $range = $request->get('range', 'daily');

        if (!in_array($range, ['daily', 'weekly', 'monthly'])) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid range'
            ], 422);
        }

        $query = Order::where('delivery_agent_id', $user->id)
            ->where('status', 'delivered'); // removed delivered_at condition

        // 📅 Date filter
        if ($request->filled(['fromDate', 'toDate'])) {
            $query->whereBetween('delivered_at', [
                Carbon::parse($request->fromDate)->startOfDay(),
                Carbon::parse($request->toDate)->endOfDay()
            ]);
        }

        // 📊 Grouping logic
        if ($range === 'weekly') {
            $query->select(
                DB::raw('YEARWEEK(delivered_at, 1) as date'),
                DB::raw('COUNT(*) as totalOrders')
            )->groupBy('date')->orderBy('date');
        } elseif ($range === 'monthly') {
            $query->select(
                DB::raw('DATE_FORMAT(delivered_at, "%Y-%m") as date'),
                DB::raw('COUNT(*) as totalOrders')
            )->groupBy('date')->orderBy('date');
        } else {
            // daily
            $query->select(
                DB::raw('DATE(delivered_at) as date'),
                DB::raw('COUNT(*) as totalOrders')
            )->groupBy('date')->orderBy('date');
        }

        $data = $query->get();

        // ⏱️ Assume 30 min per order
        $graph = $data->map(function ($row) {
            $minutes = $row->totalOrders * 30;

            return [
                'date'        => $row->date,
                'totalOrders' => (int) $row->totalOrders,
                'totalHours'  => round($minutes / 60, 2)
            ];
        });

        return response()->json([
            'status' => true,
            'data'   => $graph
        ]);
    }

    public function startDuty(Request $request)
    {
        $agent = $request->user();
        $now = now();

        // FIX invalid state automatically
        if ($agent->is_online == 1 && !$agent->duty_start_time) {
            $agent->update([
                'is_online' => 0
            ]);
        }

        // Already active
        if ($agent->is_online == 1 && $agent->duty_start_time) {
            return response()->json([
                'status' => false,
                'message' => 'Already online'
            ], 422);
        }

        // Start / Resume duty
        $agent->update([
            'is_online' => 1,
            'duty_start_time' => $now,
            'duty_paused_at' => null
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Duty started',
            'todayDutyTime' => $agent->total_duty_minutes,
            'startedAt' => $now
        ]);
    }

    public function pauseDuty(Request $request)
    {
        $agent = $request->user();

        if ($agent->is_online != 1 || !$agent->duty_start_time) {
            return response()->json([
                'status' => false,
                'message' => 'Duty not active'
            ], 422);
        }

        $pausedAt = now();

        $minutes = $pausedAt->diffInMinutes(
            \Carbon\Carbon::parse($agent->duty_start_time)
        );

        $agent->update([
            'is_online' => 0,
            'duty_start_time' => null,
            'duty_paused_at' => $pausedAt,
            'total_duty_minutes' => $agent->total_duty_minutes + $minutes
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Duty paused',
            'todayDutyTime' => $agent->total_duty_minutes,
            'pausedAt' => $pausedAt
        ]);
    }
    public function resumeDuty(Request $request)
    {
        $agent = $request->user();

        // ❌ Cannot resume if already online
        if ($agent->is_online == 1) {
            return response()->json([
                'status' => false,
                'message' => 'Duty already active'
            ], 422);
        }

        // ❌ Cannot resume if never started
        if (!$agent->duty_paused_at) {
            return response()->json([
                'status' => false,
                'message' => 'Duty not paused'
            ], 422);
        }

        $resumedAt = now();

        $agent->update([
            'is_online' => 1,
            'duty_start_time' => $resumedAt,
            'duty_paused_at' => null
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Duty resumed',
            'todayDutyTime' => $agent->total_duty_minutes,
            'resumedAt' => $resumedAt
        ]);
    }
    public function stopDuty(Request $request)
    {
        $agent = $request->user();

        // If already offline
        if ($agent->is_online == 0 && !$agent->duty_start_time) {
            return response()->json([
                'status' => false,
                'message' => 'Already offline'
            ], 422);
        }

        $endedAt = now();
        $totalMinutes = $agent->total_duty_minutes;

        // If duty currently active, calculate session time
        if ($agent->is_online == 1 && $agent->duty_start_time) {
            $sessionMinutes = $endedAt->diffInMinutes(
                \Carbon\Carbon::parse($agent->duty_start_time)
            );
            $totalMinutes += $sessionMinutes;
        }

        // Stop duty completely
        $agent->update([
            'is_online' => 0,
            'duty_start_time' => null,
            'duty_paused_at' => null,
            'total_duty_minutes' => $totalMinutes
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Duty stopped',
            'todayDutyTime' => $totalMinutes,
            'endedAt' => $endedAt
        ]);
    }
    public function partnerSummary(Request $request)
    {
        $agent = $request->user();

        // Calculate live duty time if online
        $todayDutyTime = $agent->total_duty_minutes;

        if ($agent->is_online == 1 && $agent->duty_start_time) {
            $todayDutyTime += now()->diffInMinutes(
                \Carbon\Carbon::parse($agent->duty_start_time)
            );
        }

        return response()->json([
            'status' => true,
            'isOnline' => (bool) $agent->is_online,
            'todayDutyTime' => $todayDutyTime,
            'stats' => [
                // placeholders (extend later)
                'totalOrdersToday' => 0,
                'deliveredOrders' => 0,
                'earningsToday' => 0
            ]
        ]);
    }

    public function resetDailyDuty()
    {
        DB::beginTransaction();

        try {
            $date    = Carbon::yesterday('Asia/Kolkata')->toDateString();
            $resetAt = Carbon::now('Asia/Kolkata');

            // 🧮 Total duty time of all agents (previous day snapshot)
            $totalDutyTime = DB::table('users')
                ->whereNull('deleted_at')
                ->sum('total_duty_minutes');

            // 🔄 Reset duty for all agents
            DB::table('users')
                ->whereNull('deleted_at')
                ->update([
                    'duty_start_time'     => null,
                    'duty_paused_at'      => null,
                    'total_duty_minutes'  => 0,
                    'is_online'           => 0,
                    'updated_at'          => now(),
                ]);

            DB::commit();

            return response()->json([
                'status'         => true,
                'date'           => $date,
                'totalDutyTime'  => $totalDutyTime, // in minutes
                'resetAt'        => $resetAt,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status'  => false,
                'message' => 'Daily duty reset failed',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
    public function dayPerformance(Request $request)
    {
        $date = $request->query('date', Carbon::today()->toDateString());

        $totalDutyTime = DB::table('users')
            ->whereNull('deleted_at')
            ->sum('total_duty_minutes');

        $totalOrders = DB::table('orders')
            ->whereDate('created_at', $date)
            ->count();

        return response()->json([
            'date'          => $date,
            'totalDutyTime' => $totalDutyTime,
            'totalOrders'   => $totalOrders,
        ]);
    }
    public function weekPerformance(Request $request)
    {
        $start = Carbon::parse($request->weekStart);
        $data = [];

        $dailyDuty = DB::table('users')->sum('total_duty_minutes');

        for ($i = 0; $i < 7; $i++) {
            $data[] = [
                'date'          => $start->copy()->addDays($i)->toDateString(),
                'totalDutyTime' => $dailyDuty,
            ];
        }

        return response()->json($data);
    }
    public function monthPerformance(Request $request)
    {
        $month = $request->query('month', Carbon::now()->format('Y-m'));

        $startDate = Carbon::createFromFormat('Y-m', $month)
            ->startOfMonth();

        $endDate = Carbon::createFromFormat('Y-m', $month)
            ->endOfMonth();

        $results = [];

        // Current total duty (no history available)
        $totalDutyTime = DB::table('users')
            ->whereNull('deleted_at')
            ->sum('total_duty_minutes');

        while ($startDate->lte($endDate)) {
            $results[] = [
                'date'          => $startDate->toDateString(),
                'totalDutyTime' => $totalDutyTime, // best possible
            ];

            $startDate->addDay();
        }

        return response()->json($results);
    }
}
