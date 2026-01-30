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


        // $stocks = $query->paginate(20);

        $stocks = $query->get()->groupBy('warehouse_id');

        $warehouses = $user->role_id == 1
            ? Warehouse::select('id', 'name')->orderBy('name')->get()
            : collect();

        return view(
            'menus.warehouse.add-stock.index',
            compact('stocks', 'warehouses')
        );
    }

    public function addStockForm()
    {
        $mode = 'add';
        $user = User::with('warehouse')->find(Auth::id());

        if (!$user) {
            abort(401, 'Unauthenticated');
        }

        $userWarehouse = $user->warehouse;
        $readonly = true;

        $categories = Category::all();
        $products = collect();
        $product_batches = ProductBatch::all();
        $sub_categories = [];
        $suppliers = Supplier::select('id', 'supplier_name')->get();

        // ✅ define warehouses
        $warehouses = collect();
        if ($user->role_id == 1) {
            $warehouses = Warehouse::orderBy('name')->get();
        }

        // ✅ NEW: Supplier Challans (Received only)
        $usedChallanIds = WarehouseStock::whereNotNull('supplier_challan_id')
            ->pluck('supplier_challan_id')
            ->unique();

        $challans = SupplierChallan::where('status', 'received')
            ->whereNotIn('id', $usedChallanIds)
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
                'challans' // ✅ PASS TO VIEW
            )
        );
    }


    public function byCategory($categoryId)
    {
        return SubCategory::where('category_id', $categoryId)
            ->select('id', 'name')
            ->get();
    }

    public function addStock(Request $request)
    {
        // 🔹 Incoming request log
        Log::info('🟢 AddStock: Request received', [
            'payload' => $request->all()
        ]);

        // $exists = WarehouseStock::where('warehouse_id', $request->warehouse_id)
        //     ->where('challan_no', $request->challan_no)
        //     ->exists();

        $exists = WarehouseStock::where('supplier_challan_id', $request->supplier_challan_id)
            ->exists();

        if ($exists) {
            Log::warning('⛔ Duplicate challan attempt blocked', [
                'warehouse_id' => $request->warehouse_id,
                'challan_no'   => $request->challan_no,
            ]);

            return back()
                ->with('error', 'This supplier challan is already added to warehouse stock.')
                ->withInput();
        }

        $request->validate([
            'warehouse_id' => 'required|exists:warehouses,id',
            // 'supplier_id'  => 'required|exists:suppliers,id',
            'bill_no'      => 'required|string',
            'supplier_challan_id' => 'required|exists:supplier_challans,id',

            // 'challan_no' => [
            //     'required',
            //     'string',
            //     Rule::unique('warehouse_stock', 'challan_no')
            //         ->where('warehouse_id', $request->warehouse_id),
            // ],

            'batch_no'     => 'required|string',

            'products' => 'required|array|min:1',
            'products.*.category_id' => 'required|exists:categories,id',
            'products.*.sub_category_id' => 'required|exists:sub_categories,id',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|numeric|min:0.01',
        ]);

        DB::beginTransaction();

        try {

            Log::info('🟡 AddStock: Transaction started', [
                'warehouse_id' => $request->warehouse_id,
                'challan_no'   => $request->challan_no,
                // 'supplier_id'  => $request->supplier_id,
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

                $stock = WarehouseStock::where([
                    'warehouse_id' => $request->warehouse_id,
                    'category_id'  => $item['category_id'],
                    'product_id'   => $item['product_id'],
                ])
                    ->where('sub_category_id', $item['sub_category_id'])
                    ->first();

                if ($stock) {

                    Log::info('➕ AddStock: Existing stock found, updating quantity', [
                        'stock_id'     => $stock->id,
                        'old_quantity' => $stock->quantity,
                        'add_quantity' => $item['quantity'],
                    ]);

                    $stock->quantity += $item['quantity'];
                    $stock->save();

                    Log::info('✅ AddStock: Stock updated', [
                        'stock_id'    => $stock->id,
                        'new_quantity' => $stock->quantity,
                    ]);
                } else {

                    Log::info('🆕 AddStock: Creating new stock row');

                    $newStock = WarehouseStock::create([
                        'warehouse_id'         => $request->warehouse_id,
                        // 'supplier_id'          => $request->supplier_id,
                        'supplier_challan_id' => $request->supplier_challan_id, // ✅ MUST
                        'category_id'          => $item['category_id'],
                        'sub_category_id'      => $item['sub_category_id'],
                        'product_id'           => $item['product_id'],
                        'quantity'             => $item['quantity'],
                        'bill_no'              => $request->bill_no,
                        'challan_no'           => $request->challan_no,
                        'batch_no'             => $request->batch_no,
                    ]);

                    Log::info('✅ AddStock: New stock created', [
                        'stock_id' => $newStock->id,
                    ]);
                }
            }

            DB::commit();

            Log::info('🟢 AddStock: Transaction committed successfully', [
                'challan_no' => $request->challan_no,
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

    public function showStockForm($id)
    {
        $mode = 'view';

        $warehouse_stock = WarehouseStock::with(['warehouse', 'category', 'product', 'batch'])
            ->findOrFail($id);

        $stockWarehouse = $warehouse_stock->warehouse;

        $categories = Category::all();
        $products = Product::where('category_id', $warehouse_stock->category_id)->get();
        $product_batches = ProductBatch::where('product_id', $warehouse_stock->product_id)->get();
        $suppliers = Supplier::select('id', 'supplier_name')->get();
        $warehouses = Warehouse::select('id', 'name')->get();

        // 🔥 find challan by challan_no
        $selectedChallan = SupplierChallan::find(
            $warehouse_stock->supplier_challan_id
        );


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
            'selectedChallan' // ✅ important
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
            //  'supplierChallan'   

        ])->findOrFail($id);

        $stockWarehouse = $warehouse_stock->warehouse;

        $categories = Category::all();
        $products = Product::where('category_id', $warehouse_stock->category_id)->get();
        $product_batches = ProductBatch::where('product_id', $warehouse_stock->product_id)->get();
        $suppliers = Supplier::select('id', 'supplier_name')->get();

        $sub_categories = SubCategory::where('category_id', $warehouse_stock->category_id)
            ->select('id', 'name')
            ->get();

        $warehouses = Warehouse::select('id', 'name')->get();

        // ✅ ADD THESE (same as view)
        $challans = SupplierChallan::where('status', 'received')
            ->orderBy('id', 'desc')
            ->get();
        $selectedChallan = SupplierChallan::find(
            $warehouse_stock->supplier_challan_id
        );


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
            'challans',          // ✅ important
            'selectedChallan'    // ✅ important
        ));
    }


    public function updateStock(Request $request, $id)
    {
        Log::info('Update Stock Request', array_merge(
            $request->all(),
            ['id' => $id]
        ));

        $request->validate([
            'warehouse_id' => 'required|exists:warehouses,id',
            'category_id'  => 'required|exists:categories,id',
            'product_id'   => 'required|exists:products,id',
            'sub_category_id'  => 'required|exists:sub_categories,id',
            'batch_id' => 'nullable|exists:product_batches,id',
            'quantity'     => 'required|numeric|min:0.01',
            'supplier_id' => 'required|exists:suppliers,id',
            'bill_no'       => 'required|string',
            'challan_no'    => 'required|string',
            'batch_no'      => 'required|string',

        ]);

        DB::beginTransaction();

        try {
            $stock = WarehouseStock::where('id', $id)->firstOrFail();

            $stock->update([
                'warehouse_id' => $request->warehouse_id,
                'category_id'  => $request->category_id,
                'product_id'   => $request->product_id,
                //'batch_id'     => $request->batch_id,
                'batch_id'     => $request->batch_id ?? null,
                'quantity'     => $request->quantity,
                'supplier_id' => $request->supplier_id,
                'bill_no'       => $request->bill_no,
                'challan_no'    =>  $request->challan_no,
                'batch_no'      =>  $request->batch_no,

            ]);

            DB::commit();

            return redirect()
                ->route('index.addStock.warehouse')
                ->with('success', 'Stock updated successfully');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'Update failed');
        }
    }

    public function destroyStock(Request $request, $id)
    {

        Log::info('Delete Stock Request', $request->all());

        try {

            $stock = WarehouseStock::findOrFail($id);

            $stock->delete();

            Log::info('Warehouse stock soft deleted', [
                'stock_id' => $stock->id,
                'warehouse_id' => $stock->warehouse_id,
                'product_id' => $stock->product_id,
                'batch_id' => $stock->batch_id,
            ]);

            return redirect()
                ->back()
                ->with('success', 'Stock deleted successfully');
        } catch (\Throwable $e) {
            Log::error('Failed to delete stock', [
                'error' => $e->getMessage(),
            ]);

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

    /**
     * Get products by sub category (AJAX)
     */
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
                    // 🔑 IDs (MOST IMPORTANT)
                    'category_id'     => $item->category_id,
                    'sub_category_id' => $item->sub_category_id,
                    'product_id'      => $item->product_id,

                    // 👁 Names (for UI)
                    'category'        => $item->category->name ?? '-',
                    'sub_category'    => $item->subCategory->name ?? '-',
                    'product'         => $item->product->name ?? '-',

                    'quantity'        => $item->received_qty
                        ?? $item->ordered_qty
                        ?? 0,
                ];
            }),
        ]);
    }
}
