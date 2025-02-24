<?php

namespace App\Console\Commands;

use App\Models\Payment;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RemovePendingPayment extends Command
{
    protected $signature = 'remove:removeOlderPendingTelebirrPayments';

    protected $description = 'Remove pending Telebirr payments older than 3 hours';

    public function handle()
    {
        // Get expired payments
        $expiredPayments = Payment::where('status', 'pending')
        ->where('payment_type', 'telebirr')
        ->where('created_at', '<', now()->subHours(3))
        ->get();

        $count = 0;

        if ($expiredPayments->isNotEmpty()) {
            foreach ($expiredPayments as $payment) {
                $payment->delete();
                $count++;
            }

            $message = "Successfully soft deleted $count expired pending Telebirr payments.";
            $this->info($message);
            Log::info($message);
        } else {
            $message = 'No expired Telebirr payments found.';
            $this->info($message);
            Log::info($message);
        }
    }
}