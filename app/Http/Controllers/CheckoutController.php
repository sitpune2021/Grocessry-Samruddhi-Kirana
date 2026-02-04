<?php

namespace App\Http\Controllers;

use App\Services\FifoStockService;
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
use App\Models\Warehouse;
use App\Models\District;
use App\Models\ProductBatch;
use Illuminate\Support\Facades\Validator;
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

    public function createRazorpayOrder(Request $request)
    {
        $api = new Api(
            config('services.razorpay.key'),
            config('services.razorpay.secret')
        );

        $razorpayOrder = $api->order->create([
            'receipt' => 'ORD-' . time(),
            'amount' => $request->amount * 100,
            'currency' => 'INR'
        ]);

        Order::where('id', $request->order_id)->update([
            'razorpay_order_id' => $razorpayOrder['id']
        ]);

        Payment::where('order_id', $request->order_id)->update([
            'razorpay_order_id' => $razorpayOrder['id']
        ]);

        return response()->json([
            'razorpay_order_id' => $razorpayOrder['id']
        ]);
    }


    public function placeOrder(Request $request)
    {
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

        $cart = Cart::with('items.product')
            ->where('user_id', auth()->id())
            ->first();

        if (!$cart || $cart->items->isEmpty()) {
            return redirect()->route('cart')
                ->with('error', 'Your cart is empty or already processed.');
        }


        $finalTotal = $request->final_total ?? $cart->subtotal;


        $dcId = session('dc_warehouse_id');

        if (!$dcId) {
            if ($request->ajax()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Something went wrong'
                ], 422);
            }

            return redirect()->route('cart')
                ->with('error', 'Delivery location not selected');
        }

        // FINAL STOCK CHECK
        foreach ($cart->items as $item) {

            $availableQty = ProductBatch::where('product_id', $item->product_id)
                ->where('warehouse_id', $dcId)
                ->sum('quantity');

            if ($item->qty > $availableQty) {
                return redirect()->route('cart')->with(
                    'error',
                    "{$item->product->name} stock has changed. Only {$availableQty} left."
                );
            }
        }

        $order = Order::create([
            'user_id' => auth()->id(),
            'warehouse_id' => $dcId,
            'order_number' => 'ORD-' . time(),
            'channel' => 'web',

            'subtotal' => $cart->subtotal,
            'discount' => $request->coupon_discount ?? 0,
            'coupon_code' => $request->coupon_code ?? null,

            'total_amount' => $finalTotal,
            'payment_method' => $request->payment_method,
            'payment_status' => 'pending',
            'status' => 'pending',
            'order_type' => 'delivery',
        ]);


        Payment::create([
            'order_id' => $order->id,
            'user_id' => auth()->id(),
            'payment_gateway' => $request->payment_method === 'online' ? 'razorpay' : 'cash',
            'amount' => $order->total_amount,
            'status' => 'pending'
        ]);

        foreach ($cart->items as $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $item->product_id,
                'quantity' => $item->qty,
                'price' => $item->price,
                'line_total' => $item->line_total,
                'total' => $item->line_total,
            ]);
        }

        if (strtolower($request->payment_method) === 'cash') {

            $dcId = session('dc_warehouse_id');
            $userId = auth()->id();

            $order->load('items');

            DB::transaction(function () use ($order, $dcId, $userId) {

                $fifo = new FifoStockService();

                foreach ($order->items as $item) {
                    $fifo->consume(
                        $item->product_id,
                        $dcId,
                        $item->quantity,
                        $order->id,
                        $userId
                    );
                }

                // Mark order confirmed
                $order->update([
                    'payment_status' => 'paid',
                    'status' => 'confirmed'
                ]);
            });

            // Clear cart AFTER FIFO success
            $cart->items()->delete();
            $cart->delete();

            return redirect()->route('my_orders')
                ->with('success', 'Order placed successfully');
        }

        // ONLINE → JSON response
        return response()->json([
            'status' => true,
            'order_id' => $order->id,
            'amount' => $order->total_amount
        ]);
    }

    public function paymentSuccess(Request $request)
    {
        $api = new Api(
            config('services.razorpay.key'),
            config('services.razorpay.secret')
        );

        try {
            $api->utility->verifyPaymentSignature([
                'razorpay_order_id' => $request->razorpay_order_id,
                'razorpay_payment_id' => $request->razorpay_payment_id,
                'razorpay_signature' => $request->razorpay_signature
            ]);

            $order = Order::where('razorpay_order_id', $request->razorpay_order_id)->firstOrFail();

            $order->load('items');

            Payment::where('order_id', $order->id)->update([
                'payment_id' => $request->razorpay_payment_id,
                'razorpay_signature' => $request->razorpay_signature,
                'status' => 'success'
            ]);


            $dcId = $order->warehouse_id; // 🔥 from order
            $userId = $order->user_id;

            DB::transaction(function () use ($order, $dcId, $userId) {

                $fifo = new FifoStockService();

                foreach ($order->items as $item) {
                    $fifo->consume(
                        $item->product_id,
                        $dcId,
                        $item->quantity,
                        $order->id,
                        $userId
                    );
                }

                $order->update([
                    'payment_status' => 'paid',
                    'status' => 'confirmed'
                ]);
            });

            $cart = Cart::where('user_id', $order->user_id)->first();

            if ($cart) {
                $cart->items()->delete();
                $cart->delete();
            }
            return response()->json([
                'status' => true,
                'redirect_url' => route('thank_you', $order->id)
            ]);
        } catch (\Exception $e) {
            Log::error('Razorpay Error', ['error' => $e->getMessage()]);
            return response()->json(['status' => false], 400);
        }
    }

    public function thankYou(Order $order)
    {
        // Security: only owner can see


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
