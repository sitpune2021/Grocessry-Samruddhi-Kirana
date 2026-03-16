<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MasterWarehouse;
use App\Models\Product;
use App\Models\Category;
use App\Models\WarehouseStock;
use App\Models\Warehouse;
use App\Models\ProductBatch;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\SubCategory;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Models\SupplierChallan;
use Illuminate\Validation\Rule;


class stockWarehouseController extends Controller
{
    public function indexWarehouse(Request $request)
    {
        $user = Auth::user();

        $query = WarehouseStock::with([
            'warehouse:id,name,type,parent_id',
            'category:id,name',
            'product:id,name',
            'supplier:id,supplier_name',
        ])->orderBy('id', 'desc');

        if ($user->role_id == 1) {
            if ($request->filled('warehouse_id')) {
                $query->where('warehouse_id', $request->warehouse_id);
            }
        } elseif ($user->warehouse->type === 'master') {

            $masterWarehouseId = $user->warehouse_id;

            $districtIds = Warehouse::where('type', 'district')
                ->where('parent_id', $masterWarehouseId)
                ->pluck('id');

            $talukaIds = Warehouse::where('type', 'taluka')
                ->whereIn('parent_id', $districtIds)
                ->pluck('id');

            $shopIds = Warehouse::where('type', 'distribution_center')
                ->whereIn('parent_id', $talukaIds)
                ->pluck('id');

            $allowedWarehouseIds = collect([$masterWarehouseId])
                ->merge($districtIds)
                ->merge($talukaIds)
                ->merge($shopIds);

            $query->whereIn('warehouse_id', $allowedWarehouseIds);
        } elseif ($user->warehouse->type === 'district') {

            $districtWarehouseId = $user->warehouse_id;

            $talukaIds = Warehouse::where('type', 'taluka')
                ->where('parent_id', $districtWarehouseId)
                ->pluck('id');

            $shopIds = Warehouse::where('type', 'distribution_center')
                ->whereIn('parent_id', $talukaIds)
                ->pluck('id');

            $allowedWarehouseIds = collect([$districtWarehouseId])
                ->merge($talukaIds)
                ->merge($shopIds);

            $query->whereIn('warehouse_id', $allowedWarehouseIds);
        } elseif ($user->warehouse->type === 'taluka') {

            $talukaWarehouseId = $user->warehouse_id;

            $shopIds = Warehouse::where('type', 'distribution_center')
                ->where('parent_id', $talukaWarehouseId)
                ->pluck('id');

            $allowedWarehouseIds = collect([$talukaWarehouseId])
                ->merge($shopIds);

            $query->whereIn('warehouse_id', $allowedWarehouseIds);
        } else {
            $query->where('warehouse_id', $user->warehouse_id);
        }

        $stocks = $query
            ->select(
                'warehouse_id',
                'category_id',
                'sub_category_id',
                'product_id',
                DB::raw('SUM(quantity) as quantity')
            )
            ->groupBy(
                'warehouse_id',
                'category_id',
                'sub_category_id',
                'product_id'
            )
            ->get()
            ->groupBy('warehouse_id');
        $warehouses = $user->role_id == 1
            ? Warehouse::select('id', 'name')->orderBy('name')->get()
            : collect();

        return view(
            'menus.warehouse.add-stock.index',
            compact('stocks', 'warehouses')
        );
    }


    // ============================================================
    // FUNCTION 1: addStockForm()
    // Replace your existing addStockForm() with this
    // ============================================================

    public function addStockForm()
    {
        $mode = 'add';
        $user = User::with('warehouse')->find(Auth::id());

        if (!$user) {
            abort(401, 'Unauthenticated');
        }

        $userWarehouse   = $user->warehouse;
        $readonly        = true;
        $warehouse_stock = null;
        $selectedChallan = null;

        $categories      = Category::all();
        $products        = collect();
        $product_batches = ProductBatch::all();
        $sub_categories  = [];
        $suppliers       = Supplier::select('id', 'supplier_name')->get();

        $warehouses = collect();
        if ($user->role_id == 1) {
            $warehouses = Warehouse::orderBy('name')->get();
        }

        // ✅ FIX: Only show challans with status = 'received'
        // After stock is added, status becomes 'stock_added' so it disappears from list
        $challans = SupplierChallan::where('status', 'received')
            ->orderBy('id', 'desc')
            ->get();

        return view(
            'menus.warehouse.add-stock.add-stock',
            compact(
                'mode',
                'userWarehouse',
                'categories',
                'product_batches',
                'products',
                'sub_categories',
                'suppliers',
                'readonly',
                'warehouses',
                'challans',
                'selectedChallan',
                'warehouse_stock'
            )
        );
    }


    // ============================================================
    // FUNCTION 2: addStock()
    // Replace your existing addStock() with this
    // ============================================================

