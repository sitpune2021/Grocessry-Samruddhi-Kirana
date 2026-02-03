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

    public function placeOrder(Request $request)
    {
        DB::beginTransaction();

        try {
            Log::info('PlaceOrder started', $request->all());

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
                    'first_name', 'last_name', 'address', 'city', 'country', 'postcode', 'phone', 'email'
                ]) + ['type' => 1]
            );

            Log::info('User address saved', $userAddress->toArray());

            // 3️⃣ Load cart
            $cart = Cart::with('items.product')
                ->where('user_id', auth()->id())
                ->first();

            if (!$cart || $cart->items->isEmpty()) {
                Log::warning('Cart empty for user '.auth()->id());
                return redirect()->route('cart')->with('error', 'Cart is empty.');
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

        $dcId = session('dc_warehouse_id');

        if (!$dcId) {
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
            'warehouse_id'   => $dcId,
            'order_number' => 'ORD-' . time(),
            'channel' => 'web',
            'subtotal' => $cart->subtotal,
            'total_amount' => $finalTotal,
            'payment_method' => $request->payment_method,
            'payment_status' => 'pending',
            'status' => 'pending',
            'order_type' => 'delivery',
        ]);

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

            DB::commit();

            Log::info('Order placed successfully', ['order_id' => $order->id]);
            return redirect()->route('my_orders')->with('success', 'Order placed successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Order placement failed', [
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString()
            ]);
            return back()->with('error', 'Something went wrong while placing the order.');
        }
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

            Cart::where('user_id', $order->user_id)->delete();


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
