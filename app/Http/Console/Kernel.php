<?php

namespace App\Console;

use App\Console\Commands\EnrichHotelsCommand;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;


class Kernel extends ConsoleKernel
{
    protected $commands = [
        EnrichHotelsCommand::class,
    ];

    protected function schedule(Schedule $schedule)
    {
        // Enrichir les hôtels une fois par semaine
        $schedule->command('hotels:enrich')->weekly();
    }
}
