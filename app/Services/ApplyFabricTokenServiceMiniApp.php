<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class ApplyFabricTokenServiceMiniApp
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
                    "X-APP-Key" => 'c4182ef8-9249-458a-985e-06d191f4d505',
                ])->post('https://196.188.120.3:38443/apiaccess/payment/gateway' . '/payment/v1/token', [
                    'appSecret' => 'fad0f06383c6297f545876694b974599',
                ]);

            if ($response->successful()) {
                return $response->body();
            }

            Log::info('Apply Fabric Token Request - MiniApp', [
                'url' => 'https://196.188.120.3:38443/apiaccess/payment/gateway' . '/payment/v1/token',
                'headers' => [
                    "Content-Type" => "application/json",
                    "X-APP-Key" => 'c4182ef8-9249-458a-985e-06d191f4d505',
                ],
                'body' => ['appSecret' => 'fad0f06383c6297f545876694b974599'],
            ]);
            
            throw new \Exception('Error retrieving the Fabric token MiniApp: ' . $response->status());
        } catch (Exception $e) {
            Log::error('Exception in applyFabricToken - MiniApp', ['error' => $e->getMessage()]);
            throw new \Exception('Error retrieving the Fabric token - MiniApp: ' . $e);
        }
    }
}