<?php

namespace App\Console\Commands;

use App\Models\Payment;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RemovePendingTelebirrMiniAppPayment extends Command
{
    protected $signature = 'remove:removeOlderPendingTelebirrMiniAppPayments';

    protected $description = 'Remove pending Telebirr MiniApp payments older than 5 Minutes';

    public function handle()
    {
        // Get expired payments
        $expiredPayments = Payment::where('status', 'pending')
        ->where('payment_type', 'Telebirr MiniApp')
        ->where('created_at', '<', now()->subMinutes(5))
        ->get();

        $count = 0;

        if ($expiredPayments->isNotEmpty()) {
            foreach ($expiredPayments as $payment) {
                $payment->delete();
                $count++;
            }

            $message = "Successfully soft deleted $count expired pending Telebirr MiniApp payments.";
            $this->info($message);
            Log::info($message);
        } else {
            $message = 'No expired Telebirr MiniApp payments found.';
            $this->info($message);
            Log::info($message);
        }
    }
}