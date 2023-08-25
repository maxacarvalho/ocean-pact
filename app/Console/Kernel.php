<?php

namespace App\Console;

use App\Console\Commands\ProcessBuyerInvitationsCommand;
use App\Console\Commands\ProcessSupplierInvitationsCommand;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule): void
    {
        $schedule->command('telescope:prune')->daily();

        $schedule->command(ProcessBuyerInvitationsCommand::class)
            ->everyMinute()
            ->withoutOverlapping()
            ->onOneServer();

        $schedule->command(ProcessSupplierInvitationsCommand::class)
            ->everyMinute()
            ->withoutOverlapping()
            ->onOneServer();
    }

    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
