<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Product;
use App\Models\Warehouse;
use App\Models\WarehouseStock;
use App\Models\WarehouseTransfer;
use App\Models\ProductBatch;
use App\Models\StockMovement;
use App\Models\Category;
use Illuminate\Support\Facades\Auth;

class WarehouseTransferController extends Controller
{

    public function index()
    {
        // Eager load related models for display
        $transfers = WarehouseTransfer::with([
            'fromWarehouse',
            'toWarehouse',
            'category',
            'product',
            'batch'
        ])->orderBy('created_at', 'desc')->get();

        return view('warehouse.index', compact('transfers'));
    }

    //  public function create()
    // {
    //     return view('warehouse.transfer', [
    //         'warehouses' => Warehouse::where('status', 'active')->get(),
    //         'categories' => collect(), 
    //         'products'   => collect(), 
    //         'batches'    => collect(), 
    //         'transfer'   => null,
    //     ]);
    // }

   public function create()
{
    $user = auth()->user();

    // From warehouse = user warehouse
    $fromWarehouse = Warehouse::where('status', 'active')
        ->where('id', $user->warehouse_id)
        ->first();

    $toWarehouses = Warehouse::where('status', 'active')
        ->where('id', '!=', $user->warehouse_id)
        ->get();

    return view('warehouse.transfer', [
        'fromWarehouse' => $fromWarehouse,
        'toWarehouses'  => $toWarehouses,
        'categories'    => collect(),
        'products'      => collect(),
        'batches'       => collect(),
        'transfer'      => null,
    ]);
}




    public function getProductsByCategory($category_id)
    {
        return Product::where('category_id', $category_id)->get();
    }

    // public function getBatchesByProducts(Request $request)
    // {
    //     if ($request->has('product_ids')) {

    //         $batches = ProductBatch::whereIn('product_id', $request->product_ids)
    //             ->where('is_blocked', 0)
    //             ->whereDate('expiry_date', '>=', now())
    //             ->select('id', 'product_id', 'batch_no')
    //             ->get();

    //         return response()->json([
    //             'type' => 'batches',
    //             'data' => $batches
    //         ]);
    //     }
    // }

    public function getBatchesByProducts(Request $request)
{
    if ($request->has('product_ids')) {

        $batches = ProductBatch::whereIn('id', function ($query) use ($request) {
                $query->selectRaw('MIN(id)')
                      ->from('product_batches')
                      ->whereIn('product_id', $request->product_ids)
                      ->where('is_blocked', 0)
                      ->whereDate('expiry_date', '>=', now())
                      ->groupBy('product_id');
            })
            ->select('id', 'product_id', 'batch_no', 'quantity') // 🔥 ADD quantity
            ->get();

        return response()->json([
            'type' => 'batches',
            'data' => $batches
        ]);
    }
}



    // Multiple product store function
    public function store(Request $request)
    {
        // dd($request->items);

        $request->validate([
            'items'                         => 'required|array|min:1',
            'items.*.from_warehouse_id'     => 'required|exists:warehouses,id',
            'items.*.to_warehouse_id'       => 'required|different:items.*.from_warehouse_id|exists:warehouses,id',
            'items.*.category_id'           => 'required|exists:categories,id',
            'items.*.product_id'            => 'required|exists:products,id',
            'items.*.batch_id'              => 'required|exists:product_batches,id',
            'items.*.quantity'              => 'required|integer|min:1',
        ]);

        DB::transaction(function () use ($request) {

            foreach ($request->items as $item) {

                // ✅ ONLY batch validity (expiry / blocked)
                $batch = ProductBatch::findOrFail($item['batch_id']);

                if ($batch->is_blocked || $batch->expiry_date < now()->toDateString()) {
                    throw new \Exception("Batch {$batch->batch_no} is expired or blocked");
                }

                WarehouseTransfer::create([
                    'from_warehouse_id' => $item['from_warehouse_id'],
                    'to_warehouse_id'   => $item['to_warehouse_id'],
                    'category_id'       => $item['category_id'],
                    'product_id'        => $item['product_id'],
                    'batch_id'          => $item['batch_id'],
                    'quantity'          => $item['quantity'],
                    'status'            => 0,
                    'created_by'        => Auth::id(),
                ]);
            }
        });

        return redirect()
            ->route('transfer.index')
            ->with('success', 'Transfer entry saved successfully');
    }


    public function getWarehouseStock($warehouse_id, $batch_id)
    {
        $stock = WarehouseStock::where([
            'warehouse_id' => $warehouse_id,
            'batch_id'     => $batch_id,
        ])->first();

        return response()->json([
            'quantity' => $stock ? $stock->quantity : 0
        ]);
    }

