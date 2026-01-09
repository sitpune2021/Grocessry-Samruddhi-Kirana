<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use Throwable;
use App\Models\Offer;

class ProductController extends Controller
{
    // Add product to cart
    public function addToCart(Request $request)
    {
        try {
            $user = $request->user();
            if (!$user) {
                return response()->json([
                    'status' => false,
                    'message' => 'unauthenticated user'
                ], 401);
            }

            $request->validate([
                'product_id' => 'required|exists:products,id',
                'quantity'   => 'nullable|integer|min:1',
            ]);

            $cart = Cart::where('user_id', $user->id)
                ->where('product_id', $request->product_id)
                ->first();

            if ($cart) {
                $cart->quantity += $request->quantity ?? 1;
                $cart->save();
            } else {
                Cart::create([
                    'user_id'    => $user->id,
                    'product_id' => $request->product_id,
                    'quantity'   => $request->quantity ?? 1,
                ]);
            }

            return response()->json([
                'status'  => true,
                'message' => 'Product added to cart'
            ], 200);
        } catch (ValidationException $e) {
            return response()->json([
                'status'  => false,
                'message' => $e->errors()
            ], 422);
        } catch (Throwable $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Something went wrong',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    // View cart
    public function viewCart(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'unauthenticated user'
            ], 401);
        }

        $cartItems = Cart::with('product')
            ->where('user_id', $user->id)
            ->get();

        return response()->json([
            'status' => true,
            'data'   => $cartItems
        ], 200);
    }

    // Clear all cart items
    public function clearCart(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'unauthenticated user'
            ], 401);
        }

        Cart::where('user_id', $user->id)->delete();

        return response()->json([
            'status' => true,
            'message' => 'Cart cleared'
        ], 200);
    }

    public function removeSingleItem(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'unauthenticated user'
            ], 401);
        }

        $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        $cartItem = Cart::where('user_id', $user->id)
            ->where('product_id', $request->product_id)
            ->first();

        if (!$cartItem) {
            return response()->json([
                'status' => false,
                'message' => 'Product not found in cart'
            ], 404);
        }

        $cartItem->delete();

        return response()->json([
            'status' => true,
            'message' => 'Product removed from cart'
        ], 200);
    }
    public function checkout(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'unauthenticated'
            ], 401);
        }

        $cartItems = Cart::with('product')
            ->where('user_id', $user->id)
            ->get();

        if ($cartItems->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => 'Cart is empty'
            ], 400);
        }

        // 🧮 Subtotal calculation
        $subtotal = 0;
        foreach ($cartItems as $item) {
            $price = $item->product->retailer_price ?? 0;
            $subtotal += $price * $item->quantity;
        }

        $deliveryCharge = 50;
        $couponDiscount = 0;
        $offerId = null;
        $couponCode = null;

        // 🎟 APPLY COUPON IF PROVIDED
        if ($request->coupon_code) {
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
                    'message' => 'Minimum order amount not met'
                ], 400);
            }

            // 💰 Discount logic
            if ($offer->discount_type === 'flat') {
                $couponDiscount = $offer->discount_value;
            } else {
                $couponDiscount = ($subtotal * $offer->discount_value) / 100;
            }

            $offerId = $offer->id;
            $couponCode = $offer->code;
        }

        $totalAmount = max(($subtotal + $deliveryCharge - $couponDiscount), 0);

        // 🧾 CREATE ORDER
        $order = Order::create([
            'user_id'         => $user->id,
            'order_number'    => 'ORD-' . time(),
            'subtotal'        => $subtotal,
            'delivery_charge' => $deliveryCharge,
            'discount'        => 0,
            'coupon_discount' => $couponDiscount,
            'total_amount'    => $totalAmount,
            'status'          => 'pending',
            'offer_id'        => $offerId,
            'coupon_code'     => $couponCode
        ]);

        // 🧺 ORDER ITEMS
        foreach ($cartItems as $item) {
            OrderItem::create([
                'order_id'  => $order->id,
                'product_id' => $item->product_id,
                'quantity'  => $item->quantity,
                'price'     => $item->product->retailer_price ?? 0,
                'total'     => ($item->product->retailer_price ?? 0) * $item->quantity
            ]);
        }

        // 🧹 CLEAR CART
        Cart::where('user_id', $user->id)->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Order placed successfully',
            'data' => [
                'order_id'        => $order->id,
                'order_number'   => $order->order_number,
                'subtotal'       => $subtotal,
                'delivery_charge' => $deliveryCharge,
                'coupon_discount' => $couponDiscount,
                'total_amount'   => $totalAmount
            ]
        ], 200);
    }

    // public function checkout(Request $request)
    // {
    //     $user = $request->user();
    //     if (!$user) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'unauthenticated'
    //         ], 401);
    //     }

    //     $cartItems = Cart::with('product')
    //         ->where('user_id', $user->id)
    //         ->get();

    //     if ($cartItems->isEmpty()) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'Cart is empty'
    //         ], 400);
    //     }

    //     // 🔢 Calculate totals
    //     $subtotal = 0;
    //     foreach ($cartItems as $item) {
    //         $price = $item->product->base_price ?? 0; // use correct price column
    //         $subtotal += $price * $item->quantity;
    //     }

    //     $deliveryCharge = 50;
    //     $discount = 0;
    //     $totalAmount = $subtotal + $deliveryCharge - $discount;

    //     // 📦 Create Order
    //     $order = Order::create([
    //         'user_id' => $user->id,
    //         'order_number' => 'ORD-' . time(),
    //         'subtotal' => $subtotal,
    //         'delivery_charge' => $deliveryCharge,
    //         'discount' => $discount,
    //         'total_amount' => $totalAmount,
    //         'status' => 'pending'
    //     ]);

    //     // 🧾 Order items + response items
    //     $itemsResponse = [];

    //     foreach ($cartItems as $item) {
    //         $price = $item->product->base_price ?? 0;

    //         OrderItem::create([
    //             'order_id' => $order->id,
    //             'product_id' => $item->product_id,
    //             'quantity' => $item->quantity,
    //             'price' => $price,
    //             'total' => $price * $item->quantity
    //         ]);

    //         // LIMITED response item
    //         $itemsResponse[] = [
    //             'product_id'   => $item->product_id,
    //             'product_name' => $item->product->name ?? '',
    //             'quantity'     => $item->quantity,
    //             'price'        => (float) $price,
    //             'total'        => (float) ($price * $item->quantity),
    //         ];
    //     }

    //     // 🗑 Clear cart
    //     Cart::where('user_id', $user->id)->delete();

    //     // ✅ FINAL CLEAN RESPONSE
    //     return response()->json([
    //         'status'  => true,
    //         'message' => 'Order placed successfully',
    //         'data'    => [
    //             'order_id'        => $order->id,
    //             'order_number'    => $order->order_number,
    //             'subtotal'        => (float) $subtotal,
    //             'delivery_charge' => (float) $deliveryCharge,
    //             'discount'        => (float) $discount,
    //             'total_amount'    => (float) $totalAmount,
    //             'items'           => $itemsResponse
    //         ]
    //     ], 200);
    // }
}