    public function addStock(Request $request)
    {
        Log::info('🟢 AddStock: Request received', [
            'payload' => $request->all()
        ]);

        // ✅ Validate FIRST
        $request->validate([
            'warehouse_id'               => 'required|exists:warehouses,id',
            'bill_no'                    => 'required|string',
            'supplier_challan_id'        => 'required|exists:supplier_challans,id',
            'batch_no'                   => 'required|string',
            'products'                   => 'required|array|min:1',
            'products.*.category_id'     => 'required|exists:categories,id',
            'products.*.sub_category_id' => 'required|exists:sub_categories,id',
            'products.*.product_id'      => 'required|exists:products,id',
            'products.*.quantity'        => 'required|numeric|min:0.01',
        ]);

        // ✅ Duplicate check — make sure challan is still 'received' status
        $challan = SupplierChallan::findOrFail($request->supplier_challan_id);

        if ($challan->status !== 'received') {
            Log::warning('⛔ Duplicate challan attempt blocked', [
                'warehouse_id'        => $request->warehouse_id,
                'supplier_challan_id' => $request->supplier_challan_id,
                'challan_status'      => $challan->status,
            ]);

            return back()
                ->with('error', 'This supplier challan is already added to warehouse stock.')
                ->withInput();
        }

        DB::beginTransaction();

        try {

            Log::info('🟡 AddStock: Transaction started', [
                'warehouse_id' => $request->warehouse_id,
                'challan_no'   => $challan->challan_no,
                'products_cnt' => count($request->products),
            ]);

            foreach ($request->products as $index => $item) {

                Log::info('🔍 AddStock: Processing product', [
                    'index'           => $index,
                    'category_id'     => $item['category_id'],
                    'sub_category_id' => $item['sub_category_id'],
                    'product_id'      => $item['product_id'],
                    'quantity'        => $item['quantity'],
                ]);

                // total stock calculate
                $totalStock = WarehouseStock::where('warehouse_id', $request->warehouse_id)
                    ->where('product_id', $item['product_id'])
                    ->sum('batch_qty');

                // always create new batch row
                WarehouseStock::create([
                    'warehouse_id'        => $request->warehouse_id,
                    'supplier_challan_id' => $request->supplier_challan_id,
                    'category_id'         => $item['category_id'],
                    'sub_category_id'     => $item['sub_category_id'],
                    'product_id'          => $item['product_id'],

                    'batch_qty'           => $item['quantity'],              // batch qty
                    'quantity'            => $totalStock + $item['quantity'], // total qty

                    'bill_no'             => $request->bill_no,
                    'challan_no'          => $challan->challan_no,
                    'batch_no'            => $request->batch_no,
                ]);
            }
            // ✅ FIX: Mark challan as 'stock_added' so it won't appear in dropdown again
            $challan->status = 'stock_added';
            $challan->save();

            Log::info('✅ Challan status updated to stock_added', [
                'challan_id' => $challan->id,
                'challan_no' => $challan->challan_no,
            ]);

            DB::commit();

            Log::info('🟢 AddStock: Transaction committed successfully', [
                'challan_no'   => $challan->challan_no,
                'warehouse_id' => $request->warehouse_id,
            ]);

            return redirect()
                ->route('index.addStock.warehouse')
                ->with('success', 'Stock saved successfully');
        } catch (\Exception $e) {

            DB::rollBack();

            Log::error('🔴 AddStock: Transaction failed', [
                'error'   => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
                'request' => $request->all(),
            ]);

            return redirect()
                ->route('index.addStock.warehouse')
                ->with('error', 'Something went wrong while saving stock');
        }
    }
    public function byCategory($categoryId)
    {
        return SubCategory::where('category_id', $categoryId)
            ->select('id', 'name')
            ->get();
    }
    public function showStockForm($id)
    {
        $mode = 'view';

        $warehouse_stock = WarehouseStock::with(['warehouse', 'category', 'product', 'batch'])
            ->findOrFail($id);

        $stockWarehouse  = $warehouse_stock->warehouse;
        $categories      = Category::all();
        $products        = Product::where('category_id', $warehouse_stock->category_id)->get();
        $product_batches = ProductBatch::where('product_id', $warehouse_stock->product_id)->get();
        $suppliers       = Supplier::select('id', 'supplier_name')->get();
        $warehouses      = Warehouse::select('id', 'name')->get();

        $selectedChallan = SupplierChallan::find($warehouse_stock->supplier_challan_id);

        $challans = SupplierChallan::where('status', 'received')
            ->orderBy('id', 'desc')
            ->get();

        return view('menus.warehouse.add-stock.add-stock', compact(
            'warehouses',
            'mode',
            'warehouse_stock',
            'stockWarehouse',
            'categories',
            'products',
            'product_batches',
            'suppliers',
            'challans',
            'selectedChallan'
        ));
    }

