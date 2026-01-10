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
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class StockController extends Controller
{

    public function create()
    {
        $user = Auth::user();

        // Super Admin → all warehouses
        // Normal User → only their warehouse
        $warehouses = $user->role_id == 1
            ? Warehouse::orderBy('name')->get()
            : Warehouse::where('id', $user->warehouse_id)->get();

        $categories = WarehouseStock::where('warehouse_id', $user->warehouse_id)
            ->whereNull('warehouse_stock.deleted_at')
            ->join('categories', 'categories.id', '=', 'warehouse_stock.category_id')
            ->whereNull('categories.deleted_at')
            ->select('categories.id', 'categories.name')
            ->distinct()
            ->orderBy('categories.name')
            ->get();

        return view('sale.create', [
            'warehouses'      => $warehouses,
            'categories'      => $categories,
            'subCategories'   => collect(),
            'products'        => collect(),
            'selectedProduct' => null,
            'availableStock'  => 0,
            'user'            => $user,
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


    // Warehouse → Categories
    // public function getCategoriesByWarehouse($warehouseId)
    // {
    //     return WarehouseStock::where('warehouse_id', $warehouseId)
    //         ->whereNull('warehouse_stock.deleted_at')
    //         ->join('categories', 'categories.id', '=', 'warehouse_stock.category_id')
    //         ->select('categories.id', 'categories.name')
    //         ->distinct()
    //         ->orderBy('categories.name')
    //         ->get();
    // }

    // Category → Sub Categories
    // public function getSubCategories($warehouseId, $categoryId)
    // {
    //      return WarehouseStock::where('warehouse_stock.warehouse_id', $warehouseId)
    //     ->where('warehouse_stock.category_id', $categoryId)
    //     ->whereNotNull('warehouse_stock.sub_category_id')
    //     ->whereNull('warehouse_stock.deleted_at')
    //     ->join('sub_categories', 'sub_categories.id', '=', 'warehouse_stock.sub_category_id')
    //     ->whereNull('sub_categories.deleted_at')
    //     ->select('sub_categories.id', 'sub_categories.name')
    //     ->distinct()
    //     ->orderBy('sub_categories.name')
    //     ->get();

    // }

    public function getSubCategories($warehouseId, $categoryId)
    {
        // 🔍 Incoming request log
        Log::info('Fetching subcategories', [
            'warehouse_id' => $warehouseId,
            'category_id'  => $categoryId,
        ]);

        try {
            $subCategories = WarehouseStock::where('warehouse_stock.warehouse_id', $warehouseId)
                ->where('warehouse_stock.category_id', $categoryId)
                ->whereNotNull('warehouse_stock.sub_category_id') // IMPORTANT
                ->whereNull('warehouse_stock.deleted_at')
                ->join('sub_categories', 'sub_categories.id', '=', 'warehouse_stock.sub_category_id')
                ->whereNull('sub_categories.deleted_at')
                ->select('sub_categories.id', 'sub_categories.name')
                ->distinct()
                ->orderBy('sub_categories.name')
                ->get();

            Log::info('Subcategories fetched successfully', [
                'count' => $subCategories->count(),
                'data'  => $subCategories,
            ]);

            return $subCategories;
        } catch (\Throwable $e) {


            Log::error('Error fetching subcategories', [
                'warehouse_id' => $warehouseId,
                'category_id'  => $categoryId,
                'error'        => $e->getMessage(),
                'line'         => $e->getLine(),
                'file'         => $e->getFile(),
            ]);

            return response()->json([
                'message' => 'Failed to load subcategories'
            ], 500);
        }
    }


   public function getProductsBySubCategory($warehouseId, $subCategoryId)
{
    return WarehouseStock::where('warehouse_stock.warehouse_id', $warehouseId)
        ->where('warehouse_stock.sub_category_id', $subCategoryId)
        ->whereNotNull('warehouse_stock.product_id')
        ->whereNull('warehouse_stock.deleted_at')
        ->join('products', 'products.id', '=', 'warehouse_stock.product_id')
        ->whereNull('products.deleted_at') // only if products use SoftDeletes
        ->select('products.id', 'products.name')
        ->distinct()
        ->orderBy('products.name')
        ->get();
}


    // Product → Quantity
    public function getProductQuantity($warehouseId, $productId)
    {
        return WarehouseStock::where('warehouse_id', $warehouseId)
        ->where('product_id', $productId)
        ->whereNull('deleted_at')
        ->sum('quantity');
    }
}
