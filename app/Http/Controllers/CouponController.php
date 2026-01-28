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

        Log::info('Offer Store Request Received', $request->all());

        $validated = $request->validate([
            'code' => 'required|string|unique:coupons,code', // ✅ correct table name
            'title' => 'nullable|string',
            'discount_type' => 'required|in:percentage,flat',
            'discount_value' => [
                'required',
                'numeric',
                function ($attribute, $value, $fail) use ($request) {
                    if ($request->discount_type === 'percentage' && $value > 100) {
                        $fail('Percentage discount cannot be more than 100.');
                    }
                }
            ],
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'min_amount' => 'required|numeric|min:0',
            'max_usage' => 'required|integer|min:1',
            'description' => 'nullable|string',
            'terms_condition' => 'nullable|string',
            'status' => 'required|in:0,1',
        ]);

        $coupon = \App\Models\Coupon::create($validated);

        Log::info('Coupon Created', ['coupon_id' => $coupon->id]);

        return redirect()->route('coupons.index')
            ->with('success', 'Coupon created successfully');
    }
    public function show($id)
    {
        $offer = Coupon::findOrFail($id);
        return view('coupons.create', compact('offer'))->with('mode', 'view');
    }

    public function edit(Coupon $coupon)
    {
        $products = Product::all();
        $categories = Category::all();

        return view('coupons.create', [
            'offer' => $coupon,
            'products' => $products,
            'categories' => $categories,
            'mode' => 'edit'
        ]);
    }
    public function update(Request $request, Coupon $coupon)
    {
        Log::info('Offer Update Request Received', [
            'user_id' => Auth::id(),
            'offer_id' => $coupon->id,
            'request_data' => $request->all()
        ]);

        $validated = $request->validate([
            'code' => 'required|string|unique:coupons,code,' . $coupon->id,
            'title' => 'nullable|string',
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

        $coupon->update($validated);

        return redirect()
            ->route('coupons.index')
            ->with('success', 'Coupon updated successfully');
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
