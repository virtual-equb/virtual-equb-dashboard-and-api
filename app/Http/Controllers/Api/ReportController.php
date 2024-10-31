<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\EqubResource;
use App\Http\Resources\Api\MemberResource;
use Exception;
use App\Repositories\Member\IMemberRepository;
use App\Repositories\Payment\IPaymentRepository;
use App\Repositories\EqubType\IEqubTypeRepository;
use App\Repositories\Equb\IEqubRepository;
use App\Repositories\EqubTaker\IEqubTakerRepository;
use App\Repositories\User\IUserRepository;
use Illuminate\Support\Facades\Auth;

/**
 * @group Reports
 */
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
        $this->middleware('auth:api');
        $this->memberRepository = $memberRepository;
        $this->paymentRepository = $paymentRepository;
        $this->equbTypeRepository = $equbTypeRepository;
        $this->equbRepository = $equbRepository;
        $this->equbTakerRepository = $equbTakerRepository;
        $this->userRepository = $userRepository;
    }
    public function memberFilter()
    {
        try {
            $userData = Auth::user();
            $roles = ['admin'];
            if ($userData && $userData->hasAnyRole($roles)) {
                $data['title'] = "Virtual Equb - Members Report";
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
     * Get Members between dates
     *
     * This api gets members based on date filters.
     *
     * @param dateFrom date required The date to start filter. Example: 01/01/2012
     * @param dateTo date required The date to end filter. Example: 01/01/2012
     *
     * @return JsonResponse
     */
    public function members($dateFrom, $dateTo)
    {
        try {
            $data['offset'] = 0;
            $offset = 0;
            $data['limit'] = 50;
            $data['pageNumber'] = 1;
            $userData = Auth::user();
            $roles = ['admin'];
            if ($userData && $userData->hasAnyRole($roles)) {
                $data['title'] = "Virtual Equb - Members Report";
                $data['totalMember'] = $this->memberRepository->getCountByDate($dateFrom, $dateTo);
                $data['members'] = $this->memberRepository->getByDate($dateFrom, $dateTo, $offset);
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
     * Get Members between dates for pagination
     *
     * This api gets members based on date filters for pagination.
     *
     * @param dateFrom date required The date to start filter. Example: 01/01/2012
     * @param dateTo date required The date to end filter. Example: 01/01/2012
     * @param offsetVal int required The date to end filter. Example: 0
     * @param pageNumberVal int required The date to end filter. Example: 2
     *
     * @return JsonResponse
     */
    public function paginateMembers($dateFrom, $dateTo, $offsetVal, $pageNumberVal)
    {
        try {
            $data['offset'] = $offsetVal;
            $offset = $offsetVal;
            $data['limit'] = 50;
            $data['pageNumber'] = $pageNumberVal;
            $userData = Auth::user();
            $roles = ['admin'];
            if ($userData && $userData->hasAnyRole($roles)) {
                $data['title'] = "Virtual Equb - Members Report";
                $data['totalMember'] = $this->memberRepository->getCountByDate($dateFrom, $dateTo);
                $data['members'] = $this->memberRepository->getByDate($dateFrom, $dateTo, $offset);
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
    public function equbTypeFilter()
    {
        try {
            $userData = Auth::user();
            $roles = ['admin'];
            if ($userData && $userData->hasAnyRole($roles)) {
                $data['title'] = "Virtual Equb - Equb Types Report";
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
     * Get equb types by date
     *
     * This api gets equb types based on date filters .
     *
     * @param dateFrom date required The date to start filter. Example: 01/01/2012
     * @param dateTo date required The date to end filter. Example: 01/01/2012
     *
     * @return JsonResponse
     */
    public function equbTypes($dateFrom, $dateTo)
    {
        try {
            $userData = Auth::user();
            $role = ['admin'];
            if ($userData && $userData->hasAnyRole($role)) {
                $data['title'] = "Virtual Equb - Equb Types Report";
                $data['equbTypes'] = $this->equbTypeRepository->getByDate($dateFrom, $dateTo);
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
     * Get all payments
     *
     * This api gets all payments.
     *
     * @return JsonResponse
     */
    public function paymentFilter()
    {
        try {
            $userData = Auth::user();
            $role = ['admin'];
            if ($userData && $userData->hasAnyRole($role)) {
                $data['title'] = "Virtual Equb - Payments Report";
                $data['members'] = $this->memberRepository->getAll();
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
     * Get payments between dates
     *
     * This api gets payments based on date filters and member id and equb id.
     *
     * @param dateFrom date required The date to start filter. Example: 01/01/2012
     * @param dateTo date required The date to end filter. Example: 01/01/2012
     * @param member_id int required The member id. Example: 1
     * @param equb_id int required The equb id. Example: 2
     *
     * @return JsonResponse
     */
    public function payments($dateFrom, $dateTo, $member_id, $equb_id)
    {
        try {
            $data['offset'] = 0;
            $offset = 0;
            $data['limit'] = 50;
            $data['pageNumber'] = 1;
            $userData = Auth::user();
            $role = ['admin'];
            if ($userData && $userData->hasAnyRole($role)) {
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
     * Get payments between dates for pagnation
     *
     * This api gets payments based on date filters and member id and equb id for pagnation.
     *
     * @param dateFrom date required The date to start filter. Example: 01/01/2012
     * @param dateTo date required The date to end filter. Example: 01/01/2012
     * @param member_id int required The member id. Example: 1
     * @param equb_id int required The equb id. Example: 2
     * @param offsetVal int required The date to end filter. Example: 0
     * @param pageNumberVal int required The date to end filter. Example: 2
     *
     * @return JsonResponse
     */
    public function paginatePayments($dateFrom, $dateTo, $member_id, $equb_id, $offsetVal, $pageNumberVal)
    {
        try {
            $data['offset'] = $offsetVal;
            $offset = $offsetVal;
            $data['limit'] = 50;
            $data['pageNumber'] = $pageNumberVal;
            $userData = Auth::user();
            $role = ['admin'];
            if ($userData && $userData->hasAnyRole($role)) {
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
     * Get equb collectors
     *
     * This api gets all equbcollectors.
     *
     * @return JsonResponse
     */
    public function collectedByFilter()
    {
        try {
            $userData = Auth::user();
            $role = ['admin'];
            if ($userData && $userData->hasAnyRole($role)) {
                $data['title'] = "Virtual Equb - Collected By Report";
                $data['collecters'] = $this->userRepository->getCollecters();
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
     * Get payments between dates and with collector
     *
     * This api gets payments based on date filters and member id and equb id and with collector.
     *
     * @param dateFrom date required The date to start filter. Example: 01/01/2012
     * @param dateTo date required The date to end filter. Example: 01/01/2012
     * @param collecter int required The member id. Example: 1
     *
     * @return JsonResponse
     */
    public function collectedBys($dateFrom, $dateTo, $collecter)
    {
        try {
            $data['offset'] = 0;
            $offset = 0;
            $data['limit'] = 50;
            $data['pageNumber'] = 1;
            $userData = Auth::user();
            $role = ['admin'];
            if ($userData && $userData->hasAnyRole($role)) {
                $data['title'] = "Virtual Equb - Collected by Report";
                if ($collecter == "all") {
                    $data['totalPayments'] = $this->paymentRepository->getCountCollectedBys($dateFrom, $dateTo);
                    $data['collecters'] = $this->paymentRepository->getByDate($dateFrom, $dateTo, $offset);
                } else {
                    $data['totalPayments'] = $this->paymentRepository->getCountCollectedBysWithCollecter($dateFrom, $dateTo, $collecter);
                    $data['collecters'] = $this->paymentRepository->getCollectedByUser($dateFrom, $dateTo, $collecter, $offset);
                }
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
     * Get payments between dates and with collector
     *
     * This api gets payments based on date filters and member id and equb id and with collector.
     *
     * @param dateFrom date required The date to start filter. Example: 01/01/2012
     * @param dateTo date required The date to end filter. Example: 01/01/2012
     * @param collecter int required The member id. Example: 1
     * @param offsetVal int required The date to end filter. Example: 0
     * @param pageNumberVal int required The date to end filter. Example: 2
     *
     * @return JsonResponse
     */
    public function paginateCllectedBys($dateFrom, $dateTo, $collecter, $offsetVal, $pageNumberVal)
    {
        try {
            $data['offset'] = $offsetVal;
            $offset = $offsetVal;
            $data['limit'] = 50;
            $data['pageNumber'] = $pageNumberVal;
            $userData = Auth::user();
            $role = ['admin'];
            if ($userData && $userData->hasAnyRole($role)) {
                $data['title'] = "Virtual Equb - Collected by Report";
                if ($collecter == "all") {
                    $data['totalPayments'] = $this->paymentRepository->getCountCollectedBys($dateFrom, $dateTo);
                    $data['collecters'] = $this->paymentRepository->getByDate($dateFrom, $dateTo, $offset);
                } else {
                    $data['totalPayments'] = $this->paymentRepository->getCountCollectedBysWithCollecter($dateFrom, $dateTo, $collecter);
                    $data['collecters'] = $this->paymentRepository->getCollectedByUser($dateFrom, $dateTo, $collecter, $offset);
                }
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
     * Get all members
     *
     * This api gets all members.
     *
     * @return JsonResponse
     */
    public function equbFilter()
    {
        try {
            $userData = Auth::user();
            $role = ['admin'];
            if ($userData && $userData->hasAnyRole($role)) {
                $data['title'] = "Virtual Equb - Equbs Report";
                $data['members'] = $this->memberRepository->getAll();
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
     * Get equbs between dates
     *
     * This api gets equbs based on date filters.
     *
     * @param dateFrom date required The date to start filter. Example: 01/01/2012
     * @param dateTo date required The date to end filter. Example: 01/01/2012
     *
     * @return JsonResponse
     */
    public function equbs($dateFrom, $dateTo)
    {
        try {
            $data['offset'] = 0;
            $offset = 0;
            $data['limit'] = 50;
            $data['pageNumber'] = 1;
            $userData = Auth::user();
            $role = ['admin'];
            if ($userData && $userData->hasAnyRole($role)) {

                $data['title'] = "Virtual Equb - Equbs Report";
                $data['totalEqub'] = $this->equbRepository->getCountByDate($dateFrom, $dateTo);
                $data['equbs'] = $this->equbRepository->getByDate($dateFrom, $dateTo, $offset);
                return response()->json([
                    'totalEqub' => $data['totalEqub'],
                    'equbs' => EqubResource::collection($data['equbs'])
                ]);
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
     * Get equbs between dates for pagination
     *
     * This api gets equbs based on date filters for pagination.
     *
     * @param dateFrom date required The date to start filter. Example: 01/01/2012
     * @param dateTo date required The date to end filter. Example: 01/01/2012
     * @param offsetVal int required The date to end filter. Example: 0
     * @param pageNumberVal int required The date to end filter. Example: 2
     *
     * @return JsonResponse
     */
    public function paginateEqubs($dateFrom, $dateTo, $offsetVal, $pageNumberVal)
    {
        try {
            $data['offset'] = $offsetVal;
            $offset = $offsetVal;
            $data['limit'] = 50;
            $data['pageNumber'] = $pageNumberVal;
            $userData = Auth::user();
            $role = ['admin'];
            if ($userData && $userData->hasAnyRole($role)) {

                $data['title'] = "Virtual Equb - Equbs Report";
                $data['totalEqub'] = $this->equbRepository->getCountByDate($dateFrom, $dateTo);
                $data['equbs'] = $this->equbRepository->getByDate($dateFrom, $dateTo, $offset);
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
     * Get all lotteries
     *
     * This api gets all lotteries.
     *
     * @return JsonResponse
     */
    public function lotteryFilter()
    {
        try {
            $userData = Auth::user();
            $role = ['admin'];
            if ($userData && $userData->hasAnyRole($role)) {
                $data['members'] = $this->memberRepository->getAll();
                $data['title'] = "Virtual Equb - Lotterys Report";
                return response()->json([
                    'title' => $data['title'],
                    'members' => MemberResource::collection($data['members'])
                ]);
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
     * Get equbs between dates
     *
     * This api gets equbs based on date filters.
     *
     * @param dateFrom date required The date to start filter. Example: 01/01/2012
     * @param dateTo date required The date to end filter. Example: 01/01/2012
     * @param member_id int required The member id. Example: 1
     * @param equb_type_id int required The id of the equb type. Example: 2
     *
     * @return JsonResponse
     */
    public function lotterys($dateFrom, $dateTo, $member_id, $equb_type_id)
    {
        try {
            $data['offset'] = 0;
            $offset = 0;
            $data['limit'] = 50;
            $data['pageNumber'] = 1;
            $userData = Auth::user();
            $role = ['admin'];
            if ($userData && $userData->hasAnyRole($role)) {
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
     * Get equbs between dates for pagination
     *
     * This api gets equbs based on date filters for pagination.
     *
     * @param dateFrom date required The date to start filter. Example: 01/01/2012
     * @param dateTo date required The date to end filter. Example: 01/01/2012
     * @param member_id int required The member id. Example: 1
     * @param equb_type_id int required The id of the equb type. Example: 2
     * @param offsetVal int required The date to end filter. Example: 0
     * @param pageNumberVal int required The date to end filter. Example: 2
     *
     * @return JsonResponse
     */
    public function paginateLotterys($dateFrom, $dateTo, $member_id, $equb_type_id, $offsetVal, $pageNumberVal)
    {
        try {
            $data['offset'] = $offsetVal;
            $offset = $offsetVal;
            $data['limit'] = 50;
            $data['pageNumber'] = $pageNumberVal;
            $userData = Auth::user();
            $role = ['admin'];
            if ($userData && $userData->hasAnyRole($role)) {
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
     * Get all unpaid lotteries
     *
     * This api gets all unpaid lotteries.
     *
     * @return JsonResponse
     */
    public function unPaidLotteryFilter()
    {
        try {
            $userData = Auth::user();
            $role = ['admin'];
            if ($userData && $userData->hasAnyRole($role)) {
                $data['title'] = "Virtual Equb - Lotterys Report";
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
     * Get all unpaid lotteries of a member
     *
     * This api gets all unpaid lotteries of a member.
     *
     * @return JsonResponse
     */
    public function unPaidLotterys()
    {
        try {
            $data['offset'] = 0;
            $offset = 0;
            $data['limit'] = 50;
            $data['pageNumber'] = 1;
            $userData = Auth::user();
            $role = ['admin'];
            if ($userData && $userData->hasAnyRole($role)) {
                $data['title'] = "Virtual Equb - UnPaid Lotterys Report";
                $member_id = $this->equbTakerRepository->getMemberId();
                $data['totalLotterys'] = $this->equbRepository->getUnPaidLotteryCount($member_id);
                $data['lotterys'] = $this->equbRepository->getUnPaidLottery($member_id, $offset);
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
     * Get all unpaid lotteries
     *
     * This api gets all unpaid lotteries.
     *
     * @param offsetVal int required The date to end filter. Example: 0
     * @param pageNumberVal int required The date to end filter. Example: 1
     *
     * @return JsonResponse
     */
    public function paginateUnPaidLotterys($offsetVal, $pageNumberVal)
    {
        try {
            $data['offset'] = $offsetVal;
            $offset = $offsetVal;
            $data['limit'] = 50;
            $data['pageNumber'] = $pageNumberVal;
            $userData = Auth::user();
            $role = ['admin'];
            if ($userData && $userData->hasAnyRole($role)) {
                $data['title'] = "Virtual Equb - UnPaid Lotterys Report";
                $member_id = $this->equbTakerRepository->getMemberId();
                $data['totalLotterys'] = $this->equbRepository->getUnPaidLotteryCount($member_id);
                $data['lotterys'] = $this->equbRepository->getUnPaidLottery($member_id, $offset);
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
}
