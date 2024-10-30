<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\CBEBirrService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class CBEBirrController extends Controller
{
    protected $key;

    public function __construct()
    {
        $this->key = config('app.cbebirr_api_key');
    }

    private function preparePayload($data)
    {
        $sortedData = collect($data)->sortKeys();
        $payloadString = $sortedData->map(function($value, $key) {
            return "{$key}={$value}";
        })->join('&');
        return $payloadString;
    }

    private function hashPayload($payloadString)
    {
        return hash('sha256', $payloadString); // Adjust based on the hashing algorithm provided
    }

    private function encrypt($data)
    {
        return openssl_encrypt($data, 'aes-256-cbc', $this->key, 0, $this->getIv());
    }

    private function decrypt($encryptedData)
    {
        return openssl_decrypt($encryptedData, 'aes-256-cbc', $this->key, 0, $this->getIv());
    }

    private function getIv()
    {
        return substr(hash('sha256', $this->key), 0, 16); // Adjust IV length as per encryption method
    }


    public function initiateTransaction(Request $request) 
    {
        $payload = [
            "U" => config('app.cbebirr_user_id'),
            "W" => config('app.cbebirr_password'),
            "T" => $request->transactionId,
            "A" => $request->amount,
            "MC" => config('app.cbebirr_merchant_code'),
            "key" => config('app.cbebirr_api_key')
        ];

        // Prepare hashed payload
        $sortedPayloadString = $this->preparePayload($payload);
        $hashedValue = $this->hashPayload($sortedPayloadString);

        // Encrypt individual data items
        $encryptedData = [
            "U" => $this->encrypt($payload['U']),
            "W" => $this->encrypt($payload['W']),
            "T" => $this->encrypt($payload['T']),
            "A" => $this->encrypt($payload['A']),
            "MC" => $this->encrypt($payload['MC']),
            "HV" => $this->encrypt($hashedValue)
        ];

        // Encrypt the entire payload JSON
        $finalEncryptedPayload = $this->encrypt(json_encode($encryptedData));

        // Send a query parameter
        // $response = Http::get(config('app.cbebirr_api_url'), [
        //     'r' => $finalEncryptedPayload
        // ]);
        $response = Http::withoutVerifying()->get(config('app.cbebirr_api_url'), [
            'r' => $finalEncryptedPayload
        ]);

        return $response->body();
    }

    public function transactionStatus($transactionId)
    {
        $payload = [
            "transactionId" => $transactionId,
            "Tillcode" => config('app.cbebirr_merchant_code'),
            "Key" => config('app.cbebirr_api_key')
        ];

        $sortedPayloadString = $this->preparePayload($payload);
        $hashedValue = $this->hashPayload($sortedPayloadString);

        // Send request with hashed signature
        // $response = Http::post(config('app.cbebirr_status_api_url'), [
        //     "transactionId" => $transactionId,
        //     "Tillcode" => config('app.cbebirr_merchant_code'),
        //     "Signiture" => $hashedValue
        // ]);
        $response = Http::withoutVerifying()->post(config('app.cbebirr_status_api_url'), [
            "transactionId" => $transactionId,
            "Tillcode" => config('app.cbebirr_merchant_code'),
            "Signiture" => $hashedValue
        ]);

        $decryptedResponse = $this->decrypt($response->body());

        return json_decode($decryptedResponse);
    }
}
