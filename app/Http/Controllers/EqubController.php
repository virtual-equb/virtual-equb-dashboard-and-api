<?php

namespace App\Http\Controllers;

use Exception;
use Carbon\Carbon;
use App\Models\Equb;
use App\Models\User;
use App\Models\Member;
use App\Models\EqubType;
use App\Models\RejectedDate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;
use App\Repositories\Equb\IEqubRepository;
use App\Repositories\Member\IMemberRepository;
use App\Repositories\Payment\IPaymentRepository;
use App\Repositories\EqubType\IEqubTypeRepository;
use App\Repositories\EqubTaker\IEqubTakerRepository;
use App\Repositories\ActivityLog\IActivityLogRepository;

class EqubController extends Controller
{
    private $activityLogRepository;
    private $paymentRepository;
    private $equbTypeRepository;
    private $equbRepository;
    private $equbTakerRepository;
    private $memberRepository;
    private $title;
    public function __construct(
        IEqubRepository $equbRepository,
        IMemberRepository $memberRepository,
        IPaymentRepository $paymentRepository,
        IEqubTypeRepository $equbTypeRepository,
        IEqubTakerRepository $equbTakerRepository,
        IActivityLogRepository $activityLogRepository
    ) {
        $this->activityLogRepository = $activityLogRepository;
        $this->memberRepository = $memberRepository;
        $this->paymentRepository = $paymentRepository;
        $this->equbTypeRepository = $equbTypeRepository;
        $this->equbRepository = $equbRepository;
        $this->equbTakerRepository = $equbTakerRepository;
        $this->title = "Virtual Equb - Equb";

        // Permission guard
        // $this->middleware('permission_check_logout:update equb', ['only' => ['update', 'edit', 'updateStatus']]);
        // $this->middleware('permission_check_logout:delete equb', ['only' => ['destroy']]);
        // $this->middleware('permission_check_logout:view equb', ['only' => ['index', 'show', 'getReservedLotteryDate']]);
        // $this->middleware('permission_check_logout:create equb', ['only' => ['store', 'create', 'addUnpaid']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $userData = Auth::user();
            $adminRoles = ['admin', 'general_manager', 'operation_manager', 'it', 'finance'];
            $memberRole = ['member'];
            $collectorRole = ['equb_collector'];
            if ($userData->hasAnyRole($adminRoles)) {
                $userData = Auth::user();
                $equbs = $this->equbRepository->getAll();
                return view('admin/equb.equbList', compact('equbs'));
            } elseif ($userData->hasRole($collectorRole)) {
                $userData = Auth::user();
                $equbs = $this->equbRepository->getAll();
                return view('equbCollecter/equb.equbList', compact('equbs'));
            } elseif ($userData->hasRole($member)) {
                $userData = Auth::user();
                $equbs = $this->equbRepository->getAll();
                return view('member/equb.equbList', compact('equbs'));
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
    public function sendStartNotifications()
    {
        try {
            $now = Carbon::now();
            $next24Hours = $now->copy()->addHours(24);

            // Retrieve Equbs where start_date is within the next 24 hours and not yet notified
            $dueEqubs = Equb::whereBetween('start_date', [$now, $next24Hours])
                            ->where('notified', 'No')
                            ->get();

            if ($dueEqubs->isEmpty()) {
                return response()->json([
                    'message' => 'No Equbs found within the due range.',
                    'code' => 200,
                    'count' => 0,
                    'debug' => [
                        'start_date_from' => $now->toDateTimeString(),
                        'start_date_to' => $next24Hours->toDateTimeString(),
                        'equbs_found' => $dueEqubs->toArray()
                    ]
                ]);
            }
            $count = 0;
            foreach ($dueEqubs as $equb) {
                $member = Member::find($equb->member_id);

                if ($member && $member->phone) {
                    
                        $startDate = Carbon::parse($equb->start_date);
                        $shortcode = config('key.SHORT_CODE');
                        $message = "Reminder: Your Equb will start on " . $startDate->format('Y-m-d H:i') . ". Please be prepared. For further information, call $shortcode";
                        
                
                        if ($this->sendSms($member->phone, $message)) {
                            $count++;
                            // Update the Equb notified field
                            $equb->update(['notified' => 'Yes']);
                        } else {
                            Log::error("Failed to send SMS to {$member->phone}");
                        }
                    
                }
            }

            return response()->json([
                'message' => 'Notification sent for due Equbs.',
                'code' => 200,
                'count' => $count
            ]);

        } catch (Exception $ex) {
            return response()->json([
                'message' => 'Unable to process your request, Please try again!',
                'code' => 500,
                'error' => $ex->getMessage(),
            ]);
        }
    }

    public function sendEndNotifications() 
    {
        try {
            $now = Carbon::now();
            $next24Hours = $now->copy()->addHours(24);

            $dueEqubs = Equb::whereBetween('end_date', [$now, $next24Hours])->get();

            if ($dueEqubs->isEmpty()) {
                return response()->json([
                    'message' => 'No Equbs found within the due range.',
                    'code' => 200,
                    'count' => 0,
                    'debug' => [
                        'end_date_from' => $now->toDateTimeString(),
                        'end_date_to' => $next24Hours->toDateTimeString(),
                        'equbs_found' => $dueEqubs->toArray()
                    ]
                    ]);
            }
            $count = 0;
            foreach($dueEqubs as $equb) {
                $member = Member::find($equb->member_id);

                if ($member && $member->phone) {
                    $endDate = Carbon::parse($equb->end_date);
                    $shortcode = config('key.SHORT_CODE');
                    $message = "Reminder: Your Equb will end on " . $endDate->format('Y-m-d H:i') . ". For further information, call $shortcode";
                    
                    $this->sendSms($member->phone, $message);
                    $count++;
                }

                return response()->json([
                    'message' => 'Notification sent for Ending Equbs',
                    'code' => 200,
                    'count' => $count
                ]);

            }
                                
        } catch (Exception $ex) {
            return response()->json([
                'message' => 'Unable to process your request, Please try again!',
                'code' => 500,
                'error' => $ex->getMessage(),
            ]);
        }
    }
    public function getReservedLotteryDate($lottery_date)
    {
        try {
            $equbId = [];
            $equbDetail = [];
            $lotteryDate = [];
            $ExpectedTotal = [];
            $lotteryDateList = explode(",", $lottery_date);
            foreach ($lotteryDateList as $c) {
                $equb_id = Equb::whereRaw('FIND_IN_SET("' . $c . '",lottery_date)')->pluck('id')->first();
                $lottery_date = Equb::whereRaw('FIND_IN_SET("' . $c . '",lottery_date)');
                if ($equb_id != null) {
                    array_push($equbId, $equb_id);
                }
                if ($lottery_date != null) {
                    array_push($lotteryDate, $c);
                }
            }
            foreach ($lotteryDate as $key => $value) {
                $Expected = $this->equbRepository->getExpectedByLotteryDate($value)->first();
                if ($lotteryDate != null) {
                    array_push($ExpectedTotal, $value . "______________" . $Expected->expected);
                }
            }

            $data['equbDetail'] = Equb::whereRaw('FIND_IN_SET("' . $c . '",lottery_date)')->with('member')->get();
            return view('admin/equb/lotteryDetail', compact('ExpectedTotal'), $data);
        } catch (Exception $ex) {
            $msg = "Unable to process your request, Please try again!";
            $type = 'error';
            Session::flash($type, $msg);
            return back();
        }
    }
    public function lotteryDateCheck(Request $request)
    {
        try {
            if (!empty($request->lottery_date)) {
                $lotteryDateCheck = $request->lottery_date;
                $lotteryDateList = explode(",", $lotteryDateCheck);
                $reserved_date = 0;
                foreach ($lotteryDateList as $c) {
                    $lottery_date_one = Equb::whereRaw('FIND_IN_SET("' . $c . '",lottery_date)')->count();
                    $reserved_date = $reserved_date + $lottery_date_one;
                }
                $lotteryDateCheck = $request->lottery_date;
                $rejected_date_count = 0;
                foreach ($lotteryDateList as $b) {
                    $lottery_date_two = RejectedDate::whereRaw('FIND_IN_SET("' . $b . '",rejected_date)')->count();
                    $rejected_date_count = $rejected_date_count + $lottery_date_two;
                }
                if ($rejected_date_count > 0) {
                    echo "false";
                } else {
                    echo "true";
                }
            } else {
                echo "true";
            }
        } catch (Exception $ex) {
            $msg = "Unable to process your request, Please try again!";
            $type = 'error';
            Session::flash($type, $msg);
            return back();
        }
    }
    public function lotteryDateCheckForUpdate(Request $request)
    {
        try {
            if (!empty($request->lottery_date)) {
                $equb_id = $request->equb_id;
                $lotteryDateCheck = $request->lottery_date;
                $lotteryDateList = explode(",", $lotteryDateCheck);
                $reserved_date = 0;
                foreach ($lotteryDateList as $c) {
                    $lottery_date_one = Equb::whereRaw('FIND_IN_SET("' . $c . '",lottery_date)')->where('id', '!=', $equb_id)->count();
                    $reserved_date = $reserved_date + $lottery_date_one;
                }
                $lotteryDateCheck = $request->lottery_date;
                $rejected_date_count = 0;
                foreach ($lotteryDateList as $b) {
                    $lottery_date_two = RejectedDate::whereRaw('FIND_IN_SET("' . $b . '",rejected_date)')->where('id', '!=', $equb_id)->count();
                    $rejected_date_count = $rejected_date_count + $lottery_date_two;
                }
                if ($rejected_date_count > 0) {
                    echo "false";
                } else {
                    echo "true";
                }
            } else {
                echo "true";
            }
        } catch (Exception $ex) {
            $msg = "Unable to process your request, Please try again!";
            $type = 'error';
            Session::flash($type, $msg);
            return back();
        }
    }

    public function getDailyPaidAmount($equb_id)
    {
        try {
            $daily_paid_amount = $this->equbRepository->getDailyPaid($equb_id);
            return ($daily_paid_amount);
        } catch (Exception $ex) {
            $msg = "Unable to process your request, Please try again!";
            $type = 'error';
            Session::flash($type, $msg);
            return back();
        }
    }
    public function equbCheck(Request $request)
    {
        try {
            if (!empty($request->equb_type_id)) {
                $equbType = $request->equb_type_id;
                $memberId = $request->member_id;
                $users_count = Equb::where('equb_type_id', $equbType)->where('member_id', '=', $memberId)->count();
                if ($users_count > 0) {
                    echo "false";
                } else {
                    echo "true";
                }
            } else {
                echo "true";
            }
        } catch (Exception $ex) {
            $msg = "Unable to process your request, Please try again!";
            $type = 'error';
            Session::flash($type, $msg);
            return back();
        }
    }
    public function equbCheckForUpdate(Request $request)
    {
        try {
            if (!empty($request->equb_type_id)) {
                $equbType = $request->equb_type_id;
                $equb_id = $request->equb_id;
                $memberId = $request->member_id;
                $users_count = Equb::where('equb_type_id', $equbType)->where('member_id', '=', $memberId)->where('id', '!=', $equb_id)->count();
                if ($users_count > 0) {
                    echo "false";
                } else {
                    echo "true";
                }
            } else {
                echo "true";
            }
        } catch (Exception $ex) {
            $msg = "Unable to process your request, Please try again!";
            $type = 'error';
            Session::flash($type, $msg);
            return back();
        }
    }
    public function dateEqubCheck(Request $request)
    {
        try {
            $date = $request->end_date;
            $equbTypeId = $request->equb_type_id;
            if (!empty($date)) {
                $equbTypeEndDate = $this->equbTypeRepository->getStartDate($equbTypeId)->end_date;
                $date = \Carbon\Carbon::parse($date);
                $today = \Carbon\Carbon::now()->format('Y-m-d');
                $today = \Carbon\Carbon::parse($today);
                $difference = $today->diffInDays($date, false);
                if ($difference < 1 || $date > $equbTypeEndDate) {
                    echo "false";
                } else {
                    echo "true";
                }
            } else {
                echo "true";
            }
        } catch (Exception $ex) {
            $msg = "Unable to process your request, Please try again!";
            $type = 'error';
            Session::flash($type, $msg);
            return back();
        }
    }
    public function startDateCheck(Request $request)
    {
        try {
            $date = $request->start_date;
            $equbTypeId = $request->equb_type_id;
            if (!empty($date)) {
                $equbTypeStartDate = $this->equbTypeRepository->getStartDate($equbTypeId)->start_date;
                $date = \Carbon\Carbon::parse($date);
                $today = \Carbon\Carbon::now()->format('Y-m-d');
                $today = \Carbon\Carbon::parse($today);
                $difference = $today->diffInDays($date, false);
                if ($difference < 1 || $date < $equbTypeStartDate) {
                    echo "false";
                } else {
                    echo "true";
                }
            } else {
                echo "true";
            }
        } catch (Exception $ex) {
            $msg = "Unable to process your request, Please try again!";
            $type = 'error';
            Session::flash($type, $msg);
            return back();
        }
    }
    public function dateEqubLotteryCheck(Request $request)
    {
        try {
            $date = $request->lottery_date;
            if (!empty($date)) {
                $date = \Carbon\Carbon::parse($date);
                $today = \Carbon\Carbon::now()->format('Y-m-d');
                $today = \Carbon\Carbon::parse($today);
                $difference = $today->diffInDays($date, false);
                if ($difference < 1) {
                    echo "false";
                } else {
                    echo "true";
                }
            } else {
                echo "true";
            }
        } catch (Exception $ex) {
            $msg = "Unable to process your request, Please try again!";
            $type = 'error';
            Session::flash($type, $msg);
            return back();
        }
    }
    /*public function show($id)
    {
        try {
            $userData = Auth::user();
            $adminRoles = ['admin', 'general_manager', 'operation_manager', 'it', 'finance','call_center'];
            $memberRole = ['member'];
            $collectorRole = ['equb_collector'];
    
            // Fetch equb data based on user role
            if ($userData->hasAnyRole($adminRoles)) {
                $equbTakerData['equb'] = $this->equbRepository->getByIdNestedForLottery($id);
            } elseif ($userData->hasRole($collectorRole)) {
                $equbTakerData['equb'] = $this->equbRepository->getByIdNested($id);
            } elseif ($userData->hasRole($memberRole)) {
                $equbTakerData['equb'] = $this->equbRepository->getByIdNested($id);
            } else {
                return view('auth/login');
            }
    
            // Fetch total payments
            $equbTakerData['total'] = $this->paymentRepository->getTotal($id);
    
            // Get paginated payment details
            $equbTakerData['payments'] = $this->paymentRepository->getPaginatedPayments($id);
    
            // Return appropriate view based on user role
            if ($userData->hasAnyRole($adminRoles)) {
                return view('admin/equb.equbDetails', $equbTakerData);
            } elseif ($userData->hasRole($collectorRole)) {
                return view('equbCollecter/equb.equbDetails', $equbTakerData);
            } elseif ($userData->hasRole($memberRole)) {
                return view('member/equb.equbDetails', $equbTakerData);
            }
    
        } catch (Exception $ex) {
            $msg = "Unable to process your request, Please try again!";
            $type = 'error';
            Session::flash($type, $msg);
            return back();
        }
    }*/
    public function show($id)
    {
        try {
            $userData = Auth::user();
            $adminRoles = ['admin', 'general_manager', 'operation_manager', 'it', 'finance', 'call_center', 'assistant', 'collector and finance', 'Customer service supervisor', 'Legal Affair Officers', 'Marketing Manager'];
            $memberRole = ['member'];
            $collectorRole = ['equb_collector'];
         //   $userData = Auth::user();
         if ($userData->hasAnyRole($adminRoles)) {
                $equbTakerData['equb'] = $this->equbRepository->getByIdNestedForLottery($id);
                $equbTakerData['total'] = $this->paymentRepository->getTotal($id);
                return view('admin/equb.equbDetails', $equbTakerData);
            } elseif ($userData && ($userData['role'] == "equb_collector")) {
                $equbTakerData['equb'] = $this->equbRepository->getByIdNested($id);
                $equbTakerData['total'] = $this->paymentRepository->getTotal($id);
                return view('equbCollecter/equb.equbDetails', $equbTakerData);
            } elseif ($userData && ($userData['role'] == "member")) {
                $equbTakerData['equb'] = $this->equbRepository->getByIdNested($id);
                $equbTakerData['total'] = $this->paymentRepository->getTotal($id);
                return view('member/equb.equbDetails', $equbTakerData);
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
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        try {
            $userData = Auth::user();
            $adminRoles = ['admin', 'general_manager', 'operation_manager', 'it', 'finance'];
            $memberRole = ['member'];
            $collectorRole = ['equb_collector'];
            if ($userData->hasAnyRole($adminRoles)) {
                $data['title'] = $this->title;
                return view('admin/equb/addEqub', $data);
            } elseif ($userData->hasRole($collectorRole)) {
                $data['title'] = $this->title;
                return view('equbCollecter/equb/addEqub', $data);
            } elseif ($userData->hasRole($memberRole)) {
                $data['title'] = $this->title;
                return view('member/equb/addEqub', $data);
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
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $userData = Auth::user();
            $equbType = EqubType::where('id', $request->input('equb_type_id'))->first();
            // if ($userData && ($userData['role'] == "admin") || ($userData['role'] == "equb_collector")) {
                $this->validate($request, [
                    'equb_type_id' => 'required',
                    // 'amount' => ['required', 
                    //     function ($attribute, $value, $fail) use ($request) {
                    //         // Check if the equb type is 'Manual'
                    //         $equbType = EqubType::find($request->input('equb_type_id'));
                    //         if ($equbType && $equbType->type === 'Manual') {
                    //             // Validate amount range for 'Manual' equb type
                    //             if ($value < 500 || $value > 15000) {
                    //                 $fail("The {$attribute} must be between 500 and 15000.");
                    //             }
                    //         }
                    //     }
                    // ],
                    'amount' => 'required',
                    'total_amount' => 'nullable',
                    'start_date' => 'required',
                    // 'end_date' => 'required',
                    // 'timeline' => 'required',
                    // 'lottery_date' => 'required',
                ]);
                $member = $request->input('member_id');
                $equbType = $request->input('equb_type_id');
                $amount = $request->input('amount');
                $totalAmount = $request->input('total_amount');
                $startDate = $request->input('start_date');
                $timeline = $request->input('timeline');
                $endDate = $request->input('end_date');
                $lotteryDate = $request->input('lottery_date');
                $startDateCheck = $this->isDateInYMDFormat($startDate);
                $endDateCheck = $this->isDateInYMDFormat($endDate);
                $formattedStartDate = $startDate;
                $formattedEndDate = $endDate;
                if (!$startDateCheck) {
                    $carbonStartDate = Carbon::createFromFormat('m/d/Y', $startDate);
                    $formattedStartDate = $carbonStartDate->format('Y-m-d');
                }
                if (!$endDateCheck) {
                    $carbonDate = Carbon::createFromFormat('m/d/Y', $endDate);
                    $formattedEndDate = $carbonDate->format('Y-m-d');
                }

                // Retreive the equbType data
                $equbTypeData = EqubType::find($equbType);
                if (!$equbTypeData) {
                    return response()->json([
                        'code' => 404,
                        'message' => 'Equb type not found.'
                    ]);
                }
                // Check if the equb already exists for the member
                if (!empty($equbType)) {
                    $equbs_count = Equb::where('equb_type_id', $equbType)
                                        ->where('member_id', $member)
                                        ->count();
                    if ($equbs_count > 0) {
                        return response()->json([
                            'code' => 400,
                            'message' => 'Equb already exists!'
                        ]);
                    }
                }

                // Automatically calculate lottery date for 'Manual' type
                // if ($equbTypeData->type === 'Manual') {
                //     // Set lottery_date to 45 days after the start date if it's not provided
                //     if (!$lotteryDate) {
                //         $lotteryDate = Carbon::parse($formattedStartDate)->addDays(45)->format('Y-m-d');
                //     }

                //     // check if there are existing lotteries on the same day (to avoid conflicts)
                //     $existingLottery = Equb::where('lottery_date', $lotteryDate)->exists();
                //     if ($existingLottery) {
                //         $msg = 'Lottery date already exists!';
                //         $type = 'error';
                //         Session::flash($type, $msg);
                //         // return response()->json([
                //         //     'code' => 400,
                //         //     'message' => 'Lottery date already exists!'
                //         // ]);
                //     }

                //     // Ensure total funds available for the lottery (Projection check)
                //     $cashProjection = Equb::whereDate('lottery_date', $lotteryDate)->sum('amount');
                //     if ($cashProjection < $totalAmount) {
                //         // if the cash projection is insufficient, extend the lottery date by 1 day
                //         $lotteryDate = Carbon::parse($lotteryDate)->addDay()->format('Y-m-d');
                //     }
                // }
                if ($equbTypeData->type === 'Manual') {
                    // Set initial lottery_date (45 days after start date) if not provided
                    if (!$lotteryDate) {
                        $lotteryDate = Carbon::parse($formattedStartDate)->addDays(45)->format('Y-m-d');
                    }
                
                    // Get all upcoming lottery dates with their total funds
                    $futureLotteries = Equb::select('lottery_date', DB::raw('SUM(amount) as total_funds'))
                        ->whereDate('lottery_date', '>=', $lotteryDate)
                        ->groupBy('lottery_date')
                        ->orderBy('lottery_date', 'asc')
                        ->get();
                
                    // Find the earliest date with enough funds
                    foreach ($futureLotteries as $lottery) {
                        if ($lottery->total_funds >= $totalAmount) {
                            $lotteryDate = $lottery->lottery_date;
                            break;
                        }
                    }
                
                    // If no suitable date was found, pick the next available day
                    if (!$futureLotteries->contains('lottery_date', $lotteryDate)) {
                        $lotteryDate = Carbon::parse($lotteryDate)->addDay()->format('Y-m-d');
                    }
                
                    // Store the lottery date (it will now have enough funds)
                }

                // Determine lottery_date based on equbType
                if($equbTypeData->type === 'Automatic') {
                    $lotteryDate = $equbTypeData->lottery_date;
                    $totalAmount = $equbTypeData->total_amount;
                } else {
                    $lotteryDate = $request->input('lottery_date');
                }
                if($equbTypeData->type === 'Seasonal') {
                    $lotteryDate = $equbTypeData->lottery_date;
                    $totalAmount = $equbTypeData->amount;
                }

                $equbData = [
                    'member_id' => $member,
                    'equb_type_id' => $equbType,
                    'amount' => $amount,
                    'total_amount' => $totalAmount,
                    'start_date' => $formattedStartDate,
                    'end_date' => $formattedEndDate,
                    'timeline' => $timeline,
                    'lottery_date' => $lotteryDate,
                ];
                $create = $this->equbRepository->create($equbData);
                if ($create) {
                    $equbTypes = EqubType::where('id', $equbType)->first();
                    if ($equbTypes->type == 'Automatic' || $equbTypes->type == 'Seasonal') {
                        // Update remaining_quota based on expected_members - 1
                        $equbTypes->remaining_quota = $equbTypes->expected_members - $equbTypes->total_members - 1;
                        
                       
                        // Manually increment total_members by 1
                         $equbTypes->total_members = $equbTypes->total_members + 1;
                         
                          // Increment total_members by 1 since a new member joined
                        // $equbTypes->increment('total_members', 1);
            
                        // Check if the remaining_quota is now 0 and deactivate if true
                        if ($equbTypes->remaining_quota == 0) {
                            $equbTypes->status = "Deactive";
                        }
            
                        // Save the updated EqubType
                        $equbTypes->save();
                    }
                    
                }
                $equbTakerData = [
                    'member_id' => $member,
                    'equb_id' => $create->id,
                    'payment_type' => '',
                    'amount' => $totalAmount,
                    'remaining_amount' => $totalAmount,
                    'status' => 'unpaid',
                    'paid_by' => '',
                    'total_payment' => 0,
                    'remaining_payment' => $totalAmount,
                    'cheque_amount' => '',
                    'cheque_bank_name' => '',
                    'cheque_description' => '',
                ];
                $createEkubTaker = $this->equbTakerRepository->create($equbTakerData);
                // dd($equbTakerData, $createEkubTaker);
                
                if ($create) {
                    $activityLog = [
                        'type' => 'equbs',
                        'type_id' => $create->id,
                        'action' => 'created',
                        'user_id' => $userData->id,
                        'username' => $userData->name,
                        'role' => $userData->role,
                    ];
                    $this->activityLogRepository->createActivityLog($activityLog);

                    // Send Notifications for members
                    $shortcode = config('key.SHORT_CODE');
                    $member = Member::find($member);
                    if ($member && $member->phone) {
                        $memberMessage = "Dear {$member->full_name}, Your Equb has been successfully registered. Our customer service will contact you soon. For more information call {$shortcode}";
                        $this->sendSms($member->phone, $memberMessage);
                    }

                    // Finance Sms
                    $finances = User::role('finance')->get();
                    foreach ($finances as $finance) {
                        if ($finance->phone_number) {
                            $financeMessage = "Finance Alert: A New Member {$member->full_name}, has joined Equb titled {$create->equbType->name}. Please review the details.";
                            $this->sendSms($finance->phone_number, $financeMessage);
                        }
                    }

                    // Call center sms
                    $call_centers = User::role('call_center')->get();
                    foreach($call_centers as $finance) {
                        if ($finance->phone_number) {
                            $financeMessage = "Call Center Alert: A New Member {$member->full_name}, has joined Equb titled {$create->equbType->name}. Please review the details.";
                            $this->sendSms($finance->phone_number, $financeMessage);
                        }
                    }
                    
                    $msg = "Equb has been registered successfully!";
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
            $msg = "Unknown Error Occurred, Please try again!" . $ex->getMessage();
            $type = 'error';
            Session::flash($type, $msg);
            return back();
        }
    }
    public function store1(Request $request) 
    {
        try {
            $userData = Auth::user();

            $rules = [
                'type' => 'required|in:Manual,Automatic',
                'amount' => ['required', function ($attribute, $value, $fail) use ($request) {
                    if ($request->input('type') === 'Manual' && ($value < 500 || $value > 15000)) {
                        $fail("The {$attribute} must be between 500 and 15000.");
                    }
                }],
                'total_amount' => 'required',
                'start_date' => 'required|date_format:Y-m-d',
            ];
            if ($request->input('type') === 'Automatic') {
                $rules['equb_type_id'] = 'required|exists:equb_types,id';
            }
            $this->validate($request, $rules);

            $type = $request->input('type');
            $amount = $request->input('amount');
            $totalAmount = $request->input('total_amount');
            $startDate = $request->input('start_date');
            $endDate = $request->input('end_date');
            $timeline = $request->input('timeline');
            $lotteryDate = $request->input('lottery_date');
            $memberId = $request->input('member_id');
            $main_equb_id = $request->input('main_equb_id');

            // Format end date if needed
            if (!$this->isDateInYMDFormat($endDate)) {
                try {
                    $carbonDate = Carbon::createFromFormat('m/d/Y', $endDate);
                    $endDate = $carbonDate->format('Y-m-d');
                } catch (Exception $ex) {
                    $msg = "Invalid date format for end date!";
                    $type = 'error';
                    Session::flash($type, $msg);
                }
            }
            // Handle Manual Equb (Create new EqubType)
            if ($type === 'Manual') {
                $equbType = EqubType::create([
                    'name' => 'Manual Equb -' . now()->timestamp,
                    'main_equb_id' => $main_equb_id,
                    'round' => 1,
                    'amount' => $amount,
                    'total_amount' => $totalAmount,
                    'total_members' => 0,
                    'expected_members' => 0,
                    'status' => 'active',
                    'remark' => 'Auto-created Manual Equb',
                    'rote' => 'Daily',
                    'type' => 'Manual',
                    'terms' => 'Standard terms apply',
                    'quota' => 0,
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                    'remaining_quota' => 0,
                    'image' => null,
                    'lottery_round' => 0
                ]);
            } else {
                $equbType = EqubType::find($request->input('equb_type_id'));
                if (!$equbType) {
                    $msg = "Equb type not found";
                    $type = 'error';
                    Session::flash($type, $msg);
                }
            }

            // Prevent duplicate equb registration for the same member
            if (Equb::where('equb_type_id', $equbType->id)->where('member_id', $memberId)->exists()) {
                $msg = "Equb already exists for this member!";
                $type = "error";
                Session::flash($type, $msg);
            }

            // Automatically calculate lottery date for 'Manual' Equb
            if ($type === 'Manual') {
                if (!$lotteryDate) {
                    $lotteryDate = Carbon::parse($startDate)->addDays(45)->format('Y-m-d');
                }

                // check for existing lotteries on the same date
                if (Equb::where('lottery_date', $lotteryDate)->exists()) {
                    $msg = "Lottery date already exists for another equb.";
                    $type = "error";
                    Session::flash($type, $msg);
                }

                // Ensure sufficient funds before finalizing lottery date
                $cashProjection = Equb::whereDate('lottery_date', $lotteryDate)->sum('amount');
                if ($cashProjection < $totalAmount) {
                    // if insufficient funds, extend the lottery date
                    $lotteryDate = Carbon::parse($lotteryDate)->addDay()->format('Y-m-d');
                }
            }

            // Create equb entry
            $equbData = [
                'member_id' => $memberId,
                'equb_type_id' => $equbType->id,
                'amount' => $amount,
                'total_amount' => $totalAmount,
                'start_date' => $startDate,
                'timeline' => $timeline,
                'end_date' => $endDate,
                'lottery_date' => $lotteryDate
            ];

            $equb = $this->equbRepository->create($equbData);

            if ($equb) {
                // if automatic, update equbType member count
                if ($type === 'Automatic') {
                    $equbType->decrement('remaining_quota', 1);
                    $equbType->increment('total_members', 1);
                }

                // Register Equb taker
                $this->equbTakerRepository->create([
                    'member_id' => $memberId,
                    'equb_id' => $equb->id,
                    'payment_type' => '',
                    'amount' => $totalAmount,
                    'remaining_amount' => $totalAmount,
                    'status' => 'unpaid',
                    'paid_by' => '',
                    'total_payment' => 0,
                    'remaining_payment' => $totalAmount,
                    'cheque_amount' => '',
                    'cheque_bank_name' => '',
                    'cheque_description' => '',
                ]);
                 // Log activity
                $this->activityLogRepository->createActivityLog([
                    'type' => 'equbs',
                    'type_id' => $equb->id,
                    'action' => 'created',
                    'user_id' => $userData->id,
                    'username' => $userData->name,
                    'role' => $userData->role
                ]);

                // Send Notifications for members
                $shortcode = config('key.SHORT_CODE');
                $member = Member::find($memberId);
                if ($member && $member->phone) {
                    $memberMessage = "Dear {$member->full_name}, Your Equb has been successfully registered. Our customer service will contact you soon. For more information call {$shortcode}";
                    $this->sendSms($member->phone, $memberMessage);
                }

                // Finance Sms
                $finances = User::role('finance')->get();
                foreach ($finances as $finance) {
                    if ($finance->phone_number) {
                        $financeMessage = "Finance Alert: A New Member {$member->full_name}, has joined Equb titled {$equb->equbType->name}. Please review the details.";
                        $this->sendSms($finance->phone_number, $financeMessage);
                    }
                }

                // Call center sms
                $call_centers = User::role('call_center')->get();
                foreach($call_centers as $finance) {
                    if ($finance->phone_number) {
                        $financeMessage = "Call Center Alert: A New Member {$member->full_name}, has joined Equb titled {$equb->equbType->name}. Please review the details.";
                        $this->sendSms($finance->phone_number, $financeMessage);
                    }
                }
                
                $msg = "Equb has been registered successfully!";
                $type = 'success';
                Session::flash($type, $msg);
                return redirect('/member');
            }

        } catch (Exception $ex) {
            return response()->json([
                'error' => $ex->getMessage()
            ], 500);
        }
    }
    public function isDateInYMDFormat($dateString)
    {
        try {
            // Attempt to parse the date using Carbon
            $parsedDate = Carbon::createFromFormat('Y-m-d', $dateString);

            // Check if the parsed date matches the input date string
            return $parsedDate->format('Y-m-d') === $dateString;
        } catch (\Exception $e) {
            // An exception will be thrown if parsing fails
            return false;
        }
    }
    public function addUnpaid($equbId)
    {
        $equb = $this->equbRepository->getById($equbId);
        $equbTakerData = [
            'member_id' => $equb->member_id,
            'equb_id' => $equb->id,
            'payment_type' => '',
            'amount' => $equb->total_amount,
            'remaining_amount' => $equb->total_amount,
            'status' => 'unpaid',
            'paid_by' => '',
            'total_payment' => 0,
            'remaining_payment' => 0,
            'cheque_amount' => '',
            'cheque_bank_name' => '',
            'cheque_description' => '',
        ];
        $createEkubTaker = $this->equbTakerRepository->create($equbTakerData);
        // dd($equb);
    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Equb  $equb
     * @return \Illuminate\Http\Response
     */
    public function edit(Equb $equb)
    {
        try {
            $userData = Auth::user();
            $adminRoles = ['admin', 'general_manager', 'operation_manager', 'it', 'finance'];
            $memberRole = ['member'];
            $collectorRole = ['equb_collector'];
            if ($userData->hasAnyRole($adminRoles)) {
                $data['equb'] = $this->equbRepository->getById($equb);
                return view('admin/member/updateMember', $data);
            } elseif ($userData->hasRole($collectorRole)) {
                $data['equb'] = $this->equbRepository->getById($equb);
                return view('equbCollecter/member/updateMember', $data);
            } elseif ($userData->hasRole($memberRole)) {
                $data['equb'] = $this->equbRepository->getById($equb);
                return view('member/member/updateMember', $data);
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
    // public function updateStatus($id, Request $request)
    // {
    //     try {
    //         $userData = Auth::user();
    //             $status = $this->equbRepository->getStatusById($id)->status;
    //             if ($status == "Deactive") {
    //                 $status = "Active";
    //             } else {
    //                 $status = "Deactive";
    //             }
    //             $updated = [
    //                 'status' => $status,
    //             ];
    //             $updated = $this->equbRepository->update($id, $updated);
    //             if ($updated) {
    //                 if ($status == "Deactive") {
    //                     $status = "Deactivated";
    //                 } else {
    //                     $status = "Activated";
    //                 }
    //                 $activityLog = [
    //                     'type' => 'members',
    //                     'type_id' => $id,
    //                     'action' => $status,
    //                     'user_id' => $userData->id,
    //                     'username' => $userData->name,
    //                     'role' => $userData->role,
    //                 ];
    //                 $this->activityLogRepository->createActivityLog($activityLog);
    //                 $msg = "Status updated successfully!";
    //                 $type = 'success';
    //                 Session::flash($type, $msg);
    //                 return redirect('/member');
    //             } else {
    //                 $msg = "Unknown error occurred, Please try again!";
    //                 $type = 'error';
    //                 Session::flash($type, $msg);
    //                 return redirect('/member');
    //             }
    //     } catch (Exception $ex) {
    //         $msg = "Unable to process your request, Please try again!";
    //         $type = 'error';
    //         Session::flash($type, $msg);
    //         return back();
    //     }
    // }
    public function updateStatus($id, Request $request)
    {
        try {
            $userData = Auth::user();
            $status = $request->status; // ✅ Get the status directly from the request

            $updated = $this->equbRepository->update($id, ['status' => $status]);

            if ($updated) {
                $activityLog = [
                    'type' => 'members',
                    'type_id' => $id,
                    'action' => ucfirst($status), // Log "Activated" or "Deactivated"
                    'user_id' => $userData->id,
                    'username' => $userData->name,
                    'role' => $userData->role,
                ];
                $this->activityLogRepository->createActivityLog($activityLog);

                // return response()->json(['success' => true, 'message' => "Status updated successfully!"]);
                $msg = "Status updated successfully!";
                $type = 'success';
                Session::flash($type, $msg);
                return redirect('/member');
            } else {
                $msg = "Unknown error happened please try again!";
                    $type = 'error';
                    Session::flash($type, $msg);
                    return redirect('/member');
            }
        } catch (Exception $ex) {
            $msg = "Unable to process your request, Please try again!";
            $type = 'error';
            Session::flash($type, $msg);
            return back();
        }
    }

    public function equbCheckForDrawUpdate($id, Request $request)
    {
        try {
            $userData = Auth::user();
            // if ($userData && ($userData['role'] == "admin") || ($userData['role'] == "equb_collector")) {
                $status = $this->equbRepository->getStatusById($id)->check_for_draw;
                if ($status == false) {
                    $status = true;
                } else {
                    $status = false;
                }
                // dd($status);
                $updated = [
                    'check_for_draw' => $status,
                ];
                $updated = $this->equbRepository->update($id, $updated);
                if ($updated) {
                    if ($status == "Deactive") {
                        $status = "Deactivated";
                    } else {
                        $status = "Activated";
                    }
                    $activityLog = [
                        'type' => 'equbs',
                        'type_id' => $id,
                        'action' => $status,
                        'user_id' => $userData->id,
                        'username' => $userData->name,
                        'role' => $userData->role,
                    ];
                    $this->activityLogRepository->createActivityLog($activityLog);
                    $msg = "Equb draw check updated successfully!";
                    $type = 'success';
                    Session::flash($type, $msg);
                    return redirect('/member');
                } else {
                    $msg = "Unknown error occurred, Please try again!";
                    $type = 'error';
                    Session::flash($type, $msg);
                    return redirect('/member');
                }
            // } else {
            //     return view('auth/login');
            // }
        } catch (Exception $ex) {
            $msg = "Unable to process your request, Please try again!";
            $type = 'error';
            Session::flash($type, $msg);
            return back();
        }
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Equb  $equb
     * @return \Illuminate\Http\Response
     */
    public function memberByEqubType($id)
    {
        // Fetch members based on the Equb type
        $equbTypes = $this->equbRepository->getMemberByEqubType($id);
    
        // Check if the result is empty
        if ($equbTypes->isEmpty()) {
            return response()->json(['error' => 'No members found'], 404);
        }
    
        // Convert the collection to array and format the response
        $formattedEqubTypes = $equbTypes->map(function ($equbType) {
            return [
                'id' => $equbType->id,
                'member_id' => $equbType->member_id,
                'equb_type_id' => $equbType->equb_type_id,
                'amount' => $equbType->amount,
                'total_amount' => $equbType->total_amount,
                'start_date' => $equbType->start_date,
                'end_date' => $equbType->end_date,
                'lottery_date' => $equbType->lottery_date,
                'status' => $equbType->status,
                'full_name' => $equbType->member->full_name ?? null, // Safely access member's first name
                'phone' => $equbType->member->phone ?? null, // Safely access member's first name
                'created_at' => $equbType->created_at,
                'updated_at' => $equbType->updated_at,
                'timeline' => $equbType->timeline,
                'check_for_draw' => $equbType->check_for_draw,
            ];
        });
    
        // Return the formatted response
        return response()->json([
            'equbTypes' => $formattedEqubTypes // This will contain the array of equb types with names
        ]);
    }
    public function update($id, Request $request)
    {
        // dd($request->end_date);
        try {
            $userData = Auth::user();
            // if ($userData && ($userData['role'] == "admin") || ($userData['role'] == "equb_collector")) {
                $oldEqub = Equb::where('id', $id)->first();
                $oldEqubTypeData = EqubType::where('id', $oldEqub->equb_type_id)->first();
                $member = Member::where('id', $oldEqub->member_id)->first();
                $equbType = $request->input('equb_type_id');
                $newEqubTypeData = EqubType::where('id', $equbType)->first();
                $amount = $request->input('amount');
                $totalAmount = $request->input('total_amount');
                $startDate = $request->input('start_date');
                $timeline = $request->input('timeline');
                $endDate = $request->input('end_date');
                $lotteryDate = $request->input('lottery_date');

                $endDateCheck = $this->isDateInYMDFormat($endDate);
                $formattedEndDate = $endDate;
                if (!$endDateCheck) {
                    $carbonDate = Carbon::createFromFormat('m/d/Y', $endDate);
                    $formattedEndDate = $carbonDate->format('Y-m-d');
                }

                $updated = [
                    'equb_type_id' => $equbType,
                    'amount' => $amount,
                    'total_amount' => $totalAmount,
                    'start_date' => $startDate,
                    'end_date' => $formattedEndDate,
                    'timeline' => $timeline,
                    'lottery_date' => $lotteryDate,
                ];
                $equbTakerData = [
                    'amount' => $totalAmount,
                    'remaining_amount' => $totalAmount,
                ];
                $equb = $this->equbRepository->getById($id);
                $equbTaker = $this->equbTakerRepository->getEkubTaker($id, $equb->member_id);
                $updated = $this->equbRepository->update($id, $updated);
                $updateEkubTaker = $this->equbTakerRepository->update($equbTaker[0]->id, $equbTakerData);
                if ($updated) {
                    $activityLog = [
                        'type' => 'equbs',
                        'type_id' => $id,
                        'action' => 'updated',
                        'user_id' => $userData->id,
                        'username' => $userData->name,
                        'role' => $userData->role,
                    ];
                    $this->activityLogRepository->createActivityLog($activityLog);
                    $shortcode = config('key.SHORT_CODE');
                    $lotteryDateMessage = $lotteryDate ? $lotteryDate : $newEqubTypeData->lottery_date;
                    $message = "Your $oldEqubTypeData->name ekub has been updated to equb type: $newEqubTypeData->name, end date: $formattedEndDate, lottery date: $lotteryDateMessage. For further informations please call $shortcode";
                    $this->sendSms($member->phone, $message);
                    $msg = "equb updated successfully!";
                    $type = 'success';
                    Session::flash($type, $msg);
                    return redirect('member/');
                } else {
                    $msg = "Unknown error occurred, Please try again!";
                    $type = 'error';
                    Session::flash($type, $msg);
                    return redirect('/member');
                }
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
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Equb  $equb
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
                $userData = Auth::user();
                $equb = $this->paymentRepository->getEqubForDelete($id);
                if ($equb) {
                    $payment = $this->paymentRepository->getByMemberId($equb->member_id, $equb->id);
                    if ($payment) {
                        $msg = "In order to delete this equb, you must first remove all associated payments.";
                        $type = 'error';
                        Session::flash($type, $msg);
                        return redirect('member/');
                    }
                }
                $equb = $this->equbRepository->getById($id);
                // dd($equb);
                if ($equb != null) {
                    $equbTypes = EqubType::where('id', $equb->equb_type_id)->first();
                    $ekubTakerId = $this->equbTakerRepository->getEkubTaker($id, $equb->member_id);
                    // dd($ekubTakerId[0]->id);
                    if ($ekubTakerId && count($ekubTakerId) > 0) {
                        $deletedEkubTaker = $this->equbTakerRepository->delete($ekubTakerId[0]->id);
                    }
                    $deleted = $this->equbRepository->delete($id);
                    if ($deleted) {
                        if ($equbTypes->type == 'Automatic') {
                            $equbTypes->remaining_quota += 1;
                            if ($equbTypes->remaining_quota > 0) {
                                $equbTypes->status = "Active";
                            }
                            $equbTypes->save();
                        }
                        $activityLog = [
                            'type' => 'equbs',
                            'type_id' => $id,
                            'action' => 'deleted',
                            'user_id' => $userData->id,
                            'username' => $userData->name,
                            'role' => $userData->role,
                        ];
                        $this->activityLogRepository->createActivityLog($activityLog);
                        $msg = "Equb has been deleted successfully!";
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
        } catch (Exception $ex) {
            // dd($ex);
            $msg = "Unable to process your request, Please try again!";
            $type = 'error';
            Session::flash($type, $msg);
            return $msg;
        }
    }
}
