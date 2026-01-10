<?php

namespace App\Http\Controllers;

use App\Models\CustomerOrder;
use App\Models\DeliveryAgent;
use Illuminate\Http\Request;
use App\Models\Order;

class CustomerOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $orders = CustomerOrder::with('customerOrderItems')->paginate(10);
        $deliveryAgents = DeliveryAgent::with('user')
            ->get();

        return view('menus.customer-management.customer-order.index', compact('orders','deliveryAgents'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function userorder()
    {
        $orders = Order::latest()->get();
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

    
}
