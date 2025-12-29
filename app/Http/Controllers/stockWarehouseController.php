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

class stockWarehouseController extends Controller
{
    public function indexWarehouse()
    {
        $stocks = WarehouseStock::with([
            'warehouse:id,name',
            'category:id,name',
            'product:id,name',
            'batch:id,batch_no'
        ])->orderBy('id', 'desc')->paginate(10);

        return view('menus.warehouse.add-stock.index', compact('stocks'));
    }


    // add stock in warehouse
    public function addStockForm()
    {
        $mode = 'add';
        $warehouses = Warehouse::all();
        $categories = Category::all();
        $products = Product::all(); // ADD THIS
        $product_batches = ProductBatch::all();
        $sub_categories = []; // initially empty

        return view('menus.warehouse.add-stock.add-stock', compact('mode', 'warehouses', 'categories', 'product_batches', 'products','sub_categories'));
    }

    public function byCategory($categoryId)
    {
        return SubCategory::where('category_id', $categoryId)
                ->select('id','name')
                ->get();
    }

    public function addStock(Request $request)
    {
        // 🔹 Log request data
        Log::info('Add Stock Request', $request->all());

        $request->validate([
            'warehouse_id' => 'required|exists:warehouses,id',
            'category_id'  => 'required|exists:categories,id',
            'sub_category_id'  => 'nullable|exists:sub_categories,id',
            'product_id'   => 'required|exists:products,id',
            //'batch_id'     => 'required|exists:product_batches,id',
            'batch_id' => 'nullable|exists:product_batches,id',
            'quantity'     => 'required|numeric|min:0.01',
        ]);

        DB::beginTransaction();

        try {
            // 🔹 Check existing stock
            Log::info('Checking warehouse stock', [
                'warehouse_id' => $request->warehouse_id,
                'product_id'   => $request->product_id,
                'batch_id'     => $request->batch_id,
            ]);

            // $stock = WarehouseStock::where([
            //     'warehouse_id' => $request->warehouse_id,
            //     'category_id'  => $request->category_id,
            //     'product_id'   => $request->product_id,
            //     'batch_id'     => $request->batch_id,
            // ])->first();
            $stock = WarehouseStock::where([
                'warehouse_id' => $request->warehouse_id,
                'category_id'  => $request->category_id,
                'product_id'   => $request->product_id,
            ]);

            if ($request->filled('sub_category_id')) {
                $stock->where('sub_category_id', $request->sub_category_id);
            } else {
                $stock->whereNull('sub_category_id');
            }

            if ($request->filled('batch_id')) {
                $stock->where('batch_id', $request->batch_id);
            } else {
                $stock->whereNull('batch_id');
            }

            $stock = $stock->first();


            if ($stock) {
                Log::info('Stock exists, updating quantity', [
                    'stock_id'     => $stock->id,
                    'old_quantity' => $stock->quantity,
                    'added_qty'    => $request->quantity,
                ]);

                $stock->quantity += $request->quantity;
                $stock->save();

                Log::info('Stock updated successfully', [
                    'new_quantity' => $stock->quantity,
                ]);
            } else {
                Log::info('Stock not found, creating new entry');

                $newStock = WarehouseStock::create([
                    'warehouse_id' => $request->warehouse_id,
                    'category_id'  => $request->category_id,
                    'sub_category_id' => $request->sub_category_id ?? null,
                    'product_id'   => $request->product_id,
                    //'batch_id'     => $request->batch_id,
                    'batch_id'     => $request->batch_id ?? null,
                    'quantity'     => $request->quantity,
                ]);

                Log::info('New stock created', $newStock->toArray());
            }

            DB::commit();

            Log::info('Add stock transaction committed successfully');

            return redirect()
                ->route('index.addStock.warehouse')
                ->with('success', 'Stock saved successfully');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Add stock failed', [
                'message' => $e->getMessage(),
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
        $warehouse_stock = WarehouseStock::with(['warehouse', 'category', 'product', 'batch'])->findOrFail($id);
        $warehouses = Warehouse::all();
        $categories = Category::all();
        $products = Product::where('category_id', $warehouse_stock->category_id)->get();
        $product_batches = ProductBatch::where('product_id', $warehouse_stock->product_id)->get();

        return view('menus.warehouse.add-stock.add-stock', compact(
            'mode',
            'warehouse_stock',
            'warehouses',
            'categories',
            'products',
            'product_batches'
        ));
    }

    
    public function editStockForm(Request $request, $id)
    {
        $mode = 'edit';

        $warehouse_stock = WarehouseStock::with([
            'warehouse',
            'category',
            'product',
            'batch'
        ])->findOrFail($id);

        $warehouses = Warehouse::all();
        $categories = Category::all();

        $products = Product::where('category_id', $warehouse_stock->category_id)->get();
        $product_batches = ProductBatch::where('product_id', $warehouse_stock->product_id)->get();

        // ✅ FIX: Fetch sub categories for selected category
        $sub_categories = SubCategory::where('category_id', $warehouse_stock->category_id)
            ->select('id', 'name')
            ->get();

        return view('menus.warehouse.add-stock.add-stock', compact(
            'mode',
            'warehouse_stock',
            'warehouses',
            'categories',
            'products',
            'product_batches',
            'sub_categories'
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
            //'batch_id'     => 'required|exists:product_batches,id',
            'batch_id' => 'nullable|exists:product_batches,id',
            'quantity'     => 'required|numeric|min:0.01',
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
}
