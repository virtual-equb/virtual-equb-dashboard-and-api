<?php

namespace App\Http\Controllers\api;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Payment;
use App\Models\Equb;
use App\Models\Member;
use App\Models\User;
use App\Http\Controllers\Controller;
use App\Models\EqubType;
use Exception;
use App\Repositories\User\IUserRepository;
use App\Repositories\Member\IMemberRepository;
use App\Repositories\Payment\IPaymentRepository;
use App\Repositories\EqubType\IEqubTypeRepository;
use App\Repositories\Equb\IEqubRepository;
use App\Repositories\ActivityLog\IActivityLogRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use DateTime;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;

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
            if ($userData['role'] == "admin") {
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
            if ($userData['role'] == "admin" || $userData['role'] == "equb_collector") {
                $data['totalMember'] = $this->memberRepository->getMember();
                $data['members'] = $this->memberRepository->getAllByPaginate($offset);
                // $data['equbTypes'] = $this->equbTypeRepository->getActive();
                // $data['equbs'] = $this->equbRepository->getAll();
                // $data['payments'] = $this->paymentRepository->getAllPayment();
                $data['title'] = $this->title;
                $data['limit'] = $limit;
                $data['pageNumber'] = $pageNumber;
                return response()->json($data);
            } elseif ($userData['role'] == "member") {
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
                "error" => $ex
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
    public function getMemberById($id)
    {
        try {
            $userData = Auth::user();
            // dd($userData);
            if ($userData['role'] == "member" || $userData['role'] == "admin"  || $userData['role'] == "equb_collector") {
                $data['member'] = $this->memberRepository->getMemberById($id);
                return response()->json($data);
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
                "error" => $ex
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
            if ($userData['role'] == "admin" || $userData['role'] == "equb_collector") {
                $data['totalMember'] = $this->memberRepository->getMember();
                $data['members'] = $this->memberRepository->getAllByPaginate($offset);
                // $data['equbTypes'] = $this->equbTypeRepository->getActive();
                // $data['equbs'] = $this->equbRepository->getAll();
                // $data['payments'] = $this->paymentRepository->getAllPayment();
                $data['title'] = $this->title;
                $data['limit'] = $limit;
                $data['pageNumber'] = $pageNumber;
                return response()->json($data);
            } elseif ($userData['role'] == "member") {
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
                "error" => $ex
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
                "error" => $ex
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
                "error" => $ex
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
                "error" => $ex
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
                "error" => $ex
            ]);
        }
    }
    public function create()
    {
        try {
            $userData = Auth::user();
            if ($userData && ($userData['role'] == "admin" || $userData['role'] == "equb_collector" ||
                $userData['role'] == "member")) {
                $data['payment'] = $this->paymentRepository->getAll();
                $data['equb'] = $this->equbRepository->getAll();
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
            $userData = Auth::user();
            if ($userData['role'] == "admin" || $userData['role'] == "equb_collector") {
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
        $shortcode = config('key.SHORT_CODE');
        try {
            $userData = Auth::user();
            // if ($userData && ($userData['role'] == "admin") || ($userData['role'] == "equb_collector")) {
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
                        'data' => $create,
                        'user' => $user
                    ]);
                } else {
                    return response()->json([
                        'code' => 400,
                        'message' => 'Unknown error occurred, Please try again!',
                        "error" => "Unknown error occurred, Please try again!"
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
            $userData = Auth::user();
            if (
                $userData['role'] == "admin" || $userData['role'] == "equb_collector"
                || $userData['role'] == "member"
            ) {
                $data['totalPayment'] = $this->paymentRepository->getTotalPaid($id);
                $data['member'] = $this->memberRepository->getByIdNested($id);
                $member = $data['member'];
                $equbs = $member->equbs;
                $equbsArray = [];
                foreach ($equbs as $equb) {
                    $totalPpayment = Payment::where('equb_id', $equb['id'])->where('status', 'paid')->sum('amount');
                    $totalEqubAmount = Equb::select('total_amount')->where('id', $equb['id'])->pluck('total_amount')->first();
                    $remainingPayment =  $totalEqubAmount - $totalPpayment;
                    if ($remainingPayment > 0) {
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
                return response()->json($equbsArray);
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
    public function getPaidEqubs($id)
    {
        try {
            $userData = Auth::user();
            if (
                $userData['role'] == "admin" || $userData['role'] == "equb_collector"
                || $userData['role'] == "member"
            ) {
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
            return response()->json($request);
            $userData = Auth::user();
            if (($userData['role'] == "admin") || ($userData['role'] == "equb_collector")) {
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
            if ($userData && ($userData['role'] == "admin") || ($userData['role'] == "equb_collector")) {
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
                // $address = [
                //     'City' => $city,
                //     'SubCity' => $subcity,
                //     'Woreda' => $woreda,
                //     'House_Number' => $housenumber,
                //     'Specific_Location' => $location
                // ];
                $updated = [
                    'full_name' => $name,
                    'phone' => $phone,
                    'gender' => $gender,
                    'email' => $email,
                    'city' => $city,
                    'subcity' => $subcity,
                    'woreda' => $woreda,
                    'house_number' => $housenumber,
                    'specific_location' => $location,
                    // 'address' => json_encode($address),

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
            return response()->json([
                'code' => 500,
                'message' => 'Unable to process your request, Please try again!',
                "error" => $ex
            ]);
        }
    }
    public function rate($id, Request $request)
    {
        // dd($id);
        try {
            $userData = Auth::user();
            if ($userData && ($userData['role'] == "admin") || ($userData['role'] == "equb_collector")) {
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
            if (($userData['role'] == "admin") || ($userData['role'] == "equb_collector")) {
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
                "error" => $ex
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
                if ($member_count > 0) {
                    return response()->json([
                        'code' => 403,
                        'message' => 'Phone already exists',
                    ]);
                }
            }

            // Check if the email already exists
            if (!empty($email)) {
                $member_count = Member::where('email', $email)->count();
                if ($member_count > 0) {
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
                'status' => "Pending",
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
                'gender' => $gender
            ];
            $user = $this->userRepository->createUser($user);
            $memberRole = Role::firstOrCreate(['name' => 'Member']);
            
            $user->assignRole($memberRole->name);
            if ($create && $user) {
                return response()->json([
                    'code' => 200,
                    'message' => "Member has registered successfully!",
                    'data' => $create
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
    public function getProfilePicture($userId)
    {
        $user = Member::findOrFail($userId);
        $path = 'public/' . $user->profile_photo_path;
        // dd($path);
        if (Storage::exists($path)) {
            $file = Storage::get($path);
            $type = Storage::mimeType($path);

            return response($file, 200)->header('Content-Type', $type);
        } else {
            return null;
        }
        return response()->json([
            'code' => 400,
            'message' => 'Image not found',
            "error" => "Image not found"
        ]);
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
    public function updateProfile($id, Request $request)
    {
        // dd($request);
        try {
            $userData = Auth::user();
            if (($userData['role'] == "member")) {
                $name = $request->input('full_name');
                $phone = $request->input('phone');
                $gender = $request->input('gender');
                $city = $request->input('city');
                $subcity = $request->input('subcity');
                $woreda = $request->input('woreda');
                $housenumber = $request->input('housenumber');
                $location = $request->input('location');
                $email = $request->input('email');
                // $address = [
                //     'City' => $city,
                //     'SubCity' => $subcity,
                //     'Woreda' => $woreda,
                //     'House_Number' => $housenumber,
                //     'Specific_Location' => $location
                // ];
                // dd($address);
                $updated = [
                    'full_name' => $name,
                    'phone' => $phone,
                    'gender' => $gender,
                    'email' => $email,
                    'city' => $city,
                    'subcity' => $subcity,
                    'woreda' => $woreda,
                    'house_number' => $housenumber,
                    'specific_location' => $location,
                    // 'address' => json_encode($address),
                ];
                if ($request->file('profile_picture')) {
                    $image = $request->file('profile_picture');
                    $imageName = time() . '.' . $image->getClientOriginalExtension();
                    $image->storeAs('public/profile_pictures', $imageName);
                    $updated['profile_photo_path'] = 'profile_pictures/' . $imageName;
                }
                if (!empty($phone)) {
                    $member_count = Member::where('phone', $phone)->where('id', '!=', $id)->count();
                    if ($member_count > 0) {
                        return response()->json([
                            'code' => 403,
                            'message' => 'Phone already exist',
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
                $updateUser = $this->userRepository->updateUser($userData->id, $updateUser);
                if ($updated && $updateUser) {
                    return response()->json([
                        'code' => 200,
                        'message' => 'Profile has been updated successfully!',
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
            return response()->json([
                'code' => 500,
                'message' => 'Unable to process your request, Please try again!',
                "error" => $ex
            ]);
        }
    }
}
