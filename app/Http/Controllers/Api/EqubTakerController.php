<?php

namespace App\Http\Controllers\api;

use Illuminate\Http\Request;
use Exception;
use App\Repositories\Member\IMemberRepository;
use App\Repositories\EqubTaker\IEqubTakerRepository;
use App\Repositories\Payment\IPaymentRepository;
use App\Repositories\EqubType\IEqubTypeRepository;
use App\Repositories\Equb\IEqubRepository;
use App\Repositories\ActivityLog\IActivityLogRepository;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
/**
 * @group Equb Takers
 */
class EqubTakerController extends Controller
{
    private $activityLogRepository;
    private $equbTakerRepository;
    private $memberRepository;
    private $equbRepository;
    private $equbTypeRepository;
    private $paymentRepository;
    private $title;
    public function __construct(
        IEqubTakerRepository $equbTakerRepository,
        IMemberRepository $memberRepository,
        IEqubRepository $equbRepository,
        IEqubTypeRepository $equbTypeRepository,
        IPaymentRepository $paymentRepository,
        IActivityLogRepository $activityLogRepository
    ) {
        $this->middleware('auth:api');
        $this->activityLogRepository = $activityLogRepository;
        $this->equbTakerRepository = $equbTakerRepository;
        $this->memberRepository = $memberRepository;
        $this->equbRepository = $equbRepository;
        $this->equbTypeRepository = $equbTypeRepository;
        $this->paymentRepository = $paymentRepository;
        $this->title = "Virtual Equb - Equb Taker";
    }
    /**
     * Get All Equb Takers
     *
     * This api returns all Equb Takers.
     *
     * @return JsonResponse
     */
    public function index()
    {
        try {
            $userData = Auth::user();
            if ($userData && ($userData['role'] == "admin" || $userData['role'] == "equb_collector")) {
                $data['equbTakers']  = $this->equbTakerRepository->getAll();
                $data['equbs']  = $this->equbRepository->getAll();
                $data['members']  = $this->memberRepository->getMemberWithEqub();
                $data['title']  = $this->title;
                return response()->json($data);
            } else {
                return response()->json([
                    'code' => 400,
                    'message' => 'You can\'t perform this action!'
                ]);
            }
        } catch (Exception $ex) {
            return response()->json([
                'code' => 500,
                'message' => 'Unable to process your request, Please try again!',
                "error" => $ex
            ]);
        }
    }
    /**
     * Get remaining lottery amount
     *
     * This api returns remmaining lottery amount of an equb.
     *
     * @param id int required The id of the equb. Example: 2
     *
     * @return JsonResponse
     */
    public function getRemainingLotteryAmount($id)
    {
        try {
            $equb = $this->equbRepository->getRemainingLotteryAmount($id)->equbTakers->toArray();
            if ($equb) {
                $remainingLotteryAmount = $this->equbRepository->getRemainingLotteryAmount($id)->equbTakers->last()->remaining_amount;
            } else {
                $remainingLotteryAmount = 1;
            }
            return response()->json($remainingLotteryAmount);
        } catch (Exception $ex) {
            return response()->json([
                'code' => 500,
                'message' => 'Unable to process your request, Please try again!',
                "error" => $ex
            ]);
        }
    }
    /**
     * Create equb taker
     *
     * @bodyParam payment_type string required The type of payment. Example: 2
     * @bodyParam amount int required The amount to be paid. Example: 1000
     * @bodyParam status string required The status of the payment. Example: 10000
     * @bodyParam lottey_member_id int required The member id of the lottery receiver. Example: 1
     * @bodyParam lottery_equb_id int required The id of the lottery equb. Example: 1
     * @bodyParam cheque_amount int required The amount the member has written a check for. Example: 10000
     * @bodyParam cheque_bank_name string required The bank of the check. Example: CBE
     * @bodyParam cheque_description string required The description of the check. Example: From Member
     *
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        try {
            $userData = Auth::user();
            if ($userData && ($userData['role'] == "admin") || ($userData['role'] == "equb_collector")) {
                $this->validate($request, [
                    'payment_type' => 'required',
                    'amount' => 'required',
                    'status' => 'required'
                ]);

                $memberId = $request->input('lottey_member_id');
                $equbId = $request->input('lottery_equb_id');
                $paymentType = $request->input('payment_type');
                $amount = $request->input('amount');
                $status = $request->input('status');
                $totalPpayment = $this->paymentRepository->getTotalPaid($equbId);
                $totalEqubAmount = $this->equbRepository->getTotalEqubAmount($equbId);
                $takenEqub = $this->equbTakerRepository->getTotalEqubAmount($equbId);
                $remainingAmount = $totalEqubAmount - $takenEqub;
                $remainingAmount = $remainingAmount - $amount;
                $remainingPayment =  $totalEqubAmount - $totalPpayment;
                $chequeAmount = $request->input('cheque_amount');
                $chequeBankName = $request->input('cheque_bank_name');
                $chequeDescription = $request->input('cheque_description');

                $equbTakerData = [
                    'member_id' => $memberId,
                    'equb_id' => $equbId,
                    'payment_type' => $paymentType,
                    'amount' => $amount,
                    'remaining_amount' => $remainingAmount,
                    'status' => $status,
                    'paid_by' => $userData->name,
                    'total_payment' => $totalPpayment,
                    'remaining_payment' => $remainingPayment,
                    'cheque_amount' => $chequeAmount,
                    'cheque_bank_name' => $chequeBankName,
                    'cheque_description' => $chequeDescription,
                ];
                $create = $this->equbTakerRepository->create($equbTakerData);
                if ($create) {
                    $activityLog = [
                        'type' => 'equb_takers',
                        'type_id' => $create->id,
                        'action' => 'created',
                        'user_id' => $userData->id,
                        'username' => $userData->name,
                        'role' => $userData->role,
                    ];
                    $this->activityLogRepository->createActivityLog($activityLog);
                    return response()->json([
                        'code' => 200,
                        'message' => 'Lottery winner registerd successfully!',
                        'data' => $create
                    ]);
                } else {
                    return response()->json([
                        'code' => 400,
                        'message' => 'Unknown error occurred, Please try again!',
                        "error" => "Unknown error occurred, Please try again!"
                    ]);
                }
            } else {
                return response()->json([
                    'code' => 400,
                    'message' => 'You can\'t perform this action!'
                ]);
            }
        } catch (Exception $ex) {
            return response()->json([
                'code' => 500,
                'message' => 'Unable to process your request, Please try again!',
                "error" => $ex
            ]);
        }
    }
    /**
     * Update equb taker
     *
     * This api updates equb takers.
     *
     * @bodyParam payment_type string required The type of payment. Example: 2
     * @bodyParam amount int required The amount to be paid. Example: 1000
     * @bodyParam status string required The status of the payment. Example: 10000
     * @bodyParam lottey_member_id int required The member id of the lottery receiver. Example: 1
     * @bodyParam lottery_equb_id int required The id of the lottery equb. Example: 1
     * @bodyParam cheque_amount int required The amount the member has written a check for. Example: 10000
     * @bodyParam cheque_bank_name string required The bank of the check. Example: CBE
     * @bodyParam cheque_description string required The description of the check. Example: From Member
     *
     * @return JsonResponse
     */
    public function edit($id)
    {
        try {
            $userData = Auth::user();
            if ($userData && ($userData['role'] == "admin" || $userData['role'] == "equb_collector")) {
                $data['equbTaker'] = $this->equbTakerRepository->getAllEqubTaker($id);
                $data['members'] = $this->memberRepository->getAll();
                $data['equbs'] = $this->equbRepository->getAll();
                $data['equbTypes'] = $this->equbTypeRepository->getAll();
                return response()->json($data);
            } else {
                return response()->json([
                    'code' => 403,
                    'message' => 'You can\'t perform this action!'
                ]);
            }
        } catch (Exception $ex) {
            return response()->json([
                'code' => 500,
                'message' => 'Unable to process your request, Please try again!',
                "error" => $ex
            ]);
        }
    }
    /**
     * Update lottery
     *
     * This api updates lottery.
     *
     * @bodyParam update_lottery_payment_type string required The type of payment. Example: 2
     * @bodyParam update_lottery_amount int required The amount to be paid. Example: 1000
     * @bodyParam update_lottery_status string required The status of the payment. Example: 10000
     * @bodyParam update_lottery_cheque_amount int required The amount the member has written a check for. Example: 10000
     * @bodyParam update_lottery_cheque_bank_name string required The bank of the check. Example: CBE
     * @bodyParam update_lottery_cheque_description string required The description of the check. Example: From Member
     *
     * @param lottey_member_id int required The member id of the lottery receiver. Example: 1
     * @param lottery_equb_id int required The id of the lottery equb. Example: 1
     * @param lottery_id int required The id of the lottery. Example: 1
     *
     * @return JsonResponse
     */
    public function updateLottery($member_id, $equb_id, $id, Request $request)
    {
        try {
            $userData = Auth::user();
            if ($userData && ($userData['role'] == "admin") || ($userData['role'] == "equb_collector")) {
                $paymentType = $request->input('update_lottery_payment_type');
                $amount = $request->input('update_lottery_amount');
                $status = $request->input('update_lottery_status');
                $totalPpayment = $this->paymentRepository->getTotalPaid($equb_id);
                $totalEqubAmount = $this->equbRepository->getTotalEqubAmount($equb_id);
                $remainingPayment =  $totalEqubAmount - $totalPpayment;
                $remainingAmount =  $totalEqubAmount - $amount;
                $chequeAmount = $request->input('update_lottery_cheque_amount');
                $chequeBankName = $request->input('update_lottery_cheque_bank_name');
                $chequeDescription = $request->input('update_lottery_cheque_description');

                $updated = [
                    'payment_type' => $paymentType,
                    'amount' => $amount,
                    'remaining_amount' => $remainingAmount,
                    'status' => $status,
                    'paid_by' => $userData->name,
                    'total_payment' => $totalPpayment,
                    'remaining_payment' => $remainingPayment,
                    'cheque_amount' => $chequeAmount,
                    'cheque_bank_name' => $chequeBankName,
                    'cheque_description' => $chequeDescription,
                ];
                $updated = $this->equbTakerRepository->update($id, $updated);
                if ($updated) {
                    $activityLog = [
                        'type' => 'equb_takers',
                        'type_id' => $id,
                        'action' => 'updated',
                        'user_id' => $userData->id,
                        'username' => $userData->name,
                        'role' => $userData->role,
                    ];
                    $this->activityLogRepository->createActivityLog($activityLog);
                    return response()->json([
                        'code' => 200,
                        'message' => 'lottery winner updated successfully!',
                        'data' => $updated
                    ]);
                } else {
                    return response()->json([
                        'code' => 400,
                        'message' => 'Unknown error occurred, Please try again!',
                        "error" => "Unknown error occurred, Please try again!"
                    ]);
                }
            } else {
                return response()->json([
                    'code' => 403,
                    'message' => 'You can\'t perform this action!'
                ]);
            }
        } catch (Exception $ex) {
            return response()->json([
                'code' => 500,
                'message' => 'Unable to process your request, Please try again!',
                "error" => $ex
            ]);
        }
    }
    /**
     * Delete lottery
     *
     * This api deletes a lottery.
     *
     * @param id int required The id of the lottery. Example: 1
     *
     * @return JsonResponse
     */
    public function destroy($id)
    {
        try {
            $userData = Auth::user();
            if ($userData && ($userData['role'] == "admin") || ($userData['role'] == "equb_collector")) {
                $equbTaker = $this->equbTakerRepository->getById($id);
                if ($equbTaker != null) {
                    $deleted = $this->equbTakerRepository->delete($id);
                    if ($deleted) {
                        $activityLog = [
                            'type' => 'equb_takers',
                            'type_id' => $id,
                            'action' => 'deleted',
                            'user_id' => $userData->id,
                            'username' => $userData->name,
                            'role' => $userData->role,
                        ];
                        $this->activityLogRepository->createActivityLog($activityLog);
                        return response()->json([
                            'code' => 200,
                            'message' => 'Lottery winner updated successfully!'
                        ]);
                    } else {
                        return response()->json([
                            'code' => 400,
                            'message' => 'Unknown error occurred, Please try again!',
                            "error" => "Unknown error occurred, Please try again!"
                        ]);
                    }
                } else {
                    return false;
                }
            } else {
                return response()->json([
                    'code' => 403,
                    'message' => 'You can\'t perform this action!'
                ]);
            }
        } catch (Exception $ex) {
            return response()->json([
                'code' => 500,
                'message' => 'Unable to process your request, Please try again!',
                "error" => $ex
            ]);
        }
    }
}
