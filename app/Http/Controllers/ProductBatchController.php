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
use Illuminate\Support\Facades\Auth;
use App\Models\Unit;

class ProductBatchController extends Controller
{



    public function index()
    {
        $user = Auth::user();
        $isSuperAdmin = $user->role_id == 1;

        $batches = ProductBatch::with(['product.category', 'warehouse'])
            ->when(!$isSuperAdmin, function ($q) use ($user) {
                $q->where('warehouse_id', $user->warehouse_id);
            })
            ->latest()
            ->get();
        return view('batches.index', compact('batches'));
    }

    public function create()
    {
        $user = Auth::user();
        $isSuperAdmin = $user->role_id == 1;

        $units = Unit::select('id', 'name')->get();

        // Warehouses list
        $warehouses = $isSuperAdmin
            ? Warehouse::select('id', 'name')->orderBy('name')->get()
            : Warehouse::where('id', $user->warehouse_id)->get();

        // Categories based on logged-in user's warehouse
        $categories = WarehouseStock::query()
            ->join('categories', 'categories.id', '=', 'warehouse_stock.category_id')
            ->where('warehouse_stock.warehouse_id', $user->warehouse_id)
            ->whereNull('warehouse_stock.deleted_at')
            ->select('categories.id', 'categories.name')
            ->distinct()
            ->get();

        return view('batches.create', [
            'mode'       => 'add',
            'batch'      => null,
            'warehouses' => $warehouses,
            'categories' => $categories,
            'products'   => collect(),
            'units'      => $units,
            'user'       => $user, // 🔥 pass user
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
        Log::info('Product batch store request received', [
            'user_id' => Auth::id(),
            'payload' => $request->all(),
        ]);
        $user = Auth::user();
        $isSuperAdmin = $user->role_id == 1;

        if (!$isSuperAdmin) {
            $request->merge([
                'warehouse_id' => $user->warehouse_id
            ]);
        }


        try {
            $validated = $request->validate([
                'warehouse_id' => $isSuperAdmin
                    ? 'required|exists:warehouses,id'
                    : 'nullable',
                'category_id'      => 'required|exists:categories,id',
                'sub_category_id'  => 'required|exists:sub_categories,id',
                'product_id'       => 'required|exists:products,id',
                'batch_no'         => 'required|string|max:50',
                'mfg_date'         => 'nullable|date',
                'expiry_date'      => 'nullable|date|after:mfg_date',
                'quantity'         => 'required|integer|min:1',
                'unit_id'          => 'required|exists:units,id',
            ]);

            Log::info('Batch validation successful', [
                'validated_data' => $validated,
            ]);


            $warehouseId = $isSuperAdmin
                ? $request->warehouse_id
                : $user->warehouse_id;

            $product = Product::findOrFail($validated['product_id']);

            $expiryDate = $validated['expiry_date'] ??
                (
                    $validated['mfg_date'] && $product->expiry_days
                    ? Carbon::parse($validated['mfg_date'])->addDays($product->expiry_days)
                    : null
                );

            Log::info('Expiry date calculated', [
                'product_id' => $product->id,
                'expiry_date' => $expiryDate,
            ]);

            $batch = ProductBatch::create([
                'warehouse_id'    => $warehouseId,
                'category_id'     => $validated['category_id'],
                'sub_category_id' => $validated['sub_category_id'],
                'product_id'      => $validated['product_id'],
                'batch_no'        => $validated['batch_no'],
                'mfg_date'        => $validated['mfg_date'],
                'expiry_date'     => $expiryDate,
                'quantity'        => $validated['quantity'],
                'unit_id'         => $validated['unit_id'],
            ]);

            Log::info('Product batch created successfully', [
                'batch_id' => $batch->id,
                'warehouse_id' => $warehouseId,
            ]);

            StockMovement::create([
                'warehouse_id'      => $warehouseId,
                'product_batch_id' => $batch->id,
                'type'             => 'in',
                'quantity'         => $validated['quantity'],
            ]);

            Log::info('Stock movement entry created', [
                'batch_id' => $batch->id,
                'quantity' => $validated['quantity'],
            ]);

            return redirect()
                ->route('batches.index')
                ->with('success', 'Batch added successfully');
        } catch (ValidationException $e) {

            Log::warning('Batch validation failed', [
                'errors' => $e->errors(),
                'user_id' => Auth::id(),
            ]);

            throw $e;
        } catch (\Exception $e) {

            Log::error('Batch create failed', [
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
                'user_id' => Auth::id(),
            ]);

            return back()->with('error', 'Something went wrong');
        }
    }
    public function show($id)
    {
        $user = Auth::user();
        $isSuperAdmin = $user->role_id == 1;

        $batch = ProductBatch::when(
            !$isSuperAdmin,
            fn($q) => $q->where('warehouse_id', $user->warehouse_id)
        )
            ->findOrFail($id);
        $warehouses = $isSuperAdmin
            ? Warehouse::all()
            : Warehouse::where('id', $user->warehouse_id)->get();

        return view('batches.create', [
            'mode'       => 'view',
            'batch'      => $batch,
            'categories' => Category::all(),
            'warehouses' => $warehouses,
            'units'      => Unit::select('id', 'name')->get(),
            'subCategories' => SubCategory::where(
                'category_id',
                $batch->category_id
            )->get(),
            'products' => Product::where(
                'sub_category_id',
                $batch->sub_category_id
            )->get(),
        ]);
    }

    public function edit($id)
    {

        $user = Auth::user();
        $isSuperAdmin = $user->role_id == 1;

        $batch = ProductBatch::when(
            !$isSuperAdmin,
            fn($q) => $q->where('warehouse_id', $user->warehouse_id)
        )
            ->findOrFail($id);
        return view('batches.create', [
            'mode'          => 'edit',
            'batch'         => $batch,
            'warehouses' => $isSuperAdmin
                ? Warehouse::all()
                : Warehouse::where('id', $user->warehouse_id)->get(),
            'categories'    => Category::all(),
            'units' => Unit::select('id', 'name')->get(),
            'subCategories' => SubCategory::where('category_id', $batch->category_id)->get(), // ✅
            'products'      => Product::where('sub_category_id', $batch->sub_category_id)->get(), // ✅
        ]);
    }

    public function update(Request $request, $id)
    {
        $user = Auth::user();
        $isSuperAdmin = $user->role_id == 1;

        if (!$isSuperAdmin) {
            $request->merge([
                'warehouse_id' => $user->warehouse_id
            ]);
        }

        $batch = ProductBatch::when(
            !$isSuperAdmin,
            fn($q) => $q->where('warehouse_id', $user->warehouse_id)
        )
            ->findOrFail($id);
        $validated = $request->validate([
            'category_id'      => 'required|exists:categories,id',
            'sub_category_id'  => 'required|exists:sub_categories,id',
            'product_id'       => 'required|exists:products,id',
            'batch_no'         => 'required|string|max:50',
            'mfg_date'         => 'nullable|date',
            'expiry_date'      => 'nullable|date|after:mfg_date',
            'quantity'         => 'required|integer|min:1',
            'unit_id' => 'required|exists:units,id',

        ]);

        $oldQty = $batch->quantity;          // 👈 OLD
        $newQty = $validated['quantity'];    // 👈 NEW

        $product = Product::findOrFail($validated['product_id']);

        $validated['expiry_date'] = $validated['expiry_date'] ??
            (
                $validated['mfg_date'] && $product->expiry_days
                ? Carbon::parse($validated['mfg_date'])->addDays($product->expiry_days)
                : $batch->expiry_date
            );

        $batch->update($validated);


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
        $user = Auth::user();

        $query = ProductBatch::with(['product', 'warehouse'])
            ->where('quantity', '>', 0)
            ->whereDate('expiry_date', '<=', now()->addDays(30));

        if ($user->role_id != 1) {
            $query->where('warehouse_id', $user->warehouse_id);
        }

        // Super Admin → all warehouses (no filter)

        $batches = $query
            ->orderBy('expiry_date')
            ->paginate();

        return view('batches.expiry', compact('batches'));
    }


    public function getCategoriesByWarehouse($warehouseId)
    {
        return WarehouseStock::where('warehouse_stock.warehouse_id', $warehouseId)
            ->join(
                'categories',
                'categories.id',
                '=',
                'warehouse_stock.category_id'
            )
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
