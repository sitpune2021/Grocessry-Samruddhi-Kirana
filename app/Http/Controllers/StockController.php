<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Exception;
use App\Models\Category;
use App\Models\Warehouse;
use App\Models\WarehouseStock;
use App\Models\ProductBatch;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class StockController extends Controller
{


    public function create()
    {
        return view('sale.create', [
            'warehouses'      => Warehouse::all(),
            'categories'      => collect(),
            'products'        => collect(),
            'selectedProduct' => null,
            'availableStock'  => 0,
        ]);
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'warehouse_id' => 'required|exists:warehouses,id',
                'product_id'   => 'required|exists:products,id',
                'quantity'     => 'required|integer|min:1',
            ]);

            $warehouseId = $validated['warehouse_id'];
            $productId   = $validated['product_id'];
            $sellQty     = $validated['quantity'];

            DB::beginTransaction();

            // 1️⃣ Warehouse stock check
            $warehouseStock = WarehouseStock::where('warehouse_id', $warehouseId)
                ->where('product_id', $productId)
                ->lockForUpdate()
                ->first();

            if (!$warehouseStock || $warehouseStock->quantity < $sellQty) {
                DB::rollBack();
                return back()->withErrors([
                    'quantity' => 'Insufficient stock in warehouse'
                ]);
            }

            // 2️⃣ Deduct warehouse stock
            $warehouseStock->decrement('quantity', $sellQty);

            // 3️⃣ Deduct product_batches quantity
            $remaining = $sellQty;

            $batches = ProductBatch::where('product_id', $productId)
                ->where('quantity', '>', 0)
                ->orderBy('id')
                ->get();

            foreach ($batches as $batch) {
                if ($remaining <= 0) break;

                $deduct = min($batch->quantity, $remaining);
                $batch->decrement('quantity', $deduct);
                $remaining -= $deduct;
            }

            // 4️⃣ Get ANY ONE batch ID for stock movement
            $batchForMovement = ProductBatch::where('product_id', $productId)
                ->orderBy('id')
                ->first();

            if (!$batchForMovement) {
                DB::rollBack();
                return back()->withErrors([
                    'product_id' => 'No batch found for this product'
                ]);
            }

            // 5️⃣ Stock movement OUT
            StockMovement::create([
                'product_batch_id' => $batchForMovement->id,
                'warehouse_id'     => $warehouseId,
                'type'             => 'out',
                'quantity'         => $sellQty,
            ]);

            DB::commit();

            return redirect()
                ->route('sell.index')
                ->with('success', 'Product sold successfully');

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Sale error', ['error' => $e->getMessage()]);
            return back()->with('error', 'Something went wrong during sale');
        }
    }

    public function getProductsByCategory($categoryId)
    {
        $products = Product::where('category_id', $categoryId)->get();
        return response()->json($products);
    }

    // Warehouse → Categories
    public function getCategoriesByWarehouse($warehouseId)
    {
        return WarehouseStock::where('warehouse_id', $warehouseId)
            ->join('categories', 'categories.id', '=', 'warehouse_stock.category_id')
            ->select('categories.id', 'categories.name')
            ->distinct()
            ->get();
    }

    // Category → Sub Categories
    public function getSubCategoriesByWarehouse($warehouseId, $categoryId)
    {
        return WarehouseStock::where('warehouse_stock.warehouse_id', $warehouseId)
            ->where('warehouse_stock.category_id', $categoryId)
            ->join('sub_categories', 'sub_categories.id', '=', 'warehouse_stock.sub_category_id')
            ->select('sub_categories.id', 'sub_categories.name')
            ->distinct()
            ->get();
    }

    // Sub Category → Products
    public function getProductsBySubCategory($warehouseId, $subCategoryId)
    {
        return WarehouseStock::where([
                'warehouse_id'    => $warehouseId,
                'warehouse_stock.sub_category_id' => $subCategoryId
            ])
            ->join('products', 'products.id', '=', 'warehouse_stock.product_id')
            ->select('products.id', 'products.name')
            ->distinct()
            ->get();
    }

    // Product → Quantity
    public function getProductQuantity($warehouseId, $productId)
    {
        return WarehouseStock::where([
            'warehouse_id' => $warehouseId,
            'product_id'   => $productId,
        ])->sum('quantity');
    }


}
