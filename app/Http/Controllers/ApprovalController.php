<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Warehouse;
use App\Models\WarehouseStock;
use App\Models\WarehouseTransfer;
use App\Models\ProductBatch;
use App\Models\StockMovement;
use App\Models\Category;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Product;
use App\Models\TransferChallan;


class ApprovalController extends Controller
{
   
    public function index()
    {
        $userWarehouseId = auth()->user()->warehouse_id;

        $transfers = WarehouseTransfer::with([
            'approvedByWarehouse',
            'requestedByWarehouse',
            'product'
        ])
        ->where(function ($q) use ($userWarehouseId) {

            // MASTER: Pending requests
            $q->where(function ($q2) use ($userWarehouseId) {
                $q2->where('approved_by_warehouse_id', $userWarehouseId)
                ->where('status', 0)
                ->whereNull('challan_id');
            });

            // DISTRICT: Dispatched stock
            $q->orWhere(function ($q2) use ($userWarehouseId) {
                $q2->where('requested_by_warehouse_id', $userWarehouseId)
                ->where('status', 1);
            });

        })
        ->orderBy('created_at')
        // ->get()
        // ->groupBy(function ($item) {
        //     return $item->approved_by_warehouse_id . '_' . $item->requested_by_warehouse_id;
        // });
        ->get()
        ->groupBy(function ($item) {
            return $item->approved_by_warehouse_id . '_' . $item->requested_by_warehouse_id;
        }) ?? collect();


        return view('approval.warehousetransfer', compact('transfers'));
    }

    public function bulkDispatch(Request $request)
    {
        DB::transaction(function () use ($request) {

            $transfers = WarehouseTransfer::whereIn('id', $request->transfer_ids)
                ->where('status', 0)
                ->lockForUpdate()
                ->get();

            foreach ($transfers as $transfer) {

                $sourceWarehouseId = $transfer->approved_by_warehouse_id;

                // Stock
                $sourceStock = WarehouseStock::where('warehouse_id', $sourceWarehouseId)
                    ->where('product_id', $transfer->product_id)
                    ->lockForUpdate()
                    ->first();

                if (!$sourceStock || $sourceStock->quantity < $transfer->quantity) {
                    throw new \Exception("Insufficient stock for {$transfer->product->name}");
                }

                $sourceStock->decrement('quantity', $transfer->quantity);

                // Batch
                $sourceBatch = ProductBatch::where('id', $transfer->batch_id)
                    ->where('warehouse_id', $sourceWarehouseId)
                    ->lockForUpdate()
                    ->first();

                if (!$sourceBatch || $sourceBatch->quantity < $transfer->quantity) {
                    throw new \Exception("Insufficient batch stock");
                }

                $sourceBatch->decrement('quantity', $transfer->quantity);

                // Movement
                StockMovement::create([
                    'product_batch_id' => $sourceBatch->id,
                    'type'             => 'dispatch',
                    'quantity'         => -$transfer->quantity,
                    'warehouse_id'     => $sourceWarehouseId,
                ]);

                // Status
                $transfer->update(['status' => 1]);
            }
        });

        return back()->with('success', 'All products dispatched successfully');
    }

    public function districtIndex()
    {
        $userWarehouseId = auth()->user()->warehouse_id;

        $transfers = WarehouseTransfer::with([
            'approvedByWarehouse',
            'requestedByWarehouse',
            'product',
            'challanItem'
        ])
        ->where('requested_by_warehouse_id', $userWarehouseId)
        ->where('status', 1) // Only Dispatched
        ->orderBy('created_at')
        // ->get()
        // ->groupBy(function ($item) {
        //     return $item->approved_by_warehouse_id . '_' . $item->requested_by_warehouse_id;
        // });
        ->get()
->groupBy(function ($item) {
    return $item->approved_by_warehouse_id . '_' . $item->requested_by_warehouse_id;
}) ?? collect();


        return view('district.warehouse_receive', compact('transfers'));
    }

