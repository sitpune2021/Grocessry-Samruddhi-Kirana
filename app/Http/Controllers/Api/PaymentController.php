<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Razorpay\Api\Api;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{

public function create(Request $request)
{
    Log::info('Payment Create API called', [
        'request_data' => $request->all()
    ]);

    try {
        $request->validate([
            'order_id' => 'required|exists:orders,id'
        ]);

        Log::info('Validation passed', [
            'order_id' => $request->order_id
        ]);

        $order = Order::findOrFail($request->order_id);

        Log::info('Order fetched', [
            'order_id' => $order->id,
            'amount' => $order->total_amount
        ]);

        // Prevent duplicate payment
        $existingPayment = Payment::where('order_id', $order->id)
            ->where('status', 'pending')
            ->first();

        if ($existingPayment) {

            Log::warning('Duplicate payment attempt', [
                'order_id' => $order->id,
                'razorpay_order_id' => $existingPayment->razorpay_order_id
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Payment already initiated',
                'razorpay_order_id' => $existingPayment->razorpay_order_id,
                'amount' => $order->total_amount * 100,
                'key' => env('RAZORPAY_KEY'),
            ]);
        }

        Log::info('Creating Razorpay order...');

        $api = new Api(env('RAZORPAY_KEY'), env('RAZORPAY_SECRET'));

        $razorpayOrder = $api->order->create([
            'receipt' => 'order_' . $order->id,
            'amount' => $order->total_amount * 100,
            'currency' => 'INR',
        ]);

        Log::info('Razorpay order created', [
            'razorpay_order_id' => $razorpayOrder['id']
        ]);

        $payment = Payment::create([
            'order_id' => $order->id,
            'user_id' => $order->user_id,
            'payment_gateway' => 'razorpay',
            'razorpay_order_id' => $razorpayOrder['id'],
            'amount' => $order->total_amount,
            'status' => 'pending',
        ]);

        Log::info('Payment record created', [
            'payment_id' => $payment->id,
            'order_id' => $order->id
        ]);

        return response()->json([
            'success' => true,
            'razorpay_order_id' => $razorpayOrder['id'],
            'amount' => $order->total_amount * 100,
            'key' => env('RAZORPAY_KEY'),
        ]);

    } catch (\Exception $e) {

        Log::error('Payment Create API failed', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);

        return response()->json([
            'success' => false,
            'message' => 'Something went wrong'
        ], 500);
    }
}

public function verify(Request $request)
{
    Log::info('Payment Verify API called', [
        'request_data' => $request->all()
    ]);

    try {
        $request->validate([
            'razorpay_payment_id' => 'required',
            'razorpay_order_id' => 'required',
            'razorpay_signature' => 'required',
        ]);

        Log::info('Validation passed', [
            'razorpay_order_id' => $request->razorpay_order_id
        ]);

        $api = new Api(env('RAZORPAY_KEY'), env('RAZORPAY_SECRET'));

        Log::info('Starting signature verification');

        $api->utility->verifyPaymentSignature([
            'razorpay_order_id' => $request->razorpay_order_id,
            'razorpay_payment_id' => $request->razorpay_payment_id,
            'razorpay_signature' => $request->razorpay_signature
        ]);

        Log::info('Signature verified successfully');

        $payment = Payment::where('razorpay_order_id', $request->razorpay_order_id)->firstOrFail();

        Log::info('Payment record found', [
            'payment_id' => $payment->id,
            'order_id' => $payment->order_id
        ]);

        $payment->update([
            'payment_id' => $request->razorpay_payment_id,
            'razorpay_signature' => $request->razorpay_signature,
            'status' => 'success'
        ]);

        Log::info('Payment updated to success', [
            'payment_id' => $payment->id
        ]);

        // Update order
        Order::where('id', $payment->order_id)->update([
            'payment_status' => 'paid',
            'status' => 'confirmed'
        ]);

        Log::info('Order updated to paid', [
            'order_id' => $payment->order_id
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Payment successful'
        ]);

    } catch (\Exception $e) {

        Log::error('Payment verification failed', [
            'error' => $e->getMessage(),
            'request_data' => $request->all()
        ]);

        Payment::where('razorpay_order_id', $request->razorpay_order_id)
            ->update([
                'status' => 'failed',
                'failure_reason' => $e->getMessage()
            ]);

        Log::warning('Payment marked as failed', [
            'razorpay_order_id' => $request->razorpay_order_id
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
