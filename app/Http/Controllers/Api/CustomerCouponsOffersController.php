<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Offer;
use App\Models\Cart;

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


    public function removeOffer()
    {
        return response()->json([
            'status' => true,
            'message' => 'offer removed successfully'
        ], 200);
    }
}
