<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

use App\Models\Retailer;
use App\Models\RetailerCart;
use App\Models\RetailerCartItem;
use App\Models\RetailerOrder;
use App\Models\RetailerOrderItem;
use App\Models\Warehouse;
use App\Models\WarehouseTransfer;

class RetailerOrderController extends Controller
{
    

    /**
        * Place Retailer Order
    */
    public function placeOrder(Request $request)
    {
        DB::beginTransaction();

        try {

            /*
            |--------------------------------------------------------------------------
            | 1. Logged-in User
            |--------------------------------------------------------------------------
            */

            $user = $request->user();

            Log::info('Retailer Place Order - Request', [
                'user_id' => $user->id,
            ]);


            /*
            |--------------------------------------------------------------------------
            | 2. Get Retailer
            |--------------------------------------------------------------------------
            */

            $retailer = Retailer::where('user_id', $user->id)
                ->where('is_active', 1)
                ->first();


            if (!$retailer) {

                DB::rollBack();

                return response()->json([
                    'success' => false,
                    'message' => 'Retailer profile not found.',
                ], 404);
            }


            Log::info('Retailer Place Order - Retailer Found', [
                'retailer_id' => $retailer->id,
                'retailer_name' => $retailer->name,
            ]);


            /*
            |--------------------------------------------------------------------------
            | 3. Get Active Cart
            |--------------------------------------------------------------------------
            */

            $cart = RetailerCart::where(
                    'retailer_id',
                    $retailer->id
                )
                ->where(
                    'status',
                    'active'
                )
                ->latest('id')
                ->first();


            if (!$cart) {

                DB::rollBack();

                return response()->json([
                    'success' => false,
                    'message' => 'Active cart not found.',
                ], 404);
            }


            /*
            |--------------------------------------------------------------------------
            | 4. Get Cart Items
            |--------------------------------------------------------------------------
            */

            $cartItems = RetailerCartItem::where(
                'cart_id',
                $cart->id
            )->get();


            if ($cartItems->isEmpty()) {

                DB::rollBack();

                return response()->json([
                    'success' => false,
                    'message' => 'Your cart is empty.',
                ], 422);
            }


            Log::info('Retailer Place Order - Cart Found', [
                'cart_id' => $cart->id,
                'retailer_id' => $retailer->id,
                'warehouse_id' => $cart->warehouse_id,
                'items_count' => $cartItems->count(),
            ]);


            /*
            |--------------------------------------------------------------------------
            | 5. Verify Distribution Center
            |--------------------------------------------------------------------------
            */

            $warehouse = Warehouse::where(
                    'id',
                    $cart->warehouse_id
                )
                ->where(
                    'type',
                    'distribution_center'
                )
                ->where(
                    'status',
                    'active'
                )
                ->first();


            if (!$warehouse) {

                DB::rollBack();

                return response()->json([
                    'success' => false,
                    'message' => 'Distribution Center is not available.',
                ], 422);
            }


            Log::info('Retailer Place Order - DC Found', [
                'warehouse_id' => $warehouse->id,
                'warehouse_name' => $warehouse->name,
            ]);


            /*
            |--------------------------------------------------------------------------
            | 6. Verify Stock For Every Product
            |--------------------------------------------------------------------------
            */

            $orderTotal = 0;

            $validatedItems = [];


            foreach ($cartItems as $item) {

                /*
                |----------------------------------------------------------------------
                | Get DC Stock
                |----------------------------------------------------------------------
                */

                $stock = WarehouseTransfer::where(
                        'requested_by_warehouse_id',
                        $warehouse->id
                    )
                    ->where(
                        'product_id',
                        $item->product_id
                    )
                    ->where(
                        'status',
                        2
                    )
                    ->sum('quantity');


                /*
                |----------------------------------------------------------------------
                | Already Ordered / Reserved Quantity
                |----------------------------------------------------------------------
                |
                | Pending / approved / dispatched orders ka quantity
                | stock se minus karenge.
                |
                */

                $alreadyOrdered = RetailerOrderItem::where(
                        'product_id',
                        $item->product_id
                    )
                    ->whereHas('order', function ($query) use ($warehouse) {

                        $query->where(
                            'warehouse_id',
                            $warehouse->id
                        )
                        ->whereIn(
                            'status',
                            [
                                'pending',
                                'approved',
                                'dispatched'
                            ]
                        );

                    })
                    ->sum('quantity');


                /*
                |----------------------------------------------------------------------
                | Available Quantity
                |----------------------------------------------------------------------
                */

                $availableQuantity =
                    (int) $stock - (int) $alreadyOrdered;


                Log::info(
                    'Retailer Place Order - Stock Check',
                    [
                        'warehouse_id' => $warehouse->id,
                        'product_id' => $item->product_id,
                        'stock' => $stock,
                        'already_ordered' => $alreadyOrdered,
                        'available_quantity' => $availableQuantity,
                        'requested_quantity' => $item->quantity,
                    ]
                );


                /*
                |----------------------------------------------------------------------
                | Stock Not Available
                |----------------------------------------------------------------------
                */

                if ($availableQuantity < $item->quantity) {

                    DB::rollBack();

                    return response()->json([
                        'success' => false,
                        'message' =>
                            'Insufficient stock for one or more products.',

                        'product_id' =>
                            $item->product_id,

                        'requested_quantity' =>
                            (int) $item->quantity,

                        'available_quantity' =>
                            max(0, $availableQuantity),

                    ], 422);
                }


                /*
                |----------------------------------------------------------------------
                | Calculate Item Total
                |----------------------------------------------------------------------
                */

                $itemTotal =
                    (float) $item->total;


                $orderTotal += $itemTotal;


                $validatedItems[] = [

                    'cart_item_id' =>
                        $item->id,

                    'category_id' =>
                        $item->category_id,

                    'product_id' =>
                        $item->product_id,

                    'quantity' =>
                        $item->quantity,

                    'price' =>
                        $item->price,

                    'discount_amount' =>
                        $item->discount_amount ?? 0,

                    'total' =>
                        $itemTotal,

                ];
            }


            /*
            |--------------------------------------------------------------------------
            | 7. Generate Order Number
            |--------------------------------------------------------------------------
            */

            $orderNo =
                'ORD-' .
                now()->format('YmdHis') .
                '-' .
                strtoupper(
                    substr(
                        uniqid(),
                        -5
                    )
                );


            /*
            |--------------------------------------------------------------------------
            | 8. Create Order
            |--------------------------------------------------------------------------
            */

            $order = RetailerOrder::create([

                'order_no' =>
                    $orderNo,

                'retailer_id' =>
                    $retailer->id,

                'warehouse_id' =>
                    $warehouse->id,

                'status' =>
                    'pending',

                'total_amount' =>
                    $orderTotal,

            ]);


            Log::info(
                'Retailer Place Order - Order Created',
                [
                    'order_id' =>
                        $order->id,

                    'order_no' =>
                        $order->order_no,

                    'retailer_id' =>
                        $retailer->id,

                    'warehouse_id' =>
                        $warehouse->id,

                    'total_amount' =>
                        $orderTotal,
                ]
            );


            /*
            |--------------------------------------------------------------------------
            | 9. Create Order Items
            |--------------------------------------------------------------------------
            */

            foreach ($validatedItems as $item) {

                RetailerOrderItem::create([

                    'retailer_order_id' =>
                        $order->id,

                    'category_id' =>
                        $item['category_id'],

                    'product_id' =>
                        $item['product_id'],

                    'quantity' =>
                        $item['quantity'],

                    'price' =>
                        $item['price'],

                    'discount_amount' =>
                        $item['discount_amount'],

                    'total' =>
                        $item['total'],

                ]);
            }


            /*
            |--------------------------------------------------------------------------
            | 10. Clear Cart Items
            |--------------------------------------------------------------------------
            */

            RetailerCartItem::where(
                'cart_id',
                $cart->id
            )->delete();


            /*
            |--------------------------------------------------------------------------
            | 11. Delete Cart
            |--------------------------------------------------------------------------
            */

            $cart->delete();


            Log::info(
                'Retailer Place Order - Cart Removed',
                [
                    'cart_id' =>
                        $cart->id,

                    'order_id' =>
                        $order->id,
                ]
            );


            /*
            |--------------------------------------------------------------------------
            | 12. Commit Transaction
            |--------------------------------------------------------------------------
            */

            DB::commit();


            /*
            |--------------------------------------------------------------------------
            | 13. Success Response
            |--------------------------------------------------------------------------
            */

            return response()->json([

                'success' => true,

                'message' =>
                    'Order placed successfully.',

                'order' => [

                    'id' =>
                        $order->id,

                    'order_no' =>
                        $order->order_no,

                    'retailer_id' =>
                        $order->retailer_id,

                    'warehouse_id' =>
                        $order->warehouse_id,

                    'warehouse_name' =>
                        $warehouse->name,

                    'status' =>
                        $order->status,

                    'total_amount' =>
                        number_format(
                            $order->total_amount,
                            2,
                            '.',
                            ''
                        ),

                    'created_at' =>
                        $order->created_at,

                ],

                'items' =>
                    $validatedItems,

            ], 201);


        } catch (\Throwable $e) {

            /*
            |--------------------------------------------------------------------------
            | Rollback
            |--------------------------------------------------------------------------
            */

            DB::rollBack();


            /*
            |--------------------------------------------------------------------------
            | Error Log
            |--------------------------------------------------------------------------
            */

            Log::error(
                'Retailer Place Order - Error',
                [

                    'user_id' =>
                        $request->user()?->id,

                    'error' =>
                        $e->getMessage(),

                    'line' =>
                        $e->getLine(),

                    'file' =>
                        $e->getFile(),

                ]
            );


            return response()->json([

                'success' => false,

                'message' =>
                    'Unable to place order.',

                'error' =>
                    config('app.debug')
                        ? $e->getMessage()
                        : null,

            ], 500);
        }
    }


}
