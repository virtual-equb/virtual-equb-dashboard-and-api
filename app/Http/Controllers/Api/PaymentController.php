<?php

namespace App\Http\Controllers\api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Equb;
use App\Models\EqubType;
use App\Models\Member;
use App\Models\Payment;
use App\Models\User;
use Exception;
use App\Repositories\Payment\IPaymentRepository;
use App\Repositories\Member\IMemberRepository;
use App\Repositories\Equb\IEqubRepository;
use App\Repositories\EqubTaker\IEqubTakerRepository;
use App\Repositories\ActivityLog\IActivityLogRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

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
        $this->middleware('auth:api')->except('getPaymentsByReference');
        $this->activityLogRepository = $activityLogRepository;
        $this->paymentRepository = $paymentRepository;
        $this->memberRepository = $memberRepository;
        $this->equbRepository = $equbRepository;
        $this->equbTakerRepository = $equbTakerRepository;
        $this->title = "Virtual Equb - Payment";

        // Permission Guard
        $this->middleware('permission:update payment', ['only' => ['update', 'edit', 'updatePayment']]);
        $this->middleware('permission:delete payment', ['only' => ['destroy', 'deleteAllPayment', 'deletePayment']]);
        $this->middleware('permission:view payment', ['only' => ['index', 'show']]);
        $this->middleware('permission:create payment', ['only' => ['store', 'storeForAdmin', 'create', 'initialize']]);
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
                    // $lotDate = $equbType->type == 'Automatic' ? $equbType->lottery_date : $equb->lottery_date;
                    // try {
                    //     $shortcode = config('key.SHORT_CODE');
                    //     $message = "You have successfully paid $amount ETB and a total of $totalPpayment ETB for the equb $equbType->name. Your remaining unpaid amount is $remainingPayment ETB. Your lottery date is $lotDate" . " For further information please call " . $shortcode;
                    //     $this->sendSms($memberPhone, $message);
                    // } catch (Exception $ex) {
                    //     return redirect()->back()->with('error', 'Failed to send SMS');
                    // };
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
                "error" => $ex
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
        // dd($request);
        try {
            $user = Auth::user();
            $userId = $user->id;
            // dd($userId);
            $equbId = $request->input('equb_id');
            $amount = $request->input('amount');

            $equb = Equb::where('id', $equbId)->first();
            $equb_amount = $equb->amount;
            $credit = $equb_amount - $amount;
            $member = $userId;
            $equb_id = $equbId;
            $paymentType = "telebirr";
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
            $memberData = Member::where('phone', $user->phone_number)->first();
            $paymentData = [
                'member_id' => $memberData->id,
                'equb_id' => $equb_id,
                'payment_type' => $paymentType,
                'amount' => $amount,
                'creadit' => $totalCredit,
                'balance' => $availableBalance,
                'collecter' => $memberData->id,
                // 'transaction_number' => $reference,
                'status' => 'pending'
            ];
            $telebirr = $this->paymentRepository->create($paymentData);
            //Telebirr initialization

            if ($telebirr) {

                $telebirr->transaction_number = $telebirr->id;
                $telebirr->save();
                return response()->json([
                    'code' => 200,
                    'data' => [
                        "code" => 200,
                        "outTradeNo" => $telebirr->id,
                        "appId" => TELEBIRR_APP_ID,
                        "receiverName" => TELEBIRR_RECEIVER_NAME,
                        "shortCode" => TELEBIRR_SHORT_CODE,
                        "subject" => TELEBIRR_SUBJECT,
                        "returnUrl" => url(TELEBIRR_RETURN_URL . "/$telebirr->id"),
                        "notifyUrl" => url(TELEBIRR_NOTIFY_URL . "/$telebirr->id"),
                        "inAppPaymentUrl" => TELEBIRR_INAPP_PAYMENT_URL,
                        "h5PaymentUrl" => TELEBIRR_H5_URL,
                        "timeoutExpress" => TELEBIRR_TIMEOUT_EXPRESS,
                        "appKey" => TELEBIRR_APP_KEY,
                        "publicKey" => TELEBIRR_PUBLIC_KEY_C,
                        "user_id" => $memberData->id,
                        "totalAmount" => (string)$telebirr->amount,
                    ]
                ], 200);
            } else {
                return response()->json([
                    'code' => 400,
                    'message' => 'Unknown error occurred, Please try again!'
                ], 400);
            }
        } catch (Exception $error) {
            // dd($error);
            return response()->json([
                'code' => 500,
                'message' => 'Unable to process your request, Please try again!',
                "error" => $error->getMessage()
            ], 500);
        }
    }
    public function callback(Request $request, Payment $payment)
    {
        // try {
        if ($payment) {
            $public_key = TELEBIRR_PUBLIC_KEY;
            $pkey_public = openssl_pkey_get_public($public_key);

            $dataFromTele = $this->decrypt_RSA($pkey_public, $request->getContent());
            $dataObj = json_decode($dataFromTele, true);
            if ($dataObj['tradeStatus'] == 2) {

                $tradeDt = $dataObj['tradeDate'];
                $tradeDate = date("Y-m-d H:i:s", $tradeDt);
                $telebirrObj = [
                    'msisdn' => $dataObj['msisdn'],
                    'totalAmount' => $dataObj['totalAmount'],
                    'tradeDate' => $tradeDate,
                    'tradeNo' => $dataObj['tradeNo'],
                    'tradeStatus' => $dataObj['tradeStatus'],
                    'transactionNo' => $dataObj['transactionNo'],
                    'status' => 'paid'
                ];
                $payment->update($telebirrObj);

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
    }
    private function decrypt_RSA($publicPEMKey, $data)
    {
        $public_key = '-----BEGIN PUBLIC KEY-----MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAmSvHmZwuQjZbD+X6qxJZIjms4kyEo/tRJGt66F/aUkIrsoMaY/kIS+hgiUhcQi1Lem0DDCb+CHAaSf/YiiCmdhXhaSDckMgZvIzcAZhQX0pHtZhbim9G/0/ekrm7JWCq0+YJ7KF5xcWtRNyHpVKi6snpqsAVp9o8rsMHPhn4YvLZGVUapONRwtmBJ5YLJdkmMD9FU1r/B+yl8lIQjr3iVHMaCQXbEv7mF34FP9wDm5kvysSsthZ6APzJMWTswNCDIgVrbmXOvyOxd3x8PNCFkwwH4BrLxsmyDY7KnXm55oqOukeYODtG3AShnwVFDn7G/7mdI8vEURHkwbVT4SuQJwIDAQAB-----END PUBLIC KEY-----';
        $pkey_public = openssl_pkey_get_public($public_key);
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
    public function getTransaction(Payment $payment)
    {
        try {
            return response()->json([
                'code' => 200,
                'transaction' => $payment
            ]);
        } catch (Exception $error) {
            return response()->json([
                'code' => 500,
                'message' => 'Unable to process your request, Please try again!',
                "error" => $error->getMessage()
            ]);
        }
    }
}
