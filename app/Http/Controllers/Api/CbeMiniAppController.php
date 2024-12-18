<?php

namespace App\Http\Controllers\Api;

use Exception;
use App\Models\AppToken;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class CbeMiniAppController extends Controller
{
    public function index(){
        return view('cbe_payment');
    }
    public function validateToken(Request $request) {

        try {
            // $token = $request->header('Authorization');
            $token = "Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJwaG9uZSI6IjI1MTkxODA5NDQ1NSIsImV4cCI6MTczODI3OTU0NH0.8NrfTbeErIXyin-PH0Vgvnkq4-q2TeVvQz4P3FtBqZU";
            // dd($token);
            if (!$token) {
                return response()->json([
                    'error' => 'Token is missing'
                ], 400);
            }

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => $token,
            ])->get('https://cbebirrpaymentgateway.cbe.com.et:8888/auth/user');

            if ($response->status() === 200) {
                return response()->json(['data' => $response->json()], 200);
                // save the token
                AppToken::create([
                    'token' => $token
                ]);
            } else {
                return response()->json(['error' => 'Invalid Token'], 401);
            }

        } catch (Exception $ex) {
            return response()->json([
                'error' => $ex->getMessage()
            ], 500);
        }
    }

    public function processPayment(Request $request)
    {
        // dd($request->all());
        try {
            $validated = $request->validate([
                'amount' => 'required|numeric',
                'transactionId' => 'required|string',
                'tillCode' => 'required|string',
                // 'callBackURL' => 'required|url',
                // 'transactionTime' => 'required|date',
                // 'companyName' => 'required|string',
                // 'token' => 'required|string',
            ]);
        
            $payload = [
                'token' => "Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJwaG9uZSI6IjI1MTkxODA5NDQ1NSIsImV4cCI6MTczODI3OTU0NH0.8NrfTbeErIXyin-PH0Vgvnkq4-q2TeVvQz4P3FtBqZU",
                'callBackURL' => route('cbe.callback'),
                'amount' => $validated['amount'],
                'companyName' => "Virtualekub",
                'trasnactionId' => $validated['transactionId'],
                'tillCode' => $validated['tillCode'],
                'key' => env('CBE_HASHING_KEY'),
                'transactionTime' => now()->toIso8601String(), 
            ];
        
            // $payload['tillCode'] = '4002415';
        
            // Sort payload by keys
            ksort($payload);
            // Convert to query string format
            $processedPayload = http_build_query($payload);
            dd($processedPayload);
            // Hash the payload
            $signature = hash_hmac('sha256', $processedPayload, env('CBE_HASHING_KEY'));
            // dd($signature);
            $payload['signature'] = $signature;
        
            // Send request to CBE Birr
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => $payload['token'],
            ])->post('https://cbebirrpaymentgateway.cbe.com.et:8888/auth/pay', $payload);
        
            if ($response->status() === 200) {
                return response()->json(['token' => $response->json('token')], 200);
            } else {
                return response()->json(['error' => 'Transaction failed'], 401);
            } 

        } catch (Exception $ex) {
            // dd($ex);
            $msg = $ex->getMessage();
            Session::flash('error', $msg);
            return back();
        }
    }

    public function paymentCallback(Request $request)
    {
        try {

            $token = $request->header('Authorization');
            if (!$token) {
                return response()->json([
                    'error' => 'Token is missing'
                ], 400);
            }

            // Validate the token
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => $token,
            ])->get('https://cbebirrpaymentgateway.cbe.com.et:8888/auth/user');

            if ($response->status() !== 200) {
                return response()->json(['error' => 'Invalid Token'], 401);
            }

            // Verify the signature
            $data = $request->all();
            $hashingKey = env('CBE_HASHING_KEY');
            ksort($data);

            $processedPayload = http_build_query($data);
            $calculatedSignature = hash_hmac('sha256', $processedPayload, $hashingKey);

            if ($calculatedSignature !== $data['signature']) {
                return response()->json(['error' => 'Invalid Signature'], 400);
            }

            // Process the transaction
            return response()->json(['status' => 'success'], 200);

        } catch (Exception $ex) {
            return response()->json([
                'error' => $ex->getMessage()
            ], 500);
        }
    }
}