    public function bulkReceive(Request $request)
    {
        DB::transaction(function () use ($request) {

            $transfers = WarehouseTransfer::whereIn('id', $request->transfer_ids)
                ->where('status', 1)
                ->lockForUpdate()
                ->get();

            foreach ($transfers as $transfer) 
            {
               $qty = $transfer->challan
                ->items
                ->where('product_id', $transfer->product_id)
                ->first()
                ->quantity ?? $transfer->quantity;

                $destWarehouseId = $transfer->requested_by_warehouse_id;
                $product = Product::findOrFail($transfer->product_id);

                /* DEST STOCK */
                $destStock = WarehouseStock::firstOrNew([
                    'warehouse_id' => $destWarehouseId,
                    'product_id'   => $transfer->product_id,
                ]);

                $destStock->category_id = $product->category_id;
                $destStock->quantity   = ($destStock->quantity ?? 0) + $qty;
                $destStock->save();

                /* DEST BATCH */
                $sourceBatch = ProductBatch::findOrFail($transfer->batch_id);

                $destBatch = ProductBatch::firstOrNew([
                    'warehouse_id' => $destWarehouseId,
                    'product_id'   => $transfer->product_id,
                    'batch_no'     => $sourceBatch->batch_no,
                ]);

                $destBatch->category_id  = $product->category_id;
                $destBatch->mfg_date     = $sourceBatch->mfg_date;
                $destBatch->expiry_date = $sourceBatch->expiry_date;
                $destBatch->quantity = ($destBatch->quantity ?? 0) + $qty;
                //$destBatch->quantity    = ($destBatch->quantity ?? 0) + $transfer->quantity;
                $destBatch->save();

                /* STOCK MOVEMENT */
                StockMovement::create([
                    'product_batch_id' => $destBatch->id,
                    'type'             => 'transfer',
                    'quantity' => $qty,
                    'warehouse_id'     => $destWarehouseId,
                ]);

                /* FINAL STATUS */
                $transfer->update(['status' => 2]);

                 /* FINAL STATUS (CHALLAN) */
                $challanId = $transfers->first()->challan_id ?? null;

                if ($challanId) {
                    TransferChallan::where('id', $challanId)
                        ->update(['status' => 'received']);
                }
            }
        });

        return back()->with('success', 'All stock received successfully');
    } 

    public function reject(WarehouseTransfer $transfer)
    {
        if ($transfer->status != 0) {
            return back()->with('error', 'Only pending transfers can be rejected');
        }

        DB::transaction(function () use ($transfer) {
            $transfer->status = 3; // REJECTED
            $transfer->save();
        });

        return back()->with('success', 'Transfer rejected successfully');
    }

    // OLD BUT Proper working without show insuffient stock error massage
    // public function dispatchChallan(Request $request)
    // {
    //     DB::transaction(function () use ($request) {

    //         $challan = TransferChallan::with('items')->findOrFail($request->challan_id);

    //         foreach ($challan->items as $item) {

    //             $transfer = WarehouseTransfer::where('product_id', $item->product_id)
    //                 ->where('approved_by_warehouse_id', $challan->from_warehouse_id)
    //                 ->where('requested_by_warehouse_id', $challan->to_warehouse_id)
    //                 ->where('status', 0)
    //                 ->lockForUpdate()
    //                 ->first();

    //             if (!$transfer) {
    //                 continue;
    //             }

    //             $dispatchQty = $item->quantity;   // ✅ CHALLAN QTY
    //             $warehouseId = $challan->from_warehouse_id;

    //             /* 1️⃣ WAREHOUSE STOCK */
    //             $stock = WarehouseStock::where('warehouse_id', $warehouseId)
    //                 ->where('product_id', $transfer->product_id)
    //                 ->lockForUpdate()
    //                 ->first();

    //             if (!$stock || $stock->quantity < $dispatchQty) {
    //                 throw new \Exception("Insufficient stock");
    //             }

    //             $stock->decrement('quantity', $dispatchQty);

    //             /* 2️⃣ PRODUCT BATCH */
    //             $batch = ProductBatch::where('id', $transfer->batch_id)
    //                 ->where('warehouse_id', $warehouseId)
    //                 ->lockForUpdate()
    //                 ->first();

    //             if (!$batch || $batch->quantity < $dispatchQty) {
    //                 throw new \Exception("Insufficient batch stock");
    //             }

    //             $batch->decrement('quantity', $dispatchQty);

