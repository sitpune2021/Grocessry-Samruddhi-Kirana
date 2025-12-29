<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductBatch;
use App\Models\StockMovement;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Exception;
use Carbon\Carbon;
use App\Models\Warehouse;
use App\Models\WarehouseStock;
use App\Models\SubCategory;


class ProductBatchController extends Controller
{

    public function index()
    {
        $batches = ProductBatch::with('product.category')->latest()->get();
        return view('batches.index', compact('batches'));
    }

    public function create()
    {
        return view('batches.create', [
            'mode'       => 'add',
            'batch'      => null,
            'warehouses' => Warehouse::all(),   
            'categories' => collect(),           // Category::all() hatao
            'products'   => collect(),
        ]);
    }

    public function getProductsByCategory($category_id)
    {
        return response()->json(
            Product::where('category_id', $category_id)->get()
        );
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'warehouse_id' => 'required|exists:warehouses,id',
                //'category_id' => 'required|exists:categories,id',
                'category_id' => 'required|exists:warehouse_stock,category_id',
                'sub_category_id' => 'required|exists:sub_categories,id',
                'product_id'  => 'required|exists:products,id',
                'batch_no'    => 'required|string|max:50',
                'mfg_date'    => 'nullable|date',
                'expiry_date' => 'nullable|date|after:mfg_date',
                'quantity'    => 'required|integer|min:1',
            ]);

            $product = Product::findOrFail($validated['product_id']);

            // FIX: Auto expiry calculation
            $expiryDate = $validated['expiry_date'] ??
                (
                    $validated['mfg_date'] && $product->expiry_days
                    ? Carbon::parse($validated['mfg_date'])->addDays($product->expiry_days)
                    : null
                );

            $batch = ProductBatch::create([
                'warehouse_id' => $validated['warehouse_id'],
                'category_id' => $validated['category_id'],
                'sub_category_id' => $validated['sub_category_id'],
                'product_id'  => $validated['product_id'],
                'batch_no'    => $validated['batch_no'],
                'mfg_date'    => $validated['mfg_date'],
                'expiry_date' => $expiryDate,
                'quantity'    => $validated['quantity'],
            ]);

            // Stock IN entry
            StockMovement::create([
                'warehouse_id'      => $batch->warehouse_id,
                'product_batch_id' => $batch->id,
                'type'             => 'in',
                'quantity'         => $validated['quantity'],
            ]);

            Log::info('Product batch created', ['batch_id' => $batch->id]);

            return redirect()
                ->route('batches.index')     // FIX
                ->with('success', 'Batch added successfully');
        } catch (ValidationException $e) {
            throw $e;
        } catch (Exception $e) {
            Log::error('Batch create error', ['error' => $e->getMessage()]);
            return back()->with('error', 'Something went wrong');
        }
    }

    public function show($id)
    {
        $batch = ProductBatch::with('product.category')->findOrFail($id);

        return view('batches.create', [        // SAME PAGE
            'mode'       => 'view',          // FIX
            'batch'      => $batch,
            'categories' => Category::all(),
            'products'   => Product::where('category_id', $batch->category_id)->get(),
        ]);
    }

    public function edit($id)
    {
        $batch = ProductBatch::findOrFail($id);

        return view('batches.create', [
            'mode'          => 'edit',
            'batch'         => $batch,
            'warehouses'    => Warehouse::all(),
            'categories'    => Category::all(),
            'subCategories' => SubCategory::where('category_id', $batch->category_id)->get(), // ✅
            'products'      => Product::where('sub_category_id', $batch->sub_category_id)->get(), // ✅
        ]);
    }

    public function update(Request $request, $id)
    {
        $batch = ProductBatch::findOrFail($id);

        $validated = $request->validate([
            'category_id'      => 'required|exists:categories,id',
            'sub_category_id'  => 'required|exists:sub_categories,id',
            'product_id'       => 'required|exists:products,id',
            'batch_no'         => 'required|string|max:50',
            'mfg_date'         => 'nullable|date',
            'expiry_date'      => 'nullable|date|after:mfg_date',
            'quantity'         => 'required|integer|min:1',
        ]);

        $oldQty = $batch->quantity;          // 👈 OLD
        $newQty = $validated['quantity'];    // 👈 NEW

        $product = Product::findOrFail($validated['product_id']);

        // Expiry auto calculation
        $validated['expiry_date'] = $validated['expiry_date'] ??
            (
                $validated['mfg_date'] && $product->expiry_days
                ? Carbon::parse($validated['mfg_date'])->addDays($product->expiry_days)
                : $batch->expiry_date
            );

        // 🔁 UPDATE BATCH
        $batch->update($validated);

        /* ===============================
        📦 STOCK MOVEMENT LOGIC
        =============================== */

        if ($newQty != $oldQty) {

            $diff = abs($newQty - $oldQty);

            StockMovement::create([
                'product_batch_id' => $batch->id,
                'type'             => $newQty > $oldQty ? 'in' : 'out',
                'quantity'         => $diff,
            ]);
        }

        return redirect()
            ->route('batches.index')
            ->with('success', 'Batch updated successfully');
    }

    public function destroy($id)
    {
        ProductBatch::findOrFail($id)->delete();
        return redirect()->route('batches.index')->with('success', 'Batch deleted successfully');
    }

    public function expiryAlerts()
    {
        $batches = ProductBatch::where('quantity', '>', 0)
            ->whereDate('expiry_date', '<=', now()->addDays(30))
            ->orderBy('expiry_date')
            ->get();

        return view('batches.expiry', compact('batches'));
    }

    public function getCategoriesByWarehouse($warehouseId)
    {
        return WarehouseStock::where('warehouse_id', $warehouseId)
            ->join('categories', 'categories.id', '=', 'warehouse_stock.category_id')
            ->select('categories.id', 'categories.name')
            ->distinct()
            ->get();
    }

    public function getProductsByWarehouseCategory($warehouseId, $categoryId)
    {
        return WarehouseStock::where([
                'warehouse_id' => $warehouseId,
                'category_id'  => $categoryId,
            ])
            ->join('products', 'products.id', '=', 'warehouse_stock.product_id')
            ->select('products.id', 'products.name')
            ->distinct()
            ->get();
    }

    public function getSubCategories($warehouseId, $categoryId)
    {
        return WarehouseStock::where('warehouse_stock.warehouse_id', $warehouseId)
            ->where('warehouse_stock.category_id', $categoryId)
            ->join(
                'sub_categories',
                'sub_categories.id',
                '=',
                'warehouse_stock.sub_category_id'
            )
            ->select('sub_categories.id', 'sub_categories.name')
            ->distinct()
            ->get();
    }

    public function getProductsBySubCategory($warehouseId, $subCategoryId)
    {
        return WarehouseStock::where('warehouse_stock.warehouse_id', $warehouseId)
            ->where('warehouse_stock.sub_category_id', $subCategoryId)
            ->join(
                'products',
                'products.id',
                '=',
                'warehouse_stock.product_id'
            )
            ->select('products.id', 'products.name')
            ->distinct()
            ->get();
    }

    public function getProductQuantity($warehouseId, $productId)
    {
        $qty = WarehouseStock::where('warehouse_id', $warehouseId)
            ->where('product_id', $productId)
            ->sum('quantity');

        return response()->json([
            'quantity' => (int) $qty
        ]);
    }



}
