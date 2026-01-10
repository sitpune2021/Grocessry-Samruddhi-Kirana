<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductBatch;
use App\Models\StockMovement;
use App\Models\User;
use App\Models\Warehouse;
use App\Models\WarehouseStock;
use App\Models\WarehouseStockReturn;
use App\Models\WarehouseStockReturnItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class WarehouseStockReturnController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();

        $returns = WarehouseStockReturn::with(['WarehouseStockReturnItem', 'creator.role'])
            ->orderBy('id', 'desc')
            ->paginate(10);

        return view(
            'menus.warehouse-stock-return.stock-return-index',
            compact('returns')
        );
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $users = Auth::user();

        $fromWarehouseId = $users->warehouse_id;

        $warehouses = Warehouse::where('id', '!=', $fromWarehouseId)
            ->get();
        $user = User::with('warehouse')->find(auth()->id());
        $batch = ProductBatch::where('warehouse_id', $fromWarehouseId)->get();


        // $warehouseStocks = WarehouseStock::with(['product', 'batch'])->where('warehouse_id', $fromWarehouseId)->get();
        $warehouseStocks = ProductBatch::with('product')
            ->where('warehouse_id', $fromWarehouseId)
            ->where('is_blocked', 0)
            ->get();

        return view('menus.warehouse-stock-return.stock-return', compact(
            'warehouses',
            'user',
            'warehouseStocks'
        ));
    }

    /**
     * Store a newly created resource in storage.
     */


    // public function store(Request $request)
    // {
    //     DB::beginTransaction();

    //     try {

    //         /** 🟢 REQUEST RECEIVED */
    //         Log::info('Warehouse stock return request received', [
    //             'user_id' => Auth::id(),
    //             'payload' => $request->except(['items.*.product_image'])
    //         ]);

    //         /** ✅ VALIDATION */
    //         $request->validate([
    //             'from_warehouse_id' => 'required|exists:warehouses,id',
    //             'to_warehouse_id'   => 'required|exists:warehouses,id|different:from_warehouse_id',
    //             'return_reason'     => 'required|string',
    //             'remarks'           => 'nullable|string',

    //             'items'                     => 'required|array|min:1',
    //             'items.*.product_id'        => 'required|exists:products,id',
    //             'items.*.batch_id' => 'required|exists:product_batches,id',

    //             'items.*.return_qty'        => 'required|integer|min:1',
    //             'items.*.product_image'     => 'nullable|image|max:2048',
    //         ]);

    //         Log::info('Warehouse stock return validation passed', [
    //             'user_id' => Auth::id()
    //         ]);

    //         /** 1️⃣ CREATE STOCK RETURN */
    //         $stockReturn = WarehouseStockReturn::create([
    //             'from_warehouse_id' => $request->from_warehouse_id,
    //             'to_warehouse_id'   => $request->to_warehouse_id,
    //             'return_reason'     => $request->return_reason,
    //             'remarks'           => $request->remarks,
    //             'status'            => 'draft',
    //             'created_by'        => Auth::id(),
    //         ]);

    //         Log::info('Warehouse stock return created', [
    //             'stock_return_id' => $stockReturn->id,
    //             'from_warehouse'  => $request->from_warehouse_id,
    //             'to_warehouse'    => $request->to_warehouse_id
    //         ]);

    //         /** 2️⃣ LOOP ITEMS */
    //         foreach ($request->items as $index => $item) {

    //             Log::debug('Processing stock return item', [
    //                 'stock_return_id' => $stockReturn->id,
    //                 'product_id'      => $item['product_id'],
    //                 'batch_id'        => $item['batch_id'],
    //                 'return_qty'      => $item['return_qty']
    //             ]);

    //             $batchStock = ProductBatch::where([
    //                 'id'           => $item['batch_id'],
    //                 'warehouse_id' => $request->from_warehouse_id,
    //                 'product_id'   => $item['product_id'],
    //             ])->lockForUpdate()->first();


    //             if (!$batchStock) {
    //                 Log::warning('Stock not found for return item', [
    //                     'product_id'  => $item['product_id'],
    //                     'batch_id'    => $item['batch_id'],
    //                     'warehouse_id' => $request->from_warehouse_id
    //                 ]);

    //                 throw new \Exception('Stock not found for selected product & batch.');
    //             }

    //             if ($item['return_qty'] > $batchStock->quantity) {
    //                 Log::warning('Return quantity exceeds available stock', [
    //                     'product_id'      => $item['product_id'],
    //                     'batch_id'        => $item['batch_id'],
    //                     'available_qty'   => $batchStock->quantity,
    //                     'requested_qty'   => $item['return_qty']
    //                 ]);

    //                 throw new \Exception('Return quantity cannot exceed available stock.');
    //             }

    //             /** IMAGE UPLOAD */
    //             $imagePath = null;
    //             if (!empty($item['product_image'])) {
    //                 $imagePath = $item['product_image']->store('stock-returns', 'public');

    //                 Log::debug('Product image uploaded', [
    //                     'path' => $imagePath
    //                 ]);
    //             }

    //             /** 3️⃣ INSERT ITEM */
    //             WarehouseStockReturnItem::create([
    //                 'stock_return_id' => $stockReturn->id,
    //                 'product_id'      => $item['product_id'],
    //                 'batch_no'        => $item['batch_id'],
    //                 'return_qty'      => $item['return_qty'],
    //                 'product_image'   => $imagePath,
    //                 'condition'       => 'good',
    //             ]);
    //         }

    //         DB::commit();

    //         Log::info('Warehouse stock return saved successfully', [
    //             'stock_return_id' => $stockReturn->id,
    //             'created_by'      => Auth::id()
    //         ]);

    //         return redirect()
    //             ->route('stock-returns.index')
    //             ->with('success', 'Warehouse stock return saved successfully.');
    //     } catch (\Exception $e) {

    //         DB::rollBack();

    //         Log::error('Warehouse stock return failed', [
    //             'user_id' => Auth::id(),
    //             'message' => $e->getMessage(),
    //             'trace'   => $e->getTraceAsString()
    //         ]);

    //         return back()
    //             ->withInput()
    //             ->with('error', $e->getMessage());
    //     }
    // }

    public function store(Request $request)
    {
        DB::beginTransaction();

        try {

            Log::info('🟢 Warehouse stock return request received', [
                'user_id' => Auth::id(),
            ]);

            /** ✅ VALIDATION */
            $request->validate([
                'from_warehouse_id' => 'required|exists:warehouses,id',
                'to_warehouse_id'   => 'required|exists:warehouses,id|different:from_warehouse_id',
                'return_reason'     => 'required|string',
                'remarks'           => 'nullable|string',

                'items'                     => 'required|array|min:1',
                'items.*.product_id'        => 'required|exists:products,id',
                'items.*.batch_id'          => 'required|exists:product_batches,id',
                'items.*.return_qty'        => 'required|integer|min:1',
                'items.*.product_image'     => 'nullable|image|max:2048',
            ]);

            /** 1️⃣ CREATE STOCK RETURN (DRAFT) */
            $stockReturn = WarehouseStockReturn::create([
                'from_warehouse_id' => $request->from_warehouse_id,
                'to_warehouse_id'   => $request->to_warehouse_id,
                'return_reason'     => $request->return_reason,
                'remarks'           => $request->remarks,
                'status'            => 'draft',
                'created_by'        => Auth::id(),
            ]);

            /** 2️⃣ PROCESS ITEMS */
            foreach ($request->items as $item) {

                /** 🔒 LOCK PRODUCT BATCH */
                $batch = ProductBatch::where([
                    'id'           => $item['batch_id'],
                    'warehouse_id' => $request->from_warehouse_id,
                    'product_id'   => $item['product_id'],
                ])->lockForUpdate()->first();

                if (!$batch) {
                    throw new \Exception('Product batch stock not found.');
                }

                if ($item['return_qty'] > $batch->quantity) {
                    throw new \Exception('Return quantity exceeds batch stock.');
                }

                /** 📉 DEDUCT FROM PRODUCT_BATCHES */
                $batch->decrement('quantity', $item['return_qty']);

                /** 🔒 LOCK WAREHOUSE STOCK */
                $warehouseStock = WarehouseStock::where([
                    'warehouse_id' => $request->from_warehouse_id,  //3
                    'product_id'   => $item['product_id'],// 1
                    'batch_id'     => $item['batch_id'],//1
                ])->lockForUpdate()->first();
// dd(  $warehouseStock);
                if (!$warehouseStock) {
                    throw new \Exception('Warehouse stock not found.');
                }

                if ($item['return_qty'] > $warehouseStock->quantity) {
                    throw new \Exception('Return quantity exceeds warehouse stock.');
                }

                /** 📉 DEDUCT FROM WAREHOUSE_STOCKS */
                $warehouseStock->decrement('quantity', $item['return_qty']);

                /** 📦 IMAGE UPLOAD */
                $imagePath = null;
                if (!empty($item['product_image'])) {
                    $imagePath = $item['product_image']->store('stock-returns', 'public');
                }

                /** 🧾 INSERT RETURN ITEM */
                WarehouseStockReturnItem::create([
                    'stock_return_id' => $stockReturn->id,
                    'product_id'      => $item['product_id'],
                    'batch_id'        => $item['batch_id'],
                    'return_qty'      => $item['return_qty'],
                    'product_image'   => $imagePath,
                    'condition'       => 'good',
                ]);

                /** 🧮 INSERT STOCK MOVEMENT (OUT) */
                StockMovement::create([
                    'product_batch_id' => $batch->id,
                    'type'             => 'out',
                    'quantity'         => $item['return_qty'],
                ]);
            }

            DB::commit();

            Log::info('✅ Warehouse stock return completed', [
                'stock_return_id' => $stockReturn->id,
            ]);

            return redirect()
                ->route('stock-returns.index')
                ->with('success', 'Warehouse stock return created successfully.');
        } catch (\Exception $e) {

            DB::rollBack();

            Log::error('❌ Warehouse stock return failed', [
                'message' => $e->getMessage(),
            ]);

            return back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $return = WarehouseStockReturn::with([
            'items.product',
            'fromWarehouse',
            'toWarehouse'
        ])->findOrFail($id);

        return view('stock_returns.show', compact('return'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
    public function downloadPdf(string $id)
    {
        $return = WarehouseStockReturn::with([
            'WarehouseStockReturnItem',
            'fromWarehouse',
            'toWarehouse'
        ])->findOrFail($id);

        return view(
            'menus.warehouse-stock-return.challan-draft',
            compact('return')
        );
    }

    public function sendForApproval($id)
    {
        try {
            $stockReturn = WarehouseStockReturn::where('id', $id)
                ->where('status', 'draft')
                ->firstOrFail();

            $stockReturn->update([
                'status'      => 'approved',
                'approved_by' => auth()->id(),
            ]);

            Log::info('Stock return sent for approval', [
                'stock_return_id' => $stockReturn->id,
                'user_id' => auth()->id()
            ]);

            return back()->with('success', 'Stock return sent for approval.');
        } catch (\Exception $e) {

            Log::error('Error sending stock return for approval', [
                'error' => $e->getMessage()
            ]);

            return back()->with('error', $e->getMessage());
        }
    }

    public function dispatch($id)
    {
        Log::info('🚚 Stock Dispatch Started', [
            'stock_return_id' => $id,
            'user_id' => auth()->id(),
        ]);

        try {
            DB::transaction(function () use ($id) {

                // 1️⃣ Fetch approved stock return
                $return = WarehouseStockReturn::with('WarehouseStockReturnItem.product')
                    ->where('id', $id)
                    ->where('status', 'approved')
                    ->lockForUpdate()
                    ->firstOrFail();


                Log::info('Stock Return Approved & Locked', [
                    'return_id' => $return->id,
                    'from_warehouse' => $return->from_warehouse_id,
                    'to_warehouse' => $return->to_warehouse_id,
                ]);

                foreach ($return->WarehouseStockReturnItem as $item) {

                    $product = $item->product;

                    Log::info('Dispatching Item', [
                        'product_id' => $item->product_id,
                        'product_name' => $product->name ?? null,
                        'batch_no' => $item->batch_no,
                        'dispatch_qty' => $item->return_qty,
                    ]);

                    // 2️⃣ Lock stock from source warehouse
                    $stock =  ProductBatch::where([
                        'id'           => $item->batch_no, //1
                        'warehouse_id' => $return->from_warehouse_id, //3
                        'product_id'   => $item->product_id, //1
                    ])
                        ->lockForUpdate()
                        ->first();


                    Log::info('Source Stock Found', [
                        'warehouse_stock_id' => $stock->id,
                        'available_qty' => $stock->quantity,
                    ]);

                    // 3️⃣ Validate stock
                    if ($item->return_qty > $stock->quantity) {

                        Log::error('❌ Insufficient Stock During Dispatch', [
                            'product_id' => $item->product_id,
                            'required_qty' => $item->return_qty,
                            'available_qty' => $stock->quantity,
                        ]);

                        throw new \Exception('Insufficient stock during dispatch');
                    }

                    // 4️⃣ Deduct stock
                    $stock->decrement('quantity', $item->return_qty);

                    Log::info('Stock Deducted Successfully', [
                        'warehouse_stock_id' => $stock->id,
                        'deducted_qty' => $item->return_qty,
                        'remaining_qty' => $stock->quantity - $item->return_qty,
                    ]);
                }

                // 5️⃣ Update return status
                $return->update([
                    'status'        => 'dispatched',
                    'dispatched_at' => now(),
                ]);

                Log::info('🚚 Stock Return Dispatched', [
                    'return_id' => $return->id,
                    'dispatched_at' => now(),
                ]);
            });

            Log::info('✅ Stock Dispatch Completed Successfully', [
                'stock_return_id' => $id,
            ]);

            return back()->with('success', 'Stock dispatched successfully.');
        } catch (\Exception $e) {

            Log::error('❌ Stock Dispatch Failed', [
                'stock_return_id' => $id,
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return back()->with('error', 'Stock dispatch failed. Please check logs.');
        }
    }

    public function receive($id)
    {
        Log::info('📦 Stock Receive Process Started', [
            'stock_return_id' => $id,
            'user_id' => auth()->id(),
        ]);

        try {
            DB::transaction(function () use ($id) {

                // 1️⃣ Fetch dispatched stock return
                $stockReturn = WarehouseStockReturn::with('WarehouseStockReturnItem')
                    ->where('id', $id)
                    ->where('status', 'dispatched')
                    ->lockForUpdate()
                    ->firstOrFail();

                Log::info('Stock Return Fetched', [
                    'id' => $stockReturn->id,
                    'from_warehouse' => $stockReturn->from_warehouse_id,
                    'to_warehouse' => $stockReturn->to_warehouse_id,
                ]);

                foreach ($stockReturn->WarehouseStockReturnItem as $item) {

                    Log::info('Processing Item', [
                        'product_id' => $item->product_id,
                        'batch_no' => $item->batch_no,
                        'return_qty' => $item->return_qty,
                    ]);

                    // 2️⃣ Check stock in destination warehouse
                    $warehouseStock = WarehouseStock::where([
                        'warehouse_id' => $stockReturn->to_warehouse_id,
                        'product_id'   => $item->product_id,
                        'batch_id'     => $item->batch_no,
                    ])->lockForUpdate()->first();
                    $product = $item->product;

                    if ($warehouseStock) {
                        Log::info('Existing stock found, incrementing quantity', [
                            'warehouse_stock_id' => $warehouseStock->id,
                            'old_qty' => $warehouseStock->quantity,
                            'add_qty' => $item->return_qty,
                        ]);

                        $warehouseStock->increment('quantity', $item->return_qty);

                        Log::info('Stock quantity updated', [
                            'new_qty' => $warehouseStock->quantity + $item->return_qty,
                        ]);
                    } else {
                        Log::info('No existing stock found, creating new record');

                        WarehouseStock::create([
                            'warehouse_id' => $stockReturn->to_warehouse_id,
                            'product_id'   => $item->product_id,
                            'batch_id'     => $item->batch_no,
                            'quantity'     => $item->return_qty,
                            'category_id'      => $product?->category_id,
                            'sub_category_id'  => $product?->sub_category_id,
                        ]);
                    }
                }

                // 3️⃣ Update stock return status
                $stockReturn->update([
                    'status'      => 'received',
                    'received_at' => now(),
                ]);

                Log::info('Stock Return Marked as Received', [
                    'stock_return_id' => $stockReturn->id,
                ]);
            });

            Log::info('✅ Stock Receive Process Completed Successfully', [
                'stock_return_id' => $id,
            ]);

            return back()->with('success', 'Stock received successfully.');
        } catch (\Exception $e) {

            Log::error('❌ Stock Receive Failed', [
                'stock_return_id' => $id,
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()->with('error', 'Stock receive failed. Please check logs.');
        }
    }

    // public function close($id)
    // {
    //     try {
    //         $stockReturn = WarehouseStockReturn::where('id', $id)
    //             ->where('status', 'received')
    //             ->firstOrFail();

    //         $stockReturn->update([
    //             'status' => 'closed'
    //         ]);

    //         Log::info('📦 Stock Return Closed', ['id' => $id]);

    //         return back()->with('success', 'Stock return closed successfully.');
    //     } catch (\Exception $e) {
    //         return back()->with('error', 'Unable to close stock return.');
    //     }
    // }
}
