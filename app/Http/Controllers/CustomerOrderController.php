<?php

namespace App\Http\Controllers;

use App\Models\CustomerOrder;
use App\Models\DeliveryAgent;
use Illuminate\Http\Request;
use App\Models\Order;

class CustomerOrderController extends Controller
{


    public function index()
    {
        $orders = Order::with(['items', 'user','orderItems'])
            ->latest()
            ->paginate(10);

        $deliveryAgents = DeliveryAgent::with('user')->get();

        return view(
            'menus.customer-management.customer-order.index',
            compact('orders', 'deliveryAgents')
        );
    }

    public function show($id)
    {
        $order = Order::with(['items', 'user', 'deliveryAgent'])
            ->findOrFail($id);

        return view(
            'menus.customer-management.customer-order.show',
            compact('order')
        );
    }

    public function userorder()
    {
        $orders = Order::with(['items.product', 'deliveryAgent.user'])
                        ->latest()
                        ->get();

        return view('website.user_order', compact('orders'));
    }

    public function orderapprove($id)
    {
        $order = Order::findOrFail($id);

        $order->update([
            'status' => 'approved'
        ]);

        return back()->with('success', 'Order Approved Successfully');
    }

    public function cancel(Request $request, $id)
    {
        $request->validate([
            'cancel_reason' => 'required|string|max:255',
            'cancel_comment' => 'nullable|string'
        ]);

        $order = Order::findOrFail($id);

        $order->update([
            'status' => 'cancelled',
            'cancel_reason' => $request->cancel_reason,
            'cancel_comment' => $request->cancel_comment,
            'cancelled_at' => now(),
        ]);

        return back()->with('success', 'Order Cancelled');
    }

    
}
