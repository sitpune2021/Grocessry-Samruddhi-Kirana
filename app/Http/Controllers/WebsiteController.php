<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\AboutPage;
use Illuminate\Http\Request;
use App\Models\Banner;
use App\Models\Brand;
use App\Models\Product;
use App\Models\Category;
use App\Models\ContactDetail;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\UserAddress;
use Illuminate\Support\Facades\Auth;

class WebsiteController extends Controller
{


    public function index(Request $request)
    {
        // banners
        $banners = Banner::latest()->get();

        // categories
        $categories = Category::orderBy('name')->get();
        $cate = Category::whereNull('deleted_at')
            ->whereHas('products', function ($q) {
                $q->whereNull('deleted_at');
            })
            ->with(['products' => function ($q) {
                $q->whereNull('deleted_at');
            }])
            ->orderBy('name')
            ->take(5)
            ->get();

        $saleproduct = Product::whereNull('deleted_at')
            ->whereHas('sale', function ($q) {
                $q->active()->online();
            })
            ->with('sale')
            ->latest()
            ->take(12)
            ->get();
        $brands = Brand::where('status', 1)->get();

        $categoriestop = Category::orderBy('id', 'DESC')->get();

        // category id
        $categoryId = $request->category_id;

        // ALL PRODUCTS (always)
        $allProducts = Product::whereNull('deleted_at')
            ->latest()
            ->paginate(12, ['*'], 'all_page');


        $latestPro = Product::whereNull('deleted_at')
            ->orderBy('id', 'DESC')
            ->take(12)
            ->get();


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
            'cate',
            'allProducts',
            'categoriestop',
            'categoryProducts',
            'latestPro',
            'brands',
            'saleproduct'
        ));
    }

    public function myOrders(Request $request)
    {
        $userId = Auth::id();

        $tab = $request->get('tab', 'orders'); // default orders

        $orders = Order::with('items.product')
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();

        $addresses = UserAddress::where('user_id', $userId)->get();

        return view('website.my_orders', compact('orders', 'addresses', 'tab'));
    }

    public function about()
    {
        // single about page data
        $about = AboutPage::first();

        return view('website.aboutpage', compact('about'));
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

        $products = $query->latest()->paginate(40)->withQueryString();

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
        $qty = $request->qty ?? 1;

        $userId = Auth::id() ?? session()->getId();

        // Get or create cart
        $cart = Cart::firstOrCreate([
            'user_id' => $userId,
        ]);

        // Use FINAL PRICE always
        $price = $product->final_price;

        // Check if product already exists
        $item = CartItem::where('cart_id', $cart->id)
            ->where('product_id', $product->id)
            ->first();

        if ($item) {
            $item->qty += $qty;
            $item->price = $price; // 🔥 ensure updated price
            $item->line_total = $item->qty * $price;
            $item->save();
        } else {
            CartItem::create([
                'cart_id'    => $cart->id,
                'product_id' => $product->id,
                'qty'        => $qty,
                'price'      => $price,              // ✅ final_price
                'line_total' => $price * $qty,        // ✅ final_price * qty
            ]);
        }

        // Recalculate totals
        $subtotal = CartItem::where('cart_id', $cart->id)->sum('line_total');
        $cartQty  = CartItem::where('cart_id', $cart->id)->sum('qty');

        // Update cart
        $cart->update([
            'quantity' => $cartQty,
            'subtotal' => $subtotal,
            'total'    => $subtotal,
        ]);

        return redirect()->route('cart')
            ->with('success', 'Product added to cart');
    }

    public function cart()
    {
        $userId = Auth::id() ?? session()->getId();

        $cart = Cart::with('items.product')
            ->where('user_id', $userId)
            ->first();

        return view('website.cart', compact('cart'));
    }

    public function removeItem($id)
    {
        $item = CartItem::findOrFail($id);

        $cart = Cart::where('id', $item->cart_id)->first();

        $item->delete();

        // Recalculate totals
        $subtotal = CartItem::where('cart_id', $cart->id)->sum('line_total');

        $cart->update([
            'subtotal' => $subtotal,
            'total'    => $subtotal,
        ]);

        return redirect()->back()->with('success', 'Item removed from cart.');
    }

    public function update(Request $request, $itemId)
    {
        $request->validate([
            'qty' => 'required|integer|min:1'
        ]);

        $userId = Auth::id() ?? session()->getId();

        $item = CartItem::where('id', $itemId)
            ->whereHas('cart', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            })
            ->firstOrFail();

        $item->qty = $request->qty;
        $item->line_total = $item->qty * $item->price;
        $item->save();

        $cart = $item->cart;

        $cart->subtotal = $cart->items()->sum('line_total');
        $cart->total = $cart->subtotal;
        $cart->save();

        return response()->json([
            'success'     => true,
            'qty'         => $item->qty,
            'line_total'  => number_format($item->line_total, 2),
            'cart_total'  => number_format($cart->total, 2),
            'subtotal'    => number_format($cart->subtotal, 2),
            'cart_count'  => $cart->items()->sum('qty'),
        ]);
    }

    public function productdetails($id)
    {
        $product = Product::findOrFail($id);

        // Same category ke related products (current product ko chhod kar)
        $relatedProducts = Product::where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->latest()
            ->take(8)   // jitne chaho utne
            ->get();

        return view('website.shop_detail', compact('product', 'relatedProducts'));
    }

    public function categoryProducts($slug)
    {
        $category = Category::where('slug', $slug)->firstOrFail();

        $products = Product::where('category_id', $category->id)
            ->whereNull('deleted_at')
            ->latest()
            ->paginate(12);

        return view('website.category-products', compact('category', 'products'));
    }
}
