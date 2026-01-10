<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Cart;
 use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    
    public function index()
    {
        $userId = Auth::id();

        $cart = Cart::with('items.product')
            ->where('user_id', $userId)
            ->first();

        return view('website.checkout', compact('cart'));
    }

    public function placeOrder(Request $request)
    {
        $cart = Cart::where('user_id', auth()->id())
                    ->with('items')
                    ->first();

        if (!$cart || $cart->items->isEmpty()) {
            return back()->with('error','Cart empty');
        }

        // Generate Order Number
        $orderNumber = 'ORD-' . date('Ymd') . '-' . rand(1000,9999);

        $order = Order::create([
            'user_id'        => auth()->id(),
            'order_number'  => $orderNumber,
            'subtotal'      => $cart->subtotal,
            'discount'      => $cart->discount ?? 0,
            'total_amount' => $cart->total,
            'payment_method'=> $request->payment_method,
            'status'        => 'pending',
        ]);

        foreach ($cart->items as $item) {
            OrderItem::create([
                'order_id'  => $order->id,
                'product_id'=> $item->product_id,
                'qty'       => $item->qty,
                'price'     => $item->price,
                'line_total'=> $item->line_total,
                'quantity'   => $item->qty,
                'total'      => '0',
            ]);
        }

        $cart->items()->delete();
        $cart->delete();

        return redirect('/')->with('success','Order placed successfully!');
    }

}
