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
        if ($userWarehouseType === 'taluka') {
            $returns->where(function ($q) use ($warehouseId) {
                $q->where('from_warehouse_id', $warehouseId) // Taluka created
                    ->orWhere('to_warehouse_id', $warehouseId); // Taluka receiving
            });
        }

        if ($userWarehouseType === 'district') {
            $returns->where(function ($q) use ($warehouseId) {
                $q->where('from_warehouse_id', $warehouseId)
                    ->orWhere('to_warehouse_id', $warehouseId);
            });
        }

        if ($userWarehouseType === 'distribution_center') {

            $returns->where(function ($q) use ($warehouseId) {
                $q->where('from_warehouse_id', $warehouseId)
                    ->orWhere('to_warehouse_id', $warehouseId);
            });
        }


        if ($userWarehouseType === 'master') {
            $returns->where('to_warehouse_id', $warehouseId);
        }

        $returns = $returns->latest()->paginate(10);

        return view(
            'menus.warehouse-stock-return.stock-return-index',
            compact('returns', 'userWarehouseType')
        );
    }





    // Raise stock return request
    public function create()
    {
        $mode = "add";
        $user = User::with('warehouse')->findOrFail(auth()->id());

        $fromWarehouse = $user->warehouse;

        $fromWarehouseId = $fromWarehouse->id ?? null;
        /**
         * FILTER TO WAREHOUSE BASED ON LEVEL
         */
        if ($fromWarehouse?->type === 'distribution_center') {

            // Taluka → District
            $warehouses = Warehouse::where('type', 'taluka')->get();
        } elseif ($fromWarehouse?->type === 'taluka') {

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

        Log::info('📦 Stock Return Creation Started', [
            'user_id' => Auth::id(),
            'from_warehouse_id' => $request->from_warehouse_id,
            'to_warehouse_id'   => $request->to_warehouse_id,
        ]);

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

            Log::info('✅ Validation Passed');

            /** 🔐 WAREHOUSE FLOW VALIDATION */
            $fromWarehouse = Warehouse::findOrFail($request->from_warehouse_id);
            $toWarehouse   = Warehouse::findOrFail($request->to_warehouse_id);

            Log::info('🏭 Warehouse Flow Check', [
                'from_type' => $fromWarehouse->type,
                'to_type'   => $toWarehouse->type,
            ]);

            if (
                ($fromWarehouse->type === 'taluka' && $toWarehouse->type !== 'district') ||
                ($fromWarehouse->type === 'district' && $toWarehouse->type !== 'master')
            ) {
                Log::warning('❌ Invalid Warehouse Return Flow', [
                    'from' => $fromWarehouse->type,
                    'to'   => $toWarehouse->type,
                ]);
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

            Log::info('🧾 Stock Return Created', [
                'stock_return_id' => $stockReturn->id,
            ]);

            /** 📦 PROCESS ITEMS */
            foreach ($request->items as $item) {

                Log::info('➡️ Processing Item', [
                    'product_id' => $item['product_id'],
                    'batch_id'   => $item['batch_id'],
                    'qty'        => $item['return_qty'],
                ]);

                /** 🔒 LOCK PRODUCT BATCH */
                $batch = ProductBatch::where([
                    'id'           => $item['batch_id'],
                    'warehouse_id' => $request->from_warehouse_id,
                    'product_id'   => $item['product_id'],
                ])->lockForUpdate()->firstOrFail();

                if ($item['return_qty'] > $batch->quantity) {
                    Log::error('❌ Batch Stock Insufficient', [
                        'available' => $batch->quantity,
                        'requested' => $item['return_qty'],
                    ]);
                    throw new \Exception('Return quantity exceeds batch stock.');
                }

                $batch->decrement('quantity', $item['return_qty']);

                Log::info('🔻 Batch Quantity Reduced', [
                    'remaining' => $batch->quantity - $item['return_qty'],
                ]);

                /** 🔒 LOCK WAREHOUSE STOCK */
                $warehouseStock = WarehouseStock::where('warehouse_id', $request->from_warehouse_id)
                    ->where('product_id', $item['product_id'])
                    ->lockForUpdate()
                    ->firstOrFail();

                if ($item['return_qty'] > $warehouseStock->quantity) {
                    Log::error('❌ Warehouse Stock Insufficient', [
                        'available' => $warehouseStock->quantity,
                        'requested' => $item['return_qty'],
                    ]);
                    throw new \Exception('Return quantity exceeds warehouse stock.');
                }

                $warehouseStock->decrement('quantity', $item['return_qty']);

                Log::info('🔻 Warehouse Stock Reduced', [
                    'remaining' => $warehouseStock->quantity - $item['return_qty'],
                ]);

                /** 📸 IMAGE */
                $imagePath = null;
                if (!empty($item['product_image'])) {
                    $imagePath = $item['product_image']->store('stock-returns', 'public');
                    Log::info('📷 Product Image Uploaded', [
                        'path' => $imagePath,
                    ]);
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

                Log::info('🧾 Stock Return Item Saved');

                /** 🔁 STOCK MOVEMENT */
                StockMovement::create([
                    'product_batch_id' => $item['batch_id'],
                    'warehouse_id'     => $request->from_warehouse_id,
                    'type'             => 'return',
                    'quantity'         => $item['return_qty'],
                ]);

                Log::info('📊 Stock Movement Logged');
            }

            DB::commit();

            Log::info('✅ Stock Return Transaction Completed', [
                'stock_return_id' => $stockReturn->id,
            ]);

            return redirect()
                ->route('stock-returns.index')
                ->with('success', 'Warehouse stock return created successfully.');
        } catch (\Exception $e) {

            DB::rollBack();

            Log::error('❌ Stock Return Creation Failed', [
                'error' => $e->getMessage(),
                'file'  => $e->getFile(),
                'line'  => $e->getLine(),
                'user'  => Auth::id(),
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

    // district to taluka
    public function returnToTaluka(string $id)
    {
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
        if ($fromWarehouse?->type === 'distribution_center') {
            // Distribution Center can send to Talukas
            $warehouses = Warehouse::where('type', 'taluka')->get();
        } elseif ($fromWarehouse?->type === 'taluka') {
            // Taluka → No return form to lower level
            $warehouses = collect();
        } else {
            $warehouses = collect();
        }

        /** AVAILABLE STOCK IN DISTRIBUTION CENTER */
        $warehouseStocks = ProductBatch::with('product')
            ->where('warehouse_id', $fromWarehouseId)
            ->where('is_blocked', 0)
            ->get();

        $mode = 'edit'; // since we are editing an existing return

        return view('menus.warehouse-stock-return.dc-to-taluka-return-edit', compact(
            'warehouses',
            'user',
            'warehouseStocks',
            'mode',
            'stockReturn'
        ));
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

    public function dcApprove($id)
    {
        $return = WarehouseStockReturn::findOrFail($id);
        $user = auth()->user();

        if (
            $return->status !== 'draft' ||
            $user->warehouse->type !== 'taluka' ||
            $user->warehouse_id !== $return->to_warehouse_id
        ) {
            abort(403, 'Unauthorized action.');
        }

        $return->update(['status' => 'approved']);

        return back()->with('success', 'Stock return approved by Taluka.');
    }

    public function dcDispatch($id)
    {
        $return = WarehouseStockReturn::findOrFail($id);
        $user = auth()->user();

        if (
            $return->status !== 'approved' ||
            $user->warehouse->type !== 'distribution_center' ||
            $user->warehouse_id !== $return->from_warehouse_id
        ) {
            abort(403, 'Unauthorized action.');
        }

        $return->update(['status' => 'dispatched']);

        return back()->with('success', 'Stock dispatched to Taluka.');
    }

    public function dcReceive($id)
    {
        $return = WarehouseStockReturn::findOrFail($id);
        $user = auth()->user();

        if (
            $return->status !== 'dispatched' ||
            $user->warehouse->type !== 'taluka' ||
            $user->warehouse_id !== $return->to_warehouse_id
        ) {
            abort(403, 'Unauthorized action.');
        }

        $return->update(['status' => 'received']);

        return back()->with('success', 'Stock received successfully.');
    }



    // 
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


    /* Taulka to district  → dispatch */
    public function dispatch($id)
    {
        Log::info('🚚 Stock Dispatch Started', [
            'stock_return_id' => $id,
            'user_id' => auth()->id(),
        ]);

        try {
            DB::transaction(function () use ($id) {

                $return = WarehouseStockReturn::with('WarehouseStockReturnItem.product')
                    ->where('id', $id)
                    ->where('status', 'approved')
                    ->lockForUpdate()
                    ->firstOrFail();

                if (auth()->user()->warehouse_id !== $return->from_warehouse_id) {
                    abort(403, 'Unauthorized');
                }

                foreach ($return->WarehouseStockReturnItem as $item) {

                    $stock = ProductBatch::where([
                        'id'           => $item->batch_no,
                        'warehouse_id' => $return->from_warehouse_id,
                        'product_id'   => $item->product_id,
                    ])
                        ->lockForUpdate()
                        ->first();

                    if (!$stock) {
                        throw new \Exception(
                            "Batch not found for Product ID {$item->product_id}"
                        );
                    }

                    if ($stock->quantity < $item->return_qty) {
                        throw new \Exception(
                            "Insufficient stock for Product ID {$item->product_id}.
                         Available: {$stock->quantity}, Required: {$item->return_qty}"
                        );
                    }

                    // 🔴 DEDUCT ONLY HERE
                    $stock->decrement('quantity', $item->return_qty);

                    Log::info('Stock Deducted', [
                        'batch_id' => $stock->id,
                        'deducted' => $item->return_qty,
                        'remaining' => $stock->quantity,
                    ]);
                }

                $return->update([
                    'status' => 'dispatched',
                    'dispatched_at' => now(),
                ]);
            });

            return back()->with('success', 'Stock dispatched successfully');
        } catch (\Exception $e) {

            Log::error('❌ Stock Dispatch Failed', [
                'stock_return_id' => $id,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', $e->getMessage());
        }
    }

    /* district  → receive */
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



    // master will approve district return
    public function approve1($id)
    {

        $return = WarehouseStockReturn::where('id', $id)
            ->where('status', 'MASTER_CREATED')
            ->firstOrFail();

        $return->update([
            'status' => 'MASTER_APPROVED' // stays CREATED, logical step
        ]);

        return back()->with('success', 'Sent for approval');
    }
    // district will dispact master return
    public function dispatch1($id)
    {
        DB::beginTransaction();

        try {
            $return = WarehouseStockReturn::with('WarehouseStockReturnItem')->findOrFail($id);

            foreach ($return->WarehouseStockReturnItem as $item) {

                $stock = WarehouseStock::where([
                    'warehouse_id' => $return->from_warehouse_id,
                    'product_id'   => $item->product_id,
                    'batch_id'     => $item->batch_id,
                ])->lockForUpdate()->firstOrFail();

                if ($stock->quantity < $item->return_qty) {
                    throw new \Exception('Insufficient stock');
                }

                // 🔴 STOCK OUT
                $stock->decrement('quantity', $item->return_qty);
            }

            $return->update(['status' => 'MASTER_DISPATCHED']);

            DB::commit();
            return back()->with('success', 'Stock Dispatched');
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    // master will receive district return
    public function receive1($id)
    {
        DB::beginTransaction();

        try {
            $return = WarehouseStockReturn::with('WarehouseStockReturnItem')->findOrFail($id);

            foreach ($return->WarehouseStockReturnItem as $item) {

                WarehouseStock::updateOrCreate(
                    [
                        'warehouse_id' => $return->to_warehouse_id,
                        'product_id'   => $item->product_id,
                        'batch_id'     => $item->batch_id,
                    ],
                    [
                        'quantity' => DB::raw('quantity + ' . $item->return_qty)
                    ]
                );
            }

            $return->update(['status' => 'MASTER_RECEIVED']);

            DB::commit();
            return back()->with('success', 'Stock Received');
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }





    /**
     * REturn from district to master edit form page
     */
    public function returnToMaster(string $id)
    {

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
        $mode = "edit";
        return view('menus.warehouse-stock-return.return-edit', compact(
            'warehouses',
            'user',
            'warehouseStocks',
            'mode',
            'stockReturn'
        ));
    }


    /**
     * Update the district to master update controller
     *      */


    // public function update(Request $request)
    // {
    //     $user = auth()->user();

    //     // ✅ Only Taluka or District allowed
    //     if (!in_array($user->warehouse->type, ['taluka', 'district'])) {
    //         abort(403, 'Unauthorized warehouse');
    //     }

    //     // ✅ Validate
    //     $request->validate([
    //         'to_warehouse_id'            => 'required|exists:warehouses,id',
    //         'items'                      => 'required|array|min:1',
    //         'items.*.product_id'         => 'required|exists:products,id',
    //         'items.*.batch_id'           => 'required|exists:product_batches,id',
    //         'items.*.return_qty'         => 'required|integer|min:1',
    //         'remarks'                    => 'nullable|string',
    //     ]);

    //     DB::beginTransaction();

    //     try {

    //         // 🔁 Decide flow based on warehouse type
    //         if ($user->warehouse->type === 'taluka') {
    //             $status        = 'draft';              // Taluka → District
    //             $returnReason  = 'taluka_to_district';
    //         } else {
    //             $status        = 'MASTER_CREATED';     // District → Master
    //             $returnReason  = 'district_to_master';
    //         }

    //         // ✅ Create Stock Return
    //         $stockReturn = WarehouseStockReturn::create([
    //             'from_warehouse_id' => $user->warehouse_id,
    //             'to_warehouse_id'   => $request->to_warehouse_id,
    //             'return_reason'     => $returnReason,
    //             'remarks'           => $request->remarks,
    //             'status'            => $status,
    //             'created_by'        => $user->id,
    //         ]);

    //         // ✅ Items
    //         foreach ($request->items as $item) {
    //             $stockReturn->WarehouseStockReturnItem()->create([
    //                 'product_id' => $item['product_id'],
    //                 'batch_id'   => $item['batch_id'],
    //                 'return_qty' => $item['return_qty'],
    //             ]);
    //         }

    //         DB::commit();

    //         return redirect()
    //             ->route('stock-returns.index')
    //             ->with('success', 'Stock return sent successfully');
    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         throw $e;
    //     }
    // }

    public function update(Request $request, $id = null)
    {
        $user = auth()->user();

        // Remove null items to avoid validation errors
        if (isset($request->items)) {
            $request->merge([
                'items' => array_filter($request->items, fn($item) => !is_null($item) && isset($item['product_id']))
            ]);
        }

        $request->validate([
            'to_warehouse_id'            => 'required|exists:warehouses,id',
            'items'                      => 'required|array|min:1',
            'items.*.product_id'         => 'required|exists:products,id',
            'items.*.batch_id'           => 'required|exists:product_batches,id',
            'items.*.return_qty'         => 'required|integer|min:1',
            'remarks'                    => 'nullable|string',
            'return_reason'              => 'nullable|string',
        ]);

        DB::beginTransaction();

        try {
            // Determine flow
            if ($user->warehouse->type === 'taluka') {
                $status       = 'DISTRICT_CREATED';
                $returnReason = $request->return_reason ?? 'taluka_to_district';
            } else {
                $status       = 'MASTER_CREATED';
                $returnReason = $request->return_reason ?? 'district_to_master';
            }

            if ($id) {
                // ✅ Edit existing return
                $stockReturn = WarehouseStockReturn::findOrFail($id);
                $stockReturn->update([
                    'to_warehouse_id' => $request->to_warehouse_id,
                    'return_reason'   => $returnReason,
                    'remarks'         => $request->remarks,
                    'status'          => $status,
                ]);

                // Delete old items and re-insert
                $stockReturn->WarehouseStockReturnItem()->delete();
            } else {
                // ✅ Create new return
                $stockReturn = WarehouseStockReturn::create([
                    'from_warehouse_id' => $user->warehouse_id,
                    'to_warehouse_id'   => $request->to_warehouse_id,
                    'return_reason'     => $returnReason,
                    'remarks'           => $request->remarks,
                    'status'            => $status,
                    'created_by'        => $user->id,
                ]);
            }

            // Save items
            foreach ($request->items as $item) {
                $imagePath = null;
                if (!empty($item['product_image'])) {
                    $imagePath = $item['product_image']->store('stock-returns', 'public');
                }

                $stockReturn->WarehouseStockReturnItem()->create([
                    'product_id'    => $item['product_id'],
                    'batch_id'      => $item['batch_id'],
                    'return_qty'    => $item['return_qty'],
                    'product_image' => $imagePath,
                ]);
            }

            DB::commit();

            return redirect()->route('stock-returns.index')->with('success', 'Stock return saved successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Stock return transaction failed', [
                'user_id' => $user->id,
                'error'   => $e->getMessage()
            ]);
            throw $e;
        }
    }


    // returnToDistrict from taluka (distribution center stock return)
    // public function returnToDistrict($id)
    // {
    //     $user = auth()->user();

    //     $oldReturn = WarehouseStockReturn::with([
    //         'items.product',
    //         'items.batch'
    //     ])->findOrFail($id);

    //     // ✅ SECURITY
    //     if (
    //         $oldReturn->status !== 'received' ||
    //         $user->warehouse->type !== 'taluka' ||
    //         $user->warehouse_id !== $oldReturn->to_warehouse_id
    //     ) {
    //         abort(403, 'Unauthorized');
    //     }

    //     // District list
    //     $warehouses = Warehouse::where('type', 'district')->get();

    //     // Taluka stock
    //     $warehouseStocks = ProductBatch::with('product')
    //         ->where('warehouse_id', $user->warehouse_id)
    //         ->where('is_blocked', 0)
    //         ->get();

    //     $mode = 'edit';

    //     return view(
    //         'menus.warehouse-stock-return.return-edit',
    //         compact(
    //             'oldReturn',
    //             'warehouses',
    //             'warehouseStocks',
    //             'user',
    //             'mode'
    //         )
    //     );
    // }

}
