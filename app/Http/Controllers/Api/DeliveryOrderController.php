<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;

class DeliveryOrderController extends Controller
{
    public function getNewOrders(Request $request)
    {
        $user = $request->user();
        $perPage = $request->get('per_page', 10);

        if ($this->agentHasActiveOrder($user->id)) {
            return response()->json([
                'status' => true,
                'data' => []
            ]);
        }

        $orders = Order::with('orderItems.product')
            ->where('status', 'pending')
            ->paginate($perPage);

        return response()->json([
            'status' => true,
            'data' => $orders
        ]);
    }

    public function acceptOrder(Request $request, $orderId)
    {
        $user = $request->user();
        $order = Order::find($orderId);
        if (!$order || $order->status !== 'pending') {
            return response()->json([
                'status' => false,
                'message' => 'Order not available for accept'
            ], 404);
        }
        $order->status = 'accepted';
        $order->delivery_agent_id = $user->id;
        $order->save();

        return response()->json([
            'status' => true,
            'message' => 'Order accepted',
            'data' => $order
        ]);
    }

    public function rejectOrder(Request $request, $orderId)
    {
        $user = $request->user();
        $order = Order::find($orderId);
        if (!$order || $order->status !== 'pending') {
            return response()->json([
                'status' => false,
                'message' => 'Order not available for reject'
            ], 404);
        }

        $order->status = 'rejected';
        $order->save();
        return response()->json([
            'status' => true,
            'message' => 'Order rejected'
        ]);
    }

    public function getAvailableOrders(Request $request)
    {
        $user = $request->user();
        $perPage = $request->get('per_page', 10);

        if ($this->agentHasActiveOrder($user->id)) {
            return response()->json([
                'status' => true,
                'data' => []
            ]);
        }

        $orders = Order::with('orderItems.product')
            ->where('status', 'pending')
            ->paginate($perPage);

        return response()->json([
            'status' => true,
            'data' => $orders
        ]);
    }

    public function getDeliveryQueue(Request $request)
    {
        $user = $request->user();
        $perPage = $request->get('per_page', 10);

        $orders = Order::with('orderItems.product')
            ->where('delivery_agent_id', $user->id)
            ->whereIn('status', ['accepted', 'on_the_way'])
            ->paginate($perPage);

        return response()->json([
            'status' => true,
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
        $reasons = Order::whereNotNull('cancel_reason')
            ->distinct()
            ->pluck('cancel_reason');
        return response()->json([
            'status' => true,
            'data' => $reasons
        ]);
    }
    public function cancelOrder(Request $request, $orderId)
    {
        $request->validate([
            'cancel_reason' => 'required|string',
            'cancel_comment' => 'nullable|string'
        ]);
        $order = Order::find($orderId);
        if (!$order) {
            return response()->json([
                'status' => false,
                'message' => 'Order not found'
            ], 404);
        }
        $order->status = 'cancelled';
        $order->cancel_reason = $request->cancel_reason;
        $order->cancel_comment = $request->cancel_comment;
        $order->cancelled_at = now();
        $order->save();
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

        $query = Order::where('delivery_agent_id', $deliveryBoy->id);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('type')) {
            $query->where('delivery_type', $request->type);
        }

        if ($request->filled('dateFrom')) {
            $query->whereDate('created_at', '>=', $request->dateFrom);
        }

        if ($request->filled('dateTo')) {
            $query->whereDate('created_at', '<=', $request->dateTo);
        }

        $deliveries = $query->latest()->paginate(10);

        return response()->json([
            'status' => true,
            'data' => $deliveries
        ]);
    }
    //Search by order_id, customer name, mobile
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



    // Get Delivery Status
    //Filters: dateFrom, dateTo
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
 
}
