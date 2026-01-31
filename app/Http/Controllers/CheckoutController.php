<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Cart;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use App\Models\UserAddress;
use Illuminate\Support\Facades\Log;
use Razorpay\Api\Api;
use App\Models\Payment;

class CheckoutController extends Controller
{
    public function index()
    {
        $userId = Auth::id();

        $cart = Cart::with('items.product')
            ->where('user_id', $userId)
            ->first();

        if (!$cart || $cart->items->isEmpty()) {
            return redirect()->route('cart')
                ->with('error', 'Your cart is empty');
        }

        $address = UserAddress::where('user_id', $userId)->first();

        $coupons = Coupon::where('status', 1)
            ->whereDate('start_date', '<=', now())
            ->whereDate('end_date', '>=', now())
            ->get();

        return view('website.checkout', compact('cart', 'address', 'coupons'));
    }

    public function placeOrder(Request $request)
    {
        try {

            $request->validate([
                'first_name' => 'required',
                'address'    => 'required',
                'city'       => 'required',
                'country'    => 'required',
                'postcode'   => 'required',
                'phone'      => 'required',
                'email'      => 'required|email',
                'payment_method' => 'required',
            ]);

            // Save or update user address
            UserAddress::updateOrCreate(
                ['user_id' => auth()->id(), 'type' => 1],
                $request->only([
                    'first_name',
                    'last_name',
                    'address',
                    'city',
                    'country',
                    'postcode',
                    'phone',
                    'email'
                ]) + ['type' => 1]
            );

            $cart = Cart::where('user_id', auth()->id())
                ->with('items.product')
                ->first();

            if (!$cart || $cart->items->isEmpty()) {
                return redirect()->route('cart')->with('error', 'Cart empty');
            }

            // Coupon calculation
            $couponDiscount = 0;
            $couponCode = null;

            if ($request->coupon_code) {
                $coupon = Coupon::where('code', $request->coupon_code)
                    ->where('status', 1)
                    ->whereDate('start_date', '<=', now())
                    ->whereDate('end_date', '>=', now())
                    ->where('min_amount', '<=', $cart->subtotal)
                    ->first();

                if ($coupon) {
                    if ($coupon->discount_type === 'percentage') {
                        $couponDiscount = ($cart->subtotal * $coupon->discount_value) / 100;
                    } else {
                        $couponDiscount = $coupon->discount_value;
                    }

                    if ($couponDiscount > $cart->subtotal) {
                        $couponDiscount = $cart->subtotal;
                    }

                    $couponCode = $coupon->code;
                }
            }

            $finalTotal = $cart->subtotal - $couponDiscount;

            // Create Order
            $order = Order::create([
                'user_id'          => auth()->id(),
                'warehouse_id'     => 0,
                'order_number'     => 'ORD-' . time(),
                'channel'          => 'web',
                'subtotal'         => $cart->subtotal,
                'discount'         => $couponDiscount,
                'coupon_discount'  => $couponDiscount,
                'coupon_code'      => $couponCode,
                'delivery_charge'  => 0,
                'total_amount'     => $finalTotal,
                'payment_method'   => $request->payment_method,
                'payment_status'   => 'pending', // ✅ FIX
                'status'           => 'pending',
                'order_type'       => 'delivery',
                'razorpay_order_id' => $request->razorpay_order_id ?? null,
            ]);


            Payment::create([
                'order_id' => $order->id,
                'user_id'  => auth()->id(),
                'payment_gateway' => $request->payment_method === 'online'
                    ? 'razorpay'
                    : 'cash',
                'razorpay_order_id' => $request->razorpay_order_id ?? null,
                'amount' => $order->total_amount,
                'status' => 'pending',
                'meta' => json_encode([
                    'order_number' => $order->order_number
                ])
            ]);


            // Create Order Items
            foreach ($cart->items as $item) {
                OrderItem::create([
                    'order_id'   => $order->id,
                    'product_id' => $item->product_id,
                    'quantity'   => $item->qty,
                    'price'      => $item->price,
                    'line_total' => $item->line_total,
                    'total'      => $item->line_total,
                ]);
            }

            // Clear cart
            $cart->items()->delete();
            $cart->delete();

            return redirect()->route('my_orders')
                ->with('success', 'Order placed successfully!');
        } catch (\Exception $e) {
            Log::error('Order Error', ['error' => $e->getMessage()]);
            return back()->with('error', 'Something went wrong');
        }
    }

    public function thankYou(Order $order)
    {
        // Security: only owner can see
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        return view('website.thank-you', compact('order'));
    }


    public function applyCoupon(Request $request)
    {
        $coupon = Coupon::where('code', $request->coupon_code)
            ->where('status', 1)
            ->whereDate('start_date', '<=', now())
            ->whereDate('end_date', '>=', now())
            ->first();

        if (!$coupon) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid or expired coupon'
            ]);
        }

        if ($request->subtotal < $coupon->min_amount) {
            return response()->json([
                'status' => false,
                'message' => 'Minimum order ₹' . $coupon->min_amount . ' required'
            ]);
        }

        $discount = $coupon->discount_type === 'percentage'
            ? ($request->subtotal * $coupon->discount_value) / 100
            : $coupon->discount_value;

        if ($discount > $request->subtotal) $discount = $request->subtotal;

        $finalTotal = $request->subtotal - $discount;

        return response()->json([
            'status' => true,
            'discount' => number_format($discount, 2),
            'final_total' => number_format($finalTotal, 2)
        ]);
    }
}
