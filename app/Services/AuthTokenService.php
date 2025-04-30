<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use App\Services\ApplyFabricTokenServiceMiniApp;
use App\Helpers\SignHelper;
use Illuminate\Support\Facades\Log;
use Exception;

class AuthTokenService
{
    protected $applyFabricTokenServiceMiniApp;
    protected $baseUrl;
    protected $fabricAppId;
    protected $appSecret;
    protected $merchantAppId;

    public function __construct()
    {
        $this->applyFabricTokenServiceMiniApp = new ApplyFabricTokenServiceMiniApp(
            $this->baseUrl = env('TELEBIRR_BASE_URL'), 
            $this->fabricAppId = env('TELEBIRR_FABRIC_APP_ID'),
            $this->appSecret = env('TELEBIRR_APP_SECRET'),
            $this->merchantAppId = env('TELEBIRR_MERCHANT_APP_ID'),
        );
    }

    public function authToken($authToken)
    {
        $tokenResult = json_decode($this->applyFabricTokenServiceMiniApp->applyFabricToken());

        if (!$tokenResult || !isset($tokenResult->token)) {
            throw new Exception('Failed to retrive Fabric token - MiniApp Auto Login:' . json_encode($tokenResult));
        }

        $fabricToken = $tokenResult->token;

        return $this->requestAuthToken($fabricToken, $authToken);
    }

    protected function requestAuthToken($fabricToken, $appToken)
    {
        $requestBody = $this->createRequestObject($appToken);

        try {
            $response = Http::timeout(60)->withHeaders([
                'Content-Type' => 'application/json',
                'X-APP-Key'    =>  $this->fabricAppId,
                'Authorization' => $fabricToken,
            ])->post($this->baseUrl . '/payment/v1/auth/authToken', $requestBody);
            
            Log::info('Getting MiniApp Auth Login Result' . $response->body());

            return $response->body();
        } catch (Exception $e) {
            Log::info('Failed log from requestAuthToken');

            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    
    public function createRequestObject($appToken)
    {
        try {
            $req = [
                'nonce_str' => SignHelper::createNonceStr(),
                'method' => 'payment.authtoken',
                'timestamp' => SignHelper::createTimeStamp(),
                'version' => '1.0',
                'biz_content' => [
                    'access_token'  => $appToken,
                    'trade_type' => 'InApp',
                    'appid' => $this->merchantAppId,
                    'resource_type' => 'OpenId',
                ],
            ];

            // Sign the request
            $req['sign'] = SignHelper::sign($req); 
            $req['sign_type'] = 'SHA256WithRSA';

            return $req;
        } catch (Exception $e) {
            throw $e;
        }
    }
}