<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Razorpay\Api\Api;
use Illuminate\Support\Str;


class PaymentGetwayController extends Controller
{

    public function createRazorpayOrder(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id'
        ]);

        $order = Order::findOrFail($request->order_id);

        $api = new Api(
            config('services.razorpay.key'),
            config('services.razorpay.secret')
        );

        $razorpayOrder = $api->order->create([
            'receipt' => $order->order_number,
            'amount' => (float) ($order->total_amount * 100),
            'currency' => 'INR'
        ]);

        // Save razorpay_order_id
        $order->update([
            'razorpay_order_id' => $razorpayOrder['id']
        ]);

        return response()->json([
            'razorpay_order_id' => $order->razorpay_order_id,
            'amount' => $order->total_amount * 100,
            'key' => config('services.razorpay.key')
        ]);


        // if ($order->razorpay_order_id) {
        //     return response()->json([
        //         'razorpay_order_id' => $order->razorpay_order_id,
        //         'amount' => $order->total_amount * 100,
        //         'key' => config('services.razorpay.key')
        //     ]);
        // }
    }

    public function verifyRazorpayPayment(Request $request)
    {
        Log::info('Razorpay VERIFY HIT', $request->all());

        $request->validate([
            'razorpay_payment_id' => 'required',
            'razorpay_order_id' => 'required',
            'razorpay_signature' => 'required',
            'order_id' => 'required|exists:orders,id'
        ]);

         $order = Order::findOrFail($request->order_id);

    if (!$order->razorpay_order_id) {
        return response()->json([
            'message' => 'Razorpay order id missing in DB'
        ], 422);
    }
    
        $api = new Api(
            config('services.razorpay.key'),
            config('services.razorpay.secret')
        );

        try {
            $api->utility->verifyPaymentSignature([
                'razorpay_order_id' => $request->razorpay_order_id,
                'razorpay_payment_id' => $request->razorpay_payment_id,
                'razorpay_signature' => $request->razorpay_signature,
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Payment failed !'], 400);
        }

        $order = Order::findOrFail($request->order_id);

        Payment::create([
            'order_id' => $order->id,
            'user_id' => $order->user_id,
            'payment_gateway' => 'razorpay',
            'payment_id' => $request->razorpay_payment_id,
            'amount' => $order->total_amount,
            'status' => 'success',
            'meta' => json_encode($request->all())
        ]);

        $order->update([
            'payment_status' => 'paid'
        ]);

        return response()->json(['success' => true]);
    }
}
