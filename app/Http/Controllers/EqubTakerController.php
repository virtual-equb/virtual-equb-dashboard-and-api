<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Exception;
use App\Repositories\Member\IMemberRepository;
use App\Repositories\EqubTaker\IEqubTakerRepository;
use App\Repositories\Payment\IPaymentRepository;
use App\Repositories\EqubType\IEqubTypeRepository;
use App\Repositories\Equb\IEqubRepository;
use App\Repositories\ActivityLog\IActivityLogRepository;
use App\Models\EqubTaker;
use App\Models\Member;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

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
        $this->activityLogRepository = $activityLogRepository;
        $this->equbTakerRepository = $equbTakerRepository;
        $this->memberRepository = $memberRepository;
        $this->equbRepository = $equbRepository;
        $this->equbTypeRepository = $equbTypeRepository;
        $this->paymentRepository = $paymentRepository;
        $this->title = "Virtual Equb - Equb Taker";
    }
 
    public function index()
    {
        try {
            $userData = Auth::user();
            $adminRoles = ['admin', 'general_manager', 'operation_manager', 'it', 'finance',' call_center', 'assistant', 'collector and finance', 'Customer service supervisor', 'Legal Affair Officers', 'Marketing Manager'];
            $member = ['member'];
            $equbcollector = ['equb_collector'];
            if ($userData && $userData->hasAnyRole($adminRoles)){
                $data['equbTakers']  = $this->equbTakerRepository->getAll();
                $data['equbs']  = $this->equbRepository->getAll();
                $data['members']  = $this->memberRepository->getMemberWithEqub();
                $data['title']  = $this->title;
                $totalEqubTaker = EqubTaker::count();

                return view('admin/equbTaker.equbTakerList', $data, compact('totalEqubTaker'));
            } elseif ($userData && $userData->hasAnyRole($equbcollector)) {
                $data['equbTakers']  = $this->equbTakerRepository->getAll();
                $data['equbs']  = $this->equbRepository->getAll();
                $data['members']  = $this->memberRepository->getMemberWithEqub();
                $data['title']  = $this->title;

                return view('equbcollector/equbTaker.equbTakerList', $data);
            } elseif ($userData && $userData->hasAnyRole($member)) {
                $data['equbTakers']  = $this->equbTakerRepository->getAll();
                $data['equbs']  = $this->equbRepository->getAll();
                $data['members']  = $this->memberRepository->getMemberWithEqub();
                $data['title']  = $this->title;

                return view('member/equbTaker.equbTakerList', $data);
            } else  {
                return view('auth/login');
            }
        } catch (Exception $ex) {
            $msg = "Unknown Error Occurred, Please try again!";
            $type = 'error';
            Session::flash($type, $msg);
            return back();
        }
    }

    public function getRemainingLotteryAmount($id)
    {
        try {
            $equb = $this->equbRepository->getRemainingLotteryAmount($id)->equbTakers->toArray();
            if ($equb) {
                $remainingLotteryAmount = $this->equbRepository->getRemainingLotteryAmount($id)->equbTakers->last()->remaining_amount;
            } else {
                $remainingLotteryAmount = 1;
            }
            return ($remainingLotteryAmount);
        } catch (Exception $ex) {
            $msg = "Unable to process your request, Please try again!";
            $type = 'error';
            Session::flash($type, $msg);
            return back();
        }
    }
   
    public function store(Request $request)
    {
        try {
            $userData = Auth::user();
                $this->validate($request, [
                    'payment_type' => 'required',
                    'amount' => 'required',
                    // 'status' => 'required'
                ]);

                $memberId = $request->input('lottey_member_id');
                $equbId = $request->input('lottery_equb_id');
                $paymentType = $request->input('payment_type');
                $amount = $request->input('amount');
                $status = 'pending';
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

                //new status added
                if ($status == "paid") {
                    if ($remainingAmount == 0) {
                        $equbTakers = EqubTaker::where('member_id', $memberId)->where('equb_id', $equbId)->get();
                        foreach ($equbTakers as $equbTaker) {
                            $equbTaker->status = "void";
                            $equbTaker->save();
                        }
                    }
                }

                $totalPpayment = $this->paymentRepository->getTotalPaid($equbId);
                $totalEqubAmount = $this->equbRepository->getTotalEqubAmount($equbId);
                $remainingPayment =  $totalEqubAmount - $totalPpayment;

                $create = $this->equbTakerRepository->create($equbTakerData);

                if ($remainingPayment == 0 && $create->status == 'paid' && $create->remaining_amount == 0) {
                    $ekubStatus = [
                        'status' => 'Deactive'
                    ];
                    $ekubStatusUpdate = $this->equbRepository->update($equbId, $ekubStatus);
                }

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
                    $msg = "Lottery winner - payment data has been registered successfully!";
                    $type = 'success';
                    Session::flash($type, $msg);
                    return redirect('member/');
                } else {
                    $msg = "Unknown Error Occurred, Please try again!";
                    $type = 'error';
                    Session::flash($type, $msg);
                    redirect('member/');
                }
        } catch (Exception $ex) {
            $msg = "Unknown Error Occurred, Please try again!";
            $type = 'error';
            Session::flash($type, $msg);
            return back();
        }
    }

    public function changeStatus($status, $id)
    {
        try {
            $equbTaker = $this->equbTakerRepository->getById($id);
            $userData = Auth::user();

            if ($userData && ($userData['role'] == "admin") || ($userData['role'] == "equb_collector")) {
                $memberId = $equbTaker->member_id;
                $equbId = $equbTaker->equb_id;
                $amount = $equbTaker->amount;

                $totalPpayment = $this->paymentRepository->getTotalPaid($equbId);
                $totalEqubAmount = $this->equbRepository->getTotalEqubAmount($equbId);
                $takenEqub = $this->equbTakerRepository->getTotalEqubAmount($equbId);
                $remainingAmount = $totalEqubAmount - $takenEqub;
                $remainingAmount = $remainingAmount - $amount;
                $remainingPayment =  $totalEqubAmount - $totalPpayment;

                //new status added
                if ($status == "paid") {
                    if ($remainingAmount == 0) {
                        $equbTakers = EqubTaker::where('member_id', $memberId)->where('equb_id', $equbId)->get();
                        foreach ($equbTakers as $equbTaker) {
                            $equbTaker->status = "void";
                            $equbTaker->save();
                        }
                    }
                }

                $totalPpayment = $this->paymentRepository->getTotalPaid($equbId);
                $totalEqubAmount = $this->equbRepository->getTotalEqubAmount($equbId);
                $remainingPayment =  $totalEqubAmount - $totalPpayment;

                $updatedEkubTaker = [
                    "status" => $status
                ];
                $create = $this->equbTakerRepository->update($id, $updatedEkubTaker);
                $equbTaker = $this->equbTakerRepository->getById($id);

                if ($remainingPayment == 0 && $equbTaker->status == 'paid' && $equbTaker->remaining_amount == 0) {
                    $ekubStatus = [
                        'status' => 'Deactive'
                    ];
                    $this->equbRepository->update($equbId, $ekubStatus);
                }

                if ($create) {
                    $activityLog = [
                        'type' => 'equb_takers',
                        'type_id' => $id,
                        'action' => $status,
                        'user_id' => $userData->id,
                        'username' => $userData->name,
                        'role' => $userData->role,
                    ];
                    $this->activityLogRepository->createActivityLog($activityLog);

                    try {
                        $notifiedMember = Member::where('id', $memberId)->first();
                        $memberPhone = $notifiedMember->phone;
                        $equbName = $equbTaker->equb->equbType->name;

                        $shortcode = config('key.SHORT_CODE');
                        $message = "Congrats! You've received a payment of $equbTaker->cheque_amount ETB for winning the $equbName equb. Lottery date: $equbTaker->created_at. For more info, call $shortcode.";
                        
                        $this->sendSms($memberPhone, $message);

                    } catch (Exception $ex) {
                        return redirect()->back()->with('error', 'Failed to send SMS');
                    };

                    $msg = "Lottery winner - payment data has been $status successfully!";
                    $type = 'success';
                    Session::flash($type, $msg);
                    return redirect('member/');
                } else {
                    $msg = "Unknown Error Occurred, Please try again!";
                    $type = 'error';
                    Session::flash($type, $msg);
                    redirect('member/');
                }
            } else {
                $msg = "You do not have the required role to approve this transaction.";
                $type = 'error';
                Session::flash($type, $msg);
                return back();
            }
        } catch (Exception $ex) {
            $msg = "Unknown Error Occurred, Please try again!";
            $type = 'error';
            Session::flash($type, $msg);
            return back();
        }
    }
    
    public function edit($id)
    {
        try {
            $userData = Auth::user();
            if ($userData && ($userData['role'] == "admin" || $userData['role'] == "general_manager" || $userData['role'] == "operation_manager" || $userData['role'] == "it")){
                $data['equbTaker'] = $this->equbTakerRepository->getAllEqubTaker($id);
                $data['members'] = $this->memberRepository->getAll();
                $data['equbs'] = $this->equbRepository->getAll();
                $data['equbTypes'] = $this->equbTypeRepository->getAll();
                return view('admin/equbTaker/editEqubTaker', $data);
            } elseif ($userData && $userData['role'] == "equb_collector") {
                $data['equbTaker'] = $this->equbTakerRepository->getAllEqubTaker($id);
                $data['members'] = $this->memberRepository->getAll();
                $data['equbs'] = $this->equbRepository->getAll();
                $data['equbTypes'] = $this->equbTypeRepository->getAll();
                return view('equbCollecter/equbTaker/editEqubTaker', $data);
            } elseif ($userData && $userData['role'] == "member") {
                $data['equbTaker'] = $this->equbTakerRepository->getAllEqubTaker($id);
                $data['members'] = $this->memberRepository->getAll();
                $data['equbs'] = $this->equbRepository->getAll();
                $data['equbTypes'] = $this->equbTypeRepository->getAll();
                return view('member/equbTaker/editEqubTaker', $data);
            } else {
                return view('auth/login');
            }
        } catch (Exception $ex) {
            $msg = "Unable to process your request, Please try again!";
            $type = 'error';
            Session::flash($type, $msg);
            return back();
        }
    }

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
                    $msg = "Lottery detail has been updated successfully!";
                    $type = 'success';
                    Session::flash($type, $msg);
                    return redirect('member/');
                } else {
                    $msg = "Unknown error occurred, Please try again!";
                    $type = 'error';
                    Session::flash($type, $msg);
                    return back();
                }
            } else {
                return view('auth/login');
            }
        } catch (Exception $ex) {
            $msg = "Unable to process your request, Please try again!";
            $type = 'error';
            Session::flash($type, $msg);
            return back();
        }
    }

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
                        $msg = "Lottery history has been deleted successfully!";
                        $type = 'success';
                        Session::flash($type, $msg);
                        return redirect('member/');
                    } else {
                        $msg = "Unknown Error Occurred, Please try again!";
                        $type = 'error';
                        Session::flash($type, $msg);
                        redirect('/member');
                    }
                } else {
                    return false;
                }
            } else {
                return view('auth/login');
            }
        } catch (Exception $ex) {
            $msg = "Unable to process your request, Please try again!";
            $type = 'error';
            Session::flash($type, $msg);
            return $msg;
        }
    }
}
