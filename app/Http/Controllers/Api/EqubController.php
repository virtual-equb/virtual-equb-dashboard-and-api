<?php

namespace App\Http\Controllers\api;

use Exception;
use Carbon\Carbon;
use App\Models\Equb;
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
use App\Http\Resources\Api\EqubResource;
use App\Repositories\Equb\EqubRepository;
use App\Repositories\Equb\IEqubRepository;
use App\Repositories\Member\IMemberRepository;
use App\Repositories\Payment\IPaymentRepository;
use App\Repositories\EqubType\IEqubTypeRepository;
use App\Repositories\EqubTaker\IEqubTakerRepository;
use App\Repositories\ActivityLog\IActivityLogRepository;

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

        // Guard Permissions
        $this->middleware('api_permission_check:update equb', ['only' => ['update', 'edit', 'updateStatus']]);
        $this->middleware('api_permission_check:delete equb', ['only' => ['destroy']]);
        $this->middleware('api_permission_check:view equb', ['only' => ['index', 'show', 'create']]);
        $this->middleware('api_permission_check:create equb', ['only' => ['store', 'create']]);
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

            $data['equbs'] = $this->equbRepository->getAll();

            return response([
                'code' => 200,
                'data' => EqubResource::collection($data['equbs'])
            ]);


        } catch (Exception $ex) {
            return response()->json([
                'code' => 500,
                'message' => 'Unable to process your request, Please try again!',
                "error" => $ex->getMessage()
            ]);
        }
    }

    public function sendStartNotifications()
    {
        try {
            $now = Carbon::now();
            $next24Hours = $now->copy()->addHours(24);

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

                    $this->sendSms($member->phone, $message);
                    $count++;

                    // Update the Equb notified field
                    $equb->update(['notified' => 'Yes']);
                }

                return response()->json([
                    'message' => 'Notification sent for due Equbs',
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
                "error" => $ex->getMessage()
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

            return response([
                'code' => 200,
                'data' => new EqubRepository($data['daily_paid_amoun'])
            ]);
        } catch (Exception $ex) {
            return response()->json([
                'code' => 500,
                'message' => 'Unable to process your request, Please try again!',
                "error" => $ex->getMessage()
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
                "error" => $ex->getMessage()
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
                "error" => $ex->getMessage()
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
                "error" => $ex->getMessage()
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
                "error" => $ex->getMessage()
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
                "error" => $ex->getMessage()
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
            $adminRoles = ['admin', 'general_manager', 'operation_manager', 'it'];
            $memberRoles = ['equb_collector', 'member'];
            if ($userData && $userData->hasAnyRole($adminRoles)){

                $equbTakerData['equb'] = $this->equbRepository->getByIdNestedForLottery($id);
                $equbTakerData['total'] = $this->paymentRepository->getTotal($id);

                return response()->json($equbTakerData);

            } elseif ($userData && $userData->hasAnyRole($memberRoles)) {

                $equbTakerData['equb'] = $this->equbRepository->getByIdNested($id);
                $equbTakerData['total'] = $this->paymentRepository->getTotal($id);

                return response()->json($equbTakerData);

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
            $data['title'] = $this->title;
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

            // Validate required fields
            $this->validate($request, [
                'equb_type_id' => 'required',
                'amount' => 'required',
                'total_amount' => 'required',
                'start_date' => 'required|date_format:Y-m-d',
                'timeline' => 'required',
            ]);

            // Collecting request inputs
            $member = $request->input('member_id');
            $equbType = $request->input('equb_type_id');
            $amount = $request->input('amount');
            $totalAmount = $request->input('total_amount');
            $startDate = $request->input('start_date');
            $endDate = $request->input('end_date');
            $timeline = $request->input('timeline');
            $lotteryDate = $request->input('lottery_date');

            // Parse endDate if it doesn't match expected format
            $formattedEndDate = $endDate;
            if (!$this->isDateInYMDFormat($endDate)) {
                try {
                    $carbonDate = Carbon::createFromFormat('m/d/Y', $endDate);
                    $formattedEndDate = $carbonDate->format('Y-m-d');
                } catch (\Exception $e) {
                    Log::error("Date parsing error for endDate: " . $e->getMessage());
                    return response()->json([
                        'code' => 400,
                        'message' => 'Invalid date format for end date.'
                    ]);
                }
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

            // Prepare data for new Equb
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

            // Create the Equb
            $create = $this->equbRepository->create($equbData);
            if ($create) {
                // Update remaining quota for Automatic type Equb
                $equbTypes = EqubType::find($equbType);
                if ($equbTypes && $equbTypes->type == 'Automatic') {
                    $equbTypes->decrement('remaining_quota', 1);
                }

                // Prepare data for Equb Taker
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

                // Create activity log
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
                    'message' => 'Equb has been registered successfully!',
                    'data' => $create
                ]);
            } else {
                return response()->json([
                    'code' => 400,
                    'message' => 'Unknown error occurred! Please try again.'
                ]);
            }
        } catch (\Exception $ex) {
            Log::error("Store method error: " . $ex->getMessage());
            return response()->json([
                'code' => 500,
                'message' => 'Unable to process your request, please try again!',
                'error' => $ex->getMessage()
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
                        'data' => new EqubResource($updated)
                    ]);
                } else {
                    return response()->json([
                        'code' => 400,
                        'message' => 'Unkown Error occurred! Please try again!'
                    ]);
                }
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
