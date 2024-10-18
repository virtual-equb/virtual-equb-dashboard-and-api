<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Repositories\ActivityLog\IActivityLogRepository;
use Illuminate\Support\Facades\Auth;
use Exception;

class ActivityLogController extends Controller
{
    private $activityLogRepository;
    private $title;
    public function __construct(IActivityLogRepository $activityLogRepository,)
    {
        $this->middleware('auth:api');
        $this->activityLogRepository = $activityLogRepository;
        $this->title = "Virtual Equb - Activity Log";
    }
    public function index()
    {
        $this->middleware('auth');
        try {
            $userData = Auth::user();
            if ($userData && ($userData['role'] == "admin" || $userData['role'] == "general_manager" || $userData['role'] == "operation_manager" || $userData['role'] == "it")){
                $data['title'] = $this->title;
                $data['countedTypes']  = $this->activityLogRepository->countByType();
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
    public function logDetail($type)
    {
        try {
            $offset = 0;
            $limit = 50;
            $pageNumber = 1;
            $userData = Auth::user();
            if ($userData && ($userData['role'] == "admin" || $userData['role'] == "general_manager" || $userData['role'] == "operation_manager" || $userData['role'] == "it")){
                $data['title'] = $this->title;
                $data['activityLogs']  = $this->activityLogRepository->getAllActivityLog($type, $offset);
                $data['totalLoge'] = $this->activityLogRepository->countActivityLog($type);
                $data['offset'] = $offset;
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
    public function logDetailPaginate($type, $offsetVal, $pageNumberVal)
    {
        try {
            $limit = 50;
            $offset = $offsetVal;
            $pageNumber = $pageNumberVal;
            $userData = Auth::user();
            if ($userData && ($userData['role'] == "admin" || $userData['role'] == "general_manager" || $userData['role'] == "operation_manager" || $userData['role'] == "it")){
                $data['title'] = $this->title;
                $data['activityLogs']  = $this->activityLogRepository->getAllActivityLog($type, $offset);
                $data['totalLoge'] = $this->activityLogRepository->countActivityLog($type);
                $data['offset'] = $offset;
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
}
