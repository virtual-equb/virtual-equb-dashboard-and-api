<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\User;
use App\Models\Member;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\LotteryWinner;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;
use App\Repositories\ISubCityRepository;
use App\Repositories\City\ICityRepository;
use App\Repositories\Equb\IEqubRepository;
use App\Repositories\User\IUserRepository;

use App\Repositories\Member\IMemberRepository;
use App\Repositories\SubCity\SubCityRepository;
use App\Repositories\Payment\IPaymentRepository;
use App\Repositories\EqubType\IEqubTypeRepository;
use App\Repositories\ActivityLog\IActivityLogRepository;
use App\Repositories\SubCity\ISubCityRepository as SubCityISubCityRepository;

class MemberController extends Controller
{
    private $activityLogRepository;
    private $memberRepository;
    private $paymentRepository;
    private $equbTypeRepository;
    private $equbRepository;
    private $userRepository;
    private $title;
    private $cityRepository;
    private $subCityRepository;
    public function __construct(
        IMemberRepository $memberRepository,
        IPaymentRepository $paymentRepository,
        IEqubTypeRepository $equbTypeRepository,
        IUserRepository $userRepository,
        IEqubRepository $equbRepository,
        IActivityLogRepository $activityLogRepository,
        ICityRepository $cityRepository,
        SubCityISubCityRepository $subCityRepository
    ) {
        $this->activityLogRepository = $activityLogRepository;
        $this->memberRepository = $memberRepository;
        $this->paymentRepository = $paymentRepository;
        $this->equbTypeRepository = $equbTypeRepository;
        $this->equbRepository = $equbRepository;
        $this->userRepository = $userRepository;
        $this->cityRepository = $cityRepository;
        $this->subCityRepository = $subCityRepository;
        $this->title = "Virtual Equb - Member";

        // Guard Permission
        // $this->middleware('permission_check_logout:update member', ['only' => ['update', 'updateStatus', 'updatePendingStatus', 'rate', 'edit']]);
        // $this->middleware('permission_check_logout:delete member', ['only' => ['destroy']]);
        // $this->middleware('permission_check_logout:view member', ['only' => ['index', 'show', 'indexPending', 'searchPendingMember', 'member', 'searchMember', 'searchEqub', 'searchStatus']]);
        // $this->middleware('permission_check_logout:create member', ['only' => ['store', 'create', 'register']]);
    }
    public function clearSearchEntry()
    {
        try {
            $offset = 0;
            $limit = 10;
            $pageNumber = 1;
            $userData = Auth::user();
            // if ($userData && ($userData['role'] == "admin" || $userData['role'] == "general_manager" || $userData['role'] == "operation_manager" || $userData['role'] == "it" || $userData['role'] == "finance" || $userData['role'] == "customer_service" || $userData['role'] == "assistant")) {
                $totalMember = $this->memberRepository->getMember();
                $members = $this->memberRepository->getAllByPaginate($offset, $limit);
                $equbTypes = $this->equbTypeRepository->getActive();
                $equbs = $this->equbRepository->getAll();
                $payments = $this->paymentRepository->getAllPayment();
                $title = $this->title;
                return view('admin/member.memberTable', compact('title', 'members', 'equbTypes', 'equbs', 'payments', 'pageNumber', 'offset', 'limit', 'totalMember'));
            // }
        } catch (Exception $ex) {
            $msg = "Unknown Error Occurred, Please try again!";
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
            // if ($userData && ($userData['role'] == "admin" || $userData['role'] == "general_manager" || $userData['role'] == "operation_manager" || $userData['role'] == "it" || $userData['role'] == "finance" || $userData['role'] == "customer_service" || $userData['role'] == "assistant")) {
                $totalMember = $this->memberRepository->getPendingMembers();
                $members = $this->memberRepository->getAllPendingByPaginate($offset, $limit);
                $equbTypes = $this->equbTypeRepository->getActive();
                $equbs = $this->equbRepository->getAll();
                $payments = $this->paymentRepository->getAllPayment();
                $title = $this->title;
                return view('admin/member.pendingMemberTable', compact('title', 'members', 'equbTypes', 'equbs', 'payments', 'pageNumber', 'offset', 'limit', 'totalMember'));
            // }
        } catch (Exception $ex) {
            $msg = "Unknown Error Occurred, Please try again!";
            $type = 'error';
            Session::flash($type, $msg);
            return back();
        }
    }
    public function index1()
    {
        try {
            $offset = 0;
            $limit = 50;
            $pageNumber = 1;
            $userData = Auth::user();
            $adminRoles = ['admin', 'general_manager', 'operation_manager', 'it', 'finance', 'call_center', 'assistant', 'collector and finance', 'Customer service supervisor', 'Legal Affair Officers', 'Marketing Manager'];
            $memberRole = ['member'];
            $collector = ['equb_collector'];
            $title = $this->title;
            $equbTypes = $this->equbTypeRepository->getActive();
            $cities = $this->cityRepository->getAll();

            if ($userData && $userData->hasAnyRole($adminRoles) || $userData && $userData->hasAnyRole($collector)) {
                // $totalMember = $this->memberRepository->getMember();
                $members = $this->memberRepository->getAllByPaginate($offset, $limit);
                // $equbTypes = $this->equbTypeRepository->getActive();
                // $equbs = $this->equbRepository->getAll();
                $equbs = $this->equbRepository->getAllWithPagination($limit);
                $payments = $this->paymentRepository->getRecentPayments($limit);
                // $payments = $this->paymentRepository->getAllPayment();
                // $title = $this->title;
                // $cities = $this->cityRepository->getAll();
                //dd($members);
                return view('admin/member.memberList', compact('title', 'equbTypes', 'members', 'equbs', 'payments','cities'));
            } elseif ($userData && $userData->hasAnyRole($memberRole)) {
                // $members = $this->memberRepository->getByPhone($userData['phone_number']);
                // $equbTypes = $this->equbTypeRepository->getActive();
                // $equbs = $this->equbRepository->getAll();
                // $payments = $this->paymentRepository->getAllPayment();
                // $members = $this->memberRepository->getByPhone($userData['phone_number']);
                // $cities = $this->cityRepository->getAll();
                $members = $this->memberRepository->getByPhone($userData->phone_number);
                $equbs = $this->equbRepository->getByMember($userData->id);
                $payments = $this->paymentRepository->getPaymentsByMember($userData->id);
                return view('member/member.memberList', compact('title', 'members', 'equbTypes', 'equbs', 'payments','cities'));
            } else {
                return view('auth/login');
            }
        } catch (Exception $ex) {
            $msg = "Unknown Error Occurred, Please try again! " . $ex->getMessage();
            $type = 'error';
            Session::flash($type, $msg);
            return back();
        }
    }
    public function index()
    {
        try {
            $offset = 0;
            $limit = 50;
            $pageNumber = 1;
            $userData = Auth::user();
            $adminRoles = [
               'admin', 'general_manager', 'operation_manager', 'it', 'finance', 
                'call_center', 'assistant', 'collector and finance', 
                'Customer service supervisor', 'Legal Affair Officers', 'Marketing Manager' 
            ];
            $memberRole = ['member'];
            $collector = ['equb_collector'];
            $title = $this->title;
            $equbTypes = $this->equbTypeRepository->getActive();
            $cities = $this->cityRepository->getAll();

            if ($userData && $userData->hasAnyRole($adminRoles) || $userData && $userData->hasAnyRole($collector)) {
                $members = $this->memberRepository->getAllByPaginate($offset, $limit);
                $equbs = $this->equbRepository->getAllWithPagination($limit);
                $payments = $this->paymentRepository->getRecentPayments($limit);

                return view('admin/member.memberList', compact('title', 'members', 'equbTypes', 'equbs', 'payments', 'cities'));
            }

            if ($userData && $userData->hasAnyRole($memberRole)) {
                $members = $this->memberRepository->getByPhone($userData->phone_number);
                $equbs = $this->equbRepository->getByMember($userData->id);
                $payments = $this->paymentRepository->getPaymentsByMember($userData->id);

                return view('member/member.memberList', compact('title', 'members', 'equbTypes', 'equbs', 'payments', 'cities'));
            }

            return view('auth/login');

        } catch (Exception $ex) {
            Session::flash('error', "Unknown Error Occurred, Please try again! " . $ex->getMessage());
            return back();
        }
    }
    public function index3()
    {
        try {
            $userData = Auth::user();
            if (!$userData) {
                return view('auth/login');
            }

            // Define roles
            $adminRoles = [
                'admin', 'general_manager', 'operation_manager', 'it', 'finance', 
                'call_center', 'assistant', 'collector and finance', 
                'Customer service supervisor', 'Legal Affair Officers', 'Marketing Manager' 
            ];
            $collector = ['equb_collector'];
            $memberRole = ['member'];

            // Cache frequently used data
            $title = Cache::remember('title', now()->addMinutes(60), function () {
                return $this->title;
            });

            $equbTypes = Cache::remember('active_equb_types', now()->addMinutes(60), function () {
                return $this->equbTypeRepository->getActive();
            });

            $cities = Cache::remember('all_cities', now()->addMinutes(60), function () {
                return $this->cityRepository->getAll();
            });

            if ($userData->hasAnyRole($adminRoles) || $userData->hasAnyRole($collector)) {
                // Use paginate() instead of offset/limit
                $members = $this->memberRepository->getAllByPaginate(0, 50);
                $equbs = $this->equbRepository->getAllWithPagination(50);
                $payments = $this->paymentRepository->getRecentPayments(50);

                return view('admin/member.memberList', compact('title', 'members', 'equbTypes', 'equbs', 'payments', 'cities'));
            }

            if ($userData->hasAnyRole($memberRole)) {
                $members = $this->memberRepository->getByPhone($userData->phone_number);
                $equbs = $this->equbRepository->getByMember($userData->id);
                $payments = $this->paymentRepository->getPaymentsByMember($userData->id);

                return view('member/member.memberList', compact('title', 'members', 'equbTypes', 'equbs', 'payments', 'cities'));
            }

            return view('auth/login');

        } catch (Exception $ex) {
            Log::error("Error in index method: " . $ex->getMessage());
            Session::flash('error', "Unknown Error Occurred, Please try again! " . $ex->getMessage());
            return back();
        }
    }

