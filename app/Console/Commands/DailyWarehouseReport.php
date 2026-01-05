<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\WarehouseStock;
use App\Services\WhatsAppStockService;

class DailyWarehouseReport extends Command
{
    protected $signature = 'report:daily-warehouse';
    protected $description = 'Send daily warehouse stock report at 9 PM';

    public function handle()
    {
        $stocks = WarehouseStock::with(['warehouse', 'product'])
            ->whereNull('deleted_at')
            ->get()
            ->groupBy('warehouse_id');

        foreach ($stocks as $warehouseStocks) {

            $warehouse = $warehouseStocks->first()->warehouse;

            if (!$warehouse || !$warehouse->contact_number) {
                continue;
            }

            $message = "📊 *DAILY STOCK REPORT*\n";
            $message .= "Warehouse: {$warehouse->name}\n";
            $message .= "Date: " . now()->format('d-m-Y') . "\n\n";

            foreach ($warehouseStocks as $stock) {
                $message .= "• {$stock->product->name} : {$stock->quantity}\n";
            }

            WhatsAppStockService::send(
                $warehouse->contact_number,
                $message
            );
        }

        $this->info('Daily warehouse report sent.');
    }
}
