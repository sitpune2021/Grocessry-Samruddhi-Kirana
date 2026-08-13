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
use App\Models\RetailerOrder;
use App\Models\RetailerOrderItem;

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


        /*
            |--------------------------------------------------------------------------
            | Retailer Orders For Logged-in Distribution Center
            |--------------------------------------------------------------------------
        */

        // $retailerOrders = RetailerOrder::with([
        //         'retailer',
        //         'items.product',
        //         'items.category'
        //     ])
        //     ->where('warehouse_id', $userWarehouseId)
        //     ->latest()
        //     ->get();

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


    public function retailerindex()
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
        ->get()
        ->groupBy(function ($item) {
            return $item->approved_by_warehouse_id . '_' . $item->requested_by_warehouse_id;
        }) ?? collect();


        /*
            |--------------------------------------------------------------------------
            | Retailer Orders For Logged-in Distribution Center
            |--------------------------------------------------------------------------
        */

        $retailerOrders = RetailerOrder::with([
                'retailer',
                'items.product',
                'items.category'
            ])
            ->where('warehouse_id', $userWarehouseId)
            ->latest()
            ->get();

        return view('retailers.retailer_order', compact('transfers', 'retailerOrders'));
    }

    /**
        * Approve Retailer Order
    */
    public function approveRetailerOrder(RetailerOrder $order)
    {
        try {

            /*
            |--------------------------------------------------------------------------
            | 1. Logged-in User Warehouse
            |--------------------------------------------------------------------------
            */

            $userWarehouseId = auth()->user()->warehouse_id;


            /*
            |--------------------------------------------------------------------------
            | 2. Verify Order Belongs To Logged-in DC
            |--------------------------------------------------------------------------
            */

            if ((int) $order->warehouse_id !== (int) $userWarehouseId) {

                return back()->with(
                    'error',
                    'You are not authorized to approve this retailer order.'
                );
            }


            /*
            |--------------------------------------------------------------------------
            | 3. Check Order Status
            |--------------------------------------------------------------------------
            */

            if ($order->status !== 'pending') {

                return back()->with(
                    'error',
                    'Only pending retailer orders can be approved.'
                );
            }


            /*
            |--------------------------------------------------------------------------
            | 4. Approve Order
            |--------------------------------------------------------------------------
            */

            DB::transaction(function () use ($order) {

                $order->update([
                    'status' => 'approved',
                ]);

            });


            return back()->with(
                'success',
                'Retailer order approved successfully.'
            );


        } catch (\Throwable $e) {

            Log::error(
                'Retailer Order Approval Error',
                [
                    'order_id' => $order->id ?? null,
                    'user_id'  => auth()->id(),
                    'error'    => $e->getMessage(),
                    'line'     => $e->getLine(),
                    'file'     => $e->getFile(),
                ]
            );


            return back()->with(
                'error',
                'Unable to approve retailer order.'
            );
        }
    }

    /**
        * Reject Retailer Order
    */
    public function rejectRetailerOrder(RetailerOrder $order)
    {
        try {

            /*
            |--------------------------------------------------------------------------
            | 1. Logged-in User Warehouse
            |--------------------------------------------------------------------------
            */

            $userWarehouseId = auth()->user()->warehouse_id;


            /*
            |--------------------------------------------------------------------------
            | 2. Verify Order Belongs To Logged-in DC
            |--------------------------------------------------------------------------
            */

            if ((int) $order->warehouse_id !== (int) $userWarehouseId) {

                return back()->with(
                    'error',
                    'You are not authorized to reject this retailer order.'
                );
            }


            /*
            |--------------------------------------------------------------------------
            | 3. Only Pending Order Can Be Rejected
            |--------------------------------------------------------------------------
            */

            if ($order->status !== 'pending') {

                return back()->with(
                    'error',
                    'Only pending retailer orders can be rejected.'
                );
            }


            /*
            |--------------------------------------------------------------------------
            | 4. Reject Order
            |--------------------------------------------------------------------------
            */

            DB::transaction(function () use ($order) {

                $order->update([
                    'status' => 'cancelled',
                ]);

            });


            return back()->with(
                'success',
                'Retailer order rejected successfully.'
            );


        } catch (\Throwable $e) {

            Log::error(
                'Retailer Order Rejection Error',
                [
                    'order_id' => $order->id ?? null,
                    'user_id'  => auth()->id(),
                    'error'    => $e->getMessage(),
                    'line'     => $e->getLine(),
                    'file'     => $e->getFile(),
                ]
            );


            return back()->with(
                'error',
                'Unable to reject retailer order.'
            );
        }
    }
  
    /**
        * Dispatch Retailer Order
    */
    public function dispatchRetailerOrder(RetailerOrder $order)
    {
        Log::info('=================================================');
        Log::info('RETAILER ORDER DISPATCH START');
        Log::info('=================================================');

        Log::info('Dispatch Request Received', [
            'order_id'           => $order->id,
            'order_no'           => $order->order_no,
            'order_status'       => $order->status,
            'order_warehouse_id' => $order->warehouse_id,
            'logged_user_id'     => auth()->id(),
            'logged_warehouse_id'=> auth()->user()->warehouse_id ?? null,
        ]);

        try {

            DB::transaction(function () use ($order) {

                // ============================================================
                // 1. GET LOGGED-IN DC WAREHOUSE
                // ============================================================

                $userWarehouseId = auth()->user()->warehouse_id;

                Log::info('STEP 1: Logged-in DC Warehouse', [
                    'user_id'      => auth()->id(),
                    'warehouse_id' => $userWarehouseId,
                ]);

                if (!$userWarehouseId) {

                    throw new \Exception(
                        'Logged-in user does not have a warehouse assigned.'
                    );
                }


                // ============================================================
                // 2. CHECK ORDER BELONGS TO LOGGED-IN DC
                // ============================================================

                Log::info('STEP 2: Checking Order Warehouse', [
                    'order_id'            => $order->id,
                    'order_warehouse_id'  => $order->warehouse_id,
                    'logged_warehouse_id' => $userWarehouseId,
                ]);

                if ((int) $order->warehouse_id !== (int) $userWarehouseId) {

                    throw new \Exception(
                        'This retailer order does not belong to your DC warehouse.'
                    );
                }


                // ============================================================
                // 3. CHECK ORDER STATUS
                // ============================================================

                Log::info('STEP 3: Checking Order Status', [
                    'order_id' => $order->id,
                    'status'   => $order->status,
                ]);

                if ($order->status !== 'approved') {

                    throw new \Exception(
                        'Only approved retailer orders can be dispatched.'
                    );
                }


                // ============================================================
                // 4. LOAD ORDER ITEMS
                // ============================================================

                $order->load([
                    'items.product'
                ]);

                Log::info('STEP 4: Order Items Loaded', [
                    'order_id'    => $order->id,
                    'items_count' => $order->items->count(),
                ]);


                if ($order->items->isEmpty()) {

                    throw new \Exception(
                        'This retailer order has no items.'
                    );
                }


                // ============================================================
                // 5. PROCESS EACH ORDER ITEM
                // ============================================================

                foreach ($order->items as $item) {

                    $productId   = $item->product_id;
                    $requiredQty = (int) $item->quantity;

                    $productName = $item->product->name ?? 'Unknown Product';


                    Log::info('-------------------------------------------------');
                    Log::info('STEP 5: Processing Retailer Order Product', [
                        'order_id'       => $order->id,
                        'order_no'       => $order->order_no,
                        'product_id'     => $productId,
                        'product_name'   => $productName,
                        'required_qty'   => $requiredQty,
                        'warehouse_id'   => $userWarehouseId,
                    ]);


                    // ========================================================
                    // 6. CHECK ORDER QUANTITY
                    // ========================================================

                    if ($requiredQty <= 0) {

                        throw new \Exception(
                            "Invalid quantity for {$productName}."
                        );
                    }


                    // ========================================================
                    // 7. GET DC TOTAL PRODUCT STOCK
                    // ========================================================

                    $warehouseStock = WarehouseStock::where(
                            'warehouse_id',
                            $userWarehouseId
                        )
                        ->where('product_id', $productId)
                        ->lockForUpdate()
                        ->first();


                    Log::info('STEP 7: Warehouse Stock Found', [
                        'warehouse_stock_id' =>
                            $warehouseStock->id ?? null,

                        'warehouse_id' =>
                            $userWarehouseId,

                        'product_id' =>
                            $productId,

                        'quantity' =>
                            $warehouseStock->quantity ?? null,
                    ]);


                    if (!$warehouseStock) {

                        throw new \Exception(
                            "Warehouse stock not found for {$productName}."
                        );
                    }


                    // ========================================================
                    // 8. CHECK DC TOTAL STOCK
                    // ========================================================

                    $warehouseQtyBefore = (int) $warehouseStock->quantity;


                    Log::info('STEP 8: Checking Warehouse Stock', [
                        'product_id'       => $productId,
                        'product_name'     => $productName,
                        'warehouse_before' => $warehouseQtyBefore,
                        'required_qty'     => $requiredQty,
                    ]);


                    if ($warehouseQtyBefore < $requiredQty) {

                        throw new \Exception(
                            "Insufficient DC stock for {$productName}. "
                            . "Available: {$warehouseQtyBefore}, "
                            . "Required: {$requiredQty}"
                        );
                    }


                    // ========================================================
                    // 9. GET ACTUAL BATCHES FROM DC
                    // ========================================================
                    //
                    // IMPORTANT:
                    //
                    // We DO NOT deduct warehouse_transfers.quantity.
                    //
                    // We DO NOT use warehouse_transfers.batch_id.
                    //
                    // We use actual ProductBatch records belonging to
                    // the logged-in DC warehouse.
                    //
                    // FIFO = oldest batch first.
                    //
                    // ========================================================

                    $batches = ProductBatch::where(
                            'warehouse_id',
                            $userWarehouseId
                        )
                        ->where('product_id', $productId)
                        ->where('quantity', '>', 0)
                        ->where('is_blocked', 0)
                        ->orderBy('id', 'asc')
                        ->lockForUpdate()
                        ->get();


                    Log::info('STEP 9: DC Product Batches Found', [
                        'warehouse_id' => $userWarehouseId,
                        'product_id'   => $productId,
                        'batch_count'  => $batches->count(),

                        'batches' => $batches->map(function ($batch) {

                            return [
                                'batch_id'     => $batch->id,
                                'batch_no'     => $batch->batch_no,
                                'product_id'   => $batch->product_id,
                                'warehouse_id' => $batch->warehouse_id,
                                'quantity'     => $batch->quantity,
                                'expiry_date'  => $batch->expiry_date,
                                'is_blocked'   => $batch->is_blocked,
                            ];

                        })->toArray(),
                    ]);


                    if ($batches->isEmpty()) {

                        throw new \Exception(
                            "No available batch found in DC warehouse for {$productName}."
                        );
                    }


                    // ========================================================
                    // 10. CALCULATE TOTAL BATCH STOCK
                    // ========================================================

                    $totalBatchQty = (int) $batches->sum('quantity');


                    Log::info('STEP 10: Total Batch Quantity', [
                        'product_id'      => $productId,
                        'product_name'    => $productName,
                        'required_qty'    => $requiredQty,
                        'total_batch_qty' => $totalBatchQty,
                        'warehouse_qty'   => $warehouseQtyBefore,
                    ]);


                    if ($totalBatchQty < $requiredQty) {

                        throw new \Exception(
                            "Insufficient batch stock for {$productName}. "
                            . "Available batch quantity: {$totalBatchQty}, "
                            . "Required: {$requiredQty}"
                        );
                    }


                    // ========================================================
                    // 11. REMAINING ORDER QUANTITY
                    // ========================================================

                    $remainingQty = $requiredQty;


                    // ========================================================
                    // 12. FIFO BATCH DEDUCTION
                    // ========================================================

                    foreach ($batches as $batch) {

                        if ($remainingQty <= 0) {
                            break;
                        }


                        $availableBatchQty = (int) $batch->quantity;


                        if ($availableBatchQty <= 0) {
                            continue;
                        }


                        // ====================================================
                        // HOW MUCH TO DEDUCT FROM THIS BATCH
                        // ====================================================

                        $deductQty = min(
                            $remainingQty,
                            $availableBatchQty
                        );


                        Log::info('STEP 12: Batch Deduction Starting', [
                            'order_id'          => $order->id,
                            'product_id'        => $productId,
                            'product_name'      => $productName,
                            'batch_id'          => $batch->id,
                            'batch_no'          => $batch->batch_no,
                            'batch_before'      => $availableBatchQty,
                            'warehouse_before'  => $warehouseStock->quantity,
                            'remaining_order'   => $remainingQty,
                            'deduct_qty'        => $deductQty,
                        ]);


                        // ====================================================
                        // 13. DEDUCT DC WAREHOUSE TOTAL STOCK
                        // ====================================================

                        $warehouseStock->decrement(
                            'quantity',
                            $deductQty
                        );

                        $warehouseStock->refresh();


                        Log::info('STEP 13: Warehouse Stock Deducted', [
                            'warehouse_stock_id' =>
                                $warehouseStock->id,

                            'product_id' =>
                                $productId,

                            'warehouse_id' =>
                                $userWarehouseId,

                            'deducted_qty' =>
                                $deductQty,

                            'warehouse_before' =>
                                $warehouseQtyBefore,

                            'warehouse_after' =>
                                $warehouseStock->quantity,
                        ]);


                        // ====================================================
                        // 14. DEDUCT PRODUCT BATCH STOCK
                        // ====================================================

                        $batch->decrement(
                            'quantity',
                            $deductQty
                        );

                        $batch->refresh();


                        Log::info('STEP 14: Product Batch Deducted', [
                            'batch_id' =>
                                $batch->id,

                            'batch_no' =>
                                $batch->batch_no,

                            'product_id' =>
                                $productId,

                            'warehouse_id' =>
                                $userWarehouseId,

                            'deducted_qty' =>
                                $deductQty,

                            'batch_before' =>
                                $availableBatchQty,

                            'batch_after' =>
                                $batch->quantity,
                        ]);


                        // ====================================================
                        // 15. CREATE STOCK MOVEMENT
                        // ====================================================

                        $movement = StockMovement::create([
                            'product_batch_id' => $batch->id,
                            'type'             => 'dispatch',
                            'quantity'         => -$deductQty,
                            'warehouse_id'     => $userWarehouseId,
                        ]);


                        Log::info('STEP 15: Stock Movement Created', [
                            'movement_id'      => $movement->id ?? null,
                            'product_batch_id' => $batch->id,
                            'warehouse_id'     => $userWarehouseId,
                            'product_id'       => $productId,
                            'quantity'         => -$deductQty,
                        ]);


                        // ====================================================
                        // 16. UPDATE REMAINING ORDER QUANTITY
                        // ====================================================

                        $remainingQty -= $deductQty;


                        Log::info('STEP 16: Remaining Order Quantity', [
                            'product_id'      => $productId,
                            'required_qty'    => $requiredQty,
                            'deducted_qty'    => $deductQty,
                            'remaining_qty'   => $remainingQty,
                        ]);
                    }


                    // ========================================================
                    // 17. VERIFY COMPLETE DISPATCH
                    // ========================================================

                    if ($remainingQty > 0) {

                        throw new \Exception(
                            "Unable to dispatch complete quantity for "
                            . "{$productName}. "
                            . "Remaining quantity: {$remainingQty}"
                        );
                    }


                    Log::info('STEP 17: Product Successfully Dispatched', [
                        'product_id'   => $productId,
                        'product_name' => $productName,
                        'quantity'     => $requiredQty,
                    ]);
                }


                // ============================================================
                // 18. UPDATE RETAILER ORDER STATUS
                // ============================================================

                Log::info('STEP 18: Updating Retailer Order Status', [
                    'order_id'   => $order->id,
                    'order_no'   => $order->order_no,
                    'old_status' => $order->status,
                    'new_status' => 'dispatched',
                ]);


                $order->status = 'dispatched';
                $order->save();


                // ============================================================
                // 19. VERIFY ORDER STATUS
                // ============================================================

                $order->refresh();


                Log::info('STEP 19: Retailer Order Status After Save', [
                    'order_id' => $order->id,
                    'order_no' => $order->order_no,
                    'status'   => $order->status,
                ]);


                if ($order->status !== 'dispatched') {

                    throw new \Exception(
                        'Retailer order status could not be updated to dispatched.'
                    );
                }


                // ============================================================
                // 20. FINAL TRANSACTION LOG
                // ============================================================

                Log::info(
                    '================================================='
                );

                Log::info(
                    'RETAILER ORDER DISPATCH TRANSACTION SUCCESS'
                );

                Log::info(
                    '================================================='
                );
            });


            // ================================================================
            // SUCCESS RESPONSE
            // ================================================================

            Log::info('RETAILER DISPATCH SUCCESS', [
                'order_id' => $order->id,
                'order_no' => $order->order_no,
            ]);


            return back()->with(
                'success',
                'Retailer order dispatched successfully and DC stock deducted.'
            );


        } catch (\Throwable $e) {

            // ================================================================
            // ERROR LOG
            // ================================================================

            Log::error(
                '================================================='
            );

            Log::error(
                'RETAILER ORDER DISPATCH FAILED'
            );

            Log::error(
                '=================================================',
                [
                    'order_id' =>
                        $order->id ?? null,

                    'order_no' =>
                        $order->order_no ?? null,

                    'order_status' =>
                        $order->status ?? null,

                    'order_warehouse_id' =>
                        $order->warehouse_id ?? null,

                    'user_id' =>
                        auth()->id(),

                    'warehouse_id' =>
                        auth()->user()->warehouse_id ?? null,

                    'error_message' =>
                        $e->getMessage(),

                    'error_line' =>
                        $e->getLine(),

                    'error_file' =>
                        $e->getFile(),

                    'trace' =>
                        $e->getTraceAsString(),
                ]
            );


            return back()->with(
                'error',
                $e->getMessage()
            );
        }
    }


}
