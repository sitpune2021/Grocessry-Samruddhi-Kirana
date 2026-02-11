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
use Illuminate\Validation\Rule;

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

        if (!session()->has('delivery_pincode')) {
            return redirect()->route('home')
                ->with('error', 'Please select delivery location');
        }
        $userAddresses = UserAddress::where('user_id', auth()->id())->get();

        $defaultAddress = UserAddress::where('user_id', $userId)
            ->where('is_default', 1)
            ->first();

        $deliveryPincode = session('delivery_pincode');

        $address = UserAddress::where('user_id', $userId)->first();

        $coupons = Coupon::where('status', 1)
            ->whereDate('start_date', '<=', now())
            ->whereDate('end_date', '>=', now())
            ->get();

        return view('website.checkout', compact('cart', 'address', 'coupons', 'userAddresses', 'deliveryPincode', 'defaultAddress'));
    }

    public function createRazorpayOrder(Request $request)
    {
        $api = new Api(
            config('services.razorpay.key'),
            config('services.razorpay.secret')
        );

        $razorpayOrder = $api->order->create([
            'amount' => $request->amount * 100,
            'currency' => 'INR',
            'receipt' => 'order_' . $request->order_id
        ]);

        // 🔥 SAVE razorpay_order_id in orders table
        Order::where('id', $request->order_id)->update([
            'razorpay_order_id' => $razorpayOrder['id']
        ]);

        return response()->json([
            'razorpay_order_id' => $razorpayOrder['id']
        ]);
    }

    public function placeOrder(Request $request)
    {
        Log::info('Checkout Request:', $request->all());
        $rules = [
            'selected_address' => [
                'nullable',
                Rule::exists('user_addresses', 'id')
                    ->where('user_id', auth()->id()),
            ],
        ];

        if (!$request->filled('selected_address')) {
            $rules = array_merge($rules, [
                'type'       => 'required|in:1,2,3',
                'first_name' => 'required',
                'last_name'  => 'required',
                'flat_house' => 'required',
                'area'       => 'required',
                'city'       => 'required',
                'postcode'   => 'required|digits:6',
                'phone'      => 'required|digits:10',
            ]);
        }

        $request->validate($rules);
        if ($request->filled('selected_address')) {

            $existingAddress = UserAddress::where('id', $request->selected_address)
                ->where('user_id', auth()->id())
                ->firstOrFail();

            $addressId   = $existingAddress->id;
            $addressType = $existingAddress->type;
        } else {

            // ONLY when user explicitly adds a NEW address
            $addressType = $request->type;

            $address = UserAddress::create([
                'user_id'    => auth()->id(),
                'type'       => $addressType,
                'first_name' => $request->first_name,
                'last_name'  => $request->last_name,
                'flat_house' => $request->flat_house,
                'floor'      => $request->floor,
                'area'       => $request->area,
                'landmark'   => $request->landmark,
                'city'       => $request->city,
                'postcode'   => $request->postcode,
                'phone'      => $request->phone,
                'is_default' => 1
            ]);

            $addressId = $address->id;
        }


        $cart = Cart::with('items.product')
            ->where('user_id', auth()->id())
            ->first();

        if (!$cart || $cart->items->isEmpty()) {
            return $request->ajax()
                ? response()->json(['status' => false, 'message' => 'Cart empty'], 422)
                : redirect()->route('cart')->with('error', 'Cart empty');
        }

        $dcId = session('dc_warehouse_id');
        if (!$dcId) {
            return $request->ajax()
                ? response()->json(['status' => false, 'message' => 'Delivery location not selected'], 422)
                : redirect()->route('cart')->with('error', 'Delivery location not selected');
        }

        foreach ($cart->items as $item) {
            $available = ProductBatch::where('product_id', $item->product_id)
                ->where('warehouse_id', $dcId)
                ->sum('quantity');

            if ($item->qty > $available) {
                return $request->ajax()
                    ? response()->json([
                        'status' => false,
                        'message' => "{$item->product->name} only {$available} left"
                    ], 422)
                    : redirect()->route('cart')
                    ->with('error', "{$item->product->name} only {$available} left");
            }
        }

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

        $order = Order::create([
            'user_id'          => auth()->id(),
            'order_number'     => 'ORD-' . time(),
            'channel'          => 'web',
            'subtotal'         => $cart->subtotal,
            'discount'         => $couponDiscount,
            'coupon_discount'  => $couponDiscount,
            'coupon_code'      => $couponCode,
            'total_amount'     => $finalTotal,
            'payment_method'   => $request->payment_method,
            'payment_status'   => 'pending',
            'status'           => 'pending',
            'order_type'       => 'delivery',
        ]);

        Payment::create([
            'order_id'        => $order->id,
            'user_id'         => auth()->id(),
            'payment_gateway' => $request->payment_method === 'online' ? 'razorpay' : 'cash',
            'amount'          => $order->total_amount,
            'status'          => 'pending'
        ]);

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

        if ($request->payment_method === 'cash') {
            $cart->items()->delete();
            $cart->delete();

            return redirect()->route('my_orders')
                ->with('success', 'Order placed successfully');
        }

        return response()->json([
            'status'   => true,
            'order_id' => $order->id,
            'amount'   => $order->total_amount
        ]);
    }

    public function paymentSuccess(Request $request)
    {
        $api = new Api(
            config('services.razorpay.key'),
            config('services.razorpay.secret')
        );
        Log::info('Razorpay Incoming Data', $request->all());

        try {
            // ✅ VERIFY SIGNATURE
            $api->utility->verifyPaymentSignature([
                'razorpay_order_id' => $request->razorpay_order_id,
                'razorpay_payment_id' => $request->razorpay_payment_id,
                'razorpay_signature' => $request->razorpay_signature
            ]);

            // ✅ FIND ORDER
            $order = Order::where('razorpay_order_id', $request->razorpay_order_id)->firstOrFail();

            Payment::where('order_id', $order->id)->update([
                'payment_id' => $request->razorpay_payment_id,
                'razorpay_signature' => $request->razorpay_signature,
                'status' => 'success'
            ]);

            // ✅ UPDATE ORDER
            $order->update([
                'payment_status' => 'paid',
                'status' => 'confirmed'
            ]);

            // 🔥 EMPTY CART
            $cart = Cart::where('user_id', $order->user_id)->first();
            if ($cart) {
                $cart->items()->delete();
                $cart->delete();
            }

            return response()->json([
                'status' => true,
                'redirect_url' => route('my_orders')
            ]);
        } catch (\Exception $e) {
            Log::error('Razorpay Verify Failed', [
                'msg' => $e->getMessage(),
                'data' => $request->all()
            ]);

            return response()->json([
                'status' => false,
                'message' => 'Payment verification failed'
            ], 400);
        }
    }

    public function applyCoupon(Request $request)
    {
        Log::info($request->all());
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
