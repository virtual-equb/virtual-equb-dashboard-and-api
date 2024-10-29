<?php

namespace App\Http\Controllers\Api;

use Exception;
use App\Repositories\Payment\IPaymentRepository;
use App\Repositories\Equb\IEqubRepository;
use App\Repositories\EqubType\IEqubTypeRepository;
use App\Repositories\Member\IMemberRepository;
use App\Repositories\User\IUserRepository;
use Illuminate\Support\Arr;
use App\Models\Payment;
use App\Http\Controllers\Controller;
use App\Models\LotteryWinner;
use App\Models\Member;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
/**
 * @group Dashboard
 */
class HomeController extends Controller
{
    private $paymentRepository;
    private $equbRepository;
    private $memberRepository;
    private $userRepository;
    private $title;
    private $equbTypeRepository;
    public function __construct(IPaymentRepository $paymentRepository, IEqubRepository $equbRepository, IEqubTypeRepository $equbTypeRepository, IMemberRepository $memberRepository, IUserRepository $userRepository)
    {
        $this->middleware('auth:api');
        $this->paymentRepository = $paymentRepository;
        $this->equbRepository = $equbRepository;
        $this->equbTypeRepository = $equbTypeRepository;
        $this->memberRepository = $memberRepository;
        $this->userRepository = $userRepository;
        $this->title = "Virtual Equb - Dashboard";

        // Guard Permission
        $this->middleware('permission: view dashboard', ['only' => ['index', 'equbTypeIndex']]);
    }
    /**
     * Get all dashboard info
     *
     * This api returns all dashboard info.
     *
     * @return JsonResponse
     */
    public function index()
    {
        try {
            $userData = Auth::user();

            // if ($userData && ($userData['role'] == "admin" || $userData['role'] == "general_manager" || $userData['role'] == "operation_manager" || $userData['role'] == "it")){
                $profile = Auth::user();
                $title = $this->title;
                $totalEqubAmount = $this->equbRepository->getExpectedTotal();
                $totalEqubPayment = $this->paymentRepository->getTotalPayment();
                $activeMember = $this->memberRepository->getActiveMember();
                $fullPaidAmount = Payment::selectRaw('sum(payments.amount) as paidAmount')
                    ->join('equbs', 'payments.equb_id', '=', 'equbs.id')
                    ->join('equb_types', 'equb_types.id', '=', 'equbs.equb_type_id')
                    ->groupBy('equb_types.name')
                    ->orderBy('equb_types.id', 'asc')
                    ->whereDate('payments.created_at', '>=', date('Y-m-d'))
                    ->get();
                $lable = Payment::join('equbs', 'payments.equb_id', '=', 'equbs.id')
                    ->join('equb_types', 'equb_types.id', '=', 'equbs.equb_type_id')
                    ->groupBy('equb_types.name')
                    ->orderBy('equb_types.id', 'asc')
                    ->whereDate('payments.created_at', '>=', date('Y-m-d'))
                    ->pluck('equb_types.name');
                $equbTypeId = Payment::join('equbs', 'payments.equb_id', '=', 'equbs.id')
                    ->join('equb_types', 'equb_types.id', '=', 'equbs.equb_type_id')
                    ->groupBy('equb_types.id')
                    ->whereDate('payments.created_at', '>=', date('Y-m-d'))
                    ->pluck('equb_types.id');
                $equbTypeId = $equbTypeId->toArray();
                $fullUnPaidAmount = Payment::selectRaw('sum(payments.amount) as unpaidAmount')
                    ->join('equbs', 'payments.equb_id', '=', 'equbs.id')
                    ->join('equb_types', 'equb_types.id', '=', 'equbs.equb_type_id')
                    ->groupBy('equb_types.name')
                    ->whereDate('payments.created_at', '>=', date('Y-m-d'))
                    ->whereDate('payments.created_at', '>=', date('Y-m-d'))
                    ->where('payments.status', 'unpaid')
                    ->get();
                $Expected = $this->equbRepository->getExpected($equbTypeId);
                $lables = $lable->toArray();
                $fullPaidAmount = $fullPaidAmount->toArray();
                $Expected = $Expected->toArray();
                $fullPaidAmount = Arr::pluck($fullPaidAmount, 'paidAmount');
                $Expected = Arr::pluck($Expected, 'expected');
                $lables = json_encode($lables);
                $fullPaidAmount = json_encode($fullPaidAmount);
                $Expected = json_encode($Expected);
                $lables = str_replace('"', "", $lables);
                $fullPaidAmount = str_replace('"', "", $fullPaidAmount);
                $Expected = str_replace('"', "", $Expected);
                $fullDaylyPaidAmount = $this->paymentRepository->getDaylyPaidAmount();
                $daylyPendingAmount = $this->paymentRepository->getDaylyPendingAmount();
                $daylyPaidAmount = $fullDaylyPaidAmount + $daylyPendingAmount;
                $daylyUnpaidAmount = $totalEqubAmount - $daylyPaidAmount;
                if ($daylyUnpaidAmount <= 0) {
                    $daylyUnpaidAmount = 0;
                }
                $daylyExpected = $totalEqubAmount;

                $fullWeeklyPaidAmount = $this->paymentRepository->getWeeklyPaidAmount();
                $weeklyPendingAmount = $this->paymentRepository->getWeeklyPendingAmount();
                $weeklyPaidAmount = $fullWeeklyPaidAmount;

                $weeklyExpected  = $this->equbRepository->getExpectedAmount();
                $sum = 0;
                $index = count($weeklyExpected);
                $day = Carbon::today()->addDays(7);
                for ($i = 0; $i < $index; $i++) {
                    $end_date = $weeklyExpected[$i]->end_date;
                    $end_date = \Carbon\Carbon::parse($end_date);
                    $start_date = $weeklyExpected[$i]->start_date;
                    $start_date = \Carbon\Carbon::parse($start_date);
                    $currunt_date = \Carbon\Carbon::today();
                    if ($end_date >= $currunt_date) {
                        if ($start_date >= $currunt_date) {
                            $difference = $start_date->diffInDays($end_date, false);
                        } else {
                            $difference = $currunt_date->diffInDays($end_date, false);
                        }
                        if ($difference >= 6) {
                            $wE = $weeklyExpected[$i]->amount;
                            $wE = $wE * 7;
                            $sum = $sum + $wE;
                        } else {
                            $wE = $weeklyExpected[$i]->amount;
                            $difference = $difference + 1;
                            $wE = $wE * $difference;
                            $sum = $sum + $wE;
                        }
                    }
                }
                $weeklyExpected = $sum;
                $weeklyExpected1  = $this->equbRepository->getExpectedBackPayment();
                $sum1 = 0;
                $index1 = count($weeklyExpected1);
                for ($i = 0; $i < $index1; $i++) {
                    $end_date = $weeklyExpected1[$i]->end_date;
                    $end_date = \Carbon\Carbon::parse($end_date);
                    $start_date = $weeklyExpected1[$i]->start_date;
                    $start_date = \Carbon\Carbon::parse($start_date);
                    $currunt_date = \Carbon\Carbon::today()->subDays(7);
                    if ($end_date >= $currunt_date) {
                        if ($start_date >= $currunt_date) {
                            $difference = $start_date->diffInDays($end_date, false);
                        } else {
                            $difference = $currunt_date->diffInDays($end_date, false);
                        }
                        if ($difference >= 6) {
                            $wE = $weeklyExpected1[$i]->amount;
                            $wE = $wE * 7;
                            $sum1 = $sum1 + $wE;
                        } else {
                            $wE = $weeklyExpected1[$i]->amount;
                            $difference = $difference + 1;
                            $wE = $wE * $difference;
                            $sum1 = $sum1 + $wE;
                        }
                    }
                }
                $lastWeeklyExpected = $sum1;
                $weeklyUnpaidAmount = $lastWeeklyExpected - $weeklyPaidAmount;
                $fullMonthlyPaidAmount = $this->paymentRepository->getMonthlyPaidAmount();
                $monthlyPendingAmount = $this->paymentRepository->getMonthlyPendingAmount();
                $monthlyPaidAmount = $fullMonthlyPaidAmount + $monthlyPendingAmount;
                $monthlyExpected  = $this->equbRepository->getExpectedAmount();
                $sum2 = 0;
                $index = count($monthlyExpected);
                for ($i = 0; $i < $index; $i++) {
                    $end_date = $monthlyExpected[$i]->end_date;
                    $end_date = \Carbon\Carbon::parse($end_date);
                    $start_date = $monthlyExpected[$i]->start_date;
                    $start_date = \Carbon\Carbon::parse($start_date);
                    $currunt_date = \Carbon\Carbon::today();
                    if ($end_date >= $currunt_date) {
                        if ($start_date >= $currunt_date) {
                            $difference = $start_date->diffInDays($end_date, false);
                        } else {
                            $difference = $currunt_date->diffInDays($end_date, false);
                        }
                        if ($difference >= 29) {
                            $wE = $monthlyExpected[$i]->amount;
                            $wE = $wE * 30;
                            $sum2 = $sum2 + $wE;
                        } else {
                            $wE = $monthlyExpected[$i]->amount;
                            $difference = $difference + 1;
                            $wE = $wE * $difference;
                            $sum2 = $sum2 + $wE;
                        }
                    }
                }
                $monthlyExpected = $sum2;
                $monthlyUnpaidAmount = $monthlyExpected - $monthlyPaidAmount;
                $fullYearlyPaidAmount = $this->paymentRepository->getYearlyPaidAmount();
                $yearlyPendingAmount = $this->paymentRepository->getYearlyPendingAmount();
                $yearlyPaidAmount =  $fullYearlyPaidAmount + $yearlyPendingAmount;
                $yearlyExpected  = $this->equbRepository->getExpectedAmount();
                $sum3 = 0;
                $index = count($yearlyExpected);
                for ($i = 0; $i < $index; $i++) {
                    $end_date = $yearlyExpected[$i]->end_date;
                    $end_date = \Carbon\Carbon::parse($end_date);
                    $start_date = $yearlyExpected[$i]->start_date;
                    $start_date = \Carbon\Carbon::parse($start_date);
                    $currunt_date = \Carbon\Carbon::today();
                    if ($end_date >= $currunt_date) {
                        if ($start_date >= $currunt_date) {
                            $difference = $start_date->diffInDays($end_date, false);
                        } else {
                            $difference = $currunt_date->diffInDays($end_date, false);
                        }
                        if ($difference >= 364) {
                            $wE = $yearlyExpected[$i]->amount;
                            $wE = $wE * 365;
                            $sum3 = $sum3 + $wE;
                        } else {
                            $wE = $yearlyExpected[$i]->amount;
                            $difference = $difference + 1;
                            $wE = $wE * $difference;
                            $sum3 = $sum3 + $wE;
                        }
                    }
                }
                $yearlyExpected = $sum3;
                $yearlyUnpaidAmount = $yearlyExpected - $yearlyPaidAmount;
                $totalMember = $this->memberRepository->getMember();
                $totalUser = $this->userRepository->getUser();
                $tudayPaidMember = $this->equbRepository->tudayPaidMember();
                return  response()->json(compact('title', 'lables', 'fullPaidAmount', 'Expected', 'daylyPaidAmount', 'daylyUnpaidAmount', 'daylyExpected', 'weeklyPaidAmount', 'weeklyExpected', 'monthlyPaidAmount', 'monthlyExpected', 'yearlyPaidAmount', 'yearlyExpected', 'totalMember', 'tudayPaidMember', 'activeMember', 'totalEqubPayment'), 200);
            // } else {
            //     return response()->json([
            //         'code' => 400,
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
    public function equbTypeIndex($equb_type_id)
    {
        try {
            $userData = Auth::user();
            // if ($userData && ($userData['role'] == "admin" || $userData['role'] == "general_manager" || $userData['role'] == "operation_manager" || $userData['role'] == "it")){
                $profile = Auth::user();
                $title = $this->title;
                $totalEqubAmount = $this->equbRepository->getEqubTypeExpectedTotal($equb_type_id);
                $totalEqubPayment = $this->paymentRepository->getEqubTypeTotalPayment($equb_type_id);
                $activeMember = $this->memberRepository->getEqubTypeActiveMember($equb_type_id);
                $fullPaidAmount = Payment::selectRaw('sum(payments.amount) as paidAmount')
                ->join('equbs', 'payments.equb_id', '=', 'equbs.id')
                ->join('equb_types', 'equb_types.id', '=', 'equbs.equb_type_id')
                ->where('equb_types.id', '=', $equb_type_id)
                ->groupBy('equb_types.name')
                ->orderBy('equb_types.id', 'asc')
                ->whereDate('payments.created_at', '>=', date('Y-m-d'))
                ->get();
                $lable = Payment::join('equbs', 'payments.equb_id', '=', 'equbs.id')
                ->join('equb_types', 'equb_types.id', '=', 'equbs.equb_type_id')
                ->where('equb_types.id', '=', $equb_type_id)
                ->groupBy('equb_types.name')
                ->orderBy('equb_types.id', 'asc')
                ->whereDate('payments.created_at', '>=', date('Y-m-d'))
                ->pluck('equb_types.name');
                $equbTypeId = Payment::join('equbs', 'payments.equb_id', '=', 'equbs.id')
                ->join('equb_types', 'equb_types.id', '=', 'equbs.equb_type_id')
                ->where('equb_types.id', '=', $equb_type_id)
                ->groupBy('equb_types.id')
                ->whereDate('payments.created_at', '>=', date('Y-m-d'))
                ->pluck('equb_types.id');
                $equbTypeId = $equbTypeId->toArray();
                $fullUnPaidAmount = Payment::selectRaw('sum(payments.amount) as unpaidAmount')
                ->join('equbs', 'payments.equb_id', '=', 'equbs.id')
                ->join('equb_types', 'equb_types.id', '=', 'equbs.equb_type_id')
                ->where('equb_types.id', '=', $equb_type_id)
                ->groupBy('equb_types.name')
                ->whereDate('payments.created_at', '>=', date('Y-m-d'))
                ->whereDate('payments.created_at', '>=', date('Y-m-d'))
                ->where('payments.status', 'unpaid')
                ->get();
                // dd($fullUnPaidAmount);
                // dd($equb_type_id);

                $Expected = $this->equbRepository->getExpected($equbTypeId);
                // dd($Expected);
                $lables = $lable->toArray();
                $fullPaidAmount = $fullPaidAmount->toArray();
                $Expected = $Expected->toArray();
                $fullPaidAmount = Arr::pluck($fullPaidAmount, 'paidAmount');
                $Expected = Arr::pluck($Expected, 'expected');
                $lables = json_encode($lables);
                $fullPaidAmount = json_encode($fullPaidAmount);
                $Expected = json_encode($Expected);
                $lables = str_replace('"', "", $lables);
                $fullPaidAmount = str_replace('"', "", $fullPaidAmount);
                $Expected = str_replace('"', "", $Expected);
                $fullDaylyPaidAmount = $this->paymentRepository->getEqubTypeDaylyPaidAmount($equb_type_id);
                $daylyPendingAmount = $this->paymentRepository->getEqubTypeDaylyPendingAmount($equb_type_id);
                $daylyPaidAmount = $fullDaylyPaidAmount + $daylyPendingAmount;
                $daylyUnpaidAmount = $totalEqubAmount - $daylyPaidAmount;
                if ($daylyUnpaidAmount <= 0) {
                    $daylyUnpaidAmount = 0;
                }
                $daylyExpected = $totalEqubAmount;
                $fullWeeklyPaidAmount = $this->paymentRepository->getEqubTypeWeeklyPaidAmount($equb_type_id);
                $weeklyPendingAmount = $this->paymentRepository->getEqubTypeWeeklyPendingAmount($equb_type_id);
                $weeklyPaidAmount = $fullWeeklyPaidAmount;
                $weeklyExpected  = $this->equbRepository->getEqubTypeExpectedAmount($equb_type_id);
                $sum = 0;
                $index = count($weeklyExpected);
                $day = Carbon::today()->addDays(7);
                for ($i = 0; $i < $index; $i++) {
                    $end_date = $weeklyExpected[$i]->end_date;
                    $end_date = \Carbon\Carbon::parse($end_date);
                    $start_date = $weeklyExpected[$i]->start_date;
                    $start_date = \Carbon\Carbon::parse($start_date);
                    $currunt_date = \Carbon\Carbon::today();
                    // if ($end_date >= $currunt_date) {
                    //     if ($start_date >= $currunt_date) {
                    //         $difference = $start_date->diffInDays($end_date, false);
                    //     } else {
                    //         $difference = $currunt_date->diffInDays($end_date, false);
                    //     }
                    if ($start_date <= $currunt_date && $end_date >= $currunt_date) {
                        $difference = $currunt_date->diffInDays($end_date, false);
                    } else {
                        $difference = $start_date->diffInDays($end_date, false);
                    }
                    if ($difference >= 6) {
                        $wE = $weeklyExpected[$i]->amount;
                        $wE = $wE * 7;
                        $sum = $sum + $wE;
                    } else {
                        $wE = $weeklyExpected[$i]->amount;
                        $difference = $difference + 1;
                        $wE = $wE * $difference;
                        $sum = $sum + $wE;
                    }
                    // }
                }
                $weeklyExpected = $sum;
                $weeklyExpected1  = $this->equbRepository->getEqubTypeExpectedBackPayment($equb_type_id);
                $sum1 = 0;
                $index1 = count($weeklyExpected1);
                for ($i = 0; $i < $index1; $i++) {
                    $end_date = $weeklyExpected1[$i]->end_date;
                    $end_date = \Carbon\Carbon::parse($end_date);
                    $start_date = $weeklyExpected1[$i]->start_date;
                    $start_date = \Carbon\Carbon::parse($start_date);
                    $currunt_date = \Carbon\Carbon::today()->subDays(7);
                    // if ($end_date >= $currunt_date) {
                    // if ($start_date >= $currunt_date) {
                    //     $difference = $start_date->diffInDays($end_date, false);
                    // } else {
                    //     $difference = $currunt_date->diffInDays($end_date, false);
                    // }
                    if ($start_date <= $currunt_date && $end_date >= $currunt_date) {
                        $difference = $currunt_date->diffInDays($end_date, false);
                    } else {
                        $difference = $start_date->diffInDays($end_date, false);
                    }
                    if ($difference >= 6) {
                        $wE = $weeklyExpected1[$i]->amount;
                        $wE = $wE * 7;
                        $sum1 = $sum1 + $wE;
                    } else {
                        $wE = $weeklyExpected1[$i]->amount;
                        $difference = $difference + 1;
                        $wE = $wE * $difference;
                        $sum1 = $sum1 + $wE;
                    }
                    // }
                }
                $lastWeeklyExpected = $sum1;
                $weeklyUnpaidAmount = $lastWeeklyExpected - $weeklyPaidAmount;
                $fullMonthlyPaidAmount = $this->paymentRepository->getEqubTypeMonthlyPaidAmount($equb_type_id);
                $monthlyPendingAmount = $this->paymentRepository->getEqubTypeMonthlyPendingAmount($equb_type_id);
                $monthlyPaidAmount = $fullMonthlyPaidAmount + $monthlyPendingAmount;
                $monthlyExpected  = $this->equbRepository->getEqubTypeExpectedAmount($equb_type_id);
                $sum2 = 0;
                $index = count($monthlyExpected);
                for ($i = 0; $i < $index; $i++) {
                    $end_date = $monthlyExpected[$i]->end_date;
                    $end_date = \Carbon\Carbon::parse($end_date);
                    $start_date = $monthlyExpected[$i]->start_date;
                    $start_date = \Carbon\Carbon::parse($start_date);
                    $currunt_date = \Carbon\Carbon::today();
                    // if ($end_date >= $currunt_date) {
                    // if ($start_date >= $currunt_date) {
                    //     $difference = $start_date->diffInDays($end_date, false);
                    // } else {
                    //     $difference = $currunt_date->diffInDays($end_date, false);
                    // }
                    if ($start_date <= $currunt_date && $end_date >= $currunt_date) {
                        $difference = $currunt_date->diffInDays($end_date, false);
                    } else {
                        $difference = $start_date->diffInDays($end_date, false);
                    }
                    if ($difference >= 29) {
                        $wE = $monthlyExpected[$i]->amount;
                        $wE = $wE * 30;
                        $sum2 = $sum2 + $wE;
                    } else {
                        $wE = $monthlyExpected[$i]->amount;
                        $difference = $difference + 1;
                        $wE = $wE * $difference;
                        $sum2 = $sum2 + $wE;
                    }
                    // dd($sum2);
                    // }
                }
                $monthlyExpected = $sum2;
                $monthlyUnpaidAmount = $monthlyExpected - $monthlyPaidAmount;
                $fullYearlyPaidAmount = $this->paymentRepository->getEqubTypeYearlyPaidAmount($equb_type_id);
                $yearlyPendingAmount = $this->paymentRepository->getEqubTypeYearlyPendingAmount($equb_type_id);
                $yearlyPaidAmount =  $fullYearlyPaidAmount + $yearlyPendingAmount;
                $yearlyExpected  = $this->equbRepository->getEqubTypeExpectedAmount($equb_type_id);
                $sum3 = 0;
                $index = count($yearlyExpected);
                for ($i = 0; $i < $index; $i++) {
                    $end_date = $yearlyExpected[$i]->end_date;
                    $end_date = \Carbon\Carbon::parse($end_date);
                    $start_date = $yearlyExpected[$i]->start_date;
                    $start_date = \Carbon\Carbon::parse($start_date);
                    $currunt_date = \Carbon\Carbon::today();
                    // if ($end_date >= $currunt_date) {
                    // if ($start_date >= $currunt_date) {
                    //     $difference = $start_date->diffInDays($end_date, false);
                    // } else {
                    //     $difference = $currunt_date->diffInDays($end_date, false);
                    // }
                    if ($start_date <= $currunt_date && $end_date >= $currunt_date) {
                        $difference = $currunt_date->diffInDays($end_date, false);
                    } else {
                        $difference = $start_date->diffInDays($end_date, false);
                    }
                    if ($difference >= 364) {
                        $wE = $yearlyExpected[$i]->amount;
                        $wE = $wE * 365;
                        $sum3 = $sum3 + $wE;
                    } else {
                        $wE = $yearlyExpected[$i]->amount;
                        $difference = $difference + 1;
                        $wE = $wE * $difference;
                        $sum3 = $sum3 + $wE;
                    }
                    // }
                }
                $yearlyExpected = $sum3;
                $yearlyUnpaidAmount = $yearlyExpected - $yearlyPaidAmount;
                $totalMember = $this->memberRepository->getEqubTypeMember($equb_type_id);
                $totalUser = $this->userRepository->getUser();
                $tudayPaidMember = $this->equbRepository->tudayEqubTypePaidMember($equb_type_id);
                $automaticMembersArray = [];
                $automaticWinnerMembers = LotteryWinner::where('created_at', ">", Carbon::today()->format('Y-m-d'))
                    ->where('equb_type_id', $equb_type_id)
                    ->orderBy('created_at', 'desc')
                    ->get();
                // dd($automaticWinnerMembers);
                foreach ($automaticWinnerMembers as $member) {
                    $memberInfo = Member::where('id', $member->member_id)->first();
                    if ($memberInfo) {
                        array_push($automaticMembersArray, [
                            "full_name" => $memberInfo->full_name,
                            "phone" => $memberInfo->phone,
                            "gender" => $memberInfo->gender
                        ]);
                    }
                }
                return  response()->json(compact('title', 'lables', 'fullPaidAmount', 'Expected', 'daylyPaidAmount', 'daylyUnpaidAmount', 'daylyExpected', 'weeklyPaidAmount', 'weeklyExpected', 'monthlyPaidAmount', 'monthlyExpected', 'yearlyPaidAmount', 'yearlyExpected', 'totalMember', 'tudayPaidMember', 'activeMember', 'totalEqubPayment'), 200);
                // return view('admin/equbtype-dashboard', compact('equb_type_id', 'automaticMembersArray', 'title', 'lables', 'fullPaidAmount', 'fullUnPaidAmount', 'Expected', 'daylyPaidAmount', 'daylyUnpaidAmount', 'daylyExpected', 'weeklyPaidAmount', 'weeklyUnpaidAmount', 'weeklyExpected', 'monthlyPaidAmount', 'monthlyUnpaidAmount', 'monthlyExpected', 'yearlyPaidAmount', 'yearlyUnpaidAmount', 'yearlyExpected', 'totalMember', 'tudayPaidMember', 'activeMember', 'totalUser', 'totalEqubPayment'));
            // } elseif ($userData && ($userData['role'] == "equb_collector")) {
            //     // return redirect('/member/');
            //     return response()->json([
            //         'code' => 400,
            //         'message' => 'You can\'t perform this action!'
            //     ]);
            // } elseif ($userData && ($userData['role'] == "member")) {
            //     // return redirect('/member/');
            //     return response()->json([
            //         'code' => 400,
            //         'message' => 'You can\'t perform this action!'
            //     ]);
            // } else {
            //     return response()->json([
            //         'code' => 400,
            //         'message' => 'You can\'t perform this action!'
            //     ]);
            // }
        } catch (Exception $ex) {
            // dd($ex);
            return response()->json([
                'code' => 400,
                'message' => 'Unable to process your request, Please try again!'
            ]);
        }
    }
    public function logout()
    {
        return response()->json([
            'code' => 400,
            'message' => 'You can\'t perform this action!'
        ]);
    }
}
