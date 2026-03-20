<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Cart;
use App\Models\Coupon;
use App\Models\CartItem;
use Illuminate\Support\Facades\DB;

class CustomerCouponsOffersController extends Controller
{
    /**
     * Get all currently active coupons
     */
    public function getAllCoupons(Request $request)
    {
        $today = Carbon::today();

        $coupons = Coupon::where('status', 1)
            ->whereDate('start_date', '<=', $today)
            ->whereDate('end_date', '>=', $today)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($coupon) {
                return [
                    'id'            => $coupon->id,
                    'title'         => $coupon->title,
                    'code'          => $coupon->code,
                    'description'   => $coupon->description,
                    'discount_text' => $coupon->discount_type == 'flat'
                        ? '₹' . $coupon->discount_value . ' OFF'
                        : $coupon->discount_value . '% OFF',
                    'min_amount'    => (float)$coupon->min_amount,
                    'valid_till'    => Carbon::parse($coupon->end_date)->format('d M Y'),
                ];
            });

        return response()->json([
            'status' => true,
            'data'   => $coupons
        ]);
    }

    /**
     * Apply Coupon to Cart
     */
    public function applyCoupon(Request $request)
    {
        // 1. Change validation to expect 'id'
        $request->validate([
            'id' => 'required|integer|exists:coupons,id',
        ]);

        $user = auth()->user();
        $cart = Cart::with('items')->where('user_id', $user->id)->first();

        if (!$cart || $cart->items->isEmpty()) {
            return response()->json(['status' => false, 'message' => 'Cart is empty'], 400);
        }

        // 2. Fetch coupon by ID and check dates
        $today = Carbon::today();
        $coupon = Coupon::where('id', $request->id) // Query by ID
            ->where('status', 1)
            ->whereDate('start_date', '<=', $today)
            ->whereDate('end_date', '>=', $today)
            ->first();

        if (!$coupon) {
            return response()->json(['status' => false, 'message' => 'This coupon is no longer available'], 404);
        }

        // 3. Minimum amount check (using your 'min_amount' column)
        if ($cart->subtotal < $coupon->min_amount) {
            return response()->json([
                'status' => false,
                'message' => 'Minimum order amount for this coupon is ₹' . $coupon->min_amount
            ], 400);
        }

        // 4. Calculate Discount
        $discount = 0;
        if ($coupon->discount_type == 'flat') {
            $discount = $coupon->discount_value;
        } else {
            $discount = ($cart->subtotal * $coupon->discount_value) / 100;
            if (!empty($coupon->max_discount)) {
                $discount = min($discount, $coupon->max_discount);
            }
        }

        // Prevent discount from exceeding subtotal
        $discount = min($discount, $cart->subtotal);

        // 5. Update Cart with the new totals
        $cart->update([
            'coupon_id'   => $coupon->id,
            'coupon_code' => $coupon->code,
            'discount'    => round($discount, 2),
            'total'       => max(($cart->subtotal + ($cart->tax_total ?? 0)) - $discount, 0)
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Coupon applied successfully',
            'data' => [
                'coupon_code' => $coupon->code,
                'discount'    => $cart->discount,
                'new_total'   => $cart->total
            ]
        ]);
    }

    /**
     * Remove Coupon
     */
    public function removeCoupon(Request $request)
    {
        $user = $request->user();
        $cart = Cart::where('user_id', $user->id)->first();

        if ($cart) {
            $subtotal = $cart->subtotal;
            $tax = $cart->tax_total ?? 0;
            $delivery = $cart->delivery_charge ?? 0;

            $cart->update([
                'discount'    => 0,
                'coupon_id'   => null,
                'coupon_code' => null,
                'total'       => $subtotal + $tax + $delivery
            ]);
        }

        return response()->json([
            'status'  => true,
            'message' => 'Coupon removed successfully'
        ]);
    }
}
