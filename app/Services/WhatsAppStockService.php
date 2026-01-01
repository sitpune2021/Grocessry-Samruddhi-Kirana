<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppStockService
{
    public static function send($number, $message)
    {
        // ✅ LOCALHOST: only log
        if (app()->environment('local')) 
        {
            Log::info('WHATSAPP | LOW STOCK ALERT', [
                'to' => $number,
                'message' => $message
            ]);
            return true;
        }

        // ✅ SERVER: real WhatsApp API call
        try {
            Http::post(config('services.whatsapp.url'), [
                'to'      => $number,
                'message' => $message
            ]);
            return true;

        } catch (\Exception $e) {
            Log::error('WhatsApp Send Failed', [
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
}
