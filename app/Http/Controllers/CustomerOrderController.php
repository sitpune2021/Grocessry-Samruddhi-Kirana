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

        $orders = Order::with([
            'items',
            'user',
            'orderItems',
            'address',
            'deliveryAgent.user'
        ])
            ->orderByDesc('id')
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


public function userorder(Request $request)
{
    $user = auth()->user();

    $warehouseId = $request->query('warehouse_id');
    $fromDate = $request->query('from_date');
    $toDate = $request->query('to_date');

    $query = Order::where('channel', 'web')
        ->with(['items.product', 'deliveryAgent.user', 'warehouse'])
        ->latest();

    // 🔐 SAME AS POS LOGIC
    if ($user->role_id == 1 || $user->role_id == 2) {
        // Admin → can filter
        if ($warehouseId) {
            $query->where('warehouse_id', $warehouseId);
        }
    } else {
        // DC user → force own warehouse
        $query->where('warehouse_id', $user->warehouse_id);
    }

    // 📅 Date filter
    if ($fromDate && $toDate && $fromDate <= $toDate) {
        $query->whereBetween('created_at', [
            $fromDate . ' 00:00:00',
            $toDate . ' 23:59:59'
        ]);
    }

    $orders = $query->get();

    return view('website.user_order', compact('orders'));
}    public function exportCsv()
    {
        $orders = Order::where('channel', 'web')
            ->with(['items.product', 'deliveryAgent.user', 'user'])
            ->latest()
            ->get();

        $filename = "website_orders_" . date('Ymd_His') . ".csv";

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = [
            'Order ID',
            'User Name',
            'Order Number',
            'Product Names',
            'Total Amount',
            'Delivery Agent',
            'Status'
        ];

        $callback = function () use ($orders, $columns) {

            $file = fopen('php://output', 'w');

            // Header row
            fputcsv($file, $columns);

            foreach ($orders as $order) {

                // Products combine
                $products = [];
                foreach ($order->items as $item) {
                    $products[] = ($item->product->name ?? 'N/A') . " (Qty: " . $item->quantity . ")";
                }

                $productList = implode(' | ', $products);

                $row = [
                    $order->id,
                    $order->user->first_name ?? 'N/A',
                    $order->order_number,
                    $productList,
                    $order->total_amount,
                    ($order->deliveryAgent->user->first_name ?? 'N/A') . ' ' .
                        ($order->deliveryAgent->user->last_name ?? ''),
                    $order->status
                ];

                fputcsv($file, $row);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
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
