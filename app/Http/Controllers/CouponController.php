<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Coupon;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CouponController extends Controller
{
    public function index()
    {
        $offers = Coupon::with(['product', 'category'])->paginate(10);
        return view('coupons.index', compact('offers'));
    }
    public function create()
    {

        $products = Product::all();
        $categories = Category::all();
        return view('coupons.create', compact('products', 'categories',))->with('mode', 'add');
    }

    public function store(Request $request)
    {
        // convert "all" to null
        if ($request->category_id === 'all') {
            $request->merge(['category_id' => null]);
        }

        if ($request->product_id === 'all') {
            $request->merge(['product_id' => null]);
        }

        Log::info('Offer Store Request Received', $request->all());

        $validated = $request->validate([
            'code' => 'required|string|unique:offers,code',
            'title' => 'nullable|string',
            'category_id' => 'nullable|exists:categories,id',
            'product_id' => 'nullable|exists:products,id',
            'discount_type' => 'required|in:percentage,flat',
            'discount_value' => 'required|numeric|min:0',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'min_amount' => 'required|numeric|min:0',
            'max_usage' => 'required|integer|min:1',
            'description' => 'nullable|string',
            'terms_condition' => 'nullable|string',
            'status' => 'required|in:0,1',
        ]);

        $offer = Coupon::create($validated);

        Log::info('Offer Created', ['offer_id' => $offer->id]);

        return redirect()->route('coupons.index')->with('success', 'Offer created successfully');
    }



    public function show(Coupon $offer)
    {
        $products = Product::all();
        $categories = Category::all();
        return view('coupons.create', compact('offer', 'products', 'categories'))->with('mode', 'view');
    }

    public function edit(Coupon $offer)
    {
        $products = Product::all();
        $categories = Category::all();
        return view('coupons.create', compact('offer', 'products', 'categories'))->with('mode', 'edit');
    }

    public function update(Request $request, Coupon $offer)
    {
        Log::info('Offer Update Request Received', [
            'user_id' => Auth::id(),
            'offer_id' => $offer->id,
            'request_data' => $request->all()
        ]);

        $validated = $request->validate([
            'code' => 'required|string|unique:offers,code,' . $offer->id,
            'title' => 'nullable|string',
            'category_id' => 'nullable|exists:categories,id',
            'product_id' => 'nullable|exists:products,id',
            'discount_type' => 'required|in:percentage,flat',
            'discount_value' => 'required|numeric|min:0',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'min_amount' => 'required|numeric|min:0',
            'max_usage' => 'required|integer|min:1',
            'description' => 'nullable|string',
            'terms_condition' => 'nullable|string',
            'status' => 'required|boolean',
        ]);

        try {
            // 🔹 Store old data for logs
            $oldData = $offer->only(array_keys($validated));

            // 🔹 Update offer (ONLY validated fields)
            $offer->update($validated);

            Log::info('Offer Updated Successfully', [
                'offer_id' => $offer->id,
                'old_data' => $oldData,
                'new_data' => $offer->only(array_keys($validated))
            ]);

            return redirect()
                ->route('coupons.index')
                ->with('success', 'Offer updated successfully');
        } catch (\Exception $e) {

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

    public function destroy(Coupon $offer)
    {
        $offer->delete();
        return redirect()->route('coupons.index')->with('success', 'Offer deleted successfully');
    }
    public function productsByCategory($categoryId)
    {
        return Product::where('category_id', $categoryId)
            ->select('id', 'name')
            ->get();
    }
}