    //             /* 3️⃣ STOCK MOVEMENT */
    //             StockMovement::create([
    //                 'product_batch_id' => $batch->id,
    //                 'type' => 'dispatch',
    //                 'quantity' => -$dispatchQty,
    //                 'warehouse_id' => $warehouseId,
    //             ]);

    //             /* 4️⃣ UPDATE TRANSFER */
    //             //$transfer->quantity = $dispatchQty;   // optional but good
    //             $transfer->status = 1;
    //             $transfer->save();
    //         }

    //         $challan->update(['status' => 'dispatched']);
    //     });

    //     return back()->with('success', 'Challan dispatched successfully');
    // }


///////////////////////////////////////////////////////////////////////////////////


    public function dispatchChallan(Request $request)
    {
        try {

            DB::beginTransaction();

            $challan = TransferChallan::with('items')->findOrFail($request->challan_id);

            foreach ($challan->items as $item) {

                $transfer = WarehouseTransfer::where('product_id', $item->product_id)
                    ->where('approved_by_warehouse_id', $challan->from_warehouse_id)
                    ->where('requested_by_warehouse_id', $challan->to_warehouse_id)
                    ->where('status', 0)
                    ->lockForUpdate()
                    ->first();

                if (!$transfer) {
                    continue;
                }

                $dispatchQty = $item->quantity;
                $warehouseId = $challan->from_warehouse_id;

                // 1️⃣ CHECK WAREHOUSE STOCK
                $stock = WarehouseStock::where('warehouse_id', $warehouseId)
                    ->where('product_id', $transfer->product_id)
                    ->lockForUpdate()
                    ->first();

                if (!$stock || $stock->quantity < $dispatchQty) {
                    DB::rollBack();
                    return back()->with('error', 'Insufficient stock in warehouse');
                }

                // 2️⃣ CHECK BATCH STOCK
                $batch = ProductBatch::where('id', $transfer->batch_id)
                    ->where('warehouse_id', $warehouseId)
                    ->lockForUpdate()
                    ->first();

                if (!$batch || $batch->quantity < $dispatchQty) {
                    DB::rollBack();
                    return back()->with('error', 'Insufficient stock in batch');
                }

                // 3️⃣ DECREMENT STOCK
                $stock->decrement('quantity', $dispatchQty);
                $batch->decrement('quantity', $dispatchQty);

                // 4️⃣ STOCK MOVEMENT
                StockMovement::create([
                    'product_batch_id' => $batch->id,
                    'type'             => 'dispatch',
                    'quantity'         => -$dispatchQty,
                    'warehouse_id'     => $warehouseId,
                ]);

                // 5️⃣ UPDATE TRANSFER
                $transfer->status = 1;
                $transfer->save();
            }

            $challan->update(['status' => 'dispatched']);

            DB::commit();

            return back()->with('success', 'Challan dispatched successfully');

        } catch (\Throwable $e) {

            DB::rollBack();

            return back()->with('error', 'Something went wrong while dispatching');
        }
    }

    public function singleDispatch(WarehouseTransfer $transfer)
    {
        if ($transfer->status != 0) {
            return back()->with('error', 'Only pending transfers can be dispatched');
        }

        DB::transaction(function () use ($transfer) {

            $sourceWarehouseId = $transfer->approved_by_warehouse_id;

            // STOCK
            $sourceStock = WarehouseStock::where('warehouse_id', $sourceWarehouseId)
                ->where('product_id', $transfer->product_id)
                ->lockForUpdate()
                ->first();

            if (!$sourceStock || $sourceStock->quantity < $transfer->quantity) {
                throw new \Exception("Insufficient stock");
            }

            $sourceStock->decrement('quantity', $transfer->quantity);

            // BATCH
            $sourceBatch = ProductBatch::where('id', $transfer->batch_id)
                ->where('warehouse_id', $sourceWarehouseId)
                ->lockForUpdate()
                ->first();

            if (!$sourceBatch || $sourceBatch->quantity < $transfer->quantity) {
                throw new \Exception("Insufficient batch stock");
            }

            $sourceBatch->decrement('quantity', $transfer->quantity);

            // STOCK MOVEMENT
            StockMovement::create([
                'product_batch_id' => $sourceBatch->id,
                'type'             => 'dispatch',
                'quantity'         => -$transfer->quantity,
                'warehouse_id'     => $sourceWarehouseId,
            ]);

            // STATUS
            $transfer->update(['status' => 1]);
        });

        return back()->with('success', 'Product dispatched successfully');
    }

