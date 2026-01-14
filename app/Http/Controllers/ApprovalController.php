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
            $q->where('approved_by_warehouse_id', $userWarehouseId)   // Master
            ->orWhere('requested_by_warehouse_id', $userWarehouseId); // District
        })
        ->where('status', 0)
        ->latest()
        ->paginate(10);

        return view('approval.warehousetransfer', compact('transfers'));
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


    public function reject(WarehouseTransfer $transfer)
    {
        if ($transfer->status != 0) {
            return back()->with('error', 'Only pending transfers can be rejected');
        }

        DB::transaction(function () use ($transfer) {
            $transfer->status = 2; // rejected
            // $transfer->rejected_at = now(); // optional
            $transfer->save();
        });

        return back()->with('success', 'Transfer rejected successfully');
    }


}
