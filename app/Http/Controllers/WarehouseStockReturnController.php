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
        $user = auth()->user();
        $warehouseId = $user->warehouse_id;
        $userWarehouseType = $user->warehouse->type ?? null;

        $returns = WarehouseStockReturn::with([
            'fromWarehouse',
            'toWarehouse',
            'WarehouseStockReturnItem',
            'creator.role'
        ]);

        if ($userWarehouseType === 'district') {
            // District sees what it sent OR needs to receive
            $returns->where('from_warehouse_id', $warehouseId)
                ->orWhere('to_warehouse_id', $warehouseId);
        }

        if ($userWarehouseType === 'master') {
            // Master sees only incoming returns
            $returns->where('to_warehouse_id', $warehouseId);
        }

        $returns = $returns->latest()->paginate(10);
        // ->where(function ($q) use ($warehouseId) {
        //     $q->where('from_warehouse_id', $warehouseId)
        //         ->orWhere('to_warehouse_id', $warehouseId);
        // })
        // ->orderBy('id', 'desc')
        // ->paginate(10);

        return view(
            'menus.warehouse-stock-return.stock-return-index',
            compact('returns', 'userWarehouseType')
        );
    }

    public function create()
    {
        $mode = "add";
        $user = User::with('warehouse')->findOrFail(auth()->id());

        $fromWarehouse = $user->warehouse;

        $fromWarehouseId = $fromWarehouse->id ?? null;
        /**
         * FILTER TO WAREHOUSE BASED ON LEVEL
         */
        if ($fromWarehouse?->type === 'taluka') {

            // Taluka → District
            $warehouses = Warehouse::where('type', 'district')->get();
        } elseif ($fromWarehouse?->type === 'district') {

            // District → Master
            $warehouses = Warehouse::where('type', 'master')->get();
        } else {
            // Master → No return allowed
            $warehouses = collect();
        }

        /**
         * AVAILABLE STOCK IN LOGGED-IN WAREHOUSE
         */
        $warehouseStocks = ProductBatch::with('product')
            ->where('warehouse_id', $fromWarehouseId)
            ->where('is_blocked', 0)
            ->get();

        return view('menus.warehouse-stock-return.stock-return', compact(
            'warehouses',
            'user',
            'warehouseStocks',
            'mode'
        ));
    }


    public function store(Request $request)
    {
        DB::beginTransaction();

        try {

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

            /** 🔐 WAREHOUSE FLOW VALIDATION */
            $fromWarehouse = Warehouse::findOrFail($request->from_warehouse_id);
            $toWarehouse   = Warehouse::findOrFail($request->to_warehouse_id);

            if (
                ($fromWarehouse->type === 'taluka' && $toWarehouse->type !== 'district') ||
                ($fromWarehouse->type === 'district' && $toWarehouse->type !== 'master')
            ) {
                abort(403, 'Invalid warehouse return flow.');
            }

            /** 🧾 CREATE STOCK RETURN */
            $stockReturn = WarehouseStockReturn::create([
                'from_warehouse_id' => $request->from_warehouse_id,
                'to_warehouse_id'   => $request->to_warehouse_id,
                'return_reason'     => $request->return_reason,
                'remarks'           => $request->remarks,
                'status'            => 'draft',
                'created_by'        => Auth::id(),
            ]);

            /** 📦 PROCESS ITEMS */
            foreach ($request->items as $item) {

                /** 🔒 LOCK PRODUCT BATCH */
                $batch = ProductBatch::where([
                    'id'           => $item['batch_id'],
                    'warehouse_id' => $request->from_warehouse_id,
                    'product_id'   => $item['product_id'],
                ])->lockForUpdate()->firstOrFail();

                if ($item['return_qty'] > $batch->quantity) {
                    throw new \Exception('Return quantity exceeds batch stock.');
                }

                $batch->decrement('quantity', $item['return_qty']);

                /** 🔒 LOCK WAREHOUSE STOCK */
                $warehouseStock = WarehouseStock::where('warehouse_id', $request->from_warehouse_id)
                    ->where('product_id', $item['product_id'])
                    // ->where('batch_id', $item['batch_id'])
                    ->lockForUpdate()
                    ->firstOrFail();

                // w-3 p-2 b-4
                if ($item['return_qty'] > $warehouseStock->quantity) {
                    throw new \Exception('Return quantity exceeds warehouse stock.');
                }

                $warehouseStock->decrement('quantity', $item['return_qty']);

                /** 📸 IMAGE */
                $imagePath = null;
                if (!empty($item['product_image'])) {
                    $imagePath = $item['product_image']->store('stock-returns', 'public');
                }

                /** 🧾 RETURN ITEM */
                WarehouseStockReturnItem::create([
                    'stock_return_id' => $stockReturn->id,
                    'product_id'      => $item['product_id'],
                    'batch_no'        => $item['batch_id'],
                    'return_qty'      => $item['return_qty'],
                    'product_image'   => $imagePath,
                    'condition'       => 'good',
                ]);

                /** 🔁 STOCK MOVEMENT */
                StockMovement::create([
                    'product_batch_id' => $item['batch_id'],
                    'warehouse_id'     => $request->from_warehouse_id,
                    'type'             => 'out',
                    'quantity'         => $item['return_qty'],
                ]);
            }

            DB::commit();

            return redirect()
                ->route('stock-returns.index')
                ->with('success', 'Warehouse stock return created successfully.');
        } catch (\Exception $e) {

            DB::rollBack();

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
        $mode = "edit";

        $user = User::with('warehouse')->findOrFail(auth()->id());
        $fromWarehouse = $user->warehouse;
        $fromWarehouseId = $fromWarehouse->id ?? null;

        /** FETCH STOCK RETURN */
        $stockReturn = WarehouseStockReturn::with([
            'WarehouseStockReturnItem',
            'WarehouseStockReturnItem.batch',
            'WarehouseStockReturnItem.product'
        ])->findOrFail($id);

        /** FILTER TO WAREHOUSE */
        if ($fromWarehouse?->type === 'taluka') {
            $warehouses = Warehouse::where('type', 'district')->get();
        } elseif ($fromWarehouse?->type === 'district') {
            $warehouses = Warehouse::where('type', 'master')->get();
        } else {
            $warehouses = collect();
        }

        /** AVAILABLE STOCK */
        $warehouseStocks = ProductBatch::with('product')
            ->where('warehouse_id', $fromWarehouseId)
            ->where('is_blocked', 0)
            ->get();

        return view('menus.warehouse-stock-return.return-edit', compact(
            'warehouses',
            'user',
            'warehouseStocks',
            'mode',
            'stockReturn'
        ));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        DB::beginTransaction();

        Log::info('Stock Return Update Started', [
            'stock_return_id' => $id,
            'user_id' => auth()->id()
        ]);

        try {

            $stockReturn = WarehouseStockReturn::with('WarehouseStockReturnItem')->findOrFail($id);

            Log::info('Old Stock Return Loaded', [
                'from_warehouse' => $stockReturn->from_warehouse_id,
                'to_warehouse'   => $stockReturn->to_warehouse_id
            ]);

            /* ============================
        1️⃣ REVERSE OLD STOCK
        ============================ */
            foreach ($stockReturn->WarehouseStockReturnItem as $oldItem) {

                Log::info('Reversing Old Stock', [
                    'product_id' => $oldItem->product_id,
                    'batch_id'   => $oldItem->batch_id,
                    'qty'        => $oldItem->return_qty
                ]);

                // ADD back to district
                WarehouseStock::where([
                    'warehouse_id' => $stockReturn->from_warehouse_id,
                    'product_id'   => $oldItem->product_id,
                    'batch_id'     => $oldItem->batch_id,
                ])->increment('quantity', $oldItem->return_qty);

                // REMOVE from master
                WarehouseStock::where([
                    'warehouse_id' => $stockReturn->to_warehouse_id,
                    'product_id'   => $oldItem->product_id,
                    'batch_id'     => $oldItem->batch_id,
                ])->decrement('quantity', $oldItem->return_qty);
            }

            /* ============================
        2️⃣ UPDATE MAIN RETURN
        ============================ */
            $stockReturn->update([
                'to_warehouse_id' => $request->to_warehouse_id,
                'return_reason'   => $request->return_reason,
                'remarks'         => $request->remarks,
            ]);

            Log::info('Stock Return Main Record Updated', [
                'stock_return_id' => $stockReturn->id
            ]);

            /* ============================
        3️⃣ DELETE OLD ITEMS
        ============================ */
            $stockReturn->WarehouseStockReturnItem()->delete();

            Log::info('Old Stock Return Items Deleted', [
                'stock_return_id' => $stockReturn->id
            ]);

            /* ============================
        4️⃣ INSERT NEW ITEMS
        ============================ */
            foreach ($request->items as $item) {

                $districtStock = WarehouseStock::where([
                    'warehouse_id' => $stockReturn->from_warehouse_id,
                    'product_id'   => $item['product_id'],
                    'batch_id'     => $item['batch_id'],
                ])->first();

                if (!$districtStock || $districtStock->quantity < $item['return_qty']) {

                    Log::warning('Insufficient Stock Detected', [
                        'product_id' => $item['product_id'],
                        'batch_id'   => $item['batch_id'],
                        'available'  => $districtStock->quantity ?? 0,
                        'requested'  => $item['return_qty']
                    ]);

                    DB::rollBack();
                    return back()->withErrors('Insufficient stock for selected batch');
                }

                /* ---- IMAGE UPLOAD ---- */
                $imagePath = null;
                if (!empty($item['product_image'])) {
                    $imagePath = $item['product_image']->store('return_images', 'public');
                }

                /* ---- SAVE ITEM ---- */
                $stockReturn->items()->create([
                    'product_id'    => $item['product_id'],
                    'batch_id'      => $item['batch_id'],
                    'return_qty'    => $item['return_qty'],
                    'product_image' => $imagePath,
                ]);

                Log::info('New Stock Return Item Added', [
                    'product_id' => $item['product_id'],
                    'batch_id'   => $item['batch_id'],
                    'qty'        => $item['return_qty']
                ]);

                /* ============================
            5️⃣ STOCK CALCULATION
            ============================ */

                // 🔻 DECREMENT from district
                WarehouseStock::where([
                    'warehouse_id' => $stockReturn->from_warehouse_id,
                    'product_id'   => $item['product_id'],
                    'batch_id'     => $item['batch_id'],
                ])->decrement('quantity', $item['return_qty']);

                // 🔺 INCREMENT to master
                WarehouseStock::updateOrCreate(
                    [
                        'warehouse_id' => $stockReturn->to_warehouse_id,
                        'product_id'   => $item['product_id'],
                        'batch_id'     => $item['batch_id'],
                    ],
                    [
                        'quantity' => DB::raw('quantity + ' . (int)$item['return_qty'])
                    ]
                );
            }

            DB::commit();

            Log::info('Stock Return Updated Successfully', [
                'stock_return_id' => $stockReturn->id
            ]);

            return redirect()->route('stock-returns.index')
                ->with('success', 'Stock Return Updated Successfully');
        } catch (\Exception $e) {

            DB::rollBack();

            Log::error('Stock Return Update Failed', [
                'stock_return_id' => $id,
                'error' => $e->getMessage(),
                'line'  => $e->getLine(),
                'file'  => $e->getFile()
            ]);

            return back()->withErrors($e->getMessage());
        }
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

            if (auth()->user()->warehouse_id !== $stockReturn->to_warehouse_id) {
                abort(403, 'Unauthorized');
            }

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


    /* DISTRICT → APPROVE */
    public function approve($id)
    {
        $return = WarehouseStockReturn::findOrFail($id);
        $userWarehouseId = auth()->user()->warehouse_id;

        // Only TO warehouse (District)
        if ($return->to_warehouse_id !== $userWarehouseId) {
            abort(403, 'Unauthorized');
        }

        if ($return->status !== 'pending_approval') {
            abort(400, 'Invalid status');
        }

        $return->update([
            'status' => 'approved'
        ]);

        return back()->with('success', 'Stock approved');
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

                if (auth()->user()->warehouse_id !== $return->from_warehouse_id) {
                    abort(403, 'Unauthorized');
                }


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

                if (auth()->user()->warehouse_id !== $stockReturn->to_warehouse_id) {
                    abort(403, 'Unauthorized');
                }


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

    public function sendForApproval1($id)
    {
        $return = WarehouseStockReturn::where('id', $id)
            ->where('status', 'CREATED')
            ->firstOrFail();

        // District only
        if (auth()->user()->warehouse_id !== $return->from_warehouse_id) {
            abort(403);
        }

        $return->update([
            'status' => 'CREATED' // stays CREATED, logical step
        ]);

        return back()->with('success', 'Sent for approval');
    }
    public function approve1($id)
{
    $return = WarehouseStockReturn::findOrFail($id);

    // Master only
    if (auth()->user()->warehouse_id !== $return->to_warehouse_id) {
        abort(403);
    }

    if ($return->status !== 'CREATED') {
        abort(400, 'Invalid status');
    }

    $return->update([
        'status' => 'APPROVED',
        'approved_at' => now(),
        'approved_by' => auth()->id(),
    ]);

    return back()->with('success', 'Stock return approved');
}

}
