<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('inspire')->hourly();
        // $schedule->command('check:enddate')->dailyAt('9:00');
        // $schedule->command('check:lotterydate')->dailyAt('9:00');
        // $schedule->command('equb:draw')->dailyAt('9:00');
        // $schedule->command('check:unpaidPayments')->dailyAt('9:00');
        // $schedule->command('send:reminder')->dailyAt('9:00');
        // $schedule->command('check:equbtype')->dailyAt('9:00');

        // Updated Schedules
        $schedule->command('equb:notify-due-starts')->dailyAt('00:00');
        $schedule->command('equb:notify-due-ends')->dailyAt('00:00');
        $schedule->command('equb:sendnotifications')->dailyAt('00:00');
        $schedule->command('equb:autoDrawLottery')->dailyAt('00:00'); // Run every day at midnight
        $schedule->command('equb:draw-winners')->dailyAt('00:00');
        $schedule->command('remove:removeOlderPendingTelebirrPayments')->everyThreeHours($minutes = 0);
        $schedule->command('payments:check-pending')->everyTenMinutes();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
