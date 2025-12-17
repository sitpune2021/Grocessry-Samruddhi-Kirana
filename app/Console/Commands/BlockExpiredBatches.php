<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ProductBatch;
use Illuminate\Support\Facades\Log;

class BlockExpiredBatches extends Command
{
    protected $signature = 'batches:block-expired';
    protected $description = 'Automatically block expired batches';

    public function handle()
    {
        $today = now()->toDateString();

        $expiredBatches = ProductBatch::whereDate('expiry_date', '<', $today)
            ->where('is_blocked', false)
            ->get();

        foreach ($expiredBatches as $batch) {
            $batch->update(['is_blocked' => true]);

            Log::info('Batch auto-blocked due to expiry', [
                'batch_id'    => $batch->id,
                'product_id'  => $batch->product_id,
                'batch_no'    => $batch->batch_no,
                'expiry_date' => $batch->expiry_date,
                'timestamp'   => now(),
            ]);
        }

        $this->info('Expired batches blocked successfully.');
    }
    
}
