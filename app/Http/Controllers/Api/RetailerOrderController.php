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

    /**
     * Get Retailer Orders
    */
    public function myOrders(Request $request)
    {
        try {

            // ============================================================
            // 1. LOGGED-IN USER
            // ============================================================

            $user = $request->user();

            Log::info('Retailer Orders List - Request', [
                'user_id' => $user->id,
            ]);


            // ============================================================
            // 2. GET RETAILER
            // ============================================================

            $retailer = Retailer::where('user_id', $user->id)
                ->where('is_active', 1)
                ->first();


            if (!$retailer) {

                return response()->json([
                    'success' => false,
                    'message' => 'Retailer profile not found.',
                ], 404);
            }


            // ============================================================
            // 3. GET ORDERS
            // ============================================================

            $orders = RetailerOrder::where(
                    'retailer_id',
                    $retailer->id
                )
                ->with([
                    'items.product',
                    'warehouse'
                ])
                ->latest('id')
                ->paginate(
                    $request->get('limit', 10)
                );


            // ============================================================
            // 4. FORMAT RESPONSE
            // ============================================================

            $orders->getCollection()->transform(function ($order) {

                return [
                    'id' => $order->id,

                    'order_no' => $order->order_no,

                    'retailer_id' => $order->retailer_id,

                    'warehouse_id' => $order->warehouse_id,

                    'warehouse_name' =>
                        $order->warehouse->name ?? null,

                    'status' => $order->status,

                    'status_label' => ucfirst(
                        $order->status
                    ),

                    'total_amount' => number_format(
                        (float) $order->total_amount,
                        2,
                        '.',
                        ''
                    ),

                    'created_at' => $order->created_at,

                    'updated_at' => $order->updated_at,

                    'items' => $order->items->map(function ($item) {

                        return [

                            'id' => $item->id,

                            'product_id' =>
                                $item->product_id,

                            'product_name' =>
                                $item->product->name ?? null,

                            'quantity' =>
                                (int) $item->quantity,

                            'price' =>
                                number_format(
                                    (float) $item->price,
                                    2,
                                    '.',
                                    ''
                                ),

                            'discount_amount' =>
                                number_format(
                                    (float) ($item->discount_amount ?? 0),
                                    2,
                                    '.',
                                    ''
                                ),

                            'total' =>
                                number_format(
                                    (float) $item->total,
                                    2,
                                    '.',
                                    ''
                                ),
                        ];

                    })->values(),

                ];
            });


            // ============================================================
            // 5. LOG
            // ============================================================

            Log::info('Retailer Orders List - Success', [

                'user_id' =>
                    $user->id,

                'retailer_id' =>
                    $retailer->id,

                'orders_count' =>
                    $orders->count(),
            ]);


            // ============================================================
            // 6. RESPONSE
            // ============================================================

            return response()->json([

                'success' => true,

                'message' =>
                    'Retailer orders fetched successfully.',

                'data' => $orders,

            ], 200);


        } catch (\Throwable $e) {

            Log::error(
                'Retailer Orders List - Error',
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
                    'Unable to fetch retailer orders.',

                'error' =>
                    config('app.debug')
                        ? $e->getMessage()
                        : null,

            ], 500);
        }
    }

    /**
        * Receive Retailer Order
    */
    public function receiveOrder(
        Request $request,
        RetailerOrder $order
    ) {
        try {

            // ============================================================
            // 1. LOGGED-IN USER
            // ============================================================

            $user = $request->user();

            Log::info('================ RETAILER RECEIVE START ================', [

                'user_id' => $user->id,

                'order_id' => $order->id,

                'order_no' => $order->order_no,

                'order_status' => $order->status,

                'order_retailer_id' =>
                    $order->retailer_id,
            ]);


            // ============================================================
            // 2. GET RETAILER
            // ============================================================

            $retailer = Retailer::where('user_id', $user->id)
                ->where('is_active', 1)
                ->first();


            if (!$retailer) {

                Log::warning(
                    'Retailer Receive - Retailer Not Found',
                    [
                        'user_id' => $user->id,
                    ]
                );

                return response()->json([

                    'success' => false,

                    'message' =>
                        'Retailer profile not found.',

                ], 404);
            }


            // ============================================================
            // 3. SECURITY CHECK
            // ============================================================

            if (
                (int) $order->retailer_id
                !==
                (int) $retailer->id
            ) {

                Log::warning(
                    'Retailer Receive - Unauthorized Order',
                    [

                        'user_id' =>
                            $user->id,

                        'retailer_id' =>
                            $retailer->id,

                        'order_id' =>
                            $order->id,

                        'order_retailer_id' =>
                            $order->retailer_id,
                    ]
                );


                return response()->json([

                    'success' => false,

                    'message' =>
                        'You are not authorized to receive this order.',

                ], 403);
            }


            // ============================================================
            // 4. ONLY DISPATCHED ORDER CAN BE RECEIVED
            // ============================================================

            if ($order->status !== 'dispatched') {

                Log::warning(
                    'Retailer Receive - Invalid Status',
                    [

                        'order_id' =>
                            $order->id,

                        'current_status' =>
                            $order->status,

                        'required_status' =>
                            'dispatched',
                    ]
                );


                return response()->json([

                    'success' => false,

                    'message' =>
                        'Only dispatched orders can be received.',

                    'current_status' =>
                        $order->status,

                ], 422);
            }


            // ============================================================
            // 5. UPDATE STATUS
            // ============================================================

            $oldStatus = $order->status;


            $order->update([

                'status' =>
                    'delivered',

            ]);


            // ============================================================
            // 6. REFRESH ORDER
            // ============================================================

            $order->refresh();


            Log::info(
                'Retailer Receive - Status Updated',
                [

                    'order_id' =>
                        $order->id,

                    'order_no' =>
                        $order->order_no,

                    'old_status' =>
                        $oldStatus,

                    'new_status' =>
                        $order->status,

                    'retailer_id' =>
                        $retailer->id,
                ]
            );


            // ============================================================
            // 7. SUCCESS
            // ============================================================

            Log::info(
                '================ RETAILER RECEIVE SUCCESS ================'
            );


            return response()->json([

                'success' => true,

                'message' =>
                    'Order received successfully.',

                'order' => [

                    'id' =>
                        $order->id,

                    'order_no' =>
                        $order->order_no,

                    'retailer_id' =>
                        $order->retailer_id,

                    'warehouse_id' =>
                        $order->warehouse_id,

                    'status' =>
                        $order->status,

                    'total_amount' =>
                        number_format(
                            (float) $order->total_amount,
                            2,
                            '.',
                            ''
                        ),

                    'updated_at' =>
                        $order->updated_at,
                ],

            ], 200);


        } catch (\Throwable $e) {

            Log::error(
                '================ RETAILER RECEIVE FAILED ================',
                [

                    'user_id' =>
                        $request->user()?->id,

                    'order_id' =>
                        $order->id ?? null,

                    'order_no' =>
                        $order->order_no ?? null,

                    'retailer_id' =>
                        $retailer->id ?? null,

                    'current_status' =>
                        $order->status ?? null,

                    'error_message' =>
                        $e->getMessage(),

                    'error_line' =>
                        $e->getLine(),

                    'error_file' =>
                        $e->getFile(),

                    'trace' =>
                        $e->getTraceAsString(),
                ]
            );


            return response()->json([

                'success' => false,

                'message' =>
                    'Unable to receive order.',

                'error' =>
                    config('app.debug')
                        ? $e->getMessage()
                        : null,

            ], 500);
        }
    }

    
}
