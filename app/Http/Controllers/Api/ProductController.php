<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use Throwable;
use App\Models\Offer;
use App\Models\Role;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    public function addToCart(Request $request)
    {
        $user = $request->user();
        if ($res = $this->checkCustomer($user)) return $res;

        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity'   => 'nullable|integer|min:1',
        ]);

        $cart = Cart::where('user_id', $user->id)
            ->where('product_id', $request->product_id)
            ->first();

        if ($cart) {
            $cart->quantity += $request->quantity ?? 1;
            $cart->save();
        } else {
            Cart::create([
                'user_id'    => $user->id,
                'product_id' => $request->product_id,
                'quantity'   => $request->quantity ?? 1,
            ]);
        }

        return response()->json([
            'status' => true,
            'message' => 'Product added to cart'
        ]);
    }

    public function viewCart(Request $request)
    {
        $user = $request->user();
        if ($res = $this->checkCustomer($user)) return $res;

        $cartItems = Cart::with('product')
            ->where('user_id', $user->id)
            ->get();

        return response()->json([
            'status' => true,
            'data' => $cartItems
        ]);
    }

    public function clearCart(Request $request)
    {
        $user = $request->user();
        if ($res = $this->checkCustomer($user)) return $res;

        Cart::where('user_id', $user->id)->delete();

        return response()->json([
            'status' => true,
            'message' => 'Cart cleared'
        ]);
    }

    public function removeSingleItem(Request $request)
    {
        $user = $request->user();
        if ($res = $this->checkCustomer($user)) return $res;

        // Validate ke user ne product choose keli aahe ki nahi
        $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        $cartItem = Cart::where('user_id', $user->id)
            ->where('product_id', $request->product_id)
            ->first();

        if (!$cartItem) {
            return response()->json([
                'status' => false,
                'message' => 'Selected product not found in cart'
            ], 404);
        }

        // 🔹 1 by 1 quantity remove
        if ($cartItem->quantity > 1) {
            $cartItem->quantity -= 1;
            $cartItem->save();
        } else {
            $cartItem->delete();
        }

        return response()->json([
            'status' => true,
            'message' => 'Selected product quantity reduced by 1',
            'data' => [
                'product_id' => $cartItem->product_id,
                'remaining_quantity' => $cartItem->quantity ?? 0
            ]
        ]);
    }


    public function checkout(Request $request)
    {
        $user = $request->user();
        if ($res = $this->checkCustomer($user)) return $res;

        $cartItems = Cart::with('product')
            ->where('user_id', $user->id)
            ->get();

        if ($cartItems->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => 'Cart is empty'
            ], 400);
        }

        // Subtotal
        $subtotal = 0;
        foreach ($cartItems as $item) {
            $price = $item->product->retailer_price ?? 0;
            $subtotal += $price * $item->quantity;
        }

        $deliveryCharge = 50;
        $couponDiscount = 0;
        $offerId = null;
        $couponCode = null;

        // Coupon Apply
        if ($request->coupon_code) {
            $offer = Offer::where('code', $request->coupon_code)
                ->where('status', 1)
                ->whereDate('start_date', '<=', now())
                ->whereDate('end_date', '>=', now())
                ->first();

            if (!$offer) {
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid coupon'
                ], 400);
            }

            if ($subtotal < $offer->min_amount) {
                return response()->json([
                    'status' => false,
                    'message' => 'Minimum order amount not met'
                ], 400);
            }

            $couponDiscount = $offer->discount_type === 'flat'
                ? $offer->discount_value
                : ($subtotal * $offer->discount_value) / 100;

            $offerId = $offer->id;
            $couponCode = $offer->code;
        }

        $totalAmount = max(($subtotal + $deliveryCharge - $couponDiscount), 0);

        // Create Order
        $order = Order::create([
            'user_id' => $user->id,
            'order_number' => 'ORD-' . time(),
            'subtotal' => $subtotal,
            'delivery_charge' => $deliveryCharge,
            'coupon_discount' => $couponDiscount,
            'total_amount' => $totalAmount,
            'status' => 'pending',
            'offer_id' => $offerId,
            'coupon_code' => $couponCode
        ]);

        // Order Items
        foreach ($cartItems as $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $item->product_id,
                'quantity' => $item->quantity,
                'price' => $item->product->retailer_price ?? 0,
                'total' => ($item->product->retailer_price ?? 0) * $item->quantity
            ]);
        }

        // Clear Cart
        Cart::where('user_id', $user->id)->delete();

        return response()->json([
            'status' => true,
            'message' => 'Order placed successfully',
            'data' => [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'subtotal' => $subtotal,
                'delivery_charge' => $deliveryCharge,
                'coupon_discount' => $couponDiscount,
                'total_amount' => $totalAmount
            ]
        ]);
    }

    protected function checkCustomer($user)
    {
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthenticated user'
            ], 401);
        }

        if (
            !$user->role ||
            strtolower($user->role->name) !== 'customer'
        ) {
            return response()->json([
                'status' => false,
                'message' => 'Only customers are allowed'
            ], 403);
        }

        return null; // ✅ allowed
    }



    public function returnProduct(Request $request)
    {
        $user = $request->user();
        if ($res = $this->checkCustomer($user)) return $res;

        $request->validate([
            'order_item_id'     => 'required|exists:order_items,id',
            'reason'            => 'required|string|max:191',
            'return_type'       => 'required|in:refund,exchange',
            'product_images'    => 'nullable|array',
            'product_images.*'  => 'image|mimes:jpg,jpeg,png',
        ]);

        // Fetch order item
        $orderItem = OrderItem::findOrFail($request->order_item_id);

        // Ensure this order belongs to logged-in customer
        if ($orderItem->order->user_id !== $user->id) {
            return response()->json([
                'status' => false,
                'message' => 'You cannot return this product'
            ], 403);
        }

        /* ================= Upload Return Images ================= */
        $imagePaths = [];

        if ($request->hasFile('product_images')) {
            foreach ($request->file('product_images') as $image) {

                $filename = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();

                $path = $image->storeAs(
                    'customer_return_products',
                    $filename,
                    'public'
                );

                $imagePaths[] = $path;
            }
        }

        /* ================= Insert Return Request ================= */
        $returnId = DB::table('customer_order_returns')->insertGetId([
            'order_id'        => $orderItem->order_id,
            'order_item_id'  => $orderItem->id,
            'product_id'     => $orderItem->product_id,
            'customer_id'    => $user->id,
            'quantity'       => $orderItem->quantity,
            'reason'         => $request->reason,
            'return_type'    => $request->return_type,   // refund | exchange
            'status'         => 'requested',
            'qc_status'      => 'pending',
            'product_images' => !empty($imagePaths) ? json_encode($imagePaths) : null,
            'created_at'     => now(),
            'updated_at'     => now(),
        ]);

        $returnData = DB::table('customer_order_returns')
            ->where('id', $returnId)
            ->first();

        return response()->json([
            'status' => true,
            'message' => 'Return request submitted successfully',
            'data' => $returnData
        ]);
    }
}
