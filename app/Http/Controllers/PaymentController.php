<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Equb;
use App\Models\EqubType;
use App\Models\Member;
use App\Models\Payment;
use Exception;
use App\Repositories\Payment\IPaymentRepository;
use App\Repositories\Member\IMemberRepository;
use App\Repositories\Equb\IEqubRepository;
use App\Repositories\EqubTaker\IEqubTakerRepository;
use App\Repositories\ActivityLog\IActivityLogRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

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
        $this->activityLogRepository = $activityLogRepository;
        $this->paymentRepository = $paymentRepository;
        $this->memberRepository = $memberRepository;
        $this->equbRepository = $equbRepository;
        $this->equbTakerRepository = $equbTakerRepository;
        $this->title = "Virtual Equb - Payment";
    }
    public function create()
    {
        /** @var App\Models\User */
        try {
            $userData = Auth::user();
            $adminRoles = ['admin', 'general_manager', 'operation_manager', 'it'];
            $collector = ['equb_collector'];
            $member = ['member'];
            if ($userData) {
                // if ($userData->hasAnyRole($adminRoles)) {
                    $data['title'] = $this->title;
                    return view('admin/payment/addPayment', $data);
                // } if ($userData->hasRole($collector)) {
                    $data['title'] = $this->title;
                    return view('equbCollecter/payment/addPayment', $data);
                // } if ($userData->hasRole($member)) {
                    $data['title'] = $this->title;
                    return view('member/payment/addPayment', $data);
                // } 
            }
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
            $adminRoles = ['admin', 'general_manager', 'operation_manager', 'it', 'equb_collector'];
            $collector = ['equb_collector'];
            $member = ['member'];
            // if ($userData->hasAnyRole($adminRoles)) {
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
                    $remainingPayment =  max(0, $totalEqubAmount - $totalPpayment);
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
                        return redirect()->back()->with('error', 'Failed to send SMS');
                    };
                    $msg = "Congratulations! Payment has been registered successfully!";
                    $type = 'success';
                    Session::flash($type, $msg);
                    return redirect('/member');
                } else {
                    $msg = "Unknown Error Occurred, Please try again!";
                    $type = 'error';
                    Session::flash($type, $msg);
                    redirect('/member');
                }
            // } else {
            //     return view('auth/login');
            // }
        } catch (Exception $ex) {
            // dd($ex);
            $msg = "Unknown Error Occurred, Please try again!";
            $type = 'error';
            Session::flash($type, $msg);
            return back();
        }
    }
    public function storeFromChapa(Request $request)
    {
        try {
            $userData = Auth::user();
            $adminRoles = ['admin', 'general_manager', 'operation_manager', 'it', 'equb_collector'];
            $member = ['member'];
            // if ($userData && $userData->hasAnyRole($adminRoles)) {
                $this->validate($request, [
                    // 'payment_type' => 'required',
                    'amount' => 'required',
                    'equb_id' => 'required',
                    // 'creadit' => 'required',
                    // 'remark' => 'required',
                ]);
                $amount = $request->input('amount');
                $equb = Equb::where('id', $request->input('equb_id')->first());
                $equb_amount = $equb->amount;
                $diff = $equb_amount - $amount;
                // dd($diff);
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
                    $msg = "Congratulations! Payment has been registered successfully!";
                    $type = 'success';
                    Session::flash($type, $msg);
                    return redirect('/member');
                } else {
                    $msg = "Unknown Error Occurred, Please try again!";
                    $type = 'error';
                    Session::flash($type, $msg);
                    redirect('/member');
                }
            // } else {
            //     return view('auth/login');
            // }
        } catch (Exception $ex) {
            // dd($ex);
            $msg = $ex->getMessage();
            $type = 'error';
            Session::flash($type, $msg);
            return back();
        }
    }
    public function index($member_id, $equb_id)
    {
        /** @var App\Models\User */
        try {
            $offset = 0;
            $limit = 100;
            $pageNumber = 1;
            $userData = Auth::user();
            
            if ($userData) {
                $adminRoles = ['admin', 'general_manager', 'operation_manager', 'it', 'finance','call_center'];
                $member = ['member'];
                $collector = ['equb_collector'];
                if ($userData->hasAnyRole($adminRoles)) {
                    $paymentData['member'] = $this->memberRepository->getMemberById($member_id);
                    $paymentData['equb'] = $this->equbRepository->geteEubById($equb_id);
                    $paymentData['payments'] = $this->paymentRepository->getSinglePayment($member_id, $equb_id, $offset);
                    // dd($paymentData['payments']);
                    $paymentData['totalCredit'] = $this->paymentRepository->getTotalCredit($equb_id);
                    $paymentData['totalPaid'] = $this->paymentRepository->getTotalPaid($equb_id);
                    $paymentData['total'] = $this->paymentRepository->getTotalCount($equb_id);
                    $paymentData['offset'] = $offset;
                    $paymentData['limit'] = $limit;
                    $paymentData['pageNumber'] = $pageNumber;
                    return view('admin/payment.paymentList', $paymentData);
                } elseif ($userData->hasAnyRole($collector)) {
                    $paymentData['member'] = $this->memberRepository->getMemberById($member_id);
                    $paymentData['equb'] = $this->equbRepository->geteEubById($equb_id);
                    $paymentData['payments'] = $this->paymentRepository->getSinglePayment($member_id, $equb_id, $offset);
                    $paymentData['totalCredit'] = $this->paymentRepository->getTotalCredit($equb_id);
                    $paymentData['totalPaid'] = $this->paymentRepository->getTotalPaid($equb_id);
                    $paymentData['total'] = $this->paymentRepository->getTotalCount($equb_id);
                    $paymentData['offset'] = $offset;
                    $paymentData['limit'] = $limit;
                    $paymentData['pageNumber'] = $pageNumber;
                    return view('equbCollecter/payment.paymentList', $paymentData);
                } elseif ($userData->hasAnyRole($member)) {
                    $paymentData['member'] = $this->memberRepository->getMemberById($member_id);
                    $paymentData['equb'] = $this->equbRepository->geteEubById($equb_id);
                    $paymentData['payments'] = $this->paymentRepository->getSinglePayment($member_id, $equb_id, $offset);
                    $paymentData['totalCredit'] = $this->paymentRepository->getTotalCredit($equb_id);
                    $paymentData['totalPaid'] = $this->paymentRepository->getTotalPaid($equb_id);
                    $paymentData['total'] = $this->paymentRepository->getTotalCount($equb_id);
                    $paymentData['offset'] = $offset;
                    $paymentData['limit'] = $limit;
                    $paymentData['pageNumber'] = $pageNumber;
                    return view('member/payment.paymentList', $paymentData);
                } else {
                    return back();
                };
            }
            
        } catch (Exception $ex) {
            $msg = $ex->getMessage();
            $type = 'error';
            Session::flash($type, $msg);
            return back();
        }
    }
    public function indexAll()
    {
        try {
            $userData = Auth::user();
            $adminRoles = ['admin', 'general_manager', 'operation_manager', 'it', 'equb_collector'];
            $member = ['member'];
            // if ($userData->hasAnyRole($adminRoles)) {
                $this->middleware('auth');
                $data['title'] = $this->title;
                $data['paids'] = Payment::where('status', 'pending')->with('member')->get();
                return view('admin/payment.pendingPaymentList', $data);
            // } else {
            //     return view('auth/login');
            // }
        } catch (Exception $ex) {
            $msg = $ex->getMessage();
            $type = 'error';
            Session::flash($type, $msg);
            return back();
        }
    }
    public function paidPayment()
    {
        try {
            $userData = Auth::user();
            $adminRoles = ['admin', 'general_manager', 'operation_manager', 'it', 'equb_collector'];
            $member = ['member'];
            // if ($userData->hasAnyRole($adminRoles)) {
                $this->middleware('auth');
                $data['title'] = $this->title;
                $data['paids'] = Payment::where('status', 'pending')->with('member')->get();
                return view('admin/payment.paidPaymentList', $data);
            // } else {
            //     return view('auth/login');
            // }
            // } else {
            //     return view('auth/login');
            // }
        } catch (Exception $ex) {
            $msg = $ex->getMessage();
            $type = 'error';
            Session::flash($type, $msg);
            return back();
        }
    }
 
    // public function indexPendingPaginate($offsetVal, $pageNumberVal)
    // {
    //     try {
    //         $offset = $offsetVal;
    //         $limit = 10;
    //         $pageNumber = $pageNumberVal;
    //         $userData = Auth::user();
    //         if ($userData && ($userData['role'] == "admin" || $userData['role'] == "general_manager" || $userData['role'] == "operation_manager" || $userData['role'] == "it" || $userData['role'] == "finance")) {
    //             // $paymentData['member'] = $this->memberRepository->getMemberById($member_id);
    //             // $paymentData['equb'] = $this->equbRepository->geteEubById($equb_id);
    //             $paymentData['payments'] = $this->paymentRepository->getAllPendingByPaginate($offset);
    //             // $paymentData['totalCredit'] = $this->paymentRepository->getTotalCredit($equb_id);
    //             // $paymentData['totalPaid'] = $this->paymentRepository->getTotalPaid($equb_id);
    //             $paymentData['total'] = $this->paymentRepository->countPendingPayments();
    //             $paymentData['offset'] = $offset;
    //             $paymentData['limit'] = $limit;
    //             $paymentData['pageNumber'] = $pageNumber;
    //             $paymentData['title'] = $this->title;
    //             // dd($paymentData);
    //             return view('admin/payment.pendingPaymentList', $paymentData);
    //         }
    //         // elseif ($userData && ($userData['role'] == "equb_collector")) {
    //         //     $paymentData['member'] = $this->memberRepository->getMemberById($member_id);
    //         //     $paymentData['equb'] = $this->equbRepository->geteEubById($equb_id);
    //         //     $paymentData['payments'] = $this->paymentRepository->getSinglePayment($member_id, $equb_id, $offset);
    //         //     $paymentData['totalCredit'] = $this->paymentRepository->getTotalCredit($equb_id);
    //         //     $paymentData['totalPaid'] = $this->paymentRepository->getTotalPaid($equb_id);
    //         //     $paymentData['total'] = $this->paymentRepository->getTotalCount($equb_id);
    //         //     $paymentData['offset'] = $offset;
    //         //     $paymentData['limit'] = $limit;
    //         //     $paymentData['pageNumber'] = $pageNumber;
    //         //     return view('equbCollecter/payment.paymentList', $paymentData);
    //         // } elseif ($userData && ($userData['role'] == "member")) {
    //         //     $paymentData['member'] = $this->memberRepository->getMemberById($member_id);
    //         //     $paymentData['equb'] = $this->equbRepository->geteEubById($equb_id);
    //         //     $paymentData['payments'] = $this->paymentRepository->getSinglePayment($member_id, $equb_id, $offset);
    //         //     $paymentData['totalCredit'] = $this->paymentRepository->getTotalCredit($equb_id);
    //         //     $paymentData['totalPaid'] = $this->paymentRepository->getTotalPaid($equb_id);
    //         //     $paymentData['total'] = $this->paymentRepository->getTotalCount($equb_id);
    //         //     $paymentData['offset'] = $offset;
    //         //     $paymentData['limit'] = $limit;
    //         //     $paymentData['pageNumber'] = $pageNumber;
    //         //     return view('member/payment.paymentList', $paymentData);
    //         // }
    //         else {
    //             return back();
    //         };
    //     } catch (Exception $ex) {
    //         $msg = "Unable to process your request, Please try again!";
    //         $type = 'error';
    //         Session::flash($type, $msg);
    //         return back();
    //     }
    // }
    public function searchPaidPayment($searchInput, $offset, $pageNumber = null)
    {
        // dd($searchInput);
        try {
            $userData = Auth::user();
            $adminRoles = ['admin', 'general_manager', 'operation_manager', 'it', 'equb_collector', 'call_center'];
            $member = ['member'];
            $collector = ['equb_collector'];
            // if ($userData->hasAnyRole($adminRoles)) {
                $data['offset'] = $offset;
                $limit = 50;
                $data['limit'] = $limit;
                $data['totalPayments'] = $this->paymentRepository->searchPaidPaymentCount($searchInput);
                if ($offset == 0) {
                    $data['pageNumber'] = 1;
                } else {
                    $data['pageNumber'] = $pageNumber;
                }
                $data['searchInput'] = $searchInput;
                $data['payments'] = $this->paymentRepository->searchPaidPayment($offset, $searchInput);
                // dd($data);
                return view('admin/payment.searchPendingPaymentTable', $data);
            // } elseif ($userData->hasRole($collector)) {
                $data['offset'] = $offset;
                $limit = 50;
                $data['limit'] = $limit;
                $data['totalPayments'] = $this->paymentRepository->searchPendingPaymentCount($searchInput);
                if ($offset == 0) {
                    $data['pageNumber'] = 1;
                } else {
                    $data['pageNumber'] = $pageNumber;
                }
                $data['searchInput'] = $searchInput;
                $data['payments'] = $this->paymentRepository->searchPendingPayment($offset, $searchInput);
                return view('admin/payment.searchPendingPaymentTable', $data);
            // }
        } catch (Exception $ex) {
            $msg = $ex->getMessage();
            $type = 'error';
            Session::flash($type, $msg);
            return back();
        }
    }
    public function searchPendingPayment($searchInput, $offset, $pageNumber = null)
    {
        // dd($searchInput);
        try {
            $userData = Auth::user();
            $adminRoles = ['admin', 'general_manager', 'operation_manager', 'it', 'equb_collector', 'call_center'];
            $member = ['member'];
            $collector = ['equb_collector'];
            // if ($userData->hasAnyRole($adminRoles)) {
                $data['offset'] = $offset;
                $limit = 50;
                $data['limit'] = $limit;
                $data['totalPayments'] = $this->paymentRepository->searchPendingPaymentCount($searchInput);
                if ($offset == 0) {
                    $data['pageNumber'] = 1;
                } else {
                    $data['pageNumber'] = $pageNumber;
                }
                $data['searchInput'] = $searchInput;
                $data['payments'] = $this->paymentRepository->searchPendingPayment($offset, $searchInput);
                // dd($data);
                return view('admin/payment.searchPendingPaymentTable', $data);
            // } elseif ($userData->hasRole($collector)) {
                $data['offset'] = $offset;
                $limit = 50;
                $data['limit'] = $limit;
                $data['totalPayments'] = $this->paymentRepository->searchPendingPaymentCount($searchInput);
                if ($offset == 0) {
                    $data['pageNumber'] = 1;
                } else {
                    $data['pageNumber'] = $pageNumber;
                }
                $data['searchInput'] = $searchInput;
                $data['payments'] = $this->paymentRepository->searchPendingPayment($offset, $searchInput);
                return view('admin/payment.searchPendingPaymentTable', $data);
            // }
        } catch (Exception $ex) {
            $msg = $ex->getMessage();
            $type = 'error';
            Session::flash($type, $msg);
            return back();
        }
    }
    public function clearPendingSearchEntry()
    {
        try {
            $offset = 0;
            $limit = 10;
            $pageNumber = 1;
            $userData = Auth::user();
            $adminRoles = ['admin', 'general_manager', 'operation_manager', 'it', 'equb_collector', 'assistant', 'call_center'];
            $member = ['member'];
            $collector = ['equb_collector'];
            // if ($userData->hasAnyRole($adminRoles)) {
                $data['payments']  = $this->paymentRepository->getAllPendingByPaginate($offset);
                $data['totalPayments']  = $this->paymentRepository->countPendingPayments();
                $data['pageNumber'] = $pageNumber;
                $data['offset'] = $offset;
                $data['limit'] = $limit;
                $title = $this->title;
                return view('admin/payment.pendingPaymentTable', $data);
            // }
        } catch (Exception $ex) {
            // dd($ex);
            $msg = $ex->getMessage();
            $type = 'error';
            Session::flash($type, $msg);
            return back();
        }
    }
    public function indexPendingPaginate($offsetVal, $pageNumberVal)
    {
        // dd("hello");
        try {
            $limit = 10;
            $offset = $offsetVal;
            $pageNumber = $pageNumberVal;
            $userData = Auth::user();
            $adminRoles = ['admin', 'general_manager', 'operation_manager', 'it', 'equb_collector', 'finance', 'call_center'];
            $member = ['member'];
            $collector = ['equb_collector'];
            if ($userData->hasAnyRole($adminRoles)) {
                // $totalMember = $this->memberRepository->getPendingMembers();
                $payments = $this->paymentRepository->getAllPendingByPaginate($offset);
                // $totalPaid = $this->paymentRepository->getTotalPaid($equb_id);
                // $equbTypes = $this->equbTypeRepository->getActive();
                // $equbs = $this->equbRepository->getAll();
                $totalPayments = $this->paymentRepository->countPendingPayments();
                $title = $this->title;
                return view('admin/payment.pendingPaymentTable', compact('title', 'payments', 'pageNumber', 'offset', 'limit', 'totalPayments'));
                // return view('admin/payment.pendingPaymentList', compact('title', 'equbTypes', 'members', 'equbs', 'payments', 'pageNumber', 'offset', 'limit', 'totalPayments'));
            } elseif ($userData->hasRole($collector)) {
                // $totalMember = $this->memberRepository->getPendingMembers();
                $members = $this->memberRepository->getAllPendingByPaginate($offset);
                // $totalPaid = $this->paymentRepository->getTotalPaid($equb_id);
                // $equbTypes = $this->equbTypeRepository->getActive();
                // $equbs = $this->equbRepository->getAll();
                $payments = $this->paymentRepository->countPendingPayments();
                $title = $this->title;
                return view('equbCollecter/member.pendingMemberTable', compact('title', 'payments', 'pageNumber', 'offset', 'limit', 'totalPayments'));
                // return view('equbCollecter/member.pendingMemberTable', compact('title', 'members', 'equbTypes', 'equbs', 'payments', 'pageNumber', 'offset', 'limit', 'totalMember'));
            } else {
                return view('auth/login');
            }
        } catch (Exception $ex) {
            $msg = $ex->getMessage();
            $type = 'error';
            Session::flash($type, $msg);
            return back();
        }
    }
    public function indexPaidPaginate($offsetVal, $pageNumberVal)
    {
        // dd("hello");
        try {
            $limit = 10;
            $offset = $offsetVal;
            $pageNumber = $pageNumberVal;
            $userData = Auth::user();
            $adminRoles = ['admin', 'general_manager', 'operation_manager', 'it', 'equb_collector', 'finance', 'call_center'];
            $member = ['member'];
            $collector = ['equb_collector'];
            if ($userData->hasAnyRole($adminRoles)) {
                // $totalMember = $this->memberRepository->getPendingMembers();
                $payments = $this->paymentRepository->getAllPaidByPaginate($offset);
                // $totalPaid = $this->paymentRepository->getTotalPaid($equb_id);
                // $equbTypes = $this->equbTypeRepository->getActive();
                // $equbs = $this->equbRepository->getAll();
                $totalPayments = $this->paymentRepository->countPendingPayments();
                $title = $this->title;
                return view('admin/payment.pendingPaymentTable', compact('title', 'payments', 'pageNumber', 'offset', 'limit', 'totalPayments'));
                // return view('admin/payment.pendingPaymentList', compact('title', 'equbTypes', 'members', 'equbs', 'payments', 'pageNumber', 'offset', 'limit', 'totalPayments'));
            } elseif ($userData->hasRole($collector)) {
                // $totalMember = $this->memberRepository->getPendingMembers();
                $members = $this->memberRepository->getAllPendingByPaginate($offset);
                // $totalPaid = $this->paymentRepository->getTotalPaid($equb_id);
                // $equbTypes = $this->equbTypeRepository->getActive();
                // $equbs = $this->equbRepository->getAll();
                $payments = $this->paymentRepository->countPendingPayments();
                $title = $this->title;
                return view('equbCollecter/member.pendingMemberTable', compact('title', 'payments', 'pageNumber', 'offset', 'limit', 'totalPayments'));
                // return view('equbCollecter/member.pendingMemberTable', compact('title', 'members', 'equbTypes', 'equbs', 'payments', 'pageNumber', 'offset', 'limit', 'totalMember'));
            } else {
                return view('auth/login');
            }
        } catch (Exception $ex) {
            $msg = $ex->getMessage();
            $type = 'error';
            Session::flash($type, $msg);
            return back();
        }
    }
 
    public function show($member_id, $equb_id, $offsetVal, $pageNumberVal)
    {
        try {
            $limit = 10;
            $offset = $offsetVal;
            $pageNumber = $pageNumberVal;
            $userData = Auth::user();
            $adminRoles = ['admin', 'general_manager', 'operation_manager', 'it', 'equb_collector'];
            $member = ['member'];
            $collector = ['equb_collector'];
            if ($userData->hasAnyRole($adminRoles)) {
                $paymentData['member'] = $this->memberRepository->getMemberById($member_id);
                $paymentData['equb'] = $this->equbRepository->geteEubById($equb_id);
                $paymentData['payments'] = $this->paymentRepository->getSinglePayment($member_id, $equb_id, $offset);
                $paymentData['totalCredit'] = $this->paymentRepository->getTotalCredit($equb_id);
                $paymentData['totalPaid'] = $this->paymentRepository->getTotalPaid($equb_id);
                $paymentData['total'] = $this->paymentRepository->getTotalCount($equb_id);
                $paymentData['offset'] = $offset;
                $paymentData['limit'] = $limit;
                $paymentData['pageNumber'] = $pageNumber;
        //    /     return view('admin/payment.paymentList', $paymentData);
            } elseif ($userData->hasRole($collector)) {
                $paymentData['member'] = $this->memberRepository->getMemberById($member_id);
                $paymentData['equb'] = $this->equbRepository->geteEubById($equb_id);
                $paymentData['payments'] = $this->paymentRepository->getSinglePayment($member_id, $equb_id, $offset);
                $paymentData['totalCredit'] = $this->paymentRepository->getTotalCredit($equb_id);
                $paymentData['totalPaid'] = $this->paymentRepository->getTotalPaid($equb_id);
                $paymentData['total'] = $this->paymentRepository->getTotalCount($equb_id);
                $paymentData['offset'] = $offset;
                $paymentData['limit'] = $limit;
                $paymentData['pageNumber'] = $pageNumber;
                return view('equbCollecter/payment.paymentList', $paymentData);
            } elseif ($userData->hasRole($member)) {
                $paymentData['member'] = $this->memberRepository->getMemberById($member_id);
                $paymentData['equb'] = $this->equbRepository->geteEubById($equb_id);
                $paymentData['payments'] = $this->paymentRepository->getSinglePayment($member_id, $equb_id, $offset);
                $paymentData['totalCredit'] = $this->paymentRepository->getTotalCredit($equb_id);
                $paymentData['totalPaid'] = $this->paymentRepository->getTotalPaid($equb_id);
                $paymentData['total'] = $this->paymentRepository->getTotalCount($equb_id);
                $paymentData['offset'] = $offset;
                $paymentData['limit'] = $limit;
                $paymentData['pageNumber'] = $pageNumber;
                return view('member/payment.paymentList', $paymentData);
            } else {
                return back();
            };
        } catch (Exception $ex) {
            $msg = $ex->getMessage();
            $type = 'error';
            Session::flash($type, $msg);
            return back();
        }
    }
    public function updatePayment($member, $equb_id, $id, Request $request)
    {
        try {
            $userData = Auth::user();
            $adminRoles = ['admin', 'general_manager', 'operation_manager', 'it', 'equb_collector'];
            $member = ['member'];
            $collector = ['equb_collector'];
            // if ($userData->hasAnyRole($adminRoles)) {
                $paymentType = $request->input('update_payment_type');
                $amount = $request->input('update_amount');
                $credit = $request->input('update_creadit');
                $remark = $request->input('update_payment_remark');
                
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
                    'payment_type' => $paymentType,
                    'amount' => $amount,
                    'note' => $remark,
                    'collecter' => $userData->id,
                    'creadit' => $totalCredit,
                    'balance' => $availableBalance
                ];
                
                $updatedCreditAndBalance = $this->paymentRepository->update($lastId, $updateCreditAndBalance);
                // if ($updated && $updatedCreditAndBalance) {
                if ($updatedCreditAndBalance) {
                    $activityLog = [
                        'type' => 'payments',
                        'type_id' => $id,
                        'action' => 'updated',
                        'user_id' => $userData->id,
                        'username' => $userData->name,
                        'role' => $userData->role,
                    ];
                    $this->activityLogRepository->createActivityLog($activityLog);
                    $msg = "Payment detail has been updated successfully!";
                    $type = 'success';
                    Session::flash($type, $msg);
                    return redirect('member/');
                } else {
                    $msg = "Unknown error occurred, Please try again!";
                    $type = 'error';
                    Session::flash($type, $msg);
                    return back();
                }
            // } else {
            //     return view('auth/login');
            // }
        } catch (Exception $ex) {
            // dd($ex);
            $msg = $ex->getMessage();
            $type = 'error';
            Session::flash($type, $msg);
            return back();
        }
    }
    public function updatePendingPayment($member, $equb_id, $id, Request $request)
    {
        try {
            $userData = Auth::user();
            $adminRoles = ['admin', 'general_manager', 'operation_manager', 'it', 'equb_collector'];
            $member = ['member'];
            $collector = ['equb_collector'];
            if ($userData->hasAnyRole($adminRoles)) {
                $paymentType = $request->input('update_payment_type');
                $amount = $request->input('update_amount');
                $credit = $request->input('update_creadit');
                $remark = $request->input('update_payment_remark');
                
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
                // $updated = $this->paymentRepository->update($id, $updated);
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
                    'payment_type' => $paymentType,
                    'amount' => $amount,
                    'note' => $remark,
                    'collecter' => $userData->id,
                    'creadit' => $totalCredit,
                    'balance' => $availableBalance
                ];
                //
                $updatedCreditAndBalance = $this->paymentRepository->update($lastId, $updateCreditAndBalance);
                
                if ($updatedCreditAndBalance) {
                    $activityLog = [
                        'type' => 'payments',
                        'type_id' => $id,
                        'action' => 'updated',
                        'user_id' => $userData->id,
                        'username' => $userData->name,
                        'role' => $userData->role,
                    ];
                    $this->activityLogRepository->createActivityLog($activityLog);
                    $msg = "Payment detail has been updated successfully!";
                    $type = 'success';
                    Session::flash($type, $msg);
                    return redirect('payment/show-all-pending-payment');
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
            // dd($ex);
            $msg = $ex->getMessage();
            $type = 'error';
            Session::flash($type, $msg);
            return back();
        }
    }
    public function deletePayment($member_id, $equb_id, $id)
    {
        try {
            $userData = Auth::user();
            $adminRoles = ['admin', 'general_manager', 'operation_manager', 'it', 'equb_collector'];
            $member = ['member'];
            $collector = ['equb_collector'];
            // if ($userData->hasAnyRole($adminRoles)) {
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
                        $msg = "Payment has been deleted successfully!";
                        $type = 'success';
                        Session::flash($type, $msg);
                        return redirect('payment/show-payment/' . $member_id . '/' . $equb_id);
                    } else {
                        $msg = "Unknown Error Occurred, Please try again!";
                        $type = 'error';
                        Session::flash($type, $msg);
                        redirect('/member');
                    }
                } else {
                    return false;
                }
            // } else {
            //     return view('auth/login');
            // }
        } catch (Exception $ex) {
            $msg = $ex->getMessage();
            $type = 'error';
            Session::flash($type, $msg);
            return $msg;
        }
    }
    public function deleteAllPayment($member_id, $equb_id)
    {
        try {
            $userData = Auth::user();
            $adminRoles = ['admin', 'general_manager', 'operation_manager', 'it', 'equb_collector'];
            $member = ['member'];
            $collector = ['equb_collector'];
            if ($userData->hasAnyRole($adminRoles)) {
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
                        $msg = "All payments of this member has been deleted successfully!";
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
            $msg = $ex->getMessage();
            $type = 'error';
            Session::flash($type, $msg);
            return $msg;
        }
    }
    public function destroy($id)
    {
        try {
            $userData = Auth::user();
            $adminRoles = ['admin', 'general_manager', 'operation_manager', 'it', 'equb_collector'];
            $member = ['member'];
            $collector = ['equb_collector'];
            if ($userData->hasAnyRole($adminRoles)) {
                $payment = $this->paymentRepository->getById($id);
                if ($payment != null) {
                    $deleted = $this->paymentRepository->forceDelete($id);
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
                        $msg = "Payment has been deleted successfully!";
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
            $msg = $ex->getMessage();
            $type = 'error';
            Session::flash($type, $msg);
            return $msg;
        }
    }
    public function destroyPending($id)
    {
        try {
            $userData = Auth::user();
            $adminRoles = ['admin', 'general_manager', 'operation_manager', 'it', 'equb_collector'];
            $member = ['member'];
            $collector = ['equb_collector'];
            if ($userData->hasAnyRole($adminRoles)) {
                $payment = $this->paymentRepository->getById($id);
                if ($payment != null) {
                    $deleted = $this->paymentRepository->forceDelete($id);
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
                        $msg = "Payment has been deleted successfully!";
                        $type = 'success';
                        Session::flash($type, $msg);
                        return redirect('payment/show-all-pending-payment');
                    } else {
                        $msg = "Unknown Error Occurred, Please try again!";
                        $type = 'error';
                        Session::flash($type, $msg);
                        return redirect('payment/show-all-pending-payment');
                    }
                } else {
                    return false;
                }
            } else {
                return view('auth/login');
            }
        } catch (Exception $ex) {
            $msg = $ex->getMessage();
            $type = 'error';
            Session::flash($type, $msg);
            return $msg;
        }
    }
    public function approvePayment($id, Request $request)
    {
        // dd($id);
        try {
            // return response()->json($request);
            $userData = Auth::user();
            $adminRoles = ['admin', 'general_manager', 'operation_manager', 'it', 'equb_collector'];
            $member = ['member'];
            $collector = ['equb_collector'];
            // if ($userData->hasAnyRole($adminRoles)) {
                $payment = $this->paymentRepository->getById($id);
                // dd($payment);
                $updated = [
                    'status' => 'paid',
                ];
                // dd($updated);
                $updated = $this->paymentRepository->update($id, $updated);
                if ($updated) {
                    $notifiedMember = Member::where('id', $payment->member_id)->first();
                    $equb = $this->equbRepository->getById($payment->equb_id);
                    // $equbType = $equb->eqybType;
                    $equbType = EqubType::where('id', $equb->equb_type_id)->first();
                    // dd($equbType);
                    $totalPpayment = $this->paymentRepository->getTotalPaid($payment->equb_id);
                    $totalEqubAmount = $this->equbRepository->getTotalEqubAmount($payment->equb_id);
                    $remainingPayment =  $totalEqubAmount - $totalPpayment;
                    $lotteryDateList = explode(",", $equb->lottery_date);
                    $dates = collect($lotteryDateList)->map(function ($date) {
                        return Carbon::parse($date);
                    });
                    $maxDate = $dates->max();
                    $lotDate = $equbType->type == 'Automatic' ? $equbType->lottery_date : $maxDate->toDateString();
                    try {
                        $shortcode = config('key.SHORT_CODE');
                        $message = "You have successfully paid $payment->amount ETB and a total of $totalPpayment ETB for the equb $equbType->name. Your remaining unpaid amount is $remainingPayment ETB. Your lottery date is $lotDate" . ". For further information please call " . $shortcode;
                        $this->sendSms($notifiedMember->phone, $message);
                        if ($remainingPayment == 0) {
                            $paymentMessage = "You have successfully finished your payment of $totalPpayment ETB for the equb $equbType->name" . ". For further information please call " . $shortcode;
                            $this->sendSms($notifiedMember->phone, $paymentMessage);
                        }
                    } catch (Exception $ex) {
                        return redirect()->back()->with('error', 'Failed to send SMS');
                    };
                    $activityLog = [
                        'type' => 'payments',
                        'type_id' => $id,
                        'action' => 'approve',
                        'user_id' => $userData->id,
                        'username' => $userData->name,
                        'role' => $userData->role,
                    ];
                    $this->activityLogRepository->createActivityLog($activityLog);
                    $msg = "Status has been updated seccessfully";
                    $type = 'success';
                    Session::flash($type, $msg);
                    return redirect('member/');
                } else {
                    $msg = "Unknown error occurred, Please try again!";
                    $type = 'error';
                    Session::flash($type, $msg);
                    return redirect('member/');
                }
            // } else {
            //     return view('auth/login');
            // }
        } catch (Exception $ex) {
            // dd($ex);
            $msg = $ex->getMessage();
            $type = 'error';
            Session::flash($type, $msg);
            return $msg;
        }
    }
    public function approvePendingPayment($id, Request $request)
    {
        // dd($id);
        try {
            // return response()->json($request);
            $userData = Auth::user();
            $adminRoles = ['admin', 'general_manager', 'operation_manager', 'it', 'equb_collector'];
            $member = ['member'];
            $collector = ['equb_collector'];
            // if ($userData->hasAnyRole($adminRoles)) {
                $payment = $this->paymentRepository->getById($id);
                // dd($payment);
                $updated = [
                    'status' => 'paid',
                ];
                // dd($updated);
                $updated = $this->paymentRepository->update($id, $updated);
                if ($updated) {
                    $notifiedMember = Member::where('id', $payment->member_id)->first();
                    $equb = $this->equbRepository->getById($payment->equb_id);
                    // $equbType = $equb->eqybType;
                    $equbType = EqubType::where('id', $equb->equb_type_id)->first();
                    // dd($equbType);
                    $totalPpayment = $this->paymentRepository->getTotalPaid($payment->equb_id);
                    $totalEqubAmount = $this->equbRepository->getTotalEqubAmount($payment->equb_id);
                    $remainingPayment =  $totalEqubAmount - $totalPpayment;
                    $lotteryDateList = explode(",", $equb->lottery_date);
                    $dates = collect($lotteryDateList)->map(function ($date) {
                        return Carbon::parse($date);
                    });
                    $maxDate = $dates->max();
                    $lotDate = $equbType->type == 'Automatic' ? $equbType->lottery_date : $maxDate->toDateString();
                    try {
                        $shortcode = config('key.SHORT_CODE');
                        $message = "You have successfully paid $payment->amount ETB and a total of $totalPpayment ETB for the equb $equbType->name. Your remaining unpaid amount is $remainingPayment ETB. Your lottery date is $lotDate" . ". For further information please call " . $shortcode;
                        $this->sendSms($notifiedMember->phone, $message);
                        if ($remainingPayment == 0) {
                            $paymentMessage = "You have successfully finished your payment of $totalPpayment ETB for the equb $equbType->name" . ". For further information please call " . $shortcode;
                            $this->sendSms($notifiedMember->phone, $paymentMessage);
                        }
                    } catch (Exception $ex) {
                        return redirect()->back()->with('error', 'Failed to send SMS');
                    };
                    $activityLog = [
                        'type' => 'payments',
                        'type_id' => $id,
                        'action' => 'approve',
                        'user_id' => $userData->id,
                        'username' => $userData->name,
                        'role' => $userData->role,
                    ];
                    $this->activityLogRepository->createActivityLog($activityLog);
                    $msg = "Status has been updated seccessfully";
                    $type = 'success';
                    Session::flash($type, $msg);
                    return redirect('payment/show-all-pending-payment');
                } else {
                    $msg = "Unknown error occurred, Please try again!";
                    $type = 'error';
                    Session::flash($type, $msg);
                    return redirect('payment/show-all-pending-payment');
                }
            // } else {
            //     return view('auth/login');
            // }
        } catch (Exception $ex) {
            // dd($ex);
            $msg = $ex->getMessage();
            $type = 'error';
            Session::flash($type, $msg);
            return $msg;
        }
    }
    public function rejectpayment($id, Request $request)
    {
        try {
            $userData = Auth::user();
            $adminRoles = ['admin', 'general_manager', 'operation_manager', 'it', 'equb_collector'];
            $member = ['member'];
            $collector = ['equb_collector'];
            // if ($userData->hasAnyRole($adminRoles)) {
                $payment = $this->paymentRepository->getById($id);
                $updated = [
                    'status' => 'unpaid',
                ];
                $updated = $this->paymentRepository->update($id, $updated);
                if ($updated) {
                    $activityLog = [
                        'type' => 'payments',
                        'type_id' => $id,
                        'action' => 'reject',
                        'user_id' => $userData->id,
                        'username' => $userData->name,
                        'role' => $userData->role,
                    ];
                    $this->activityLogRepository->createActivityLog($activityLog);
                    try {
                        $notifiedMember = Member::where('id', $payment->member_id)->first();
                        $shortcode = config('key.SHORT_CODE');
                        $message = "Your payment of " . $payment->amount . "ETB has been rejected. Please try again." . " For further information please call " . $shortcode;
                        $this->sendSms($notifiedMember->phone, $message);
                    } catch (Exception $ex) {
                        return redirect()->back()->with('error', 'Failed to send SMS');
                    };
                    $msg = "Status has been updated seccessfully";
                    $type = 'success';
                    Session::flash($type, $msg);
                    return redirect('member/');
                } else {
                    $msg = "Unknown error occurred, Please try again!";
                    $type = 'error';
                    Session::flash($type, $msg);
                    return redirect('member/');
                }
            // } else {
            //     return view('auth/login');
            // }
        } catch (Exception $ex) {
            $msg = $ex->getMessage();
            $type = 'error';
            Session::flash($type, $msg);
            return $msg;
        }
    }
    public function rejectPendingPayment($id, Request $request)
    {
        try {
            $userData = Auth::user();
            $adminRoles = ['admin', 'general_manager', 'operation_manager', 'it', 'equb_collector'];
            $member = ['member'];
            $collector = ['equb_collector'];
            // if ($userData->hasAnyRole($adminRoles)) {
                $payment = $this->paymentRepository->getById($id);
                $updated = [
                    'status' => 'unpaid',
                ];
                $updated = $this->paymentRepository->update($id, $updated);
                if ($updated) {
                    $activityLog = [
                        'type' => 'payments',
                        'type_id' => $id,
                        'action' => 'reject',
                        'user_id' => $userData->id,
                        'username' => $userData->name,
                        'role' => $userData->role,
                    ];
                    $this->activityLogRepository->createActivityLog($activityLog);
                    try {
                        $notifiedMember = Member::where('id', $payment->member_id)->first();
                        $shortcode = config('key.SHORT_CODE');
                        $message = "Your payment of " . $payment->amount . "ETB has been rejected. Please try again." . " For further information please call " . $shortcode;
                        $this->sendSms($notifiedMember->phone, $message);
                    } catch (Exception $ex) {
                        return redirect()->back()->with('error', 'Failed to send SMS');
                    };
                    $msg = "Status has been updated seccessfully";
                    $type = 'success';
                    Session::flash($type, $msg);
                    return redirect('payment/show-all-pending-payment');
                } else {
                    $msg = "Unknown error occurred, Please try again!";
                    $type = 'error';
                    Session::flash($type, $msg);
                    return redirect('payment/show-all-pending-payment');
                }
            // } else {
            //     return view('auth/login');
            // }
        } catch (Exception $ex) {
            $msg = $ex->getMessage();
            $type = 'error';
            Session::flash($type, $msg);
            return $msg;
        }
    }
}
