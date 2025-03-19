<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule)
    {
        

        $schedule->command('equb:notify-due-starts')->dailyAt('00:00');
        $schedule->command('equb:notify-due-ends')->dailyAt('00:00');
        $schedule->command('equb:sendnotifications')->dailyAt('00:00');
        $schedule->command('equb:autoDrawLottery')->dailyAt('00:00'); // Run every day at midnight
        $schedule->command('equb:draw-winners')->dailyAt('00:00');
        $schedule->command('check:enddate')->cron('0 */8 * * *');
        $schedule->command('equb:draw')->cron('0 */15 * * *');
        $schedule->command('check:lotterydate')->cron('0 */9 * * *')->withoutOverlapping();
        $schedule->command('check:unpaidPayments')->cron('0 */10 * * *');
        
        // $schedule->command('payments:check-pending')->everyTenMinutes();
        // $schedule->command('remove:removeOlderPendingTelebirrPayments')->cron('0 */3 * * *');
        // $schedule->command('send:reminders')->cron('0 */9 * * *')->withoutOverlapping();

    }

    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
