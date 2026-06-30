<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected $commands = [
        \App\Console\Commands\GenerateCertificatesCommand::class,
        \App\Console\Commands\ZatcaValidateCommand::class,
    ];

    protected function schedule(Schedule $schedule): void
    {
        // Schedule commands for automatic ZATCA operations
        // $schedule->command('zatca:validate')->daily();
    }

    protected function commands(): void
    {
        $this->load(app_path('Console/Commands'));

        require base_path('routes/console.php');
    }
}
