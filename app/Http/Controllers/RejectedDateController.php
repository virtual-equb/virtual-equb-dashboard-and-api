<?php

namespace App\Http\Controllers;

use App\Models\RejectedDate;
use Illuminate\Http\Request;
use App\Repositories\RejectedDate\IRejectedDateRepository;
use App\Repositories\ActivityLog\IActivityLogRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Exception;

class RejectedDateController extends Controller
{
    private $activityLogRepository;
    private $rejectedDateRepository;
    private $title;
    public function __construct(
        IRejectedDateRepository $rejectedDateRepository,
        IActivityLogRepository $activityLogRepository
    ) {
        $this->activityLogRepository = $activityLogRepository;
        $this->rejectedDateRepository = $rejectedDateRepository;
        $this->title = "Virtual Equb - Off Date";

        // Permission Guard
        // $this->middleware('permission_check_logout:update rejected_date', ['only' => ['update', 'edit']]);
        // $this->middleware('permission_check_logout:delete rejected_date', ['only' => ['destroy']]);
        // $this->middleware('permission_check_logout:view rejected_date', ['only' => ['index', 'show', 'offDateCheck']]);
        // $this->middleware('permission_check_logout:create rejected_date', ['only' => ['store', 'create']]);
    }
    public function index()
    {
        try {
            $userData = Auth::user();
            // if ($userData && ($userData['role'] == "admin" || $userData['role'] == "general_manager" || $userData['role'] == "operation_manager" || $userData['role'] == "assistant")){
                $data['title'] = $this->title;
                $data['rejectedDate']  = $this->rejectedDateRepository->getAll();
                return view('admin/rejectedDate.rejectedDateList', $data);
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
    public function store(Request $request)
    {
        try {
            $userData = Auth::user();
            // if ($userData && ($userData['role'] == "admin" || $userData['role'] == "general_manager" || $userData['role'] == "operation_manager" || $userData['role'] == "assistant")){
                $this->validate($request, [
                    'rejected_date' => 'required',
                ]);
                $name = $request->input('rejected_date');
                $description = $request->input('description');
                $rejectedDateData = [
                    'rejected_date' => $name,
                    'description' => $description,
                ];
                $create = $this->rejectedDateRepository->create($rejectedDateData);
                if ($create) {
                    $activityLog = [
                        'type' => 'rejected_dates',
                        'type_id' => $create->id,
                        'action' => 'created',
                        'user_id' => $userData->id,
                        'username' => $userData->name,
                        'role' => $userData->role,
                    ];
                    $this->activityLogRepository->createActivityLog($activityLog);
                    $msg = "Off date has been registered successfully!";
                    $type = 'success';
                    Session::flash($type, $msg);
                    return redirect('/rejectedDate');
                } else {
                    $msg = "Unknown Error Occurred, Please try again!";
                    $type = 'error';
                    Session::flash($type, $msg);
                    redirect('/rejectedDate');
                }
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
    public function offDateCheck(Request $request)
    {
        try {
            if (!empty($request->rejected_date)) {
                $offDate = $request->off_date_id;
                $offDateCheck = $request->rejected_date;
                $dates_count = RejectedDate::where('rejected_date', $offDateCheck)->where('id', '!=', $offDate)->count();
                if ($dates_count > 0) {
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
    public function update($id, Request $request)
    {
        try {
            $userData = Auth::user();
            // if ($userData && ($userData['role'] == "admin" || $userData['role'] == "general_manager" || $userData['role'] == "operation_manager" || $userData['role'] == "assistant")){
                $date = $request->input('rejected_date');
                $description = $request->input('description');
                $updated = [
                    'rejected_date' => $date,
                    'description' => $description,
                ];
                $updated = $this->rejectedDateRepository->update($id, $updated);
                if ($updated) {
                    $activityLog = [
                        'type' => 'rejected_dates',
                        'type_id' => $id,
                        'action' => 'updated',
                        'user_id' => $userData->id,
                        'username' => $userData->name,
                        'role' => $userData->role,
                    ];
                    $this->activityLogRepository->createActivityLog($activityLog);
                    $msg = "Off date has been updated successfully!";
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
    public function destroy($id)
    {
        try {
            $userData = Auth::user();
            // if ($userData && ($userData['role'] == "admin" || $userData['role'] == "general_manager" || $userData['role'] == "operation_manager" || $userData['role'] == "assistant")){
                $rejectedDate = $this->rejectedDateRepository->getById($id);
                if ($rejectedDate != null) {
                    $deleted = $this->rejectedDateRepository->delete($id);
                    if ($deleted) {
                        $activityLog = [
                            'type' => 'rejected_dates',
                            'type_id' => $id,
                            'action' => 'deleted',
                            'user_id' => $userData->id,
                            'username' => $userData->name,
                            'role' => $userData->role,
                        ];
                        $this->activityLogRepository->createActivityLog($activityLog);
                        $msg = "Off date has been deleted successfully!";
                        $type = 'success';
                        Session::flash($type, $msg);
                        return redirect('rejectedDate/');
                    } else {
                        $msg = "Unknown Error Occurred, Please try again!";
                        $type = 'error';
                        Session::flash($type, $msg);
                        redirect('/rejectedDate');
                    }
                } else {
                    return false;
                }
            // } else {
            //     return view('auth/login');
            // }
        } catch (Exception $ex) {
            $msg = "Unable to process your request, Please try again!";
            $type = 'error';
            Session::flash($type, $msg);
            return $msg;
        }
    }
}
