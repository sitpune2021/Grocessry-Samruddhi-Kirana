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
            ->map(function ($item) {
                if (is_array($item)) {
                    return $item;
                }
                return json_decode($item, true) ?? [];
            })
            ->flatten()
            ->filter()
            ->unique()
            ->values()
            ->toArray();

        // $challans = SupplierChallan::where('status', 'received')
        //     ->whereNotIn('id', $usedChallanIds)
        //     ->orderBy('id', 'desc')
        //     ->get();

        $challansQuery = SupplierChallan::where('status', 'received')
            ->whereNotIn('id', $usedChallanIds);

        // If NOT admin → restrict by warehouse
        if ($user->role_id != 1) {
            $challansQuery->where('warehouse_id', $userWarehouse->id);
        }

        $challans = $challansQuery->orderBy('id', 'desc')->get();

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
        Log::info('AddStock START', [
            'request' => $request->all()
        ]);

        $request->validate([
            'supplier_challan_id' => 'required|exists:supplier_challans,id',
            'warehouse_id'        => 'required|exists:warehouses,id',
            'bill_no'             => 'required|string|max:100',
            'batch_no'            => 'required|string|max:100',

            'products'                    => 'required|array|min:1',
            'products.*.category_id'      => 'required|exists:categories,id',
            'products.*.sub_category_id'  => 'required|exists:sub_categories,id',
            'products.*.product_id'       => 'required|exists:products,id',
            'products.*.quantity'         => 'required|numeric|min:1',
        ]);

        DB::beginTransaction();

        try {

            foreach ($request->products as $index => $item) {

                Log::info('Processing Product', [
                    'index' => $index,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'batch' => $request->batch_no,
                    'challan' => $request->supplier_challan_id
                ]);

                $stock = WarehouseStock::where([
                    'warehouse_id' => $request->warehouse_id,
                    'category_id'  => $item['category_id'],
                    'product_id'   => $item['product_id'],
                ])
                    ->where('sub_category_id', $item['sub_category_id'])
                    ->first();

                if ($stock) {

                    Log::info('Existing stock found', [
                        'stock_id' => $stock->id,
                        'old_qty' => $stock->quantity
                    ]);

                    // Total qty
                    $stock->quantity += $item['quantity'];

                    // Batch array
                    $batches = $stock->batch_no ?? [];
                    if (!in_array($request->batch_no, $batches)) {
                        $batches[] = $request->batch_no;
                    }

                    // Challan array
                    $challans = $stock->supplier_challan_id ?? [];
                    if (!in_array($request->supplier_challan_id, $challans)) {
                        $challans[] = $request->supplier_challan_id;
                    }

                    // Batch-wise qty
                    $batchQty = $stock->batch_qty ?? [];

                    if (isset($batchQty[$request->batch_no])) {
                        $batchQty[$request->batch_no] += $item['quantity'];
                    } else {
                        $batchQty[$request->batch_no] = $item['quantity'];
                    }

                    Log::info('Updating stock', [
                        'new_total_qty' => $stock->quantity,
                        'batches' => $batches,
                        'challans' => $challans,
                        'batch_qty' => $batchQty
                    ]);

                    // Save
                    $stock->batch_no = $batches;
                    $stock->supplier_challan_id = $challans;
                    $stock->batch_qty = $batchQty;
                    $stock->bill_no = $request->bill_no;

                    $stock->save();

                    Log::info('Stock Updated', [
                        'stock_id' => $stock->id
                    ]);
                } else {

                    Log::info('Creating new stock');

                    $newStock = WarehouseStock::create([
                        'warehouse_id' => $request->warehouse_id,
                        'supplier_challan_id' => [$request->supplier_challan_id],
                        'category_id' => $item['category_id'],
                        'sub_category_id' => $item['sub_category_id'],
                        'product_id' => $item['product_id'],
                        'quantity' => $item['quantity'],
                        'bill_no' => $request->bill_no,
                        'challan_no' => $request->challan_no,
                        'batch_no' => [$request->batch_no],
                        'batch_qty' => [
                            $request->batch_no => $item['quantity']
                        ],
                    ]);

                    Log::info('New Stock Created', [
                        'stock_id' => $newStock->id
                    ]);
                }
            }

            DB::commit();

            Log::info('🟢 AddStock SUCCESS');
            return redirect()
                ->route('index.addStock.warehouse')
                ->with('success', 'Stock saved successfully');
        } catch (\Exception $e) {

            DB::rollBack();

            Log::error('🔴 AddStock FAILED', [
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
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
