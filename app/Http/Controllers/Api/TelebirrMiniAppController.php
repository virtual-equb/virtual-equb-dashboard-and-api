<?php

namespace App\Http\Controllers\api;

use Exception;
use Carbon\Carbon;
use App\Models\Equb;
use App\Models\User;
use App\Models\Member;
use App\Models\Payment;
use App\Models\EqubType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Services\CreateOrderService;
use Illuminate\Support\Facades\Auth;
use App\Repositories\Equb\IEqubRepository;
use App\Repositories\Member\IMemberRepository;
use App\Repositories\Payment\IPaymentRepository;
use App\Repositories\EqubTaker\IEqubTakerRepository;
use App\Repositories\ActivityLog\IActivityLogRepository;
use App\Events\TelebirrPaymentStatusUpdated;

/**
 * @group Payments
 */
define('TELEBIRR_APP_ID', config('key.TELEBIRR_APP_ID'));
define('TELEBIRR_RECEIVER_NAME', config("key.TELEBIRR_RECEIVER_NAME"));
define('TELEBIRR_SHORT_CODE', config('key.TELEBIRR_SHORT_CODE'));
define('TELEBIRR_SUBJECT', config('key.TELEBIRR_SUBJECT'));
define('TELEBIRR_RETURN_URL', config('key.TELEBIRR_RETURN_URL'));
define('TELEBIRR_NOTIFY_URL', config('key.TELEBIRR_NOTIFY_URL'));
define('TELEBIRR_TIMEOUT_EXPRESS', config('key.TELEBIRR_TIMEOUT_EXPRESS'));
define('TELEBIRR_APP_KEY', config('key.TELEBIRR_APP_KEY'));
define('TELEBIRR_PUBLIC_KEY', config('key.TELEBIRR_PUBLIC_KEY'));
define('TELEBIRR_PUBLIC_KEY_C', config('key.TELEBIRR_PUBLIC_KEY_C'));
define('TELEBIRR_INAPP_PAYMENT_URL', config('key.TELEBIRR_INAPP_PAYMENT_URL'));
define('TELEBIRR_H5_URL', config('key.TELEBIRR_H5_URL'));
define('TELEBIRR_BASE_URL', config('key.TELEBIRR_BASE_URL'));
define('TELEBIRR_FABRIC_APP_ID', config('key.TELEBIRR_FABRIC_APP_ID'));
define('TELEBIRR_APP_SECRET', config('key.TELEBIRR_APP_SECRET'));
define('TELEBIRR_MERCHANT_APP_ID', config('key.TELEBIRR_MERCHANT_APP_ID'));
define('TELEBIRR_MERCHANT_CODE', config('key.TELEBIRR_MERCHANT_CODE'));
define('TELEBIRR_TITLE', config('key.TELEBIRR_TITLE'));
define('PRIVATE_KEY', config('key.PRIVATE_KEY'));

class TelebirrMiniAppController extends Controller
{
    private $activityLogRepository;
    private $paymentRepository;
    private $memberRepository;
    private $equbRepository;
    private $equbTakerRepository;
    private $title;
    public function __construct(
        IPaymentRepository $paymentRepository,
        IMemberRepository $memberRepository,
        IEqubRepository $equbRepository,
        IEqubTakerRepository $equbTakerRepository,
        IActivityLogRepository $activityLogRepository
    ) {
        $this->middleware('auth:api')->except('getPaymentsByReference', 'callback');
        $this->activityLogRepository = $activityLogRepository;
        $this->paymentRepository = $paymentRepository;
        $this->memberRepository = $memberRepository;
        $this->equbRepository = $equbRepository;
        $this->equbTakerRepository = $equbTakerRepository;
        $this->title = "Virtual Equb - Payment";
    }

