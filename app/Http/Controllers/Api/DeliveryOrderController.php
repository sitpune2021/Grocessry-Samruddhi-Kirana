<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\DeliveryNotification;
use App\Models\DeliveryNotificationSetting;
use Illuminate\Support\Facades\DB;


class DeliveryOrderController extends Controller
{
    public function getNewOrders(Request $request)
    {
        $perPage = $request->get('per_page', 10);

        $orders = Order::with([
            'orderItems.product',
            'deliveryAddress:id,user_id,latitude,longitude'
        ])
            ->where('status', 'pending')
            ->whereNull('delivery_agent_id')
            ->latest()
            ->paginate($perPage);

        return response()->json([
            'status' => true,
            'data' => $orders
        ]);
    }

    public function acceptOrder(Request $request, $orderId)
    {
        $user = $request->user();

        DB::beginTransaction();
        try {
            $order = Order::where('id', $orderId)
                ->where('status', 'pending')
                ->whereNull('delivery_agent_id')
                ->lockForUpdate()
                ->first();

            if (!$order) {
                DB::rollBack();
                return response()->json([
                    'status' => false,
                    'message' => 'Order not available'
                ], 404);
            }

            // check active order
            $hasActiveOrder = Order::where('delivery_agent_id', $user->id)
                ->whereIn('status', ['accepted', 'in_progress', 'on_the_way'])
                ->exists();

            if ($hasActiveOrder) {
                $order->update([
                    'delivery_agent_id' => $user->id,
                    'status' => 'queued'
                ]);

                $message = 'Order added to delivery queue';
            } else {
                $order->update([
                    'delivery_agent_id' => $user->id,
                    'status' => 'accepted'
                ]);

                $message = 'Order accepted successfully';
            }

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => $message,
                'data' => $order
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong'
            ], 500);
        }
    }

    public function rejectOrder(Request $request, $orderId)
    {
        $order = Order::where('id', $orderId)
            ->where('status', 'pending')
            ->whereNull('delivery_agent_id')
            ->first();

        if (!$order) {
            return response()->json([
                'status' => false,
                'message' => 'Order not available'
            ], 404);
        }

        $order->update([
            'status' => 'rejected'
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Order rejected successfully'
        ]);
    }

    public function getAvailableOrders(Request $request)
    {
        $perPage = $request->get('per_page', 10);

        $orders = Order::with([
            'orderItems.product',
            'customerAddress:id,user_id,latitude,longitude'
        ])
            ->where('status', 'pending')
            ->whereNull('delivery_agent_id')
            ->orderBy('created_at', 'asc') // FIFO
            ->paginate($perPage);

        return response()->json([
            'status'  => true,
            'message' => 'Partner gets list of available orders (queue)',
            'data'    => $orders
        ]);
    }


    public function getDeliveryQueue(Request $request)
    {
        $user = $request->user();
        $perPage = $request->get('per_page', 10);

        $orders = Order::with('orderItems.product')
            ->where('delivery_agent_id', $user->id)
            ->where('status', 'queued')
            ->orderBy('created_at', 'asc')
            ->paginate($perPage);

        return response()->json([
            'status' => true,
            'data' => $orders
        ]);
    }

    public function markPending(Request $request, $orderId)
    {
        $user = $request->user();

        $order = Order::where('id', $orderId)
            ->where('delivery_agent_id', $user->id)
            ->whereIn('status', ['in_progress', 'accepted'])
            ->first();

        if (!$order) {
            return response()->json([
                'status' => false,
                'message' => 'Order not found or cannot be marked pending'
            ], 404);
        }

        $order->update([
            'status' => 'pending'
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Order marked as pending',
            'data' => $order
        ]);
    }


    public function resumeOrder(Request $request, $orderId)
    {
        $user = $request->user();

        $order = Order::where('id', $orderId)
            ->where('delivery_agent_id', $user->id)
            ->where('status', 'pending')
            ->first();

        if (!$order) {
            return response()->json([
                'status' => false,
                'message' => 'Pending order not found'
            ], 404);
        }

        $hasActiveOrder = Order::where('delivery_agent_id', $user->id)
            ->where('status', 'in_progress')
            ->exists();

        if ($hasActiveOrder) {
            $order->update([
                'status' => 'queued'
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Order moved to queue (another order in progress)',
                'data' => $order
            ]);
        }

        $order->update([
            'status' => 'in_progress'
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Order resumed successfully',
            'data' => $order
        ]);
    }

    public function startOrder(Request $request, $orderId)
    {
        $user = $request->user();

        $order = Order::where('id', $orderId)
            ->where('delivery_agent_id', $user->id)
            ->whereIn('status', ['accepted', 'queued'])
            ->first();

        if (!$order) {
            return response()->json([
                'status' => false,
                'message' => 'Order not found or invalid status'
            ], 404);
        }

        // optional: auto-finish previous in_progress order
        Order::where('delivery_agent_id', $user->id)
            ->where('status', 'in_progress')
            ->update(['status' => 'completed']);

        $order->update([
            'status' => 'in_progress'
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Order started successfully',
            'data' => $order
        ]);
    }


    public function getPendingOrders(Request $request)
    {
        $user = $request->user();
        $perPage = $request->get('per_page', 10);

        $orders = Order::with('orderItems.product')
            ->where('delivery_agent_id', $user->id)
            ->where('status', 'pending')
            ->orderBy('updated_at', 'asc')
            ->paginate($perPage);

        return response()->json([
            'status' => true,
            'message' => 'Pending orders fetched successfully',
            'data' => $orders
        ]);
    }

    public function getOrderDetails(Request $request, $orderId)
    {
        $user = $request->user();
        $order = Order::with('user')
            ->where('id', $orderId)
            ->first();

        if (!$order) {
            return response()->json([
                'status' => false,
                'message' => 'Order not found'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data' => $order
        ]);
    }
    public function getOrderItems(Request $request, $orderId)
    {
        $user = $request->user();

        $items = OrderItem::with('product')
            ->where('order_id', $orderId)
            ->get();

        if ($items->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => 'No items found for this order'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data' => $items
        ]);
    }
    public function confirmInstructionsRead(Request $request, $orderId)
    {
        $user = $request->user();
        $order = Order::find($orderId);
        if (!$order) {
            return response()->json([
                'status' => false,
                'message' => 'Order not found'
            ], 404);
        }
        return response()->json([
            'status' => true,
            'message' => 'Instructions marked as read'
        ]);
    }

    public function getPickupDetails(Request $request, $orderId)
    {
        $user = $request->user();
        $order = Order::with('orderItems.product')
            ->where('id', $orderId)
            ->first();

        if (!$order) {
            return response()->json([
                'status' => false,
                'message' => 'Order not found'
            ], 404);
        }
        return response()->json([
            'status' => true,
            'data' => $order
        ]);
    }

    public function verifyItem(Request $request, $orderId, $itemId)
    {
        $user = $request->user();
        $item = OrderItem::where('id', $itemId)
            ->where('order_id', $orderId)
            ->first();

        if (!$item) {
            return response()->json([
                'status' => false,
                'message' => 'Item not found'
            ], 404);
        }
        return response()->json([
            'status' => true,
            'message' => 'Item verified successfully'
        ]);
    }

    public function reportItemIssue(Request $request, $orderId, $itemId)
    {
        $user = $request->user();
        $request->validate([
            'issueType' => 'required|string',
            'comment'   => 'nullable|string'
        ]);

        $item = OrderItem::where('id', $itemId)
            ->where('order_id', $orderId)
            ->first();

        if (!$item) {
            return response()->json([
                'status' => false,
                'message' => 'Item not found'
            ], 404);
        }
        return response()->json([
            'status' => true,
            'message' => 'Item issue reported'
        ]);
    }

    public function uploadPickupProof(Request $request, $orderId)
    {
        $user = $request->user();

        $request->validate([
            'pickup_proof' => 'required|image|max:2048'
        ]);
        $order = Order::find($orderId);
        if (!$order) {
            return response()->json([
                'status' => false,
                'message' => 'Order not found'
            ], 404);
        }
        $file = $request->file('pickup_proof');
        $originalName = $file->getClientOriginalName();
        $file->storeAs('pickup_proofs', $originalName, 'public');
        $order->pickup_proof = $originalName;
        $order->save();
        return response()->json([
            'status' => true,
            'message' => 'Pickup proof uploaded',
            'pickup_proof' => $originalName
        ]);
    }

    public function confirmPickup(Request $request, $orderId)
    {
        $user = $request->user();
        $order = Order::find($orderId);
        if (!$order) {
            return response()->json([
                'status' => false,
                'message' => 'Order not found'
            ], 404);
        }
        $order->status = 'on_the_way';
        $order->save();
        return response()->json([
            'status' => true,
            'message' => 'Pickup confirmed, order is on the way'
        ]);
    }
    public function getCancellationReasons()
    {
        return response()->json([
            'status' => true,
            'data' => [
                [
                    'id' => 1,
                    'reason' => 'Customer not available'
                ],
                [
                    'id' => 2,
                    'reason' => 'Wrong address'
                ],
                [
                    'id' => 3,
                    'reason' => 'Item damaged'
                ],
                [
                    'id' => 4,
                    'reason' => 'Customer cancelled'
                ],
                [
                    'id' => 5,
                    'reason' => 'Other'
                ]
            ]
        ]);
    }

    public function cancelOrder(Request $request, $orderId)
    {
        $user = $request->user();

        // 🔐 Delivery agent check
        if (!$user->role || strtolower($user->role->name) !== 'delivery agent') {
            return response()->json([
                'status' => false,
                'message' => 'Access denied'
            ], 403);
        }

        // ✅ Base validation
        $request->validate([
            'reasonId' => 'required|integer|in:1,2,3,4,5',
            'comment'  => 'nullable|string'
        ]);

        // 🔴 If reason = Other, comment is REQUIRED
        if ((int)$request->reasonId === 5 && empty($request->comment)) {
            return response()->json([
                'status' => false,
                'message' => 'Comment is required when reason is Other'
            ], 422);
        }

        // 🔍 Order must belong to this agent
        $order = Order::where('id', $orderId)
            ->where('delivery_agent_id', $user->id)
            ->first();

        if (!$order) {
            return response()->json([
                'status' => false,
                'message' => 'Order not found'
            ], 404);
        }

        // ❌ Cannot cancel after delivery
        if ($order->status === 'delivered') {
            return response()->json([
                'status' => false,
                'message' => 'Delivered order cannot be cancelled'
            ], 400);
        }

        // 🔁 Reason mapping
        $reasons = [
            1 => 'Customer not available',
            2 => 'Wrong address',
            3 => 'Item damaged',
            4 => 'Customer cancelled',
            5 => 'Other'
        ];

        // ✅ Cancel order
        $order->update([
            'status'         => 'cancelled',
            'cancel_reason'  => $reasons[$request->reasonId],
            'cancel_comment' => $request->comment,
            'cancelled_at'   => now()
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Order cancelled successfully'
        ]);
    }

    private function agentHasActiveOrder($userId)
    {
        return Order::where('delivery_agent_id', $userId)
            ->whereIn('status', ['accepted', 'on_the_way'])
            ->exists();
    }

    public function myDeliveries(Request $request)
    {
        $deliveryBoy = $request->user();

        $limit = $request->get('limit', 10);

        $query = Order::where('delivery_agent_id', $deliveryBoy->id);

        // 🔍 Search (order id / customer name / phone)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', "%$search%")
                    ->orWhere('customer_name', 'like', "%$search%")
                    ->orWhere('customer_phone', 'like', "%$search%");
            });
        }

        // 📌 Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // 🚚 Delivery type filter
        if ($request->filled('deliveryType')) {
            $query->where('delivery_type', $request->deliveryType);
        }

        // 📅 Date range filter
        if ($request->filled('fromDate')) {
            $query->whereDate('created_at', '>=', $request->fromDate);
        }

        if ($request->filled('toDate')) {
            $query->whereDate('created_at', '<=', $request->toDate);
        }

        // 📦 Paginated deliveries
        $deliveries = $query->latest()->paginate($limit);

        // 📊 Summary counts
        $summary = [
            'total' => Order::where('delivery_agent_id', $deliveryBoy->id)->count(),
            'completed' => Order::where('delivery_agent_id', $deliveryBoy->id)
                ->where('status', 'completed')->count(),
            'cancelled' => Order::where('delivery_agent_id', $deliveryBoy->id)
                ->where('status', 'cancelled')->count(),
        ];

        // 🚴 In-progress deliveries
        $inProgress = Order::where('delivery_agent_id', $deliveryBoy->id)
            ->whereIn('status', ['assigned', 'picked_up'])
            ->get();

        // ⏭️ Up-next delivery
        $upNext = Order::where('delivery_agent_id', $deliveryBoy->id)
            ->where('status', 'assigned')
            ->orderBy('created_at')
            ->first();

        return response()->json([
            'status' => true,
            'summary' => $summary,
            'inProgress' => $inProgress,
            'upNext' => $upNext,
            'deliveries' => $deliveries
        ]);
    }

    public function search(Request $request)
    {
        $request->validate([
            'query' => 'required|string'
        ]);

        $deliveryBoy = $request->user();
        $search = $request->get('query');

        $deliveries = Order::where('delivery_agent_id', $deliveryBoy->id)
            ->where(function ($q) use ($search) {
                $q->where('order_number', 'LIKE', '%' . $search . '%')
                    ->orWhere('status', 'LIKE', '%' . $search . '%');
            })
            ->latest()
            ->get();

        return response()->json([
            'status' => true,
            'data' => $deliveries
        ]);
    }

    public function status(Request $request)
    {
        $deliveryBoy = $request->user();

        $query = Order::where('delivery_agent_id', $deliveryBoy->id);

        if ($request->filled('dateFrom')) {
            $query->whereDate('created_at', '>=', $request->dateFrom);
        }

        if ($request->filled('dateTo')) {
            $query->whereDate('created_at', '<=', $request->dateTo);
        }

        $total = (clone $query)->count();
        $delivered = (clone $query)->where('status', 'delivered')->count();
        $cancelled = (clone $query)->where('status', 'cancelled')->count();
        $pending = (clone $query)->whereIn('status', ['pending', 'accepted'])->count();

        return response()->json([
            'status' => true,
            'data' => [
                'total_orders' => $total,
                'delivered_orders' => $delivered,
                'cancelled_orders' => $cancelled,
                'pending_orders' => $pending
            ]
        ]);
    }
    public function getOrderSummary(Request $request, $orderId)
    {
        $deliveryBoy = $request->user();

        $order = Order::with('orderItems.product')
            ->where('id', $orderId)
            ->where('delivery_agent_id', $deliveryBoy->id)
            ->first();

        if (!$order) {
            return response()->json([
                'status' => false,
                'message' => 'Order not found'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data' => [
                'order_number' => $order->order_number,
                'subtotal' => $order->subtotal,
                'delivery_charge' => $order->delivery_charge,
                'discount' => $order->discount,
                'total_amount' => $order->total_amount,
                'items_count' => $order->orderItems->count(),
                'status' => $order->status
            ]
        ]);
    }
    public function completeOrder(Request $request, $orderId)
    {
        $deliveryBoy = $request->user();

        $order = Order::where('id', $orderId)
            ->where('delivery_agent_id', $deliveryBoy->id)
            ->first();

        if (!$order) {
            return response()->json([
                'status' => false,
                'message' => 'Order not found'
            ], 404);
        }

        if ($order->status !== 'on_the_way') {
            return response()->json([
                'status' => false,
                'message' => 'Order cannot be completed'
            ], 400);
        }

        $order->status = 'delivered';
        $order->delivered_at = now();
        $order->save();

        return response()->json([
            'status' => true,
            'message' => 'Order completed successfully'
        ]);
    }
    public function rateCustomer(Request $request, $orderId)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'tags' => 'nullable|array'
        ]);

        $deliveryBoy = $request->user();

        $order = Order::where('id', $orderId)
            ->where('delivery_agent_id', $deliveryBoy->id)
            ->first();

        if (!$order) {
            return response()->json([
                'status' => false,
                'message' => 'Order not found'
            ], 404);
        }

        $order->customer_rating = $request->rating;
        $order->customer_rating_tags = $request->tags ? json_encode($request->tags) : null;
        $order->save();

        return response()->json([
            'status' => true,
            'message' => 'Customer rated successfully'
        ]);
    }
    public function totalOrders(Request $request)
    {
        $user = $request->user();
        $totalOrders = Order::where('delivery_agent_id', $user->id)->count();

        return response()->json([
            'status' => true,
            'totalOrders' => $totalOrders
        ]);
    }
    public function getPickupItems(Request $request, $orderId)
    {
        $user = $request->user();

        if (!$user->role || strtolower($user->role->name) !== 'delivery agent') {
            return response()->json([
                'status' => false,
                'message' => 'Access denied'
            ], 403);
        }

        $order = Order::with(['orderItems.product'])
            ->where('id', $orderId)
            ->where('delivery_agent_id', $user->id)
            ->first();

        if (!$order) {
            return response()->json([
                'status' => false,
                'message' => 'Order not found'
            ], 404);
        }

        $items = $order->orderItems->map(function ($item) {

            $image = null;

            if (!empty($item->product->product_images[0])) {
                $image = asset('storage/products/' . $item->product->product_images[0]);
            }

            return [
                'id'       => $item->id,
                'name'     => $item->product->name ?? null,
                'quantity' => $item->qty,
                'image'    => $image, // ✅ FULL IMAGE URL
                'isPicked' => (bool) $item->is_picked,
            ];
        });

        return response()->json([
            'status' => true,
            'data' => [
                'orderId' => $order->id,
                'items'   => $items
            ]
        ]);
    }

    public function deliverySummary(Request $request)
    {
        $user = $request->user();

        // 🔐 Only delivery agent
        if (!$user->role || strtolower($user->role->name) !== 'delivery agent') {
            return response()->json([
                'status' => false,
                'message' => 'Access denied'
            ], 403);
        }

        // ✅ Base query: completed deliveries
        $query = Order::where('delivery_agent_id', $user->id)
            ->where('status', 'delivered')
            ->whereNotNull('delivered_at');

        // 📅 Date range filter
        if ($request->filled('dateRange')) {

            switch ($request->dateRange) {

                case 'today':
                    $query->whereDate('delivered_at', now()->toDateString());
                    break;

                case 'week':
                    $query->whereBetween('delivered_at', [
                        now()->startOfWeek(),
                        now()->endOfWeek()
                    ]);
                    break;

                case 'month':
                    $query->whereMonth('delivered_at', now()->month)
                        ->whereYear('delivered_at', now()->year);
                    break;

                case 'custom':
                    $request->validate([
                        'fromDate' => 'required|date',
                        'toDate'   => 'required|date|after_or_equal:fromDate'
                    ]);

                    $query->whereBetween('delivered_at', [
                        $request->fromDate,
                        $request->toDate
                    ]);
                    break;
            }
        }

        // 📦 Fetch deliveries
        $deliveries = $query->get();

        $completedCount = $deliveries->count();

        // ⏱️ Avg delivery time (minutes)
        $avgDeliveryTimeMin = $deliveries->avg(function ($order) {
            if ($order->picked_at && $order->delivered_at) {
                return $order->picked_at->diffInMinutes($order->delivered_at);
            }
            return null;
        }) ?? 0;

        // 🛣️ Total distance (if column exists)
        $totalDistanceKm = $deliveries->sum('delivery_distance_km') ?? 0;

        return response()->json([
            'status' => true,
            'completedCount' => $completedCount,
            'totalDistanceKm' => round($totalDistanceKm, 2),
            'avgDeliveryTimeMin' => round($avgDeliveryTimeMin)
        ]);
    }
}
