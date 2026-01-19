<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RetailerOffer;
use App\Models\User;

class RetailerOfferController extends Controller
{
    public function index()
    {
        // Only show offers assigned to Retailers
        $offers = RetailerOffer::with('user')
            ->whereHas('user', fn($q) => $q->where('role_id', 7)) // role_id 7 = Retailer
            ->paginate(10);

        return view('retailer-offer.index', compact('offers'));
    }

    public function create()
    {
        // Only users with role 'Retailer'
        $retailers = User::where('role_id', 7)->get();

        return view('retailer-offer.create', [
            'mode' => 'add',
            'retailers' => $retailers
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id', // assign offer to a retailer user
            'offer_name' => 'required|string|max:255',
            'discount_type' => 'required|string',
            'discount_value' => 'required|numeric',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'status' => 'required|in:0,1',
        ]);

        RetailerOffer::create($request->all());

        return redirect()->route('retailer-offers.index')
            ->with('success', 'Retailer Offer added successfully!');
    }

    public function edit($id)
    {
        $offer = RetailerOffer::findOrFail($id);

        $retailers = User::where('role_id', 7)->get();

        return view('retailer-offer.create', [
            'mode' => 'edit',
            'offer' => $offer,
            'retailers' => $retailers
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'offer_name' => 'required|string|max:255',
            'discount_type' => 'required|string',
            'discount_value' => 'required|numeric',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'status' => 'required|in:0,1',
        ]);

        $offer = RetailerOffer::findOrFail($id);
        $offer->update($request->all());

        return redirect()->route('retailer-offers.index')
            ->with('success', 'Retailer Offer updated successfully!');
    }

    public function destroy($id)
    {
        $offer = RetailerOffer::findOrFail($id);
        $offer->delete();

        return redirect()->route('retailer-offers.index')
            ->with('success', 'Retailer Offer deleted successfully!');
    }
}
