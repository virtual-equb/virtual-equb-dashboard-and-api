<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use App\Services\ApplyFabricTokenServiceMiniApp;
use App\Helpers\SignHelperMiniApp;
use Illuminate\Support\Facades\Log;
use Exception;

class AuthTokenService
{
    protected $applyFabricTokenServiceMiniApp;

    public function __construct(ApplyFabricTokenServiceMiniApp $applyFabricTokenServiceMiniApp)
    {
        $this->applyFabricTokenServiceMiniApp = $applyFabricTokenServiceMiniApp;
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
            $response = Http::withOptions(['verify' => false])->withHeaders([
                'Content-Type' => 'application/json',
                'X-APP-Key'    => 'c4182ef8-9249-458a-985e-06d191f4d505',
                'Authorization' => $fabricToken,
            ])->post('https://196.188.120.3:38443/apiaccess/payment/gateway' . '/payment/v1/auth/authToken', $requestBody);
            
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
                'nonce_str' => SignHelperMiniApp::createNonceStr(),
                'method' => 'payment.authtoken',
                'timestamp' => SignHelperMiniApp::createTimeStamp(),
                'version' => '1.0',
                'biz_content' => [
                    'access_token'  => $appToken,
                    'trade_type' => 'InApp',
                    'appid' => '1350921361971201',
                    'resource_type' => 'OpenId',
                ],
            ];

            // Sign the request
            $req['sign'] = SignHelperMiniApp::sign($req); 
            $req['sign_type'] = 'SHA256WithRSA';

            return $req;
        } catch (Exception $e) {
            throw $e;
        }
    }
}