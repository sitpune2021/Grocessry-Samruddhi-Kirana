<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;


class WhatsAppService
{
    // Serverside cron job code
    // public static function send($number, $message)
    // {
    //     try {
    //         Http::post(config('services.whatsapp.url'), [
    //             'to'      => $number,
    //             'message' => $message
    //         ]);
    //     } catch (\Exception $e) {
    //         Log::error('WhatsApp Error', ['error' => $e->getMessage()]);
    //     }
    // }

    // localhost wp alert code only store nhi log file.

    public static function send($number, $message)
    {
        /**
         * LOCALHOST / DEVELOPMENT
         * ---------------------------------
         * Sirf log karega
         */
        if (app()->environment('local')) 
        {
            Log::info('WHATSAPP TEST MESSAGE', [
                'to' => $number,
                'message' => $message
            ]);
            return;
        }

        /**
         * PRODUCTION / SERVER
         * ---------------------------------
         * Actual WhatsApp API call
         */
        try {
            Http::withToken(config('services.whatsapp.token'))
                ->post(config('services.whatsapp.url'), [
                    "messaging_product" => "whatsapp",
                    "to" => "91" . $number,
                    "type" => "text",
                    "text" => [
                        "body" => $message
                    ]
                ]);

        } catch (\Exception $e) {
            Log::error('WhatsApp Send Failed', [
                'error' => $e->getMessage()
            ]);
        }
    }


}
