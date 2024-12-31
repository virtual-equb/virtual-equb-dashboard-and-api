<?php

namespace App\Http\Controllers\Api;

use Exception;
use App\Models\Equb;
use App\Models\AppToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Repositories\ActivityLog\IActivityLogRepository;
use App\Repositories\Equb\IEqubRepository;
use App\Repositories\EqubTaker\IEqubTakerRepository;
use App\Repositories\Member\IMemberRepository;
use App\Repositories\Payment\IPaymentRepository;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class CbeMiniAppController extends Controller
{
    private $activityLogRepository;
    private $paymentRepository;
    private $equbRepository;
    private $equbTakerRepository;
    private $memberRepository;
    public function __construct(
        IPaymentRepository $paymentRepository,
        IMemberRepository $memberRepository,
        IEqubRepository $equbRepository,
        IEqubTakerRepository $equbTakerRepository,
        IActivityLogRepository $activityLogRepository
    )
    {
        $this->activityLogRepository = $activityLogRepository;
        $this->paymentRepository = $paymentRepository;
        $this->memberRepository = $memberRepository;
        $this->equbRepository = $equbRepository;
        $this->equbTakerRepository = $equbTakerRepository;

    }
    public function index(){
        return view('cbe_payment');
    }
    public function validateToken(Request $request)
    {
        try {
            $token = $request->header('Authorization');
            // $token = "Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJwaG9uZSI6IjI1MTkxODA5NDQ1NSIsImV4cCI6MTczODYzMjEzMH0.cN95szHJNoJwp8tdtpDOk29vPmQeVoYP8dbKFBFy4_M";
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
                // $phone = $response->json('phone');
                $equb = Equb::with('equbType')->whereHas('member', function ($query) use ($Phone) {
                    $query->where('phone', $Phone);
                })->get();
                // dd($equb);
                if ($equb->count() === 0) {
                    // return response()->json(['error' => 'No equb found for the user'], 404);
                    return view('cbe_payment', [
                        'token' => $token, 
                        'phone' => $Phone, 
                        'equbs' => [], 
                        'error' => 'No equb found for the user'
                    ]);
                }
                return view('cbe_payment', [
                    'token' => $cleanedToken, 
                    'phone' => $Phone, 
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


    public function processPayment(Request $request)
    {
        try {
            // Step 2: Process the payment
            // Step 2.1: Preparing data to be sent
            $validated = $request->validate([
                'amount' => 'required|numeric',
                'equb_id' => 'required|exists:equbs,id',
                'token' => 'required|exists:app_tokens,token',
                'phone' => 'required|exists:app_tokens,phone',
            ]);
    
            $transactionId = uniqid(); // Generate unique transaction ID
            $transactionTime = now()->toIso8601String(); // Get current timestamp in ISO8601 format
            $callbackUrl = route('cbe.callback'); // Callback URL for response handling
            $companyName = env('CBE_MINI_COMPANY_NAME'); // Provided company name
            $hashingKey = env('CBE_MINI_HASHING_KEY'); // Provided hashing key
            $tillCode = env('CBE_MINI_TILL_CODE'); // Provided till code

            // Payment data
            $equb = Equb::with('equbType')->findOrFail($validated['equb_id']);
            $member = $equb->member->where('phone', $validated['phone'])->first();
            // dd($callbackUrl);
            // Prepare payload for hashing (including 'key')
            $payloadForHashing = [
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
            ksort($payloadForHashing); // Sort payload by keys

            $processedPayload = urldecode(http_build_query($payloadForHashing)); // Convert sorted payload to query string
    
            // Step 2.3.3: Hash the processed payload
            // $signature = hash_hmac('sha256', $processedPayload, $hashingKey);
            $signature = hash('sha256', $processedPayload);
            // dd($signature);
            // Prepare final payload (excluding 'key')
            $payload = [
                "amount" => $validated['amount'],
                "callBackURL" => $callbackUrl,
                "companyName" => $companyName,
                "signature" => $signature, // Add the signature
                "tillCode" => $tillCode,
                "token" => $validated['token'],
                "transactionId" => $transactionId,
                "transactionTime" => $transactionTime,
            ];
    
            // Ensure payload is sorted according to the desired order
            $orderedKeys = [
                "amount",
                "callBackURL",
                "companyName",
                "signature", // Place "signature" before "key"
                "tillCode",
                "token",
                "transactionId",
                "transactionTime",
            ];
            
            $sortedPayload = array_merge(array_flip($orderedKeys), $payload);
            // ksort($sortedPayload);
            // $finalPayload = http_build_query($sortedPayload);
            // Step 2.5: Sending the final payload
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => "Bearer " . $validated['token'],
            ])->post('https://cbebirrpaymentgateway.cbe.com.et:8888/auth/pay', $sortedPayload);
                
            // Check the response status
            if ($response->status() === 200) {

                Payment::create([
                    'member_id' => $member->id,
                    'equb_id' => $equb->id,
                    'transaction_number' => $transactionId,
                    'amount' => $validated['amount'],
                    'status' => 'pending',
                    'payment_type' => 'CBE Mini App',
                    'collecter' => $member->id,
                    'signature' => $signature,
                ]);

                return response()->json(['status' => 'success', 'token' => $response->json('token'), 'signature' => $signature], 200);
            } else {
                Log::error('CBE API Error:', ['response' => $response->json()]);
                return response()->json(['status' => 'error', 'message' => 'Transaction failed'], $response->status());
            }
        } catch (\Exception $ex) {
            return response()->json(['status' => 'error', 'message' => $ex->getMessage()], 500);
        }
    }
    

    public function paymentCallback(Request $request)
    {
        try {
            // return 123;
            $token = $request->header('Authorization');
            // $token = "Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJwaG9uZSI6IjI1MTkxODA5NDQ1NSIsImV4cCI6MTczODYzMjEzMH0.cN95szHJNoJwp8tdtpDOk29vPmQeVoYP8dbKFBFy4_M";
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
            $receivedSignature = $data['signature'] ?? null;
            unset($data['signature']);

            $hashingKey = env('CBE_HASHING_KEY');
            $data['key'] = $hashingKey;

            ksort($data);

            $processedPayload = urldecode(http_build_query($data));
            $calculatedSignature = hash_hmac('sha256', $processedPayload, $hashingKey);
            // $calculatedSignature = hash('sha256', $processedPayload);

            if ($calculatedSignature !== $receivedSignature) {
                return response()->json(['error' => 'Invalid Signature'], 400);
            }
            // if (!$signature) {
            //     return response()->json(['error' => 'Invalid Signature'], 400);
            // }
            $payment = Payment::where('transaction_number', $data['transactionId'])->first();
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
                            $availableBalance -= $totalCredit;
                            $totalCredit = 0;
                        } elseif ($at > $equbAmount) {
                            $diff = $at - $equbAmount;
                            $totalCredit -= $diff;
                            $availableBalance = ($availableBalance + $diff) - $totalCredit;
                            $totalCredit = 0;
                        }
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
                } else {
                    $totalCredit = $totalCredit;
                    $availableBalance = 0;
                }
                $amount = $at;
            }
            // Update the payment record with the CBE details
            $payment->update([
                'transaction_number' => $data['transactionId'],
                'status' => 'paid',
                'paid_date' => now(),
                'amount' => $amount,
                'creadit' => $totalCredit,
                'balance' => $availableBalance,
                'payment_type' => 'CBE Gateway',
                'collecter' => $memberId,
                'signature' => $data['signature'],
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
                'user_id' => $payment->member_id,
                'username' => $payment->member->name,
                'role' => $payment->member->role,
            ];
            $this->activityLogRepository->createActivityLog($activityLog);
            Log::info('Transaction verified successfully.');
           

            // Process the transaction
            return response()->json(['status' => 'success'], 200);

        } catch (Exception $ex) {
            return response()->json([
                'error' => $ex->getMessage()
            ], 500);
        }
    }
}
