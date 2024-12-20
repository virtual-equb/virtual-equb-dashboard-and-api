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

    // public function processPayment(Request $request)
    // {
    //     // dd($request->all());
    //     try {
    //         $validated = $request->validate([
    //             'amount' => 'required|numeric',
    //             'transactionId' => 'required|string',
    //             'tillCode' => 'required|string',
    //             // 'callBackURL' => 'required|url',
    //             // 'transactionTime' => 'required|date',
    //             // 'companyName' => 'required|string',
    //             // 'token' => 'required|string',
    //         ]);

    //         $payload = [
    //             'amount' => $validated['amount'], // Transaction amount
    //             'callBackURL' => route('cbe.callback'), // Your callback URL
    //             'companyName' => 'Virtualekub', // Company name
    //             'transactionId' => $validated['transactionId'], // Unique transaction ID
    //             'tillCode' => $validated['tillCode'], // Merchant's till code
    //             'key' => env('CBE_HASHING_KEY'), // Hashing key from CBE
    //             'token' => "Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJwaG9uZSI6IjI1MTkxODA5NDQ1NSIsImV4cCI6MTczODI3OTU0NH0.8NrfTbeErIXyin-PH0Vgvnkq4-q2TeVvQz4P3FtBqZU", // Authorization token
    //             'transactionTime' => now()->toIso8601String(), // Current timestamp
    //         ];
        
    //         // $payload['tillCode'] = '4002415';
        
    //         // Sort payload by keys
    //         ksort($payload);
    //         // Convert to query string format
    //         $processedPayload = http_build_query($payload);
    //         // dd($processedPayload);
    //         // Hash the payload
    //         $signature = hash_hmac('sha256', $processedPayload, env('CBE_HASHING_KEY'));
    //         // dd($signature);
    //         $payload['signature'] = $signature;
        
    //         // Send request to CBE Birr
    //         $response = Http::withHeaders([
    //             'Content-Type' => 'application/json',
    //             'Accept' => 'application/json',
    //             // 'Authorization' => $payload['token'],
    //             'Authorization' => "Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJwaG9uZSI6IjI1MTkxODA5NDQ1NSIsImV4cCI6MTczODI3OTU0NH0.8NrfTbeErIXyin-PH0Vgvnkq4-q2TeVvQz4P3FtBqZU",
    //         ])->post('https://cbebirrpaymentgateway.cbe.com.et:8888/auth/pay', $payload);
        
    //         if ($response->status() === 200) {
    //             return response()->json(['token' => $response->json('token')], 200);
    //         } else {
    //             return response()->json(['error' => 'Transaction failed'], 401);
    //         } 

    //     } catch (Exception $ex) {
    //         // dd($ex);
    //         $msg = $ex->getMessage();
    //         Session::flash('error', $msg);
    //         return back();
    //     }
    // }
    public function processPayment(Request $request)
    {
        try {
            $validated = $request->validate([
                'amount' => 'required|numeric',
                'transactionId' => 'required|string',
                'tillCode' => 'required|string',
            ]);
    
            $payload = [
                'amount' => $validated['amount'],
                'callBackURL' => route('cbe.callback'),
                'companyName' => 'Virtualekub',
                'key' => env('CBE_HASHING_KEY'),
                // 'key' => 'x9pBKzQBj45uWWlkID0w6CZISM0lkg',
                'tillCode' => $validated['tillCode'],
                'token' => "Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...",
                'transactionId' => $validated['transactionId'],
                'transactionTime' => now()->toIso8601String(),
            ];
    
            ksort($payload);
            $processedPayload = http_build_query($payload);
            dd($processedPayload);
            $signature = hash_hmac('sha256', $processedPayload, env('CBE_HASHING_KEY'));
            $payload['signature'] = $signature;
    
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => "Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...",
            ])->post('https://cbebirrpaymentgateway.cbe.com.et:8888/auth/pay', $payload);
    
            if ($response->status() === 200) {
                return response()->json(['status' => 'success', 'token' => $response->json('token')], 200);
            } else {
                \Log::error('CBE API Error:', [$response->json()]);
                return response()->json(['status' => 'error', 'message' => 'Transaction failed'], $response->status());
            }
        } catch (\Exception $ex) {
            return response()->json(['status' => 'error', 'message' => $ex->getMessage()], 500);
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
