<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Retailer;
use App\Models\RetailerPricing;
use App\Models\Category;
use App\Models\Product;
use App\Models\RetailerOrder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
class RetailerOrderController extends Controller
{
    public function create()
    {
        return view('retailer-orders.create', [
            'retailers'  => Retailer::where('is_active', 1)->get(),
            'categories' => Category::all(),
        ]);
    }

    public function getRetailerPrice($retailerId, $productId)
    {
        $today = now()->toDateString();

        $pricing = RetailerPricing::where([
                'retailer_id' => $retailerId,
                'product_id'  => $productId,
                'is_active'   => 1,
            ])
            ->whereDate('effective_from', '<=', $today)
            ->where(function ($q) use ($today) {
                $q->whereNull('effective_to')
                ->orWhereDate('effective_to', '>=', $today);
            })
            ->orderByDesc('effective_from')
            ->first();

        return response()->json([
            'price' => $pricing?->effective_price ?? 0
        ]);
    }

    

public function store(Request $request)
{
    $request->validate([
        'retailer_id' => 'required',
        'items'       => 'required|array',
    ]);

    DB::transaction(function () use ($request) {

        // ✅ Order number generate
        $orderNo = 'RO-' . date('Ymd') . '-' . rand(1000, 9999);

        $order = RetailerOrder::create([
            'order_no'     => $orderNo,          // 🔥 FIX
            'retailer_id'  => $request->retailer_id,
            'status'       => 'pending',
            'total_amount' => 0,
            'warehouse_id' => 0,
        ]);

        $grandTotal = 0;

        foreach ($request->items as $item) {

            $lineTotal = $item['price'] * $item['quantity'];

            $order->items()->create([
                'category_id' => $item['category_id'],
                'product_id'  => $item['product_id'],
                'price'       => $item['price'],     // 🔒 locked price
                'quantity'    => $item['quantity'],
                'total'       => $lineTotal,
            ]);

            $grandTotal += $lineTotal;
        }

        $order->update([
            'total_amount' => $grandTotal
        ]);
    });

    return redirect()
        ->route('retailer-orders.create')
        ->with('success', 'Order created successfully');
}



    public function getCategoriesByRetailer($retailerId)
    {
        $categories = Category::whereIn('id', function ($q) use ($retailerId) {
            $q->select('category_id')
            ->from('retailer_pricings')
            ->where('retailer_id', $retailerId)
            ->where('is_active', 1);
        })->get(['id', 'name']);

        return response()->json($categories);
    }

    public function getProductsByRetailerCategory($retailerId, $categoryId)
    {
        $products = Product::whereIn('id', function ($q) use ($retailerId, $categoryId) {
            $q->select('product_id')
            ->from('retailer_pricings')
            ->where('retailer_id', $retailerId)
            ->where('category_id', $categoryId)
            ->where('is_active', 1);
        })->get(['id', 'name']);

        return response()->json($products);
    }


}
