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
    }

    protected function commands()
    {
        $this->load(__DIR__.'/Commands');
        require base_path('routes/console.php');
    }
}
