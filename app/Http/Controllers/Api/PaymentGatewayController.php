<?php

namespace App\Http\Controllers\Api;

use App\Models\Payment;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\CBETransaction;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\TempTransaction;
use Illuminate\Support\Facades\Http;

class PaymentGatewayController extends Controller {
        public function __construct()
        {
            // $this->middleware('auth:api');
        }
        private $securityKey = 'b14ca5898a4e4133bbce2ea2315a1916';

        // Sort the payload
        private function sortedMap($payload)
        {
            ksort($payload);
            return $payload;
        }

        // Create a SHA256 hash
        private function createSignature($payload)
        {
            $temp = [];
            foreach ($payload as $key => $value) {
                $temp[] = "{$key}={$value}";
            }
            return hash('sha256', implode('&', $temp));
        }

        private function encrypt($plainText, $securityKey)
        {
            // Create MD5 hash of the security key (16 bytes)
            $securityKeyHash = md5($securityKey, true);

            // Extend the 16-byte hash to 24 bytes for Triple DES
            $securityKeyArray = $securityKeyHash . substr($securityKeyHash, 0, 8);

            // Encrypt the plain text using Triple DES (3DES-ECB)
            $encryptedData = openssl_encrypt($plainText, 'des-ede3', $securityKeyArray, OPENSSL_RAW_DATA);

            // Encode the encrypted result in base64 (to match Node.js implementation)
            return base64_encode($encryptedData);
        }

        // Triple DES decryption
        private function decrypt($encryptedText, $securityKey)
        {
            // Create MD5 hash of the security key (16 bytes)
            $securityKeyHash = md5($securityKey, true);

            // Extend the 16-byte hash to 24 bytes for Triple DES
            $securityKeyArray = $securityKeyHash . substr($securityKeyHash, 0, 8);

            // Decode the encrypted base64 text back to raw binary
            $encryptedData = base64_decode($encryptedText);

            // Decrypt the data using Triple DES (3DES-ECB)
            return openssl_decrypt($encryptedData, 'des-ede3', $securityKeyArray, OPENSSL_RAW_DATA);
        }
        // private function decrypt($encryptedText, $securityKey)
        // {
        //     $securityKeyHash = md5($securityKey, true);
        //     $securityKeyArray = $securityKeyHash . substr($securityKeyHash, 0, 8);
        //     // null = openssl_random_pseudo_bytes(openssl_cipher_iv_length('des-ede3'));

        //     return openssl_decrypt($encryptedText, 'des-ede3', $securityKeyArray, OPENSSL_RAW_DATA);
        // }

        private $storedAmount;
        private $memberId;
        private $equbId;
        private $balance;
        private $paymentType;

        public function generateUrl(Request $request)
        {
            // Validate that the 'amount' field (A) is present in the request
            $request->validate([
                'amount' => 'required|numeric',
                // 'member_id' => 'required|exists:members,id',
                // 'equb_id' => 'required|exists:equbs,id',
                // 'payment_type' => 'nullable',
                // 'balance' => 'nullable',
                // 'collector' => 'nullable'
            ]);

            // Generate a unique token
            // $token = Str::uuid();

            // TempTransaction::create([
            //     'token' => $token,
            //     'amount' => $request->input('amount'),
            //     'member_id' => $request->input('member_id'),
            //     'equb_id' => $request->input('equb_id'),
            //     'payment_type' => $request->input('payment_type'),
            //     'balance' => $request->input('balance')
            // ]);

            // Call encryptData
            $encryptedUrl = $this->encryptData();

            // Store the amount in the class property for access by `encryptData`
            $this->storedAmount = $request->input('amount');

            // Call the `encryptData` function and get the URL
            return $this->encryptData();
            // return response()->json([
            //     'message' => 'Transaction initialized',
            //     'token' => $token,
            //     'url' => $encryptedUrl
            // ]);
        }

        // Encrypt and send payload
        public function encryptData()
        {
            $amount = $this->storedAmount;
            $payload = [
                "U" => "VEKUB",
                "W" => "782290",
                "T" => "1122_t_med_lab22",
                // "A" => "500",
                "A" => $amount,
                "MC" => "822100",
                "Key" => $this->securityKey
            ];

            // Sort and encrypt the payload
            $sortedPayload = $this->sortedMap($payload);
            $signature = $this->createSignature($sortedPayload);
            $sortedPayload['HV'] = $signature;
            unset($sortedPayload['Key']);

            $encryptedPayload = [];
            foreach ($sortedPayload as $key => $value) {
                $encryptedPayload[$key] = $this->encrypt($value, $this->securityKey);
            }

            $finalEncryptedPayload = $this->encrypt(json_encode($encryptedPayload), $this->securityKey);

            $url = 'https://cbebirrpaymentgateway.cbe.com.et:8888/Default.aspx?r=' . urlencode($finalEncryptedPayload);

            try {
                $response = Http::withOptions(['verify' => false])->post($url);

                // Log the payload and the response for debugging
                Log::info('Payload: ', ['payload' => $encryptedPayload]);
                Log::info('Response: ', ['response' => $response->body()]);

                return response()->json([
                    'message' => 'Payload sent successfully',
                    'responseStatus' => $response->status(),
                    'responseData' => $response->body(),
                    'url' => $url // Log the full request URL
                ]);
            } catch (\Exception $e) {
                Log::error('Error sending payload: ' . $e->getMessage());

                return response()->json([
                    'message' => 'Error sending payload' . $e->getMessage(),
                    'error' => $e->getMessage()
                ], 500);
            }
        }

