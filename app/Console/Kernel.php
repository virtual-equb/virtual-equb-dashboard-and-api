<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule)
    {
        // Updated Schedules
        $schedule->command('check:enddate')->everyEightHours();
        $schedule->command('send:reminders')->everyNineHours()->withoutOverlapping();
        $schedule->command('equb:draw')->everyFifteenHours();
        $schedule->command('check:lotterydate')->everyNineHours()->withoutOverlapping();
        $schedule->command('check:unpaidPayments')->everyTenHours();
        $schedule->command('remove:removeOlderPendingTelebirrPayments')->everyThreeHours();
    }

    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
