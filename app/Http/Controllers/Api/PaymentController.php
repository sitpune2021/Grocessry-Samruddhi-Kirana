<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Razorpay\Api\Api;
use App\Models\Order;
use App\Models\Payment;


class PaymentController extends Controller
{

public function create(Request $request)
{
    $request->validate([
        'order_id' => 'required|exists:orders,id'
    ]);

    $order = Order::findOrFail($request->order_id);

    // Prevent duplicate payment
    $existingPayment = Payment::where('order_id', $order->id)
        ->where('status', 'pending')
        ->first();

    if ($existingPayment) {
        return response()->json([
            'success' => true,
            'message' => 'Payment already initiated',
            'razorpay_order_id' => $existingPayment->razorpay_order_id,
            'amount' => $order->total_amount * 100,
            'key' => env('RAZORPAY_KEY'),
        ]);
    }

    $api = new Api(env('RAZORPAY_KEY'), env('RAZORPAY_SECRET'));

    $razorpayOrder = $api->order->create([
        'receipt' => 'order_' . $order->id,
        'amount' => $order->total_amount * 100,
        'currency' => 'INR',
    ]);

    $payment = Payment::create([
        'order_id' => $order->id,
        'user_id' => $order->user_id,
        'payment_gateway' => 'razorpay',
        'razorpay_order_id' => $razorpayOrder['id'],
        'amount' => $order->total_amount,
        'status' => 'pending',
    ]);

    return response()->json([
        'success' => true,
        'razorpay_order_id' => $razorpayOrder['id'],
        'amount' => $order->total_amount * 100,
        'key' => env('RAZORPAY_KEY'),
    ]);
}

public function verify(Request $request)
{
    $request->validate([
        'razorpay_payment_id' => 'required',
        'razorpay_order_id' => 'required',
        'razorpay_signature' => 'required',
    ]);

    $api = new Api(env('RAZORPAY_KEY'), env('RAZORPAY_SECRET'));

    try {
        $api->utility->verifyPaymentSignature([
            'razorpay_order_id' => $request->razorpay_order_id,
            'razorpay_payment_id' => $request->razorpay_payment_id,
            'razorpay_signature' => $request->razorpay_signature
        ]);

        $payment = Payment::where('razorpay_order_id', $request->razorpay_order_id)->firstOrFail();

        $payment->update([
            'payment_id' => $request->razorpay_payment_id,
            'razorpay_signature' => $request->razorpay_signature,
            'status' => 'success'
        ]);

        // Update order
        Order::where('id', $payment->order_id)->update([
            'payment_status' => 'paid',
            'status' => 'confirmed'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Payment successful'
        ]);

    } catch (\Exception $e) {

        Payment::where('razorpay_order_id', $request->razorpay_order_id)
            ->update([
                'status' => 'failed',
                'failure_reason' => $e->getMessage()
            ]);

        return response()->json([
            'success' => false,
            'message' => 'Payment verification failed'
        ]);
    }
}

public function failure(Request $request)
{
    $request->validate([
        'razorpay_order_id' => 'required',
        'error' => 'nullable|string'
    ]);

    Payment::where('razorpay_order_id', $request->razorpay_order_id)
        ->update([
            'status' => 'failed',
            'failure_reason' => $request->error
        ]);

    return response()->json([
        'success' => false,
        'message' => 'Payment marked as failed'
    ]);
}

}