    // Edit Method 
   public function edit($id)
{
    $transfer = WarehouseTransfer::with(['product', 'batch'])->findOrFail($id);

    // 🔥 FROM warehouse (locked)
    $fromWarehouse = Warehouse::where('id', $transfer->from_warehouse_id)
        ->where('status', 'active')
        ->first();

    // 🔥 TO warehouses list
    $toWarehouses = Warehouse::where('status', 'active')
        ->where('id', '!=', $transfer->from_warehouse_id)
        ->get();

    // Categories available in from warehouse
    $categories = Category::whereIn('id', function ($q) use ($transfer) {
        $q->select('category_id')
            ->from('warehouse_stock')
            ->where('warehouse_id', $transfer->from_warehouse_id)
            ->where('quantity', '>', 0);
    })->get();

    $products = Product::where('category_id', $transfer->category_id)->get();

    $selectedProducts = [$transfer->product_id];

    $batches = ProductBatch::where('product_id', $transfer->product_id)->get();

    return view('warehouse.transfer', compact(
        'transfer',
        'fromWarehouse',
        'toWarehouses',
        'categories',
        'products',
        'batches',
        'selectedProducts'
    ));
}


    // Update Method
    public function update(Request $request, $id)
    {
        $transfer = WarehouseTransfer::findOrFail($id);

        $validated = $request->validate([
            'from_warehouse_id' => 'required',
            'to_warehouse_id'   => 'required|different:from_warehouse_id',
            'category_id'       => 'required',
            'product_id'        => 'required|array|min:1',
            'batch_id'          => 'required|array|min:1',
            'quantity'          => 'required|integer|min:1',
        ]);

        DB::transaction(function () use ($transfer, $validated) {

            $transfer->update([
                'from_warehouse_id' => $validated['from_warehouse_id'],
                'to_warehouse_id'   => $validated['to_warehouse_id'],
                'category_id'       => is_array($validated['category_id'])
                    ? $validated['category_id'][0]
                    : $validated['category_id'],
                'product_id'        => $validated['product_id'][0],
                'batch_id'          => $validated['batch_id'][0],
                'quantity'          => $validated['quantity'],
            ]);
        });

        return redirect()->route('transfer.index')
            ->with('success', 'Transfer updated successfully');
    }





    public function destroy($id)
    {
        $batch = ProductBatch::findOrFail($id);
        $batch->delete(); // soft delete
        return redirect()->route('warehouse.index')->with('success', 'Batch deleted successfully');
    }

    public function show($id)
    {
        $transfer = WarehouseTransfer::with([
            'product',
            'batch',
            'fromWarehouse',
            'toWarehouse'
        ])->findOrFail($id);

        return view('warehouse.show', compact('transfer'));
    }

    public function checkBatchValidity($batch_id)
    {
        $batch = ProductBatch::find($batch_id);

        if (!$batch) {
            return response()->json([
                'valid' => false,
                'message' => 'Batch not found'
            ]);
        }

        if ($batch->expiry_date < now()->toDateString()) {
            return response()->json([
                'valid' => false,
                'message' => "Batch {$batch->batch_no} is expired"
            ]);
        }

        if ($batch->is_blocked) {
            return response()->json([
                'valid' => false,
                'message' => "Batch {$batch->batch_no} is blocked"
            ]);
        }

        return response()->json([
            'valid' => true
        ]);
    }

    public function getCategoriesByWarehouse($warehouse_id)
    {
        $categoryIds = WarehouseStock::where('warehouse_id', $warehouse_id)
            ->where('quantity', '>', 0)
            ->pluck('category_id')
            ->unique();

        $categories = Category::whereIn('id', $categoryIds)
            ->select('id', 'name')
            ->get();

        return response()->json($categories);
    }


    public function getWarehouseStockData(Request $request)
    {
        /* -------- WAREHOUSE → PRODUCTS -------- */
        if (
            $request->has('warehouse_id') &&
            !$request->has('category_ids') &&
            !$request->has('product_ids')
        ) {

            $products = WarehouseStock::where('warehouse_id', $request->warehouse_id)
                ->where('quantity', '>', 0)
                ->with('product:id,name')
                ->get()
                ->pluck('product')
                ->unique('id')
                ->values();

            return response()->json([
                'type' => 'products',
                'data' => $products
            ]);
        }

        /* -------- CATEGORY → PRODUCTS (OPTIONAL) -------- */
        if ($request->has('warehouse_id') && $request->has('category_ids')) {

            $products = WarehouseStock::where('warehouse_id', $request->warehouse_id)
                ->whereIn('category_id', $request->category_ids)
                ->where('quantity', '>', 0)
                ->with('product:id,name')
                ->get()
                ->pluck('product')
                ->unique('id')
                ->values();

            return response()->json([
                'type' => 'products',
                'data' => $products
            ]);
        }

        /* -------- PRODUCT → BATCHES (FROM product_batches ONLY) -------- */
        if ($request->has('product_ids')) {

            $batches = ProductBatch::whereIn('product_id', $request->product_ids)
                ->where('is_blocked', 0)
                ->whereDate('expiry_date', '>=', now())
                ->select('id', 'product_id', 'batch_no', 'quantity')
                ->get();

            return response()->json([
                'type' => 'batches',
                'data' => $batches
            ]);
        }

        return response()->json([]);
    }


    public function getBatchStock($batchId)
    {
        $batch = ProductBatch::where('id', $batchId)
            ->where('is_blocked', 0)
            ->whereDate('expiry_date', '>=', now())
            ->first();

        return response()->json([
            'quantity' => $batch->quantity ?? 0
        ]);
    }
}
