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
use App\Models\Warehouse;
use App\Models\District;
use App\Models\Talukas;
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
            'amount' => $request->amount,
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

        // 1️⃣ Validation
        $request->validate([
            'first_name'     => 'required',
            'last_name'      => 'required',
            'address'        => 'required',
            'city'           => 'required',
            'country'        => 'required',
            'postcode'       => 'required',
            'phone'          => 'required',
            'email'          => 'required|email',
            'payment_method' => 'required',
        ]);

        Log::info('Validation passed');

        // 2️⃣ Save or update user address
        $userAddress = UserAddress::updateOrCreate(
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

        Log::info('User address saved', $userAddress->toArray());

        // 3️⃣ Load cart
        $cart = Cart::with('items.product')
            ->where('user_id', auth()->id())
            ->first();

        if (!$cart || $cart->items->isEmpty()) {
            Log::warning('Cart empty for user ' . auth()->id());
            return redirect()->route('cart')->with('error', 'Cart is empty.');
        }

        // 🔥 COUPON CALCULATION (FINAL)
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

        Log::info('Cart loaded', $cart->toArray());

        // 4️⃣ Find district by user city
        // $district = District::where('name', $request->city)->first();
        // if (!$district) {
        //     Log::error('District not found for city: '.$request->city);
        //     return back()->with('error', 'Invalid city selected.');
        // }
        // Log::info('District found', $district->toArray());
        // 4️⃣ Find taluka by city name
        $city = trim($request->city);

        $taluka = Talukas::whereRaw('LOWER(name) = ?', [strtolower($city)])->first();

        if (!$taluka) {
            Log::error('Taluka not found for city', [
                'input_city' => $request->city
            ]);
            return back()->with('error', 'We do not deliver to this city.');
        }

        Log::info('Taluka found', $taluka->toArray());

        if (!$taluka) {
            Log::error('Taluka not found for city: ' . $request->city);
            return back()->with('error', 'We do not deliver to this city.');
        }

        Log::info('Taluka found', $taluka->toArray());

        // 5️⃣ Get district from taluka
        $district = District::find($taluka->district_id);

        if (!$district) {
            Log::error('District not found for taluka: ' . $taluka->id);
            return back()->with('error', 'We do not deliver to this city.');
        }

        Log::info('District resolved from taluka', $district->toArray());


        // 5️⃣ Find distribution center in that district
        // $distributionCenter = Warehouse::where('type', 'distribution_center')
        //     ->where('district_id', $district->id)
        //     ->where('status', 'active')
        //     ->first();

        // if (!$distributionCenter) {
        //     Log::error('No distribution center in district: '.$district->name);
        //     return back()->with('error', 'No distribution center available in your area.');
        // }
        //Log::info('Distribution center found', $distributionCenter->toArray());

        // 5️⃣ Find ALL distribution centers in district
        $warehouses = Warehouse::where('type', 'distribution_center')
            ->where('district_id', $district->id)
            ->where('taluka_id', $taluka->id)
            ->where('status', 'active')
            ->get();

        if ($warehouses->isEmpty()) {
            return back()->with('error', 'No distribution center available in your area.');
        }

        // 6️⃣ Find warehouse which can fulfill FULL cart
        $selectedWarehouse = null;

        foreach ($warehouses as $warehouse) {

            $canFulfill = true;

            foreach ($cart->items as $item) {

                $stockQty = DB::table('warehouse_stock')
                    ->where('warehouse_id', $warehouse->id)
                    ->where('product_id', $item->product_id)
                    ->sum('quantity');

                if ($stockQty < $item->qty) {
                    $canFulfill = false;
                    break;
                }
            }

            if ($canFulfill) {
                $selectedWarehouse = $warehouse;
                break; // ✅ first nearest/available warehouse
            }
        }

        if (!$selectedWarehouse) {
    if ($request->ajax()) {
        return response()->json([
            'status' => false,
            'error' => 'This product is currently out of stock in your area.'
        ], 422);
    }

    return back()->with('error', 'This product is currently out of stock in your area.');
}



        // // 6️⃣ Stock check
        // foreach ($cart->items as $item) {
        //     $stockQty = DB::table('warehouse_stock')
        //         ->where('warehouse_id', $distributionCenter->id)
        //         ->where('product_id', $item->product_id)
        //         ->sum('quantity');

        //     if ($stockQty < $item->qty) {
        //         Log::error("Product out of stock", ['product' => $item->product_id]);
        //         return back()->with('error', "Product '{$item->product->name}' is out of stock.");
        //     }
        // }

        // 7️⃣ Create order
        $finalTotal = $cart->subtotal;
        $finalTotal = $cart->subtotal - $couponDiscount;
        $order = Order::create([
            'user_id'          => auth()->id(),
            //'warehouse_id'     => $distributionCenter->id,
            'warehouse_id'     => $selectedWarehouse->id,
            'order_number'     => 'ORD-' . time(),
            'channel'          => 'web',
            'subtotal'         => $cart->subtotal,
            'discount'         => $couponDiscount,        // 🔥 IMPORTANT
            'coupon_discount'  => $couponDiscount,        // 🔥 IMPORTANT
            'coupon_code'      => $couponCode,
            'delivery_charge'  => 0,
            'total_amount'     => $finalTotal,
            'payment_method'   => $request->payment_method,
            'payment_status'   => 'pending',
            'status'           => 'pending',
            'order_type'       => 'delivery',
        ]);

        Payment::create([
            'order_id' => $order->id,
            'user_id' => auth()->id(),
            'payment_gateway' => $request->payment_method === 'online' ? 'razorpay' : 'cash',
            'amount' => $order->total_amount,
            'status' => 'pending'
        ]);

        Log::info('Order created', $order->toArray());

        // 8️⃣ Create order items
        foreach ($cart->items as $item) {
            Log::info('Creating OrderItem', $item->toArray());
            OrderItem::create([
                'order_id'        => $order->id,
                'product_id'      => $item->product_id,
                'product_batch_id' => $item->product_batch_id ?? null,
                'quantity'        => $item->qty,
                'price'           => $item->price,
                'tax_percent'     => $item->tax_percent ?? 0,
                'tax_amount'      => $item->tax_amount ?? 0,
                'line_total'      => $item->line_total,
                'total'           => $item->line_total,
                'is_picked'       => false,
            ]);
        }

        // CASH → normal redirect
        if ($request->payment_method === 'Cash') {
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

    public function validateOrder(Request $request)
    {
        // 1️⃣ Basic validation
        $validator = Validator::make($request->all(), [
            'first_name'     => 'required',
            'last_name'      => 'required',
            'address'        => 'required',
            'city'           => 'required',
            'country'        => 'required',
            'postcode'       => 'required',
            'phone'          => 'required',
            'email'          => 'required|email',
            'payment_method' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first()
            ]);
        }

        // 2️⃣ Find taluka by city
        $city = trim($request->city);

        $taluka = Talukas::whereRaw(
            'LOWER(name) = ?',
            [strtolower($city)]
        )->first();

        if (!$taluka) {
            return response()->json([
                'status' => 'error',
                'message' => 'We do not deliver to this city.'
            ]);
        }

        // 3️⃣ Get district from taluka
        $district = District::find($taluka->district_id);

        if (!$district) {
            return response()->json([
                'status' => 'error',
                'message' => 'We do not deliver to this city.'
            ]);
        }

        // 4️⃣ Find distribution centers
        $warehouses = Warehouse::where('type', 'distribution_center')
            ->where('district_id', $district->id)
            ->where('taluka_id', $taluka->id)
            ->where('status', 'active')
            ->get();

        if ($warehouses->isEmpty()) {
            return response()->json([
                'status' => 'error',
                'message' => 'No distribution center available in your area.'
            ]);
        }

        // 5️⃣ Cart check
        $cart = Cart::with('items.product')
            ->where('user_id', auth()->id())
            ->first();

        if (!$cart || $cart->items->isEmpty()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Your cart is empty.'
            ]);
        }

        // 6️⃣ Stock check (ANY warehouse that can fulfill full cart)
        foreach ($warehouses as $warehouse) {

            $canFulfill = true;

            foreach ($cart->items as $item) {
                $stockQty = DB::table('warehouse_stock')
                    ->where('warehouse_id', $warehouse->id)
                    ->where('product_id', $item->product_id)
                    ->sum('quantity');

                if ($stockQty < $item->qty) {
                    $canFulfill = false;
                    break;
                }
            }

            if ($canFulfill) {
                return response()->json(['status' => 'success']);
            }
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Products are not available together in nearby warehouses.'
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
