<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Banner;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\Category;
use App\Models\ContactDetail;
use Illuminate\Support\Facades\Auth;
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

    // public function shop(Request $request)
    // {
    //     $categoryId = $request->category_id;
    //     $maxPrice   = $request->price;

    //     // categories for sidebar
    //     $categories = Category::orderBy('name')->get();

    //     // products query
    //     $productsQuery = Product::whereNull('deleted_at');

    //     // category filter
    //     if ($categoryId) {
    //         $productsQuery->where('category_id', $categoryId);
    //     }

    //     // price filter (MRP based)
    //     if ($maxPrice) {
    //         $productsQuery->where('mrp', '<=', $maxPrice);
    //     }

    //     // pagination 12 (3 per row x 4 rows)
    //     $products = $productsQuery
    //         ->latest()
    //         ->paginate(12)
    //         ->withQueryString();

    //     return view('website.shop', compact(
    //         'products',
    //         'categories',
    //         'categoryId',
    //         'maxPrice'
    //     ));
    // }

    // public function shopFilter(Request $request)
    // {
    //     $categoryId = $request->category_id;
    //     $page       = $request->page ?? 1;

    //     $query = Product::whereNull('deleted_at');

    //     if ($categoryId && $categoryId !== 'all') {
    //         $query->where('category_id', $categoryId);
    //     }

    //     $products = $query
    //         ->latest()
    //         ->paginate(12, ['*'], 'page', $page);

    //     return view('website.partials.product-list', compact('products'))->render();
    // }  

    public function shop(Request $request)
    {
        $categoryId = $request->category_id;
        $minPrice  = $request->min_price;
        $maxPrice  = $request->max_price;

        $categories = Category::orderBy('name')->get();

        $query = Product::whereNull('deleted_at');

        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        if ($minPrice !== null && $maxPrice !== null) {
            $query->whereBetween('mrp', [$minPrice, $maxPrice]);
        }

        $products = $query->latest()->paginate(12)->withQueryString();

        return view('website.shop', compact(
            'products',
            'categories',
            'categoryId',
            'minPrice',
            'maxPrice'
        ));
    }

    public function shopFilter(Request $request)
    {
        $categoryId = $request->category_id;
        $minPrice  = $request->min_price;
        $maxPrice  = $request->max_price;
        $page      = $request->page ?? 1;

        $query = Product::whereNull('deleted_at');

        if ($categoryId && $categoryId !== 'all') {
            $query->where('category_id', $categoryId);
        }

        if ($minPrice !== null && $maxPrice !== null) {
            $query->whereBetween('mrp', [$minPrice, $maxPrice]);
        }

        $products = $query
            ->latest()
            ->paginate(12, ['*'], 'page', $page);

        return view('website.partials.product-list', compact('products'))->render();
    }

    public function addToCart(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        $product = Product::findOrFail($request->product_id);

        $userId = Auth::id() ?? session()->getId();

        // Get or create cart
        $cart = Cart::firstOrCreate([
            'user_id' => $userId,
        ]);

        // Check if product already in cart
        $item = CartItem::where('cart_id', $cart->id)
            ->where('product_id', $product->id)
            ->first();

        if ($item) {
            $item->qty += 1;
            $item->line_total = $item->qty * $item->price;
            $item->save();
        } else {
            CartItem::create([
                'cart_id'    => $cart->id,
                'product_id' => $product->id,
                'qty'        => 1,
                'price'      => $product->mrp,
                'line_total' => $product->mrp,
            ]);
        }

        // Recalculate cart totals
        $subtotal = CartItem::where('cart_id', $cart->id)->sum('line_total');

        $cart->update([
            'subtotal' => $subtotal,
            'total'    => $subtotal,
        ]);

        return redirect()->route('cart')->with('success', 'Product added to cart');
    }


}
