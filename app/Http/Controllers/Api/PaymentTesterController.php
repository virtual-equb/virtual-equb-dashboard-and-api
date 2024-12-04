<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;

class PaymentTesterController extends Controller
{
    private $securityKey = 'b14ca5898a4e4133bbce2ea2315a1916';

     // Triple DES encryption
     private function encrypt($plainText, $securityKey)
    {
        // Create MD5 hash of the security key (16 bytes)
        $securityKeyHash = md5($securityKey, true);

        // Extend the 16-byte hash to 24 bytes for Triple DES
        $securityKeyArray = $securityKeyHash . substr($securityKeyHash, 0, 8);

        // Encrypt the plain text using Triple DES (3DES-ECB)
        $encryptedData = openssl_encrypt($plainText, 'des-ede3', $securityKeyArray, OPENSSL_RAW_DATA);

        // Encode the encrypted result in base64
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

    // Sort the payload alphabetically by keys
    private function sortedMap($payload)
    {
        ksort($payload);  // Sorts by key
        return $payload;
    }

    // Create a SHA256 hash (similar to Node.js createSignature function)
    private function createSignature($payload)
   {
       // Sort the payload by keys
    //    ksort($payload);
       // Prepare the string in "key=value" format, separated by "&"
       $temp = [];
       foreach ($payload as $key => $value) {
           $temp[] = "{$key}={$value}";
       }

       // Concatenate the string with "&"
       $hashString = implode('&', $temp);

       // Generate SHA-256 hash
       return hash('sha256', $hashString);
   }

    public function encryptData(Request $request)
    {
        $payload = [
            "U" => "VEKUB",  // User ID
            "W" => "782290", // Password
            "T" => "1122_t_med_lab22", // Transaction ID
            "A" => "500", // Amount
            "MC" => "822100", // Merchant Code
            "Key" => "b14ca5898a4e4133bbce2ea2315a1916"
        ];

        // Step 2: Sort and create the hash signature
        $sortedPayload = $this->sortedMap($payload);
        $signature = $this->createSignature($sortedPayload);

        // Add the hash value to the payload
        $sortedPayload['HV'] = $signature;
        unset($sortedPayload['Key']);

        // Step 3: Encrypt each field
        $encryptedPayload = [];
        foreach ($sortedPayload as $key => $value) {
            $encryptedPayload[$key] = $this->encrypt($value, $this->securityKey);
        }

        // Step 5: Encrypt the entire payload JSON
        $finalEncryptedPayload = $this->encrypt(json_encode($encryptedPayload), $this->securityKey);

        // Step 6: Send the encrypted data as a query parameter
        $url = 'https://cbebirrpaymentgateway.cbe.com.et:8888/Default.aspx?r=' . urlencode($finalEncryptedPayload);

        try {
            $response = Http::withOptions(['verify' => false])->post($url);
            return response()->json([
                'message' => 'Payload sent successfully',
                'responseStatus' => $response->status(),
                'responseData' => $response->body(),
                'url' => $url
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error sending payload',
                'error' => $e->getMessage()
            ], 500);
        }
    }

     // Handle transaction status
    //  public function transactionStatus(Request $request)
    //  {
    //      // Log the request body for debugging
    //      Log::info('Request body: ', $request->all());

    //      $encVal = $request->input('EncVal');

    //      if (!$encVal) {
    //          return response()->json(['message' => 'Missing EncVal in request body'], 400);
    //      }

    //      // Decrypt the EncVal
    //      try {
    //          $decryptedResponse = json_decode($this->decrypt($encVal, $this->securityKey), true);
    //      } catch (\Exception $e) {
    //          Log::error('Decryption error: ' . $e->getMessage());
    //          return response()->json(['message' => 'Invalid EncVal format or decryption failed'], 400);
    //      }

    //      // Extract the values
    //      $transactionId = $decryptedResponse['TransactionId'] ?? '';
    //      $state = $decryptedResponse['State'] ?? '';
    //      $tndDate = $decryptedResponse['TNDDate'] ?? '';
    //      $signature = $decryptedResponse['Signature'] ?? '';

    //      Log::info('Decrypted Response: ', $decryptedResponse);

    //      // Prepare the payload for hashing
    //      $hashingPayload = [
    //          'TransactionId' => $transactionId,
    //          'State' => $state,
    //          'TNDDate' => $tndDate,
    //          'Key' => $this->securityKey  // Include Key if required for hash calculation
    //      ];

    //      // Log the payload before signing
    //      Log::info('Hashing Payload Before Signing: ', $hashingPayload);

    //      ksort($hashingPayload);

    //      // Create the signature hash (sorted and hashed)
    //      $calculatedHash = $this->createSignature($hashingPayload, true);

    //      Log::info('Calculated Hash: ' . $calculatedHash);
    //      Log::info('Received Signature: ' . $signature);

    //      if ($calculatedHash === $signature) {
    //          return response()->json([
    //              'message' => 'Transaction verified',
    //              'TransactionId' => $transactionId,
    //              'State' => $state,
    //              'TNDDate' => $tndDate
    //          ]);
    //      } else {
    //          return response()->json(['message' => 'Transaction verification failed: data may be altered.'], 400);
    //      }
    //  }
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
            $decryptedResponse = json_decode($this->decrypt($encVal, $this->securityKey), true);
        } catch (\Exception $e) {
            Log::error('Decryption error: ' . $e->getMessage());
            return response()->json(['message' => 'Invalid EncVal format or decryption failed'], 400);
        }
        var_dump('Decrypted Response', $decryptedResponse['Signiture']);

