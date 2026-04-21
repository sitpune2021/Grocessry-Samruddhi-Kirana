<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderItem;
use Throwable;
use App\Models\Offer;
use App\Models\WarehouseStock;
use App\Models\Role;
use App\Models\UserAddress;
use Illuminate\Support\Facades\DB;
use App\Models\Coupon;
use Carbon\Carbon;

class ProductController extends Controller
{

    public function addToCart(Request $request)
    {
        $warehouseId = $request->header('warehouse-id');

        if (!$warehouseId) {
            return response()->json([
                'status' => false,
                'message' => 'Warehouse not selected'
            ], 400);
        }

        $user = $request->user();
        if ($res = $this->checkCustomer($user)) return $res;

        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity'   => 'nullable|integer|min:1',
        ]);

        $qty = $request->quantity ?? 1;
        $product = Product::with('tax')->findOrFail($request->product_id);

        // REAL STOCK
        $availableStock = WarehouseStock::where('product_id', $product->id)
            ->where('warehouse_id', $warehouseId)
            ->sum('quantity');

        if ($availableStock <= 0) {
            return response()->json([
                'status' => false,
                'message' => 'Product out of stock'
            ], 400);
        }

        $cart = Cart::where('user_id', $user->id)->first();

        if ($cart && $cart->warehouse_id != $warehouseId) {
            return response()->json([
                'status' => false,
                'message' => 'Your cart belongs to a different location. Please clear cart.'
            ], 400);
        }


        // Get or create cart
        $cart = Cart::firstOrCreate(
            [
                'user_id' => $user->id,
                'warehouse_id' => $warehouseId
            ],
            [
                'subtotal'  => 0,
                'tax_total' => 0,
                'discount'  => 0,
                'total'     => 0
            ]
        );

        // Existing cart item
        $cartItem = CartItem::where('cart_id', $cart->id)
            ->where('product_id', $product->id)
            ->first();

        $existingQty = $cartItem ? $cartItem->qty : 0;
        $finalQty = $existingQty + $qty;

        if ($finalQty > $availableStock) {
            return response()->json([
                'status' => false,
                'message' => 'Only ' . $availableStock . ' items available'
            ], 400);
        }

        // 🔹 TAX CALCULATION
        $price = $product->retailer_price;

        $cgstPercent = $product->tax?->cgst ?? 0;
        $sgstPercent = $product->tax?->sgst ?? 0;

        $cgstAmount = ($price * $cgstPercent / 100) * $finalQty;
        $sgstAmount = ($price * $sgstPercent / 100) * $finalQty;
        $taxTotal   = $cgstAmount + $sgstAmount;

        $itemTotal = ($price * $finalQty) + $taxTotal;

        if ($cartItem) {
            $cartItem->update([
                'qty'         => $finalQty,
                'price'       => $price,
                'cgst_amount' => $cgstAmount,
                'sgst_amount' => $sgstAmount,
                'tax_total'   => $taxTotal,
                'item_total'  => $itemTotal
            ]);
        } else {
            CartItem::create([
                'cart_id'     => $cart->id,
                'product_id'  => $product->id,
                'qty'         => $qty,
                'price'       => $price,
                'cgst_amount' => ($price * $cgstPercent / 100) * $qty,
                'sgst_amount' => ($price * $sgstPercent / 100) * $qty,
                'tax_total'   => (($price * $cgstPercent / 100) + ($price * $sgstPercent / 100)) * $qty,
                'item_total'  => (($price * $qty) + ((($price * $cgstPercent / 100) + ($price * $sgstPercent / 100)) * $qty))
            ]);
        }

        $subtotal = CartItem::where('cart_id', $cart->id)
            ->sum(DB::raw('price * qty'));

        $taxTotal = CartItem::where('cart_id', $cart->id)
            ->sum('tax_total');

        $cart->update([
            'subtotal'  => $subtotal,
            'tax_total' => $taxTotal,
            'total'     => $subtotal + $taxTotal

        ]);

        // AUTO APPLY OFFER
        // $this->autoApplyOffer($cart);


        return response()->json([
            'status' => true,
            'message' => 'Product added to cart successfully'
        ]);
    }

    // private function autoApplyOffer(Cart $cart)
    // {
    //     $today = Carbon::today()->toDateString();

    //     $offer = Offer::where('status', 1)
    //         ->whereDate('start_date', '<=', $today)
    //         ->whereDate('end_date', '>=', $today)
    //         ->where('min_order_amount', '<=', $cart->subtotal)
    //         ->orderBy('discount_value', 'desc') // best offer
    //         ->first();

    //     if (!$offer) {
    //         // ❌ No offer matched → remove discount
    //         $cart->update([
    //             'discount' => 0,
    //             'total'    => $cart->subtotal + $cart->tax_total
    //         ]);
    //         return;
    //     }

    //     // 💸 Discount calculation
    //     if (in_array($offer->offer_type, ['flat', 'flat_discount'])) {
    //         $discount = $offer->discount_value;
    //     } else {
    //         $discount = ($cart->subtotal * $offer->discount_value) / 100;

    //         if (!empty($offer->max_discount)) {
    //             $discount = min($discount, $offer->max_discount);
    //         }
    //     }

    //     $discount = min($discount, $cart->subtotal);

    //     // ✅ Apply offer
    //     $cart->update([
    //         'discount' => round($discount, 2),
    //         'total'    => max(($cart->subtotal + $cart->tax_total) - $discount, 0),
    //         'offer_id' => $offer->id ?? null
    //     ]);
    // }

    // public function viewCart(Request $request)
    // {
    //     $user = $request->user();
    //     if ($res = $this->checkCustomer($user)) return $res;

    //     $cart = Cart::with([
    //         'items.product.tax',
    //         'coupon'
    //     ])->where('user_id', $user->id)->first();

    //     if ($cart && $cart->coupon) {
    //     $cart->coupon_details = [
    //         'id' => $cart->coupon->id,
    //         'title' => $cart->coupon->title,
    //         'code' => $cart->coupon->code,
    //         'discount_type' => $cart->coupon->discount_type,
    //         'discount_value' => $cart->coupon->discount_value,
    //     ];
    // } else {
    //     $cart->coupon_details = null;
    // }

    //     return response()->json([
    //         'status' => true,
    //         'data'   => $cart
    //     ]);
    // }


    public function viewCart(Request $request)
    {
        $user = $request->user();
        if ($res = $this->checkCustomer($user)) return $res;

        $today = now();

        $cart = Cart::with([
            'items.product.tax'
        ])->where('user_id', $user->id)->first();

        // Step 1: Get valid coupons
        $coupons = Coupon::where('status', 1)
            ->whereDate('start_date', '<=', $today)
            ->whereDate('end_date', '>=', $today)
            ->get();

        // Step 2: Format coupons
        $couponList = $coupons->map(function ($coupon) use ($cart) {

            // Check applicable
            $isApplicable = $cart && $cart->subtotal >= $coupon->min_amount;

            return [
                'id' => $coupon->id,
                'title' => $coupon->title,
                'code' => $coupon->code,
                // 'description' => $coupon->description,

                // 'discount_text' => $coupon->discount_type == 'flat'
                //     ? '₹' . $coupon->discount_value . ' OFF'
                //     : $coupon->discount_value . '% OFF',

                // 'min_amount' => (float)$coupon->min_amount,
                // 'valid_till' => \Carbon\Carbon::parse($coupon->end_date)->format('d M Y'),

                'is_applicable' => $isApplicable
            ];
        });

        // Step 3: Best coupon logic
        // $bestCoupon = $couponList
        //     ->where('is_applicable', true)
        //     ->sortByDesc(function ($coupon) {
        //         // extract numeric value for comparison
        //         return (int) filter_var($coupon['discount_text'], FILTER_SANITIZE_NUMBER_INT);
        //     })
        //     ->first();

        return response()->json([
            'status' => true,
            'data' => [
                'cart' => $cart,
                'available_coupons' => $couponList,
                // 'best_coupon' => $bestCoupon
            ]
        ]);
    }
    public function clearCart(Request $request)
    {
        $user = $request->user();
        if ($res = $this->checkCustomer($user)) return $res;

        $cart=Cart::where('user_id', $user->id)->first();
        $cart->delete();

        $warehouseId = $request->header('warehouse-id');

        if ($cart->warehouse_id != $warehouseId) {
            return response()->json([
                'status' => false,
                'message' => 'Cart belongs to different warehouse'
            ], 400);
        }

        return response()->json([
            'status' => true,
            'message' => 'Cart cleared'
        ]);
    }

    public function removeSingleItem(Request $request)
    {
        $user = $request->user();
        if ($res = $this->checkCustomer($user)) return $res;

        $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);
        $cart = Cart::where('user_id', $user->id)->first();

        $cartItem = CartItem::where('cart_id', $cart->id)
            ->where('product_id', $request->product_id)
            ->first();
        // $cartItem = Cart::where('user_id', $user->id)
        //     ->where('product_id', $request->product_id)
        //     ->first();

        if (!$cartItem) {
            return response()->json([
                'status' => false,
                'message' => 'Selected product not found in cart'
            ], 404);
        }

        // FIXED qty
        if ($cartItem->qty > 1) {
            $cartItem->qty -= 1;
            $cartItem->save();
        } else {
            $cartItem->delete();
        }

        return response()->json([
            'status' => true,
            'message' => 'Selected product quantity reduced by 1',
            'data' => [
                'product_id' => $cartItem->product_id,
                'remaining_quantity' => $cartItem->quantity ?? 0
            ]
        ]);
    }
    public function checkout(Request $request)
    {
        $user = $request->user();
        if ($res = $this->checkCustomer($user)) return $res;

        $request->validate([
            'address_id' => 'required|exists:user_addresses,id',
            'payment_method' => 'required|in:cash,online'
        ]);

        $address = UserAddress::where('id', $request->address_id)
            ->where('user_id', $user->id)
            ->first();

        if (!$address) {
            return response()->json(['status' => false, 'message' => 'Invalid address'], 400);
        }

        $cart = Cart::with('items')->where('user_id', $user->id)->first();

        $warehouseId = $request->header('warehouse-id');

        if (!$warehouseId) {
            return response()->json([
                'status' => false,
                'message' => 'Warehouse not selected'
            ], 400);
        }

        if ($cart->warehouse_id != $warehouseId) {
            return response()->json([
                'status' => false,
                'message' => 'Your location changed. Please review your cart.'
            ], 400);
        }

        if (!$cart || $cart->items->isEmpty()) {
            return response()->json(['status' => false, 'message' => 'Cart is empty'], 400);
        }

        $subtotal = $cart->items->sum(fn($item) => $item->price * $item->qty);

        $taxTotal       = $cart->tax_total ?? 0;
        $couponDiscount = (float)($cart->discount ?? 0);
        $deliveryCharge = 0;

        $totalAmount = max(($subtotal + $taxTotal + $deliveryCharge) - $couponDiscount, 0);

        // ✅ Create Pending Order ONLY
        $order = Order::create([
            'user_id'         => $user->id,
            'order_number'    => 'ORD-' . time(),
            'subtotal'        => $subtotal,
            'tax_total'       => $taxTotal,
            'delivery_charge' => 0,
            'coupon_discount' => $couponDiscount,
            'coupon_code'     => $cart->coupon_code,
            'coupon_id'       => $cart->coupon_id,
            'total_amount'    => $totalAmount,
            'status'          => 'pending', // IMPORTANT
            'address_id'      => $address->id,
            'address_type'    => $address->type,
            'channel'         => 'app',
            'payment_method'  => $request->payment_method,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Order created, proceed to payment',
            'data' => [
                'order_id'     => $order->id,
                'order_number' => $order->order_number,
                'amount'       => round($totalAmount, 2)
            ]
        ]);
    }

    public function confirmOrder(Request $request)
    {
        DB::beginTransaction();

        try {
            $order = Order::findOrFail($request->order_id);

            // Already confirmed
            if ($order->status === 'confirmed') {
                return response()->json([
                    'status' => true,
                    'message' => 'Order already confirmed'
                ]);
            }

            // FIXED PAYMENT CHECK
            if (
                $order->payment_method === 'online' &&
                $order->payment_status !== 'paid'
            ) {
                return response()->json([
                    'status' => false,
                    'message' => 'Payment not completed'
                ], 400);
            }

            $warehouseId = $request->header('warehouse-id');

            if (!$warehouseId) {
                throw new \Exception('Warehouse not provided');
            }

            $cart = Cart::where('user_id', $order->user_id)->first();

            if ($cart->warehouse_id != $warehouseId) {
                throw new \Exception('Cart warehouse mismatch');
            }

            if (!$cart) {
                throw new \Exception('Cart not found');
            }

            $cartItems = CartItem::with('product')
                ->where('cart_id', $cart->id)
                ->get();

            if ($cartItems->isEmpty()) {
                throw new \Exception('Cart is empty');
            }

            foreach ($cartItems as $item) {

                //STOCK CHECK (WAREHOUSE BASED)
                $totalStock = WarehouseStock::where('product_id', $item->product_id)
                    ->where('warehouse_id', $warehouseId)
                    ->sum('quantity');

                if ($totalStock < $item->qty) {
                    throw new \Exception("Insufficient stock for product ID: {$item->product_id}");
                }

                // CREATE ORDER ITEM (INSIDE LOOP)
                OrderItem::create([
                    'order_id'   => $order->id,
                    'product_id' => $item->product_id,
                    'quantity'   => $item->qty,
                    'price'      => $item->price,
                    'total'      => $item->qty * $item->price
                ]);

                // FIFO STOCK DEDUCTION (WAREHOUSE BASED)
                $remainingQty = $item->qty;

                $stocks = WarehouseStock::where('product_id', $item->product_id)
                    ->where('warehouse_id', $warehouseId)
                    ->where('quantity', '>', 0)
                    ->orderBy('created_at')
                    ->lockForUpdate()
                    ->get();

                foreach ($stocks as $stock) {

                    if ($remainingQty <= 0) break;

                    if ($stock->quantity >= $remainingQty) {
                        $stock->quantity -= $remainingQty;
                        $stock->save();
                        $remainingQty = 0;
                    } else {
                        $remainingQty -= $stock->quantity;
                        $stock->quantity = 0;
                        $stock->save();
                    }
                }
            }

            CartItem::where('cart_id', $cart->id)->delete();
            $cart->delete();

            $order->update([
                'status' => 'confirmed'
            ]);

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Order confirmed successfully'
            ]);
        } catch (\Exception $e) {

            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => 'Order confirmation failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    protected function checkCustomer($user)
    {
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthenticated user'
            ], 401);
        }

        if (
            !$user->role ||
            strtolower($user->role->name) !== 'customer'
        ) {
            return response()->json([
                'status' => false,
                'message' => 'Only customers are allowed'
            ], 403);
        }

        return null; // ✅ allowed
    }

    public function returnProduct(Request $request)
    {
        $user = $request->user();
        if ($res = $this->checkCustomer($user)) return $res;

        $request->validate([
            'order_item_id'     => 'required|exists:order_items,id',
            'reason'            => 'required|string|max:191',
            'return_type'       => 'required|in:refund,exchange',
            'product_images'    => 'nullable|array',
            'product_images.*'  => 'image|mimes:jpg,jpeg,png',
        ]);

        // Fetch order item
        $orderItem = OrderItem::findOrFail($request->order_item_id);

        // Ensure this order belongs to logged-in customer
        if ($orderItem->order->user_id !== $user->id) {
            return response()->json([
                'status' => false,
                'message' => 'You cannot return this product'
            ], 403);
        }

        /* ================= Upload Return Images ================= */
        $imagePaths = [];

        if ($request->hasFile('product_images')) {
            foreach ($request->file('product_images') as $image) {

                $filename = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();

                $path = $image->storeAs(
                    'customer_return_products',
                    $filename,
                    'public'
                );

                $imagePaths[] = $path;
            }
        }

        /* ================= Insert Return Request ================= */
        $returnId = DB::table('customer_order_returns')->insertGetId([
            'order_id'        => $orderItem->order_id,
            'order_item_id'  => $orderItem->id,
            'product_id'     => $orderItem->product_id,
            'customer_id'    => $user->id,
            'quantity'       => $orderItem->quantity,
            'reason'         => $request->reason,
            'return_type'    => $request->return_type,   // refund | exchange
            'status'         => 'requested',
            'qc_status'      => 'pending',
            'product_images' => !empty($imagePaths) ? json_encode($imagePaths) : null,
            'created_at'     => now(),
            'updated_at'     => now(),
        ]);

        $returnData = DB::table('customer_order_returns')
            ->where('id', $returnId)
            ->first();

        return response()->json([
            'status' => true,
            'message' => 'Return request submitted successfully',
            'data' => $returnData
        ]);
    }
    public function pastOrders(Request $request)
    {
        $user = $request->user();
        if ($res = $this->checkCustomer($user)) return $res;

        $perPage = $request->per_page ?? 10;

        $orders = Order::with([
            'orderItems.product:id,name,product_images',
        ])
            ->where('user_id', $user->id)
            ->whereIn('status', ['delivered', 'cancelled'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        return response()->json([
            'status' => true,
            'data' => $orders
        ]);
    }
    public function newOrders(Request $request)
    {
        $user = $request->user();
        if ($res = $this->checkCustomer($user)) return $res;

        $orders = Order::with([
            'orderItems.product:id,name,product_images',
            'deliveryAddress'
        ])
            ->where('user_id', $user->id)
            ->whereNotIn('status', ['delivered', 'cancelled'])
            ->whereIn('payment_method', ['cash', 'online'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'status' => true,
            'data' => $orders
        ]);
    }
    public function rateOrder(Request $request, $orderId)
    {
        $user = $request->user();
        if ($res = $this->checkCustomer($user)) return $res;

        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'tags'   => 'nullable|array'
        ]);

        $order = Order::where('id', $orderId)
            ->where('user_id', $user->id)
            ->where('status', 'delivered')
            ->first();

        if (!$order) {
            return response()->json([
                'status' => false,
                'message' => 'Order not delivered yet'
            ], 400);
        }

        if ($order->customer_rating) {
            return response()->json([
                'status' => false,
                'message' => 'Rating already submitted'
            ], 400);
        }

        $order->update([
            'customer_rating' => $request->rating,
            'customer_rating_tags' => $request->tags
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Thank you for rating your order'
        ]);
    }
    public function incrementCart(Request $request)
    {
        $user = $request->user();
        if ($res = $this->checkCustomer($user)) return $res;

        $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        $product = Product::with('tax')->findOrFail($request->product_id);

        $warehouseId = $request->header('warehouse-id');

        $cart = Cart::where('user_id', $user->id)->first();

        if ($cart && $cart->warehouse_id != $warehouseId) {
            return response()->json([
                'status' => false,
                'message' => 'Cart belongs to different warehouse'
            ], 400);
        }

        $availableStock = WarehouseStock::where('product_id', $product->id)
            ->where('warehouse_id', $warehouseId)
            ->sum('quantity');

        if ($availableStock <= 0) {
            return response()->json([
                'status' => false,
                'message' => 'Product out of stock'
            ], 400);
        }

        $cart = Cart::firstOrCreate(['user_id' => $user->id]);

        $cartItem = CartItem::where('cart_id', $cart->id)
            ->where('product_id', $product->id)
            ->first();

        $newQty = ($cartItem?->qty ?? 0) + 1;

        if ($newQty > $availableStock) {
            return response()->json([
                'status' => false,
                'message' => 'Only ' . $availableStock . ' items available'
            ], 400);
        }

        // 🔹 Price & tax
        $price = $product->retailer_price;
        $cgst = $product->tax?->cgst ?? 0;
        $sgst = $product->tax?->sgst ?? 0;

        $cgstAmount = ($price * $cgst / 100) * $newQty;
        $sgstAmount = ($price * $sgst / 100) * $newQty;
        $taxTotal   = $cgstAmount + $sgstAmount;
        $itemTotal = ($price * $newQty) + $taxTotal;

        CartItem::updateOrCreate(
            [
                'cart_id' => $cart->id,
                'product_id' => $product->id
            ],
            [
                'qty' => $newQty,
                'price' => $price,
                'cgst_amount' => $cgstAmount,
                'sgst_amount' => $sgstAmount,
                'tax_total' => $taxTotal,
                'item_total' => $itemTotal
            ]
        );

        $this->recalculateCart($cart->id);

        return response()->json([
            'status' => true,
            'message' => 'Product quantity increased',
            'qty' => $newQty
        ]);
    }
    public function decrementCart(Request $request)
    {
        $user = $request->user();
        if ($res = $this->checkCustomer($user)) return $res;

        $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        $cart = Cart::where('user_id', $user->id)->first();
        $warehouseId = $request->header('warehouse-id');

        if ($cart->warehouse_id != $warehouseId) {
            return response()->json([
                'status' => false,
                'message' => 'Cart belongs to different warehouse'
            ], 400);
        }
        if (!$cart) {
            return response()->json([
                'status' => false,
                'message' => 'Cart empty'
            ], 404);
        }

        $cartItem = CartItem::where('cart_id', $cart->id)
            ->where('product_id', $request->product_id)
            ->first();

        if (!$cartItem) {
            return response()->json([
                'status' => false,
                'message' => 'Product not in cart'
            ], 404);
        }

        if ($cartItem->qty > 1) {
            $cartItem->qty -= 1;
            $cartItem->save();
        } else {
            $cartItem->delete();
        }

        $this->recalculateCart($cart->id);

        return response()->json([
            'status' => true,
            'message' => 'Product quantity decreased',
            'remaining_qty' => $cartItem->qty ?? 0
        ]);
    }
    public function removeFromCart(Request $request)
    {
        $user = $request->user();
        if ($res = $this->checkCustomer($user)) return $res;

        $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        $cart = Cart::where('user_id', $user->id)->first();

        CartItem::where('cart_id', $cart?->id)
            ->where('product_id', $request->product_id)
            ->delete();

        $this->recalculateCart($cart->id);

        return response()->json([
            'status' => true,
            'message' => 'Product removed from cart'
        ]);
    }
    private function recalculateCart($cartId)
    {
        $cart = Cart::with('items')->find($cartId);
        if (!$cart) return;

        // Use the items already loaded to avoid extra queries
        $subtotal = $cart->items->sum(function ($item) {
            return $item->price * $item->qty;
        });

        $taxTotal = $cart->items->sum('tax_total');

        // Check if a coupon is applied (logic for later)
        $discount = $cart->discount ?? 0;

        $cart->update([
            'subtotal'  => $subtotal,
            'tax_total' => $taxTotal,
            'total'     => max(($subtotal + $taxTotal) - $discount, 0)
        ]);
    }

 public function search(Request $request)
{
    $search = trim($request->query('search'));
    $warehouseId = $request->header('warehouse-id');

    if (!$warehouseId) {
        return response()->json([
            'status' => false,
            'message' => 'Warehouse not selected'
        ], 400);
    }

    $products = Product::select('products.*')

        // ✅ STOCK SUBQUERY
        ->selectSub(function ($query) use ($warehouseId) {
            $query->from('warehouse_stock')
                ->selectRaw('COALESCE(SUM(quantity),0)')
                ->whereColumn('warehouse_stock.product_id', 'products.id')
                ->where('warehouse_stock.warehouse_id', $warehouseId);
        }, 'stock')

        ->when($search, function ($q) use ($search) {
            $q->where('products.name', 'LIKE', '%' . $search . '%');
        })

        // 🔥 SORTING (BEST PRACTICE)
        ->orderByRaw('stock > 0 DESC') // in-stock first
        ->orderByDesc('stock')         // higher stock first

        ->paginate(10);

    // ✅ FORMAT RESPONSE
    $products->getCollection()->transform(function ($product) {

        $images = is_string($product->product_images)
            ? json_decode($product->product_images, true)
            : ($product->product_images ?? []);

        $discountPercent = 0;
        if ($product->mrp > 0 && $product->final_price < $product->mrp) {
            $discountPercent = round(
                (($product->mrp - $product->final_price) / $product->mrp) * 100
            );
        }

        return [
            'id' => $product->id,
            'name' => $product->name,

            'mrp' => (float) $product->mrp,
            'final_price' => (float) $product->final_price,

            'discount_percentage' => $discountPercent,
            'discount_label' => $discountPercent > 0
                ? $discountPercent . '% OFF'
                : null,

            'stock' => (int) $product->stock,
            'in_stock' => $product->stock > 0,

            'image_urls' => collect($images)->map(
                fn($img) => asset('storage/products/' . $img)
            )->values(),
        ];
    });

    return response()->json([
        'status' => true,
        'count'  => $products->total(),
        'data'   => $products
    ]);
}
    public function addBrandProductToCart(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'qty'        => 'nullable|integer|min:1'
        ]);

        $userId = auth()->id();
        $qty = $request->qty ?? 1;

        $product = Product::findOrFail($request->product_id);

        // 1️⃣ Get or create cart
        $cart = Cart::firstOrCreate(
            ['user_id' => $userId],
            ['total' => 0]
        );

        // 2️⃣ Find product in cart
        $item = CartItem::where('cart_id', $cart->id)
            ->where('product_id', $product->id)
            ->first();

        if ($item) {
            // Increment qty
            $item->qty += $qty;
        } else {
            // Create new row
            $item = new CartItem();
            $item->cart_id   = $cart->id;
            $item->product_id = $product->id;
            $item->qty       = $qty;
            $item->price     = $product->final_price;
        }

        // 3️⃣ Calculations
        $item->line_total = $item->qty * $item->price;
        $item->tax_total  = 0; // GST already included
        $item->item_total = $item->line_total;
        $item->save();

        // 4️⃣ Update cart totals
        $cart->subtotal = CartItem::where('cart_id', $cart->id)->sum('line_total');
        $cart->total    = $cart->subtotal - ($cart->discount ?? 0);
        $cart->save();

        return response()->json([
            'status'  => true,
            'message' => 'Product added to cart',
            'cart_id' => $cart->id
        ]);
    }
    public function incrementCartItem(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        $userId = auth()->id();

        $cart = Cart::where('user_id', $userId)->first();
        if (!$cart) {
            return response()->json([
                'status' => false,
                'message' => 'Cart not found'
            ]);
        }

        $item = CartItem::where('cart_id', $cart->id)
            ->where('product_id', $request->product_id)
            ->first();

        if (!$item) {
            return response()->json([
                'status' => false,
                'message' => 'Item not found in cart'
            ]);
        }

        // ➕ Increment
        $item->qty += 1;
        $item->line_total = $item->qty * $item->price;
        $item->item_total = $item->line_total;
        $item->save();

        // Update cart totals
        $cart->subtotal = CartItem::where('cart_id', $cart->id)->sum('line_total');
        $cart->total    = $cart->subtotal - ($cart->discount ?? 0);
        $cart->save();

        return response()->json([
            'status' => true,
            'message' => 'Quantity incremented'
        ]);
    }
    public function decrementCartItem(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        $userId = auth()->id();

        $cart = Cart::where('user_id', $userId)->first();
        if (!$cart) {
            return response()->json([
                'status' => false,
                'message' => 'Cart not found'
            ]);
        }

        $item = CartItem::where('cart_id', $cart->id)
            ->where('product_id', $request->product_id)
            ->first();

        if (!$item) {
            return response()->json([
                'status' => false,
                'message' => 'Item not found in cart'
            ]);
        }

        // ➖ Decrement
        $item->qty -= 1;

        if ($item->qty <= 0) {
            // Remove item completely
            $item->delete();
        } else {
            $item->line_total = $item->qty * $item->price;
            $item->item_total = $item->line_total;
            $item->save();
        }

        // Update cart totals
        $cart->subtotal = CartItem::where('cart_id', $cart->id)->sum('line_total');
        $cart->total    = $cart->subtotal - ($cart->discount ?? 0);
        $cart->save();

        return response()->json([
            'status' => true,
            'message' => 'Quantity decremented'
        ]);
    }
}