    public function initialize(Request $request)
    {
        try {
            $request->validate([
                'amount' => 'required|numeric',
                'member_id' => 'required|exists:members,id',
                'equb_id' => 'required|exists:equbs,id',
            ]);

            $req = $request->all();
            $user = Auth::user();

            $equbId = $request->input('equb_id');
            $amount = $request->input('amount');
            $equb_id = $equbId;
            $paymentType = "Telebirr MiniApp";
            $memberData = Member::where('phone', $user->phone_number)->first();
            $collector = User::where('name', 'telebirr')->first();

            // Check if a payment was made by this member for this equb in the last 24 hours
            $existingPayment = Payment::where('member_id', $memberData->id)
                ->where('equb_id', $equbId)
                ->where('status', 'paid')
                ->where('created_at', '>=', now()->subDay())
                ->exists();

            if ($existingPayment) {
                return response()->json([
                    'code' => 400,
                    'message' => 'You can only make one payment transaction per 24 hours for this Equb.'
                ], 400);
            }
            
            // Check if equb status is active and able to process payment
            $equb_status = $this->equbRepository->getStatusById($equbId);

            if ($equb_status->status != 'Active') {
                return response()->json([
                    'code' => 500,
                    'message' => 'Payment processing failed: The Equb is currently not in active status.',
                ]);
            }

            // Check if entered amount is more that the total amount to be paid and restrict user from paying more than the required amount
            $totalEqubAmountToPay = $this->equbRepository->getTotalEqubAmount($equb_id);
            $totalPaidAmount = $this->paymentRepository->getTotalPaid($equb_id);
            $remainingAmountToPay = $totalEqubAmountToPay - $totalPaidAmount;

            if ($amount > $remainingAmountToPay) {
                return response()->json([
                    'code' => 500,
                    'message' => 'Payment processing failed: You cannot pay more than the required total amount for this Equb.',
                ]);
            }
        
            $paymentData = [
                'member_id' => $memberData->id,
                'equb_id' => $equb_id,
                'payment_type' => $paymentType,
                'amount' => $amount,
                'collecter' => $collector->id,
                'status' => 'pending'
            ];

            $telebirr = $this->paymentRepository->create($paymentData);

            // Telebirr initialization 
            if ($telebirr) {
                $baseUrl = TELEBIRR_BASE_URL;
                $fabricAppId = TELEBIRR_FABRIC_APP_ID;
                $appSecret = TELEBIRR_APP_SECRET;
                $merchantAppId = TELEBIRR_MERCHANT_APP_ID;
                $merchantCode = TELEBIRR_MERCHANT_CODE;

                // You can also get the request parameters directly from the request object
                $req = $request->all();

                // Create an instance of CreateOrderService
                $createOrderService = new CreateOrderService(
                    $baseUrl,
                    (object) $req, // This casts the array to an object
                    $fabricAppId,
                    $appSecret,
                    $merchantAppId,
                    $merchantCode,
                    $telebirr->id // Cast to string if necessary
                );

                $result = $createOrderService->createOrder();

                // Parse URL-encoded string response into an associative array
                parse_str($result, $parsedResult);
                // Remove unwanted keys
                unset($parsedResult['sign_type'], $parsedResult['sign'], $parsedResult['nonce_str']);
                $parsedResult["paymentId"] = $telebirr->id;
                // Return the filtered array as JSON
                return response()->json($parsedResult);
            } else {
                return response()->json([
                    'code' => 400,
                    'message' => 'Unknown error occurred, Please try again!'
                ], 400);
            }
        } catch (Exception $error) {

            // Log::error('Error creating CreateOrderService: ' . $error->getMessage());
            return response()->json([
                'code' => 500,
                'message' => 'Failed to create order service',
                'error' => $error->getMessage(),
            ]);
        }
    }
    
