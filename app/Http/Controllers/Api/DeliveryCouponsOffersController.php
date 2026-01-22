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
            'coupon_code' => 'required|string',
            'order_amount' => 'required|numeric|min:1'
        ]);

        $today = Carbon::today()->toDateString();

        $offer = Offer::where('title', $request->coupon_code)
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


        return response()->json([
            'status' => true,
            'message' => 'Coupon applied successfully',
            'data' => [
                'offer_id'     => $offer->id,
                'offer_title'  => $offer->title,
                'discount'     => round($discount, 2),
                'final_amount' => max($request->order_amount - $discount, 0)
            ]
        ], 200);
    }

    public function removeOffer()
    {
        return response()->json([
            'status' => true,
            'message' => 'offer removed successfully'
        ], 200);
    }
}
