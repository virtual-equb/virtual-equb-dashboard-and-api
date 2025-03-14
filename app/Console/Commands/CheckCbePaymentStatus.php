<?php

namespace App\Console\Commands;

use App\Models\Payment;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;

class CheckCbePaymentStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'payments:check-cbe-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check if CBE returns a 500 error for payment URLs and notify admins';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // get payments that were created in the last 24 hours and are still pending
        $payments = Payment::where('payment_type', 'CBE Gateway')
                            ->where('status', 'pending')
                            ->where('created_at', '>=', Carbon::now()->subDay())
                            ->get();
        // Loop through payments and check if there's a 500 error from CBE
        foreach ($payments as $payment) {
            // Simulate sending the request to CBE (you will replace this with the actual request)
            $response = $this->checkPaymentUrl($payment);

            // If the response status is 500, notify the admins
            if ($response === 500) {
                $this->notifyAdmins($payment);
            }
        }

        $this->info('CBE Payment Status Check Completed.');
    }
    // Function to simulate the request to CBE for the payment status check
    private function checkPaymentUrl(Payment $payment)
    {
        try {
            $url = '';
            
        }catch (Exception $ex) {
            return 500;
        }
    }
}
