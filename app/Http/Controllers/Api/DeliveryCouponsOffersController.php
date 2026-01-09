<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Offer;

class DeliveryCouponsOffersController extends Controller
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
                'code',
                'description',
                'discount_type',
                'discount_value',
                'min_amount',
                'terms_condition'
            )
            ->get();

        return response()->json([
            'status' => true,
            'data'   => $offers
        ], 200);
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

        $request->validate([
            'coupon_code' => 'required|string',
            'order_amount' => 'required|numeric|min:1'
        ]);

        $today = Carbon::today()->toDateString();

        $offer = Offer::where('code', $request->coupon_code)
            ->where('status', 1)
            ->whereDate('start_date', '<=', $today)
            ->whereDate('end_date', '>=', $today)
            ->first();

        if (!$offer) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid or expired coupon'
            ], 400);
        }

        if ($request->order_amount < $offer->min_amount) {
            return response()->json([
                'status' => false,
                'message' => 'Minimum order amount not met'
            ], 400);
        }

        // 💸 Discount calculation
        if ($offer->discount_type === 'flat') {
            $discount = $offer->discount_value;
        } else { // percentage
            $discount = ($request->order_amount * $offer->discount_value) / 100;
        }

        return response()->json([
            'status' => true,
            'message' => 'Coupon applied successfully',
            'data' => [
                'coupon_id'   => $offer->id,
                'coupon_code' => $offer->code,
                'discount'    => round($discount, 2),
                'final_amount' => max($request->order_amount - $discount, 0)
            ]
        ], 200);
    }
    public function removeCoupon()
    {
        return response()->json([
            'status' => true,
            'message' => 'Coupon removed successfully'
        ], 200);
    }
}
