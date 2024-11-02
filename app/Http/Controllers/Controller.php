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
    private $afroApiKey;
    private $afroSenderId;
    private $afroSenderName;
    private $afroBaseUrl;
    private $afroSpaceBefore;
    private $afroSpaceAfter;
    private $afroExpiresIn;
    private $afroLength;
    private $afroType;

    public function __construct()
    {
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

    public function sendOtp($phone)
    {
        $prefixMessage = "Your Verification Code is";
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->afroApiKey,
        ])
        ->baseUrl($this->afroBaseUrl)
        ->withOptions(['verify' => false])
        // ->withOptions(['verify' => base_path('C:/wamp64/cacert.pem')])  
        ->get('/challenge', [
            'from' => $this->afroSenderId,
            'sender' => $this->afroSenderName,
            'to' => $phone,
            'pr' => $prefixMessage,
            'sb' => $this->afroSpaceBefore,
            'sa' => $this->afroSpaceAfter,
            'ttl' => $this->afroExpiresIn,
            'len' => $this->afroLength,
            't' => $this->afroType
        ]);

        $responseData = $response->json();
        if ($responseData['acknowledge'] == 'success') {
            return ['acknowledge' => $responseData['acknowledge']];
        }
        return [
            'acknowledge' => $responseData['acknowledge'],
            'message' => $responseData['response']['errors']
        ];
    }

    public function verifyOtp($code, $phone)
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->afroApiKey,
        ])
            ->baseUrl($this->afroBaseUrl)
            ->withOptions(['verify' => false])
            ->get('/verify?&to=' . $phone . '&code=' . $code);
        $responseData = $response->json();
        if ($responseData['acknowledge'] == 'success') {
            return ['acknowledge' => $responseData['acknowledge']];
        }
        return [
            'acknowledge' => $responseData['acknowledge'],
            'message' => $responseData['response']['errors']
        ];
    }
}
