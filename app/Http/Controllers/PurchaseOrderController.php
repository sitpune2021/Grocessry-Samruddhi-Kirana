<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\Product;
use App\Models\PurchaseOrder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\ProductBatch;
use App\Models\Supplier;
use App\Models\User;

class PurchaseOrderController extends Controller
{

    public function index()
    {
        $orders = PurchaseOrder::with(['items.product'])
            ->latest()
            ->paginate(1);

        return view('purchase_orders.index', compact('orders'));
    }

    public function create()
    {
        $user = Auth::user();

        $categories = Category::whereHas('products', function ($query) use ($user) {
            if ($user->role_id != 1) {
                $query->where('warehouse_id', $user->warehouse_id);
            }
        })
            ->select('id', 'name')
            ->orderBy('name')
            ->get();


        // CORRECT supplier query
        $suppliers = Supplier::where('warehouse_id', $user->warehouse_id)
            ->orderBy('supplier_name')
            ->get();

        return view('purchase_orders.create', compact('categories', 'suppliers'));
    }

    public function getSubCategories($category_id)
    {
        $user = Auth::user();

        $subCategories = SubCategory::where('category_id', $category_id)
            ->whereHas('products', function ($query) use ($user) {

                if ($user->role_id != 1) { // Not Super Admin
                    $query->where('warehouse_id', $user->warehouse_id);
                }
            })
            ->select('id', 'name')
            ->orderBy('name')
            ->get();

        return response()->json($subCategories);
    }

    public function getProducts($sub_category_id)
    {
        $user = Auth::user();

        $products = Product::where('sub_category_id', $sub_category_id)
            ->when($user->role_id != 1, function ($query) use ($user) {
                $query->where('warehouse_id', $user->warehouse_id);
            })
            ->select('id', 'name')
            ->orderBy('name')
            ->get();

        return response()->json($products);
    }


    public function store(Request $request)
    {
        $items = json_decode($request->items, true);

        if (!$items || count($items) === 0) {
            return redirect()
                ->back()
                ->withErrors(['items' => 'Please add at least one product'])
                ->withInput();
        }

        $user = Auth::user();
        // IMPORTANT LINE
        $po = DB::transaction(function () use ($request, $items, $user) {

            $po = PurchaseOrder::create([
                'warehouse_id' => $user->warehouse_id,
                'supplier_id'  => $request->supplier_id,
                'po_number' => 'PO-' . time(),
                'po_date' => now(),

            ]);

            foreach ($items as $item) {

                $product = Product::where('id', $item['product_id'])
                    ->where('warehouse_id', $user->warehouse_id)
                    ->firstOrFail(); // 🔒 HARD BLOCK

                $po->items()->create([
                    'product_id' => $product->id,
                    'quantity'   => $item['qty'],
                ]);

                $product->increment('stock', $item['qty']);
            }

            return $po; // must return
        });

        // NOW IT WORKS
        return redirect()
            ->route('purchase.invoice', $po->id)
            ->with('success', 'Purchase Order Created');
    }

    public function getAllProducts(Request $request)
    {
        $user = Auth::user();
        $perPage = 10;

        $products = Product::where('warehouse_id', $user->warehouse_id)
            ->select('id', 'name')
            ->orderBy('name')
            ->paginate($perPage);

        return response()->json($products);
    }


    // public function getAvailableQty($productId)
    // {
    //     $qty = ProductBatch::where('product_id', $productId)->sum('quantity');

    //     return response()->json([
    //         'available_qty' => $qty
    //     ]);
    // }

    public function invoice(PurchaseOrder $po)
    {
        $po->load('items.product');

        $user = User::with('warehouse')->find(Auth::id());

        $warehouse = $user->warehouse;

        return view('purchase_orders.invoice', compact('po', 'warehouse'));

        // return view('purchase_orders.invoice', [
        //     'po' => $po->load('items.product')
        // ]);
    }
}
