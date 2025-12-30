<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Offer;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Facades\Log;

class OfferController extends Controller
{
    public function index()
    {
        $offers = Offer::with(['products', 'categories'])->get();
        return view('offers.index', compact('offers'));
    }

    public function create()
    {

        $products = Product::all();
        $categories = Category::all();
        return view('offers.create', compact('products', 'categories',))->with('mode', 'add');
    }

   public function store(Request $request)
{
    Log::info('Offer Store Request Received', [
        'user_id' => auth()->id(),
        'request_data' => $request->all()
    ]);

    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'category_id' => 'required|exists:categories,id',
        'product_id' => 'required|exists:products,id',
        'discount_type' => 'required|in:percentage,flat',
        'discount_value' => 'required|numeric|min:0',
        'start_date' => 'required|date',
        'end_date' => 'required|date|after_or_equal:start_date',
        'status' => 'required|boolean',
    ]);

    try {
        $offer = Offer::create([
            'title' => $validated['name'],   // 🔥 mapping fixed
            'category_id' => $validated['category_id'],
            'product_id' => $validated['product_id'],
            'discount_type' => $validated['discount_type'],
            'discount_value' => $validated['discount_value'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'status' => $validated['status'],
        ]);

        Log::info('Offer Created Successfully', [
            'offer_id' => $offer->id
        ]);

        return redirect()
            ->route('offers.index')
            ->with('success', 'Offer created successfully');

    } catch (\Exception $e) {

        Log::error('Offer Creation Failed', [
            'error' => $e->getMessage(),
            'line' => $e->getLine(),
            'file' => $e->getFile()
        ]);

        return back()->withInput()->with('error', 'Offer not saved');
    }
}


    public function edit(Offer $offer)
    {
        $products = Product::all();
        $categories = Category::all();
        return view('offers.edit', compact('offer', 'products', 'categories'));
    }

    public function update(Request $request, Offer $offer)
    {
        // 🔹 Log update request
        Log::info('Offer Update Request Received', [
            'user_id' => auth()->id(),
            'offer_id' => $offer->id,
            'request_data' => $request->all()
        ]);

        $validated = $request->validate([
            'title' => 'nullable|string|max:255',
            'discount_type' => 'nullable|in:percentage,flat',
            'discount_value' => 'nullable|numeric|min:0',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'product_ids' => 'nullable|array',
            'product_ids.*' => 'exists:products,id',
            'category_ids' => 'nullable|array',
            'category_ids.*' => 'exists:categories,id',
            'status' => 'nullable|boolean',
        ]);

        try {
            // 🔹 Keep old data for audit log
            $oldData = $offer->only([
                'title',
                'discount_type',
                'discount_value',
                'start_date',
                'end_date',
                'status'
            ]);

            // 🔹 Update Offer
            $offer->update($request->only([
                'title',
                'discount_type',
                'discount_value',
                'start_date',
                'end_date',
                'status'
            ]));

            Log::info('Offer Updated Successfully', [
                'offer_id' => $offer->id,
                'old_data' => $oldData,
                'new_data' => $offer->only(array_keys($oldData))
            ]);

            // 🔹 Sync Products
            if ($request->filled('product_ids')) {
                $offer->products()->sync($request->product_ids);

                Log::info('Offer Products Updated', [
                    'offer_id' => $offer->id,
                    'product_ids' => $request->product_ids
                ]);
            }

            // 🔹 Sync Categories
            if ($request->filled('category_ids')) {
                $offer->categories()->sync($request->category_ids);

                Log::info('Offer Categories Updated', [
                    'offer_id' => $offer->id,
                    'category_ids' => $request->category_ids
                ]);
            }

            return redirect()
                ->route('offers.index')
                ->with('success', 'Offer updated successfully');
        } catch (\Exception $e) {

            // ❌ Error log
            Log::error('Offer Update Failed', [
                'offer_id' => $offer->id,
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ]);

            return back()
                ->withInput()
                ->with('error', 'Something went wrong while updating offer');
        }
    }

    public function destroy(Offer $offer)
    {
        $offer->delete();
        return redirect()->route('offers.index')->with('success', 'Offer deleted successfully');
    }
    public function productsByCategory($id)
{
    return Product::where('category_id', $id)
        ->select('id', 'product_name')
        ->get();
}

}
