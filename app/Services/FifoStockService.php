<?php

namespace App\Services;

use App\Models\ProductBatch;
use App\Models\StockMovement;

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
            ->orderByRaw('expiry_date IS NULL, expiry_date ASC') // real FIFO
            ->lockForUpdate()
            ->get();

        $consumed = [];

        foreach ($batches as $batch) {

            if ($remaining <= 0) {
                break;
            }

            $take = min($batch->quantity, $remaining);

            $batch->decrement('quantity', $take);

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
