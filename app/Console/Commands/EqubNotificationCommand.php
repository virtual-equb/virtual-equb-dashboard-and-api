<?php

namespace App\Console\Commands;

use App\Http\Controllers\api\EqubController;
use Illuminate\Console\Command;

class EqubNotificationCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'equb:send-notifications';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send daily payment, lottery notifications and missed payments for Equbs.';

    /**
     * Execute the console command.
     *
     * @return int
     */

    public function __construct()
    {
        parent::__construct();
    }
    public function handle()
    {
        $equbContorller = app(EqubController::class);

        // Send daily payment notifications
        $dailyPaymentResponse = $equbContorller->sendDailyPaymentNotification();
        // $dailyCount = $dailyPaymentResponse->getData()->count();
        $this->info("Daily payment notifications sent: ");

        // Send lottery draw notifications
        $lotteryResponse = $equbContorller->sendLotteryNotification();
        // $lotteryCount = $lotteryResponse->getData()->count();
        $this->info("Lottery notification sent: ");

        // Send missed payment notification
        $missedResponse = $equbContorller->sendMissedPaymentNotification();
        // $missedCount = $missedResponse->getData()->count();
        $this->info("Missed notification sent: ");

        $this->info("Equb notifications processed successfully.");
    }
}