        // Extract the values from the decrypted response
        $transactionId = $decryptedResponse['TransactionId'] ?? '';
        $state = $decryptedResponse['State'] ?? '';
        $tndDate = $decryptedResponse['TNDDate'] ?? '';
        $signature = $decryptedResponse['Signiture'] ?? '';  // Ensure correct spelling

        print_r('Signature: ', $signature);

        // Prepare the payload for hashing (exclude the "Key" field)
        $hashingPayload = [
            'key' => "b14ca5898a4e4133bbce2ea2315a1916",
            'state' => $state,
            'TNDDate' => date('d-m-Y', strtotime($tndDate)),
            'transactionId' => $transactionId,
            
        ];
        // var_dump('Hashing payload', $hashingPayload);

        // Sort and hash the payload
        // ksort($hashingPayload);
        $calculatedHash = $this->createSignature($hashingPayload);
        // var_dump('calculated', $calculatedHash);

        var_dump('Calculated Hash: ' . $calculatedHash);
        var_dump('Received Signature: ' . $signature);

        // Compare the calculated hash with the provided signature
        if ($calculatedHash === $signature) {
            Log::info('Transaction verified successfully.');
            return response()->json([
                'message' => 'Transaction verified',
                'TransactionId' => $transactionId,
                'State' => $state,
                'TNDDate' => $tndDate
            ]);
        } else {
            Log::error('Transaction verification failed: data may be altered.');
            return response()->json(['message' => 'Transaction verification failed: data may be altered.'], 400);
        }
    }

     // Check transaction status via CBE API
     public function checkTransactionStatus(Request $request)
     {
         $transactionId = $request->input('transactionId');
         $tillcode = $request->input('tillcode');

         $payload = [
             'transactionId' => $transactionId,
             'Tillcode' => $tillcode,
             'Key' => $this->securityKey
         ];

         // Generate hash for the payload
         $hashValue = $this->createSignature($this->sortedMap($payload));

         $requestBody = [
             'transactionId' => $transactionId,
             'Tillcode' => $tillcode,
             'Signiture' => $hashValue
         ];

         try {
             // Send the request to CBE payment gateway
             $response = Http::withOptions(['verify' => false])
                 ->post('https://cbebirrpaymentgateway.cbe.com.et:8888/api/cbebpg/TXNSTAT', $requestBody);

             Log::info('Response Body:', ['body' => $response->body()]);

             if (!$this->isJson($response->body())) {
                 return response()->json(['message' => 'Unexpected non-JSON response', 'data' => $response->body()], 500);
             }

             $responseData = $response->json();

             if (!isset($responseData['EncryptedResponseValue'])) {
                 return response()->json(['message' => 'Missing EncryptedResponseValue in the response'], 500);
             }

             $encryptedResponseValue = $responseData['EncryptedResponseValue'];

             // Decrypt the response
             $decryptedResponse = json_decode($this->decrypt($encryptedResponseValue, $this->securityKey), true);

             $tillCode = $decryptedResponse['Tillcode'] ?? '';
             $transactionId = $decryptedResponse['TransactionId'] ?? '';
             $state = $decryptedResponse['State'] ?? '';
             $tndDate = $decryptedResponse['TNDDate'] ?? '';
             $signature = $decryptedResponse['Signiture'] ?? ''; // Check if it's "Signiture"

             // Verify the signature
             $verifyPayload = [
                 'Tillcode' => $tillCode,
                 'TransactionId' => $transactionId,
                 'State' => $state,
                 'TNDDate' => $tndDate,
                 'Key' => $this->securityKey
             ];

             $verifyHashValue = $this->createSignature($this->sortedMap($verifyPayload));
             Log::info('Calculated Hash: ' . $verifyHashValue);

             if ($verifyHashValue === $signature) {
                 return response()->json(['message' => 'Transaction status verified', 'TransactionId' => $transactionId, 'State' => $state, 'TNDDate' => $tndDate]);
             } else {
                 return response()->json(['message' => 'Transaction verification failed: data may be altered'], 400);
             }
         } catch (\Exception $e) {
             return response()->json(['message' => 'Error verifying transaction status', 'error' => $e->getMessage()], 500);
         }
     }

     // Check if a string is JSON
     private function isJson($string)
     {
         json_decode($string);
         return json_last_error() === JSON_ERROR_NONE;
     }
 }
