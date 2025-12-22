<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Retailer;
use App\Models\RetailerPricing;
use App\Models\Category;
use App\Models\Product;


class RetailerPricingController extends Controller
{

    public function index()
    {
        $pricings = RetailerPricing::with([
                'retailer:id,name',
                'category:id,name',
                'product:id,name'
            ])
            ->latest()
            ->paginate(10);

        return view('retailer-pricing.index', compact('pricings'));
    }
  
    public function create()
    {
        $retailers  = Retailer::where('is_active', 1)->get();
        $categories = Category::get();

        return view('retailer-pricing.form', compact('retailers', 'categories'));
    }
 
    public function getProductsByCategory(Category $category)
    {
        return response()->json(
            $category->products()
                ->select('id', 'name')
                ->get()
        );
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'retailer_id'      => 'required|exists:retailers,id',
            'category_id'      => 'required|exists:categories,id', // ✅
            'product_id'       => 'required|exists:products,id',
            'base_price'       => 'required|numeric|min:0',
            'discount_percent' => 'nullable|numeric|min:0|max:100',
            'discount_amount'  => 'nullable|numeric|min:0',
            'effective_from'   => 'required|date',
        ]);

        /**
         * 🔐 Duplicate check
         */
        $exists = RetailerPricing::where([
            'retailer_id' => $data['retailer_id'],
            'category_id' => $data['category_id'], // ✅
            'product_id'  => $data['product_id'],
        ])->whereDate('effective_from', $data['effective_from'])
        ->exists();

        if ($exists) {
            return back()
                ->withErrors(['product_id' => 'Pricing already exists'])
                ->withInput();
        }

        /**
         * 🧮 Calculate discount amount
         */
        if (
            empty($data['discount_amount']) &&
            !empty($data['discount_percent'])
        ) {
            $data['discount_amount'] =
                ($data['base_price'] * $data['discount_percent']) / 100;
        }

        /**
         * 💰 Effective price
         */
        $effectivePrice =
            $data['base_price'] - ($data['discount_amount'] ?? 0);

        RetailerPricing::create([
            'retailer_id'      => $data['retailer_id'],
            'category_id'      => $data['category_id'],   // ✅ STORED
            'product_id'       => $data['product_id'],
            'base_price'       => $data['base_price'],
            'discount_percent' => $data['discount_percent'] ?? 0,
            'discount_amount'  => $data['discount_amount'] ?? 0,
            'effective_price'  => $effectivePrice,
            'effective_from'   => $data['effective_from'],
            'is_active'        => 1,
        ]);

        return redirect()
            ->route('retailer-pricing.index')
            ->with('success', 'Retailer pricing saved successfully');
    }


    public function edit(RetailerPricing $pricing) { }

    public function update(Request $request, RetailerPricing $pricing) { }

    public function destroy(RetailerPricing $pricing) { }

    public function bulkUpload(Request $request) { }
}

