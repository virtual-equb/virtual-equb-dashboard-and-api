<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use App\Services\ApplyFabricTokenServiceMiniApp;
use App\Helpers\SignHelper;
use Illuminate\Support\Facades\Log;
use Exception;

define('TELEBIRR_APP_ID', config('key.TELEBIRR_APP_ID'));
define('TELEBIRR_RECEIVER_NAME', config("key.TELEBIRR_RECEIVER_NAME"));
define('TELEBIRR_SHORT_CODE', config('key.TELEBIRR_SHORT_CODE'));
define('TELEBIRR_SUBJECT', config('key.TELEBIRR_SUBJECT'));
define('TELEBIRR_RETURN_URL', config('key.TELEBIRR_RETURN_URL'));
define('TELEBIRR_NOTIFY_URL', config('key.TELEBIRR_NOTIFY_URL'));
define('TELEBIRR_TIMEOUT_EXPRESS', config('key.TELEBIRR_TIMEOUT_EXPRESS'));
define('TELEBIRR_APP_KEY', config('key.TELEBIRR_APP_KEY'));
define('TELEBIRR_PUBLIC_KEY', config('key.TELEBIRR_PUBLIC_KEY'));
define('TELEBIRR_PUBLIC_KEY_C', config('key.TELEBIRR_PUBLIC_KEY_C'));
define('TELEBIRR_INAPP_PAYMENT_URL', config('key.TELEBIRR_INAPP_PAYMENT_URL'));
define('TELEBIRR_H5_URL', config('key.TELEBIRR_H5_URL'));
define('TELEBIRR_BASE_URL', config('key.TELEBIRR_BASE_URL'));
define('TELEBIRR_FABRIC_APP_ID', config('key.TELEBIRR_FABRIC_APP_ID'));
define('TELEBIRR_APP_SECRET', config('key.TELEBIRR_APP_SECRET'));
define('TELEBIRR_MERCHANT_APP_ID', config('key.TELEBIRR_MERCHANT_APP_ID'));
define('TELEBIRR_MERCHANT_CODE', config('key.TELEBIRR_MERCHANT_CODE'));
define('TELEBIRR_TITLE', config('key.TELEBIRR_TITLE'));
define('PRIVATE_KEY', config('key.PRIVATE_KEY'));

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
            $this->baseUrl = TELEBIRR_BASE_URL, 
            $this->fabricAppId = TELEBIRR_FABRIC_APP_ID,
            $this->appSecret = TELEBIRR_APP_SECRET,
            $this->merchantAppId = TELEBIRR_MERCHANT_APP_ID,
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