<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ProductBatch;
use App\Models\Warehouse;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use App\Services\WhatsAppService;

class SendExpiryWhatsappAlerts extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'expiry:whatsapp';

    /**
     * The console command description.
     */
    protected $description = 'Send WhatsApp alerts for expiring product batches';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $today = Carbon::today();
        $alertDate = $today->copy()->addDays(7);

        $batches = ProductBatch::with(['product', 'warehouse'])
            ->whereDate('expiry_date', '<=', $alertDate)
            ->whereDate('expiry_date', '>=', $today)
            ->where('quantity', '>', 0)
            ->get();

        foreach ($batches as $batch) {

            if (!$batch->warehouse || !$batch->warehouse->contact_number) {
                continue;
            }

            $message = "⚠️ *Expiry Alert*\n\n"
                . "Warehouse: {$batch->warehouse->name}\n"
                . "Product: {$batch->product->name}\n"
                . "Batch No: {$batch->batch_no}\n"
                . "MFG Date: " . optional($batch->mfg_date)->format('d-m-Y') . "\n"
                . "Expiry Date: " . Carbon::parse($batch->expiry_date)->format('d-m-Y') . "\n"
                . "Quantity: {$batch->quantity}\n\n"
                . "⏰ Expiring within 7 days.";

            // FOR LOCALHOST TEST (LOG MODE)
            WhatsAppService::send(
                $batch->warehouse->contact_number,
                $message
            );
        }

        $this->info('Expiry WhatsApp alerts processed successfully.');
    }
}