        // Handle transaction status
        public function transactionStatus(Request $request)
        {
            // Log the request body for debugging
            Log::info('Request body: ', $request->all());

            // Get the EncVal from the request
            $encVal = $request->input('EncVal');

            // Check if EncVal is present
            if (!$encVal) {
                return response()->json(['message' => 'Missing EncVal in request body'], 400);
            }

            // Decrypt the EncVal
            try {
                // Decrypt using your decryption function (Triple DES in this case)
                $decryptedResponse = json_decode($this->decrypt($encVal, $this->securityKey), true);
            } catch (\Exception $e) {
                Log::error('Decryption error: ' . $e->getMessage());
                return response()->json(['message' => 'Invalid EncVal format or decryption failed'], 400);
            }

            // Extract the values from the decrypted response
            $transactionId = $decryptedResponse['TransactionId'] ?? '';
            $state = $decryptedResponse['State'] ?? '';
            $tndDate = $decryptedResponse['TNDDate'] ?? '';
            $signature = $decryptedResponse['Signiture'] ?? '';  // Double-check if it's Signiture or Signature

            Log::info('Decrypted Response: ', $decryptedResponse);

            // Prepare the payload for hashing (order is important)
            $hashingPayload = [
                'key' => $this->securityKey,
                'state' => $state,
                'TNDDate' => $tndDate,
                'transactionId' => $transactionId,
            ];

            // Create the signature hash (equivalent of createSignature and sortedMap in Node.js)
            $calculatedHash = $this->createSignature($hashingPayload);

            Log::info('Calculated Hash: ' . $calculatedHash);
            Log::info('Received Signature: ' . $signature);

            // Compare the calculated hash with the provided signature
            if ($calculatedHash === $signature) {
                // Save transaction details
                $transaction = new CBETransaction();
                $transaction->enc_val = $encVal;
                $transaction->transaction_id = $transactionId;
                $transaction->state = $state;
                $transaction->tnd_date = $tndDate;
                $transaction->signature = $signature;
                $transaction->save();
                
                // Payment
                // $amount = $this->storedAmount;
                // $member = $this->memberId;
                // $equbId = $this->equbId;
                // $balance = $this->balance;
                // $payment = Payment::create([
                //     'amount' => $amount,
                //     'member_id' => $member,
                //     'equb_id' => $equbId,
                //     'payment_type' => 'CBE gateway',
                //     'balance' => $balance
                // ]);
                Log::info('Transaction verified successfully.');
                return response()->json([
                    'message' => 'Transaction verified',
                    'TransactionId' => $transactionId,
                    'State' => $state,
                    'TNDDate' => $tndDate,
                    // 'payment' => $payment
                ]);
            } else {
                Log::error('Transaction verification failed: data may be altered.');
                return response()->json(['message' => 'Transaction verification failed: data may be altered.'], 400);
            }
        }

        // Check transaction status
        public function checkTransactionStatus(Request $request)
        {
            $transactionId = $request->input('transactionId');
            $tillcode = $request->input('tillcode');

            $payload = [
                'transactionId' => $transactionId,
                'Tillcode' => $tillcode,
                'Key' => $this->securityKey
            ];

            $hashValue = $this->createSignature($this->sortedMap($payload));

            $requestBody = [
                'transactionId' => $transactionId,
                'Tillcode' => $tillcode,
                'Signiture' => $hashValue
            ];

            try {
                // $response = Http::post('https://cbebirrpaymentgateway.cbe.com.et:8888/api/cbebpg/TXNSTAT', $requestBody);
                // $encryptedResponseValue = $response->json()['EncryptedResponseValue'];

                // $decryptedResponse = json_decode($this->decrypt($encryptedResponseValue, $this->securityKey), true);
                $response = Http::withOptions(['verify' => false])
                ->post('https://cbebirrpaymentgateway.cbe.com.et:8888/api/cbebpg/TXNSTAT', $requestBody);

                // Log the raw response for debugging
                Log::info('Response Body:', ['body' => $response->body()]);

                // Ensure the response is JSON
                if (!$this->isJson($response->body())) {
                    return response()->json(['message' => 'Unexpected non-JSON response', 'data' => $response->body()], 500);
                }

                // Process the JSON response
                $responseData = $response->json();

                if (!isset($responseData['EncryptedResponseValue'])) {
                    return response()->json(['message' => 'Missing EncryptedResponseValue in the response'], 500);
                }

                $encryptedResponseValue = $responseData['EncryptedResponseValue'];

                // Decrypt the response
                $decryptedResponse = json_decode($this->decrypt($encryptedResponseValue, $this->securityKey), true);

                $tillCode = $decryptedResponse['Tillcode'];
                $transactionId = $decryptedResponse['TransactionId'];
                $state = $decryptedResponse['State'];
                $tndDate = $decryptedResponse['TNDDate'];
                $signature = $decryptedResponse['Signiture'];

                // Verify the signature
                $verifyPayload = [
                    'Tillcode' => $tillCode,
                    'TransactionId' => $transactionId,
                    'State' => $state,
                    'TNDDate' => $tndDate,
                    'Key' => $this->securityKey
                ];

                $verifyHashValue = $this->createSignature($this->sortedMap($verifyPayload));

                if ($verifyHashValue === $signature) {
                    return response()->json(['message' => 'Transaction status verified', 'TransactionId' => $transactionId, 'State' => $state, 'TNDDate' => $tndDate]);
                } else {
                    return response()->json(['message' => 'Transaction verification failed: data may be altered'], 400);
                }
            } catch (\Exception $e) {
                return response()->json(['message' => 'Error verifying transaction status', 'error' => $e->getMessage()], 500);
            }
        }
        private function isJson($string)
        {
            json_decode($string);
            return json_last_error() === JSON_ERROR_NONE;
        }
}