    public function editStockForm(Request $request, $id)
    {
        $mode = 'edit';

        $warehouse_stock = WarehouseStock::with([
            'warehouse',
            'category',
            'product',
            'batch',
        ])->findOrFail($id);

        $stockWarehouse  = $warehouse_stock->warehouse;
        $categories      = Category::all();
        $products        = Product::where('category_id', $warehouse_stock->category_id)->get();
        $product_batches = ProductBatch::where('product_id', $warehouse_stock->product_id)->get();
        $suppliers       = Supplier::select('id', 'supplier_name')->get();

        $sub_categories = SubCategory::where('category_id', $warehouse_stock->category_id)
            ->select('id', 'name')
            ->get();

        $warehouses = Warehouse::select('id', 'name')->get();

        $challans = SupplierChallan::where('status', 'received')
            ->orderBy('id', 'desc')
            ->get();

        $selectedChallan = SupplierChallan::find($warehouse_stock->supplier_challan_id);

        return view('menus.warehouse.add-stock.add-stock', compact(
            'warehouses',
            'mode',
            'warehouse_stock',
            'stockWarehouse',
            'categories',
            'products',
            'product_batches',
            'sub_categories',
            'suppliers',
            'challans',
            'selectedChallan'
        ));
    }

    public function updateStock(Request $request, $id)
    {
        Log::info('Update Stock Request', array_merge(
            $request->all(),
            ['id' => $id]
        ));

        $request->validate([
            'warehouse_id'    => 'required|exists:warehouses,id',
            'category_id'     => 'required|exists:categories,id',
            'product_id'      => 'required|exists:products,id',
            'sub_category_id' => 'required|exists:sub_categories,id',
            'batch_id'        => 'nullable|exists:product_batches,id',
            'quantity'        => 'required|numeric|min:0.01',
            'supplier_id'     => 'required|exists:suppliers,id',
            'bill_no'         => 'required|string',
            'challan_no'      => 'required|string',
            'batch_no'        => 'required|string',
        ]);

        DB::beginTransaction();

        try {
            $stock = WarehouseStock::where('id', $id)->firstOrFail();

            $stock->update([
                'warehouse_id'    => $request->warehouse_id,
                'category_id'     => $request->category_id,
                'product_id'      => $request->product_id,
                'batch_id'        => $request->batch_id ?? null,
                'quantity'        => $request->quantity,
                'batch_qty'       => $request->quantity, // ✅ also update batch_qty
                'supplier_id'     => $request->supplier_id,
                'bill_no'         => $request->bill_no,
                'challan_no'      => $request->challan_no,
                'batch_no'        => $request->batch_no,
            ]);

            DB::commit();

            return redirect()
                ->route('index.addStock.warehouse')
                ->with('success', 'Stock updated successfully');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'Update failed: ' . $e->getMessage());
        }
    }

    public function destroyStock(Request $request, $id)
    {
        Log::info('Delete Stock Request', $request->all());

        try {
            $stock = WarehouseStock::findOrFail($id);
            $stock->delete();

            Log::info('Warehouse stock soft deleted', [
                'stock_id'     => $stock->id,
                'warehouse_id' => $stock->warehouse_id,
                'product_id'   => $stock->product_id,
            ]);

            return redirect()
                ->back()
                ->with('success', 'Stock deleted successfully');
        } catch (\Throwable $e) {
            Log::error('Failed to delete stock', ['error' => $e->getMessage()]);

            return redirect()
                ->back()
                ->with('error', 'Unable to delete stock');
        }
    }

    public function getCategories($warehouseId)
    {
        $categories = Category::where('warehouse_id', $warehouseId)->get();
        return response()->json($categories);
    }

    public function getProductBySubCategory($subCategoryId)
    {
        return Product::where('sub_category_id', $subCategoryId)
            ->select('id', 'name')
            ->orderBy('name')
            ->get();
    }

    public function getSupplierChallan($id)
    {
        $challan = SupplierChallan::with([
            'items.category',
            'items.subCategory',
            'items.product',
        ])->findOrFail($id);

        return response()->json([
            'supplier_id'  => $challan->supplier_id,
            'warehouse_id' => $challan->warehouse_id,
            'challan_no'   => $challan->challan_no,

            'items' => $challan->items->map(function ($item) {
                return [
                    'category_id'     => $item->category_id,
                    'sub_category_id' => $item->sub_category_id,
                    'product_id'      => $item->product_id,
                    'category'        => $item->category->name    ?? '-',
                    'sub_category'    => $item->subCategory->name ?? '-',
                    'product'         => $item->product->name     ?? '-',
                    'quantity'        => $item->received_qty ?? $item->ordered_qty ?? 0,
                ];
            }),
        ]);
    }
}
