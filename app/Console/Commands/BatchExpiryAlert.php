<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ProductBatch;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Mail\BatchExpiryAlertMail;
use App\Models\Warehouse;

class BatchExpiryAlert extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'batch:expiry-alert';

    /**
     * The console command description.
     */
    protected $description = 'Send expiry alerts for product batches';

    // public function handle()
    // {
    //     $today = Carbon::today();
    //     $alertDays = 30;

    //     $batches = ProductBatch::with('product')
    //         ->where('quantity', '>', 0)
    //         ->whereNotNull('expiry_date')
    //         ->whereDate('expiry_date', '<=', $today->copy()->addDays($alertDays))
    //         ->orderBy('expiry_date')
    //         ->get();

    //     if ($batches->isEmpty()) {
    //         Log::info('Expiry Alert: No expiring batches found');
    //         return Command::SUCCESS;
    //     }

    //     foreach ($batches as $batch) {

    //         $daysLeft = $today->diffInDays(Carbon::parse($batch->expiry_date), false);

    //         Log::warning('⚠️ Batch Expiry Alert', [
    //             'product'     => $batch->product->name ?? 'N/A',
    //             'batch_no'    => $batch->batch_no,
    //             'expiry_date' => $batch->expiry_date,
    //             'days_left'   => $daysLeft,
    //             'quantity'    => $batch->quantity,
    //         ]);
    //     }

    //     return Command::SUCCESS;
    // }

    
    public function handle()
    {
        $today = Carbon::today();
        $alertDays = 30;

        $batches = ProductBatch::with('product')
            ->where('quantity', '>', 0)
            ->whereNotNull('expiry_date')
            ->whereDate('expiry_date', '<=', $today->copy()->addDays($alertDays))
            ->orderBy('expiry_date')
            ->get();

        if ($batches->isEmpty()) {
            Log::info('Expiry Alert: No expiring batches found at '.$today);
            return Command::SUCCESS;
        }

        $formattedBatches = [];

        // foreach ($batches as $batch) {

        //     $daysLeft = $today->diffInDays(Carbon::parse($batch->expiry_date), false);

        //     // 🔹 LOG ENTRY (9 PM me ye log aayega)
        //     Log::warning('⚠️ Batch Expiry Alert', [
        //         'product'     => $batch->product->name ?? 'N/A',
        //         'batch_no'    => $batch->batch_no,
        //         'expiry_date' => $batch->expiry_date,
        //         'days_left'   => $daysLeft,
        //         'quantity'    => $batch->quantity,
        //     ]);

        //     $formattedBatches[] = [
        //         'product'     => $batch->product->name ?? 'N/A',
        //         'batch_no'    => $batch->batch_no,
        //         'quantity'    => $batch->quantity,
        //         'expiry_date' => Carbon::parse($batch->expiry_date)->format('d/m/Y'),
        //         'days_left'   => $daysLeft,
        //     ];
        // }

        $warehouseWiseBatches = [];

foreach ($batches as $batch) {

    $daysLeft = $today->diffInDays(Carbon::parse($batch->expiry_date), false);

    // ✅ LOG (same as before)
    Log::warning('⚠️ Batch Expiry Alert', [
        'product'     => $batch->product->name ?? 'N/A',
        'batch_no'    => $batch->batch_no,
        'expiry_date' => $batch->expiry_date,
        'days_left'   => $daysLeft,
        'quantity'    => $batch->quantity,
        'warehouse_id'=> $batch->warehouse_id,
    ]);

    $warehouseWiseBatches[$batch->warehouse_id][] = [
        'product'     => $batch->product->name ?? 'N/A',
        'batch_no'    => $batch->batch_no,
        'quantity'    => $batch->quantity,
        'expiry_date' => Carbon::parse($batch->expiry_date)->format('d/m/Y'),
        'days_left'   => $daysLeft,
    ];
}


        // ✅ EMAIL SEND (YAHI ADD KARNA THA)
        // Mail::to(config('mail.admin_email'))
        //     ->send(new BatchExpiryAlertMail($formattedBatches));


foreach ($warehouseWiseBatches as $warehouseId => $batchesData) {

    $warehouse = Warehouse::find($warehouseId);

    if (!$warehouse || empty($warehouse->email)) {
        Log::warning('❌ Warehouse email not found', [
            'warehouse_id' => $warehouseId
        ]);
        continue;
    }

    Mail::to($warehouse->email)
        ->send(new BatchExpiryAlertMail($batchesData));

    Log::info('✅ Expiry alert email sent', [
        'warehouse' => $warehouse->name ?? 'N/A',
        'email'     => $warehouse->email,
        'count'     => count($batchesData),
        'time'      => now()
    ]);
}


        Log::info('✅ Expiry alert email sent to admin at '.now());

        return Command::SUCCESS;
    }


}
