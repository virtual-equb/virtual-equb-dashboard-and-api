<?php

namespace App\Http\Controllers\api;

use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Equb;
use App\Models\Member;
use App\Models\User;
use App\Repositories\Equb\IEqubRepository;
use App\Repositories\EqubTaker\IEqubTakerRepository;
use App\Repositories\Payment\IPaymentRepository;
use Illuminate\Support\Facades\Http;

class ChapaController extends Controller
{
    protected $publicKey;
    protected $secretKey;
    protected $baseUrl;
    private $paymentRepository;
    private $equbRepository;
    private $equbTakerRepository;
    private $returnUrl;
    public function __construct(IPaymentRepository $paymentRepository, IEqubRepository $equbRepository, IEqubTakerRepository $equbTakerRepository,)
    {
        // $this->middleware('auth');
        $this->publicKey = env('CHAPA_PUBLIC_KEY');
        $this->secretKey = env('CHAPA_SECRET_KEY');
        $this->returnUrl = env('BASE_URL');
        $this->baseUrl = 'https://api.chapa.co/v1';
        $this->paymentRepository = $paymentRepository;
        $this->equbRepository = $equbRepository;
        $this->equbTakerRepository = $equbTakerRepository;
    }
    public function initialize(Request $request)
    {
        try {
            $userId = $request->input('user_id');
            $equbId = $request->input('equb_id');
            $amount = $request->input('amount');
            //This generates a payment reference
            $reference = $this->generateReference();
            $userData = Member::where('id', $userId)->first();
            $userEmail = User::where('phone_number', $userData->phone)->first();
            $userName = explode(' ', $userData->full_name);
            $callbackurl = 'api/chapa/callback/' . $userId . '/' . $equbId . '/' . $amount . '/' . $reference;

            // Enter the details of the payment
            $data = [
                'amount' => $amount,
                'email' => $userEmail->email,
                'tx_ref' => $reference,
                'currency' => "ETB",
                // 'callback_url' => url($callbackurl),
                'return_url' => url($callbackurl),
                // 'return_url' => "localhost:8000",
                'first_name' => $userName[0],
                'last_name' => $userName[1],
                "customization" => [
                    "title" => 'Equb Payment'
                ]
            ];
            $payment = $this->initializePayment($data);
            if ($payment['status'] == 'success') {
                return response()->json([
                    'code' => 200,
                    'message' => 'Payment succesful',
                    'data' => $payment['data']['checkout_url'],
                    'referenceNumber' => $reference
                ]);
            } else {
                return response()->json([
                    'code' => 400,
                    'message' => 'Something went wrong, Please Try again.',
                    'data' => $payment['message']
                ]);
            }
        } catch (Exception $ex) {
            // dd($ex);
            return response()->json([
                'code' => 400,
                'message' => 'Unknown error occured.'
            ]);
        }
    }
    public static function generateReference(String $transactionPrefix = NULL)
    {
        if ($transactionPrefix) {
            return $transactionPrefix . '_' . uniqid(time());
        }
        return 'chapa_' . uniqid(time());
    }
    public function initializePayment(array $data)
    {
        $payment = Http::withToken($this->secretKey)->post(
            $this->baseUrl . '/transaction/initialize',
            $data
        )->json();

        return $payment;
    }
    public function callback($userId, $equbId, $amount, $reference)
    {
        try {
            $equb = Equb::where('id', $equbId)->first();
            $equb_amount = $equb->amount;
            $credit = $equb_amount - $amount;
            $member = $userId;
            $equb_id = $equbId;
            $paymentType = "Chapa";
            if ($credit <= 0) {
                $credit = 0;
            }
            $totalCredit = $this->paymentRepository->getTotalCredit($equb_id);
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
            $equbAmount = $this->equbRepository->getEqubAmount($member, $equb_id);
            $availableBalance = $this->paymentRepository->getTotalBalance($equb_id);
            $balanceData = [
                'balance' => 0
            ];
            $this->paymentRepository->updateBalance($equb_id, $balanceData);
            if ($availableBalance == null) {
                $availableBalance = 0;
            }
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
                            $totalCredit = $totalCredit - $diff;
                            $availableBalance = $availableBalance + $diff - $tc;
                            $totalCredit = 0;
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
            $memberData = Member::where('id', $userId)->first();
            $user = User::where('phone_number', $memberData->phone)->first();
            $paymentData = [
                'member_id' => $member,
                'equb_id' => $equb_id,
                'payment_type' => $paymentType,
                'amount' => $amount,
                'creadit' => $totalCredit,
                'balance' => $availableBalance,
                'collecter' => $user->id,
                'transaction_number'=>$reference
            ];
            $create = $this->paymentRepository->create($paymentData);
            if ($create) {
                $totalPpayment = $this->paymentRepository->getTotalPaid($equb_id);
                $totalEqubAmount = $this->equbRepository->getTotalEqubAmount($equb_id);
                $remainingPayment =  $totalEqubAmount - $totalPpayment;
                $updated = [
                    'total_payment' => $totalPpayment,
                    'remaining_payment' => $remainingPayment,
                ];
                $updated = $this->equbTakerRepository->updatePayment($equb_id, $updated);
                $equbTaker = $this->equbTakerRepository->getByEqubId($equb_id);

                if ($remainingPayment == 0 && $equbTaker) {
                    $ekubStatus = [
                        'status' => 'Deactive'
                    ];
                    $ekubStatusUpdate = $this->equbRepository->update($equb_id, $ekubStatus);
                }
                return response()->json([
                    'code' => 200,
                    'message' => 'You have succesfully paid!'
                ]);
            } else {
                return response()->json([
                    'code' => 400,
                    'message' => 'Failed to create payment, Please try again!'
                ]);
            }
        } catch (Exception $ex) {
            return response()->json([
                'code' => 400,
                'message' => 'Unknown Error Occurred, Please try again!'
            ]);
        }
    }
}
