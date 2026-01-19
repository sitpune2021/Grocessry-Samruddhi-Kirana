<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Offer;
use App\Models\Product;
use App\Models\Category;
use App\Models\Warehouse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class OfferController extends Controller
{
    public function index()
    {

        return view('offers.index', [
            'offers' =>  Offer::where('status', 1)->orderBy('created_at', 'desc')
                ->paginate(20)
        ]);
    }

    public function create()
    {
        return view('offers.create', [
            'mode' => 'add',
            'products' => Product::select('id', 'name')
                ->orderBy('name', 'asc')
                ->get(),
        ]);
    }

    public function store(Request $request)
    {

        Log::info('Offer store request received', [
            'title'        => $request->title,
            'offer_type'   => $request->offer_type,
            'start_date'   => $request->start_date,
            'end_date'     => $request->end_date,
            'status'       => $request->status,
            'user_id'      => Auth::id(),
            'ip'           => $request->ip(),
        ]);

        $validated = $request->validate([
            'title'              => 'required|string|max:255',
            'description'        => 'nullable|string',
            'offer_type'         => 'required|in:flat,percentage,buy_x_get_y',
            'discount_value'     => 'nullable|numeric|min:0',
            'max_discount'       => 'nullable|numeric|min:0',
            'min_order_amount'   => 'required|numeric|min:0',
            'start_date'         => 'required|date',
            'end_date'           => 'required|date|after_or_equal:start_date',
            'status'             => 'required|boolean',

            // Buy X Get Y
            'buy_quantity'       => 'required_if:offer_type,buy_x_get_y|integer|min:1',
            'get_quantity'       => 'required_if:offer_type,buy_x_get_y|integer|min:1',
        ]);

        try {

            Log::info('Offer validation passed', [
                'validated_data' => $validated,
            ]);


            $offer = Offer::create($validated);

            Log::info('Offer created successfully', [
                'offer_id'   => $offer->id,
                'offer_type' => $offer->offer_type,
                'user_id'    => Auth::id(),
            ]);

            return redirect()
                ->route('offers.index')
                ->with('success', 'Offer created successfully');
        } catch (\Throwable $e) {


            Log::error('Offer creation failed', [
                'message'   => $e->getMessage(),
                'file'      => $e->getFile(),
                'line'      => $e->getLine(),
                'request'   => $request->except(['_token']),
                'user_id'   => Auth::id(),
                'ip'        => $request->ip(),
            ]);

            return back()
                ->withInput()
                ->with('error', 'Failed to create offer. Please try again.');
        }
    }

    public function show($id)
    {
        $offer = Offer::findOrFail($id);

        return view('offers.create', [
            'offer' => $offer,
            'mode'  => 'view',
        ]);
    }


    public function edit($id)
    {
        $offer = Offer::findOrFail($id);
        return view('offers.create', [
            'offer' => $offer,
            'mode'  => 'edit',
        ]);
    }

public function update(Request $request, $id)
{
    Log::info('Offer update request received', [
        'offer_id' => $id,
    ]);

    try {
        $offer = Offer::findOrFail($id);

        $validated = $request->validate([
            'title'              => 'required|string|max:255',
            'description'        => 'nullable|string',
            'offer_type'         => 'required|in:flat,percentage,buy_x_get_y',
            'discount_value'     => 'nullable|numeric|min:0',
            'max_discount'       => 'nullable|numeric|min:0',
            'min_order_amount'   => 'required|numeric|min:0',
            'start_date'         => 'required|date',
            'end_date'           => 'required|date|after_or_equal:start_date',
            'status'             => 'required|boolean',

            // Buy X Get Y (only if selected)
            'buy_quantity'       => 'required_if:offer_type,buy_x_get_y|integer|min:1',
            'get_quantity'       => 'required_if:offer_type,buy_x_get_y|integer|min:1',
        ]);

        $offer->update($validated);

        Log::info('Offer updated successfully', [
            'offer_id' => $offer->id,
             'user_id'  => Auth::id(),
            
        ]);

        return redirect()
            ->route('offers.index')
            ->with('success', 'Offer updated successfully');

    } catch (\Throwable $e) {

        Log::error('Offer update failed', [
            'offer_id' => $id,
            'message'  => $e->getMessage(),
            'file'     => $e->getFile(),
            'line'     => $e->getLine(),
           
        ]);

        return back()
            ->withInput()
            ->with('error', 'Failed to update offer');
    }
}

    public function destroy($id)
{
    try {
        $offer = Offer::findOrFail($id);
        $offer->delete(); 

        return redirect()
            ->route('offers.index')
            ->with('success', 'Offer deleted successfully');
    } catch (\Throwable $e) {
        return back()->with('error', 'Failed to delete offer');
    }
}

}
