<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Http;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    // public function sendSms($phone, $message)
    // {
    //     $hahuApiKey = config('key.HAHU_CLOUD_API');
    //     $hahuDeviceId = config('key.HAHU_DEVICE_ID');
    //     try {
    //         Http::post("https://hahu.io/api/send/sms?secret=$hahuApiKey&mode=devices&phone=$phone&message=$message&device=$hahuDeviceId&sim=2");
    //     } catch (Exception $ex) {
    //         return redirect()->back()->with('error', 'Unknown error occured');
    //     }
    // }
    public function sendSms($phoneNumber, $message)
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
