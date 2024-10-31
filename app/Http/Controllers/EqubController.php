<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Equb;
use App\Models\RejectedDate;
use App\Http\Controllers\Controller;
use App\Models\EqubType;
use App\Models\Member;
use Exception;
use App\Repositories\Member\IMemberRepository;
use App\Repositories\Payment\IPaymentRepository;
use App\Repositories\EqubType\IEqubTypeRepository;
use App\Repositories\Equb\IEqubRepository;
use App\Repositories\EqubTaker\IEqubTakerRepository;
use App\Repositories\ActivityLog\IActivityLogRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

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
        $this->middleware('permission:update equb', ['only' => ['update', 'edit', 'updateStatus']]);
        $this->middleware('permission:delete equb', ['only' => ['destroy']]);
        $this->middleware('permission:view equb', ['only' => ['index', 'show', 'getReservedLotteryDate']]);
        $this->middleware('permission:create equb', ['only' => ['store', 'create', 'addUnpaid']]);
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
            if ($userData->hasAnyRole(['admin', 'general_manager', 'operation_manager', 'it', 'finance'])) {
                $userData = Auth::user();
                $equbs = $this->equbRepository->getAll();
                return view('admin/equb.equbList', compact('equbs'));
            } elseif ($userData->hasRole('equb_collector')) {
                $userData = Auth::user();
                $equbs = $this->equbRepository->getAll();
                return view('equbCollecter/equb.equbList', compact('equbs'));
            } elseif ($userData->hasRole('member')) {
                $userData = Auth::user();
                $equbs = $this->equbRepository->getAll();
                return view('member/equb.equbList', compact('equbs'));
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
            // foreach ($equbId as $equb_id) {
            //     $equb = Equb::where('id', $equb_id)->with('member')->first();
            //     array_push($equbDetail, $equb);
            // }
            // return view('admin/equb/lotteryDetail', compact('equbDetail', 'ExpectedTotal'));

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
    public function show($id)
    {
        try {
            $userData = Auth::user();
            if ($userData->hasAnyRole(['admin', 'general_manager', 'operation_manager', 'it', 'finance'])) {
                $equbTakerData['equb'] = $this->equbRepository->getByIdNestedForLottery($id);
                $equbTakerData['total'] = $this->paymentRepository->getTotal($id);
                return view('admin/equb.equbDetails', $equbTakerData);
            } elseif ($userData->hasRole('equb_collector')) {
                $equbTakerData['equb'] = $this->equbRepository->getByIdNested($id);
                $equbTakerData['total'] = $this->paymentRepository->getTotal($id);
                return view('equbCollecter/equb.equbDetails', $equbTakerData);
            } elseif ($userData->hasRole('member')) {
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
            if ($userData->hasAnyRole(['admin', 'general_manager', 'operation_manager', 'it', 'finance'])) {
                $data['title'] = $this->title;
                return view('admin/equb/addEqub', $data);
            } elseif ($userData->hasRole('equb_collector')) {
                $data['title'] = $this->title;
                return view('equbCollecter/equb/addEqub', $data);
            } elseif ($userData->hasRole('member')) {
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
    // /**
    //  * Draw random winner.
    //  *
    //  * @return \Illuminate\Http\Response
    //  */
    // public function draw()
    // {
    //     try {
    //         $now = Carbon::now();
    //         $members = [];
    //         $equbTypes = DB::table('equb_types')
    //             ->whereDate('lottery_date', '=', $now)
    //             ->where("deleted_at", "=", null)
    //             ->get();
    //         foreach ($equbTypes as $equbType) {
    //             $equbs = DB::table('equbs')->where('equb_type_id', $equbType->id)->pluck('member_id')->toArray();
    //             foreach ($equbs as $equb) {
    //                 if (!in_array($equb, $members)) {
    //                     array_push($members, $equb);
    //                 }
    //             }
    //         }
    //         $winner = $this->drawRandomId($members);
    //         $user = Member::where('id', $winner)->first();
    //         foreach ($equbTypes as $equbType) {
    //             $daysToBeAdded = 0;
    //             if ($equbType->rote === "Daily") {
    //                 $daysToBeAdded = 1;
    //             } elseif ($equbType->rote === "Weekly") {
    //                 $daysToBeAdded = 7;
    //             } elseif ($equbType->rote === "Biweekly") {
    //                 $daysToBeAdded = 14;
    //             } elseif ($equbType->rote === "Monthly") {
    //                 $daysToBeAdded = 30;
    //             }
    //             // dd($daysToBeAdded);
    //             $updatedLotterDate = $now->copy()->addDays($daysToBeAdded)->format('Y-m-d');
    //             EqubType::where('id', $equbType->id)->update(['lottery_date' => $updatedLotterDate]);
    //         }
    //         // dd([
    //         //     "MemberId"=> $user->id,
    //         //     "UserName"=> $user->full_name
    //         // ]);
    //         return [
    //             "MemberId" => $user->id,
    //             "UserName" => $user->full_name
    //         ];
    //     } catch (Exception $ex) {
    //         $msg = "Unable to process your request, Please try again!";
    //         $type = 'error';
    //         Session::flash($type, $msg);
    //         return back();
    //     }
    // }
    // function drawRandomId(array $ids)
    // {
    //     // Shuffle the array of IDs
    //     shuffle($ids);

    //     // Store the shuffled IDs in cache for ten seconds
    //     Cache::put('shuffled_ids', $ids, now()->addSeconds(10));

    //     // Wait for a few seconds to allow shuffling
    //     sleep(3); // You can adjust the duration as needed

    //     // Get the shuffled IDs from cache
    //     $shuffledIds = Cache::get('shuffled_ids');

    //     // Pick a random index and return the corresponding ID
    //     $randomIndex = array_rand($shuffledIds);
    //     $randomId = $shuffledIds[$randomIndex];

    //     return $randomId;
    // }
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
            // if ($userData && ($userData['role'] == "admin") || ($userData['role'] == "equb_collector")) {
                $this->validate($request, [
                    'equb_type_id' => 'required',
                    'amount' => 'required',
                    'total_amount' => 'required',
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
                    if ($equbTypes->type == 'Automatic') {
                        $equbTypes->remaining_quota -= 1;
                        if ($equbTypes->remaining_quota == 0) {
                            $equbTypes->status = "Deactive";
                        }
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
            $msg = "Unknown Error Occurred, Please try again!";
            $type = 'error';
            Session::flash($type, $msg);
            return back();
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
            if ($userData->hasAnyRole(['admin', 'general_manager', 'operation_manager', 'it', 'finance'])) {
                $data['equb'] = $this->equbRepository->getById($equb);
                return view('admin/member/updateMember', $data);
            } elseif ($userData->hasRole('equb_collector')) {
                $data['equb'] = $this->equbRepository->getById($equb);
                return view('equbCollecter/member/updateMember', $data);
            } elseif ($userData->hasRole('member')) {
                $data['equb'] = $this->equbRepository->getById($equb);
                return view('member/member/updateMember', $data);
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
    public function updateStatus($id, Request $request)
    {
        try {
            $userData = Auth::user();
                $status = $this->equbRepository->getStatusById($id)->status;
                if ($status == "Deactive") {
                    $status = "Active";
                } else {
                    $status = "Deactive";
                }
                $updated = [
                    'status' => $status,
                ];
                $updated = $this->equbRepository->update($id, $updated);
                if ($updated) {
                    if ($status == "Deactive") {
                        $status = "Deactivated";
                    } else {
                        $status = "Activated";
                    }
                    $activityLog = [
                        'type' => 'members',
                        'type_id' => $id,
                        'action' => $status,
                        'user_id' => $userData->id,
                        'username' => $userData->name,
                        'role' => $userData->role,
                    ];
                    $this->activityLogRepository->createActivityLog($activityLog);
                    $msg = "Status updated successfully!";
                    $type = 'success';
                    Session::flash($type, $msg);
                    return redirect('/member');
                } else {
                    $msg = "Unknown error occurred, Please try again!";
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


                // // Parse the dates using Carbon
                // $carbonDate1 = Carbon::parse($oldEqub->end_date);
                // $carbonDate2 = Carbon::parse($endDate);
                // // Get the formats of the two dates
                // $format1 = $carbonDate1->format('Y-m-d');
                // $format2 = $carbonDate2->format('Y-m-d');
                // $formattedEndDate = $endDate;
                // // Compare the formats
                // if ($format1 != $format2) {
                //     $carbonDate = Carbon::createFromFormat('m/d/Y', $endDate);
                //     $formattedEndDate = $carbonDate->format('Y-m-d');
                // }

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
            // if ($userData && ($userData['role'] == "admin") || ($userData['role'] == "equb_collector")) {
                $equb = $this->paymentRepository->getEqubForDelete($id);
                // dd($equb);
                if ($equb) {
                    $payment = $this->paymentRepository->getByMemberId($equb->member_id, $equb->id);
                    if ($payment) {
                        $msg = "In order to delete this equb, you must first remove all associated payments.";
                        $type = 'error';
                        Session::flash($type, $msg);
                        return redirect('member/');
                    }
                    // $deleted = $this->paymentRepository->deleteAll($equb->member_id, $equb->id);
                    // if ($deleted) {
                    //     $activityLog = [
                    //         'type' => 'payments',
                    //         'type_id' => $equb->id,
                    //         'action' => 'deleted all payment',
                    //         'user_id' => $userData->id,
                    //         'username' => $userData->name,
                    //         'role' => $userData->role,
                    //     ];
                    //     $this->activityLogRepository->createActivityLog($activityLog);
                    // } else {
                    //     $msg = "Unknown Error Occurred, Please try again!";
                    //     $type = 'error';
                    //     Session::flash($type, $msg);
                    //     redirect('/member');
                    // }
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
            // } else {
            //     return view('auth/login');
            // }
        } catch (Exception $ex) {
            // dd($ex);
            $msg = "Unable to process your request, Please try again!";
            $type = 'error';
            Session::flash($type, $msg);
            return $msg;
        }
    }
}
