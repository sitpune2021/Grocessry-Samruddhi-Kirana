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
    

    // public function create($productId = null)
    // {
    //     $categories = Category::all(); // For category dropdown
    //     $products = Product::all(); // All products by default
    //     $selectedProduct = $productId;

    //     return view('sale.create', compact('categories', 'products', 'selectedProduct'));
    // }

    public function create($productId = null)
    {
        $categories = Category::all();
        $products = Product::all();
        $warehouses = Warehouse::all();   // ✅ ADD THIS
        $selectedProduct = $productId;

        return view('sale.create', compact(
            'categories',
            'products',
            'warehouses',   // ✅ PASS TO VIEW
            'selectedProduct'
        ));
    }


    // AJAX function to return products by category
    public function getProductsByCategory($categoryId)
    {
        $products = Product::where('category_id', $categoryId)->get();
        return response()->json($products);
    }


  public function store(Request $request)
{
    try {
        $validated = $request->validate([
            'warehouse_id' => 'required|exists:warehouses,id',
            'product_id'   => 'required|exists:products,id',
            'quantity'     => 'required|integer|min:1',
        ]);

        $qty = $validated['quantity'];
        $warehouseId = $validated['warehouse_id'];
        $productId = $validated['product_id'];

        // Fetch batches for this warehouse & product
        $batches = WarehouseStock::with('batch')
            ->where('warehouse_id', $warehouseId)
            ->where('product_id', $productId)
            ->where('quantity', '>', 0)
            ->get();

        if ($batches->isEmpty()) {
            return back()->with('error', 'No stock available in selected warehouse');
        }

        DB::beginTransaction();

        foreach ($batches as $stock) {
            if ($qty <= 0) break;

            $deduct = min($stock->quantity, $qty);

            // Log before deduct
            Log::info('Before Deduct', [
                'warehouse_stock_id' => $stock->id,
                'batch_qty' => $stock->batch ? $stock->batch->quantity : 'NULL',
                'warehouse_qty' => $stock->quantity,
                'deduct' => $deduct
            ]);

            // Deduct from warehouse stock
            $stock->decrement('quantity', $deduct);

            // Deduct from batch if exists
            if ($stock->batch) {
                $stock->batch->decrement('quantity', $deduct);
            }

            // Stock movement log
            StockMovement::create([
                'product_batch_id' => $stock->batch_id,
                'warehouse_id' => $warehouseId,
                'type' => 'out',
                'quantity' => $deduct,
            ]);

            $qty -= $deduct;
        }

        if ($qty > 0) {
            DB::rollBack();
            return back()->with('error', 'Insufficient stock');
        }

        DB::commit();
        Log::info('Sale completed successfully');

        return back()->with('success', 'Sale completed successfully');

    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Sale error', ['error' => $e->getMessage()]);
        return back()->with('error', 'Something went wrong during sale');
    }
}



}
