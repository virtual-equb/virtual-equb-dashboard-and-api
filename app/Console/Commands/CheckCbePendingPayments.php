<?php

namespace App\Console\Commands;

use App\Models\Payment;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CheckCbePendingPayments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'payments:check-pending';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check pending CBE Gateway payments older than 60 minutes and notify admins';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // Get today's date 
        $today = Carbon::today();
        // get payments where status is 'pending' and created more than 60 minutes ago
        $expiredPayments = Payment::where('payment_type', 'CBE Gateway')
            ->where('status', 'pending')
            ->whereDate('created_at', $today)
            ->where('created_at', '<', Carbon::now()->subMinutes(60))
            ->get();

        if ($expiredPayments->isEmpty()) {
            $this->info('No Expired payments found');
            return;
        }

        $admins = User::role('admin')->get();
        foreach($admins as $admin) {
            if ($admin->phone_number) {
                $message = "There are " . $expiredPayments->count() . " pending payments in the CBE Gateway for more than 60 minutes. Please check.";
                $this->sendSms($admin->phone_number, $message);
            }
        }

        $this->info('Notification sent to admins.');
    }
}
