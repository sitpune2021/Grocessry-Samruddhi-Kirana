<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Cart;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Models\UserAddress;
use Illuminate\Support\Facades\Log;
use Razorpay\Api\Api;
use Illuminate\Support\Facades\DB;


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



    public function createOrder(Request $request)
    {
        $api = new Api(
            config('services.razorpay.key'),
            config('services.razorpay.secret')
        );

        $orderData = [
            'receipt'         => 'order_rcptid_' . time(), // better unique
            'amount'          => (int) ($request->amount * 100), // ✅ paise
            'currency'        => 'INR',
            'payment_capture' => 1
        ];

        $razorpayOrder = $api->order->create($orderData);

        return response()->json([
            'order_id' => $razorpayOrder['id'],
            'amount'   => $razorpayOrder['amount'],
            'currency' => $razorpayOrder['currency'],
            'key'      => config('services.razorpay.key')
        ]);
    }



    // =============================exit code========================



    public function placeOrder(Request $request)
    {
        DB::beginTransaction();

        try {

            /* ================= VALIDATION ================= */
            $request->validate([
                'first_name'      => 'required',
                'address'         => 'required',
                'city'            => 'required',
                'country'         => 'required',
                'postcode'        => 'required',
                'phone'           => 'required',
                'email'           => 'required|email',
                'payment_method'  => 'required|in:Cash,online',
            ]);

            /* ================= ADDRESS ================= */
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

            /* ================= CART ================= */
            $cart = Cart::where('user_id', auth()->id())
                ->with('items.product')
                ->first();

            if (!$cart || $cart->items->isEmpty()) {
                return redirect()->route('cart')->with('error', 'Cart empty');
            }

            /* ================= COUPON ================= */
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
                    $couponDiscount = $coupon->discount_type === 'percentage'
                        ? ($cart->subtotal * $coupon->discount_value) / 100
                        : $coupon->discount_value;

                    $couponDiscount = min($couponDiscount, $cart->subtotal);
                    $couponCode = $coupon->code;
                }
            }

            $finalTotal = $cart->subtotal - $couponDiscount;

            /* ================= ORDER ================= */
            $order = Order::create([
                'user_id'         => auth()->id(),
                'warehouse_id'    => 0,
                'order_number'    => 'ORD-' . time(),
                'channel'         => 'web',
                'subtotal'        => $cart->subtotal,
                'discount'        => $couponDiscount,
                'coupon_discount' => $couponDiscount,
                'coupon_code'     => $couponCode,
                'delivery_charge' => 0,
                'total_amount'    => $finalTotal,
                'payment_method'  => $request->payment_method,
                'payment_status'  => 'pending',
                'status'          => 'pending',
                'order_type'      => 'delivery',
            ]);

            /* ================= ORDER ITEMS ================= */
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

            /* ================= ONLINE PAYMENT ================= */
            if ($request->payment_method === 'online') {

                if (
                    !$request->razorpay_payment_id ||
                    !$request->razorpay_signature ||
                    !$request->razorpay_order_id
                ) {
                    return back()->with('error', 'Payment not completed');
                }

                // Duplicate protection
                if ($order->payment_status === 'paid') {
                    return redirect()->route('my_orders')
                        ->with('success', 'Order already paid');
                }

                $api = new Api(
                    config('services.razorpay.key'),
                    config('services.razorpay.secret')
                );

                try {
                    $api->utility->verifyPaymentSignature([
                        'razorpay_order_id'   => $request->razorpay_order_id,
                        'razorpay_payment_id' => $request->razorpay_payment_id,
                        'razorpay_signature'  => $request->razorpay_signature,
                    ]);

                    Payment::create([
                        'order_id'        => $order->id,
                        'user_id'         => auth()->id(),
                        'payment_gateway' => 'razorpay',
                        'payment_id'      => $request->razorpay_payment_id,
                        'amount'          => $order->total_amount,
                        'status'          => 'success',
                        'meta'            => json_encode($request->all()),
                    ]);

                    $order->update([
                        'payment_status'   => 'paid',
                        'status'           => 'processing',
                        'razorpay_order_id' => $request->razorpay_order_id,
                    ]);
                } catch (\Exception $e) {

                    Payment::create([
                        'order_id'        => $order->id,
                        'user_id'         => auth()->id(),
                        'payment_gateway' => 'razorpay',
                        'payment_id'      => $request->razorpay_payment_id,
                        'amount'          => $order->total_amount,
                        'status'          => 'failed',
                        'meta'            => $e->getMessage(),
                    ]);

                    $order->update([
                        'payment_status' => 'failed',
                        'status' => 'failed'
                    ]);

                    DB::rollBack();
                    return back()->with('error', 'Payment verification failed');
                }
            }

            /* ================= CLEAR CART ================= */
            $cart->items()->delete();
            $cart->delete();

            DB::commit();

            return redirect()->route('my_orders')
                ->with('success', 'Order placed successfully!');
        } catch (\Exception $e) {

            DB::rollBack();
            Log::error('Checkout Error', ['error' => $e->getMessage()]);

            return back()->with('error', 'Something went wrong');
        }
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

        // Minimum order validation (₹1000 etc.)
        if ($request->subtotal < $coupon->min_amount) {
            return response()->json([
                'status' => false,
                'message' => 'Minimum order ₹' . $coupon->min_amount . ' required'
            ]);
        }

        // Discount calculation
        if ($coupon->discount_type === 'percentage') {
            $discount = ($request->subtotal * $coupon->discount_value) / 100;
        } else {
            $discount = $coupon->discount_value;
        }

        // safety
        if ($discount > $request->subtotal) {
            $discount = $request->subtotal;
        }

        $finalTotal = $request->subtotal - $discount;

        return response()->json([
            'status' => true,
            'discount' => number_format($discount, 2),
            'final_total' => number_format($finalTotal, 2)
        ]);
    }
}
