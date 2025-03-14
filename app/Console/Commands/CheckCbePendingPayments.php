<?php

namespace App\Console\Commands;

use App\Models\Payment;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

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

        // $admins = User::role('admin')->get();
        // foreach($admins as $admin) {
        //     if ($admin->phone_number) {
        //         $message = "There are " . $expiredPayments->count() . " pending payments in the CBE Gateway for more than 60 minutes. Please check.";
        //         $this->sendSms($admin->phone_number, $message);
        //     }
        // }

        // Delete expired payments
        $deletedCount = $expiredPayments->count();
        Payment::wherein('id', $expiredPayments->pluck('id'))->delete();

        $this->info("{$deletedCount} expired payments deleted.");
    }

    public function sendSms($phoneNumber, $message) 
    {
        $afroApiKey = config('key.AFRO_API_KEY');
        $afroSenderId = config('key.AFRO_IDENTIFIER_ID');
        $afroSenderName = config('key.AFRO_SENDER_NAME');
        $afroBaseUrl = config('key.AFRO_BASE_URL');

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $afroApiKey,
        ])
            ->baseUrl($afroBaseUrl)
            ->withOptions(['verify' => false])
            ->post('/send', [
                'from' => $afroSenderId,
                'sender' => $afroSenderName,
                'to' => $phoneNumber,
                'message' => $message
            ]);
        $responseData = $response->json();

        return $responseData;
    }
}
