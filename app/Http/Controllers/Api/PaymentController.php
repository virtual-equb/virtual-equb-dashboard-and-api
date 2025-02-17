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


class PaymentController extends Controller
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

        // Permission Guard
        // $this->middleware('api_permission_check:update payment', ['only' => ['update', 'edit', 'updatePayment']]);
        // $this->middleware('api_permission_check:delete payment', ['only' => ['destroy', 'deleteAllPayment', 'deletePayment']]);
        // $this->middleware('api_permission_check:view payment', ['only' => ['index', 'show']]);
        // $this->middleware('api_permission_check:create payment', ['only' => ['store', 'storeForAdmin', 'create', 'initialize']]);
    }
    /**
     * Get all payments of members
     *
     * This api returns all payments members.
     *
     * @param member_id int required The id of the member. Example: 1
     * @param equb_id int required The id of the equb. Example: 1
     *
     * @return JsonResponse
     */
    public function index($member_id, $equb_id)
    {
        try {
            $offset = 0;
            $limit = 50;
            $pageNumber = 1;
            $userData = Auth::user();
            $adminRoles = ['admin', 'general_manager', 'operation_manager', 'it'];
            $memberRoles = ['member', 'equb_collector'];
            if ($userData && $userData->hasAnyRole($adminRoles)) {
                $paymentData['member'] = $this->memberRepository->getMemberWithPayment($member_id);
                $paymentData['totalCredit'] = $this->paymentRepository->getTotalCredit($equb_id);
                $paymentData['totalPaid'] = $this->paymentRepository->getTotalPaid($equb_id);
                $paymentData['total'] = $this->paymentRepository->getTotalCount($equb_id);
                $paymentData['offset'] = $offset;
                $paymentData['limit'] = $limit;
                $paymentData['pageNumber'] = $pageNumber;
                return response()->json($paymentData);
            } elseif ($userData && $userData->hasAnyRole($memberRoles)) {
                $paymentData['member'] = $this->memberRepository->getMemberById($member_id);
                $paymentData['equb'] = $this->equbRepository->geteEubById($equb_id);
                $paymentData['payments'] = $this->paymentRepository->getSinglePayment($member_id, $equb_id, $offset);
                $paymentData['totalCredit'] = $this->paymentRepository->getTotalCredit($equb_id);
                $paymentData['totalPaid'] = $this->paymentRepository->getTotalPaid($equb_id);
                $paymentData['total'] = $this->paymentRepository->getTotalCount($equb_id);
                $paymentData['offset'] = $offset;
                $paymentData['limit'] = $limit;
                $paymentData['pageNumber'] = $pageNumber;
                return response()->json($paymentData);
            } else {
                return response()->json([
                    'code' => 403,
                    'message' => 'You can\'t perform this action!'
                ]);
            };
        } catch (Exception $ex) {
            return response()->json([
                'code' => 500,
                'message' => 'Unable to process your request, Please try again!',
                "error" => $ex->getMessage()
            ]);
        }
    }
    public function getPaymentsByReference($reference)
    {
        try {
            $paymentData['payments'] = $this->paymentRepository->getByReferenceId($reference);
            return response()->json($paymentData);
        } catch (Exception $ex) {
            return response()->json([
                'code' => 500,
                'message' => 'Unable to process your request, Please try again!',
                "error" => $ex->getMessage()
            ]);
        }
    }
    /**
     * Create Payment
     *
     * This api creates payments.
     *
     * @bodyParam payment_type string required The payment type. Example: Bank
     * @bodyParam amount int required The amount to be paid. Example: 1000
     * @bodyParam creadit int required The credit. Example: 1000
     *
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        try {
            $userData = Auth::user();
                $this->validate($request, [
                    'payment_type' => 'required',
                    'amount' => 'required',
                    'creadit' => 'required',
                ]);
                $member = $request->input('member_id');
                $equb_id = $request->input('equb_id');
                $paymentType = $request->input('payment_type');
                $amount = $request->input('amount');
                $credit = $request->input('creadit');
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
                                $availableBalance = ($availableBalance + $diff) - $tc;
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
                $paymentData = [
                    'member_id' => $member,
                    'equb_id' => $equb_id,
                    'payment_type' => $paymentType,
                    'amount' => $amount,
                    'creadit' => $totalCredit,
                    'balance' => $availableBalance,
                    'collecter' => $userData->id,
                    'status' => 'pending'
                ];
                if ($request->file('payment_proof')) {
                    $image = $request->file('payment_proof');
                    $imageName = time() . '.' . $image->getClientOriginalExtension();
                    $image->storeAs('public/payment_proofs', $imageName);
                    $paymentData['payment_proof'] = 'payment_proofs/' . $imageName;
                }
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
                    $equb = Equb::where('id', $equb_id)->first();
                    $equbTypeId = $equb->equb_type_id;
                    $equbType = EqubType::where('id', $equbTypeId)->first();
                    $notifiedMember = Member::where('id', $member)->first();
                    $memberPhone = $notifiedMember->phone;
                    if ($remainingPayment == 0 && $equbTaker) {
                        $ekubStatus = [
                            'status' => 'Deactive'
                        ];
                        $ekubStatusUpdate = $this->equbRepository->update($equb_id, $ekubStatus);
                    }
                    $activityLog = [
                        'type' => 'payments',
                        'type_id' => $create->id,
                        'action' => 'created',
                        'user_id' => $userData->id,
                        'username' => $userData->name,
                        'role' => $userData->role,
                    ];
                    $this->activityLogRepository->createActivityLog($activityLog);
                    
                    return response()->json([
                        'code' => 200,
                        'message' => 'Payment has been added successfully. Please give us sometime to review and approve it.',
                        'data' => $create
                    ]);
                } else {
                    return response()->json([
                        'code' => 400,
                        'message' => 'Unkown Error Occurred! Please try again!'
                    ]);
                }
        } catch (Exception $ex) {
            return response()->json([
                'code' => 500,
                'message' => 'Unable to process your request, Please try again!',
                "error" => $ex->getMessage()
            ]);
        }
    }
    public function storeForAdmin(Request $request)
    {
        try {
                $userData = Auth::user();
                $this->validate($request, [
                    'payment_type' => 'required',
                    'amount' => 'required',
                    'creadit' => 'required',
                    // 'remark' => 'required',
                ]);
                $member = $request->input('member_id');
                $equb_id = $request->input('equb_id');
                $paymentType = $request->input('payment_type');
                $amount = $request->input('amount');
                $credit = $request->input('creadit');
                $remark = $request->input('remark');
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
                $paymentData = [
                    'member_id' => $member,
                    'equb_id' => $equb_id,
                    'payment_type' => $paymentType,
                    'amount' => $amount,
                    'creadit' => $totalCredit,
                    'note' => $remark,
                    'balance' => $availableBalance,
                    'collecter' => $userData->id
                ];
                $create = $this->paymentRepository->create($paymentData);
                // dd($create);
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
                    $equb = Equb::where('id', $equb_id)->first();
                    $equbTypeId = $equb->equb_type_id;
                    $equbType = EqubType::where('id', $equbTypeId)->first();
                    $notifiedMember = Member::where('id', $member)->first();
                    $memberPhone = $notifiedMember->phone;
                    // dd($equbTaker->status);

                    if ($remainingPayment == 0 && $equbTaker) {
                        $ekubStatus = [
                            'status' => 'Deactive'
                        ];
                        $ekubStatusUpdate = $this->equbRepository->update($equb_id, $ekubStatus);
                    }
                    $activityLog = [
                        'type' => 'payments',
                        'type_id' => $create->id,
                        'action' => 'created',
                        'user_id' => $userData->id,
                        'username' => $userData->name,
                        'role' => $userData->role,
                    ];
                    $this->activityLogRepository->createActivityLog($activityLog);
                    $lotteryDateList = explode(",", $equb->lottery_date);
                    $dates = collect($lotteryDateList)->map(function ($date) {
                        return Carbon::parse($date);
                    });
                    $maxDate = $dates->max();
                    $lotDate = $equbType->type == 'Automatic' ? $equbType->lottery_date : $maxDate->toDateString();
                    try {
                        $shortcode = config('key.SHORT_CODE');
                        $message = "You have successfully paid $amount ETB and a total of $totalPpayment ETB for the equb $equbType->name. Your remaining unpaid amount is $remainingPayment ETB. Your lottery date is $lotDate" . ". For further information please call " . $shortcode;
                        $this->sendSms($memberPhone, $message);
                        if ($remainingPayment == 0) {
                            $paymentMessage = "You have successfully finished your payment of $totalPpayment ETB for the equb $equbType->name" . ". For further information please call " . $shortcode;
                            $this->sendSms($memberPhone, $paymentMessage);
                        }
                    } catch (Exception $ex) {
                        // return response()->json([
                        //     'code' => 500,
                        //     'message' => 'Failed to send SMS',
                        // ]);
                    };
                    return response()->json([
                        'code' => 200,
                        'message' => 'Congratulations! Payment has been registered successfully!',
                        'data' => $create
                    ]);
                } else {
                    return response()->json([
                        'code' => 500,
                        'message' => 'Unknown Error Occurred, Please try again!',
                    ]);
                }
        } catch (Exception $ex) {
            return response()->json([
                'code' => 500,
                'message' => 'Unable to process your request, Please try again!',
                "error" => $ex->getMessage()
            ]);
        }
    }
    /**
     * Get Paginated Payment
     *
     * This api gets paginated payments.
     *
     * @return JsonResponse
     */
    public function show($member_id, $equb_id, $offsetVal, $pageNumberVal)
    {
        try {
            $limit = 10;
            $offset = $offsetVal;
            $pageNumber = $pageNumberVal;
            $userData = Auth::user();
            // if ($userData && ($userData['role'] == "admin" || $userData['role'] == "equb_collector" ||
            //     $userData['role'] == "member")) {
                $paymentData['member'] = $this->memberRepository->getMemberById($member_id);
                $paymentData['equb'] = $this->equbRepository->geteEubById($equb_id);
                $paymentData['payments'] = $this->paymentRepository->getSinglePayment($member_id, $equb_id, $offset);
                $paymentData['totalCredit'] = $this->paymentRepository->getTotalCredit($equb_id);
                $paymentData['totalPaid'] = $this->paymentRepository->getTotalPaid($equb_id);
                $paymentData['total'] = $this->paymentRepository->getTotalCount($equb_id);
                $paymentData['offset'] = $offset;
                $paymentData['limit'] = $limit;
                $paymentData['pageNumber'] = $pageNumber;
                return response()->json($paymentData);
            // } else {
            //     return response()->json([
            //         'code' => 403,
            //         'message' => 'You can\'t perform this action!'
            //     ]);
            // };
        } catch (Exception $ex) {
            return response()->json([
                'code' => 500,
                'message' => 'Unable to process your request, Please try again!',
                "error" => $ex->getMessage()
            ]);
        }
    }
    /**
     * Update Payment
     *
     * This api update payments.
     *
     * @param member_id string required The member id. Example: 1
     * @param equb_id int required The id of the equb. Example: 2
     * @param id int required The id of the payment. Example: 2
     *
     * @bodyParam payment_type string required The type of payment. Example: Bank
     * @bodyParam amount int required The amount to be paid. Example: 1000
     *
     * @return JsonResponse
     */
    public function updatePayment(Request $request, $member, $equb_id, $id)
    {
        try {
            $userData = Auth::user();
            // if ($userData && ($userData['role'] == "admin") || ($userData['role'] == "equb_collector")) {
                $paymentType = $request->input('update_payment_type');
                $amount = $request->input('update_amount');
                $credit = $request->input('update_creadit');
                $updated = [
                    'payment_type' => $paymentType,
                    'amount' => $amount,
                    'collecter' => $userData->id
                ];
                $totalCredit = $this->paymentRepository->getTotalCredit($equb_id);
                $availableBalance = $this->paymentRepository->getTotalBalance($equb_id);
                $oldAmount = $this->paymentRepository->getAmount($id);
                $creditBalance = $amount - $oldAmount;
                $curruntTotalCredit = 0;
                $curruntAvailableBalance = 0;
                if ($creditBalance > 0) {
                    $curruntAvailableBalance = $creditBalance;
                } elseif ($creditBalance < 0) {
                    $curruntTotalCredit = 0 - $creditBalance;
                } else {
                    $curruntTotalCredit = 0;
                    $curruntAvailableBalance = 0;
                }
                $totalCredit = $totalCredit + $curruntTotalCredit;
                if ($totalCredit <= 0) {
                    $totalCredit = 0;
                }
                $availableBalance = $availableBalance + $curruntAvailableBalance;
                if ($availableBalance <= 0) {
                    $availableBalance = 0;
                }
                $lastId = $this->paymentRepository->getLastId($equb_id);
                $updated = $this->paymentRepository->update($id, $updated);
                if ($totalCredit > $availableBalance) {
                    $totalCredit = $totalCredit - $availableBalance;
                    $availableBalance = 0;
                } elseif ($totalCredit < $availableBalance) {
                    $availableBalance = $availableBalance - $totalCredit;
                    $totalCredit = 0;
                } else {
                    $totalCredit = 0;
                    $availableBalance = 0;
                }
                $updateCreditAndBalance = [
                    'creadit' => $totalCredit,
                    'balance' => $availableBalance,
                ];
                $updatedCreditAndBalance = $this->paymentRepository->update($lastId, $updateCreditAndBalance);
                if ($updated && $updatedCreditAndBalance) {
                    $activityLog = [
                        'type' => 'payments',
                        'type_id' => $id,
                        'action' => 'updated',
                        'user_id' => $userData->id,
                        'username' => $userData->name,
                        'role' => $userData->role,
                    ];
                    $this->activityLogRepository->createActivityLog($activityLog);
                    return response()->json([
                        'code' => 200,
                        'message' => 'Payment has been updated successfully!',
                        'data' => $updated
                    ]);
                } else {
                    return response()->json([
                        'code' => 400,
                        'message' => 'Unkown Error! Please try again!'
                    ]);
                }
            // } else {
            //     return response()->json([
            //         'code' => 403,
            //         'message' => 'You can\'t perform this action!'
            //     ]);
            // }
        } catch (Exception $ex) {
            return response()->json([
                'code' => 500,
                'message' => 'Unable to process your request, Please try again!',
                "error" => $ex
            ]);
        }
    }
    /**
     * Delete all Payment
     *
     * This api delete all payments of the member to an equb.
     *
     * @param member_id string required The member id. Example: 1
     * @param equb_id int required The id of the equb. Example: 2
     *
     * @return JsonResponse
     */
    public function deleteAllPayment($member_id, $equb_id)
    {
        try {
            $userData = Auth::user();
            // if ($userData && ($userData['role'] == "admin") || ($userData['role'] == "equb_collector")) {
                $payment = $this->paymentRepository->getByMemberId($member_id, $equb_id);
                if ($payment != null) {
                    $deleted = $this->paymentRepository->deleteAll($member_id, $equb_id);
                    if ($deleted) {
                        $activityLog = [
                            'type' => 'payments',
                            'type_id' => $equb_id,
                            'action' => 'deleted all payment',
                            'user_id' => $userData->id,
                            'username' => $userData->name,
                            'role' => $userData->role,
                        ];
                        $this->activityLogRepository->createActivityLog($activityLog);
                        return response()->json([
                            'code' => 200,
                            'message' => 'All payment has been deleted successfully!'
                        ]);
                    } else {
                        return response()->json([
                            'code' => 500,
                            'message' => 'Unkown Error Occurred! Please try again!'
                        ]);
                    }
                } else {
                    return false;
                }
            // } else {
            //     return response()->json([
            //         'code' => 403,
            //         'message' => 'You can\'t perform this action!'
            //     ]);
            // }
        } catch (Exception $ex) {
            return response()->json([
                'code' => 500,
                'message' => 'Unable to process your request, Please try again!',
                "error" => $ex
            ]);
        }
    }
    /**
     * Delete Payment
     *
     * This api delete payment.
     *
     * @param id string required The id of the payment. Example: 1
     *
     * @return JsonResponse
     */
    public function destroy($id)
    {
        try {
            $userData = Auth::user();
            // if ($userData && ($userData['role'] == "admin") || ($userData['role'] == "equb_collector")) {
                $payment = $this->paymentRepository->getById($id);
                if ($payment != null) {
                    $deleted = $this->paymentRepository->delete($id);
                    if ($deleted) {
                        $activityLog = [
                            'type' => 'payments',
                            'type_id' => $id,
                            'action' => 'deleted',
                            'user_id' => $userData->id,
                            'username' => $userData->name,
                            'role' => $userData->role,
                        ];
                        $this->activityLogRepository->createActivityLog($activityLog);
                        return response()->json([
                            'code' => 200,
                            'message' => 'Payment has been deleted successfully!'
                        ]);
                    } else {
                        return response()->json([
                            'code' => 500,
                            'message' => 'Unkown Error Occurred! Please try again!'
                        ]);
                    }
                } else {
                    return false;
                }
            // } else {
            //     return response()->json([
            //         'code' => 403,
            //         'message' => 'You can\'t perform this action!'
            //     ]);
            // }
        } catch (Exception $ex) {
            return response()->json([
                'code' => 500,
                'message' => 'Unable to process your request, Please try again!',
                "error" => $ex
            ]);
        }
    }
    // public function initialize(Request $request)
    // {
    //     return response()->json([
    //         'code' => 200,
    //         'message' => 'Yeseral?',
    //     ]);
    // }
    public function initialize(Request $request)
    {
        try {
            $req = $request->all();
            Log::info('from initialize', $req);
            $user = Auth::user();
            $userId = $user->id;

            $equbId = $request->input('equb_id');
            $amount = $request->input('amount');
            // $equb = Equb::where('id', $equbId)->first();

            // $equb_amount = $equb->amount;
            // $credit = $equb_amount - $amount;
            // $member = $userId;
            $equb_id = $equbId;
            $paymentType = "telebirr";
            
            $memberData = Member::where('phone', $user->phone_number)->first();
            $collector = User::where('name', 'telebirr')->first();
            $paymentData = [
                'member_id' => $memberData->id,
                'equb_id' => $equb_id,
                'payment_type' => $paymentType,
                'amount' => $amount,
                // 'creadit' => $totalCredit,
                // 'balance' => $availableBalance,
                'collecter' => $collector->id,
                // 'transaction_number' => $reference,
                'status' => 'pending'
            ];

            $telebirr = $this->paymentRepository->create($paymentData);
            // Telebirr initialization 
            if ($telebirr) {


                // Get environment variables from .env or config
                $baseUrl = TELEBIRR_BASE_URL; // Assuming you have set these in env/services.php
                $fabricAppId = TELEBIRR_FABRIC_APP_ID;
                $appSecret = TELEBIRR_APP_SECRET;
                $merchantAppId = TELEBIRR_MERCHANT_APP_ID;
                $merchantCode = TELEBIRR_MERCHANT_CODE;


                // You can also get the request parameters directly from the request object
                $req = $request->all(); // or $request->input('key') for specific keys

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
            Log::info('from callback');
            Log::info($request);
            
            // return response()->json([
            //             'code' => 200,
            //             'message' => 'success'
            //         ], 200);

            if ($request) {
                // $public_key = TELEBIRR_PUBLIC_KEY;
                // $pkey_public = openssl_pkey_get_public($public_key);

                // $dataFromTele = $this->decrypt_RSA($pkey_public, $request->getContent());
                // $dataObj = json_decode($dataFromTele, true);
                $merch_order_id = $request['merch_order_id'];
                $payment = Payment::find($merch_order_id);  // Find the record by ID
               

                if ($request['trade_status'] == 'Completed') {
                    // $equbId = $payment->equb_id;
                    // $amount = $payment->amount;
                    // $equb = Equb::where('id', $equbId)->first();
    
                    // $equb_amount = $equb->amount;
                    // $credit = $equb_amount - $amount;
                    // $member = $payment->member_id;
                    // $equb_id = $equbId;
                    // $paymentType = "telebirr";
                    // if ($credit <= 0) {
                    //     $credit = 0;
                    // }
                    // $totalCredit = $this->paymentRepository->getTotalCredit($equb_id);
                    // if ($totalCredit == null) {
                    //     $totalCredit = 0;
                    // }
                    // $creditData = [
                    //     'creadit' => 0
                    // ];
                    // $this->paymentRepository->updateCredit($equb_id, $creditData);
                    // $lastTc = $totalCredit;
                    // $totalCredit = $credit + $totalCredit;
                    // $tc = $totalCredit;
                    // $equbAmount = $this->equbRepository->getEqubAmount($member, $equb_id);
                    // $availableBalance = $this->paymentRepository->getTotalBalance($equb_id);
                    // $balanceData = [
                    //     'balance' => 0
                    // ];
                    // $this->paymentRepository->updateBalance($equb_id, $balanceData);
                    // if ($availableBalance == null) {
                    //     $availableBalance = 0;
                    // }
                    // $at = $amount;
                    // $amount = $availableBalance + $amount;
                    // Log::info($payment);
    
                    // if ($amount > $equbAmount) {
                    //     if ($totalCredit > 0) {
                    //         if ($totalCredit < $amount) {
                    //             if ($at < $equbAmount) {
                    //                 $availableBalance = $availableBalance - $totalCredit;
                    //                 $totalCredit = 0;
                    //             } elseif ($at > $equbAmount) {
                    //                 $diff = $at - $equbAmount;
                    //                 $totalCredit = $totalCredit - $diff;
                    //                 $availableBalance = $availableBalance + $diff - $tc;
                    //                 $totalCredit = 0;
                    //             } elseif ($at = $equbAmount) {
                    //                 $availableBalance = $availableBalance;
                    //             }
                    //             $amount = $at;
                    //         } else {
                    //             $amount = $at;
                    //             $totalCredit = $totalCredit;
                    //         }
                    //     } else {
                    //         $totalCredit = $totalCredit;
                    //         if ($at < $equbAmount) {
                    //             $availableBalance = $availableBalance - $totalCredit;
                    //         } elseif ($at > $equbAmount) {
                    //             $diff = $at - $equbAmount;
                    //             $totalCredit = $totalCredit - $diff;
                    //             $availableBalance = $availableBalance + $diff;
                    //             $totalCredit = 0;
                    //         } elseif ($at = $equbAmount) {
                    //             $availableBalance = $availableBalance;
                    //         }
                    //         $amount = $at;
                    //     }
                    // } elseif ($amount == $equbAmount) {
                    //     $amount = $at;
                    //     $totalCredit = $lastTc;
                    //     $availableBalance = 0;
                    // } elseif ($amount < $equbAmount) {
                    //     if ($lastTc == 0) {
                    //         $totalCredit = $equbAmount - $amount;
                    //         $availableBalance = 0;
                    //         $amount = $at;
                    //     } else {
                    //         $totalCredit = $totalCredit;
                    //         $availableBalance = 0;
                    //         $amount = $at;
                    //     }
                    // }

                    $equbId = $payment->equb_id;
                    $amount = $payment->amount;
                    $equb = Equb::where('id', $equbId)->first();

                    $equb_amount = $equb->amount;
                    $member = $payment->member_id;
                    $equb_id = $equbId;
                    $paymentType = "telebirr";

                    // Get existing balance and total credit
                    $totalCredit = $this->paymentRepository->getTotalCredit($equb_id) ?? 0;
                    $lastTc = $totalCredit;
                    $tc = $lastTc; 

                    $equbAmount = $this->equbRepository->getEqubAmount($member, $equb_id);
                    $availableBalance = $this->paymentRepository->getTotalBalance($equb_id) ?? 0;

                    $at = $amount; // Store the original amount before modifications

                    // First, use the available balance to reduce the required amount
                    if ($availableBalance > 0) {
                        if ($availableBalance >= $equbAmount) {
                            // If balance is enough to cover full payment, reset it
                            $availableBalance = 0;
                            $amount = 0;
                        } else {
                            // Reduce the amount by the balance amount first
                            $amount -= $availableBalance;
                            $availableBalance = 0;
                        }
                    }

                    // Now calculate remaining credit only if amount is still outstanding
                    if ($amount > $equbAmount) {
                        $credit = $amount - $equbAmount;
                    } else {
                        $credit = 0; // No credit should be stored if full amount is covered
                    }

                    // Update balance & credit in the database
                    $this->paymentRepository->updateCredit($equb_id, ['credit' => $credit]);
                    $this->paymentRepository->updateBalance($equb_id, ['balance' => $availableBalance]);

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

                    Log::info($telebirrObj);
                    // $payment->save($telebirrObj);
                    Log::info($payment);
                    $equb_id = $payment->equb_id;

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
                    ], 200);
                } else {

                    return response()->json([
                        'code' => 400,
                        'message' => 'Payment failed, Please try again!'
                    ], 400);
                }
            }
        } catch (Exception $error) {
            return response()->json([
                'code' => 500,
                'message' => 'Unable to process your request, Please try again!',
                "error" => $error
            ]);
        }
    }
    public function callback1(Request $request)
    {
        try {
            Log::info('Telebirr callback received:', $request->all());

            // Validate incoming request
            if (!$request->has(['merch_order_id', 'trade_status', 'total_amount', 'payment_order_id', 'notify_time'])) {
                return response()->json(['code' => 400, 'message' => 'Invalid request data'], 400);
            }

            // Find payment record
            $payment = Payment::find($request->input('merch_order_id'));

            if (!$payment) {
                return response()->json(['code' => 404, 'message' => 'Payment not found'], 404);
            }

            // Ensure transaction is completed
            if ($request->input('trade_status') !== 'Completed') {
                return response()->json(['code' => 400, 'message' => 'Payment failed, please try again!'], 400);
            }

            // Fetch related Equb record
            $equb = Equb::findOrFail($payment->equb_id);

            // Convert timestamp to readable date
            $tradeDate = Carbon::createFromTimestamp($request->input('notify_time') / 1000)->format('Y-m-d H:i:s');

            // Get total paid and balance calculations
            [$totalCredit, $availableBalance] = $this->calculateCreditAndBalance($payment, $equb, $request->input('total_amount'));
            $collector = User::where('name', 'telebirr')->first();

            // Update payment details
            $payment->update([
                'amount'             => $request->input('total_amount'),
                'tradeDate'          => $tradeDate,
                'tradeNo'            => $request->input('payment_order_id'),
                'tradeStatus'        => $request->input('trade_status'),
                'transaction_number' => $request->input('payment_order_id'),
                'status'             => 'paid',
                'collecter'          => $collector->id,
                'payment_type'       => 'telebirr',
                'creadit'            => $totalCredit,
                'balance'            => $availableBalance
            ]);

            // Update Equb payment tracking
            $totalPaid = $this->paymentRepository->getTotalPaid($equb->id);
            $remainingPayment = $this->equbRepository->getTotalEqubAmount($equb->id) - $totalPaid;

            $this->equbTakerRepository->updatePayment($equb->id, [
                'total_payment'     => $totalPaid,
                'remaining_payment' => $remainingPayment,
            ]);

            // Deactivate Equb if fully paid
            if ($remainingPayment <= 0) {
                $this->equbRepository->update($equb->id, ['status' => 'Deactive']);
            }

            Log::info('Payment processed successfully:', $payment->toArray());

            return response()->json(['code' => 200, 'message' => 'You have successfully paid!'], 200);

        } catch (\Exception $e) {
            Log::error('Payment processing error:', ['error' => $e->getMessage()]);

            return response()->json([
                'code'    => 500,
                'message' => 'Unable to process your request, please try again!',
                'error'   => $e->getMessage()
            ], 500);
        }
    }
    private function calculateCreditAndBalance($payment, $equb, $amountPaid)
    {
        $equbAmount = $equb->amount;
        $previousCredit = $this->paymentRepository->getTotalCredit($equb->id) ?? 0;
        $availableBalance = $this->paymentRepository->getTotalBalance($equb->id) ?? 0;

        // Adjust balance and credit based on payment
        if ($amountPaid > $equbAmount) {
            $excessAmount = $amountPaid - $equbAmount;
            if ($previousCredit > 0) {
                $remainingCredit = max(0, $previousCredit - $excessAmount);
                $availableBalance += ($previousCredit - $remainingCredit);
            } else {
                $remainingCredit = 0;
                $availableBalance += $excessAmount;
            }
        } elseif ($amountPaid == $equbAmount) {
            $remainingCredit = 0;
            $availableBalance = 0;
        } else {
            $remainingCredit = max(0, $equbAmount - $amountPaid);
            $availableBalance = 0;
        }

        // Update repositories
        $this->paymentRepository->updateCredit($equb->id, ['creadit' => $remainingCredit]);
        $this->paymentRepository->updateBalance($equb->id, ['balance' => $availableBalance]);

        return [$remainingCredit, $availableBalance];
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
            $payment    =    $this->paymentRepository->getById($id);

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