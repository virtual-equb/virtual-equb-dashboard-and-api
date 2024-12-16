<?php

namespace App\Http\Controllers;

use Exception;
use App\Repositories\Member\IMemberRepository;
use App\Repositories\Payment\IPaymentRepository;
use App\Repositories\EqubType\IEqubTypeRepository;
use App\Repositories\Equb\IEqubRepository;
use App\Repositories\EqubTaker\IEqubTakerRepository;
use App\Repositories\User\IUserRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class ReportController extends Controller
{
    private $memberRepository;
    private $paymentRepository;
    private $equbTypeRepository;
    private $equbRepository;
    private $equbTakerRepository;
    private $userRepository;
    public function __construct(
        IMemberRepository $memberRepository,
        IPaymentRepository $paymentRepository,
        IEqubTypeRepository $equbTypeRepository,
        IEqubRepository $equbRepository,
        IEqubTakerRepository $equbTakerRepository,
        IUserRepository $userRepository
    ) {
        $this->memberRepository = $memberRepository;
        $this->paymentRepository = $paymentRepository;
        $this->equbTypeRepository = $equbTypeRepository;
        $this->equbRepository = $equbRepository;
        $this->equbTakerRepository = $equbTakerRepository;
        $this->userRepository = $userRepository;

        // Permission Guard
        // $this->middleware('permission:update report', ['only' => ['update', 'edit']]);
        // $this->middleware('permission:delete report', ['only' => ['destroy']]);
        // $this->middleware('permission_check_logout:view report', ['only' => [
        //     'index', 
        //     'show', 
        //     'memberFilter', 
        //     'members', 
        //     'paginateMembers', 
        //     'memberFilterByEqubType', 
        //     'membersByEqubType', 
        //     'paginateMembersByEqubType',
        //     'unPaidLotteryByDateFilter',
        //     'unPaidLotterysByDate',
        //     'paginateUnPaidLotterysByDate',
        //     'equbTypeFilter',
        //     'equbTypes',
        //     'paymentFilter',
        //     'payments',
        //     'paginatePayments',
        //     'unPaidFilter',
        //     'equbEndDates',
        //     'filterEqubEndDates',
        //     'loadMoreFilterEqubEndDates',
        //     'unPaids',
        //     'loadMoreUnPaids',
        //     'collectedByFilter',
        //     'collectedBys',
        //     'paginateCllectedBys',
        //     'lotteryFilter',
        //     'lotterys',
        //     'paginateLotterys',
        //     'equbFilter',
        //     'equbs',
        //     'paginateEqubs',
        //     'unPaidLotteryFilter',
        //     'unPaidLotterys',
        //     'paginateUnPaidLotterys',
        //     'reservedLotteryDatesFilter',
        //     'reservedLotteryDates',
        //     'paginateReservedLotteryDates'
        //     ]]);
        // $this->middleware('permission:create report', ['only' => ['store', 'create']]);
    }
    public function memberFilter()
    {
        try {
            $userData = Auth::user();
            // if ($userData && ($userData['role'] == "admin" || $userData['role'] == "general_manager" || $userData['role'] == "operation_manager" || $userData['role'] == "assistant" || $userData['role'] == "finance")) {
                $data['title'] = "Virtual Equb - Members Report";
                return view('admin/report/memberReport/members', $data);
            // } else {
            //     return view('auth/login');
            // }
        } catch (Exception $ex) {
            $msg = "Unknown Error Occurred, Please try again!";
            $type = 'error';
            Session::flash($type, $msg);
            return back();
        }
    }
    public function members($dateFrom, $dateTo)
    {
        try {
            $data['offset'] = 0;
            $offset = 0;
            $data['limit'] = 50;
            $data['pageNumber'] = 1;
            $userData = Auth::user();
            // if ($userData && ($userData['role'] == "admin" || $userData['role'] == "general_manager" || $userData['role'] == "operation_manager" || $userData['role'] == "assistant" || $userData['role'] == "finance")) {
                $data['title'] = "Virtual Equb - Members Report";
                $data['totalMember'] = $this->memberRepository->getCountByDate($dateFrom, $dateTo);
                $data['members'] = $this->memberRepository->getByDate($dateFrom, $dateTo, $offset);
                return view('admin/report/memberReport/filterMembers', $data);
            // } else {
            //     return view('auth/login');
            // }
        } catch (Exception $ex) {
            $msg = "Unknown Error Occurred, Please try again!";
            $type = 'error';
            Session::flash($type, $msg);
            return back();
        }
    }
    public function paginateMembers($dateFrom, $dateTo, $offsetVal, $pageNumberVal)
    {
        try {
            $data['offset'] = $offsetVal;
            $offset = $offsetVal;
            $data['limit'] = 50;
            $data['pageNumber'] = $pageNumberVal;
            $userData = Auth::user();
            // if ($userData && ($userData['role'] == "admin" || $userData['role'] == "general_manager" || $userData['role'] == "operation_manager" || $userData['role'] == "assistant" || $userData['role'] == "finance")) {
                $data['title'] = "Virtual Equb - Members Report";
                $data['totalMember'] = $this->memberRepository->getCountByDate($dateFrom, $dateTo);
                $data['members'] = $this->memberRepository->getByDate($dateFrom, $dateTo, $offset);
                return view('admin/report/memberReport/filterMembers', $data);
            // } else {
            //     return view('auth/login');
            // }
        } catch (Exception $ex) {
            $msg = "Unknown Error Occurred, Please try again!";
            $type = 'error';
            Session::flash($type, $msg);
            return back();
        }
    }
    public function memberFilterByEqubType()
    {
        try {
            $userData = Auth::user();
            // if ($userData && ($userData['role'] == "admin" || $userData['role'] == "general_manager" || $userData['role'] == "operation_manager" || $userData['role'] == "assistant" || $userData['role'] == "finance")) {
                $data['title'] = "Virtual Equb - Members Report";
                $data['equbTypes'] = $this->equbTypeRepository->getActive();
                return view('admin/report/memberReportByEqubType/membersByEqubType', $data);
            // } else {
            //     return view('auth/login');
            // }
        } catch (Exception $ex) {
            $msg = "Unknown Error Occurred, Please try again!";
            $type = 'error';
            Session::flash($type, $msg);
            return back();
        }
    }
    public function membersByEqubType($dateFrom, $dateTo, $equbType)
    {
        try {
            $data['offset'] = 0;
            $offset = 0;
            $data['limit'] = 50;
            $data['pageNumber'] = 1;
            $userData = Auth::user();
            // if ($userData && ($userData['role'] == "admin" || $userData['role'] == "general_manager" || $userData['role'] == "operation_manager" || $userData['role'] == "assistant" || $userData['role'] == "finance")) {
                $data['title'] = "Virtual Equb - Members Report";
                $data['totalMember'] = $this->equbRepository->getCountByDateAndEqubType($dateFrom, $dateTo, $equbType);
                $data['members'] = $this->equbRepository->getByDateAndEqubType($dateFrom, $dateTo, $equbType, $offset);
                return view('admin/report/memberReportByEqubType/filterMembersByEqubType', $data);
            // } else {
            //     return view('auth/login');
            // }
        } catch (Exception $ex) {
            $msg = "Unknown Error Occurred, Please try again!";
            $type = 'error';
            Session::flash($type, $msg);
            return back();
        }
    }
    public function paginateMembersByEqubType($dateFrom, $dateTo, $equbType, $offsetVal, $pageNumberVal)
    {
        try {
            $data['offset'] = $offsetVal;
            $offset = $offsetVal;
            $data['limit'] = 50;
            $data['pageNumber'] = $pageNumberVal;
            $userData = Auth::user();
            // if ($userData && ($userData['role'] == "admin" || $userData['role'] == "general_manager" || $userData['role'] == "operation_manager" || $userData['role'] == "assistant" || $userData['role'] == "finance")) {
                $data['title'] = "Virtual Equb - Members Report";
                $data['totalMember'] = $this->memberRepository->getCountByDate($dateFrom, $dateTo);
                $data['members'] = $this->memberRepository->getByDate($dateFrom, $dateTo, $offset);
                return view('admin/report/memberReport/filterMembers', $data);
            // } else {
            //     return view('auth/login');
            // }
        } catch (Exception $ex) {
            $msg = "Unknown Error Occurred, Please try again!";
            $type = 'error';
            Session::flash($type, $msg);
            return back();
        }
    }
    public function unPaidLotteryByDateFilter()
    {
        try {
            $userData = Auth::user();
            // if ($userData && ($userData['role'] == "admin" || $userData['role'] == "general_manager" || $userData['role'] == "operation_manager" || $userData['role'] == "assistant" || $userData['role'] == "finance")) {
                $data['title'] = "Virtual Equb - Lotterys Report";
                $data['equbTypes'] = $this->equbTypeRepository->getActive();
                return view('admin/report/unpaidLotteryByDateReport/lotterysByDate', $data);
            // } else {
            //     return view('auth/login');
            // }
        } catch (Exception $ex) {
            $msg = "Unknown Error Occurred, Please try again!";
            $type = 'error';
            Session::flash($type, $msg);
            return back();
        }
    }
    public function unPaidLotterysByDate($lotteryDate, $equbType)
    {
        // dd($lotteryDate);
        try {
            $data['offset'] = 0;
            $offset = 0;
            $data['limit'] = 50;
            $data['pageNumber'] = 1;
            $userData = Auth::user();
            // if ($userData && ($userData['role'] == "admin" || $userData['role'] == "general_manager" || $userData['role'] == "operation_manager" || $userData['role'] == "assistant" || $userData['role'] == "finance")) {
                $data['title'] = "Virtual Equb - UnPaid Lotterys By Lottery Date Report";
                $member_id = $this->equbTakerRepository->getMemberId();
                $member_id = json_encode($member_id);
                $member_id = str_replace('"', "", $member_id);
                $data['totalLotterys'] = $this->equbRepository->getUnPaidLotteryByLotteryDateCount($member_id, $lotteryDate, $equbType);
                $data['lotterys'] = $this->equbRepository->getUnPaidLotteryByLotteryDate($member_id, $lotteryDate, $offset, $equbType);
                // dd($data['lotterys']);
                return view('admin/report/unpaidLotteryByDateReport/filterLotterysByDate', $data);
            // } else {
            //     return view('auth/login');
            // }
        } catch (Exception $ex) {
            $msg = "Unknown Error Occurred, Please try again!";
            $type = 'error';
            Session::flash($type, $msg);
            return back();
        }
    }
    public function paginateUnPaidLotterysByDate($lotteryDate, $offsetVal, $pageNumberVal, $equbType)
    {
        try {
            $data['offset'] = $offsetVal;
            $offset = $offsetVal;
            $data['limit'] = 50;
            $data['pageNumber'] = $pageNumberVal;
            $userData = Auth::user();
            // if ($userData && ($userData['role'] == "admin" || $userData['role'] == "general_manager" || $userData['role'] == "operation_manager" || $userData['role'] == "assistant" || $userData['role'] == "finance")) {
                $data['title'] = "Virtual Equb - UnPaid Lotterys Report";
                $member_id = $this->equbTakerRepository->getMemberId();
                $member_id = json_encode($member_id);
                $member_id = str_replace('"', "", $member_id);
                $data['totalLotterys'] = $this->equbRepository->getUnPaidLotteryByEqubTypeCount($member_id, $equbType);
                $data['lotterys'] = $this->equbRepository->getUnPaidLotteryByEqubType($member_id, $equbType, $offset);
                return view('admin/report/unpaidLotteryByDateReport/filterLotterysByDate', $data);
            // } else {
            //     return view('auth/login');
            // }
        } catch (Exception $ex) {
            $msg = "Unknown Error Occurred, Please try again!";
            $type = 'error';
            Session::flash($type, $msg);
            return back();
        }
    }
    public function equbTypeFilter()
    {
        try {
            $userData = Auth::user();
            // if ($userData && ($userData['role'] == "admin" || $userData['role'] == "general_manager" || $userData['role'] == "operation_manager" || $userData['role'] == "assistant" || $userData['role'] == "finance")) {
                $data['title'] = "Virtual Equb - Equb Types Report";
                return view('admin/report/equbTypeReport/equbTypes', $data);
            // } else {
            //     return view('auth/login');
            // }
        } catch (Exception $ex) {
            $msg = "Unknown Error Occurred, Please try again!";
            $type = 'error';
            Session::flash($type, $msg);
            return back();
        }
    }
    public function equbTypes($dateFrom, $dateTo)
    {
        try {
            $userData = Auth::user();
            // if ($userData && ($userData['role'] == "admin" || $userData['role'] == "general_manager" || $userData['role'] == "operation_manager" || $userData['role'] == "assistant" || $userData['role'] == "finance")) {
                $data['title'] = "Virtual Equb - Equb Types Report";
                $data['equbTypes'] = $this->equbTypeRepository->getByDate($dateFrom, $dateTo);
                return view('admin/report/equbTypeReport/filterEqubTypes', $data);
            // } else {
            //     return view('auth/login');
            // }
        } catch (Exception $ex) {
            $msg = "Unknown Error Occurred, Please try again!";
            $type = 'error';
            Session::flash($type, $msg);
            return back();
        }
    }
    public function paymentFilter()
    {
        try {
            $userData = Auth::user();
            // if ($userData && ($userData['role'] == "admin" || $userData['role'] == "general_manager" || $userData['role'] == "operation_manager" || $userData['role'] == "assistant" || $userData['role'] == "finance")) {
                $data['title'] = "Virtual Equb - Payments Report";
                $data['members'] = $this->memberRepository->getAll();
                $data['equbTypes'] = $this->equbTypeRepository->getActive();
                return view('admin/report/paymentReport/payments', $data);
            // } else {
            //     return view('auth/login');
            // }
        } catch (Exception $ex) {
            $msg = "Unknown Error Occurred, Please try again!";
            $type = 'error';
            Session::flash($type, $msg);
            return back();
        }
    }
    public function payments($dateFrom, $dateTo, $member_id, $equb_id)
    {
        try {
            $data['offset'] = 0;
            $offset = 0;
            $data['limit'] = 50;
            $data['pageNumber'] = 1;
            $userData = Auth::user();
            // if ($userData && ($userData['role'] == "admin" || $userData['role'] == "general_manager" || $userData['role'] == "operation_manager" || $userData['role'] == "assistant" || $userData['role'] == "finance")) {
                $data['title'] = "Virtual Equb - Payments Report";
                if ($member_id == "all" && $equb_id != "all") {
                    $data['totalPayments'] = $this->paymentRepository->getCountWithDateAndEqub($dateFrom, $dateTo, $equb_id);
                    $data['payments'] = $this->paymentRepository->getWithDateAndEqub($dateFrom, $dateTo, $equb_id, $offset);
                } elseif ($member_id == "all" && $equb_id == "all") {
                    $data['totalPayments'] = $this->paymentRepository->getCountWithDate($dateFrom, $dateTo);
                    $data['payments'] = $this->paymentRepository->getByDate($dateFrom, $dateTo, $offset);
                } elseif ($member_id != "all" && $equb_id == "all") {
                    $data['totalPayments'] = $this->paymentRepository->getCountDateAndMember($dateFrom, $dateTo, $member_id);
                    $data['payments'] = $this->paymentRepository->getWithDateAndMember($dateFrom, $dateTo, $member_id, $offset);
                } else {
                    $data['totalPayments'] = $this->paymentRepository->getCountWithDateMemberAndEqub($dateFrom, $dateTo, $member_id, $equb_id);
                    $data['payments'] = $this->paymentRepository->getWithDateMemberAndEqub($dateFrom, $dateTo, $member_id, $equb_id, $offset);
                }
                return view('admin/report/paymentReport/filterPayments', $data);
            // } else {
            //     return view('auth/login');
            // }
        } catch (Exception $ex) {
            $msg = "Unknown Error Occurred, Please try again!";
            $type = 'error';
            Session::flash($type, $msg);
            return back();
        }
    }
    public function paginatePayments($dateFrom, $dateTo, $member_id, $equb_id, $offsetVal, $pageNumberVal)
    {
        try {
            $data['offset'] = $offsetVal;
            $offset = $offsetVal;
            $data['limit'] = 50;
            $data['pageNumber'] = $pageNumberVal;
            $userData = Auth::user();
            // if ($userData && ($userData['role'] == "admin" || $userData['role'] == "general_manager" || $userData['role'] == "operation_manager" || $userData['role'] == "assistant" || $userData['role'] == "finance")) {
                $data['title'] = "Virtual Equb - Payments Report";
                if ($member_id == "all" && $equb_id != "all") {
                    $data['totalPayments'] = $this->paymentRepository->getCountWithDateAndEqub($dateFrom, $dateTo, $equb_id);
                    $data['payments'] = $this->paymentRepository->getWithDateAndEqub($dateFrom, $dateTo, $equb_id, $offset);
                } elseif ($member_id == "all" && $equb_id == "all") {
                    $data['totalPayments'] = $this->paymentRepository->getCountWithDate($dateFrom, $dateTo);
                    $data['payments'] = $this->paymentRepository->getByDate($dateFrom, $dateTo, $offset);
                } elseif ($member_id != "all" && $equb_id == "all") {
                    $data['totalPayments'] = $this->paymentRepository->getCountDateAndMember($dateFrom, $dateTo, $member_id);
                    $data['payments'] = $this->paymentRepository->getWithDateAndMember($dateFrom, $dateTo, $member_id, $offset);
                } else {
                    $data['totalPayments'] = $this->paymentRepository->getCountWithDateMemberAndEqub($dateFrom, $dateTo, $member_id, $equb_id);
                    $data['payments'] = $this->paymentRepository->getWithDateMemberAndEqub($dateFrom, $dateTo, $member_id, $equb_id, $offset);
                }
                return view('admin/report/paymentReport/filterPayments', $data);
            // } else {
            //     return view('auth/login');
            // }
        } catch (Exception $ex) {
            $msg = "Unknown Error Occurred, Please try again!";
            $type = 'error';
            Session::flash($type, $msg);
            return back();
        }
    }
    public function unPaidFilter()
    {
        try {
            $userData = Auth::user();
            // if ($userData && ($userData['role'] == "admin" || $userData['role'] == "general_manager" || $userData['role'] == "operation_manager" || $userData['role'] == "assistant" || $userData['role'] == "finance")) {
                $data['title'] = "Virtual Equb - UnPaid Report";
                $data['members'] = $this->memberRepository->getAll();
                $data['equbTypes'] = $this->equbTypeRepository->getActive();
                return view('admin/report/unPaidReport/unPaids', $data);
            // } else {
            //     return view('auth/login');
            // }
        } catch (Exception $ex) {
            $msg = "Unknown Error Occurred, Please try again!";
            $type = 'error';
            Session::flash($type, $msg);
            return back();
        }
    }
    public function equbEndDates()
    {
        try {
            $userData = Auth::user();
            // if ($userData && ($userData['role'] == "admin" || $userData['role'] == "general_manager" || $userData['role'] == "operation_manager" || $userData['role'] == "assistant" || $userData['role'] == "finance")) {
                $data['title'] = "Virtual Equb - UnPaid Report";
                $data['members'] = $this->equbRepository->getAll();
                $data['equbTypes'] = $this->equbTypeRepository->getActive();
                return view('admin/report/filterEkubEndDates/filtered', $data);
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
  
    public function filterEqubEndDates($dateFrom, $dateTo, $equbType)
    {
        try {
            $data['offset'] = 0;
            $offset = 0;
            $data['limit'] = 50;
            $data['pageNumber'] = 1;
            $userData = Auth::user();
         //   if ($userData && ($userData['role'] == "admin" || $userData['role'] == "general_manager" || $userData['role'] == "operation_manager" || $userData['role'] == "assistant" || $userData['role'] == "finance")) {
                $data['title'] = "Virtual Equb - Fileter Equb By End Date Report";
              
                $data['totalEqub'] = $this->equbRepository->countFilterEqubEndDates($dateFrom, $dateTo, $equbType);
                // dd($data['totalEqub']);
                $data['equbs'] = $this->equbRepository->filterEqubEndDates($dateFrom, $dateTo, $offset, $equbType);
             
                return view('admin/report/unPaidReport/filterUnPaids', $data);
            
        } catch (Exception $ex) {
            $msg = "Unknown Error Occurred, Please try again!";
            $type = 'error';
            Session::flash($type, $msg);
            return back();
        }
    }
    public function loadMoreFilterEqubEndDates($dateFrom, $dateTo, $offsetVal, $pageNumberVal, $equbType)
    {
        try {
            $data['offset'] = $offsetVal;
            $offset = $offsetVal;
            $data['limit'] = 50;
            $data['pageNumber'] = $pageNumberVal;
            $userData = Auth::user();
            // if ($userData && ($userData['role'] == "admin" || $userData['role'] == "general_manager" || $userData['role'] == "operation_manager" || $userData['role'] == "assistant" || $userData['role'] == "finance")) {
                $data['title'] = "Virtual Equb - Fileter Equb By End Date Report";
                // $data['paids'] = $this->paymentRepository->getPaidByDate($dateFrom, $dateTo);
                // $equbId = $data['paids'];
                // $equbId = json_encode($equbId);
                // $equbId = str_replace('"', "", $equbId);
                $data['totalEqub'] = $this->equbRepository->countFilterEqubEndDates($dateFrom, $dateTo, $equbType);
                // dd($data['totalEqub']);
                $data['equbs'] = $this->equbRepository->filterEqubEndDates($dateFrom, $dateTo, $offset, $equbType);
                return view('admin/report/unPaidReport/filterUnPaids', $data);
            // } else {
            //     return view('auth/login');
            // }
        } catch (Exception $ex) {
            $msg = "Unknown Error Occurred, Please try again!";
            $type = 'error';
            Session::flash($type, $msg);
            return back();
        }
    }
    public function unPaids($dateFrom, $dateTo, $equbType)
    {
        try {
            $data['offset'] = 0;
            $offset = 0;
            $data['limit'] = 50;
            $data['pageNumber'] = 1;
            $userData = Auth::user();
            // if ($userData && ($userData['role'] == "admin" || $userData['role'] == "general_manager" || $userData['role'] == "operation_manager" || $userData['role'] == "assistant" || $userData['role'] == "finance")) {
                $data['title'] = "Virtual Equb - UnPaid Report";
                $data['paids'] = $this->paymentRepository->getPaidByDate($dateFrom, $dateTo);
                $equbId = $data['paids'];
                $equbId = json_encode($equbId);
                $equbId = str_replace('"', "", $equbId);
                $data['totalEqub'] = $this->equbRepository->getCountUnPaidByDate($dateFrom, $dateTo, $equbId, $equbType);
                $data['equbs'] = $this->equbRepository->getUnPaidByDate($dateFrom, $dateTo, $equbId, $offset, $equbType);
                return view('admin/report/unPaidReport/filterUnPaids', $data);
            // } else {
            //     return view('auth/login');
            // }
        } catch (Exception $ex) {
            $msg = "Unknown Error Occurred, Please try again!";
            $type = 'error';
            Session::flash($type, $msg);
            return back();
        }
    }
    public function loadMoreUnPaids($dateFrom, $dateTo, $offsetVal, $pageNumberVal, $equbType)
    {
        try {
            $data['offset'] = $offsetVal;
            $offset = $offsetVal;
            $data['limit'] = 50;
            $data['pageNumber'] = $pageNumberVal;
            $userData = Auth::user();
            // if ($userData && ($userData['role'] == "admin" || $userData['role'] == "general_manager" || $userData['role'] == "operation_manager" || $userData['role'] == "assistant" || $userData['role'] == "finance")) {
                $data['title'] = "Virtual Equb - UnPaid Report";
                $data['paids'] = $this->paymentRepository->getPaidByDate($dateFrom, $dateTo);
                $equbId = $data['paids'];
                $equbId = json_encode($equbId);
                $equbId = str_replace('"', "", $equbId);
                $data['totalEqub'] = $this->equbRepository->getCountUnPaidByDate($dateFrom, $dateTo, $equbId, $equbType);
                $data['equbs'] = $this->equbRepository->getUnPaidByDate($dateFrom, $dateTo, $equbId, $offset, $equbType);
                return view('admin/report/unPaidReport/filterUnPaids', $data);
            // } else {
            //     return view('auth/login');
            // }
        } catch (Exception $ex) {
            $msg = "Unknown Error Occurred, Please try again!";
            $type = 'error';
            Session::flash($type, $msg);
            return back();
        }
    }
    public function collectedByFilter()
    {
        try {
            $userData = Auth::user();
            // if ($userData && ($userData['role'] == "admin" || $userData['role'] == "general_manager" || $userData['role'] == "operation_manager" || $userData['role'] == "assistant" || $userData['role'] == "finance")) {
                $data['title'] = "Virtual Equb - Collected By Report";
                $data['collecters'] = $this->userRepository->getCollecters();
                $data['equbTypes'] = $this->equbTypeRepository->getActive();
                
                return view('admin/report/collectedByReport/collectedByReports', $data);
            // } else {
            //     return view('auth/login');
            // }
        } catch (Exception $ex) {
            $msg = "Unknown Error Occurred, Please try again!" . $ex->getMessage();
            $type = 'error';
            Session::flash($type, $msg);
            return back();
        }
    }
    public function collectedBys($dateFrom, $dateTo, $collecter, $equbType)
    {
        try {
            $data['offset'] = 0;
            $offset = 0;
            $data['limit'] = 50;
            $data['pageNumber'] = 1;
            $userData = Auth::user();
            // if ($userData && ($userData['role'] == "admin" || $userData['role'] == "general_manager" || $userData['role'] == "operation_manager" || $userData['role'] == "assistant" || $userData['role'] == "finance")) {
                $data['title'] = "Virtual Equb - Collected by Report";
                if ($collecter != "all" || $equbType != "all") {
                    $data['totalPayments'] = $this->paymentRepository->getCountCollectedBysWithCollecter($dateFrom, $dateTo, $collecter, $equbType);
                    $data['collecters'] = $this->paymentRepository->getCollectedByUser($dateFrom, $dateTo, $collecter, $offset, $equbType);
                } else {
                    $data['totalPayments'] = $this->paymentRepository->getCountCollectedBys($dateFrom, $dateTo);
                    $data['collecters'] = $this->paymentRepository->getByDate($dateFrom, $dateTo, $offset);
                }
                return view('admin/report/collectedByReport/filterCollectedByReports', $data);
            // } else {
            //     return view('auth/login');
            // }
        } catch (Exception $ex) {
            $msg = "Unknown Error Occurred, Please try again!";
            $type = 'error';
            Session::flash($type, $msg);
            return back();
        }
    }
    public function paginateCllectedBys($dateFrom, $dateTo, $collecter, $offsetVal, $pageNumberVal, $equbType)
    {
        try {
            $data['offset'] = $offsetVal;
            $offset = $offsetVal;
            $data['limit'] = 50;
            $data['pageNumber'] = $pageNumberVal;
            $userData = Auth::user();
            // if ($userData && ($userData['role'] == "admin" || $userData['role'] == "general_manager" || $userData['role'] == "operation_manager" || $userData['role'] == "assistant" || $userData['role'] == "finance")) {
                $data['title'] = "Virtual Equb - Collected by Report";
                if ($collecter != "all" || $equbType != "all") {
                    $data['totalPayments'] = $this->paymentRepository->getCountCollectedBysWithCollecter($dateFrom, $dateTo, $collecter, $equbType);
                    $data['collecters'] = $this->paymentRepository->getCollectedByUser($dateFrom, $dateTo, $collecter, $offset, $equbType);
                } else {
                    $data['totalPayments'] = $this->paymentRepository->getCountCollectedBys($dateFrom, $dateTo);
                    $data['collecters'] = $this->paymentRepository->getByDate($dateFrom, $dateTo, $offset);
                }
                return view('admin/report/collectedByReport/filterCollectedByReports', $data);
            // } else {
            //     return view('auth/login');
            // }
        } catch (Exception $ex) {
            $msg = "Unknown Error Occurred, Please try again!";
            $type = 'error';
            Session::flash($type, $msg);
            return back();
        }
    }
    public function lotteryFilter()
    {
        try {
            $userData = Auth::user();
            // if ($userData && ($userData['role'] == "admin" || $userData['role'] == "general_manager" || $userData['role'] == "operation_manager" || $userData['role'] == "it" || $userData['role'] == "finance" || $userData['role'] == "marketing_manager" || $userData['role'] == "assistant")) {
                $data['members'] = $this->memberRepository->getAll();
                $data['equbTypes'] = $this->equbTypeRepository->getActive();
                $data['title'] = "Virtual Equb - Lotterys Report";
                return view('admin/report/lotteryReport/lotterys', $data);
            // } else {
            //     return view('auth/login');
            // }
        } catch (Exception $ex) {
            $msg = "Unknown Error Occurred, Please try again!";
            $type = 'error';
            Session::flash($type, $msg);
            return back();
        }
    }
    public function lotterys($dateFrom, $dateTo, $member_id, $equb_type_id)
    {
        try {
            $data['offset'] = 0;
            $offset = 0;
            $data['limit'] = 50;
            $data['pageNumber'] = 1;
            $userData = Auth::user();
            // if ($userData && ($userData['role'] == "admin" || $userData['role'] == "general_manager" || $userData['role'] == "operation_manager" || $userData['role'] == "assistant" || $userData['role'] == "finance")) {
                $data['title'] = "Virtual Equb - Lotterys Report";
                if ($member_id == "all" && $equb_type_id != "all") {
                    $data['totalLotterys'] = $this->equbTakerRepository->getCountWithDateAndEqub($dateFrom, $dateTo, $equb_type_id);
                    $data['lotterys'] = $this->equbTakerRepository->getWithDateAndEqub($dateFrom, $dateTo, $equb_type_id, $offset);
                } elseif ($member_id == "all" && $equb_type_id == "all") {
                    $data['totalLotterys'] = $this->equbTakerRepository->getCountByDate($dateFrom, $dateTo);
                    $data['lotterys'] = $this->equbTakerRepository->getByDate($dateFrom, $dateTo, $offset);
                } elseif ($member_id != "all" && $equb_type_id == "all") {
                    $data['totalLotterys'] = $this->equbTakerRepository->getCountWithDateAndMember($dateFrom, $dateTo, $member_id);
                    $data['lotterys'] = $this->equbTakerRepository->getWithDateAndMember($dateFrom, $dateTo, $member_id, $offset);
                } else {
                    $data['totalLotterys'] = $this->equbTakerRepository->getCountWithDateMemberAndEqub($dateFrom, $dateTo, $member_id, $equb_type_id);
                    $data['lotterys'] = $this->equbTakerRepository->getWithDateMemberAndEqub($dateFrom, $dateTo, $member_id, $equb_type_id, $offset);
                }
                return view('admin/report/lotteryReport/filterLotterys', $data);
            // } else {
            //     return view('auth/login');
            // }
        } catch (Exception $ex) {
            $msg = "Unknown Error Occurred, Please try again!";
            $type = 'error';
            Session::flash($type, $msg);
            return back();
        }
    }
    public function paginateLotterys($dateFrom, $dateTo, $member_id, $equb_type_id, $offsetVal, $pageNumberVal)
    {
        try {
            $data['offset'] = $offsetVal;
            $offset = $offsetVal;
            $data['limit'] = 50;
            $data['pageNumber'] = $pageNumberVal;
            $userData = Auth::user();
            // if ($userData && ($userData['role'] == "admin" || $userData['role'] == "general_manager" || $userData['role'] == "operation_manager" || $userData['role'] == "assistant" || $userData['role'] == "finance")) {
                $data['title'] = "Virtual Equb - Lotterys Report";
                if ($member_id == "all" && $equb_type_id != "all") {
                    $data['totalLotterys'] = $this->equbTakerRepository->getCountWithDateAndEqub($dateFrom, $dateTo, $equb_type_id);
                    $data['lotterys'] = $this->equbTakerRepository->getWithDateAndEqub($dateFrom, $dateTo, $equb_type_id, $offset);
                } elseif ($member_id == "all" && $equb_type_id == "all") {
                    $data['totalLotterys'] = $this->equbTakerRepository->getCountByDate($dateFrom, $dateTo);
                    $data['lotterys'] = $this->equbTakerRepository->getByDate($dateFrom, $dateTo, $offset);
                } elseif ($member_id != "all" && $equb_type_id == "all") {
                    $data['totalLotterys'] = $this->equbTakerRepository->getCountWithDateAndMember($dateFrom, $dateTo, $member_id);
                    $data['lotterys'] = $this->equbTakerRepository->getWithDateAndMember($dateFrom, $dateTo, $member_id, $offset);
                } else {
                    $data['totalLotterys'] = $this->equbTakerRepository->getCountWithDateMemberAndEqub($dateFrom, $dateTo, $member_id, $equb_type_id);
                    $data['lotterys'] = $this->equbTakerRepository->getWithDateMemberAndEqub($dateFrom, $dateTo, $member_id, $equb_type_id, $offset);
                }
                return view('admin/report/lotteryReport/filterLotterys', $data);
            // } else {
            //     return view('auth/login');
            // }
        } catch (Exception $ex) {
            $msg = "Unknown Error Occurred, Please try again!";
            $type = 'error';
            Session::flash($type, $msg);
            return back();
        }
    }
    public function equbFilter()
    {
        try {
            $userData = Auth::user();
            // if ($userData && ($userData['role'] == "admin" || $userData['role'] == "general_manager" || $userData['role'] == "operation_manager" || $userData['role'] == "assistant" || $userData['role'] == "finance")) {
                $data['title'] = "Virtual Equb - Equbs Report";
                $data['members'] = $this->memberRepository->getAll();
                $data['equbTypes'] = $this->equbTypeRepository->getActive();
                return view('admin/report/equbReport/equbs', $data);
            // } else {
            //     return view('auth/login');
            // }
        } catch (Exception $ex) {
            $msg = "Unknown Error Occurred, Please try again!";
            $type = 'error';
            Session::flash($type, $msg);
            return back();
        }
    }
    public function equbs($dateFrom, $dateTo, $equbType)
    {
        try {
            $data['offset'] = 0;
            $offset = 0;
            $data['limit'] = 50;
            $data['pageNumber'] = 1;
            $userData = Auth::user();
            // if ($userData && ($userData['role'] == "admin" || $userData['role'] == "general_manager" || $userData['role'] == "operation_manager" || $userData['role'] == "assistant" || $userData['role'] == "finance")) {
                $data['title'] = "Virtual Equb - Equbs Report";
                $data['totalEqub'] = $this->equbRepository->getCountByDate($dateFrom, $dateTo, $equbType);
                $data['equbs'] = $this->equbRepository->getByDate($dateFrom, $dateTo, $equbType, $offset);
                return view('admin/report/equbReport/filterEqubs', $data);
            // } else {
            //     return view('auth/login');
            // }
        } catch (Exception $ex) {
            $msg = "Unknown Error Occurred, Please try again!";
            $type = 'error';
            Session::flash($type, $msg);
            return back();
        }
    }
    public function paginateEqubs($dateFrom, $dateTo, $offsetVal, $pageNumberVal, $equbType)
    {
        try {
            $data['offset'] = $offsetVal;
            $offset = $offsetVal;
            $data['limit'] = 50;
            $data['pageNumber'] = $pageNumberVal;
            $userData = Auth::user();
            // if ($userData && ($userData['role'] == "admin" || $userData['role'] == "general_manager" || $userData['role'] == "operation_manager" || $userData['role'] == "assistant" || $userData['role'] == "finance")) {
                $data['title'] = "Virtual Equb - Equbs Report";
                $data['totalEqub'] = $this->equbRepository->getCountByDate($dateFrom, $dateTo, $equbType);
                $data['equbs'] = $this->equbRepository->getByDate($dateFrom, $dateTo, $equbType, $offset);
                return view('admin/report/equbReport/filterEqubs', $data);
            // } else {
            //     return view('auth/login');
            // }
        } catch (Exception $ex) {
            $msg = "Unknown Error Occurred, Please try again!";
            $type = 'error';
            Session::flash($type, $msg);
            return back();
        }
    }
    public function unPaidLotteryFilter()
    {
        try {
            $userData = Auth::user();
            // if ($userData && ($userData['role'] == "admin" || $userData['role'] == "general_manager" || $userData['role'] == "operation_manager" || $userData['role'] == "assistant" || $userData['role'] == "finance")) {
                $data['title'] = "Virtual Equb - Lotterys Report";
                return view('admin/report/unpaidLotteryReport/lotterys', $data);
            // } else {
            //     return view('auth/login');
            // }
        } catch (Exception $ex) {
            $msg = "Unknown Error Occurred, Please try again!";
            $type = 'error';
            Session::flash($type, $msg);
            return back();
        }
    }
    public function unPaidLotterys()
    {
        try {
            $data['offset'] = 0;
            $offset = 0;
            $data['limit'] = 50;
            $data['pageNumber'] = 1;
            $userData = Auth::user();
            // if ($userData && ($userData['role'] == "admin" || $userData['role'] == "general_manager" || $userData['role'] == "operation_manager" || $userData['role'] == "assistant" || $userData['role'] == "finance")) {
                $data['title'] = "Virtual Equb - UnPaid Lotterys Report";
                $member_id = $this->equbTakerRepository->getMemberId();
                $member_id = json_encode($member_id);
                $member_id = str_replace('"', "", $member_id);
                $data['totalLotterys'] = $this->equbRepository->getUnPaidLotteryCount($member_id);
                $data['lotterys'] = $this->equbRepository->getUnPaidLottery($member_id, $offset);
                return view('admin/report/unpaidLotteryReport/filterLotterys', $data);
            // } else {
            //     return view('auth/login');
            // }
        } catch (Exception $ex) {
            $msg = "Unknown Error Occurred, Please try again!";
            $type = 'error';
            Session::flash($type, $msg);
            return back();
        }
    }
    public function paginateUnPaidLotterys($offsetVal, $pageNumberVal)
    {
        try {
            $data['offset'] = $offsetVal;
            $offset = $offsetVal;
            $data['limit'] = 50;
            $data['pageNumber'] = $pageNumberVal;
            $userData = Auth::user();
            // if ($userData && ($userData['role'] == "admin" || $userData['role'] == "general_manager" || $userData['role'] == "operation_manager" || $userData['role'] == "assistant" || $userData['role'] == "finance")) {
                $data['title'] = "Virtual Equb - UnPaid Lotterys Report";
                $member_id = $this->equbTakerRepository->getMemberId();
                $member_id = json_encode($member_id);
                $member_id = str_replace('"', "", $member_id);
                $data['totalLotterys'] = $this->equbRepository->getUnPaidLotteryCount($member_id);
                $data['lotterys'] = $this->equbRepository->getUnPaidLottery($member_id, $offset);
                return view('admin/report/unpaidLotteryReport/filterLotterys', $data);
            // } else {
            //     return view('auth/login');
            // }
        } catch (Exception $ex) {
            $msg = "Unknown Error Occurred, Please try again!";
            $type = 'error';
            Session::flash($type, $msg);
            return back();
        }
    }
    public function reservedLotteryDatesFilter()
    {
        try {
            $userData = Auth::user();
            // if ($userData && ($userData['role'] == "admin" || $userData['role'] == "general_manager" || $userData['role'] == "operation_manager" || $userData['role'] == "assistant" || $userData['role'] == "finance")) {
                $data['title'] = "Virtual Equb - Reserved Lottery Dates Report";
                $data['equbTypes'] = $this->equbTypeRepository->getActive();
                return view('admin/report/reservedLotteryDates/lotterys', $data);
            // } else {
            //     return view('auth/login');
            // }
        } catch (Exception $ex) {
            $msg = "Unknown Error Occurred, Please try again!";
            $type = 'error';
            Session::flash($type, $msg);
            return back();
        }
    }
    public function reservedLotteryDates($dateFrom, $dateTo, $equbType)
    {
        try {
            $data['offset'] = 0;
            $offset = 0;
            $data['limit'] = 50;
            $data['pageNumber'] = 1;
            $userData = Auth::user();
            // if ($userData && ($userData['role'] == "admin" || $userData['role'] == "general_manager" || $userData['role'] == "operation_manager" || $userData['role'] == "assistant" || $userData['role'] == "finance")) {
                $data['title'] = "Virtual Equb - UnPaid Lotterys Report";
                $member_id = $this->equbTakerRepository->getMemberId();
                $member_id = json_encode($member_id);
                $member_id = str_replace('"', "", $member_id);
                $data['totalLotterys'] = $this->equbRepository->getReservedLotteryDatesCount($dateFrom, $dateTo, $member_id, $equbType);
                $data['lotterys'] = $this->equbRepository->getReservedLotteryDates($dateFrom, $dateTo, $member_id, $offset, $equbType);
                return view('admin/report/reservedLotteryDates/filterLotterys', $data);
            // } else {
            //     return view('auth/login');
            // }
        } catch (Exception $ex) {
            $msg = "Unknown Error Occurred, Please try again!";
            $type = 'error';
            Session::flash($type, $msg);
            return back();
        }
    }

    public function paginateReservedLotteryDates($dateFrom, $dateTo, $equbType, $offsetVal, $pageNumberVal)
    {
        try {
            $data['offset'] = $offsetVal;
            $offset = $offsetVal;
            $data['limit'] = 50;
            $data['pageNumber'] = $pageNumberVal;
            $userData = Auth::user();
            // if ($userData && ($userData['role'] == "admin" || $userData['role'] == "general_manager" || $userData['role'] == "operation_manager" || $userData['role'] == "assistant" || $userData['role'] == "finance")) {
                $data['title'] = "Virtual Equb - UnPaid Lotterys Report";
                $member_id = $this->equbTakerRepository->getMemberId();
                $member_id = json_encode($member_id);
                $member_id = str_replace('"', "", $member_id);
                $data['totalLotterys'] = $this->equbRepository->getReservedLotteryDatesCount($dateFrom, $dateTo, $member_id, $equbType);
                $data['lotterys'] = $this->equbRepository->getReservedLotteryDates($dateFrom, $dateTo, $member_id, $offset, $equbType);
                return view('admin/report/reservedLotteryDates/filterLotterys', $data);
            // } else {
            //     return view('auth/login');
            // }
        } catch (Exception $ex) {
            $msg = "Unknown Error Occurred, Please try again!";
            $type = 'error';
            Session::flash($type, $msg);
            return back();
        }
    }
}
