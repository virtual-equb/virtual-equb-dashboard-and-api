<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use App\Helpers\SignHelper;
use Exception;
use Illuminate\Support\Facades\Log;

class TelebirrMiniAppCreateOrderService
{
    protected $baseUrl;
    protected $req;
    protected $fabricAppId;
    protected $appSecret;
    protected $merchantAppId;
    protected $merchantCode;
    protected $notifyPath;
    protected $redirectPath;
    protected $paymentId;

    public function __construct($baseUrl, $req, $fabricAppId, $appSecret, $merchantAppId, $merchantCode, $paymentId)
    {
        $this->baseUrl = $baseUrl;
        $this->req = $req;
        $this->fabricAppId = $fabricAppId;
        $this->appSecret = $appSecret;
        $this->merchantAppId = $merchantAppId;
        $this->merchantCode = $merchantCode;
        $this->paymentId = $paymentId;
        $this->notifyPath = TELEBIRR_MINIAPP_NOTIFY_URL;
        $this->redirectPath = TELEBIRR_MINIAPP_RETURN_URL;
    }

    public function createOrder()
    {
        $amount = $this->req->amount;

        // Initialize ApplyFabricToken with Telebirr configurations
        $applyFabricTokenServiceMiniApp = new ApplyFabricTokenServiceMiniApp(
            TELEBIRR_BASE_URL,
            TELEBIRR_FABRIC_APP_ID,
            TELEBIRR_APP_SECRET,
            TELEBIRR_MERCHANT_APP_ID
        );

        // Get the fabric token
        $tokenResult = json_decode($applyFabricTokenServiceMiniApp->applyFabricToken());

        if (!$tokenResult || !isset($tokenResult->token)) {
            throw new Exception('Failed to retrive Fabric token - MiniApp :' . json_encode($tokenResult));
        }

        $fabricToken = $tokenResult->token;

        // Create the order request
        $createOrderResult = $this->requestCreateOrder($fabricToken, 'Test Equb Payment', $amount);
        Log::info('Create Order API Response - MiniApp:' . $createOrderResult);

        $prepayId = json_decode($createOrderResult)->biz_content->prepay_id;

        return $this->createRawRequest($prepayId);
    }

    public function requestCreateOrder($fabricToken, $title, $amount)
    {
        try {
            $response = Http::timeout(60)->withHeaders([
                'Content-Type' => 'application/json',
                'X-APP-Key' => $this->fabricAppId,
                'Authorization' => $fabricToken,
            ])->post($this->baseUrl . '/payment/v1/merchant/preOrder', $this->createRequestObject($title, $amount));
            
            Log::info('requestCreateOrder API Response' . $response->body());

            return $response->body();
        } catch (Exception $e) {
            Log::info('log from requestCreateOrder');

            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function createRequestObject($title, $amount)
    {
        try {
            $req = [
                'nonce_str' => SignHelper::createNonceStr(),
                'method' => 'payment.preorder',
                'timestamp' => SignHelper::createTimeStamp(),
                'version' => '1.0',
                'biz_content' => [
                    'notify_url' => $this->notifyPath,
                    'business_type' => 'BuyGoods',
                    'trade_type' => 'InApp',
                    'appid' => $this->merchantAppId,
                    'merch_code' => $this->merchantCode,
                    'merch_order_id' => (string) $this->paymentId,
                    'title' => "Equb Payment",
                    'total_amount' => (string) $amount,
                    'trans_currency' => 'ETB',
                    'timeout_express' => '120m',
                    'payee_identifier' => $this->merchantCode,
                    'payee_identifier_type' => '04',
                    'payee_type' => '5000',
                ],
                'sign_type' => 'SHA256WithRSA',
            ];

            // Sign the request
            $req['sign'] = SignHelper::sign($req); 
            return $req;
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function createRawRequest($prepayId)
    {
        $maps = [
            'appid' => $this->merchantAppId,
            'merch_code' => $this->merchantCode,
            'nonce_str' => SignHelper::createNonceStr(),
            'prepay_id' => $prepayId,
            'timestamp' => SignHelper::createTimeStamp(),
            'sign_type' => 'SHA256WithRSA'
        ];

        // Construct the raw request string with proper encoding
        $rawRequest = http_build_query($maps, '', '&', PHP_QUERY_RFC3986);

        // Generate the signature
        $sign = SignHelper::sign($maps);

        // Append the sign
        $rawRequest .= '&sign=' . $sign;

        // return $rawRequest;
        return response()->json([
            'rawRequest' => $rawRequest
        ]);
    }
}
