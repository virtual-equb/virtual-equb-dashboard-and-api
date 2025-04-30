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

    public function __construct()
    {
        $this->applyFabricTokenServiceMiniApp = new ApplyFabricTokenServiceMiniApp(
            env('BASE_URL'),
            env('FABRIC_APP_ID'),
            env('APP_SECRET'),
            env('MERCHANT_APP_ID')
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
                'X-APP-Key'    =>  env('FABRIC_APP_ID'),
                'Authorization' => $fabricToken,
            ])->post(env('BASE_URL') . '/payment/v1/auth/authToken', $requestBody);
            
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
                    'appid' => env('MERCHANT_APP_ID'),
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