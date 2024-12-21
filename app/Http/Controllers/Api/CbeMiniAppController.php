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

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => $token,
            ])->get('https://cbebirrpaymentgateway.cbe.com.et:8888/auth/user');

            if ($response->status() === 200) {
                // Save the token to the database
                $Phone =  $request->json('phone');
                // Check if the phone starts with "+"
                if (strpos($Phone, '+') !== 0) {
                    $Phone = '+' . $Phone;
                }
                AppToken::create([
                    'phone' => $Phone,
                    'token' => $token
                ]);
                $phone = $response->json('phone');
                $equb = Equb::with('equbType')->whereHas('member', function ($query) use ($phone) {
                    $query->where('phone', $phone);
                })->get();
                // dd($equb);
                if ($equb->count() === 0) {
                    return response()->json(['error' => 'No equb found for the user'], 404);
                }
                return view('cbe_payment', ['token' => $token, 'phone' => $response->json('phone'), 'equbs' => $equb]);
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
        try {
            $validated = $request->validate([
                'amount' => 'required|numeric',
                'equb_id' => 'required|exists:equbs,id',
                'token' => 'required|exists:app_tokens,token',
                'phone' => 'required|exists:app_tokens,phone',
            ]);
            $equb = Equb::findOrFail($validated['equb_id']);
            $transactionId = uniqid();
            $payload = [
                'amount' => $validated['amount'],
                'callBackURL' => route('cbe.callback'),
                'companyName' => env('CBE_COMPANY_NAME'),
                'key' => env('CBE_HASHING_KEY'),
                'tillCode' => env('CBE_TILL_CODE'),
                'token' => $validated['token'],
                'transactionId' => $transactionId,
                'transactionTime' => now()->toIso8601String(),
            ];

            ksort($payload);
            $processedPayload = http_build_query($payload);
            // dd($processedPayload);
            $signature = hash_hmac('sha256', $processedPayload, env('CBE_HASHING_KEY'));
            $payload['signature'] = $signature;
    
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => $payload['token'],
            ])->post('https://cbebirrpaymentgateway.cbe.com.et:8888/auth/pay', $payload);
                dd($response);
            if ($response->status() === 200) {
                return response()->json(['status' => 'success', 'token' => $response->json('token')], 200);
            } else {
                dd($response->status());
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
