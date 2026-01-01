<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected $commands = [
        // \App\Console\Commands\BatchExpiryAlert::class, // optional
    ];

    protected function schedule(Schedule $schedule)
    {
        // Daily 9 PM expiry alert
        $schedule->command('batch:expiry-alert')
            ->dailyAt('21:00')
            ->withoutOverlapping()
            ->runInBackground();

        // (Optional) auto block expired batches
        // $schedule->command('batches:block-expired')->daily();

        // Daily 9 AM WhatsApp expiry message
        $schedule->command('expiry:whatsapp')
            ->dailyAt('09:00');

        // DAILY LOW STOCK WHATSAPP ALERT
        $schedule->command('lowstock:whatsapp')
            ->dailyAt('09:00')
            ->withoutOverlapping();

        // Daily 9 PM Warehouse Report
        $schedule->command('report:daily-warehouse')
            ->dailyAt('21:00')
            ->withoutOverlapping()
            ->runInBackground();
        
    }

    protected function commands()
    {
        $this->load(__DIR__.'/Commands');
        require base_path('routes/console.php');
    }
}
