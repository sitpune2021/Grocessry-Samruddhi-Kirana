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

class PurchaseOrderController extends Controller
{


    public function create()
    {
        return view('purchase_orders.create', [
            'categories' => Category::all()
        ]);
    }

    public function getSubCategories($category_id)
    {
        return SubCategory::where('category_id', $category_id)->get();
    }

    public function getProducts($sub_category_id)
    {
        return Product::where('sub_category_id', $sub_category_id)->get();
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

    DB::transaction(function () use ($request, $items) {

        $po = PurchaseOrder::create([
            'po_number' => 'PO-' . time(),
            'po_date' => now(),
            'subtotal' => $request->subtotal,
            'tax' => $request->tax,
            'shipping_charge' => $request->shipping_charge,
            'discount' => $request->discount,
            'grand_total' => $request->grand_total,
        ]);

        foreach ($items as $item) {

            $availableQty = ProductBatch::where('product_id', $item['product_id'])
                ->sum('quantity');

            if ($item['qty'] > $availableQty) {
                throw new \Exception(
                    'Quantity exceeds available stock for product ID ' . $item['product_id']
                );
            }

            $po->items()->create([
                'product_id' => $item['product_id'],
                'quantity'   => $item['qty'],
                'price'      => $item['price'],
                'total'      => $item['qty'] * $item['price'],
            ]);

            Product::where('id', $item['product_id'])
                ->increment('stock', $item['qty']);
        }
    });

    return redirect()->back()->with('success', 'Purchase Order Created');
}


    public function getAllProducts(Request $request)
    {
        $perPage = 10;

        $products = Product::select('id', 'name', 'base_price')
            ->orderBy('name')
            ->paginate($perPage);

        return response()->json($products);
    }

    public function getAvailableQty($productId)
    {
        $qty = ProductBatch::where('product_id', $productId)->sum('quantity');

        return response()->json([
            'available_qty' => $qty
        ]);
    }

}
