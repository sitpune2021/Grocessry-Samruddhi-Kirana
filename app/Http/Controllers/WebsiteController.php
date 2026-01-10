<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Banner;
use App\Models\Product;
use App\Models\Category;
use App\Models\ContactDetail;

class WebsiteController extends Controller
{
    
    public function index(Request $request)
    {
        // banners
        $banners = Banner::latest()->get();

        // categories
        $categories = Category::orderBy('name')->get();

        // category id
        $categoryId = $request->category_id;

        // ALL PRODUCTS (always)
        $allProducts = Product::whereNull('deleted_at')
            ->latest()
            ->paginate(8, ['*'], 'all_page');

        // CATEGORY PRODUCTS
        $categoryProducts = Product::whereNull('deleted_at')
            ->when($categoryId, function ($q) use ($categoryId) {
                $q->where('category_id', $categoryId);
            })
            ->latest()
            ->paginate(8, ['*'], 'cat_page')
            ->withQueryString();

        return view('website.index', compact(
            'banners',
            'categories',
            'categoryId',
            'allProducts',
            'categoryProducts'
        ));
    }

    public function contact()
    {
        return view('website.contact');
    }

    public function storeContact(Request $request)
    {
        $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => 'required|email|max:255',
            'message' => 'required|string',
        ]);

        ContactDetail::create([
            'name'    => $request->name,
            'email'   => $request->email,
            'message' => $request->message,
        ]);

        return back()->with('success', 'Thank you! Your message has been sent.');
    }

    public function shop(Request $request)
    {
        $categoryId = $request->category_id;
        $maxPrice   = $request->price;

        // categories for sidebar
        $categories = Category::orderBy('name')->get();

        // products query
        $productsQuery = Product::whereNull('deleted_at');

        // category filter
        if ($categoryId) {
            $productsQuery->where('category_id', $categoryId);
        }

        // price filter (MRP based)
        if ($maxPrice) {
            $productsQuery->where('mrp', '<=', $maxPrice);
        }

        // pagination 12 (3 per row x 4 rows)
        $products = $productsQuery
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('website.shop', compact(
            'products',
            'categories',
            'categoryId',
            'maxPrice'
        ));
    }

    public function shopFilter(Request $request)
    {
        $categoryId = $request->category_id;
        $page       = $request->page ?? 1;

        $query = Product::whereNull('deleted_at');

        if ($categoryId && $categoryId !== 'all') {
            $query->where('category_id', $categoryId);
        }

        $products = $query
            ->latest()
            ->paginate(12, ['*'], 'page', $page);

        return view('website.partials.product-list', compact('products'))->render();
    }


}
