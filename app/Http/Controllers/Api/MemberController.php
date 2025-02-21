<?php

namespace App\Http\Controllers\api;

use Log;
use DateTime;
use Exception;
use App\Models\Equb;
use App\Models\User;
use App\Models\Member;
use App\Models\Payment;
use App\Models\EqubType;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\Api\MemberResource;
use App\Repositories\Equb\IEqubRepository;
use App\Repositories\User\IUserRepository;
use App\Repositories\Member\IMemberRepository;
use App\Repositories\Payment\IPaymentRepository;
use App\Repositories\EqubType\IEqubTypeRepository;
use App\Repositories\ActivityLog\IActivityLogRepository;

/**
 * @group Members
 */
class MemberController extends Controller
{
    private $activityLogRepository;
    private $memberRepository;
    private $paymentRepository;
    private $equbTypeRepository;
    private $equbRepository;
    private $userRepository;
    private $title;
    public function __construct(
        IMemberRepository $memberRepository,
        IPaymentRepository $paymentRepository,
        IEqubTypeRepository $equbTypeRepository,
        IUserRepository $userRepository,
        IEqubRepository $equbRepository,
        IActivityLogRepository $activityLogRepository
    ) {
        $this->middleware('auth:api')->except(['register', 'checkMemberPhoneExist', 'getMembersByEqubType', 'getProfilePicture']);
        $this->activityLogRepository = $activityLogRepository;
        $this->memberRepository = $memberRepository;
        $this->paymentRepository = $paymentRepository;
        $this->equbTypeRepository = $equbTypeRepository;
        $this->equbRepository = $equbRepository;
        $this->userRepository = $userRepository;
        $this->title = "Virtual Equb - Member";

         // Guard Permissions
        //  $this->middleware('api_permission_check:update member', ['only' => ['update', 'edit', 'updateStatus', 'rate', 'updateProfile']]);
        //  $this->middleware('api_permission_check:delete member', ['only' => ['destroy']]);
        //  $this->middleware('api_permission_check:view member', ['only' => ['index', 'searchMember', 'create', 'show', 'getPaidEqubs']]);
        //  $this->middleware('api_permission_check:create member', ['only' => ['create']]);
    }
    /**
     * Clear search entry
     *
     * This api clear search entry.
     *
     * @return JsonResponse
     */
    public function clearSearchEntry()
    {
        try {
            $offset = 0;
            $limit = 50;
            $pageNumber = 1;
            $userData = Auth::user();
            $roles = ['admin'];

            if ($userData && $userData->hasAnyRole($roles)) {
                $data['totalMember'] = $this->memberRepository->getMember();
                $data['members'] = $this->memberRepository->getAllByPaginate($offset);
                $data['equbTypes'] = $this->equbTypeRepository->getActive();
                $data['equbs'] = $this->equbRepository->getAll();
                $data['payments'] = $this->paymentRepository->getAllPayment();
                $data['title'] = $this->title;
                $data['limit'] = $limit;
                $data['pageNumber'] = $pageNumber;

                return response()->json($data);
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
     * Get all members
     *
     * This api returns all members.
     *
     * @return JsonResponse
     */
    public function index()
    {
        try {
            $offset = 0;
            $limit = 50;
            $pageNumber = 1;
            $userData = Auth::user();
            $adminRoles = ['admin', 'equb_collector'];
            $memberRoles = ['member'];
            if ($userData->hasAnyRole($adminRoles)) {
                $data['totalMember'] = $this->memberRepository->getMember();
                $data['members'] = $this->memberRepository->getAllByPaginate($offset);
                // $data['equbTypes'] = $this->equbTypeRepository->getActive();
                // $data['equbs'] = $this->equbRepository->getAll();
                // $data['payments'] = $this->paymentRepository->getAllPayment();
                $data['title'] = $this->title;
                $data['limit'] = $limit;
                $data['pageNumber'] = $pageNumber;
                return response()->json([
                    'totalmembers' => $data['totalMember'],
                    'pageNumber' => $data['pageNumber'],
                    'limit' => $data['limit'],
                    'members' => MemberResource::collection($data['members'])
                ]);
            } elseif ($userData->hasAnyRole($memberRoles)) {
                $data['members'] = $this->memberRepository->getByPhone($userData['phone_number']);
                $data['equbTypes'] = $this->equbTypeRepository->getActive();
                $data['equbs'] = $this->equbRepository->getAll();
                $data['payments'] = $this->paymentRepository->getAllPayment();
                $data['title'] = $this->title;
                $data['limit'] = $limit;
                $data['pageNumber'] = $pageNumber;
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
                "error" => $ex->getMessage()
            ]);
        }
    }
    /**
     * Get member by id
     *
     * This api returns member using an id.
     *
     * @return JsonResponse
     */
    // public function getMemberById($id)
    // {
    //     try {
    //         $userData = Auth::user();
    //         // dd($userData);
    //             $data['member'] = $this->memberRepository->getMemberById($id);
    //             return response()->json([
    //                 'member' => new MemberResource($data['member'])
    //             ]);
           
    //     } catch (Exception $ex) {
    //         // dd($ex);
    //         return response()->json([
    //             'code' => 500,
    //             'message' => 'Unable to process your request, Please try again!',
    //             "error" => $ex->getMessage()
    //         ]);
    //     }
    // }
    public function getMemberById($id)
    {
        try {
            $userData = Auth::user();

            // Retrieve member data from the repository
            $member = $this->memberRepository->getMemberById($id);

            // Handle case where member is not found
            if (!$member) {
                return response()->json([
                    'code' => 404,
                    'message' => 'Member not found'
                ], 404);
            }

            // Return response
            return response()->json([
                'member' => new MemberResource($member)
            ]);

        } catch (Exception $ex) {
            // Handle exceptions
            return response()->json([
                'code' => 500,
                'message' => 'Unable to process your request, Please try again!',
                "error" => $ex->getMessage()
            ]);
        }
    }
    /**
     * Get all users with equb type
     *
     * This api returns all users with pagination.
     *
     * @bodyParam equbType string required The equb type to filter. Example: Manual
     *
     * @return JsonResponse
     */
    public function getMembersByEqubType(Request $request)
    {
        try {
            $data['totalUsers'] = $this->memberRepository->getMembersByEqubType($request->equbType);
            return response()->json($data);
        } catch (Exception $ex) {
            return response()->json([
                'code' => 500,
                'message' => 'Unable to process your request, Please try again!',
                "error" => $ex->getMessage()
            ]);
        }
    }
    /**
     * Load more members
     *
     * This api returns more members.
     *
     * @param offsetVal int required The offset. Example: 1
     * @param pageNumberVal int required The page number. Example: 1
     *
     * @return JsonResponse
     */
    public function loadMoreMember($offsetVal, $pageNumberVal)
    {
        try {
            $limit = 10;
            $offset = $offsetVal;
            $pageNumber = $pageNumberVal;
            $userData = Auth::user();
            $adminRoles = ['admin', 'equb_collector'];
            $memberRole = ['member'];
            if ($userData->hasAnyRole($adminRoles)) {

                $data['totalMember'] = $this->memberRepository->getMember();
                $data['members'] = $this->memberRepository->getAllByPaginate($offset);
                $data['title'] = $this->title;
                $data['limit'] = $limit;
                $data['pageNumber'] = $pageNumber;

                return response()->json($data);

            } elseif ($userData->hasAnyRole($memberRole)) {

                $data['members'] = $this->memberRepository->getByPhone($userData['phone_number']);
                $data['equbTypes'] = $this->equbTypeRepository->getActive();
                $data['equbs'] = $this->equbRepository->getAll();
                $data['payments'] = $this->paymentRepository->getAllPayment();
                $data['title'] = $this->title;
                $data['limit'] = $limit;
                $data['pageNumber'] = $pageNumber;
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
                "error" => $ex->getMessage()
            ]);
        }
    }
    /**
     * Check if phone exists
     *
     * This api checks if phone exists.
     *
     * @bodyParam m_id int required The id of the member. Example: 1
     * @bodyParam phone int required The phone number of the member. Example: 0911111111
     *
     * @return JsonResponse
     */
    public function phoneCheck(Request $request)
    {
        try {
            $memberId = $request->m_id;
            $phone = $request->phone;
            if (!empty($phone)) {
                $phoneCheck = $phone;
                $member_count = Member::where('phone', $phoneCheck)->where('id', '!=', $memberId)->count();
                if ($member_count > 0) {
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
     * Check if phone exists
     *
     * This api checks if phone exists.
     *
     * @bodyParam phone int required The phone number of the member. Example: 0911111111
     *
     * @return JsonResponse
     */
    public function checkMemberPhoneExist(Request $request)
    {
        try {
            $phone = $request->phone;
            if (!empty($phone)) {
                $phoneCheck = $phone;
                $member_count = Member::where('phone', $phoneCheck)->count();
                if ($member_count > 0) {
                    echo "true";
                } else {
                    echo "false";
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
     * Get Equb
     *
     * This api returns an equb.
     *
     * @param id int required The id of the equb. Example: 1
     *
     * @return JsonResponse
     */
    public function getEqubs($id)
    {
        try {
            return $this->memberRepository->getEqubs($id);
        } catch (Exception $ex) {
            return response()->json([
                'code' => 500,
                'message' => 'Unable to process your request, Please try again!',
                "error" => $ex->getMessage()
            ]);
        }
    }
    /**
     * Get All Equbs
     *
     * This api returns all equbs.
     *
     * @return JsonResponse
     */
    public function getAllEqubs()
    {
        try {
            return  $this->equbTypeRepository->getAll();
        } catch (Exception $ex) {
            return response()->json([
                'code' => 500,
                'message' => 'Unable to process your request, Please try again!',
                "error" => $ex->getMessage()
            ]);
        }
    }
    public function create()
    {
        try {
            $userData = Auth::user();
            $data['payment'] = $this->paymentRepository->getAll();
            $data['equb'] = $this->equbRepository->getAll();
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
     * Search members
     *
     * This api searches members.
     *
     * @bodyParam searchInput string required The search input. Example: eyob/0911212121
     *
     * @return JsonResponse
     */
    public function searchMember($searchInput, $offset, $pageNumber = null)
    {
        try {
                $data['offset'] = $offset;
                $limit = 50;
                $data['limit'] = $limit;
                $data['totalMember'] = $this->memberRepository->countMember($searchInput);
                if ($offset == 0) {
                    $data['pageNumber'] = 1;
                } else {
                    $data['pageNumber'] = $pageNumber;
                }
                $data['searchInput'] = $searchInput;
                $data['members'] = $this->memberRepository->searchMember($offset, $searchInput);
                return response()->json($data);
            
        } catch (Exception $ex) {
            return response()->json([
                'code' => 500,
                'message' => 'Unable to process your request, Please try again!',
                "error" => $ex->getMessage()
            ]);
        }
    }
    /**
     * Create member
     *
     * This api created equbs.
     *
     * @bodyParam full_name string required The name of the member. Example: eyob
     * @bodyParam phone string required The phone of the member. Example: 0911212121
     * @bodyParam gender string required The gender of the member. Example: male
     * @bodyParam city string required The city of the member. Example: Addis Ababa
     * @bodyParam woreda int required The woreda of the member. Example: 01
     * @bodyParam housenumber int required The house number of the member. Example: 1111
     * @bodyParam location string required The location of the member. Example: bole
     *
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        return 123;
        $shortcode = config('key.SHORT_CODE');
        try {
                $userData = Auth::user();
                $this->validate(
                    $request,
                    [
                        'full_name' => 'required',
                        'phone' => 'required',
                        'gender' => 'required',
                        'city' => 'required',
                        'location' => 'required',
                        'date_of_birth' => 'required'
                    ]
                );
                $fullName = $request->input('full_name');
                $phone = $request->input('phone');
                $gender = $request->input('gender');
                $city = $request->input('city');
                $subcity = $request->input('subcity');
                $woreda = $request->input('woreda');
                $housenumber = $request->input('housenumber');
                $location = $request->input('location');
                $email = $request->input('email');
                $date_of_birth = $request->input('date_of_birth');
                $password = rand(100000, 999999);

                if (!empty($phone) && Member::where('phone', $phone)->exists()) {
                    return response()->json(['code' => 403, 'message' => 'Phone already exists']);
                }
                if (!empty($email) && Member::where('email', $email)->exists()) {
                    return response()->json(['code' => 403, 'message' => 'Email already exists']);
                }
                
                $memberData = [
                    'full_name' => $fullName,
                    'phone' => $phone,
                    'gender' => $gender,
                    'email' => $email,
                    'city' => $city,
                    'subcity' => $subcity,
                    'woreda' => $woreda,
                    'house_number' => $housenumber,
                    'specific_location' => $location,
                    'date_of_birth' => $date_of_birth
                    // 'address' => json_encode($address),
                ];
                $create = $this->memberRepository->create($memberData);
                
                // dd($memberData);
                $user = [
                    'name' => $fullName,
                    'email' => $email,
                    'password' => Hash::make($password),
                    'phone_number' => $phone,
                    'gender' => $gender
                ];
                $user = $this->userRepository->createUser($user);
                $memberRole = Role::firstOrCreate(['name' => 'Member']);
                // $user->assignRole($memberRole);
                $user->assignRole($memberRole->name);

                $roleName = $create->getRoleNames()->first();
                if ($create && $user) {
                    $activityLog = [
                        'type' => 'members',
                        'type_id' => $create->id,
                        'action' => 'created',
                        'user_id' => $userData->id,
                        'username' => $userData->name,
                        'role' => $roleName,
                    ];
                    $this->activityLogRepository->createActivityLog($activityLog);
                    try {
                        $message = "Welcome to Virtual Equb! You have registered succesfully. Use the phone number " . $phone . " and password " . $password . " to log in." . " For further information please call " . $shortcode;
                        // dd($message);
                        $this->sendSms($request->phone, $message);
                    } catch (Exception $ex) {
                        return response()->json([
                            'code' => 400,
                            'message' => 'Failed to send SMS',
                            "error" => "Failed to send SMS"
                        ]);
                    };
                    return response()->json([
                        'code' => 200,
                        'message' => "Member has been registered successfully!",
                        'data' => new MemberResource($create),
                    ]);
                } else {
                    return response()->json([
                        'code' => 400,
                        'message' => 'Unknown error occurred, Please try again!',
                        "error" => "Unknown error occurred, Please try again!"
                    ]);
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
     * Show members equbs
     *
     * This api shows members equbs.
     *
     * @param id int required The id of the member. Example: 1
     *
     * @return JsonResponse
     */

    public function show($id)
    {
        try {
            $data['totalPayment'] = $this->paymentRepository->getTotalPaid($id);
            $data['member'] = $this->memberRepository->getByIdNested($id);
            $member = $data['member'];
            $equbs = $member->equbs;
            $equbsArray = [];

            foreach ($equbs as $equb) {
                $totalPpayment = Payment::where('equb_id', $equb['id'])
                    ->where('status', 'paid')
                    ->sum('amount');
                $totalEqubAmount = Equb::select('total_amount')
                    ->where('id', $equb['id'])
                    ->pluck('total_amount')
                    ->first();
                    // dd($totalEqubAmount);
                $remainingPayment = $totalEqubAmount - $totalPpayment;

                if ($remainingPayment > 0) {
                    $lotteryDate = Equb::where('id', $equb['id'])
                        ->pluck('lottery_date')
                        ->first();
                    $equbType = EqubType::where('id', $equb['equb_type_id'])->first();

                    // Check if equbType or lotteryDate is null
                    if (!$equbType || !$lotteryDate) {
                        continue; // Skip this equb if the required data is missing
                    }

                    $lotteryDate = explode(',', $lotteryDate);
                    $date = date('Y-m-d');
                    $lotteryDate = $lotteryDate[0];
                    $date1 = new DateTime($date);
                    $date2 = new DateTime($lotteryDate);
                    $date3 = new DateTime($equbType->lottery_date);

                    if ($date2 > $date1) {
                        $interval = $date2->diff($date1)->days;
                    } elseif ($date2 == $date1) {
                        $interval = 0;
                    } else {
                        $interval = "passed";
                    }

                    if ($date3 > $date1) {
                        $autoInterval = $date3->diff($date1)->days;
                    } elseif ($date3 == $date1) {
                        $autoInterval = 0;
                    } else {
                        $autoInterval = "passed";
                    }
                    // Additional feature
                    // if ($equbType->type == 'Automatic') {
                    //     error_log("Using Automatic Interval: " . $autoInterval);
                    // } else {
                    //     error_log("Using Manual Interval: " . $interval);
                    // }
                    $equb['total_payment'] = $totalPpayment;
                    $equb['remaining_payment'] = $remainingPayment;
                    $equb['remaining_lottery_date'] = $equbType->type == 'Automatic' ? $autoInterval : $interval;
                    array_push($equbsArray, $equb);
                    
                }
            }

            return response()->json($equbsArray);
        } catch (Exception $ex) {
            return response()->json([
                'code' => 500,
                'message' => 'Unable to process your request, Please try again!',
                "error" => $ex->getMessage()
            ]);
        }
    }
    // public function show($id)
    // {
    //     try {
    //         $member = $this->memberRepository->getByIdNested($id);
    
    //         // Early return if no member is found
    //         if (!$member) {
    //             return response()->json(['message' => 'Member not found'], 404);
    //         }
    
    //         $equbs = $member->equbs;
    
    //         // Preload required data for calculations
    //         $equbIds = $equbs->pluck('id')->toArray();
    
    //         $totalPayments = Payment::whereIn('equb_id', $equbIds)
    //             ->where('status', 'paid')
    //             ->selectRaw('equb_id, SUM(amount) as total_paid')
    //             ->groupBy('equb_id')
    //             ->get()
    //             ->keyBy('equb_id');
    
    //         $equbDetails = Equb::whereIn('id', $equbIds)
    //             ->select('id', 'total_amount', 'lottery_date')
    //             ->get()
    //             ->keyBy('id');
    
    //         $equbTypes = EqubType::whereIn('id', $equbs->pluck('equb_type_id')->toArray())
    //             ->get()
    //             ->keyBy('id');
    
    //         $currentDate = new DateTime(date('Y-m-d'));
    
    //         $equbsArray = $equbs->map(function ($equb) use ($totalPayments, $equbDetails, $equbTypes, $currentDate) {
    //             $equbId = $equb['id'];
    
    //             $totalPayment = $totalPayments[$equbId]->total_paid ?? 0;
    //             $totalEqubAmount = $equbDetails[$equbId]->total_amount ?? 0;
    //             $remainingPayment = $totalEqubAmount - $totalPayment;
    
    //             if ($remainingPayment <= 0) {
    //                 return null;
    //             }
    
    //             $lotteryDateStr = $equbDetails[$equbId]->lottery_date ?? null;
    //             $lotteryDate = $lotteryDateStr ? new DateTime(explode(',', $lotteryDateStr)[0]) : null;
    
    //             $equbType = $equbTypes[$equb['equb_type_id']] ?? null;
    //             $equbTypeLotteryDate = $equbType ? new DateTime($equbType->lottery_date) : null;
    
    //             $interval = $lotteryDate && $lotteryDate > $currentDate 
    //                 ? $lotteryDate->diff($currentDate)->days 
    //                 : ($lotteryDate === $currentDate ? 0 : 'passed');
    
    //             $autoInterval = $equbTypeLotteryDate && $equbTypeLotteryDate > $currentDate 
    //                 ? $equbTypeLotteryDate->diff($currentDate)->days 
    //                 : ($equbTypeLotteryDate === $currentDate ? 0 : 'passed');
    
    //             return [
    //                 'id' => $equbId,
    //                 'total_payment' => $totalPayment,
    //                 'remaining_payment' => $remainingPayment,
    //                 'remaining_lottery_date' => $equbType && $equbType->type == 'Automatic' ? $autoInterval : $interval,
    //             ];
    //         })->filter()->values(); // Filter out null values and reset indices
    
    //         return response()->json($equbsArray, 200);
    
    //     } catch (\Exception $ex) {
    //         return response()->json([
    //             'code' => 500,
    //             'message' => 'Unable to process your request, Please try again!',
    //             'error' => $ex->getMessage()
    //         ], 500);
    //     }
    // }
    
    public function getPaidEqubs($id)
    {
        try {
                $data['totalPayment'] = $this->paymentRepository->getTotalPaid($id);
                $data['member'] = $this->memberRepository->getByIdNested($id);
                $member = $data['member'];
                $equbs = $member->equbs;
                // dd($equbs);
                $equbsArray = [];
                foreach ($equbs as $equb) {
                    $totalPpayment = Payment::where('equb_id', $equb['id'])->where('status', 'paid')->sum('amount');
                    $totalEqubAmount = Equb::select('total_amount')->where('id', $equb['id'])->pluck('total_amount')->first();
                    $remainingPayment =  $totalEqubAmount - $totalPpayment;
                    if ($remainingPayment <= 0) {
                        // dd($remainingPayment);
                        $lotteryDate = Equb::where('id', $equb['id'])->pluck('lottery_date')->first();
                        $equbType = EqubType::where('id', $equb['equb_type_id'])->first();
                        $lotteryDate = explode(',', $lotteryDate);
                        $date = date('Y-m-d');
                        $lotteryDate = $lotteryDate[0];
                        $date1 = new DateTime($date);
                        $date2 = new DateTime($lotteryDate);
                        $date3 = new DateTime($equbType->lottery_date);
                        if ($date2 > $date1) {
                            $interval = $date2->diff($date1);
                            $interval = $interval->days;
                        } elseif ($date2 == $date1) {
                            $interval = 0;
                        } else {
                            $interval = "passed";
                        }
                        if ($date3 > $date1) {
                            $autoInterval = $date3->diff($date1);
                            $autoInterval = $autoInterval->days;
                        } elseif ($date3 == $date1) {
                            $autoInterval = 0;
                        } else {
                            $autoInterval = "passed";
                        }
                        $equb['total_payment'] = $totalPpayment;
                        $equb['remaining_payment'] = $remainingPayment;
                        $equb['remaining_lottery_date'] = $equbType->type == 'Automatic' ? $autoInterval : $interval;
                        array_push($equbsArray, $equb);
                    }
                }
                // dd($equbs);
                return response()->json($equbsArray);
        } catch (Exception $ex) {
            return response()->json([
                'code' => 500,
                'message' => 'Unable to process your request, Please try again!',
                "error" => $ex->getMessage()
            ]);
        }
    }
    // public function getEndedEqubs($id) {
    //     try {
    //         $data['member'] = $this->memberRepository->getByIdNested($id);
    //         $member = $data['member'];
    //         $equbs = $member->equbs;

    //         $passedEqubArray = [];
    //         $currentDate = date('Y-m-d');

    //         foreach($equbs as $equb) {
    //             $equbEndDate = Equb::select('end_date')->where('id', $equb['id'])->pluck('end_date')->first();
    //             // dd($equbEndDate);
    //             if ($equbEndDate < $currentDate) {
    //                 $totalPpayment = Payment::where('equb_id', $equb['id'])->where('status', 'paid')->sum('amount');
    //                 $totalEqubAmount = Equb::select('total_amount')->where('id', $equb['id'])->pluck('total_amount')->first();
    //                 $remainingPayment = $totalEqubAmount - $totalPpayment;

    //                 $equb['total_payment'] = $totalPpayment;
    //                 $equb['remaining_payment'] = $remainingPayment;
    //                 // $equb['statu']

    //                 array_push($passedEqubArray, $equb);
    //             }
    //         }

    //         return response()->json($passedEqubArray);

    //     } catch (Exception $ex) {
    //         return response()->json([
    //             'code' => 500,
    //             'message' => 'Unable to process your request, Please try again!',
    //             "error" => $ex->getMessage()
    //         ]);
    //     }
    // }
    public function getEndedEqubs($id)
    {
        try {
            $data['member'] = $this->memberRepository->getByIdNested($id);
            $member = $data['member'];
            $equbs = $member->equbs;
            // dd($data['member']);
            $passedEqubArray = [];
            $currentDate = date('Y-m-d');

            foreach ($equbs as $equb) {
                // Fetch equb details: end_date and total_amount
                $equbData = Equb::select('end_date', 'total_amount')
                    ->where('id', $equb['id'])
                    ->first();

                // Log equb data for debugging
                if (!$equbData) {
                    \Log::info("Equb not found for ID: {$equb['id']}");
                    continue; // Skip if no equb data found
                }

                $equbEndDate = $equbData->end_date;
                $totalEqubAmount = $equbData->total_amount;

                // Debugging log
                \Log::info("Equb ID: {$equb['id']}, End Date: {$equbEndDate}, Total Amount: {$totalEqubAmount}");

                // Only process equbs where the end date has passed
                if ($equbEndDate < $currentDate) {
                    // Calculate the total payments made by the member for this equb
                    $totalPayment = Payment::where('equb_id', $equb['id'])
                        ->where('status', 'paid')
                        ->sum('amount');

                    $remainingPayment = $totalEqubAmount - $totalPayment;

                    // Debugging log
                    \Log::info("Equb ID: {$equb['id']}, Total Payment: {$totalPayment}, Remaining Payment: {$remainingPayment}");

                    // Exclude equbs where the total amount has been fully paid
                    if ($remainingPayment > 0) {
                        $equb['total_payment'] = $totalPayment;
                        $equb['remaining_payment'] = $remainingPayment;

                        array_push($passedEqubArray, $equb);
                    }
                }
            }

            // Debugging log for result
            \Log::info('Passed Equbs:', $passedEqubArray);

            return response()->json($passedEqubArray);

        } catch (Exception $ex) {
            return response()->json([
                'code' => 500,
                'message' => 'Unable to process your request, Please try again!',
                "error" => $ex->getMessage()
            ]);
        }
    }
    /**
     * Update member status
     *
     * This api updates member status.
     *
     * @param id int required The id of the member. Example: 1
     *
     * @return JsonResponse
     */
    public function updateStatus($id, Request $request)
    {
        try {
            // return response()->json($request);
            $userData = Auth::user();
            $Roles = ['admin', 'equb_collector'];
            if ($userData && $userData->hasAnyRole($Roles)) {
                $status = $this->memberRepository->getStatusById($id)->status;
                if ($status == "Deactive") {
                    $status = "Active";
                } else {
                    $status = "Deactive";
                }
                $updated = [
                    'status' => $status,
                ];
                $updated = $this->memberRepository->update($id, $updated);
                if ($updated) {
                    if ($status == "Deactive") {
                        $updateEqubStatus = [
                            'status' => 'Deactive'
                        ];
                        $updated = $this->equbRepository->updateEqubStatus($id, $updateEqubStatus);
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
                        'message' => "Status has been updated seccessfully",
                        'data' => new MemberResource($updated)
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
     * Update member
     *
     * This api updates member status.
     *
     * @param id int required The id of the member. Example: 1
     *
     * @bodyParam full_name string required The ame of the member. Example: eyob
     * @bodyParam phone int required The phone of the member. Example: 0911252525
     * @bodyParam gender string required The gender of the member. Example: male
     * @bodyParam email string required The email of the member. Example: eyob@gmail.com
     * @bodyParam city string required The city of the member. Example: Addis Ababa
     * @bodyParam subcity string required The subcity of the member. Example: Bole
     * @bodyParam woreda string required The woreda of the member. Example: 05
     * @bodyParam housenumber string required The housenumber of the member. Example: 1414
     * @bodyParam location string required The location of the member. Example: Bole
     *
     * @return JsonResponse
     */
    public function update($id, Request $request)
    {
        // dd($request);
        try {
                $userData = Auth::user();
                $name = $request->input('full_name');
                $phone = $request->input('phone');
                $gender = $request->input('gender');
                $city = $request->input('update_city');
                $subcity = $request->input('update_subcity');
                $woreda = $request->input('update_woreda');
                $housenumber = $request->input('update_housenumber');
                $location = $request->input('update_location');
                $email = $request->input('email');
                $member_phone = $this->memberRepository->getPhone($id);
                $member_phone = $member_phone->phone;
                $user_id = $this->userRepository->getUserId($member_phone);
                $user_id = $user_id->id;
                
                $updated = [
                    'full_name' => $name,
                    'phone' => $phone,
                    'gender' => $gender,
                    'email' => $email,
                    'city' => $city,
                    'subcity' => $subcity,
                    'woreda' => $woreda,
                    'house_number' => $housenumber,
                    'specific_location' => $location

                ];
                if (!empty($phone)) {
                    $member_count = Member::where('phone', $phone)->where('id', '!=', $id)->count();
                    if ($member_count > 0) {
                        return response()->json([
                            'code' => 403,
                            'message' => 'Email already exist',
                        ]);
                    }
                }
                if (!empty($email)) {
                    $member_count = Member::where('email', $email)->where('id', '!=', $id)->count();
                    if ($member_count > 0) {
                        return response()->json([
                            'code' => 403,
                            'message' => 'Email already exist',
                        ]);
                    }
                }
                $updated = $this->memberRepository->update($id, $updated);
                $updateUser = [
                    'name' => $name,
                    'phone_number' => $phone,
                    'gender' => $gender,
                    'email' => $email
                ];
                $updateUser = $this->userRepository->updateUser($user_id, $updateUser);
                if ($updated && $updateUser) {
                    $activityLog = [
                        'type' => 'members',
                        'type_id' => $id,
                        'action' => 'updated',
                        'user_id' => $userData->id,
                        'username' => $userData->name,
                        'role' => $userData->role,
                    ];
                    $this->activityLogRepository->createActivityLog($activityLog);
                    return response()->json([
                        'code' => 200,
                        'message' => 'Member has been updated successfully!',
                        'data' => new MemberResource($updated)
                    ]);
                } else {
                    return response()->json([
                        'code' => 400,
                        'message' => 'Unknown error occurred, Please try again!',
                        "error" => "Unknown error occurred, Please try again!"
                    ]);
                }
        } catch (Exception $ex) {
            return response()->json([
                'code' => 500,
                'message' => 'Unable to process your request, Please try again!',
                "error" => $ex->getMessage()
            ]);
        }
    }
    public function rate($id, Request $request)
    {
        // dd($id);
        try {
            $roles = ['admin', 'equb_collector'];
            $userData = Auth::user();
            if ($userData && $userData->hasAnyRole($roles)) {
                $this->validate(
                    $request,
                    [
                        'rating' => 'required'
                    ]
                );
                $rating = $request->input('rating');
                $updated = [
                    'rating' => $rating
                ];
                $updated = $this->memberRepository->update($id, $updated);
                if ($updated) {
                    $activityLog = [
                        'type' => 'members',
                        'type_id' => $id,
                        'action' => 'updated',
                        'user_id' => $userData->id,
                        'username' => $userData->name,
                        'role' => $userData->role,
                    ];
                    $this->activityLogRepository->createActivityLog($activityLog);
                    return response()->json([
                        'code' => 200,
                        'message' => 'Member rating has been updated successfully!',
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
            // dd($ex);
            return response()->json([
                'code' => 500,
                'message' => 'Unable to process your request, Please try again!',
                "error" => $ex
            ]);
        }
    }
    /**
     * Delete Member
     *
     * This api deletes member.
     *
     * @param id int required The id of the member. Example: 1
     *
     * @return JsonResponse
     */
    public function destroy($id)
    {
        try {
            $userData = Auth::user();
            $roles = ['admin', 'equb_collector'];
            if ($userData && $userData->hasAnyRole($roles)) {
                $member = $this->equbRepository->getMember($id);
                if (!$member->isEmpty()) {
                    return response()->json([
                        'code' => 400,
                        'message' => 'This member has history and can not be deleted'
                    ]);
                }
                $member = $this->memberRepository->getByIdToDelete($id);
                if ($member != null) {
                    $deleted = $this->memberRepository->delete($id);
                    if ($deleted) {
                        $user_id = $this->userRepository->getUserId($member->phone);
                        if ($user_id) {
                            $userId = $user_id->id;
                            $deletedUser = $this->userRepository->deleteUser($userId);
                        }
                        $activityLog = [
                            'type' => 'members',
                            'type_id' => $id,
                            'action' => 'deleted',
                            'user_id' => $userData->id,
                            'username' => $userData->name,
                            'role' => $userData->role,
                        ];
                        $this->activityLogRepository->createActivityLog($activityLog);
                        return response()->json([
                            'code' => 200,
                            'message' => 'Member has been deleted successfully!'
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
                        'code' => 401,
                        'message' => 'member not found'
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
                "error" => $ex->getMessage()
            ]);
        }
    }
    /**
     * Register member
     *
     * This api regsters members.
     *
     * @bodyParam full_name string required The name of the member. Example: eyob
     * @bodyParam phone string required The phone of the member. Example: 0911212121
     * @bodyParam gender string required The gender of the member. Example: male
     * @bodyParam city string required The city of the member. Example: Addis Ababa
     * @bodyParam woreda int required The woreda of the member. Example: 01
     * @bodyParam housenumber int required The house number of the member. Example: 1111
     * @bodyParam location string required The location of the member. Example: bole
     * @bodyParam password string required The password of the member. Example: P@ssw0rd
     *
     * @response 200 {
     *     "message": "Member registered successfully."
     *      "user": {
     *         "id": 1,
     *         "full_name": "Eyob",
     *         "phone": "0911212121",
     *         "gender": "Male",
     *         "city": "Addis Ababa",
     *         "woreda": 1,
     *         "housenumber": 1111,
     *         "location": "Bole"
     *     }
     * }
     * @response 400 {
     *     "message": "Unknown error occurred, Please try again!."
     * }
     * @return JsonResponse
     */
    public function register(Request $request)
    {
        // return 12;
        $shortcode = config('key.SHORT_CODE');
        try {
            // Validation rules
            $this->validate(
                $request,
                [
                    'full_name' => 'required',
                    'phone' => 'required',
                    'gender' => 'required',
                    'date_of_birth' => 'required|date|before:' . now()->subYears(18)->format('Y-m-d'), // Must be before 18 years ago
                    'password' => 'required'
                ],
                [
                    'date_of_birth.before' => 'You must be at least 18 years old to register.'
                ]
            );

            // Handle the input data
            $fullName = $request->input('full_name');
            $phone = $request->input('phone');
            $gender = $request->input('gender');
            $city = $request->input('city');
            $subcity = $request->input('subcity');
            $woreda = $request->input('woreda');
            $housenumber = $request->input('housenumber');
            $location = $request->input('location');
            $email = $request->input('email');
            $password = $request->input('password');
            $dateofBirth = $request->input('date_of_birth');

            // Check if the phone number already exists
            if (!empty($phone)) {
                $member_count = Member::where('phone', $phone)->count();
                $user_count = User::where('phone_number', $phone)->count();
                if ($member_count > 0 || $user_count > 0) {
                    return response()->json([
                        'code' => 403,
                        'message' => 'Phone already exists',
                    ]);
                }
            }

            // Check if the email already exists
            if (!empty($email)) {
                $member_count = Member::where('email', $email)->count();
                $user_count = User::where('email', $email)->count();
                if ($member_count > 0 || $user_count > 0) {
                    return response()->json([
                        'code' => 403,
                        'message' => 'Email already exists',
                    ]);
                }
            }

            // Prepare the member data
            $memberData = [
                'full_name' => $fullName,
                'phone' => $phone,
                'gender' => $gender,
                'email' => $email,
                'city' => $city,
                'subcity' => $subcity,
                'woreda' => $woreda,
                'house_number' => $housenumber,
                'specific_location' => $location,
                'status' => "Active",
                'date_of_birth' => $dateofBirth
            ];

            // Handle the profile picture upload
            if ($request->file('profile_picture')) {
                $image = $request->file('profile_picture');
                $imageName = time() . '.' . $image->getClientOriginalExtension();
                $image->storeAs('public/profile_pictures', $imageName);
                $memberData['profile_photo_path'] = 'profile_pictures/' . $imageName;
            }

            // Create member and user
            $create = $this->memberRepository->create($memberData);
            $user = [
                'name' => $fullName,
                'email' => $email,
                'password' => Hash::make($password),
                'phone_number' => $phone,
                'gender' => $gender,
                'status' => 'Active'
            ];
            $user = $this->userRepository->createUser($user);

            $memberRoleAPI = Role::firstOrCreate(['name' => 'member', 'guard_name' => 'api']);
            $memberRoleWEB = Role::firstOrCreate(['name' => 'member', 'guard_name' => 'web']);
            $user->assignRole($memberRoleWEB);
            $user->assignRole($memberRoleAPI);

            if ($create && $user) {
                try {
                    $message = "Welcome to Virtual Equb! You have registered succesfully. Use the phone number " . $phone . " and password " . $password . " to log in." . " For further information please call " . $shortcode;
                    // dd($message);
                    $this->sendSms($request->phone, $message);
                } catch (Exception $ex) {
                    return response()->json([
                        'code' => 400,
                        'message' => 'Failed to send SMS',
                        "error" => "Failed to send SMS"
                    ]);
                };
                return response()->json([
                    'code' => 200,
                    'message' => "Member has registered successfully!",
                    'data' => new MemberResource($create)
                ]);
            } else {
                return response()->json([
                    'code' => 400,
                    'message' => 'Unknown error occurred, Please try again!',
                    "error" => "Unknown error occurred, Please try again!"
                ]);
            }
        } catch (Exception $ex) {
            return response()->json([
                'code' => 400,
                'message' => 'Unknown error occurred, Please try again!',
                "error" => $ex->getMessage()
            ]);
        }
    }
    // public function getProfilePicture($userId)
    // {
    //     $user = Member::findOrFail($userId);
    //     $path = 'public/' . $user->profile_photo_path;
    //     // dd($path);
    //     if (Storage::exists($path)) {
    //         $file = Storage::get($path);
    //         $type = Storage::mimeType($path);

    //         return response($file, 200)->header('Content-Type', $type);
    //     } else {
    //         return null;
    //     }
    //     return response()->json([
    //         'code' => 400,
    //         'message' => 'Image not found',
    //         "error" => "Image not found"
    //     ]);
    // }
    // public function getProfilePicture1($userId)
    // {
    //     try {
    //         // Retrieve the member data
    //         $member = Member::findOrFail($userId);

    //         // Return the member resource, which includes the profile picture information
    //         return new MemberResource($member);

    //     } catch (\Exception $ex) {
    //         // Handle exceptions and return a JSON error response
    //         return response()->json([
    //             'code' => 500,
    //             'message' => 'Failed to retrieve profile picture',
    //             'error' => $ex->getMessage()
    //         ], 500);
    //     }
    // }
    public function getProfilePicture($userId)
    {
        try {
            // Use caching to optimize frequent requests
            $profilePhotoPath = Cache::remember("user_{$userId}_profile_photo_path", 60, function () use ($userId) {
                return Member::where('id', $userId)->value('profile_photo_path');
            });

            if (!$profilePhotoPath) {
                return response()->json([
                    'code' => 404,
                    'message' => 'Profile picture not found'
                ], 404);
            }

            // Build the URL for the profile picture
            $profilePhotoUrl = asset('storage/' . $profilePhotoPath);

            return response()->json([
                'code' => 200,
                'message' => 'Profile picture retrieved successfully',
                'data' => ['profile_photo_url' => $profilePhotoUrl]
            ], 200);

        } catch (\Exception $ex) {
            return response()->json([
                'code' => 500,
                'message' => 'Failed to retrieve profile picture',
                'error' => $ex->getMessage()
            ], 500);
        }
    }


    /**
     * Update profile
     *
     * This api updates members profiles.
     *
     * @param id int required The id of the member. Example: 1
     *
     * @bodyParam full_name string required The ame of the member. Example: eyob
     * @bodyParam phone int required The phone of the member. Example: 0911252525
     * @bodyParam gender string required The gender of the member. Example: male
     * @bodyParam email string required The email of the member. Example: eyob@gmail.com
     * @bodyParam city string required The city of the member. Example: Addis Ababa
     * @bodyParam subcity string required The subcity of the member. Example: Bole
     * @bodyParam woreda string required The woreda of the member. Example: 05
     * @bodyParam housenumber string required The housenumber of the member. Example: 1414
     * @bodyParam location string required The location of the member. Example: Bole
     *
     * @return JsonResponse
     */
    // public function updateProfile($id, Request $request)
    // {
    //     // dd($request);
    //     try {
    //             $userData = Auth::user();
    //             $name = $request->input('full_name');
    //             $phone = $request->input('phone');
    //             $gender = $request->input('gender');
    //             $city = $request->input('city');
    //             $subcity = $request->input('subcity');
    //             $woreda = $request->input('woreda');
    //             $housenumber = $request->input('housenumber');
    //             $location = $request->input('location');
    //             $email = $request->input('email');
    //             // dd($address);
    //             $updated = [
    //                 'full_name' => $name,
    //                 'phone' => $phone,
    //                 'gender' => $gender,
    //                 'email' => $email,
    //                 'city' => $city,
    //                 'subcity' => $subcity,
    //                 'woreda' => $woreda,
    //                 'house_number' => $housenumber,
    //                 'specific_location' => $location
    //             ];
    //             if ($request->file('profile_picture')) {
    //                 $image = $request->file('profile_picture');
    //                 $imageName = time() . '.' . $image->getClientOriginalExtension();
    //                 $image->storeAs('public/profile_pictures', $imageName);
    //                 // $updated['profile_photo_path'] = 'profile_pictures/' . $imageName;
    //                 $updated['profile_photo_path'] = 'profile_pictures/' . $imageName;
    //             }
    //             if (!empty($phone)) {
    //                 $member_count = Member::where('phone', $phone)->where('id', '!=', $id)->count();
    //                 if ($member_count > 0) {
    //                     return response()->json([
    //                         'code' => 403,
    //                         'message' => 'Phone already exist',
    //                     ]);
    //                 }
    //             }
    //             if (!empty($email)) {
    //                 $member_count = Member::where('email', $email)->where('id', '!=', $id)->count();
    //                 if ($member_count > 0) {
    //                     return response()->json([
    //                         'code' => 403,
    //                         'message' => 'Email already exist',
    //                     ]);
    //                 }
    //             }
                
    //             $updated = $this->memberRepository->update($id, $updated);
    //             // dd($id);
    //             $updateUser = [
    //                 'name' => $name,
    //                 'phone_number' => $phone,
    //                 'gender' => $gender,
    //                 'email' => $email
    //             ];
    //             $updateUser = $this->userRepository->updateUser($userData->id, $updateUser);

    //             if ($updated && $updateUser) {
    //                 return response()->json([
    //                     'code' => 200,
    //                     'message' => 'Profile has been updated successfully!',
    //                     'data' => $updated
    //                 ]);
    //             } else {
    //                 return response()->json([
    //                     'code' => 400,
    //                     'message' => 'Unknown error occurred, Please try again!',
    //                     "error" => "Unknown error occurred, Please try again!"
    //                 ]);
    //             }
    //     } catch (Exception $ex) {
    //         return response()->json([
    //             'code' => 500,
    //             'message' => 'Unable to process your request, Please try again!',
    //             "error" => $ex->getMessage()
    //         ]);
    //     }
    // }
    public function updateProfile($id, Request $request)
    {
        try {
            // Authenticate user
            $userData = Auth::user();
            // dd($request->all());
            // Validation rules for form data (especially file upload)
            $this->validate($request, [
                'full_name' => 'nullable|string',
                'phone' => ['nullable', 'string', Rule::unique('members')->ignore($id)],
                'email' => ['nullable', 'email', Rule::unique('members')->ignore($id)],
                'gender' => 'nullable|in:male,female,other',
                'city' => 'nullable|string',
                'subcity' => 'nullable|string',
                'woreda' => 'nullable|string',
                'housenumber' => 'nullable|string',
                'location' => 'nullable|string',
                'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
            ]);

            // Collect data from the request
            $updatedData = [
                'full_name' => $request->input('full_name', $userData->full_name),
                'phone' => $request->input('phone', $userData->phone),
                'gender' => $request->input('gender', $userData->gender),
                'email' => $request->input('email', $userData->email),
                'city' => $request->input('city', $userData->city),
                'subcity' => $request->input('subcity', $userData->subcity),
                'woreda' => $request->input('woreda', $userData->woreda),
                'housenumber' => $request->input('housenumber', $userData->housenumber),
                'location' => $request->input('location', $userData->specific_location),
                'specific_location' => $request->input('specific_location', $userData->specific_location)
            ];
            

            // Handle profile picture upload (optional)
            if ($request->hasFile('profile_picture')) {
                $image = $request->file('profile_picture');
                $imageName = time() . '.' . $image->getClientOriginalExtension();
                $image->storeAs('public/profile_pictures', $imageName);
                $updatedData['profile_photo_path'] = 'profile_pictures/' . $imageName;
            }

            // Remove null values from $updatedData to avoid overwriting with null values
            $updatedData = array_filter($updatedData, fn($value) => !is_null($value));

            // Update the `members` table directly
            $member = Member::findOrFail($id);
            $member->update($updatedData);

            // Update the `users` table directly
            $userUpdates = [
                'name' => $request->input('full_name', $userData->name),
                'phone_number' => $request->input('phone', $userData->phone_number),
                'gender' => $request->input('gender', $userData->gender),
                'email' => $request->input('email', $userData->email)
            ];

            $userData->update($userUpdates);

            // Return success response with updated member data using a resource
            return response()->json([
                'code' => 200,
                'message' => 'Profile has been updated successfully!',
                'data' => new MemberResource($member)  // Return the updated member using a resource
            ]);

        } catch (\Illuminate\Database\QueryException $ex) {
            return response()->json([
                'code' => 500,
                'message' => 'Unable to process your request, Please try again!',
                'error' => $ex->getMessage()
            ]);
        } catch (Exception $ex) {
            return response()->json([
                'code' => 500,
                'message' => 'An error occurred, Please try again!',
                'error' => $ex->getMessage()
            ]);
        }
    }
}
