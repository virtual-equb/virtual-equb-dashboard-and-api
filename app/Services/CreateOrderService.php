<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use App\Services\ApplyFabricToken;
use App\Helpers\SignHelper; // Make sure to include the SignHelper class
use Exception;

use Illuminate\Support\Facades\Log;
class CreateOrderService
{
    protected $baseUrl;
    protected $req;
    protected $fabricAppId;
    protected $appSecret;
    protected $merchantAppId;
    protected $merchantCode;
    protected $notifyPath;
    protected $paymentId;


    public function __construct($baseUrl, $req, $fabricAppId, $appSecret, $merchantAppId, $merchantCode, $paymentId)
    {
        $this->baseUrl = $baseUrl; // use the provided base URL
        $this->req = $req; // request object
        $this->fabricAppId = $fabricAppId; // other parameters
        $this->appSecret = $appSecret;
        $this->merchantAppId = $merchantAppId;
        $this->merchantCode = $merchantCode;
        $this->paymentId = $paymentId;
        $this->notifyPath = TELEBIRR_NOTIFY_URL;
    }


    public function createOrder()
    {

        $amount = $this->req->amount;

        // Initialize ApplyFabricToken with Telebirr configurations
        $applyFabricTokenService = new ApplyFabricTokenService(
            TELEBIRR_BASE_URL,
            TELEBIRR_FABRIC_APP_ID,
            TELEBIRR_APP_SECRET,
            TELEBIRR_MERCHANT_APP_ID
        );

        // Get the fabric token
        $tokenResult = json_decode($applyFabricTokenService->applyFabricToken());
        if (!$tokenResult || !isset($tokenResult->token)) {
            throw new Exception('Failed to retrive Fabric token :' . json_encode($tokenResult));
        }
        $fabricToken = $tokenResult->token;

        // Create the order request
        $createOrderResult = $this->requestCreateOrder($fabricToken, TELEBIRR_TITLE, $amount);
        Log::info('Create Order API Response:' . $createOrderResult);

        $decodedResult = json_decode($createOrderResult);
        if (!$decodedResult || !isset($decodedResult->biz_content->prepay_id)) {
            throw new Exception('Invalid response from API:' . $createOrderResult);
        }
        // $prepayId = json_decode($createOrderResult)->biz_content->prepay_id;
        $prepayId = $decodedResult->biz_content->prepay_id;

        return $this->createRawRequest($prepayId);
    }

    public function requestCreateOrder($fabricToken, $title, $amount)


    {
        // Use Laravel's HTTP client to send a POST request


        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'X-APP-Key' => $this->fabricAppId,
                'Authorization' => $fabricToken,
            ])->post($this->baseUrl . '/payment/v1/merchant/preOrder', $this->createRequestObject($title, $amount));
            
            Log::info('API Response' . $response->body());
            return $response->body();
        } catch (Exception $e) {
            Log::info('log from requestCreateOrder');
             Log::info($e);
        }
    }

    // Inside your CreateOrderService class

    public function createRequestObject($title, $amount)
    {

        try {
              Log::info('Notify url');
            Log::info($this->notifyPath);

            // Create request array
            $req = [
                'nonce_str' => SignHelper::createNonceStr(),
                'method' => 'payment.preorder',
                'timestamp' => SignHelper::createTimeStamp(),
                'version' => '1.0',
                'biz_content' => [
                    'notify_url' => $this->notifyPath, // Set your notification URL
                    'business_type' => 'BuyGoods',
                    'trade_type' => 'Cross-App',
                    'appid' => $this->merchantAppId,
                    'merch_code' => $this->merchantCode,
                    'merch_order_id' => (string) $this->paymentId,
                    'title' => "hello",
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
            'sign_type' => 'SHA256WithRSA',
        ];

        // Create the raw request string
        $rawRequest = collect($maps)
            ->map(fn($value, $key) => "$key=$value")
            ->join('&');

        // Sign the raw request
        $rawRequest .= '&sign=' . SignHelper::sign($maps);

        return $rawRequest;
    }
}
