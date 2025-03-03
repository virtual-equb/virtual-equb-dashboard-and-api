<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule)
    {
        // Updated Schedules
        $schedule->command('check:enddate')->cron('0 */8 * * *');
        $schedule->command('send:reminders')->cron('0 */9 * * *')->withoutOverlapping();
        $schedule->command('equb:draw')->cron('0 */15 * * *');
        $schedule->command('check:lotterydate')->cron('0 */9 * * *')->withoutOverlapping();
        $schedule->command('check:unpaidPayments')->cron('0 */10 * * *');
        $schedule->command('remove:removeOlderPendingTelebirrPayments')->cron('0 */3 * * *');
    }

    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
