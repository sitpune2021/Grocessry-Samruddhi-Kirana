<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Warehouse;
use App\Models\WarehouseStock;
use App\Models\TalukaTransfer;
use App\Models\ProductBatch;
use App\Models\StockMovement;
use App\Models\Category;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Product;


class TalukaToTalukaApprovalController extends Controller
{

    public function index()
    {
        $transfers = TalukaTransfer::with([
            'fromWarehouse',
            'toWarehouse',
            'product'
        ])
        ->latest()
        ->paginate(10);

        return view('approval.TalukatotalukaTransfer', compact('transfers'));
    }

    public function approve(TalukaTransfer $transfer)
    {
        if ($transfer->status == 1) {
            return back()->with('error', 'Already approved');
        }

        DB::transaction(function () use ($transfer) {

            /** ------------------------------
             * SOURCE WAREHOUSE STOCK
             * ------------------------------*/
            $sourceStock = WarehouseStock::where('warehouse_id', $transfer->from_warehouse_id)
                ->where('product_id', $transfer->product_id)
                ->lockForUpdate()
                ->first();

            if (!$sourceStock || $sourceStock->quantity < $transfer->quantity) {
                throw new \Exception('Insufficient stock in source warehouse');
            }

            $sourceStock->decrement('quantity', $transfer->quantity);


            /** ------------------------------
             * SOURCE PRODUCT BATCH
             * ------------------------------*/
            $sourceBatch = ProductBatch::where('id', $transfer->batch_id)
                ->lockForUpdate()
                ->first();

            if (!$sourceBatch || $sourceBatch->quantity < $transfer->quantity) {
                throw new \Exception('Insufficient batch stock');
            }

            $sourceBatch->decrement('quantity', $transfer->quantity);


            /** ------------------------------
             * DESTINATION WAREHOUSE STOCK
             * ------------------------------*/
            $product = Product::findOrFail($transfer->product_id);

            $destStock = WarehouseStock::firstOrNew([
                'warehouse_id' => $transfer->to_warehouse_id,
                'product_id'   => $transfer->product_id,
            ]);

            $destStock->category_id = $product->category_id;
            $destStock->quantity = ($destStock->quantity ?? 0) + $transfer->quantity;
            $destStock->save();


            /** ------------------------------
             * DESTINATION PRODUCT BATCH
             * ------------------------------*/
            $destBatch = ProductBatch::firstOrNew([
                'warehouse_id' => $transfer->to_warehouse_id,
                'product_id'   => $transfer->product_id,
                'batch_no'     => $sourceBatch->batch_no,
            ]);

            $destBatch->category_id  = $product->category_id;
            $destBatch->mfg_date     = $sourceBatch->mfg_date;
            $destBatch->expiry_date = $sourceBatch->expiry_date;
            $destBatch->quantity    = ($destBatch->quantity ?? 0) + $transfer->quantity;
            $destBatch->save();


            /** ------------------------------
             * STOCK MOVEMENTS (MAIN PART)
             * ------------------------------*/

            // SOURCE (OUT)
            StockMovement::create([
                'product_batch_id' => $sourceBatch->id,
                'type'             => 'transfer',
                'quantity'         => -$transfer->quantity,
                'warehouse_id'     => $transfer->from_warehouse_id,
            ]);

            // DESTINATION (IN)
            StockMovement::create([
                'product_batch_id' => $destBatch->id,
                'type'             => 'transfer',
                'quantity'         => $transfer->quantity,
                'warehouse_id'     => $transfer->to_warehouse_id,
            ]);


            /** ------------------------------
             * MARK APPROVED
             * ------------------------------*/
            $transfer->status = 1;
            // $transfer->approved_at = now();
            $transfer->save();
            
        });

        return back()->with('success', 'Transfer approved successfully');
    }

    public function reject(TalukaTransfer $transfer)
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
