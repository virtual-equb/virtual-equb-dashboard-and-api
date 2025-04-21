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
            $response = Http::timeout(60)->withHeaders([
                    "Content-Type" => "application/json",
                    "X-APP-Key" => $this->fabricAppId,
                ])->post($this->BASE_URL . '/payment/v1/token', [
                    'appSecret' => $this->appSecret,
                ]);

            if ($response->successful()) {
                return $response->body();
            }

            Log::error("Failed to retrieve Fabric token", ['status' => $response->status(), 
                'body' => $response->body()
            ]);

            Log::info('Apply Fabric Token Request', [
                'url' => $this->BASE_URL . '/payment/v1/token',
                'headers' => [
                    "Content-Type" => "application/json",
                    "X-APP-Key" => $this->fabricAppId,
                ],
                'body' => ['appSecret' => $this->appSecret],
            ]);
            
            Log::info('Fabric Token API Response', ['response' => $response->body()]);

            throw new \Exception('Error retrieving the Fabric token: ' . $response->status());
        } catch (Exception $e) {
            Log::error('Exception in applyFabricToken', ['error' => $e->getMessage()]);
            throw new \Exception('Error retrieving the Fabric token: ' . $e);
        }
    }
}