    public function singleReceive(WarehouseTransfer $transfer)
    {
        if ($transfer->status != 1) {
            return back()->with('error', 'Only dispatched transfers can be received');
        }

        DB::transaction(function () use ($transfer) {

            $destWarehouseId = $transfer->requested_by_warehouse_id;
            $product = Product::findOrFail($transfer->product_id);

            // DEST STOCK
            $destStock = WarehouseStock::firstOrNew([
                'warehouse_id' => $destWarehouseId,
                'product_id'   => $transfer->product_id,
            ]);

            $destStock->category_id = $product->category_id;
            $destStock->quantity   = ($destStock->quantity ?? 0) + $transfer->quantity;
            $destStock->save();

            // DEST BATCH
            $sourceBatch = ProductBatch::findOrFail($transfer->batch_id);

            $destBatch = ProductBatch::firstOrNew([
                'warehouse_id' => $destWarehouseId,
                'product_id'   => $transfer->product_id,
                'batch_no'     => $sourceBatch->batch_no,
            ]);

            $destBatch->category_id  = $product->category_id;
            $destBatch->mfg_date     = $sourceBatch->mfg_date;
            $destBatch->expiry_date = $sourceBatch->expiry_date;
            $destBatch->quantity    = ($destBatch->quantity ?? 0) + $transfer->quantity;
            $destBatch->save();

            // STOCK MOVEMENT
            StockMovement::create([
                'product_batch_id' => $destBatch->id,
                'type'             => 'transfer',
                'quantity'         => $transfer->quantity,
                'warehouse_id'     => $destWarehouseId,
            ]);

            // FINAL STATUS
            $transfer->update(['status' => 2]);
        });

        return back()->with('success', 'Product received successfully');
    }

    public function approve(WarehouseTransfer $transfer)
    {
        if ($transfer->status != 0) {
            return back()->with('error', 'Only pending transfers can be approved');
        }

        DB::transaction(function () use ($transfer) {

            $sourceWarehouseId = $transfer->approved_by_warehouse_id;
            $destWarehouseId   = $transfer->requested_by_warehouse_id;

            /* ---------- SOURCE STOCK (PRODUCT LEVEL) ---------- */
            $sourceStock = WarehouseStock::where('warehouse_id', $sourceWarehouseId)
                ->where('product_id', $transfer->product_id)
                ->lockForUpdate()
                ->first();

            if (!$sourceStock || $sourceStock->quantity < $transfer->quantity) {
                throw new \Exception('Insufficient stock in source warehouse');
            }

            $sourceStock->decrement('quantity', $transfer->quantity);

            /* ---------- SOURCE BATCH ---------- */
            $sourceBatch = ProductBatch::where('id', $transfer->batch_id)
                ->where('warehouse_id', $sourceWarehouseId)
                ->lockForUpdate()
                ->first();

            if (!$sourceBatch || $sourceBatch->quantity < $transfer->quantity) {
                throw new \Exception('Insufficient batch stock');
            }

            $sourceBatch->decrement('quantity', $transfer->quantity);

            /* ---------- DEST STOCK ---------- */
            $product = Product::findOrFail($transfer->product_id);

            $destStock = WarehouseStock::firstOrNew([
                'warehouse_id' => $destWarehouseId,
                'product_id'   => $transfer->product_id,
            ]);

            $destStock->category_id = $product->category_id;
            $destStock->quantity   = ($destStock->quantity ?? 0) + $transfer->quantity;
            $destStock->save();

            /* ---------- DEST BATCH ---------- */
            $destBatch = ProductBatch::firstOrNew([
                'warehouse_id' => $destWarehouseId,
                'product_id'   => $transfer->product_id,
                'batch_no'     => $sourceBatch->batch_no,
            ]);

            $destBatch->category_id  = $product->category_id;
            $destBatch->mfg_date     = $sourceBatch->mfg_date;
            $destBatch->expiry_date = $sourceBatch->expiry_date;
            $destBatch->quantity    = ($destBatch->quantity ?? 0) + $transfer->quantity;
            $destBatch->save();

            /* ---------- STOCK MOVEMENT ---------- */
            StockMovement::create([
                'product_batch_id' => $sourceBatch->id,
                'type'             => 'transfer',
                'quantity'         => -$transfer->quantity,
                'warehouse_id'     => $sourceWarehouseId,
            ]);

            StockMovement::create([
                'product_batch_id' => $destBatch->id,
                'type'             => 'transfer',
                'quantity'         => $transfer->quantity,
                'warehouse_id'     => $destWarehouseId,
            ]);

            /* ---------- MARK APPROVED ---------- */
            $transfer->update([
                'status'      => 1,
                'approved_by' => auth()->id(),
                'approved_at' => now(),
            ]);
        });

        return back()->with('success', 'Transfer approved successfully');
    }  

