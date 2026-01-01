<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\WarehouseStock;
use App\Services\WhatsAppStockService;

class SendLowStockWhatsappAlerts extends Command
{
    protected $signature = 'lowstock:whatsapp';
    protected $description = 'Send WhatsApp alert for low stock products';

    public function handle()
    {
        $threshold = 100;

        $stocks = WarehouseStock::with(['warehouse', 'product'])
            ->where('quantity', '<=', $threshold)
            ->whereNull('deleted_at')
            ->get();

        foreach ($stocks as $stock) {

            if (!$stock->warehouse || !$stock->warehouse->contact_number) {
                continue;
            }

            $message =
                "⚠️ *LOW STOCK ALERT*\n\n" .
                "Warehouse: {$stock->warehouse->name}\n" .
                "Product: {$stock->product->name}\n" .
                "Available Qty: {$stock->quantity}\n\n" .
                "⏰ Please restock soon.";

            WhatsAppStockService::send(
                $stock->warehouse->contact_number,
                $message
            );
        }

        $this->info('Low stock WhatsApp alerts processed.');
    }
}
