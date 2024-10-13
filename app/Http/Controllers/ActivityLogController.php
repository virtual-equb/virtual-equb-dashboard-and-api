<?php

namespace App\Http\Controllers;

use App\Repositories\ActivityLog\IActivityLogRepository;
use Illuminate\Support\Facades\Auth;
use Exception;
use Illuminate\Support\Facades\Session;

class ActivityLogController extends Controller
{
    private $activityLogRepository;
    private $title;
    private $limit;
    public function __construct(IActivityLogRepository $activityLogRepository,)
    {
        $this->activityLogRepository = $activityLogRepository;
        $this->title = "Virtual Equb - Activity Log";
        $this->limit = 10;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->middleware('auth');
        try {
            $userData = Auth::user();
            if ($userData && ($userData['role'] == "admin" || $userData['role'] == "general_manager" || $userData['role'] == "operation_manager" || $userData['role'] == "assistant")) {
                $data['title'] = $this->title;
                $data['countedTypes']  = $this->activityLogRepository->countByType();
                return view('admin/activityLog.activityLogs', $data);
            } else {
                return view('auth/login');
            }
        } catch (Exception $ex) {
            $msg = "Unknown Error Occurred, Please try again!";
            $type = 'error';
            Session::flash($type, $msg);
            return back();
        }
    }
    public function paginateActivityLog($offsetVal, $pageNumberVal)
    {
        try {
            $offset = $offsetVal;
            $pageNumber = $pageNumberVal;
            $userData = Auth::user();
            if ($userData && ($userData['role'] == "admin" || $userData['role'] == "general_manager" || $userData['role'] == "operation_manager" || $userData['role'] == "assistant")) {
                $data['title'] = $this->title;
                $data['countedTypes']  = $this->activityLogRepository->paginateCountByType($offset);
                $data['totalTypes']  = $this->activityLogRepository->totalCountByType();
                $data['pageNumber'] = $pageNumber;
                $data['offset'] = $offset;
                $data['limit'] = $this->limit;
                // dd($data['totalTypes']);
                return view('admin/activityLog.activityLogsTable', $data);
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
    public function clearSearchEntry()
    {
        try {
            $offset = 0;
            $limit = $this->limit;
            $pageNumber = 1;
            $userData = Auth::user();
            if ($userData && ($userData['role'] == "admin" || $userData['role'] == "general_manager" || $userData['role'] == "operation_manager" || $userData['role'] == "it" || $userData['role'] == "finance" || $userData['role'] == "customer_service" || $userData['role'] == "assistant")) {
                $data['countedTypes']  = $this->activityLogRepository->paginateCountByType($offset);
                $data['totalTypes']  = $this->activityLogRepository->totalCountByType();
                $data['pageNumber'] = $pageNumber;
                $data['offset'] = $offset;
                $data['limit'] = $limit;
                $title = $this->title;
                return view('admin/activityLog.activityLogsTable', $data);
            }
        } catch (Exception $ex) {
            // dd($ex);
            $msg = "Unknown Error Occurred, Please try again!";
            $type = 'error';
            Session::flash($type, $msg);
            return back();
        }
    }
    public function logDetail($type, $searchInput = null)
    {
        try {
            $offset = 0;
            $limit = $this->limit;
            $pageNumber = 1;
            $userData = Auth::user();
            if ($userData && ($userData['role'] == "admin" || $userData['role'] == "general_manager" || $userData['role'] == "operation_manager" || $userData['role'] == "assistant")) {
                $data['title'] = $this->title;
                $data['activityLogs']  = $this->activityLogRepository->getAllActivityLog($type, $offset, $searchInput);
                $data['totalLoge'] = $this->activityLogRepository->countActivityLog($type, $searchInput);
                $data['offset'] = $offset;
                $data['limit'] = $limit;
                $data['pageNumber'] = $pageNumber;
                return view('admin/activityLog.logDetails', $data);
            } else {
                return view('auth/login');
            }
        } catch (Exception $ex) {
            $msg = "Unknown Error Occurred, Please try again!";
            $type = 'error';
            Session::flash($type, $msg);
            return back();
        }
    }
    public function logDetailPaginate($type, $offsetVal, $pageNumberVal, $searchInput = null)
    {
        try {
            $limit = $this->limit;
            $offset = $offsetVal;
            $pageNumber = $pageNumberVal;
            $userData = Auth::user();
            if ($userData && ($userData['role'] == "admin" || $userData['role'] == "general_manager" || $userData['role'] == "operation_manager" || $userData['role'] == "assistant")) {
                $data['title'] = $this->title;
                $data['activityLogs']  = $this->activityLogRepository->getAllActivityLog($type, $offset, $searchInput);
                $data['totalLoge'] = $this->activityLogRepository->countActivityLog($type, $searchInput);
                $data['offset'] = $offset;
                $data['limit'] = $limit;
                $data['pageNumber'] = $pageNumber;
                return view('admin/activityLog.logDetails', $data);
            } else {
                return view('auth/login');
            }
        } catch (Exception $ex) {
            $msg = "Unknown Error Occurred, Please try again!";
            $type = 'error';
            Session::flash($type, $msg);
            return back();
        }
    }
    public function searchActivity($type, $searchInput, $offset, $pageNumber = null)
    {
        try {
            $userData = Auth::user();
            if ($userData && ($userData['role'] == "admin" || $userData['role'] == "general_manager" || $userData['role'] == "operation_manager" || $userData['role'] == "it" || $userData['role'] == "customer_service")) {
                $data['offset'] = $offset;
                $limit = $this->limit;
                $data['limit'] = $limit;
                $data['totalLoge'] = $this->activityLogRepository->countActivity($searchInput, $type);
                if ($offset == 0) {
                    $data['pageNumber'] = 1;
                } else {
                    $data['pageNumber'] = $pageNumber;
                }
                $data['searchInput'] = $searchInput;
                $data['type'] = $type;
                $data['activityLogs'] = $this->activityLogRepository->searchActivity($offset, $searchInput, $type);
                // dd($data);
                // dd($data['totalLoge']);
                return view('admin/activityLog.searchActivityLogsTable', $data);
                // return view('admin/activityLog/searchActivityLogsTable', $data)->render();
            } elseif ($userData && ($userData['role'] == "equb_collector")) {
                $data['offset'] = $offset;
                $limit = $this->limit;
                $data['limit'] = $limit;
                $data['totalMember'] = $this->activityLogRepository->countActivity($searchInput, $type);
                if ($offset == 0) {
                    $data['pageNumber'] = 1;
                } else {
                    $data['pageNumber'] = $pageNumber;
                }
                $data['searchInput'] = $searchInput;
                $data['users'] = $this->activityLogRepository->searchActivity($offset, $searchInput, $type);
                return view('equbCollecter/activityLog.searchActivityLogsTable', $data);
                // return view('equbCollecter/activityLog/searchActivityLogsTable', $data)->render();
            }
        } catch (Exception $ex) {
            // dd($ex);
            $msg = "Unable to process your request, Please try again!";
            $type = 'error';
            Session::flash($type, $msg);
            return back();
        }
    }
}
