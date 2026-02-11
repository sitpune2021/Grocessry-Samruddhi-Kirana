<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Offer;
use App\Models\Cart;
use App\Models\Coupon;
use App\Models\CartItem;
use Illuminate\Support\Facades\DB;

class CustomerCouponsOffersController extends Controller
{
    public function getOffers(Request $request)
    {
        $today = Carbon::today()->toDateString();

        $offers = Offer::where('status', 1)
            ->whereDate('start_date', '<=', $today)
            ->whereDate('end_date', '>=', $today)
            ->select(
                'id',
                'title',
                'description',
                'offer_type',
                'discount_value',
                'max_discount',
                'min_order_amount',
                'start_date',
                'end_date'
            )
            ->get();

        return response()->json([
            'status' => true,
            'data'   => $offers
        ], 200);
    }

    public function applyOffer(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthenticated'
            ], 401);
        }

        $request->validate([
            'id'           => 'required|integer|exists:offers,id',
            'order_amount' => 'required|numeric|min:1'
        ]);

        $today = Carbon::today()->toDateString();

        $offer = Offer::where('id', $request->id)
            ->where('status', 1)
            ->whereDate('start_date', '<=', $today)
            ->whereDate('end_date', '>=', $today)
            ->first();

        if (!$offer) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid or expired offer'
            ], 400);
        }

        if ($request->order_amount < $offer->min_order_amount) {
            return response()->json([
                'status' => false,
                'message' => 'Minimum order amount not met'
            ], 400);
        }

        // 💸 Discount calculation
        if (in_array($offer->offer_type, ['flat', 'flat_discount'])) {
            $discount = $offer->discount_value;
        } else {
            $discount = ($request->order_amount * $offer->discount_value) / 100;

            if (!empty($offer->max_discount)) {
                $discount = min($discount, $offer->max_discount);
            }
        }

        $discount = min($discount, $request->order_amount);
        // ✅ Save discount to cart
        $cart = Cart::where('user_id', $user->id)->first();

        if ($cart) {
            $cart->discount = round($discount, 2);
            $cart->total = max($request->order_amount - $discount, 0); // optional if you already use total
            $cart->save();
        }

        return response()->json([
            'status' => true,
            'message' => 'Offer applied successfully',
            'data' => [
                'id'            => $offer->id,
                'title'         => $offer->title,
                'discount'      => round($discount, 2),
                'final_amount'  => round(max($request->order_amount - $discount, 0), 2)
            ]
        ], 200);
    }
    public function removeOffer(Request $request)
    {
        $user = $request->user();

        $cart = Cart::where('user_id', $user->id)->first();

        if (!$cart) {
            return response()->json([
                'status' => false,
                'message' => 'Cart empty'
            ], 400);
        }

        $cart->update([
            'discount' => 0,
            'offer_id' => null,
            'total' => $cart->subtotal + $cart->tax_total
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Offer removed successfully'
        ]);
    }

    // public function removeOffer()
    // {
    //     return response()->json([
    //         'status' => true,
    //         'message' => 'offer removed successfully'
    //     ], 200);
    // }
    public function getAllCoupons(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthenticated'
            ], 401);
        }

        $coupons = Coupon::where('status', 1)
            ->whereDate('start_date', '<=', now())
            ->whereDate('end_date', '>=', now())
            ->select(
                'id',
                'title',
                'code',
                'description',
                'terms_condition',
                'discount_type',
                'discount_value',
                'min_amount',
                'max_usage',
                'start_date',
                'end_date'
            )
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'status' => true,
            'data' => $coupons
        ]);
    }
    public function applyCoupon(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthenticated'
            ], 401);
        }

        // ✅ VALIDATE ID
        $request->validate([
            'id' => 'required|exists:coupons,id'
        ]);

        $cart = Cart::where('user_id', $user->id)->first();

        if (!$cart) {
            return response()->json([
                'status' => false,
                'message' => 'Cart is empty'
            ], 400);
        }

        if ($cart->offer_id) {
            return response()->json([
                'status' => false,
                'message' => 'Remove offer before applying coupon'
            ], 400);
        }

        // ✅ FETCH COUPON BY ID
        $coupon = Coupon::where('id', $request->id)
            ->where('status', 1)
            ->whereDate('start_date', '<=', now())
            ->whereDate('end_date', '>=', now())
            ->first();

        if (!$coupon) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid or expired coupon'
            ], 400);
        }

        $cartItems = CartItem::with('product')
            ->where('cart_id', $cart->id)
            ->get();

        $eligibleSubtotal = 0;

        foreach ($cartItems as $item) {

            if ($coupon->product_id && $item->product_id == $coupon->product_id) {
                $eligibleSubtotal += $item->price * $item->qty;
            } elseif ($coupon->category_id && $item->product->category_id == $coupon->category_id) {
                $eligibleSubtotal += $item->price * $item->qty;
            } elseif (is_null($coupon->product_id) && is_null($coupon->category_id)) {
                $eligibleSubtotal += $item->price * $item->qty;
            }
        }

        if ($eligibleSubtotal < $coupon->min_amount) {

            $remainingAmount = round($coupon->min_amount - $eligibleSubtotal, 2);

            return response()->json([
                'status' => false,
                'message' => 'Add ₹' . $remainingAmount . ' more to apply this coupon',
                'data' => [
                    'min_amount' => (float) $coupon->min_amount,
                    'eligible_amount' => round($eligibleSubtotal, 2),
                    'remaining_amount' => $remainingAmount
                ]
            ], 400);
        }


        $discount = ($coupon->discount_type === 'flat')
            ? $coupon->discount_value
            : ($eligibleSubtotal * $coupon->discount_value) / 100;

        $discount = min($discount, $eligibleSubtotal);

        $cartTotal = $cart->subtotal + $cart->tax_total;

        $cart->update([
            'discount'    => round($discount, 2),
            'coupon_id'   => $coupon->id,
            'coupon_code' => $coupon->code,
            'offer_id'    => null,
            'total'       => max($cartTotal - $discount, 0)
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Coupon applied successfully',
            'data' => [
                'coupon_id' => $coupon->id,
                'coupon_code' => $coupon->code,
                'eligible_amount' => round($eligibleSubtotal, 2),
                'discount' => round($discount, 2),
                'payable_amount' => round($cart->total, 2)
            ]
        ], 200);
    }

    public function removeCoupon(Request $request)
    {
        $user = $request->user();

        $cart = Cart::where('user_id', $user->id)->first();

        if (!$cart) {
            return response()->json([
                'status' => false,
                'message' => 'Cart empty'
            ], 400);
        }

        $cart->update([
            'discount'    => 0,
            'coupon_id'   => null,
            'coupon_code' => null,
            'total'       => $cart->subtotal + $cart->tax_total
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Coupon removed successfully'
        ], 200);
    }
}
