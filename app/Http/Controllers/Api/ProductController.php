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
use App\Models\Role;

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
            'status' => true,
            'message' => 'Product added to cart'
        ]);
    }

    public function viewCart(Request $request)
    {
        $user = $request->user();
        if ($res = $this->checkCustomer($user)) return $res;

        $cartItems = Cart::with('product')
            ->where('user_id', $user->id)
            ->get();

        return response()->json([
            'status' => true,
            'data' => $cartItems
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

        // Validate ke user ne product choose keli aahe ki nahi
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

        $cartItems = Cart::with('product')
            ->where('user_id', $user->id)
            ->get();

        if ($cartItems->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => 'Cart is empty'
            ], 400);
        }

        // Subtotal
        $subtotal = 0;
        foreach ($cartItems as $item) {
            $price = $item->product->retailer_price ?? 0;
            $subtotal += $price * $item->quantity;
        }

        $deliveryCharge = 50;
        $couponDiscount = 0;
        $offerId = null;
        $couponCode = null;

        // Coupon Apply
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

            $couponDiscount = $offer->discount_type === 'flat'
                ? $offer->discount_value
                : ($subtotal * $offer->discount_value) / 100;

            $offerId = $offer->id;
            $couponCode = $offer->code;
        }

        $totalAmount = max(($subtotal + $deliveryCharge - $couponDiscount), 0);

        // Create Order
        $order = Order::create([
            'user_id' => $user->id,
            'order_number' => 'ORD-' . time(),
            'subtotal' => $subtotal,
            'delivery_charge' => $deliveryCharge,
            'coupon_discount' => $couponDiscount,
            'total_amount' => $totalAmount,
            'status' => 'pending',
            'offer_id' => $offerId,
            'coupon_code' => $couponCode
        ]);

        // Order Items
        foreach ($cartItems as $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $item->product_id,
                'quantity' => $item->quantity,
                'price' => $item->product->retailer_price ?? 0,
                'total' => ($item->product->retailer_price ?? 0) * $item->quantity
            ]);
        }

        // Clear Cart
        Cart::where('user_id', $user->id)->delete();

        return response()->json([
            'status' => true,
            'message' => 'Order placed successfully',
            'data' => [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'subtotal' => $subtotal,
                'delivery_charge' => $deliveryCharge,
                'coupon_discount' => $couponDiscount,
                'total_amount' => $totalAmount
            ]
        ]);
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
}
