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
use App\Models\ProductBatch;
use App\Models\UserAddress;
use Illuminate\Support\Facades\Auth;

class WebsiteController extends Controller
{


    public function index(Request $request)
    {
        $dcId = session('dc_warehouse_id');
        // banners
        $banners = Banner::latest()->get();

        // categories
        $categories = Category::orderBy('name')->get();
        $cate = Category::whereNull('deleted_at')
            ->whereHas('products', fn($q) => $q->whereNull('deleted_at'))
            ->with(['products' => function ($q) use ($dcId) {
                $q->whereNull('deleted_at')
                    ->withSum(['batches as available_stock' => function ($b) use ($dcId) {
                        if ($dcId) {
                            $b->where('warehouse_id', $dcId)
                                ->where('quantity', '>', 0);
                        }
                    }], 'quantity');
            }])
            ->orderBy('name')
            ->take(5)
            ->get();

        $userId = Auth::id() ?? session()->getId();

        $cart = Cart::where('user_id', $userId)
            ->with('items')
            ->first();

        $cartItems = $cart ? $cart->items->keyBy('product_id') : collect();

        $saleproduct = Product::whereNull('deleted_at')
            ->whereHas('sale', fn($q) => $q->active()->online())
            ->with('sale')
            ->withSum(['batches as available_stock' => function ($q) use ($dcId) {
                if ($dcId) {
                    $q->where('warehouse_id', $dcId)->where('quantity', '>', 0);
                }
            }], 'quantity')
            ->latest()
            ->take(12)
            ->get();

        $brands = Brand::where('status', 1)->get();

        $categoriestop = Category::orderBy('id', 'DESC')->get();

        // category id
        $categoryId = $request->category_id;

        // ALL PRODUCTS (always)
        $allProducts = Product::whereNull('deleted_at')
            ->whereDoesntHave('sale', function ($q) {
                $q->active()->online();
            })
            ->withSum(['batches as available_stock' => function ($q) use ($dcId) {
                if ($dcId) {
                    $q->where('warehouse_id', $dcId)
                        ->where('quantity', '>', 0);
                }
            }], 'quantity')
            ->latest()
            ->paginate(12, ['*'], 'all_page');


        $latestPro = Product::whereNull('deleted_at')
            ->whereDoesntHave('sale', fn($q) => $q->active()->online())
            ->withSum(['batches as available_stock' => function ($q) use ($dcId) {
                if ($dcId) {
                    $q->where('warehouse_id', $dcId)->where('quantity', '>', 0);
                }
            }], 'quantity')
            ->latest()
            ->take(12)
            ->get();



        // CATEGORY PRODUCTS
        $categoryProducts = Product::whereNull('deleted_at')
            ->whereDoesntHave('sale', fn($q) => $q->active()->online())
            ->when($categoryId, fn($q) => $q->where('category_id', $categoryId))
            ->withSum(['batches as available_stock' => function ($q) use ($dcId) {
                if ($dcId) {
                    $q->where('warehouse_id', $dcId)->where('quantity', '>', 0);
                }
            }], 'quantity')
            ->latest()
            ->paginate(8, ['*'], 'cat_page')
            ->withQueryString();


        $selectedCategory = null;

        if ($categoryId) {
            $selectedCategory = Category::find($categoryId);
        }

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
            'saleproduct',
            'cartItems',
            'selectedCategory'

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
        $search    = $request->search;

        $categories = Category::orderBy('name')->get();

        $userId = Auth::id() ?? session()->getId();

        $cart = Cart::where('user_id', $userId)
            ->with('items')
            ->first();

        $cartItems = $cart ? $cart->items->keyBy('product_id') : collect();

        $query = Product::whereNull('deleted_at');

        // Category filter
        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        // Price filter
        if ($minPrice !== null && $maxPrice !== null) {
            $query->whereBetween('mrp', [$minPrice, $maxPrice]);
        }

        // Search filter
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $products = $query->latest()->paginate(40);

        // 🔥 IMPORTANT: If AJAX request → return only product list
        if ($request->ajax()) {
            return view('website.partials.product-list', compact('products', 'cartItems'))->render();
        }

        return view('website.shop', compact(
            'products',
            'categories',
            'categoryId',
            'minPrice',
            'maxPrice',
            'search',
            'cartItems'
        ));
    }



    public function liveSearch(Request $request)
    {
        $products = Product::where('name', 'like', '%' . $request->search . '%')
            ->limit(10)
            ->get();

        return response()->json($products);
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
            'qty' => 'required|integer|min:1'
        ]);

        $product = Product::findOrFail($request->product_id);
        $qty = $request->qty;
        $dcId = session('dc_warehouse_id');

        if (!$dcId) {
            return response()->json([
                'success' => false,
                'message' => 'Delivery location not selected'
            ], 422);
        }

        $availableQty = ProductBatch::where('product_id', $product->id)
            ->where('warehouse_id', $dcId)
            ->sum('quantity');

        if ($availableQty < $qty) {
            return response()->json([
                'success' => false,
                'message' => "Sorry! Only {$availableQty} items are currently available."

            ], 422);
        }

        $userId = Auth::id() ?? session()->getId();

        $cart = Cart::firstOrCreate([
            'user_id' => $userId,
        ]);

        $price = $product->final_price;

        $item = CartItem::where('cart_id', $cart->id)
            ->where('product_id', $product->id)
            ->first();

        if ($item) {
            $item->qty = $qty;   // ✅ overwrite
            $item->line_total = $qty * $price;
            $item->save();
        } else {
            $item = CartItem::create([
                'cart_id' => $cart->id,
                'product_id' => $product->id,
                'qty' => $qty,
                'price' => $price,
                'line_total' => $price * $qty,
            ]);
        }

        $cart->subtotal = $cart->items()->sum('line_total');
        $cart->quantity = $cart->items()->sum('qty');
        $cart->total = $cart->subtotal;
        $cart->save();

        return response()->json([
            'success' => true,
            'item_id' => $item->id,
            'qty' => $item->qty,
            'cart_count' => $cart->quantity
        ]);
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

        $dcId = session('dc_warehouse_id');

        if (!$dcId) {
            return response()->json([
                'success' => false,
                'message' => 'Delivery location not selected'
            ], 422);
        }

        $userId = Auth::id() ?? session()->getId();

        $item = CartItem::where('id', $itemId)
            ->whereHas('cart', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            })
            ->firstOrFail();

        //REAL STOCK CHECK (AGAIN)
        $availableQty = ProductBatch::where('product_id', $item->product_id)
            ->where('warehouse_id', $dcId)
            ->sum('quantity');

        if ($request->qty > $availableQty) {
            return response()->json([
                'success' => false,
                'out_of_stock' => true,
                'available_qty' => $availableQty,
                'message' => "Only {$availableQty} items available"
            ], 422);
        }

        // safe to update
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
        $dcId = session('dc_warehouse_id');

        $product = Product::findOrFail($id);

        $availableStock = ProductBatch::where('product_id', $id)
            ->where('warehouse_id', $dcId)
            ->sum('quantity');

        $userId = Auth::id() ?? session()->getId();

        $cart = Cart::where('user_id', $userId)->first();

        $cartItem = $cart
            ? CartItem::where('cart_id', $cart->id)
            ->where('product_id', $id)
            ->first()
            : null;

        $cartQty = $cartItem ? $cartItem->qty : 0;

        $relatedProducts = Product::where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->latest()
            ->take(8)
            ->get();

        return view(
            'website.shop_detail',
            compact('product', 'relatedProducts', 'availableStock', 'cartQty')
        );
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
