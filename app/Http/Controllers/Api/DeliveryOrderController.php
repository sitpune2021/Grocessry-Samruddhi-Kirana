<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;

class DeliveryOrderController extends Controller
{
    // DeliveryOrderController.php
    public function getNewOrders(Request $request)
    {
        $agent = $request->user(); // Authenticated delivery agent

        $perPage = $request->query('per_page', 10);

        $orders = Order::where('delivery_agent_id', $agent->id)
            ->where('status', 'new')
            ->latest()
            ->paginate($perPage);

        return response()->json([
            'status' => true,
            'message' => 'New orders fetched successfully',
            'data' => $orders
        ]);
    }
}
