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
use App\Models\Coupon;
use App\Models\Order;
use App\Models\ProductBatch;
use App\Models\UserAddress;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

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
            ->whereHas('products', function ($q) {
                $q->whereNull('deleted_at')
                    ->whereDoesntHave('sale', function ($sale) {
                        $sale->active()->online();
                    });
            })
            ->with(['products' => function ($q) use ($dcId) {
                $q->whereNull('deleted_at')
                    ->whereDoesntHave('sale', function ($sale) {
                        $sale->active()->online();
                    })
                    ->withSum(['batches as available_stock' => function ($q) use ($dcId) {
                        if ($dcId) {
                            $q->where('warehouse_id', $dcId);
                        }
                    }], 'quantity');
            }])
            ->orderBy('name')
            ->take(5)
            ->get();

        $userId = Auth::id();

        $cart = Cart::where('user_id', $userId)
            ->with('items')
            ->first();

        $cartItems = $cart ? $cart->items->keyBy('product_id') : collect();

        $saleproduct = Product::whereNull('deleted_at')
            ->whereHas('sale', fn($q) => $q->active()->online())
            ->with('sale')
            ->withSum(['batches as available_stock' => function ($q) use ($dcId) {
                if ($dcId) {
                    $q->where('warehouse_id', $dcId);
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
                    $q->where('warehouse_id', $dcId);
                }
            }], 'quantity')
            ->latest()
            ->paginate(12, ['*'], 'all_page');

        $latestPro = Product::withStock($dcId)
            ->whereNull('deleted_at')
            ->whereDoesntHave('sale', function ($q) {
                $q->active()->online();
            })
            ->latest()
            ->take(12)
            ->get();



        // CATEGORY PRODUCTS
        $categoryProducts = Product::whereNull('deleted_at')
            ->whereDoesntHave('sale', fn($q) => $q->active()->online())
            ->when($categoryId, fn($q) => $q->where('category_id', $categoryId))
            ->withSum(['batches as available_stock' => function ($q) use ($dcId) {
                if ($dcId) {
                    $q->where('warehouse_id', $dcId);
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

        $tab = $request->get('tab', 'orders');

        $orders = Order::with('items.product')
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->paginate(3) //  change here
            ->withQueryString(); //  keeps ?tab=orders

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

        $userId = Auth::id();

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

        $dcId = session('dc_warehouse_id');

        $products = $query
            ->with(['sale' => function ($q) {
                $q->active()->online();
            }])
            ->withStock($dcId)
            ->latest()
            ->paginate(40);

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
        if (!Auth::check()) {
            return response()->json(['success' => false, 'message' => 'Please login first'], 401);
        }

        $request->validate([
            'product_id' => 'required|exists:products,id',
            'qty' => 'required|integer|min:1'
        ]);

        $productId = $request->product_id;
        $qty = (int)$request->qty;
        $dcId = session('dc_warehouse_id');

        if (!$dcId) {
            return response()->json(['success' => false, 'message' => 'Delivery location not selected'], 422);
        }

        $availableQty = ProductBatch::where('product_id', $productId)
            ->where('warehouse_id', $dcId)
            ->sum('quantity');

        if ($availableQty <= 0) {
            return response()->json(['success' => false, 'message' => 'Product out of stock'], 422);
        }

        $userId = Auth::id();
        $cart = Cart::firstOrCreate(['user_id' => $userId]);

        $existingItem = CartItem::where('cart_id', $cart->id)
            ->where('product_id', $productId)
            ->first();

        $newQty = $existingItem ? $existingItem->qty + $qty : $qty;

        if ($newQty > $availableQty) {
            return response()->json(['success' => false, 'message' => "Only {$availableQty} items available in stock"], 422);
        }

        $product = Product::with('sale')->findOrFail($productId);

        $price = $product->sale && $product->sale->active()->online()->exists()
            ? $product->sale->sale_price
            : $product->final_price;

        $cartItem = CartItem::updateOrCreate(
            ['cart_id' => $cart->id, 'product_id' => $productId],
            [
                'qty' => $newQty,
                'price' => $price,
                'line_total' => $price * $newQty
            ]
        );

        $cart->update([
            'subtotal' => $cart->items()->sum('line_total'),
            'quantity' => $cart->items()->sum('qty'),
            'total'    => $cart->items()->sum('line_total')
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Product added to cart',
            'cart_count' => $cart->quantity,
            'cart_total' => number_format($cart->total, 2),
            'qty' => $cartItem->qty, // FINAL FIX
            'cart_item_id' => $cartItem->id
        ]);
    }

    public function cart()
    {
        $userId = Auth::id();
        $dcId = session('dc_warehouse_id');

        $cart = Cart::with(['items.product' => function ($q) use ($dcId) {
            $q->withSum(['batches as available_stock' => function ($q) use ($dcId) {
                if ($dcId) {
                    $q->where('warehouse_id', $dcId);
                }
            }], 'quantity');
        }])
            ->where('user_id', $userId)
            ->first();

        return view('website.cart', compact('cart'));
    }
    public function getCartData()
    {
        $userId = Auth::id();

        $dcId = session('dc_warehouse_id');

        $cart = Cart::with(['items.product' => function ($q) use ($dcId) {
            $q->withStock($dcId);
        }])
            ->where('user_id', $userId)
            ->first();

        if (!$cart) {
            return response()->json([
                'items' => [],
                'total' => 0,
                'subtotal' => 0
            ]);
        }

        $items = $cart->items->map(function ($item) {

            $images = $item->product->product_images;
            $firstImage = is_array($images) ? ($images[0] ?? null) : null;

            return [
                'id'         => $item->id,
                'product_id' => $item->product_id,
                'name'       => $item->product->name,
                'image'      => $firstImage,
                'price'      => number_format($item->price, 2),
                'qty'        => $item->qty,
                'line_total' => number_format($item->line_total, 2),
            ];
        });

        return response()->json([
            'items'    => $items,
            'subtotal' => number_format($cart->subtotal, 2),
            'total'    => number_format($cart->total, 2),
            'cart_count' => $cart->items->sum('qty')
        ]);
    }

    public function remove($id)
    {
        $item = CartItem::find($id);

        if (!$item) {
            return response()->json(['success' => false]);
        }

        $cart = $item->cart;

        $item->delete();

        $cart->subtotal = $cart->items()->sum('line_total');
        $cart->quantity = $cart->items()->sum('qty');
        $cart->total = $cart->subtotal;
        $cart->save();

        return response()->json([
            'success' => true,
            'subtotal' => number_format($cart->subtotal, 2),
            'cart_total' => number_format($cart->total, 2),
            'cart_count' => $cart->quantity
        ]);
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

        $cartItem = CartItem::findOrFail($itemId);
        $dcId = session('dc_warehouse_id');

        $available = ProductBatch::where('product_id', $cartItem->product_id)
            ->where('warehouse_id', $dcId)
            ->sum('quantity');

        if ($request->qty > $available) {
            return response()->json([
                'success' => false,
                'message' => "Only {$available} available"
            ], 422);
        }

        $cartItem->qty = $request->qty;
        $cartItem->line_total = $cartItem->price * $request->qty;
        $cartItem->save();

        $cart = $cartItem->cart;

        $cart->subtotal = $cart->items()->sum('line_total');
        $cart->quantity = $cart->items()->sum('qty');
        $cart->total = $cart->subtotal;
        $cart->save();

        return response()->json([
            'success' => true,
            'qty' => $cartItem->qty,
            'line_total' => number_format($cartItem->line_total, 2),
            'subtotal' => number_format($cart->subtotal, 2),
            'cart_total' => number_format($cart->total, 2),
            'cart_count' => $cart->quantity,
        ]);
    }

    public function productdetails($id)
    {
        $dcId = session('dc_warehouse_id');

        $product = Product::with([
            'category',
            'unit',
            'sale' => function ($q) {
                $q->active()->online();
            }
        ])->findOrFail($id);

        $availableStock = 0;

        if ($dcId) {
            $availableStock = ProductBatch::where('product_id', $id)
                ->where('warehouse_id', $dcId)
                ->sum('quantity');
        }

        $relatedProducts = Product::where('category_id', $product->category_id)
            ->where('id', '!=', $id)
            ->with(['sale' => function ($q) {
                $q->active()->online();
            }])
            ->limit(4)
            ->get();

        $cart = Cart::where('user_id', auth()->id())
            ->with('items')
            ->first();

        $cartItems = $cart ? $cart->items->keyBy('product_id') : collect();

        return view('website.shop_detail', compact(
            'product',
            'relatedProducts',
            'availableStock',
            'cartItems'
        ));
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
    public function drawer()
    {

        // $test = Product::withStock(8)->find(1);

        // dd($test->available_stock);

        $userId = Auth::id();
        $dcId = session('dc_warehouse_id');

        $cart = Cart::where('user_id', $userId)
            ->with([
                'items.product' => function ($q) use ($dcId) {
                    $q->withStock($dcId);
                }
            ])
            ->first();

        return view('website.partials.cart-drawer-items', [
            'globalCart' => $cart
        ]);
    }

    public function applyCoupon(Request $request)
    {
        Log::info('applyCoupon Request:', $request->all());

        $coupon = Coupon::where('code', $request->coupon_code)->first();

        if (!$coupon) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid coupon'
            ]);
        }

        $cart = Cart::where('user_id', auth()->id())->first();

        if (!$cart) {
            return response()->json([
                'status' => false,
                'message' => 'Cart empty'
            ]);
        }

        $cartItems = CartItem::where('cart_id', $cart->id)->get();

        $subtotal = 0;

        foreach ($cartItems as $item) {
            $subtotal += $item->price * $item->qty;
        }

        if ($subtotal < $coupon->min_amount) {
            return response()->json([
                'status' => false,
                'message' => 'Minimum order ₹' . $coupon->min_amount . ' required'
            ]);
        }

        if ($coupon->discount_type == 'percentage') {
            $discount = ($subtotal * $coupon->discount_value) / 100;
        } else {
            $discount = $coupon->discount_value;
        }

        $finalTotal = $subtotal - $discount;

        return response()->json([
            'status' => true,
            'discount' => $discount,
            'final_total' => $finalTotal
        ]);
    }
}