    public function callback(Request $request)
    {
        try {
            Log::info('callback request data', $request->all());

            if ($request) {
                $merch_order_id = $request['merch_order_id'];
                $payment = Payment::find($merch_order_id);  // Find the record by ID
               
                if ($request['trade_status'] == 'Completed') {
                    
                    $member = $payment->member_id;
                    $equb_id = $payment->equb_id;
                    $amount = $payment->amount;
                    $paymentType = $payment->payment_type;

                    $equbAmount = $this->equbRepository->getEqubAmount($member, $equb_id);
                    $totalCredit = $this->paymentRepository->getTotalCreditAPI($equb_id) ?? 0;
                    $availableBalance = $this->paymentRepository->getTotalBalanceAPI($equb_id) ?? 0;
                    $credit = $equbAmount-$amount;

                    if ($credit <= 0) {
                        $credit = 0;
                    }
                    
                    
                    if ($totalCredit == null) {
                        $totalCredit = 0;
                    }
                    $creditData = [
                        'creadit' => 0
                    ];
                    $this->paymentRepository->updateCredit($equb_id, $creditData);
                    
                    $lastTc = $totalCredit;
                    $totalCredit = $credit + $totalCredit;
                    $tc = $totalCredit;
                
                    
                    if ($availableBalance == null) {
                        $availableBalance = 0;
                    }
                    $balanceData = [
                        'balance' => 0
                    ];
                    $this->paymentRepository->updateBalance($equb_id, $balanceData);
                    
                    $at = $amount;
                    $amount = $availableBalance + $amount;
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

                    $memberData = Member::where('id', $member)->first();
                    $collector = User::where('name', 'telebirr')->first();
                    $tradeDt = $request['notify_time'];

                    // Convert milliseconds to seconds (PHP expects seconds)
                    $seconds = $tradeDt / 1000;

                    // Create a Carbon instance from the timestamp
                    $date = Carbon::createFromTimestamp($seconds);

                    // Format the date as desired
                    $readableDate = $date->format('Y-m-d H:i:s');
                    $telebirrObj = [
                        'amount' => $request['total_amount'],
                        'tradeDate' => $readableDate,
                        'tradeNo' => $request['payment_order_id'],
                        'tradeStatus' => $request['trade_status'],
                        'transaction_number' => $request['payment_order_id'],
                        'status' => 'paid'
                    ];
                    $collector = User::where('name', 'telebirr')->first();
                    $payment->amount = $telebirrObj['amount'];
                    $payment->tradeDate = $telebirrObj['tradeDate'];
                    $payment->tradeNo = $telebirrObj['tradeNo'];
                    $payment->tradeStatus = $telebirrObj['tradeStatus'];
                    $payment->transaction_number = $telebirrObj['transaction_number'];
                    $payment->status = $telebirrObj['status'];
                    $payment->collecter = $collector->id;
                    $payment->creadit = $totalCredit;
                    $payment->balance = $availableBalance;
                    $payment->payment_type = $paymentType;
                    $payment->save();

                    // Log::info($telebirrObj);
                    // $payment->save($telebirrObj);
                    // Log::info($payment);
                    $equb_id = $payment->equb_id;

                    $totalPpayment = $this->paymentRepository->getTotalPaid($equb_id);
                    $totalEqubAmount = $this->equbRepository->getTotalEqubAmount($equb_id);
                    $remainingPayment = max(0, $totalEqubAmount - $totalPpayment);

                    $updated = [
                        'total_payment' => $totalPpayment,
                        'remaining_payment' => $remainingPayment,
                        'remaining_amount' => $remainingPayment,
                        'status' => $remainingPayment == 0 ? 'paid' : 'partially_paid',
                    ];

                    $updated = $this->equbTakerRepository->updatePayment($equb_id, $updated);
                    $equbTaker = $this->equbTakerRepository->getByEqubId($equb_id);

                    if ($remainingPayment == 0 && $equbTaker) {
                        $ekubStatus = [
                            'status' => 'Deactive'
                        ];
                        $ekubStatusUpdate = $this->equbRepository->update($equb_id, $ekubStatus);
                    }

                    try {
                        event(new TelebirrPaymentStatusUpdated($payment));
                    } catch (\Exception $e) {
                        // Log the error, but don't block the transaction
                        Log::error('Error broadcasting TelebirrPaymentStatusUpdated event: ' . $e->getMessage());
                    }

                    return response()->json([
                        'code' => 200,
                        'message' => 'You have succesfully paid!'
                    ], 200);
                } else {

                    return response()->json([
                        'code' => 400,
                        'message' => 'Payment failed, Please try again!'
                    ], 400);
                }
            }
            // if ($request) {
            //     $merch_order_id = $request['merch_order_id'];
            //     $payment = Payment::find($merch_order_id);  // Find the record by ID
               
            //     if ($request['trade_status'] == 'Completed') {
                    
            //         $member = $payment->member_id;
            //         $equb_id = $payment->equb_id;
            //         $amount = $payment->amount;
            //         $paymentType = $payment->payment_type;
            
            //         $equbAmount = $this->equbRepository->getEqubAmount($member, $equb_id);
            //         $totalCredit = $this->paymentRepository->getTotalCreditAPI($equb_id) ?? 0;
            //         $availableBalance = $this->paymentRepository->getTotalBalanceAPI($equb_id) ?? 0;
                    
            //         // Calculate new credit and balance
            //         $credit = max(0, $equbAmount - $amount);
            //         $totalCredit = max(0, $totalCredit + $credit);
                    
            //         if ($amount >= $equbAmount) {
            //             $availableBalance += ($amount - $equbAmount);
            //             $totalCredit = 0;
            //         } else {
            //             $availableBalance = max(0, $availableBalance - $totalCredit);
            //         }
                    
            //         // Update payment records
            //         $this->paymentRepository->updateCredit($equb_id, ['creadit' => $totalCredit]);
            //         $this->paymentRepository->updateBalance($equb_id, ['balance' => $availableBalance]);
                    
            //         // Convert timestamp to readable date
            //         $tradeDt = $request['notify_time'];
            //         $readableDate = Carbon::createFromTimestamp($tradeDt / 1000)->format('Y-m-d H:i:s');
                    
            //         // Prepare payment update
            //         $telebirrObj = [
            //             'amount' => $request['total_amount'],
            //             'tradeDate' => $readableDate,
            //             'tradeNo' => $request['payment_order_id'],
            //             'tradeStatus' => $request['trade_status'],
            //             'transaction_number' => $request['payment_order_id'],
            //             'status' => 'paid',
            //             'collecter' => User::where('name', 'telebirr')->first()->id,
            //             'creadit' => $totalCredit,
            //             'balance' => $availableBalance,
            //             'payment_type' => $paymentType
            //         ];
                    
            //         $payment->update($telebirrObj);
                    
            //         // Update Equb Payment Status
            //         $totalPpayment = $this->paymentRepository->getTotalPaid($equb_id);
            //         $totalEqubAmount = $this->equbRepository->getTotalEqubAmount($equb_id);
            //         $remainingPayment = max(0, $totalEqubAmount - $totalPpayment);
                    
            //         $this->equbTakerRepository->updatePayment($equb_id, [
            //             'total_payment' => $totalPpayment,
            //             'remaining_payment' => $remainingPayment,
            //             'remaining_amount' => $remainingPayment,
            //             'status' => $remainingPayment == 0 ? 'paid' : 'partially_paid',
            //         ]);
                    
            //         if ($remainingPayment == 0) {
            //             $this->equbRepository->update($equb_id, ['status' => 'Deactive']);
            //         }
                    
            //         // Fire event
            //         try {
            //             Log::info('Payment status update event fired');
            //             event(new TelebirrPaymentStatusUpdated($payment));
            //         } catch (\Exception $e) {
            //             Log::error('Error broadcasting event: ' . $e->getMessage());
            //         }
                    
            //         return response()->json([
            //             'code' => 200,
            //             'message' => 'You have successfully paid!'
            //         ], 200);
            //     } else {
            //         return response()->json([
            //             'code' => 400,
            //             'message' => 'Payment failed, Please try again!'
            //         ], 400);
            //     }
            // }
            
        } catch (Exception $error) {
            return response()->json([
                'code' => 500,
                'message' => 'Unable to process your request, Please try again!',
                "error" => $error
            ]);
        }
    }

    public  function decrypt_RSA($publicPEMKey, $data)
    {
        $pkey_public = openssl_pkey_get_public(TELEBIRR_PUBLIC_KEY);
        $DECRYPT_BLOCK_SIZE = 256;
        $decrypted = '';
        $data = str_split(base64_decode($data), $DECRYPT_BLOCK_SIZE);
        foreach ($data as $chunk) {
            $partial = '';
            $decryptionOK = openssl_public_decrypt($chunk, $partial, $pkey_public, OPENSSL_PKCS1_PADDING);
            if ($decryptionOK === false) {
                return false;
            }
            $decrypted .= $partial;
        }
        return $decrypted;
    }
    
    public function getTransaction($id)
    {
        try {
            $payment = $this->paymentRepository->getById($id);

            return response()->json([
                'code' => 200,
                'transaction' => $payment
            ]);
        } catch (Exception $error) {
            return response()->json([
                'code' => 500,
                'message' => 'Unable to process your request, Please try again!',
                "error" => $error
            ]);
        }
    }
}