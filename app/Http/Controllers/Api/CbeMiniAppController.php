<?php

namespace App\Http\Controllers\Api;

use Exception;
use App\Models\Equb;
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
    public function validateToken(Request $request)
    {
        try {
            // $token = $request->header('Authorization');
            $token = "Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJwaG9uZSI6IjI1MTkxODA5NDQ1NSIsImV4cCI6MTczODQ0NjI5NX0.OitEAlGrtZrOIqHI5BNFLpqcp5xAhYy8YjIe3OdU9KE";
            if (!$token) {
                return response()->json([
                    'error' => 'Token is missing'
                ], 400);
            }

            // Remove the 'Bearer' prefix
            $cleanedToken = str_replace('Bearer ', '', $token);

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => $token,
            ])->get('https://cbebirrpaymentgateway.cbe.com.et:8888/auth/user');

            if ($response->status() === 200) {
                // Save the token to the database
                $Phone =  $response->json('phone');

                // Check if the phone starts with "+"
                if (!$Phone) {
                    return response()->json(['error' => 'Phone number is missing or invalid'], 400);
                }
                if (strpos($request->json('phone'), '+') !== 0) {
                    $Phone = '+' . $Phone;
                }
                AppToken::create([
                    'phone' => $Phone,
                    'token' => $cleanedToken
                ]);
                $phone = $response->json('phone');
                $equb = Equb::with('equbType')->whereHas('member', function ($query) use ($phone) {
                    $query->where('phone', $phone);
                })->get();
                // dd($equb);
                if ($equb->count() === 0) {
                    // return response()->json(['error' => 'No equb found for the user'], 404);
                    return view('cbe_payment', [
                        'token' => $token, 
                        'phone' => $response->json('phone'), 
                        'equbs' => [], 
                        'error' => 'No equb found for the user'
                    ]);
                }
                return view('cbe_payment', [
                    'token' => $cleanedToken, 
                    'phone' => $response->json('phone'), 
                    'equbs' => $equb,
                    'error' => ''
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
    //     try {
    //         $validated = $request->validate([
    //             'amount' => 'required|numeric',
    //             'equb_id' => 'required|exists:equbs,id',
    //             'token' => 'required|exists:app_tokens,token',
    //             'phone' => 'required|exists:app_tokens,phone',
    //         ]);
    //         $equb = Equb::findOrFail($validated['equb_id']);
    //         $transactionId = uniqid();
    //         $payload = [
    //             "amount" => $validated['amount'],
    //             "callBackURL" => route('cbe.callback'),
    //             "companyName" => env('CBE_MINI_COMPANY_NAME'),
    //             "key" => env('CBE_MINI_HASHING_KEY'),
    //             "tillCode" => env('CBE_MINI_TILL_CODE'),
    //             "token" => $validated['token'],
    //             "transactionId" => $transactionId,
    //             "transactionTime" => now()->toIso8601String(),
    //         ];
    //         // dd($payload);
    //         ksort($payload);
    //         $processedPayload = http_build_query($payload);

    //         $signature = hash_hmac('sha256', $processedPayload, env('CBE_HASHING_KEY'));
    //         $payload['signature'] = $signature;

    //         // Sort the payload again, including the signature
    //         // Custom sort the payload to move "signature" before "key"
    //         $orderedKeys = [
    //             "amount",
    //             "callBackURL",
    //             "companyName",
    //             "signature", // Place "signature" before "key"
    //             "key",
    //             "tillCode",
    //             "token",
    //             "transactionId",
    //             "transactionTime",
    //         ];
    //         $sortedPayload = array_merge(array_flip($orderedKeys), $payload);
    //         dd($sortedPayload, $signature);
    //         $response = Http::withHeaders([
    //             'Content-Type' => 'application/json',
    //             'Accept' => 'application/json',
    //             'Authorization' => $payload['token'],
    //         ])->post('https://cbebirrpaymentgateway.cbe.com.et:8888/auth/pay', $sortedPayload);
    //             // dd($response->json());
    //         if ($response->status() === 200) {
    //             return response()->json(['status' => 'success', 'token' => $response->json('token')], 200);
    //         } else {
    //             // dd($response->status());
    //             \Log::error('CBE API Error:', [$response->json()]);
    //             return response()->json(['status' => 'error', 'message' => 'Transaction failed'], $response->status());
    //         }
    //     } catch (\Exception $ex) {
    //         return response()->json(['status' => 'error', 'message' => $ex->getMessage()], 500);
    //     }
    // }
    public function processPayment(Request $request)
    {
        try {
            // Step 2.1: Preparing data to be sent
            $validated = $request->validate([
                'amount' => 'required|numeric',
                'equb_id' => 'required|exists:equbs,id',
                'token' => 'required|exists:app_tokens,token',
                'phone' => 'required|exists:app_tokens,phone',
            ]);
            // dd($validated['token']);
            $transactionId = uniqid(); // Generate unique transaction ID
            $transactionTime = now()->toIso8601String(); // Get current timestamp in ISO8601 format
            $callbackUrl = route('cbe.callback'); // Callback URL for response handling
            $companyName = env('CBE_MINI_COMPANY_NAME'); // Provided company name
            $hashingKey = env('CBE_MINI_HASHING_KEY'); // Provided hashing key
            $tillCode = env('CBE_MINI_TILL_CODE'); // Provided till code

            // Prepare payload as per guideline
            $payload = [
                "amount" => $validated['amount'],
                "callBackURL" => $callbackUrl,
                "companyName" => $companyName,
                "key" => $hashingKey,
                "tillCode" => $tillCode,
                "token" => $validated['token'],
                "transactionId" => $transactionId,
                "transactionTime" => $transactionTime,
            ];

            // Step 2.3: Sorting payload and preparing hashing payload
            ksort($payload); // Sort payload by keys
            $processedPayload = http_build_query($payload); // Convert sorted payload to query string

            // Step 2.3.3: Hash the processed payload
            // $signature = hash_hmac('sha256', $processedPayload, env('CBE_HASHING_KEY'));
            $signature = hash_hmac('sha256', $processedPayload, $hashingKey);

            // // Step 2.4: Preparing final payload
            // $finalPayload = array_merge($payload, ['signature' => $signature]);
            $payload['signature'] = $signature;

             // Sort the payload again, including the signature
             // Custom sort the payload to move "signature" before "key"
            $orderedKeys = [
                "amount",
                "callBackURL",
                "companyName",
                "signature", // Place "signature" before "key"
                "key",
                "tillCode",
                "token",
                "transactionId",
                "transactionTime",
            ];
            $sortedPayload = array_merge(array_flip($orderedKeys), $payload);
            // dd($sortedPayload, $signature);
            // Step 2.5: Sending the final payload
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                // 'Authorization' => $validated['token'],
                'Authorization' => "Bearer " . $validated['token'],
            ])->post('https://cbebirrpaymentgateway.cbe.com.et:8888/auth/pay', $sortedPayload);
                // dd($response->status());
            // Check the response status
            if ($response->status() === 200) {
                return response()->json(['status' => 'success', 'token' => $response->json('token')], 200);
            } else {
                \Log::error('CBE API Error:', ['response' => $response->json()]);
                return response()->json(['status' => 'error', 'message' => 'Transaction failed'], $response->status());
            }
        } catch (\Exception $ex) {
            return response()->json(['status' => 'error', 'message' => $ex->getMessage()], 500);
        }
    }
    // public function processPayment(Request $request)
    // {
    //     try {
    //         // Step 2.1: Preparing data to be sent
    //         $validated = $request->validate([
    //             'amount' => 'required|numeric',
    //             'equb_id' => 'required|exists:equbs,id',
    //             'token' => 'required|exists:app_tokens,token',
    //             'phone' => 'required|exists:app_tokens,phone',
    //         ]);

    //         $transactionId = uniqid(); // Generate unique transaction ID
    //         $transactionTime = now()->toIso8601String(); // Get current timestamp in ISO8601 format
    //         $callbackUrl = route('cbe.callback'); // Callback URL for response handling
    //         $companyName = env('CBE_MINI_COMPANY_NAME'); // Provided company name
    //         $hashingKey = env('CBE_MINI_HASHING_KEY'); // Provided hashing key
    //         $tillCode = env('CBE_MINI_TILL_CODE'); // Provided till code

    //         // Prepare payload as per guideline
    //         $payload = [
    //             "amount" => $validated['amount'],
    //             "callBackURL" => $callbackUrl,
    //             "companyName" => $companyName,
    //             "key" => $hashingKey,
    //             "tillCode" => $tillCode,
    //             "token" => $validated['token'],
    //             "transactionId" => $transactionId,
    //             "transactionTime" => $transactionTime,
    //         ];

    //         // Step 2.3: Sorting payload and preparing hashing payload
    //         ksort($payload); // Sort payload by keys
    //         $processedPayload = http_build_query($payload); // Convert sorted payload to query string

    //         // Step 2.3.3: Hash the processed payload
    //         $signature = hash_hmac('sha256', $processedPayload, env('CBE_HASHING_KEY'));

    //         // Step 2.4: Preparing final payload
    //         $finalPayload = array_merge($payload, ['signature' => $signature]);

    //         // Step 2.5: Sending the final payload
    //         $response = Http::withHeaders([
    //             'Content-Type' => 'application/json',
    //             'Accept' => 'application/json',
    //             'Authorization' => "Bearer " . $validated['token'],
    //         ])->post('https://cbebirrpaymentgateway.cbe.com.et:8888/auth/pay', $finalPayload);

    //         // Check the response status
    //         if ($response->status() === 200) {
    //             return response()->json(['status' => 'success', 'token' => $response->json('token')], 200);
    //         } else {
    //             \Log::error('CBE API Error:', ['response' => $response->json()]);
    //             return response()->json(['status' => 'error', 'message' => 'Transaction failed'], $response->status());
    //         }
    //     } catch (\Exception $ex) {
    //         return response()->json(['status' => 'error', 'message' => $ex->getMessage()], 500);
    //     }
    // }


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
