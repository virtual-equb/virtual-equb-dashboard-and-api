<?php

namespace App\Http\Controllers\api;

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

/**
 * @group Equbs
 */
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
        $this->middleware('auth:api');
        $this->activityLogRepository = $activityLogRepository;
        $this->memberRepository = $memberRepository;
        $this->paymentRepository = $paymentRepository;
        $this->equbTypeRepository = $equbTypeRepository;
        $this->equbRepository = $equbRepository;
        $this->equbTakerRepository = $equbTakerRepository;
        $this->title = "Virtual Equb - Equb";
    }
    /**
     * Get All Equbs
     *
     * This api returns all Equbs.
     *
     * @return JsonResponse
     */
    public function index()
    {
        try {
            $userData = Auth::user();
            // if ($userData && ($userData['role'] == "admin" ||
            //     $userData['role'] == "equb_collector" || $userData['role'] == "member")) {
                $userData = Auth::user();
                $data['equbs'] = $this->equbRepository->getAll();
                return response()->json($data);
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
            foreach ($equbId as $equb_id) {
                $equb = Equb::where('id', $equb_id)->with('member')->first();
                array_push($equbDetail, $equb);
                $data['equbDetail'] = $equbDetail;
                $data['equb'] = $equb;
            }
            return response()->json($data);
        } catch (Exception $ex) {
            return response()->json([
                'code' => 500,
                'message' => 'Unable to process your request, Please try again!',
                "error" => $ex
            ]);
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
            return response()->json([
                'code' => 500,
                'message' => 'Unable to process your request, Please try again!',
                "error" => $ex
            ]);
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
            return response()->json([
                'code' => 500,
                'message' => 'Unable to process your request, Please try again!',
                "error" => $ex
            ]);
        }
    }
    public function dateInterval($id)
    {
        try {
            $this->equbTypeRepository->getDeactive($id);
        } catch (Exception $ex) {
            return response()->json([
                'code' => 500,
                'message' => 'Unable to process your request, Please try again!',
                "error" => $ex
            ]);
        }
    }
    /**
     * Get Daily Paid Amount
     *
     * This api gets daily paid amount for each equb.
     *
     * @param equb_id int Example: 1
     *
     * @return JsonResponse
     */
    public function getDailyPaidAmount($equb_id)
    {
        try {
            $data['daily_paid_amount'] = $this->equbRepository->getDailyPaid($equb_id);
            return response()->json($data);
        } catch (Exception $ex) {
            return response()->json([
                'code' => 500,
                'message' => 'Unable to process your request, Please try again!',
                "error" => $ex
            ]);
        }
    }
    /**
     * Check if user has an equb
     *
     * @bodyParam equb_type_id int required The id of the equb type. Example: 9
     *
     * @bodyParam member_id int required The id of the member. Example: 9
     *
     * @return JsonResponse
     */
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
            return response()->json([
                'code' => 500,
                'message' => 'Unable to process your request, Please try again!',
                "error" => $ex
            ]);
        }
    }
    /**
     * Check if user has an equb
     *
     * @bodyParam equb_type_id int required The id of the equb type. Example: 9
     *
     * @bodyParam member_id int required The id of the member. Example: 9
     *
     * @bodyParam equb_id int required The id of the equb. Example: 9
     *
     * @return JsonResponse
     */
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
            return response()->json([
                'code' => 500,
                'message' => 'Unable to process your request, Please try again!',
                "error" => $ex
            ]);
        }
    }
    /**
     * Check if equb date has passed
     *
     * @bodyParam equb_type_id int required The id of the equb type. Example: 9
     *
     * @bodyParam end_date Date required The date to be checked. Example: 01/01/1996
     *
     * @return JsonResponse
     */
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
            return response()->json([
                'code' => 500,
                'message' => 'Unable to process your request, Please try again!',
                "error" => $ex
            ]);
        }
    }
    /**
     * Check if equb has started
     *
     * @bodyParam equb_type_id int required The id of the equb type. Example: 9
     *
     * @bodyParam start_date Date required The date to be checked. Example: 01/01/1996
     *
     * @return JsonResponse
     */
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
            return response()->json([
                'code' => 500,
                'message' => 'Unable to process your request, Please try again!',
                "error" => $ex
            ]);
        }
    }
    /**
     * Check if lottery date has passed
     *
     * @bodyParam lottery_date Date required The date to be checked. Example: 01/01/1996
     *
     * @return JsonResponse
     */
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
            return response()->json([
                'code' => 500,
                'message' => 'Unable to process your request, Please try again!',
                "error" => $ex
            ]);
        }
    }
    /**
     * Show equbs
     *
     * @param id int required The id of the equb. Example: 1
     *
     * @return JsonResponse
     */
    public function show($id)
    {
        try {
            $userData = Auth::user();
            // if ($userData && ($userData['role'] == "admin" || $userData['role'] == "general_manager" || $userData['role'] == "operation_manager" || $userData['role'] == "it")){
                $equbTakerData['equb'] = $this->equbRepository->getByIdNestedForLottery($id);
                $equbTakerData['total'] = $this->paymentRepository->getTotal($id);
                return response()->json($equbTakerData);
            // } elseif ($userData && ($userData['role'] == "equb_collector" || $userData['role'] == "member")) {
                $equbTakerData['equb'] = $this->equbRepository->getByIdNested($id);
                $equbTakerData['total'] = $this->paymentRepository->getTotal($id);
                return response()->json($equbTakerData);
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

    public function getPaidEqubs($memberId)
    {
        $equbs = $this->equbRepository->getMember($memberId);
        $paidEqubs = [];
        foreach ($equbs as $equb) {
            $totalPpayment = $this->paymentRepository->getTotalPaid($equb->id);
            $totalEqubAmount = $this->equbRepository->getTotalEqubAmount($equb->id);
            $remainingPayment =  $totalEqubAmount - $totalPpayment;
            if ($remainingPayment == 0) {
                array_push($paidEqubs, $equb);
            }
        }
        return response()->json($paidEqubs);
    }
    public function create()
    {
        try {
            $userData = Auth::user();
            // if ($userData && ($userData['role'] == "admin" || $userData['role'] == "equb_collector")) {
                $data['title'] = $this->title;
                return response()->json($data);
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
    //         return response()->json([
    //             'code' => 200,
    //             'message' => 'Winner has been selected',
    //             'data' => [
    //                 "MemberId" => $user->id,
    //                 "UserName" => $user->full_name
    //             ]
    //         ]);
    //         // return [
    //         //     "MemberId" => $user->id,
    //         //     "UserName" => $user->full_name
    //         // ];
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
     * Create equb
     *
     * @bodyParam equb_type_id int required The id of the equb type. Example: 2
     * @bodyParam amount int required The amount to be paid frequently. Example: 1000
     * @bodyParam total_amount int required The amount to be paid in total. Example: 10000
     * @bodyParam start_date Date required The start date of the equb. Example: 01/01/1996
     * @bodyParam end_date Date required The end date of the equb. Example: 01/01/1996
     * @bodyParam lottery_date Date required The lottery date of the equb. Example: 01/01/1996
     *
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        try {
            $userData = Auth::user();
            // if ($userData && ($userData['role'] == "admin") || ($userData['role'] == "equb_collector") || ($userData['role'] == "member")) {
                $this->validate($request, [
                    'equb_type_id' => 'required',
                    'amount' => 'required',
                    'total_amount' => 'required',
                    'start_date' => 'required',
                    'timeline' => 'required',
                    // 'end_date' => 'required',
                    // 'lottery_date' => 'required',
                ]);
                $member = $request->input('member_id');
                $equbType = $request->input('equb_type_id');
                $amount = $request->input('amount');
                $totalAmount = $request->input('total_amount');
                $startDate = $request->input('start_date');
                $endDate = $request->input('end_date');
                $timeline = $request->input('timeline');
                $lotteryDate = $request->input('lottery_date');
                $endDateCheck = $this->isDateInYMDFormat($endDate);
                $formattedEndDate = $endDate;
                if (!$endDateCheck) {
                    $carbonDate = Carbon::createFromFormat('m/d/Y', $endDate);
                    $formattedEndDate = $carbonDate->format('Y-m-d');
                }
                // $carbonDate = Carbon::createFromFormat('m/d/Y', $endDate);

                // Format the date to "Y-m-d" format
                // $formattedEndDate = $carbonDate->format('Y-m-d');
                // dd($formattedEndDate);
                if (!empty($equbType)) {
                    $equbs_count = Equb::where('equb_type_id', $equbType)->where('member_id', '=', $member)->count();
                    if ($equbs_count > 0) {
                        return response()->json([
                            'code' => 400,
                            'message' => 'Equb already exist!'
                        ]);
                    }
                }
                $equbData = [
                    'member_id' => $member,
                    'equb_type_id' => $equbType,
                    'amount' => $amount,
                    'total_amount' => $totalAmount,
                    'start_date' => $startDate,
                    'timeline' => $timeline,
                    'end_date' => $formattedEndDate,
                    'lottery_date' => $lotteryDate,
                ];
                $create = $this->equbRepository->create($equbData);
                if ($create) {
                    $equbTypes = EqubType::where('id', $equbType)->first();
                    if ($equbTypes->type == 'Automatic') {
                        $equbTypes->remaining_quota -= 1;
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
                    return response()->json([
                        'code' => 200,
                        'message' => 'Equb has been registerd successfully!',
                        'data' => $create
                    ]);
                } else {
                    return response()->json([
                        'code' => 400,
                        'message' => 'Unkown Error Occurred! Please try again!'
                    ]);
                }
            // } else {
            //     return response()->json([
            //         'code' => 403,
            //         'message' => 'You can\'t perform this action!'
            //     ]);
            // }
        } catch (Exception $ex) {
            // dd($ex);
            return response()->json([
                'code' => 500,
                'message' => 'Unable to process your request, Please try again!',
                "error" => $ex
            ]);
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
    /**
     * Update status
     *
     * @param id int required The id of the equb. Example: 2
     *
     * @return JsonResponse
     */
    public function updateStatus($id, Request $request)
    {
        try {
            $userData = Auth::user();
            // if ($userData && ($userData['role'] == "admin") || ($userData['role'] == "equb_collector")) {
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
                    return response()->json([
                        'code' => 200,
                        'message' => 'Equb Status updated successfully!',
                        'data' => $updated
                    ]);
                } else {
                    return response()->json([
                        'code' => 400,
                        'message' => 'Unkown Error Occurred! Please try again!'
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
     * Update equb
     *
     * @bodyParam equb_type_id int required The id of the equb type. Example: 2
     * @bodyParam amount int required The amount to be paid frequently. Example: 1000
     * @bodyParam total_amount int required The amount to be paid in total. Example: 10000
     * @bodyParam start_date Date required The start date of the equb. Example: 01/01/1996
     * @bodyParam end_date Date required The end date of the equb. Example: 01/01/1996
     * @bodyParam lottery_date Date required The lottery date of the equb. Example: 01/01/1996
     *
     * @return JsonResponse
     */
    public function update($id, Request $request)
    {
        try {
            $userData = Auth::user();
            // if ($userData && ($userData['role'] == "admin") || ($userData['role'] == "equb_collector")) {
                $oldEqub = Equb::where('id', $id)->first();
                $member = Member::where('id', $oldEqub->member_id)->first();
                $oldEqubTypeData = EqubType::where('id', $oldEqub->equb_type_id)->first();
                $equbType = $request->input('equb_type_id');
                $newEqubTypeData = EqubType::where('id', $equbType)->first();
                $amount = $request->input('amount');
                $totalAmount = $request->input('total_amount');
                $startDate = $request->input('start_date');
                $timeline = $request->input('timeline');
                $endDate = $request->input('end_date');
                $lotteryDate = $request->input('lottery_date');

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
                    'timeline' => $timeline,
                    'end_date' => $endDate,
                    'lottery_date' => $lotteryDate,
                ];
                $updated = $this->equbRepository->update($id, $updated);
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
                    $message = "Your $oldEqubTypeData->name ekub has been updated to equb type: $newEqubTypeData->name, end date: $endDate, lottery date: $lotteryDateMessage. For further informations please call $shortcode";
                    $this->sendSms($member->phone, $message);
                    return response()->json([
                        'code' => 200,
                        'message' => 'Equb has been updated successfully!',
                        'data' => $updated
                    ]);
                } else {
                    return response()->json([
                        'code' => 400,
                        'message' => 'Unkown Error occurred! Please try again!'
                    ]);
                }
            // } else {
            //     return response()->json([
            //         'code' => 403,
            //         'message' => 'You can\'t perform this action!'
            //     ]);
            // }
        } catch (Exception $ex) {
            // dd($ex);
            return response()->json([
                'code' => 500,
                'message' => 'Unable to process your request, Please try again!',
                "error" => $ex
            ]);
        }
    }
    /**
     * Delete Equb
     *
     * @param id int required The id of the equb. Example: 2
     *
     * @return JsonResponse
     */
    public function destroy($id)
    {
        try {
            $userData = Auth::user();
            // if ($userData && ($userData['role'] == "admin") || ($userData['role'] == "equb_collector") || ($userData['role'] == "member")) {
                // dd("hello");
                $equbP = $this->paymentRepository->getEqubForDelete($id);
                // dd($equb);
                if ($equbP) {
                    $deleted = $this->paymentRepository->deleteAll($equbP->member_id, $equbP->id);
                    if ($deleted) {
                        $activityLog = [
                            'type' => 'payments',
                            'type_id' => $equbP->id,
                            'action' => 'deleted all payment',
                            'user_id' => $userData->id,
                            'username' => $userData->name,
                            'role' => $userData->role,
                        ];
                        $this->activityLogRepository->createActivityLog($activityLog);
                    } else {
                        return response()->json([
                            'code' => 400,
                            'message' => 'Unknown Error Occurred, Please try again!'
                        ]);
                    }
                }
                $equb = $this->equbRepository->getById($id);
                if ($equb != null) {
                    $equbTypes = EqubType::where('id', $equb->equb_type_id)->first();
                    $ekubTakerId = $this->equbTakerRepository->getEkubTaker($id, $equb->member_id);
                    if (count($ekubTakerId) > 0) {
                        $deletedEkubTaker = $this->equbTakerRepository->delete($ekubTakerId[0]->id);
                    }
                    $deleted = $this->equbRepository->delete($id);
                    if ($deleted) {
                        if ($equbTypes->type == 'Automatic') {
                            $equbTypes->remaining_quota += 1;
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
                        return response()->json([
                            'code' => 200,
                            'message' => 'Equb has been deleted successfully!'
                        ]);
                        // dd($deleted);
                    } else {
                        return response()->json([
                            'code' => 400,
                            'message' => 'Unkown Error Occurred! Please try again!'
                        ]);
                    }
                } else {
                    return response()->json([
                        'code' => 400,
                        'message' => 'Equb not found'
                    ]);
                }
            // } else {
            //     return response()->json([
            //         'code' => 403,
            //         'message' => 'You can\'t perform this action!'
            //     ]);
            // }
        } catch (Exception $ex) {
            // dd($ex);
            return response()->json([
                'code' => 500,
                'message' => 'Unable to process your request, Please try again!',
                "error" => $ex
            ]);
        }
    }
}
