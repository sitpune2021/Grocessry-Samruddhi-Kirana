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
use App\Models\Warehouse;
use App\Models\WarehouseStock;
use App\Models\RetailerOrderItem;
use Illuminate\Support\Facades\Log;


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
        // Validation log
        Log::info('Retailer Order Store called', ['request' => $request->all()]);

        $request->validate([
            'retailer_id' => 'required',
            'warehouse_id' => 'required',
            'items'       => 'required|array',
        ]);

        DB::transaction(function () use ($request) {

            try {
                // Order number generate
                $orderNo = 'RO-' . date('Ymd') . '-' . rand(1000, 9999);

                $order = RetailerOrder::create([
                    'order_no'     => $orderNo,
                    'retailer_id'  => $request->retailer_id,
                    'warehouse_id' => $request->warehouse_id,
                    'status'       => 'pending',
                    'total_amount' => 0,
                ]);

                Log::info('RetailerOrder created', ['order_id' => $order->id]);

                $grandTotal = 0;

                foreach ($request->items as $item) {
                    $product = Product::find($item['product_id']);

                    if (!$product) {
                        Log::warning('Product not found', ['product_id' => $item['product_id']]);
                        continue; // skip invalid product
                    }

                    $categoryId = $item['category_id'] ?? $product->category_id;
                    $price = $item['price'] ?? 0;
                    $quantity = $item['quantity'] ?? 0;
                    $lineTotal = $price * $quantity;

                    try {
                        $orderItem = RetailerOrderItem::create([
                            'retailer_order_id' => $order->id,
                            'category_id'       => $categoryId,
                            'product_id'        => $product->id,
                            'price'             => $price,
                            'quantity'          => $quantity,
                            'total'             => $lineTotal,
                        ]);

                        Log::info('RetailerOrderItem created', [
                            'order_item_id' => $orderItem->id,
                            'product_id' => $product->id,
                            'quantity' => $quantity,
                            'total' => $lineTotal
                        ]);

                    } catch (\Exception $e) {
                        Log::error('RetailerOrderItem insert failed', [
                            'product_id' => $product->id,
                            'error' => $e->getMessage()
                        ]);
                    }

                    $grandTotal += $lineTotal;
                }

                $order->update(['total_amount' => $grandTotal]);
                Log::info('RetailerOrder total updated', ['order_id' => $order->id, 'total' => $grandTotal]);

            } catch (\Exception $e) {
                Log::error('RetailerOrder transaction failed', ['error' => $e->getMessage()]);
                throw $e; // rollback transaction
            }
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

    public function getWarehousesByCategory($retailerId, $categoryId)
    {
        // 1. Retailer ka district & taluka lo
        $retailer = Retailer::select('district_id', 'taluka_id')
            ->findOrFail($retailerId);

        // 2. Warehouses filter karo
        $warehouses = Warehouse::where('district_id', $retailer->district_id)
            ->where('taluka_id', $retailer->taluka_id)
            ->whereIn('id', function ($q) use ($categoryId) {
                $q->select('warehouse_id')
                ->from('warehouse_stock')
                ->where('category_id', $categoryId)
                ->where('quantity', '>', 0);
            })
            ->get(['id', 'name']);

        return response()->json($warehouses);
    }


}
