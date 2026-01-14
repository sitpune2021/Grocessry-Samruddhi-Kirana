<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Cart;
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

            Log::info('Place order request received', $request->all());

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
                ['user_id' => auth()->id()],
                $request->only([
                    'first_name','last_name','address','city',
                    'country','postcode','phone','email'
                ])
            );

            $cart = Cart::where('user_id', auth()->id())
                        ->with('items')
                        ->first();

            if (!$cart || $cart->items->isEmpty()) {
                Log::warning('Cart empty for user', ['user_id' => auth()->id()]);
                return back()->with('error','Cart empty');
            }

            $order = Order::create([
                'user_id' => auth()->id(),
                'order_number' => 'ORD-' . now()->timestamp,
                'subtotal' => $cart->subtotal,
                'total_amount' => $cart->total,
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
                    'total'      => $item->qty * $item->price,
                ]);
            }

            $cart->items()->delete();
            $cart->delete();

            Log::info('Order placed successfully', ['order_id' => $order->id]);

            return redirect('/')->with('success','Order placed successfully!');

        } catch (\Exception $e) {

            Log::error('Order place failed', [
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
            ]);

            return back()->with('error','Something went wrong!');
        }
    }


}
