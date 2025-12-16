<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductBatch;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Exception;
use DB;
use App\Models\Category;


class StockController extends Controller
{
    

    public function create($productId = null)
    {
        $categories = Category::all(); // For category dropdown
        $products = Product::all(); // All products by default
        $selectedProduct = $productId;

        return view('sale.create', compact('categories', 'products', 'selectedProduct'));
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

            // Validation
            $validated = $request->validate([
                'product_id' => 'required|exists:products,id',
                'quantity'   => 'required|integer|min:1',
            ]);

            $qty = $validated['quantity'];

            // Fetch batches (FIFO / Expiry wise)
            $batches = ProductBatch::where('product_id', $validated['product_id'])
                ->where('quantity', '>', 0)
                ->orderBy('expiry_date')
                ->get();

            if ($batches->isEmpty()) {
                Log::warning('No stock available for product', [
                    'product_id' => $validated['product_id'],
                ]);

                return back()->with('error', 'No stock available');
            }

            DB::beginTransaction();

            foreach ($batches as $batch) 
            {

                if ($qty <= 0) {
                    break;
                }

                $deduct = min($batch->quantity, $qty);

                // Reduce batch quantity
                $batch->decrement('quantity', $deduct);

                // Stock movement OUT
                StockMovement::create([
                    'product_batch_id' => $batch->id,
                    'type'             => 'out',
                    'quantity'         => $deduct,
                ]);

                // Per batch log (optional but useful)
                Log::info('Stock deducted from batch', [
                    'batch_id'    => $batch->id,
                    'product_id'  => $validated['product_id'],
                    'deducted'    => $deduct,
                    'remaining'   => $batch->quantity - $deduct,
                ]);

                $qty -= $deduct;
            }

            // Insufficient stock
            if ($qty > 0) {

                DB::rollBack();

                Log::error('Insufficient stock while selling product', [
                    'product_id'      => $validated['product_id'],
                    'requested_qty'   => $validated['quantity'],
                    'remaining_need'  => $qty,
                ]);

                return back()->with('error', 'Insufficient stock');
            }

            DB::commit();

            // Success log
            Log::info('Sale completed successfully', [
                'product_id' => $validated['product_id'],
                'quantity'   => $validated['quantity'],
            ]);

            return back()->with('success', 'Sale completed');

        }
        catch (ValidationException $e) {

            // Validation error
            Log::warning('Sale validation failed', [
                'errors' => $e->errors(),
                'input'  => $request->all(),
            ]);

            throw $e;
        }
        catch (Exception $e) {

            DB::rollBack();

            // Unexpected error
            Log::error('Error during sale process', [
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
                'input'   => $request->all(),
            ]);

            return back()->with('error', 'Something went wrong during sale');
        }
    }


}
