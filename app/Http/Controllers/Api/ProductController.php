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

class ProductController extends Controller
{

    public function addToCart(Request $request)
    {
        $user = $request->user();
        if ($res = $this->checkCustomer($user)) return $res;

        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity'   => 'nullable|integer|min:1',
        ]);

        $qty = $request->quantity ?? 1;
        $product = Product::with('tax')->findOrFail($request->product_id);

        // 🔹 REAL STOCK
        $availableStock = WarehouseStock::where('product_id', $product->id)
            ->sum('quantity');

        if ($availableStock <= 0) {
            return response()->json([
                'status' => false,
                'message' => 'Product out of stock'
            ], 400);
        }

        // 🔹 Get or create cart
        $cart = Cart::firstOrCreate(
            ['user_id' => $user->id],
            [
                'subtotal'  => 0,
                'tax_total' => 0,
                'discount'  => 0,
                'total'     => 0
            ]
        );

        // 🔹 Existing cart item
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

        // 🔹 RECALCULATE CART TOTALS
        $subtotal = CartItem::where('cart_id', $cart->id)
            ->sum(DB::raw('price * qty'));

        $taxTotal = CartItem::where('cart_id', $cart->id)
            ->sum('tax_total');

        $cart->update([
            'subtotal'  => $subtotal,
            'tax_total' => $taxTotal,
            'discount'  => 0,
            'total'     => $subtotal + $taxTotal
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Product added to cart successfully'
        ]);
    }

    public function viewCart(Request $request)
    {
        $user = $request->user();
        if ($res = $this->checkCustomer($user)) return $res;

        $cart = Cart::with([
            'items.product.tax'
        ])->where('user_id', $user->id)->first();

        return response()->json([
            'status' => true,
            'data'   => $cart
        ]);
    }

    public function clearCart(Request $request)
    {
        $user = $request->user();
        if ($res = $this->checkCustomer($user)) return $res;

        Cart::where('user_id', $user->id)->delete();

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

        $cartItem = Cart::where('user_id', $user->id)
            ->where('product_id', $request->product_id)
            ->first();

        if (!$cartItem) {
            return response()->json([
                'status' => false,
                'message' => 'Selected product not found in cart'
            ], 404);
        }

        // 🔹 1 by 1 quantity remove
        if ($cartItem->quantity > 1) {
            $cartItem->quantity -= 1;
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

        // 🔐 Validate address
        $request->validate([
            'address_id' => 'required|exists:user_addresses,id'
        ]);

        // 🔎 Fetch address (must belong to user)
        $address = UserAddress::where('id', $request->address_id)
            ->where('user_id', $user->id)
            ->first();

        if (!$address) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid address'
            ], 400);
        }

        // 🚫 Example rule: Work address not allowed after 8 PM
        if ($address->type == 2 && now()->hour >= 20) {
            return response()->json([
                'status' => false,
                'message' => 'Work address delivery not available after 8 PM'
            ], 400);
        }

        DB::beginTransaction();

        try {

            // 🔹 Get cart
            $cart = Cart::where('user_id', $user->id)->first();

            if (!$cart) {
                return response()->json([
                    'status' => false,
                    'message' => 'Cart is empty'
                ], 400);
            }

            $cartItems = CartItem::with('product')
                ->where('cart_id', $cart->id)
                ->get();

            if ($cartItems->isEmpty()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Cart is empty'
                ], 400);
            }

            // 🔹 Subtotal
            $subtotal = 0;
            foreach ($cartItems as $item) {
                $subtotal += ($item->price * $item->qty);
            }

            // 🔹 Delivery & coupon
            $deliveryCharge = 50;
            $couponDiscount = 0;
            $offerId = null;
            $couponCode = null;

            // 🔹 APPLY OFFER
            if ($request->filled('coupon_code')) {

                $offer = Offer::where('code', $request->coupon_code)
                    ->where('status', 1)
                    ->whereDate('start_date', '<=', now())
                    ->whereDate('end_date', '>=', now())
                    ->first();

                if (!$offer) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Invalid coupon'
                    ], 400);
                }

                if ($subtotal < $offer->min_amount) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Minimum order amount should be ₹' . $offer->min_amount
                    ], 400);
                }


                // 💸 Discount calculation
                if ($offer->discount_type === 'flat') {

                    // Flat ₹ discount
                    $couponDiscount = $offer->discount_value;
                } elseif ($offer->discount_type === 'percentage') {

                    // % discount
                    $couponDiscount = ($subtotal * $offer->discount_value) / 100;
                }


                // 🛑 Discount cannot exceed subtotal
                $couponDiscount = min($couponDiscount, $subtotal);

                $offerId = $offer->id;
                $couponCode = $offer->title;
            }

            // 🔹 Total
            $totalAmount = ($subtotal + $deliveryCharge) - $couponDiscount;

            // 🔹 STOCK CHECK
            foreach ($cartItems as $item) {

                $availableStock = (int) WarehouseStock::where('product_id', $item->product_id)
                    ->sum('quantity');

                if ($item->qty > $availableStock) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Insufficient stock for ' . $item->product->name
                    ], 400);
                }
            }

            // 🔹 Create Order
            $order = Order::create([
                'user_id'       => $user->id,
                'order_number'  => 'ORD-' . time(),
                'subtotal'      => $subtotal,
                'delivery_charge' => $deliveryCharge,
                'coupon_discount' => $couponDiscount,
                'total_amount'  => $totalAmount,
                'status'        => 'pending',
                'offer_id'      => $offerId,
                'coupon_code'   => $couponCode,
                'address_id'    => $address->id,
                'address_type'  => $address->type
            ]);

            // 🔹 Order Items + FIFO stock deduction
            foreach ($cartItems as $item) {

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'quantity' => $item->qty,
                    'price' => $item->price,
                    'total' => $item->qty * $item->price
                ]);

                $remainingQty = $item->qty;

                $stocks = WarehouseStock::where('product_id', $item->product_id)
                    ->where('quantity', '>', 0)
                    ->orderBy('created_at')
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

            // 🔹 Clear cart
            CartItem::where('cart_id', $cart->id)->delete();
            $cart->delete();

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Order placed successfully',
                'data' => [
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
                    'subtotal' => $subtotal,
                    'delivery_charge' => $deliveryCharge,
                    'coupon_discount' => $couponDiscount,
                    'total_amount' => $totalAmount,
                    'address_type' => $address->type
                ]
            ]);
        } catch (\Exception $e) {

            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => 'Checkout failed',
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

        // 🔹 Stock check
        $availableStock = WarehouseStock::where('product_id', $product->id)->sum('quantity');

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
        $subtotal = CartItem::where('cart_id', $cartId)
            ->sum(DB::raw('price * qty'));

        $taxTotal = CartItem::where('cart_id', $cartId)
            ->sum('tax_total');

        Cart::where('id', $cartId)->update([
            'subtotal' => $subtotal,
            'tax_total' => $taxTotal,
            'discount' => 0,
            'total' => $subtotal + $taxTotal
        ]);
    }
}
