<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Cart;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Models\UserAddress;
use Illuminate\Support\Facades\Log;

class CheckoutController extends Controller
{

    public function index()
    {
        $userId = Auth::id();

        $cart = Cart::with('items.product')
            ->where('user_id', $userId)
            ->first();

        // last saved address
        $address = UserAddress::where('user_id', $userId)->first();

        return view('website.checkout', compact('cart', 'address'));
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
                ->with('items')
                ->first();

            if (!$cart || $cart->items->isEmpty()) {
                return redirect()->route('cart')->with('error', 'Cart empty');
            }

            // 🔥 SERVER SIDE COUPON
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
                    if ($coupon->discount_type == 'percentage') {
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

            $totalAmount = $cart->subtotal - $couponDiscount;

            $order = Order::create([
                'user_id' => auth()->id(),
                'order_number' => 'ORD-' . time(),
                'subtotal' => $cart->subtotal,
                'coupon_discount' => $couponDiscount,
                'coupon_code' => $couponCode,
                'total_amount' => $totalAmount,
                'payment_method' => $request->payment_method,
                'status' => 'pending',
            ]);

            foreach ($cart->items as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'quantity' => $item->qty,
                    'price' => $item->price,
                    'line_total' => $item->line_total,
                ]);
            }

            $cart->items()->delete();
            $cart->delete();

            return redirect()->route('my_orders')
                ->with('success', 'Order placed successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Something went wrong');
        }
    }
}
