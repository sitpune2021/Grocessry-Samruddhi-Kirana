<?php

namespace App\Services;

use App\Models\ProductBatch;
use App\Models\StockMovement;
use App\Models\WarehouseStock;
use Illuminate\Support\Facades\DB;

class FifoStockService
{
    public function consume($productId, $warehouseId, $qty, $orderId, $userId)
    {
        $remaining = $qty;

        $batches = ProductBatch::where('product_id', $productId)
            ->where('warehouse_id', $warehouseId)
            ->where('quantity', '>', 0)
            ->where(function ($q) {
                $q->whereNull('expiry_date')
                  ->orWhere('expiry_date', '>=', now());
            })
            ->orderByRaw('expiry_date IS NULL, expiry_date ASC') // FIFO
            ->lockForUpdate()
            ->get();

        $consumed = [];

        foreach ($batches as $batch) {

            if ($remaining <= 0) break;

            $take = min($batch->quantity, $remaining);

            /* ---------- PRODUCT BATCH ---------- */
            $batch->decrement('quantity', $take);

            /* ---------- WAREHOUSE STOCK (SUMMARY) ---------- */
            $ws = WarehouseStock::where([
                'warehouse_id' => $warehouseId,
                'product_id'   => $productId,
                // 'batch_id'     => $batch->id,
            ])->lockForUpdate()->first();

            if ($ws) {
                $ws->decrement('quantity', $take);
            } else {
                WarehouseStock::create([
                    'warehouse_id' => $warehouseId,
                    'product_id'   => $productId,
                    'batch_id'     => $batch->id,
                    'quantity'     => 0 - $take, // will not go negative in practice
                ]);
            }

            /* ---------- STOCK MOVEMENT ---------- */
            StockMovement::create([
                'product_batch_id' => $batch->id,
                'warehouse_id'     => $warehouseId,
                'quantity'         => -$take,
                'reference_type'   => 'order',
                'reference_id'     => $orderId,
                'created_by'       => $userId,
            ]);

            $consumed[] = [
                'batch_id' => $batch->id,
                'qty'      => $take,
            ];

            $remaining -= $take;
        }

        if ($remaining > 0) {
            throw new \Exception('Insufficient stock');
        }

        return $consumed;
    }
}
