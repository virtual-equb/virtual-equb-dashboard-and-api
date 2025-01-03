<?php

namespace App\Http\Controllers;

use App\Models\Equb;
use App\Models\EqubType;
use App\Models\LotteryWinner;
use App\Models\Member;
use App\Repositories\Payment\IPaymentRepository;
use App\Repositories\Equb\IEqubRepository;
use App\Repositories\EqubType\IEqubTypeRepository;
use App\Repositories\Member\IMemberRepository;
use App\Repositories\User\IUserRepository;
use Illuminate\Support\Arr;
use App\Models\Payment;
use App\Repositories\MainEqub\MainEqubRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;

class HomeController extends Controller
{
    private $paymentRepository;
    private $equbRepository;
    private $memberRepository;
    private $userRepository;
    private $title;
    private $mainEqubRepository;
    public function __construct(IPaymentRepository $paymentRepository, IEqubRepository $equbRepository, IEqubTypeRepository $equbTypeRepository, IMemberRepository $memberRepository, IUserRepository $userRepository, MainEqubRepositoryInterface $mainEqubRepository)
    {
        $this->paymentRepository = $paymentRepository;
        $this->equbRepository = $equbRepository;
        $this->memberRepository = $memberRepository;
        $this->userRepository = $userRepository;
        $this->title = "Virtual Equb - Dashboard";
        $this->mainEqubRepository = $mainEqubRepository;

        // Permission Guard
        // $this->middleware('permission:view dashboard', ['only' => ['index', 'show', 'equbTypeIndex']]);
        // $this->middleware('permission_check_logout:view dashboard', ['only' => ['index', 'show', 'equbTypeIndex']]);
    }
    //Projection chart updated here
    public function index()
    {
        try {
            $userData = Auth::user();
            $roles = ['admin', 'general_manager', 'operation_manager', 'it', 'finance', 'marketing_manager', 'call_center', 'it', 'assistant'];
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
            // dd($Expected);
            $lables = $lable->toArray();
            $fullPaidAmount = $fullPaidAmount->toArray();
            $Expected = $Expected->toArray();
            $fullPaidAmount = Arr::pluck($fullPaidAmount, 'paidAmount');
            $Expected = Arr::pluck($Expected, 'expected');
            $lables = json_encode($lables, JSON_UNESCAPED_UNICODE);
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
            $weeklyExpected1  = $this->equbRepository->getExpectedBackPayment();
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
            $totalMember = $this->memberRepository->getMember();
            $totalUser = $this->userRepository->getUser();
            $tudayPaidMember = $this->equbRepository->tudayPaidMember();
            // $automaticMembersArray = [];
            // $automaticWinnerMembers = LotteryWinner::where('created_at', ">", Carbon::today()->format('Y-m-d'))->orderBy('created_at', 'desc')->get();
            // foreach ($automaticWinnerMembers as $member) {
            //     $memberInfo = Member::where('id', $member->member_id)->first();
            //     if ($memberInfo) {
            //         array_push($automaticMembersArray, [
            //             "full_name" => $memberInfo->full_name,
            //             "phone" => $memberInfo->phone,
            //             "gender" => $memberInfo->gender
            //         ]);
            //     }
            // }
            $automaticWinnerMembers = LotteryWinner::with('member')
                        ->whereDate('created_at', Carbon::today())
                        ->get();
            $automaticMembersArray = $automaticWinnerMembers->filter(function ($winner) {
                            return $winner->member; // Include only winners with a related member
                        })->map(function ($winner) {
                            return [
                                'full_name' => $winner->member->full_name,
                                'phone' => $winner->member->phone,
                                'gender' => $winner->member->gender
                            ];
                        });
                return view('admin/home', compact(
                           'automaticMembersArray',  
                           'title', 
                           'lables', 
                           'fullPaidAmount', 
                           'fullUnPaidAmount', 
                           'Expected', 
                           'daylyPaidAmount', 
                           'daylyUnpaidAmount', 
                           'daylyExpected', 
                           'weeklyPaidAmount', 
                           'weeklyUnpaidAmount', 
                           'weeklyExpected', 
                           'monthlyPaidAmount', 
                           'monthlyUnpaidAmount', 
                           'monthlyExpected', 
                           'yearlyPaidAmount', 
                           'yearlyUnpaidAmount', 
                           'yearlyExpected', 
                           'totalMember', 
                           'tudayPaidMember', 
                           'activeMember', 
                           'totalUser', 
                           'totalEqubPayment'
                        ));
        } catch (Exception $ex) {
            // dd($ex);
            $msg = "Unable to process your request, Please try again!";
            $type = 'error';
            Session::flash($type, $msg);
            return back();
        }
    }
    public function index1()
    {
        try {
            $title = $this->title;

            // Use repositories to fetch aggregate data
            $totalEqubAmount = $this->equbRepository->getExpectedTotal();
            $totalEqubPayment = $this->paymentRepository->getTotalPayment();
            $activeMember = $this->memberRepository->getActiveMember();
            $totalMember = $this->memberRepository->getMember();
            $totalUser = $this->userRepository->getUser();
            $tudayPaidMember = $this->equbRepository->tudayPaidMember();
            // dd($tudayPaidMember);
            // Fetch winner data
            // $automaticWinnerMembers = LotteryWinner::whereDate('created_at', Carbon::today())->get();
            $automaticWinnerMembers = LotteryWinner::with('member')
                        ->whereDate('created_at', Carbon::today())
                        ->get();

            // $automaticMembersArray = $automaticWinnerMembers->map(function ($winner) {
            //     if ($winner->member) { // Check if the related member exists
            //         return [
            //             'full_name' => $winner->member->full_name,
            //             'phone' => $winner->member->phone,
            //             'gender' => $winner->member->gender,
            //         ];
            //     }
            //     return null; // Return a placeholder or skip null members
            // })->filter(); // Remove null values from the collection
            $automaticMembersArray = $automaticWinnerMembers->filter(function ($winner) {
                return $winner->member; // Include only winners with a related member
            })->map(function ($winner) {
                return [
                    'full_name' => $winner->member->full_name,
                    'phone' => $winner->member->phone,
                    'gender' => $winner->member->gender
                ];
            });
            // dd($automaticMembersArray);
            // Daily Payments and Expected Amount
            $fullDaylyPaidAmount = $this->paymentRepository->getDaylyPaidAmount();
            $daylyPendingAmount = $this->paymentRepository->getDaylyPendingAmount();
            $daylyPaidAmount = $fullDaylyPaidAmount + $daylyPendingAmount;
            $daylyUnpaidAmount = max(0, $totalEqubAmount - $daylyPaidAmount);
            $daylyExpected = $totalEqubAmount;

            // Weekly stats
            $weeklyExpected = $this->calculateWeeklyExpected();
            $fullWeeklyPaidAmount = $this->paymentRepository->getWeeklyPaidAmount();
            $weeklyPaidAmount = $fullWeeklyPaidAmount;
            $weeklyUnpaidAmount = $weeklyExpected - $fullWeeklyPaidAmount;

            // Monthly stats
            $monthlyExpected = $this->calculateMonthlyExpected();
            $fullMonthlyPaidAmount = $this->paymentRepository->getMonthlyPaidAmount();
            $monthlyPaidAmount = $fullMonthlyPaidAmount;
            $monthlyUnpaidAmount = $monthlyExpected - $fullMonthlyPaidAmount;

            // Yearly Stats
            $yearlyExpected = $this->calculateYearlyExpected();
            $fullYearlyPaidAmount = $this->paymentRepository->getYearlyPaidAmount();
            $yearlyPaidAmount = $fullYearlyPaidAmount;
            $yearlyUnpaidAmount = $yearlyExpected - $fullYearlyPaidAmount;

            // chart Data (Daily Stats)
            $chartData = $this->generateChartData();

            // Extract chart variables
            $lables = $chartData['lables'];
            $fullPaidAmount = $chartData['fullPaidAmount'];
            $fullUnPaidAmount = $chartData['fullUnpaidAmount'];
            $Expected = $chartData['Expected'];

            // Return data to blade view 
            return view('admin/home', compact(
                'automaticMembersArray',
                'title',
                'lables',
                'fullPaidAmount',
                'fullUnPaidAmount',
                'Expected',
                'daylyPaidAmount',
                'daylyUnpaidAmount',
                'daylyExpected',
                'weeklyPaidAmount',
                'weeklyUnpaidAmount',
                'weeklyExpected',
                'monthlyPaidAmount',
                'monthlyUnpaidAmount',
                'monthlyExpected',
                'yearlyPaidAmount',
                'yearlyUnpaidAmount',
                'yearlyExpected',
                'totalMember',
                'tudayPaidMember',
                'activeMember',
                'totalUser',
                'totalEqubPayment'
            ));

        } catch (Exception $ex) {
            Session::flash('error', "Unable to process your request, Please try again!" . $ex->getMessage());
            return back();
        }
    }
    private function calculateWeeklyExpected()
    {
        $weeklyExpected = $this->equbRepository->getExpectedAmount();
        $sum = 0;

        foreach ($weeklyExpected as $expected) {
            $end_date = Carbon::parse($expected->end_date);
            $start_date = Carbon::parse($expected->start_date);
            $currunt_date = Carbon::today();

            if ($start_date <= $currunt_date && $end_date >= $currunt_date) {
                $difference = $currunt_date->diffInDays($end_date, false);
            } else {
                $difference = $start_date->diffInDays($end_date, false);
            }

            $days = min(7, $difference + 1); // Max days = 7
            $sum += $expected->amount * $days;
        }

        return $sum;
    }
    private function calculateMonthlyExpected()
    {
        $monthlyExpected = $this->equbRepository->getExpectedAmount();
        $sum = 0;

        foreach ($monthlyExpected as $expected) {
            $end_date = Carbon::parse($expected->end_date);
            $start_date = Carbon::parse($expected->start_date);
            $currunt_date = Carbon::today();

            if ($start_date <= $currunt_date && $end_date >= $currunt_date) {
                $difference = $currunt_date->diffInDays($end_date, false);
            } else {
                $difference = $start_date->diffInDays($end_date, false);
            }

            $days = min(30, $difference + 1); // max days = 30
            $sum += $expected->amount * $days;
        }

        return $sum;
    }
    private function calculateYearlyExpected()
    {
        $yearlyExpected = $this->equbRepository->getExpectedAmount();
        $sum = 0;

        foreach ($yearlyExpected as $expected) {
            $end_date = Carbon::parse($expected->end_date);
            $start_date = Carbon::parse($expected->start_date);
            $currunt_date = Carbon::today();

            if ($start_date <= $currunt_date && $end_date >= $currunt_date) {
                $difference = $currunt_date->diffInDays($end_date, false);
            } else {
                $difference = $start_date->diffInDays($end_date, false);
            }

            $days = min(365, $difference + 1); // max days = 365
            $sum += $expected->amount * $days;
        }

        return $sum;
    }
    private function generateChartData()
    {
        $lables = Payment::join('equbs', 'payments.equb_id', '=', 'equbs.id')
                ->join('equb_types', 'equb_types.id', '=', 'equbs.equb_type_id')
                ->groupBy('equb_types.name')
                ->orderBy('equb_types.id', 'asc')
                ->whereDate('payments.created_at', '>=', date('Y-m-d'))
                ->pluck('equb_types.name');
        // $lable = Payment::join('equbs', 'payments.equb_id', '=', 'equbs.id')
        //         ->join('equb_types', 'equb_types.id', '=', 'equbs.equb_type_id')
        //         ->groupBy('equb_types.name')
        //         ->orderBy('equb_types.id', 'asc')
        //         ->whereDate('payments.created_at', '>=', date('Y-m-d'))
        //         ->pluck('equb_types.name');
        // $lables = $lable->toArray();
        // $lables = json_encode($lables, JSON_UNESCAPED_UNICODE);
        // $lables = str_replace('"', "", $lables);
        if (empty($lables)) {
            $lables = ['No Data'];
        }
      
        // dd($lables);
        // $lables = $lables->toArray();

        $equbTypeId = Payment::join('equbs', 'payments.equb_id', '=', 'equbs.id')
                    ->join('equb_types', 'equb_types.id', '=', 'equbs.equb_type_id')
                    ->groupBy('equb_types.id')
                    ->whereDate('payments.created_at', '>=', date('Y-m-d'))
                    ->pluck('equb_types.id');

        $fullPaidAmount = Payment::selectRaw('sum(payments.amount) as paidAmount')
                ->join('equbs', 'payments.equb_id', '=', 'equbs.id') // Ensure proper join
                ->join('equb_types', 'equb_types.id', '=', 'equbs.equb_type_id')
                ->groupBy('equb_types.name')
                ->get();
        $fullPaidAmount->toArray();
        $fullPaidAmount = Arr::pluck($fullPaidAmount, 'paidAmount');

        $Expected = $this->equbRepository->getExpected($equbTypeId);
        $Expected->toArray();
        $Expected = Arr::pluck($Expected, 'expected');

        

        // Fetch total unpaid amounts grouped by equb types
        $fullUnPaidAmount = Payment::selectRaw('sum(payments.amount) as unpaidAmount')
            ->join('equbs', 'payments.equb_id', '=', 'equbs.id')
            ->join('equb_types', 'equb_types.id', '=', 'equbs.equb_type_id')
            ->groupBy('equb_types.name')
            ->where('payments.status', 'unpaid')
            ->pluck('unpaidAmount');

        return [
            'lables' =>  $lables,
            'fullPaidAmount' => json_encode($fullPaidAmount),
            'Expected' => json_encode($Expected),
            'fullUnpaidAmount' => json_encode($fullUnPaidAmount)
        ];
    }
    public function equbTypeIndex($equb_type_id)
    {
        try {
            $userData = Auth::user();
            // if ($userData && ($userData['role'] == "admin" || $userData['role'] == "general_manager" || $userData['role'] == "operation_manager" || $userData['role'] == "it" || $userData['role'] == "finance" || $userData['role'] == "marketing_manager" || $userData['role'] == "customer_service" || $userData['role'] == "assistant")) {
                $profile = Auth::user();
                $title = $this->title;
                $totalEqubAmount = $this->equbRepository->getEqubTypeExpectedTotal($equb_type_id);
                $totalEqubPayment = $this->paymentRepository->getEqubTypeTotalPayment($equb_type_id);
                $activeMember = $this->memberRepository->getEqubTypeActiveMember($equb_type_id);
                $mainEqubs = $this->mainEqubRepository->all();
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
                return view('admin/equbtype-dashboard', compact('equb_type_id', 'automaticMembersArray', 'title', 'lables', 'fullPaidAmount', 'fullUnPaidAmount', 'Expected', 'daylyPaidAmount', 'daylyUnpaidAmount', 'daylyExpected', 'weeklyPaidAmount', 'weeklyUnpaidAmount', 'weeklyExpected', 'monthlyPaidAmount', 'monthlyUnpaidAmount', 'monthlyExpected', 'yearlyPaidAmount', 'yearlyUnpaidAmount', 'yearlyExpected', 'totalMember', 'tudayPaidMember', 'activeMember', 'totalUser', 'totalEqubPayment', 'mainEqubs'));
            // } elseif ($userData && ($userData['role'] == "equb_collector")) {
            //     return redirect('/member/');
            // } elseif ($userData && ($userData['role'] == "member")) {
            //     return redirect('/member/');
            // } else {
            //     return view('auth/login');
            // }
        } catch (Exception $ex) {
            // dd($ex);
            $msg = "Unable to process your request, Please try again!";
            $type = 'error';
            Session::flash($type, $msg);
            return back();
        }
    }
    public function logout()
    {
        return view('auth/login');
    }
}