    public function countPending()
    {
        try {
            $totalMember = $this->memberRepository->getPendingMembers();
            return $totalMember;
        } catch (Exception $ex) {
            $msg = "Unknown Error Occurred, Please try again!";
            $type = 'error';
            Session::flash($type, $msg);
            return back();
        }
    }
    public function getPending()
    {
        try {
            $members = $this->memberRepository->getAllPendingMembersNotification();
            return $members;
        } catch (Exception $ex) {
            $msg = "Unknown Error Occurred, Please try again!";
            $type = 'error';
            Session::flash($type, $msg);
            return back();
        }
    }
    public function indexPending()
    {
        try {
            $offset = 0;
            $limit = 50;
            $pageNumber = 1;
            $userData = Auth::user();
            // if ($userData && ($userData['role'] == "admin" || $userData['role'] == "general_manager" || $userData['role'] == "operation_manager" || $userData['role'] == "it" || $userData['role'] == "finance" || $userData['role'] == "customer_service" || $userData['role'] == "assistant")) {
                $totalMember = $this->memberRepository->getPendingMembers();
                $members = $this->memberRepository->getAllPendingByPaginate($offset, $limit);
                $equbTypes = $this->equbTypeRepository->getActive();
                $equbs = $this->equbRepository->getAll();
                $payments = $this->paymentRepository->getAllPayment();
                $title = $this->title;
               //  dd($members);
                $title = $this->title;
                $cities = $this->cityRepository->getAll();
                return view('admin/member.pendingMemberList', compact('title', 'equbTypes', 'members', 'equbs', 'payments','cities'));
            // } elseif ($userData && ($userData['role'] == "equb_collector")) {
                $totalMember = $this->memberRepository->getPendingMembers();
                $members = $this->memberRepository->getAllPendingByPaginate($offset, $limit);
                $equbTypes = $this->equbTypeRepository->getActive();
                $equbs = $this->equbRepository->getAll();
                $payments = $this->paymentRepository->getAllPayment();
                $title = $this->title;
                return view('equbCollecter/member.pendingMemberList', compact('title', 'equbTypes', 'equbs', 'payments'));
            // } elseif ($userData && ($userData['role'] == "member")) {
                $members = $this->memberRepository->getByPhone($userData['phone_number']);
                $equbTypes = $this->equbTypeRepository->getActive();
                $equbs = $this->equbRepository->getAll();
                $payments = $this->paymentRepository->getAllPayment();
                $title = $this->title;
                return view('member/member.pendingMemberList', compact('title', 'members', 'equbTypes', 'equbs', 'payments'));
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
    public function member($offsetVal, $pageNumberVal)
    {
        try {
            $limit = 10;
            $offset = $offsetVal;
            $pageNumber = $pageNumberVal;
            $userData = Auth::user();
            // if ($userData && ($userData['role'] == "admin" || $userData['role'] == "general_manager" || $userData['role'] == "operation_manager" || $userData['role'] == "it" || $userData['role'] == "finance" || $userData['role'] == "customer_service" || $userData['role'] == "assistant")) {
                $totalMember = $this->memberRepository->getMember();
                $members = $this->memberRepository->getAllByPaginate($offset, $limit);
                $equbTypes = $this->equbTypeRepository->getActive();
                $equbs = $this->equbRepository->getAll();
                $payments = $this->paymentRepository->getAllPayment();
                $title = $this->title;
                return view('admin/member.memberTable', compact('title', 'equbTypes', 'members', 'equbs', 'payments', 'pageNumber', 'offset', 'limit', 'totalMember'));
            // } elseif ($userData && ($userData['role'] == "equb_collector")) {
                $totalMember = $this->memberRepository->getMember();
                $members = $this->memberRepository->getAllByPaginate($offset, $limit);
                $equbTypes = $this->equbTypeRepository->getActive();
                $equbs = $this->equbRepository->getAll();
                $payments = $this->paymentRepository->getAllPayment();
                $title = $this->title;
                return view('equbCollecter/member.memberTable', compact('title', 'members', 'equbTypes', 'equbs', 'payments', 'pageNumber', 'offset', 'limit', 'totalMember'));
            // } elseif ($userData && ($userData['role'] == "member")) {
                $members = $this->memberRepository->getByPhone($userData['phone_number']);
                $equbTypes = $this->equbTypeRepository->getActive();
                $equbs = $this->equbRepository->getAll();
                $payments = $this->paymentRepository->getAllPayment();
                $title = $this->title;
                return view('member/member.memberList', compact('title', 'members', 'equbTypes', 'equbs', 'payments'));
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
    public function pendingMember($offsetVal, $pageNumberVal)
    {
        try {
            $limit = 10;
            $offset = $offsetVal;
            $pageNumber = $pageNumberVal;
            $userData = Auth::user();
            // if ($userData && ($userData['role'] == "admin" || $userData['role'] == "general_manager" || $userData['role'] == "operation_manager" || $userData['role'] == "it" || $userData['role'] == "finance" || $userData['role'] == "customer_service" || $userData['role'] == "assistant")) {
                $totalMember = $this->memberRepository->getMember();
                $members = $this->memberRepository->getAllPendingMembers($offset);
                $equbTypes = $this->equbTypeRepository->getActive();
                $equbs = $this->equbRepository->getAll();
                $payments = $this->paymentRepository->getAllPayment();
                $title = $this->title;
                return view('admin/member.memberTable', compact('title', 'equbTypes', 'members', 'equbs', 'payments', 'pageNumber', 'offset', 'limit', 'totalMember'));
            // } elseif ($userData && ($userData['role'] == "equb_collector")) {
                $totalMember = $this->memberRepository->getMember();
                $members = $this->memberRepository->getAllByPaginate($offset, $limit);
                $equbTypes = $this->equbTypeRepository->getActive();
                $equbs = $this->equbRepository->getAll();
                $payments = $this->paymentRepository->getAllPayment();
                $title = $this->title;
                return view('equbCollecter/member.memberTable', compact('title', 'members', 'equbTypes', 'equbs', 'payments', 'pageNumber', 'offset', 'limit', 'totalMember'));
            // } elseif ($userData && ($userData['role'] == "member")) {
                $members = $this->memberRepository->getByPhone($userData['phone_number']);
                $equbTypes = $this->equbTypeRepository->getActive();
                $equbs = $this->equbRepository->getAll();
                $payments = $this->paymentRepository->getAllPayment();
                $title = $this->title;
                return view('member/member.memberList', compact('title', 'members', 'equbTypes', 'equbs', 'payments'));
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
            $msg = "Unknown Error Occurred, Please try again!";
            $type = 'error';
            Session::flash($type, $msg);
            return back();
        }
    }
    public function getEqubs($id)
    {
        try {
            return  $this->memberRepository->getEqubs($id);
        } catch (Exception $ex) {
            $msg = "Unknown Error Occurred, Please try again!";
            $type = 'error';
            Session::flash($type, $msg);
            return back();
        }
    }
    public function getAllEqubs()
    {
        try {
            return  $this->equbTypeRepository->getAll();
        } catch (Exception $ex) {
            $msg = "Unknown Error Occurred, Please try again!";
            $type = 'error';
            Session::flash($type, $msg);
            return back();
        }
    }
    public function create()
    {
        try {
            $userData = Auth::user();
            // if ($userData && ($userData['role'] == "admin" || $userData['role'] == "general_manager" || $userData['role'] == "operation_manager" || $userData['role'] == "it" || $userData['role'] == "customer_service")) {
                $data['payment'] = $this->paymentRepository->getAll();
                $data['equb'] = $this->equbRepository->getAll();
                return view('admin/member.memberList', $data);
            // } elseif (($userData && $userData['role'] == "equb_collector")) {
                $data['payment'] = $this->paymentRepository->getAll();
                $data['equb'] = $this->equbRepository->getAll();
                return view('equbCollecter/member.memberList', $data);
            // } elseif (($userData && $userData['role'] == "member")) {
                $data['payment'] = $this->paymentRepository->getAll();
                $data['equb'] = $this->equbRepository->getAll();
                return view('member/member.memberList', $data);
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
    public function searchMember($searchInput, $offset, $pageNumber = null)
    {
        try {
            $userData = Auth::user();
            // if ($userData && ($userData['role'] == "admin" || $userData['role'] == "general_manager" || $userData['role'] == "operation_manager" || $userData['role'] == "it" || $userData['role'] == "customer_service")) {
                $data['offset'] = $offset;
                $limit = 10;
                $data['limit'] = $limit;
                $data['totalMember'] = $this->memberRepository->countMember($searchInput);
                if ($offset == 0) {
                    $data['pageNumber'] = 1;
                } else {
                    $data['pageNumber'] = $pageNumber;
                }
                $data['searchInput'] = $searchInput;
                $data['members'] = $this->memberRepository->searchMember($offset, $searchInput);
                return view('admin/member/searchMembers', $data)->render();
            // } elseif ($userData && ($userData['role'] == "equb_collector")) {
                $data['offset'] = $offset;
                $limit = 10;
                $data['limit'] = $limit;
                $data['totalMember'] = $this->memberRepository->countMember($searchInput);
                if ($offset == 0) {
                    $data['pageNumber'] = 1;
                } else {
                    $data['pageNumber'] = $pageNumber;
                }
                $data['searchInput'] = $searchInput;
                $data['members'] = $this->memberRepository->searchMember($offset, $searchInput);
                return view('equbCollecter/member/searchMembers', $data)->render();
            // }
        } catch (Exception $ex) {
            $msg = "Unable to process your request, Please try again!";
            $type = 'error';
            Session::flash($type, $msg);
            return back();
        }
    }
    public function searchPendingMember($searchInput, $offset, $pageNumber = null)
    {
        try {
            $userData = Auth::user();
            // if ($userData && ($userData['role'] == "admin" || $userData['role'] == "general_manager" || $userData['role'] == "operation_manager" || $userData['role'] == "it" || $userData['role'] == "customer_service")) {
                $data['offset'] = $offset;
                $limit = 50;
                $data['limit'] = $limit;
                $data['totalMember'] = $this->memberRepository->countPendingMember($searchInput);
                if ($offset == 0) {
                    $data['pageNumber'] = 1;
                } else {
                    $data['pageNumber'] = $pageNumber;
                }
                $data['searchInput'] = $searchInput;
                $data['members'] = $this->memberRepository->searchPendingMember($offset, $searchInput);
                return view('admin/member/searchPendingEqubMembers', $data)->render();
            // } elseif ($userData && ($userData['role'] == "equb_collector")) {
                $data['offset'] = $offset;
                $limit = 50;
                $data['limit'] = $limit;
                $data['totalMember'] = $this->memberRepository->countPendingMember($searchInput);
                if ($offset == 0) {
                    $data['pageNumber'] = 1;
                } else {
                    $data['pageNumber'] = $pageNumber;
                }
                $data['searchInput'] = $searchInput;
                $data['members'] = $this->memberRepository->searchPendingMember($offset, $searchInput);
                return view('equbCollecter/member/searchPendingEqubMembers', $data)->render();
            // }
        } catch (Exception $ex) {
            $msg = "Unable to process your request, Please try again!";
            $type = 'error';
            Session::flash($type, $msg);
            return back();
        }
    }
    public function searchEqub($searchInput, $offset, $pageNumber = null)
    {
        try {
            $userData = Auth::user();
            // if ($userData && ($userData['role'] == "admin" || $userData['role'] == "general_manager" || $userData['role'] == "operation_manager" || $userData['role'] == "it" || $userData['role'] == "customer_service")) {
                $data['offset'] = $offset;
                $limit = 10;
                $data['limit'] = $limit;
                $data['totalMember'] = $this->memberRepository->countEqubMember($searchInput);
                if ($offset == 0) {
                    $data['pageNumber'] = 1;
                } else {
                    $data['pageNumber'] = $pageNumber;
                }
                $data['searchInput'] = $searchInput;
                $data['members'] = $this->memberRepository->searchEqub($offset, $searchInput);
                // dd($data['members']);
                return view('admin/member/searchEqubMembers', $data)->render();
            // } elseif ($userData && ($userData['role'] == "equb_collector")) {
                $data['offset'] = $offset;
                $limit = 10;
                $data['limit'] = $limit;
                $data['totalMember'] = $this->memberRepository->countEqubMember($searchInput);
                if ($offset == 0) {
                    $data['pageNumber'] = 1;
                } else {
                    $data['pageNumber'] = $pageNumber;
                }
                $data['searchInput'] = $searchInput;
                $data['members'] = $this->memberRepository->searchEqub($offset, $searchInput);
                return view('equbCollecter/member/searchEqubMembers', $data)->render();
            // }
        } catch (Exception $ex) {
            $msg = "Unable to process your request, Please try again!";
            $type = 'error';
            Session::flash($type, $msg);
            return back();
        }
    }
    public function searchPendingEqub($searchInput, $offset, $pageNumber = null)
    {
        try {
            $userData = Auth::user();
            // if ($userData && ($userData['role'] == "admin" || $userData['role'] == "general_manager" || $userData['role'] == "operation_manager" || $userData['role'] == "it" || $userData['role'] == "customer_service")) {
                $data['offset'] = $offset;
                $limit = 50;
                $data['limit'] = $limit;
                $data['totalMember'] = $this->memberRepository->countPendingEqubMember($searchInput);
                if ($offset == 0) {
                    $data['pageNumber'] = 1;
                } else {
                    $data['pageNumber'] = $pageNumber;
                }
                $data['searchInput'] = $searchInput;
                $data['members'] = $this->memberRepository->searchPendingEqub($offset, $searchInput);
                // dd($data['members']);
                return view('admin/member/searchPendingEqubMembers', $data)->render();
            // } elseif ($userData && ($userData['role'] == "equb_collector")) {
                $data['offset'] = $offset;
                $limit = 50;
                $data['limit'] = $limit;
                $data['totalMember'] = $this->memberRepository->countEqubMember($searchInput);
                if ($offset == 0) {
                    $data['pageNumber'] = 1;
                } else {
                    $data['pageNumber'] = $pageNumber;
                }
                $data['searchInput'] = $searchInput;
                $data['members'] = $this->memberRepository->searchEqub($offset, $searchInput);
                return view('equbCollecter/member/searchPendingEqubMembers', $data)->render();
            // }
        } catch (Exception $ex) {
            $msg = "Unable to process your request, Please try again!";
            $type = 'error';
            Session::flash($type, $msg);
            return back();
        }
    }
    public function searchStatus($searchInput, $offset, $pageNumber = null)
    {
        try {
            $userData = Auth::user();
            // if ($userData && ($userData['role'] == "admin" || $userData['role'] == "general_manager" || $userData['role'] == "operation_manager" || $userData['role'] == "it" || $userData['role'] == "customer_service")) {
                $data['offset'] = $offset;
                $limit = 10;
                $data['limit'] = $limit;
                $data['totalMember'] = $this->memberRepository->countStatusMember($searchInput);
                if ($offset == 0) {
                    $data['pageNumber'] = 1;
                } else {
                    $data['pageNumber'] = $pageNumber;
                }
                $data['searchInput'] = $searchInput;
                $data['members'] = $this->memberRepository->searchStatus($offset, $searchInput);
                // dd($data['totalMember']);
                return view('admin/member/searchStatusMembers', $data)->render();
            // } elseif ($userData && ($userData['role'] == "equb_collector")) {
                $data['offset'] = $offset;
                $limit = 10;
                $data['limit'] = $limit;
                $data['totalMember'] = $this->memberRepository->countStatusMember($searchInput);
                if ($offset == 0) {
                    $data['pageNumber'] = 1;
                } else {
                    $data['pageNumber'] = $pageNumber;
                }
                $data['searchInput'] = $searchInput;
                $data['members'] = $this->memberRepository->searchStatus($offset, $searchInput);
                return view('equbCollecter/member/searchStatusMembers', $data)->render();
            // }
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
                $this->validate(
                    $request,
                    [
                        'full_name' => 'required',
                        'phone' => 'required',
                        'gender' => 'required',
                        'city' => 'required',
                        'location' => 'required',
                    ]
                );
                // dd($request);
                $fullName = $request->input('full_name');
                $phone = $request->input('phone');
                $gender = $request->input('gender');
                $city = $request->input('city');
                $subcity = $request->input('subcity');
                $woreda = $request->input('woreda');
                $housenumber = $request->input('housenumber');
                $location = $request->input('location');
                $email = $request->input('email');
                $password = rand(100000, 999999);
                $this->memberRepository->checkPhone($phone);

                // Check if the phone number already exists
                if (!empty($phone) && (Member::where('phone', $phone)->exists() || User::where('phone_number', $phone)->exists())) {
                    return redirect()->back()->with('error', 'A user already exists with this phone number.');
                }

                // Check if the email already exists
                if (!empty($email) && (Member::where('email', $email)->exists() || User::where('email', $email)->exists())) {
                    return redirect()->back()->with('error', 'A user already exists with this email address.');
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
                    // 'address' => json_encode($address),
                ];
                $create = $this->memberRepository->create($memberData);
                // dd($memberData);
                $user = [
                    'name' => $fullName,
                    'email' => $email,
                    'password' => Hash::make($password),
                    'phone_number' => $phone,
                    'gender' => $gender,
                ];
                $user = $this->userRepository->createUser($user);
                // Find or create the "member" role
                // $memberRole = Role::firstOrCreate(['name' => 'Member']);
                $memberRoleAPI = Role::firstOrCreate(['name' => 'member', 'guard_name' => 'api']);
                $memberRoleWEB = Role::firstOrCreate(['name' => 'member', 'guard_name' => 'web']);
                $user->assignRole($memberRoleWEB);
                $user->assignRole($memberRoleAPI);

                // Assign the role to the user
                $user->assignRole($memberRoleWEB);
                $roleName = $user->getRoleNames()->first();
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
                        $shortcode = config('key.SHORT_CODE');
                        $message = "Welcome to Virtual Equb! You have registered succesfully. Use your phone: " . $request->phone . " and password: " . $password . " to log in" . " For further information please call " . $shortcode;
                        $this->sendSms($request->phone, $message);
                    } catch (Exception $ex) {
                        return redirect()->back()->with('error', 'Failed to send SMS');
                    };
                    $msg = "Member has been registered successfully!";
                    $type = 'success';
                    Session::flash($type, $msg);
                    return redirect('/member');
                } else {
                    $msg = "Unknown Error Occurred, Please try again!";
                    $type = 'error';
                    Session::flash($type, $msg);
                    redirect('/member');
                }
        } catch (Exception $ex) {
            // dd($ex);
            $msg = "Unknown Error Occurred, Please try again!";
            $type = 'error';
            Session::flash($type, $msg);
            return back();
        }
    }
    public function register(Request $request)
    {
        try {
            $this->validate(
                $request,
                [
                    'full_name' => 'required',
                    'phone' => 'required',
                    'gender' => 'required',
                    'city' => 'required',
                    'woreda' => 'required',
                    'housenumber' => 'required',
                    'location' => 'required',
                ]
            );
            // dd($request);
            $fullName = $request->input('full_name');
            $phone = $request->input('phone');
            $gender = $request->input('gender');
            $city = $request->input('city');
            $subcity = $request->input('subcity');
            $woreda = $request->input('woreda');
            $housenumber = $request->input('housenumber');
            $location = $request->input('location');
            $email = $request->input('email');
            $password = rand(100000, 999999);
            // $formated_name = str_replace(' ', '', $fullName);
            // $email = $formated_name . "@virtualequb.com";
            $this->memberRepository->checkPhone($phone);
            $address = [
                'City' => $city,
                'SubCity' => $subcity,
                'Woreda' => $woreda,
                'House_Number' => $housenumber,
                'Specific_Location' => $location
            ];
            $memberData = [
                'full_name' => $fullName,
                'phone' => $phone,
                'gender' => $gender,
                'email' => $email,
                'address' => json_encode($address),
            ];
            if ($request->file('profile_picture')) {
                $image = $request->file('profile_picture');
                $imageName = time() . '.' . $image->getClientOriginalExtension();
                $image->storeAs('public/profile_pictures', $imageName);
                $memberData['profile_photo_path'] = 'profile_pictures/' . $imageName;
            }
            $create = $this->memberRepository->create($memberData);
            // dd($memberData);
            $user = [
                'name' => $fullName,
                'email' => $email,
                'password' => Hash::make($password),
                'phone_number' => $phone,
                'gender' => $gender,
                'role' => "member",
            ];
            $user = $this->userRepository->createUser($user);
            if ($create && $user) {
                $activityLog = [
                    'type' => 'members',
                    'type_id' => $create->id,
                    'action' => 'created',
                    'user_id' => $user->id,
                    'username' => $user->name,
                    'role' => $user->role,
                ];
                $this->activityLogRepository->createActivityLog($activityLog);
                $msg = "Member have registered successfully!Password is " . $password . " and your username is " . $email;
                $type = 'success';
                Session::flash($type, $msg);
                return redirect('/member');
            } else {
                $msg = "Unknown Error Occurred, Please try again!";
                $type = 'error';
                Session::flash($type, $msg);
                redirect('/member');
            }
        } catch (Exception $ex) {
            // dd($ex);
            $msg = "Unknown Error Occurred, Please try again!";
            $type = 'error';
            Session::flash($type, $msg);
            return back();
        }
    }
    // public function show($id)
    // {
    //     try {
    //         $userData = Auth::user();
    //         $Adminroles = ['admin', 'general_manager', 'operation_manager', 'it', 'call_center', 'finance'];
    //         $collector = ['equb_collector'];
    //         $member = ['member'];
    //         if ($userData && $userData->hasAnyRole($Adminroles)) {
    //             $data['member'] = $this->memberRepository->getByIdNested($id);
    //             $data['equbs'] = $data['member']->equbs()->paginate(10); // 
    //             $data['data'] = $this->memberRepository->getByIdNested($id)->equbs->pluck('lottery_date')->first();
    //             return view('admin/member.memberDetails', $data);
    //         } elseif ($userData && $userData->hasAnyRole($collector)) {
    //             $totalPayment = $this->paymentRepository->getTotalPaid($id);
    //             $data['member'] = $this->memberRepository->getByIdNested($id);
    //             $data['data'] = $this->memberRepository->getByIdNested($id)->equbs->pluck('lottery_date')->first();
    //             return view('equbCollecter/member.memberDetails', $data);
    //         } elseif ($userData && $userData->hasAnyRole($member)) {
    //             $data['member'] = $this->memberRepository->getByIdNested($id);
    //             $data['data'] = $this->memberRepository->getByIdNested($id)->equbs->pluck('lottery_date')->first();
    //             return view('member/member.memberDetails', $data);
    //         } else {
    //             return view('auth/login');
    //         }
    //     } catch (Exception $ex) {
    //         $msg = "Unable to process your request, Please try again!" . $ex->getMessage();
    //         $type = 'error';
    //         Session::flash($type, $msg);
    //         return back();
    //     }
    // }
    public function show1($id)
    {
        try {
            // Get the authenticated user
            $userData = Auth::user();
            // Define roles
            $adminRoles = ['admin', 'general_manager', 'operation_manager', 'it', 'call_center', 'finance', 'assistant', 'collector and finance', 'Customer service supervisor', 'Legal Affair Officers', 'Marketing Manager'];
            $collectorRoles = ['equb_collector'];
            $memberRoles = ['member'];
            // Check if the user is authenticated and their role
            if ($userData && $userData->hasAnyRole($adminRoles)) {
                $data['member'] = $this->memberRepository->getByIdNested($id);

                // Ensure member exists
                if (!$data['member']) {
                    throw new Exception("Member not found with ID: $id");
                }

                // Get equbs paginated
                $data['equbs'] = $data['member']->equbs()->paginate(10);

                // Get the first lottery date
                $data['data'] = $data['member']->equbs->pluck('lottery_date')->first();

                return view('admin/member.memberDetails', $data);
            } elseif ($userData && $userData->hasAnyRole($collectorRoles)) {
                $totalPayment = $this->paymentRepository->getTotalPaid($id);

                $data['member'] = $this->memberRepository->getByIdNested($id);

                // Ensure member exists
                if (!$data['member']) {
                    throw new Exception("Member not found with ID: $id");
                }

                // Get the first lottery date
                $data['data'] = $data['member']->equbs->pluck('lottery_date')->first();

                return view('equbCollecter/member.memberDetails', $data);
            } elseif ($userData && $userData->hasAnyRole($memberRoles)) {
                $data['member'] = $this->memberRepository->getByIdNested($id);

                // Ensure member exists
                if (!$data['member']) {
                    throw new Exception("Member not found with ID: $id");
                }

                // Get the first lottery date
                $data['data'] = $data['member']->equbs->pluck('lottery_date')->first();

                return view('member/member.memberDetails', $data);
            } else {
                return view('auth/login');
            }
        } catch (Exception $ex) {
            // Log the error for debugging
            \Log::error('Error in show method: ' . $ex->getMessage());
            \Log::error($ex->getTraceAsString());

            // Provide user feedback
            $msg = "Unable to process your request, Please try again!";
            $type = 'error';
            Session::flash($type, $msg);

            return back();
        }
    }

    public function show($id) {
        try {
            // Get the authenticated user
            $userData = Auth::user();
            if (!$userData) {
                return view('auth/login');
            }

            // Define roles
            $adminRoles = ['admin', 'general_manager', 'operation_manager', 'it', 'call_center', 'finance', 'assistant', 'collector and finance', 'Customer service supervisor', 'Legal Affair Officers', 'Marketing Manager'];
            $collectorRoles = ['equb_collector'];
            $memberRoles = ['member'];

            // Retrieve member once to avoid multiple database queries
            $member = $this->memberRepository->getByIdNested($id);

            // Ensure member exists
            if (!$member) {
                Session::flash('error', "Member not found with ID: $id");
                return back();
            }

            // Get equbs and first lottery date
            $equbs = $member->equbs()->paginate(10);
            $lotteryDate = $equbs->first()->lottery_date ?? null;

            // Assign common data
            $data = [
                'member' => $member,
                'equbs' => $equbs,
                'date' => $lotteryDate
            ];
            // Determine role and return correct view
            if ($userData->hasAnyRole($adminRoles)) {
                return view('admin/member.memberDetails', $data);
            }
            if ($userData->hasAnyRole($collectorRoles)) {
                return view('equbCollecter/member.memberDetails', $data);
            }
            if ($userData->hasAnyRole($memberRoles)) {
                return view('member/member.memberDetails', $data);
            }

            // Default case (No matching role)
            return view('auth/login');

        } catch (Exception $ex) {
            // Log the error for debugging
            \Log::error('Error in show method: ' . $ex->getMessage());
            \Log::error($ex->getTraceAsString());

            // Provide user feedback
            $msg = "Unable to process your request, Please try again!";
            $type = 'error';
            Session::flash($type, $msg);

            return back(); 
        }
    }


    // public function updateStatus($id, Request $request)
    // {
    //     try {
    //         $userData = Auth::user();
    //             $status = $this->memberRepository->getStatusById($id)->status;
    //             $member_phone = $this->memberRepository->getPhone($id);
    //             $member_phone = $member_phone->phone;
    //             $user_id = $this->userRepository->getUserId($member_phone);
    //             $user_id = $user_id->id;
    //             if ($status == "Deactive") {
    //                 $status = "Active";
    //                 $userStatus = 1;
    //             } else {
    //                 $status = "Deactive";
    //                 $userStatus = 0;
    //             }
    //             $updated = [
    //                 'status' => $status,
    //             ];
    //             $updateUser = [
    //                 'enabled' => $userStatus,
    //             ];
    //             $updated = $this->memberRepository->update($id, $updated);
    //             $updateUser = $this->userRepository->updateUser($user_id, $updateUser);
    //             if ($updated && $updateUser) {
    //                 if ($status == "Deactive") {
    //                     $updateEqubStatus = [
    //                         'status' => 'Deactive'
    //                     ];
    //                     $updated = $this->equbRepository->updateEqubStatus($id, $updateEqubStatus);
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
    //                 try {
    //                     $shortcode = config('key.SHORT_CODE');
    //                     $message = $status == "Deactivated" ? "Sorry! Your Virtual Equb account has been $status. For further information please call " . $shortcode : "Congratulations! Your Virtual Equb account has been $status. For further information please call " . $shortcode;
    //                     $this->sendSms($member_phone, $message);
    //                 } catch (Exception $ex) {
    //                     return redirect()->back()->with('error', 'Failed to send SMS');
    //                 };
    //                 $msg = "Status has been updated successfully!";
    //                 $type = 'success';
    //                 Session::flash($type, $msg);
    //                 return back();
    //             } else {
    //                 $msg = "Unknown error occurred, Please try again!";
    //                 $type = 'error';
    //                 Session::flash($type, $msg);
    //                 return back();
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

            // Get current member status and phone
            $member = $this->memberRepository->getStatusById($id);
            $member_phone = $this->memberRepository->getPhone($id)->phone;
            $user_id = $this->userRepository->getUserId($member_phone)->id;

            // Determine new status
            $newStatus = $member->status === "Active" ? "Deactive" : "Active";
            $userStatus = $newStatus === "Active" ? 1 : 0;

            // Update member and user status
            $updated = $this->memberRepository->update($id, ['status' => $newStatus]);
            $updateUser = $this->userRepository->updateUser($user_id, ['enabled' => $userStatus]);

            if ($updated && $updateUser) {
                // Handle equb status if deactivated
                if ($newStatus === "Deactive") {
                    $this->equbRepository->updateEqubStatus($id, ['status' => 'Deactive']);
                }

                // Log activity
                $this->activityLogRepository->createActivityLog([
                    'type' => 'members',
                    'type_id' => $id,
                    'action' => $newStatus,
                    'user_id' => $userData->id,
                    'username' => $userData->name,
                    'role' => $userData->role,
                ]);

                // Send SMS notification
                try {
                    $shortcode = config('key.SHORT_CODE');
                    $message = $newStatus === "Deactive" 
                        ? "Sorry! Your Virtual Equb account has been Deactivated. For further information please call {$shortcode}"
                        : "Congratulations! Your Virtual Equb account has been Activated. For further information please call {$shortcode}";
                    $this->sendSms($member_phone, $message);
                } catch (Exception $ex) {
                    return redirect()->back()->with('error', 'Failed to send SMS' . $ex->getMessage());
                }

                // Success message
                $msg = "Status has been updated to {$newStatus} successfully!";
                $type = 'success';
                Session::flash($type, $msg);
                return back();
            } else {
                $msg = "Unknown error occurred, Please try again!";
                $type = 'error';
                Session::flash($type, $msg);
                return back();
            }

        } catch (Exception $ex) {
            $msg = "Unable to process your request, Please try again!" . $ex->getMessage();
            $type = 'error';
            Session::flash($type, $msg);
            return back();
        }
    }
    
    public function updatePendingStatus($id, $status, Request $request)
    {
        // dd($status);
        try {
            $userData = Auth::user();
            // if ($userData && ($userData['role'] == "admin") || ($userData['role'] == "equb_collector")) {
                // $status = $this->memberRepository->getStatusById($id)->status;
                $member_phone = $this->memberRepository->getPhone($id);
                $member_phone = $member_phone->phone;
                $user_id = $this->userRepository->getUserId($member_phone);
                $user_id = $user_id->id;
                if ($status == "Active") {
                    $status = "Active";
                    $userStatus = 1;
                } else {
                    $status = "Deactive";
                    $userStatus = 0;
                }
                $updated = [
                    'status' => $status,
                ];
                $updateUser = [
                    'enabled' => $userStatus,
                ];
                $updated = $this->memberRepository->update($id, $updated);
                $updateUser = $this->userRepository->updateUser($user_id, $updateUser);
                if ($updated && $updateUser) {
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
                    try {
                        $shortcode = config('key.SHORT_CODE');
                        $message = $status == "Deactivated" ? "Sorry! Your Virtual Equb account has been $status. For further information please call " . $shortcode : "Congratulations! Your Virtual Equb account has been $status. For further information please call " . $shortcode;
                        $this->sendSms($member_phone, $message);
                    } catch (Exception $ex) {
                        return redirect()->back()->with('error', 'Failed to send SMS');
                    };
                    $msg = "Status has been updated successfully!";
                    $type = 'success';
                    Session::flash($type, $msg);
                    return back();
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
            $msg = "Unable to process your request, Please try again!";
            $type = 'error';
            Session::flash($type, $msg);
            return back();
        }
    }
    public function edit(Member $member)
    {
        try {
            $userData = Auth::user();
            // if ($userData && ($userData['role'] == "admin" || $userData['role'] == "general_manager" || $userData['role'] == "operation_manager" || $userData['role'] == "it" || $userData['role'] == "customer_service")) {
                $data['member'] = $this->memberRepository->getById($member);
                return view('admin/member/updateMember', $data);
            // } elseif ($userData && ($userData['role'] == "equb_collector")) {
                $data['member'] = $this->memberRepository->getById($member);
                return view('equbCollecter/member/updateMember', $data);
            // } elseif ($userData && ($userData['role'] == "member")) {
                $data['member'] = $this->memberRepository->getById($member);
                return view('member/member/updateMember', $data);
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
    public function update($id, Request $request)
    {
        try {
            $userData = Auth::user();
    
            // Log incoming request data for debugging
            \Log::info('Incoming request data:', $request->all());
    
            // Validate request
            $this->validate(
                $request,
                [
                    'full_name' => 'required',
                    'phone' => 'required',
                    'gender' => 'required',
                    'city' => 'required',
                    'update_location' => 'required',
                    'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                ]
            );
    
            // Gather input data
            $name = $request->input('full_name');
            $phone = $request->input('phone');
            $gender = $request->input('gender');
            $city = $request->input('city');
            $subcity = $request->input('subcity');
            $woreda = $request->input('update_woreda');
            $housenumber = $request->input('update_housenumber');
            $location = $request->input('update_location');
            $email = $request->input('email');
    
            // Get member's phone and user ID
            $member_phone = $this->memberRepository->getPhone($id);
            $member_phone = $member_phone->phone;
            $user_id = $this->userRepository->getUserId($member_phone);
            $user_id = $user_id->id;
    
            // Prepare data for updating
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
            ];
    
            // Handle profile picture update
            if ($request->hasFile('profile_picture')) {
                $path = $request->file('profile_picture')->store('profile_pictures', 'public');
                $updated['profile_photo_path'] = $path; // Store the new profile photo path
            }
    
            // Update member and user
            $updatedMember = $this->memberRepository->update($id, $updated);
            $updateUser = [
                'name' => $name,
                'phone_number' => $phone,
                'gender' => $gender,
                'email' => $email
            ];
            $updatedUser = $this->userRepository->updateUser($user_id, $updateUser);
    
            if ($updatedMember && $updatedUser) {
                $activityLog = [
                    'type' => 'members',
                    'type_id' => $id,
                    'action' => 'updated',
                    'user_id' => $userData->id,
                    'username' => $userData->name,
                    'role' => $userData->role,
                ];
                $this->activityLogRepository->createActivityLog($activityLog);
                $msg = "Member details have been updated successfully!";
                $type = 'success';
                Session::flash($type, $msg);
                return redirect('member/');
            } else {
                $msg = "Unknown error occurred, Please try again!";
                $type = 'error';
                Session::flash($type, $msg);
                return back();
            }
        } catch (Exception $ex) {
            $msg = "Unable to process your request, Please try again!";
            $type = 'error';
            Session::flash($type, $msg);
            return back();
        }
    }
    public function rate($id, Request $request)
    {
        // dd($id);
        try {
            $userData = Auth::user();
            // if ($userData && ($userData['role'] == "admin") || ($userData['role'] == "equb_collector")) {
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
                    $msg = "Member rating has been updated successfully!";
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
            $msg = "Unable to process your request, Please try again!";
            $type = 'error';
            Session::flash($type, $msg);
            return back();
        }
    }
    public function destroy($id)
    {
        try {
            $userData = Auth::user();
            // if ($userData && ($userData['role'] == "admin") || ($userData['role'] == "equb_collector")) {
                $member = $this->equbRepository->getMember($id);
                $lottery = LotteryWinner::where('member_id', $id)->first();
                if (!$member->isEmpty() || $lottery) {
                    $msg = "This member has history and can not be deleted";
                    $type = 'error';
                    Session::flash($type, $msg);
                    return redirect('member/');
                }
                $member_phone = $this->memberRepository->getPhone($id);
                $member_phone = $member_phone->phone;
                $user_id = $this->userRepository->getUserId($member_phone);
                if ($user_id) {
                    $user_id = $user_id->id;
                }
                $member = $this->memberRepository->getByIdToDelete($id);
                if ($member != null) {
                    $activity = $this->activityLogRepository->getByAdminId($user_id);
                    if ($activity) {
                        $deleteActivity = $this->activityLogRepository->forceDeleteLogByUserId($user_id);
                    }
                    $deletedUser = $this->userRepository->forceDeleteUser($user_id);
                    $deleted = $this->memberRepository->forceDelete($id);
                    if ($deleted && $deletedUser) {
                        $activityLog = [
                            'type' => 'members',
                            'type_id' => $id,
                            'action' => 'deleted',
                            'user_id' => $userData->id,
                            'username' => $userData->name,
                            'role' => $userData->role,
                        ];
                        $this->activityLogRepository->createActivityLog($activityLog);
                        $shortcode = config('key.SHORT_CODE');
                        $message = "Your account has been deleted" . ". For further information please call " . $shortcode;
                        $this->sendSms($member->phone, $message);
                        $msg = "Member has been deleted successfully!";
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
