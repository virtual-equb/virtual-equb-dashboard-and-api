<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class ApplyFabricTokenService
{
    protected $BASE_URL;
    protected $fabricAppId;
    protected $appSecret;
    protected $merchantAppId;

    public function __construct($BASE_URL, $fabricAppId, $appSecret, $merchantAppId)
    {
        $this->BASE_URL = $BASE_URL;
        $this->fabricAppId = $fabricAppId;
        $this->appSecret = $appSecret;
        $this->merchantAppId = $merchantAppId;
    }

    public function applyFabricToken()
    {
        try {
            $response = Http::withOptions(['verify' => false])->timeout(60)->withHeaders([
                    "Content-Type" => "application/json",
                    "X-APP-Key" => "c4182ef8-9249-458a-985e-06d191f4d505",
                ])->post('https://196.188.120.3:38443/apiaccess/payment/gateway' . '/payment/v1/token', [
                    'appSecret' => "fad0f06383c6297f545876694b974599",
                ]);

            if ($response->successful()) {
                return $response->body();
            }

            Log::error("Failed to retrieve Fabric token", ['status' => $response->status(), 
                'body' => $response->body()
            ]);

            Log::info('Apply Fabric Token Request', [
                'url' => 'https://196.188.120.3:38443/apiaccess/payment/gateway' . '/payment/v1/token',
                'headers' => [
                    "Content-Type" => "application/json",
                    "X-APP-Key" => 'c4182ef8-9249-458a-985e-06d191f4d505',
                ],
                'body' => ['appSecret' => 'fad0f06383c6297f545876694b974599'],
            ]);
            
            Log::info('Fabric Token API Response', ['response' => $response->body()]);

            throw new \Exception('Error retrieving the Fabric token: ' . $response->status());
        } catch (Exception $e) {
            Log::error('Exception in applyFabricToken', ['error' => $e->getMessage()]);
            throw new \Exception('Error retrieving the Fabric token: ' . $e);
        }
    }
}
