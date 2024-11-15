<?php

namespace App\Jobs;

use App\Models\User;
use App\Models\Member;
use App\Service\Notification;
use Illuminate\Bus\Queueable;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class NotifyWinnersJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public $equbTypeId, $equbTypeName, $memberId, $shortcode, $message, $isWinner;

    public function __construct($equbTypeId, $equbTypeName, $memberId, $shortcode, $message = null, $isWinner = true)
    {
        $this->equbTypeId = $equbTypeId;
        $this->equbTypeName = $equbTypeName;
        $this->memberId = $memberId;
        $this->shortcode = $shortcode;
        $this->message = $message;
        $this->isWinner = $isWinner;

        $this->afroApiKey = config('key.AFRO_API_KEY');
        $this->afroSenderId = config('key.AFRO_IDENTIFIER_ID');
        $this->afroSenderName = config('key.AFRO_SENDER_NAME');
        $this->afroBaseUrl = config('key.AFRO_BASE_URL');

        $this->afroSpaceBefore = config('key.AFRO_SPACE_BEFORE_OTP');
        $this->afroSpaceAfter = config('key.AFRO_SPACE_AFTER_OTP');
        $this->afroExpiresIn = config('key.AFRO_OTP_EXPIRES_IN_SECONDS');
        $this->afroLength = config('key.AFRO_OPT_LENGTH');
        $this->afroType = config('key.AFRO_OTP_TYPE');
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $member = Member::find($this->memberId);
        if ($member) {
            $user = User::where('phone_number', $member->phone)->first();
            if ($user) {
                $title = $this->isWinner ? "Congratulations" : "Equb Round Results";
                $message = $this->isWinner
                    ? "You have been selected as the winner of the equb {$this->equbTypeName}. For further information, please call {$this->shortcode}."
                    : $this->message;

                Notification::sendNotification($user->fcm_id, $message, $title);
                $this->sendSms($user->phone_number, $message);
            }
        }
    }
    protected function sendSms($phoneNumber, $message)
    {
        $afroApiKey = config('key.AFRO_API_KEY');
        $afroSenderId = config('key.AFRO_IDENTIFIER_ID');
        $afroSenderName = config('key.AFRO_SENDER_NAME');
        $afroBaseUrl = config('key.AFRO_BASE_URL');
        // dd($afroApiKey,$afroSenderId,$afroSenderName,$afroBaseUrl);
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
        // dd($responseData);
        return $responseData;
    }
}
