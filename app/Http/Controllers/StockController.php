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
        $warehouses = Warehouse::all();
        $categories = Category::all();
        $products = Product::all();

        // Default selected product
        $selectedProduct = null;
        $availableStock = 0;

        return view('sale.create', compact(
            'warehouses',
            'categories',
            'products',
            'selectedProduct',
            'availableStock'
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

            // 1️⃣ Get total stock in warehouse for this product
            $totalWarehouseStock = WarehouseStock::where('warehouse_id', $warehouseId)
                ->where('product_id', $productId)
                ->sum('quantity');

            // 2️⃣ Check if requested quantity exceeds available warehouse stock
            if ($qty > $totalWarehouseStock) {
                return back()->with('error', "Insufficient stock! Only {$totalWarehouseStock} available in selected warehouse.");
            }

            // Fetch batches for this warehouse & product (FIFO)
            $batches = WarehouseStock::with('batch')
                ->where('warehouse_id', $warehouseId)
                ->where('product_id', $productId)
                ->where('quantity', '>', 0)
                ->get();

            DB::beginTransaction();

            foreach ($batches as $stock) {
                if ($qty <= 0) break;

                $deduct = min($stock->quantity, $qty);

                // Deduct from warehouse stock
                $stock->decrement('quantity', $deduct);

                // Deduct from batch if exists
                if ($stock->batch) {
                    $stock->batch->decrement('quantity', $deduct);
                }

                $batch = ProductBatch::findOrFail($stock['batch_id']);

                if ($batch->is_blocked || $batch->expiry_date < now()->toDateString()) {
                    return back()->withInput()->withErrors([
                        'batch_id' => "Cannot transfer expired or blocked batch ({$batch->batch_no})"
                    ]);
                }


                // Record stock movement
                StockMovement::create([
                    'product_batch_id' => $stock->batch_id,
                    'warehouse_id'     => $warehouseId,
                    'type'             => 'out',
                    'quantity'         => $deduct,
                ]);

                $qty -= $deduct;
            }

            DB::commit();
            Log::info('Sale completed successfully');

            return back()->with('success', 'Sale completed successfully');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Sale error', ['error' => $e->getMessage()]);
            return back()->with('error', 'Something went wrong during sale');
        }
    }
}
