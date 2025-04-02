<?php

namespace App\Http\Controllers\Api;

use App\Models\Payment;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\CBETransaction;
use App\Models\TempTransaction;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use App\Repositories\Equb\IEqubRepository;
use App\Repositories\Member\IMemberRepository;
use App\Repositories\Payment\IPaymentRepository;
use App\Repositories\EqubTaker\IEqubTakerRepository;
use App\Repositories\ActivityLog\IActivityLogRepository;
use Exception;

class PaymentGatewayController extends Controller {
        private $activityLogRepository;
        private $paymentRepository;
        private $memberRepository;
        private $equbRepository;
        private $equbTakerRepository;
        public function __construct(
            IPaymentRepository $paymentRepository,
            IMemberRepository $memberRepository,
            IEqubRepository $equbRepository,
            IEqubTakerRepository $equbTakerRepository,
            IActivityLogRepository $activityLogRepository
        )
        {
            // $this->middleware('auth:api');
            $this->activityLogRepository = $activityLogRepository;
            $this->paymentRepository = $paymentRepository;
            $this->memberRepository = $memberRepository;
            $this->equbRepository = $equbRepository;
            $this->equbTakerRepository = $equbTakerRepository;
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

        private $storedAmount;
        private $memberId;
        private $equbId;
        private $balance;
        private $paymentType;
        private $localTransactionId;
        //

        public function generateUrl(Request $request)
        {
            try {

                $request->validate([
                    'amount' => 'required|numeric',
                    'member_id' => 'required|exists:members,id',
                    'equb_id' => 'required|exists:equbs,id',
                ]);

                // Call encryptData
                $encryptedUrl = $this->encryptData();

                // Store the amount in the class property for access by `encryptData`
                $this->storedAmount = $request->input('amount');
                $this->memberId = $request->input('member_id');
                $this->equbId = $request->input('equb_id');
                $localTransactionId = Str::uuid();
                $this->localTransactionId = $localTransactionId;

                $equb_status = $this->equbRepository->getStatusById($this->equbId);

                if ($equb_status->status != 'Active') {
                    return response()->json([
                        'code' => 500,
                        'message' => 'Payment processing failed: The Equb is currently not in active status.',
                    ]);
                }

                $totalEqubAmountToPay = $this->equbRepository->getTotalEqubAmount($this->equbId);
                $totalPaidAmount = $this->paymentRepository->getTotalPaid($this->equbId);
                $remainingAmountToPay = $totalEqubAmountToPay - $totalPaidAmount;

                if ($this->storedAmount > $remainingAmountToPay) {
                    return response()->json([
                        'code' => 500,
                        'message' => 'Payment processing failed: You cannot pay more than the required total amount for this Equb.',
                    ]);
                }

                Payment::create([
                    'member_id' => $request->input('member_id'),
                    'equb_id' => $request->input('equb_id'),
                    'transaction_number' => $this->localTransactionId,
                    'amount' => $this->storedAmount,
                    'status' => 'pending',
                    'payment_type' => 'CBE Gateway',
                    'collecter' => $request->input('member_id')
                ]);
                
                // Call the `encryptData` function and get the URL
                return $this->encryptData();

            } catch (Exception $ex) {
                return response()->json([
                    'error' => $ex->getMessage()
                ], 500);
            }
            
        }

        public function regenerateUrl($id) {

            try {
                if (!Payment::where('id', $id)->exists()) {
                    return response()->json(['error' => 'Invalid payment ID'], 404);
                }
                $payment = Payment::findOrFail($id);
                $this->storedAmount = $payment->amount;
                $this->localTransactionId = $payment->transaction_number;
    
                return $this->encryptData();
                
            } catch (Exception $ex) {
                return response()->json([
                    'error' => $ex->getMessage()
                ], 500);
            }
            
        }

        public function cancelPayment($id) {
            try {
                if (!Payment::where('id', $id)->exists()) {
                    return response()->json(['error' => 'Invalid payment ID'], 404);
                }
                $payment = Payment::findOrFail($id);

                $payment->delete();

                return response()->json([
                    'message' => 'Payment deleted successfully',
                ], 200);

            } catch (Exception $ex) {
                return response()->json([
                    'error' => $ex->getMessage()
                ], 500);
            }
        }

        // Encrypt and send payload
        public function encryptData()
        {
            try {

                $amount = $this->storedAmount;
                $transactionId = $this->localTransactionId;
                // $member = $this->memberId;
                // dd($this->memberId);
                $payload = [
                    "U" => "VEKUB",
                    "W" => "782290",
                    // "T" => "1122_t_med_lab22",
                    "T" => $transactionId,
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

            } catch (Exception $ex) {
                return response()->json([
                    'error' => $ex->getMessage()
                ], 500);
            }
            
        }

        public function testJWT()
        {
            return response()->json([
                'JWT_SECRET' => config('key.JWT_SECRET'),
                'APP_ENV' => config('key.APP_ENV'),
                'APP_DEBUG' => config('key.APP_DEBUG'),
            ]);
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

                $payment = Payment::where('transaction_number', $transactionId)->latest()->first();

                if (!$payment) {
                    return response()->json(['message' => 'Payment record not found'], 404);
                }
                // payment calculations
                $equbId = $payment->equb_id;
                $memberId = $payment->member_id;
                $amount = $payment->amount;
                $credit = $payment->creadit;

                // Compute total credit and balance
                $totalCredit = $this->paymentRepository->getTotalCredit($equbId) ?? 0;
                $equbAmount = $this->equbRepository->getEqubAmount($memberId, $equbId);
                $availableBalance = $this->paymentRepository->getTotalBalance($equbId) ?? 0;

                $creditData = ['creadit' => 0];
                $this->paymentRepository->updateCredit($equbId, $creditData);

                $lastTc = $totalCredit;
                $totalCredit += $credit;

                $balanceData = ['balance' => 0];
                $this->paymentRepository->updateBalance($equbId, $balanceData);

                $at = $amount;
                $amount += $availableBalance;

                if ($amount > $equbAmount) {
                    if ($totalCredit > 0) {
                        if ($totalCredit < $amount) {
                            if ($at < $equbAmount) {
                                $availableBalance = $availableBalance - $totalCredit;
                                $totalCredit = 0;
                            } elseif ($at > $equbAmount) {
                                $diff = $at - $equbAmount;
                                //RECENT CODE FIX - REGARDING CREDIT AND BALANCE CALCULATION
                                $totalCredit = max (0, $lastTc - $diff);
                                $availableBalance = max(0, $availableBalance + $diff - $lastTc);
                            } elseif ($at = $equbAmount) {
                                $availableBalance = $availableBalance;
                            }
                            $amount = $at;
                        } else {
                            $amount = $at;
                            $totalCredit = $totalCredit;
                        }
                    } else {
                        $totalCredit = $totalCredit;
                        if ($at < $equbAmount) {
                            $availableBalance = $availableBalance - $totalCredit;
                        } elseif ($at > $equbAmount) {
                            $diff = $at - $equbAmount;
                            $totalCredit = $totalCredit - $diff;
                            $availableBalance = $availableBalance + $diff;
                            $totalCredit = 0;
                        } elseif ($at = $equbAmount) {
                            $availableBalance = $availableBalance;
                        }
                        $amount = $at;
                    }
                } elseif ($amount == $equbAmount) {
                    $amount = $at;
                    $totalCredit = $lastTc;
                    $availableBalance = 0;
                } elseif ($amount < $equbAmount) {
                    if ($lastTc == 0) {
                        $totalCredit = $equbAmount - $amount;
                        $availableBalance = 0;
                        $amount = $at;
                    } else {
                        $totalCredit = $totalCredit;
                        $availableBalance = 0;
                        $amount = $at;
                    }
                }

                $status = '';
                $paidDate = null;

                switch ($state) {
                    case 'COM':
                    case 'OTOUPD':
                        $status = 'paid';
                        $paidDate = now();
                        break;
                    case 'TNXFIL':
                        $status = 'failed';
                        break;
                    case 'DRAFT':
                        $status = 'draft';
                        break;
                    default:
                        $status = 'unknown';
                        break;
                }
                $collecter = User::where('name', 'Cbe Gateway')->first();
                // Update the payment record with the CBE details
                $payment->update([
                    'transaction_number' => $transactionId,
                    'status' => $status,
                    'paid_date' => $paidDate,
                    'amount' => $amount,
                    'creadit' => $totalCredit,
                    'balance' => $availableBalance,
                    'payment_type' => 'CBE Gateway',
                    'collecter' => $collecter->id
                ]);
                // Update equb total payment and remaining payment
                $totalPaid = $this->paymentRepository->getTotalPaid($equbId);
                $totalEqubAmount = $this->equbRepository->getTotalEqubAmount($equbId);
                $remainingPayment = $totalEqubAmount - $totalPaid;

                $updated = [
                    'total_payment' => $totalPaid,
                    'remaining_payment' => $remainingPayment,
                ];
                $this->equbTakerRepository->updatePayment($equbId, $updated);

                // Mark equb as deactivated if fully paid
                if ($remainingPayment == 0) {
                    $this->equbRepository->update($equbId, ['status' => 'Deactive']);
                }

                // Log the activity
                $activityLog = [
                    'type' => 'payments',
                    'type_id' => $payment->id,
                    'action' => 'updated',
                    'user_id' => $collecter->id,
                    'username' => $collecter->name,
                    'role' => $collecter->getRoleNames()->first(),
                ];
                $this->activityLogRepository->createActivityLog($activityLog);
                Log::info('Transaction verified successfully.');
                return response()->json([
                    'message' => 'Transaction verified',
                    'TransactionId' => $transactionId,
                    'State' => $state,
                    'TNDDate' => $tndDate,
                    // 'payment' => $payment
                ]);
            } else {
                throw new Exception('Transaction verification failed: data may be altered.');
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
