<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Retailer;
use App\Models\RetailerCart;
use App\Models\RetailerCartItem;
use App\Models\RetailerPricing;
use App\Models\Warehouse;
use App\Models\WarehouseTransfer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class RetailerCartController extends Controller
{


    /**
     * Bulk Add Products To Cart
    */
    public function bulkAddToCart(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | 1. Validate Request
        |--------------------------------------------------------------------------
        */

        $data = $request->validate([
            'items' => [
                'required',
                'array',
                'min:1'
            ],

            'items.*.product_id' => [
                'required',
                'integer',
                'exists:products,id'
            ],

            'items.*.quantity' => [
                'required',
                'integer',
                'min:1'
            ],
        ]);


        /*
        |--------------------------------------------------------------------------
        | 2. Logged-in User
        |--------------------------------------------------------------------------
        */

        $user = auth()->user();

        \Log::info('Retailer Bulk Cart - Logged User', [
            'user_id'       => $user->id,
            'warehouse_id'  => $user->warehouse_id,
            'items'         => $data['items'],
        ]);


        /*
        |--------------------------------------------------------------------------
        | 3. Get Retailer From Logged-in User
        |--------------------------------------------------------------------------
        |
        | users.id = retailers.user_id
        |
        */

        $retailer = Retailer::where('user_id', $user->id)
            ->where('is_active', 1)
            ->first();


        \Log::info('Retailer Bulk Cart - Retailer Check', [
            'user_id'       => $user->id,
            'retailer_id'   => $retailer?->id,
            'retailer_name' => $retailer?->name,
            'shop_id'       => $retailer?->shop_id,
        ]);


        if (!$retailer) {

            return response()->json([
                'success' => false,
                'message' => 'Retailer profile not found.'
            ], 404);
        }


        /*
        |--------------------------------------------------------------------------
        | 4. Get Retailer's Distribution Center
        |--------------------------------------------------------------------------
        |
        | retailers.shop_id = DC warehouse id
        |
        */

        $warehouse = Warehouse::where('id', $retailer->shop_id)
            ->where('type', 'distribution_center')
            ->where('status', 'active')
            ->first();


        \Log::info('Retailer Bulk Cart - DC Check', [
            'retailer_id'  => $retailer->id,
            'shop_id'      => $retailer->shop_id,
            'warehouse_id' => $warehouse?->id,
            'warehouse'    => $warehouse?->name,
        ]);


        if (!$warehouse) {

            return response()->json([
                'success' => false,
                'message' => 'Distribution Center not found for this retailer.'
            ], 404);
        }


        /*
        |--------------------------------------------------------------------------
        | 5. Create / Get Active Cart
        |--------------------------------------------------------------------------
        */

        $cart = RetailerCart::firstOrCreate(
            [
                'retailer_id'  => $retailer->id,
                'warehouse_id' => $warehouse->id,
                'status'       => 'active',
            ]
        );


        /*
        |--------------------------------------------------------------------------
        | 6. Process Products
        |--------------------------------------------------------------------------
        */

        $addedItems = [];
        $errors = [];


        foreach ($data['items'] as $item) {

            $productId = $item['product_id'];
            $quantity  = $item['quantity'];


            /*
            |--------------------------------------------------------------------------
            | 7. Get Product
            |--------------------------------------------------------------------------
            */

            $product = Product::find($productId);


            if (!$product) {

                $errors[] = [
                    'product_id' => $productId,
                    'message'    => 'Product not found.'
                ];

                continue;
            }


            /*
            |--------------------------------------------------------------------------
            | 8. Verify Product Available In Retailer's DC
            |--------------------------------------------------------------------------
            |
            | warehouse_transfers:
            |
            | requested_by_warehouse_id = DC
            | product_id                = Product
            |
            */

            $transfer = WarehouseTransfer::where(
                    'requested_by_warehouse_id',
                    $warehouse->id
                )
                ->where(
                    'product_id',
                    $productId
                )
                ->whereIn('status', [
                    'approved',
                    'completed',
                    'delivered'
                ])
                ->first();


            /*
            |----------------------------------------------------------------------
            | If your warehouse transfer uses another status
            |----------------------------------------------------------------------
            |
            | Agar aapke database mein status = 5 ya koi aur value hai,
            | to upar whereIn ko remove karke sirf warehouse + product check
            | kar sakte ho.
            |
            */


            if (!$transfer) {

                /*
                |------------------------------------------------------------------
                | Fallback check
                |------------------------------------------------------------------
                */

                $transfer = WarehouseTransfer::where(
                        'requested_by_warehouse_id',
                        $warehouse->id
                    )
                    ->where(
                        'product_id',
                        $productId
                    )
                    ->first();
            }


            if (!$transfer) {

                $errors[] = [
                    'product_id'   => $productId,
                    'product_name' => $product->name,
                    'message'      =>
                        'Product is not available in this Distribution Center.'
                ];

                continue;
            }


            /*
            |--------------------------------------------------------------------------
            | 9. Check Available Quantity
            |--------------------------------------------------------------------------
            */

            $availableQty = (int) $transfer->quantity;


            \Log::info('Retailer Bulk Cart - Stock Check', [
                'retailer_id'   => $retailer->id,
                'warehouse_id'  => $warehouse->id,
                'product_id'    => $productId,
                'product_name'  => $product->name,
                'available_qty' => $availableQty,
                'requested_qty' => $quantity,
            ]);


            if ($quantity > $availableQty) {

                $errors[] = [
                    'product_id'    => $productId,
                    'product_name'  => $product->name,
                    'requested_qty' => $quantity,
                    'available_qty' => $availableQty,
                    'message'       =>
                        "Only {$availableQty} quantity available in this Distribution Center."
                ];

                continue;
            }


            /*
            |--------------------------------------------------------------------------
            | 10. Get Retailer Pricing
            |--------------------------------------------------------------------------
            |
            | IMPORTANT:
            |
            | retailer_id  = retailers.id
            | warehouse_id = retailers.shop_id
            | product_id   = selected product
            |
            */

            $pricing = RetailerPricing::where(
                    'retailer_id',
                    $retailer->id
                )
                ->where(
                    'warehouse_id',
                    $warehouse->id
                )
                ->where(
                    'product_id',
                    $productId
                )
                ->where(
                    'is_active',
                    1
                )
                ->latest('id')
                ->first();


            \Log::info('Retailer Bulk Cart - Pricing Check', [
                'retailer_id'  => $retailer->id,
                'warehouse_id' => $warehouse->id,
                'product_id'   => $productId,
                'pricing_id'   => $pricing?->id,
                'price'        => $pricing?->effective_price,
            ]);


            if (!$pricing) {

                $errors[] = [
                    'product_id'   => $productId,
                    'product_name' => $product->name,
                    'message'      =>
                        'Retailer pricing not found for this product.'
                ];

                continue;
            }


            /*
            |--------------------------------------------------------------------------
            | 11. Price
            |--------------------------------------------------------------------------
            */

            $price = (float) $pricing->effective_price;

            $discountAmount = (float) $pricing->discount_amount;


            /*
            |--------------------------------------------------------------------------
            | 12. Check Existing Cart Item
            |--------------------------------------------------------------------------
            */

            $cartItem = RetailerCartItem::where(
                    'cart_id',
                    $cart->id
                )
                ->where(
                    'product_id',
                    $productId
                )
                ->first();


            if ($cartItem) {

                /*
                |------------------------------------------------------------------
                | Existing quantity + new quantity
                |------------------------------------------------------------------
                */

                $newQuantity =
                    (int) $cartItem->quantity + $quantity;


                /*
                |------------------------------------------------------------------
                | Stock Check Again
                |------------------------------------------------------------------
                */

                if ($newQuantity > $availableQty) {

                    $errors[] = [
                        'product_id'    => $productId,
                        'product_name'  => $product->name,
                        'requested_qty' => $newQuantity,
                        'available_qty' => $availableQty,
                        'message'       =>
                            "Cart quantity cannot exceed available stock of {$availableQty}."
                    ];

                    continue;
                }


                /*
                |------------------------------------------------------------------
                | Update Existing Cart Item
                |------------------------------------------------------------------
                */

                $cartItem->quantity = $newQuantity;

                $cartItem->price = $price;

                $cartItem->discount_amount =
                    $discountAmount;

                $cartItem->total =
                    $newQuantity * $price;

                $cartItem->save();


                $addedItems[] = [
                    'cart_item_id' => $cartItem->id,
                    'product_id'   => $productId,
                    'product_name' => $product->name,
                    'quantity'     => $newQuantity,
                    'price'        => $price,
                    'discount'     => $discountAmount,
                    'total'        => $cartItem->total,
                    'action'       => 'updated',
                ];


                continue;
            }


            /*
            |--------------------------------------------------------------------------
            | 13. Add New Cart Item
            |--------------------------------------------------------------------------
            */

            $total =
                $quantity * $price;


            $cartItem = RetailerCartItem::create([

                'cart_id' =>
                    $cart->id,

                'product_id' =>
                    $productId,

                'category_id' =>
                    $product->category_id,

                'quantity' =>
                    $quantity,

                'price' =>
                    $price,

                'discount_amount' =>
                    $discountAmount,

                'total' =>
                    $total,

            ]);


            $addedItems[] = [
                'cart_item_id' => $cartItem->id,
                'product_id'   => $productId,
                'product_name' => $product->name,
                'quantity'     => $quantity,
                'price'        => $price,
                'discount'     => $discountAmount,
                'total'        => $total,
                'action'       => 'added',
            ];
        }


        /*
        |--------------------------------------------------------------------------
        | 14. Cart Total
        |--------------------------------------------------------------------------
        */

        $cartTotal = RetailerCartItem::where(
                'cart_id',
                $cart->id
            )
            ->sum('total');


        /*
        |--------------------------------------------------------------------------
        | 15. Log Final Result
        |--------------------------------------------------------------------------
        */

        \Log::info('Retailer Bulk Cart - Final Result', [
            'cart_id'      => $cart->id,
            'retailer_id'  => $retailer->id,
            'warehouse_id' => $warehouse->id,
            'added_items'  => count($addedItems),
            'errors'       => count($errors),
            'cart_total'   => $cartTotal,
        ]);


        /*
        |--------------------------------------------------------------------------
        | 16. Response
        |--------------------------------------------------------------------------
        */

        if (count($addedItems) === 0) {

            return response()->json([
                'success' => false,
                'message' => 'No products were added to cart.',
                'retailer' => [
                    'id'   => $retailer->id,
                    'name' => $retailer->name,
                ],
                'distribution_center' => [
                    'id'   => $warehouse->id,
                    'name' => $warehouse->name,
                ],
                'errors' => $errors,
            ], 422);
        }


        return response()->json([
            'success' => true,
            'message' =>
                count($addedItems) . ' product(s) added to cart successfully.',

            'cart' => [
                'id'           => $cart->id,
                'retailer_id'  => $retailer->id,
                'warehouse_id' => $warehouse->id,
                'status'       => $cart->status,
                'total_amount' => number_format($cartTotal, 2, '.', ''),
            ],

            'items' => $addedItems,

            'errors' => $errors,
        ]);
    }


    /**
     * Get Logged-in Retailer's Active Cart
     */
    public function index(Request $request)
    {
        try {

            /*
            |--------------------------------------------------------------------------
            | 1. Logged-in User
            |--------------------------------------------------------------------------
            */

            $user = $request->user();

            Log::info('Retailer Cart - Logged User', [
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

                return response()->json([
                    'success' => false,
                    'message' => 'Retailer profile not found.',
                ], 404);
            }


            /*
            |--------------------------------------------------------------------------
            | 3. Get Active Cart
            |--------------------------------------------------------------------------
            */

            $cart = RetailerCart::with([
                'warehouse:id,name',
                'items.product:id,name',
                'items.category:id,name',
            ])
            ->where('retailer_id', $retailer->id)
            ->where('status', 'active')
            ->latest('id')
            ->first();


            /*
            |--------------------------------------------------------------------------
            | 4. Cart Not Found
            |--------------------------------------------------------------------------
            */

            if (!$cart) {

                return response()->json([
                    'success' => true,
                    'message' => 'Your cart is empty.',
                    'cart' => null,
                    'items' => [],
                    'summary' => [
                        'total_items' => 0,
                        'total_quantity' => 0,
                        'total_amount' => 0,
                    ],
                ]);
            }


            /*
            |--------------------------------------------------------------------------
            | 5. Calculate Cart Items
            |--------------------------------------------------------------------------
            */

            $items = $cart->items->map(function ($item) {

                return [
                    'cart_item_id' => $item->id,

                    'product_id' => $item->product_id,

                    'product_name' =>
                        $item->product?->name,

                    'category_id' => $item->category_id,

                    'category_name' =>
                        $item->category?->name,

                    'quantity' =>
                        (int) $item->quantity,

                    'price' =>
                        (float) $item->price,

                    'discount' =>
                        (float) $item->discount_amount,

                    'total' =>
                        (float) $item->total,
                ];
            });


            /*
            |--------------------------------------------------------------------------
            | 6. Summary
            |--------------------------------------------------------------------------
            */

            $totalQuantity = $cart->items->sum('quantity');

            $totalAmount = $cart->items->sum('total');


            /*
            |--------------------------------------------------------------------------
            | 7. Response
            |--------------------------------------------------------------------------
            */

            return response()->json([

                'success' => true,

                'message' => 'Cart fetched successfully.',

                'cart' => [

                    'id' =>
                        $cart->id,

                    'retailer_id' =>
                        $cart->retailer_id,

                    'warehouse_id' =>
                        $cart->warehouse_id,

                    'warehouse_name' =>
                        $cart->warehouse?->name,

                    'status' =>
                        $cart->status,

                    'total_amount' =>
                        number_format($totalAmount, 2, '.', ''),

                ],

                'items' =>
                    $items,

                'summary' => [

                    'total_items' =>
                        $cart->items->count(),

                    'total_quantity' =>
                        (int) $totalQuantity,

                    'total_amount' =>
                        number_format($totalAmount, 2, '.', ''),

                ],

            ]);

        } catch (\Throwable $e) {

            /*
            |--------------------------------------------------------------------------
            | Error Log
            |--------------------------------------------------------------------------
            */

            Log::error('Retailer Cart - Fetch Error', [

                'user_id' =>
                    $request->user()?->id,

                'error' =>
                    $e->getMessage(),

                'line' =>
                    $e->getLine(),

                'file' =>
                    $e->getFile(),

            ]);


            return response()->json([

                'success' => false,

                'message' =>
                    'Unable to fetch cart.',

            ], 500);
        }
    }


    /**
     * Update Retailer Cart Item
    */
    public function updateItem(Request $request, $cartItemId)
    {
        try {

            /*
            |--------------------------------------------------------------------------
            | 1. Validate Quantity
            |--------------------------------------------------------------------------
            */

            $data = $request->validate([
                'quantity' => [
                    'required',
                    'integer',
                    'min:1',
                ],
            ]);


            /*
            |--------------------------------------------------------------------------
            | 2. Logged-in User
            |--------------------------------------------------------------------------
            */

            $user = $request->user();

            Log::info('Retailer Cart Update - Request', [
                'user_id' => $user->id,
                'cart_item_id' => $cartItemId,
                'requested_quantity' => $data['quantity'],
            ]);


            /*
            |--------------------------------------------------------------------------
            | 3. Get Retailer
            |--------------------------------------------------------------------------
            */

            $retailer = Retailer::where('user_id', $user->id)
                ->where('is_active', 1)
                ->first();


            if (!$retailer) {

                return response()->json([
                    'success' => false,
                    'message' => 'Retailer profile not found.',
                ], 404);
            }


            /*
            |--------------------------------------------------------------------------
            | 4. Get Cart Item
            |--------------------------------------------------------------------------
            */

            $cartItem = RetailerCartItem::with([
                'cart',
                'product',
                'category',
            ])
            ->where('id', $cartItemId)
            ->first();


            if (!$cartItem) {

                return response()->json([
                    'success' => false,
                    'message' => 'Cart item not found.',
                ], 404);
            }


            /*
            |--------------------------------------------------------------------------
            | 5. Verify Cart Belongs To Retailer
            |--------------------------------------------------------------------------
            */

            $cart = $cartItem->cart;


            if (!$cart) {

                return response()->json([
                    'success' => false,
                    'message' => 'Cart not found.',
                ], 404);
            }


            if ((int) $cart->retailer_id !== (int) $retailer->id) {

                Log::warning('Retailer Cart Update - Unauthorized Cart', [
                    'user_id' => $user->id,
                    'retailer_id' => $retailer->id,
                    'cart_id' => $cart->id,
                    'cart_retailer_id' => $cart->retailer_id,
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'You are not authorized to update this cart.',
                ], 403);
            }


            /*
            |--------------------------------------------------------------------------
            | 6. Cart Must Be Active
            |--------------------------------------------------------------------------
            */

            if ($cart->status !== 'active') {

                return response()->json([
                    'success' => false,
                    'message' => 'This cart is no longer active.',
                ], 422);
            }


            /*
            |--------------------------------------------------------------------------
            | 7. Get Distribution Center
            |--------------------------------------------------------------------------
            */

            $warehouse = Warehouse::where('id', $cart->warehouse_id)
                ->where('type', 'distribution_center')
                ->where('status', 'active')
                ->first();


            if (!$warehouse) {

                return response()->json([
                    'success' => false,
                    'message' => 'Distribution Center is not available.',
                ], 422);
            }


            /*
            |--------------------------------------------------------------------------
            | 8. Product
            |--------------------------------------------------------------------------
            */

            $product = $cartItem->product;


            if (!$product) {

                return response()->json([
                    'success' => false,
                    'message' => 'Product not found.',
                ], 404);
            }


            /*
            |--------------------------------------------------------------------------
            | 9. Verify Product Category
            |--------------------------------------------------------------------------
            */

            if (
                (int) $product->category_id !==
                (int) $cartItem->category_id
            ) {

                return response()->json([
                    'success' => false,
                    'message' => 'Product does not belong to selected category.',
                ], 422);
            }


            /*
            |--------------------------------------------------------------------------
            | 10. Check DC Available Quantity
            |--------------------------------------------------------------------------
            |
            | IMPORTANT:
            |
            | warehouse_transfers.quantity = total quantity received
            |
            */

            $availableQty = WarehouseTransfer::where(
                    'requested_by_warehouse_id',
                    $warehouse->id
                )
                ->where(
                    'product_id',
                    $cartItem->product_id
                )
                ->sum('quantity');


            Log::info('Retailer Cart Update - DC Stock Check', [
                'warehouse_id' => $warehouse->id,
                'product_id' => $cartItem->product_id,
                'available_quantity' => $availableQty,
                'requested_quantity' => $data['quantity'],
            ]);


            /*
            |--------------------------------------------------------------------------
            | 11. Quantity Cannot Exceed DC Stock
            |--------------------------------------------------------------------------
            */

            if ($data['quantity'] > $availableQty) {

                return response()->json([
                    'success' => false,
                    'message' =>
                        'Requested quantity exceeds available quantity in Distribution Center.',

                    'product_id' =>
                        $cartItem->product_id,

                    'product_name' =>
                        $product->name,

                    'available_quantity' =>
                        (int) $availableQty,

                    'requested_quantity' =>
                        (int) $data['quantity'],
                ], 422);
            }


            /*
            |--------------------------------------------------------------------------
            | 12. Get Retailer Pricing
            |--------------------------------------------------------------------------
            */

            $pricing = RetailerPricing::where(
                    'retailer_id',
                    $retailer->id
                )
                ->where(
                    'warehouse_id',
                    $warehouse->id
                )
                ->where(
                    'product_id',
                    $cartItem->product_id
                )
                ->where(
                    'category_id',
                    $cartItem->category_id
                )
                ->where(
                    'is_active',
                    1
                )
                ->whereDate(
                    'effective_from',
                    '<=',
                    now()->toDateString()
                )
                ->where(function ($query) {

                    $query->whereNull('effective_to')
                        ->orWhereDate(
                            'effective_to',
                            '>=',
                            now()->toDateString()
                        );

                })
                ->latest('id')
                ->first();


            /*
            |--------------------------------------------------------------------------
            | 13. Pricing Not Found
            |--------------------------------------------------------------------------
            */

            if (!$pricing) {

                Log::warning(
                    'Retailer Cart Update - Pricing Not Found',
                    [
                        'retailer_id' => $retailer->id,
                        'warehouse_id' => $warehouse->id,
                        'product_id' => $cartItem->product_id,
                        'category_id' => $cartItem->category_id,
                    ]
                );

                return response()->json([
                    'success' => false,
                    'message' =>
                        'Retailer pricing not found for this product.',
                    'product_id' =>
                        $cartItem->product_id,
                    'product_name' =>
                        $product->name,
                ], 422);
            }


            /*
            |--------------------------------------------------------------------------
            | 14. Get Price
            |--------------------------------------------------------------------------
            */

            $price = (float) $pricing->base_price;

            $discountPercent =
                (float) ($pricing->discount_percent ?? 0);

            $discountAmount =
                (float) ($pricing->discount_amount ?? 0);


            /*
            |--------------------------------------------------------------------------
            | 15. Recalculate Discount
            |--------------------------------------------------------------------------
            |
            | Retailer pricing:
            |
            | Base Price = 150
            | Discount = 10%
            | Discount Amount = 15
            | Effective Price = 135
            |
            */

            if ($discountPercent > 0) {

                $discountAmount =
                    ($price * $discountPercent) / 100;
            }


            /*
            |--------------------------------------------------------------------------
            | 16. Effective Price
            |--------------------------------------------------------------------------
            */

            $effectivePrice =
                $price - $discountAmount;


            if ($effectivePrice < 0) {

                return response()->json([
                    'success' => false,
                    'message' =>
                        'Effective price cannot be negative.',
                ], 422);
            }


            /*
            |--------------------------------------------------------------------------
            | 17. Calculate Item Total
            |--------------------------------------------------------------------------
            */

            $itemTotal =
                $effectivePrice * $data['quantity'];


            /*
            |--------------------------------------------------------------------------
            | 18. Update Cart Item
            |--------------------------------------------------------------------------
            */

            $cartItem->update([

                'quantity' =>
                    $data['quantity'],

                'price' =>
                    $effectivePrice,

                'discount_amount' =>
                    $discountAmount,

                'total' =>
                    $itemTotal,

            ]);


            /*
            |--------------------------------------------------------------------------
            | 19. Recalculate Complete Cart Total
            |--------------------------------------------------------------------------
            */

            $cartTotal = RetailerCartItem::where(
                    'cart_id',
                    $cart->id
                )
                ->sum('total');


            /*
            |--------------------------------------------------------------------------
            | 20. Update Cart
            |--------------------------------------------------------------------------
            */

            $cart->update([

                'total_amount' =>
                    $cartTotal,

            ]);


            /*
            |--------------------------------------------------------------------------
            | 21. Success Log
            |--------------------------------------------------------------------------
            */

            Log::info(
                'Retailer Cart Update - Successfully Updated',
                [
                    'cart_id' =>
                        $cart->id,

                    'cart_item_id' =>
                        $cartItem->id,

                    'retailer_id' =>
                        $retailer->id,

                    'warehouse_id' =>
                        $warehouse->id,

                    'product_id' =>
                        $product->id,

                    'old_quantity' =>
                        $cartItem->getOriginal('quantity'),

                    'new_quantity' =>
                        $data['quantity'],

                    'price' =>
                        $effectivePrice,

                    'discount' =>
                        $discountAmount,

                    'item_total' =>
                        $itemTotal,

                    'cart_total' =>
                        $cartTotal,
                ]
            );


            /*
            |--------------------------------------------------------------------------
            | 22. Response
            |--------------------------------------------------------------------------
            */

            return response()->json([

                'success' => true,

                'message' =>
                    'Cart item updated successfully.',

                'cart' => [

                    'id' =>
                        $cart->id,

                    'retailer_id' =>
                        $cart->retailer_id,

                    'warehouse_id' =>
                        $cart->warehouse_id,

                    'status' =>
                        $cart->status,

                    'total_amount' =>
                        number_format(
                            $cartTotal,
                            2,
                            '.',
                            ''
                        ),
                ],

                'item' => [

                    'cart_item_id' =>
                        $cartItem->id,

                    'product_id' =>
                        $product->id,

                    'product_name' =>
                        $product->name,

                    'quantity' =>
                        (int) $cartItem->quantity,

                    'price' =>
                        (float) $cartItem->price,

                    'discount' =>
                        (float) $cartItem->discount_amount,

                    'total' =>
                        (float) $cartItem->total,

                ],

                'available_quantity' =>
                    (int) $availableQty,

            ]);


        } catch (\Throwable $e) {

            /*
            |--------------------------------------------------------------------------
            | Error Log
            |--------------------------------------------------------------------------
            */

            Log::error(
                'Retailer Cart Update - Error',
                [
                    'user_id' =>
                        $request->user()?->id,

                    'cart_item_id' =>
                        $cartItemId,

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
                    'Unable to update cart item.',

            ], 500);
        }
    }


    /**
     * Remove Item From Retailer Cart
    */
    public function removeItem(Request $request, $cartItemId)
    {
        try {

            /*
            |--------------------------------------------------------------------------
            | 1. Logged-in User
            |--------------------------------------------------------------------------
            */

            $user = $request->user();

            Log::info('Retailer Cart Remove - Request', [
                'user_id' => $user->id,
                'cart_item_id' => $cartItemId,
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

                return response()->json([
                    'success' => false,
                    'message' => 'Retailer profile not found.',
                ], 404);
            }


            /*
            |--------------------------------------------------------------------------
            | 3. Get Cart Item
            |--------------------------------------------------------------------------
            */

            $cartItem = RetailerCartItem::with([
                'cart',
                'product'
            ])
            ->where('id', $cartItemId)
            ->first();


            if (!$cartItem) {

                return response()->json([
                    'success' => false,
                    'message' => 'Cart item not found.',
                ], 404);
            }


            /*
            |--------------------------------------------------------------------------
            | 4. Get Cart
            |--------------------------------------------------------------------------
            */

            $cart = $cartItem->cart;


            if (!$cart) {

                return response()->json([
                    'success' => false,
                    'message' => 'Cart not found.',
                ], 404);
            }


            /*
            |--------------------------------------------------------------------------
            | 5. Verify Cart Belongs To Retailer
            |--------------------------------------------------------------------------
            */

            if ((int) $cart->retailer_id !== (int) $retailer->id) {

                Log::warning(
                    'Retailer Cart Remove - Unauthorized Cart',
                    [
                        'user_id' => $user->id,
                        'retailer_id' => $retailer->id,
                        'cart_id' => $cart->id,
                        'cart_retailer_id' => $cart->retailer_id,
                    ]
                );

                return response()->json([
                    'success' => false,
                    'message' => 'You are not authorized to modify this cart.',
                ], 403);
            }


            /*
            |--------------------------------------------------------------------------
            | 6. Cart Must Be Active
            |--------------------------------------------------------------------------
            */

            if ($cart->status !== 'active') {

                return response()->json([
                    'success' => false,
                    'message' => 'This cart is no longer active.',
                ], 422);
            }


            /*
            |--------------------------------------------------------------------------
            | 7. Store Item Information Before Delete
            |--------------------------------------------------------------------------
            */

            $removedItem = [
                'cart_item_id' => $cartItem->id,
                'product_id' => $cartItem->product_id,
                'product_name' => $cartItem->product?->name,
                'quantity' => (int) $cartItem->quantity,
                'price' => (float) $cartItem->price,
                'discount' => (float) $cartItem->discount_amount,
                'total' => (float) $cartItem->total,
            ];


            /*
            |--------------------------------------------------------------------------
            | 8. Delete Cart Item
            |--------------------------------------------------------------------------
            */

            $cartItem->delete();


            Log::info(
                'Retailer Cart Remove - Item Deleted',
                [
                    'cart_id' => $cart->id,
                    'cart_item_id' => $removedItem['cart_item_id'],
                    'product_id' => $removedItem['product_id'],
                    'quantity' => $removedItem['quantity'],
                ]
            );


            /*
            |--------------------------------------------------------------------------
            | 9. Recalculate Cart Total
            |--------------------------------------------------------------------------
            */

            $cartTotal = RetailerCartItem::where(
                    'cart_id',
                    $cart->id
                )
                ->sum('total');


            /*
            |--------------------------------------------------------------------------
            | 10. Update Cart Total
            |--------------------------------------------------------------------------
            */

            $cart->update([
                'total_amount' => $cartTotal,
            ]);


            /*
            |--------------------------------------------------------------------------
            | 11. Get Remaining Items Count
            |--------------------------------------------------------------------------
            */

            $remainingItems = RetailerCartItem::where(
                    'cart_id',
                    $cart->id
                )
                ->count();


            $remainingQuantity = RetailerCartItem::where(
                    'cart_id',
                    $cart->id
                )
                ->sum('quantity');


            /*
            |--------------------------------------------------------------------------
            | 12. Success Response
            |--------------------------------------------------------------------------
            */

            return response()->json([

                'success' => true,

                'message' =>
                    'Cart item removed successfully.',

                'removed_item' =>
                    $removedItem,

                'cart' => [

                    'id' =>
                        $cart->id,

                    'retailer_id' =>
                        $cart->retailer_id,

                    'warehouse_id' =>
                        $cart->warehouse_id,

                    'status' =>
                        $cart->status,

                    'total_amount' =>
                        number_format(
                            $cartTotal,
                            2,
                            '.',
                            ''
                        ),

                ],

                'summary' => [

                    'total_items' =>
                        $remainingItems,

                    'total_quantity' =>
                        (int) $remainingQuantity,

                    'total_amount' =>
                        number_format(
                            $cartTotal,
                            2,
                            '.',
                            ''
                        ),

                ],

            ]);


        } catch (\Throwable $e) {

            /*
            |--------------------------------------------------------------------------
            | Error Log
            |--------------------------------------------------------------------------
            */

            Log::error(
                'Retailer Cart Remove - Error',
                [
                    'user_id' =>
                        $request->user()?->id,

                    'cart_item_id' =>
                        $cartItemId,

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
                    'Unable to remove cart item.',

            ], 500);
        }
    }


    /**
        * Clear Retailer Cart
    */
    public function clearCart(Request $request)
    {
        DB::beginTransaction();

        try {

            /*
            |--------------------------------------------------------------------------
            | 1. Logged-in User
            |--------------------------------------------------------------------------
            */

            $user = $request->user();

            Log::info('Retailer Cart Clear - Request', [
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


            /*
            |--------------------------------------------------------------------------
            | 3. Get Active Cart
            |--------------------------------------------------------------------------
            */

            $cart = RetailerCart::where('retailer_id', $retailer->id)
                ->where('status', 'active')
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

            $items = RetailerCartItem::where(
                'cart_id',
                $cart->id
            )->get();


            $totalItems = $items->count();

            $totalQuantity = $items->sum('quantity');

            $totalAmount = $items->sum('total');


            Log::info('Retailer Cart Clear - Cart Found', [
                'cart_id' => $cart->id,
                'retailer_id' => $retailer->id,
                'warehouse_id' => $cart->warehouse_id,
                'total_items' => $totalItems,
                'total_quantity' => $totalQuantity,
                'total_amount' => $totalAmount,
            ]);


            /*
            |--------------------------------------------------------------------------
            | 5. Delete Cart Items
            |--------------------------------------------------------------------------
            */

            $deletedItems = RetailerCartItem::where(
                'cart_id',
                $cart->id
            )->delete();


            Log::info('Retailer Cart Clear - Items Deleted', [
                'cart_id' => $cart->id,
                'deleted_items' => $deletedItems,
            ]);


            /*
            |--------------------------------------------------------------------------
            | 6. Delete Main Cart
            |--------------------------------------------------------------------------
            */

            $cartId = $cart->id;

            $cart->delete();


            Log::info('Retailer Cart Clear - Main Cart Deleted', [
                'cart_id' => $cartId,
                'retailer_id' => $retailer->id,
            ]);


            /*
            |--------------------------------------------------------------------------
            | 7. Commit
            |--------------------------------------------------------------------------
            */

            DB::commit();


            /*
            |--------------------------------------------------------------------------
            | 8. Success Response
            |--------------------------------------------------------------------------
            */

            return response()->json([

                'success' => true,

                'message' => 'Cart cleared and removed successfully.',

                'deleted_cart' => [

                    'cart_id' => $cartId,

                    'retailer_id' =>
                        $retailer->id,

                    'warehouse_id' =>
                        $cart->warehouse_id,

                ],

                'summary' => [

                    'removed_items' =>
                        $totalItems,

                    'removed_quantity' =>
                        (int) $totalQuantity,

                    'previous_total_amount' =>
                        number_format(
                            $totalAmount,
                            2,
                            '.',
                            ''
                        ),

                ],

            ]);


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
                'Retailer Cart Clear - Error',
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
                    'Unable to clear cart.',

            ], 500);
        }
    }


}