    public function dispatch(WarehouseTransfer $transfer)
    {
        if ($transfer->status != 0) {
            return back()->with('error', 'Only pending transfers can be dispatched');
        }

        DB::transaction(function () use ($transfer) {

            $sourceWarehouseId = $transfer->approved_by_warehouse_id;

            // Product Stock
            $sourceStock = WarehouseStock::where('warehouse_id', $sourceWarehouseId)
                ->where('product_id', $transfer->product_id)
                ->lockForUpdate()
                ->first();

            if (!$sourceStock || $sourceStock->quantity < $transfer->quantity) {
                throw new \Exception('Insufficient stock');
            }

            $sourceStock->decrement('quantity', $transfer->quantity);

            // Product Batch
            $sourceBatch = ProductBatch::where('id', $transfer->batch_id)
                ->where('warehouse_id', $sourceWarehouseId)
                ->lockForUpdate()
                ->first();

            if (!$sourceBatch || $sourceBatch->quantity < $transfer->quantity) {
                throw new \Exception('Insufficient batch stock');
            }

            $sourceBatch->decrement('quantity', $transfer->quantity);

            // Stock Movement (Dispatch)
            StockMovement::create([
                'product_batch_id' => $sourceBatch->id,
                'type'             => 'dispatch',
                'quantity'         => -$transfer->quantity,
                'warehouse_id'     => $sourceWarehouseId,
            ]);

            // Update status
            $transfer->update(['status' => 1]);
        });

        return back()->with('success', 'Stock dispatched successfully');
    }

    public function receive(WarehouseTransfer $transfer)
    {
        if ($transfer->status != 1) {
            return back()->with('error', 'Only dispatched transfers can be received');
        }

        DB::transaction(function () use ($transfer) {

            $destWarehouseId = $transfer->requested_by_warehouse_id;

            $product = Product::findOrFail($transfer->product_id);

            // Destination Stock
            $destStock = WarehouseStock::firstOrNew([
                'warehouse_id' => $destWarehouseId,
                'product_id'   => $transfer->product_id,
            ]);

            $destStock->category_id = $product->category_id;
            $destStock->quantity   = ($destStock->quantity ?? 0) + $transfer->quantity;
            $destStock->save();

            // Destination Batch
            $destBatch = ProductBatch::firstOrNew([
                'warehouse_id' => $destWarehouseId,
                'product_id'   => $transfer->product_id,
                'batch_no'     => ProductBatch::find($transfer->batch_id)->batch_no,
            ]);

            $sourceBatch = ProductBatch::findOrFail($transfer->batch_id);

            $destBatch->category_id  = $product->category_id;
            $destBatch->mfg_date     = $sourceBatch->mfg_date;
            $destBatch->expiry_date = $sourceBatch->expiry_date;
            $destBatch->quantity    = ($destBatch->quantity ?? 0) + $transfer->quantity;
            $destBatch->save();

            // Stock Movement (Receive)
            StockMovement::create([
                'product_batch_id' => $destBatch->id,
                'type'             => 'transfer',
                'quantity'         => $transfer->quantity,
                'warehouse_id'     => $destWarehouseId,
            ]);

            // Final Status
            $transfer->update(['status' => 2]);
        });

        return back()->with('success', 'Stock received successfully');
    }


}
