<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected $commands = [
        // Commands list here, e.g.
        // \App\Console\Commands\BlockExpiredBatches::class,
    ];

    protected function schedule(Schedule $schedule)
    {
        $schedule->command('batches:block-expired')->daily();
    }


    protected function commands()
    {
        $this->load(__DIR__.'/Commands');
        require base_path('routes/console.php');
    }
}
