<?php

namespace App\Http\Controllers\api;

use App\Models\RejectedDate;
use Illuminate\Http\Request;
use App\Repositories\RejectedDate\IRejectedDateRepository;
use App\Repositories\ActivityLog\IActivityLogRepository;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Exception;

/**
 * @group Rejected Dates
 */
class RejectedDateController extends Controller
{
    private $activityLogRepository;
    private $rejectedDateRepository;
    private $title;
    public function __construct(
        IRejectedDateRepository $rejectedDateRepository,
        IActivityLogRepository $activityLogRepository
    ) {
        $this->middleware('auth:api');
        $this->activityLogRepository = $activityLogRepository;
        $this->rejectedDateRepository = $rejectedDateRepository;
        $this->title = "Virtual Equb - Off Dates";

        // Guard Permission
        $this->middleware('api_permission_check:update rejected_date', ['only' => ['update', 'edit']]);
        $this->middleware('api_permission_check:delete rejected_date', ['only' => ['destroy']]);
        $this->middleware('api_permission_check:view rejected_date', ['only' => ['index', 'show', 'offDateCheck']]);
        $this->middleware('api_permission_check:create rejected_date', ['only' => ['store', 'create']]);
    }
    /**
     * Get all rejected dates
     *
     * This api returns all rejected dates.
     *
     * @return JsonResponse
     */
    public function index()
    {
        $this->middleware('auth');
        try {
                // $userData = Auth::user();
                $data['title'] = $this->title;
                $data['rejectedDate']  = $this->rejectedDateRepository->getAll();

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
     * Create rejected dates
     *
     * This api created rejected dates.
     *
     * @bodyParam rejected_date date required The date to be rejected. Example: 01/01/2012
     *
     * @return JsonResponse
     */
    public function store(Request $request)
    {

        try {
                $userData = Auth::user();
            
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
                    return response()->json([
                        'code' => 200,
                        'message' => 'Off date has been registered successfully!',
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
                'code' => 500,
                'message' => 'Unable to process your request, Please try again!',
                "error" => $ex
            ]);
        }
    }
    /**
     * Check rejected date
     *
     * This api checks if rejected date exists.
     *
     * @param rejected_date date required The date to be rejected. Example: 01/01/2012
     * @param id int required The id of the rejected date. Example: 2
     *
     * @return JsonResponse
     */
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
            return response()->json([
                'code' => 500,
                'message' => 'Unable to process your request, Please try again!',
                "error" => $ex
            ]);
        }
    }
    /**
     * Update rejected date
     *
     * This api updates rejected date.
     *
     * @bodyParam rejected_date date required The date to be rejected. Example: 01/01/2012
     * @bodyParam description string required The description of the rejected date. Example:  this is the rejected date
     * @param id int required The id of the rejected date. Example: 1
     *
     * @return JsonResponse
     */
    public function update($id, Request $request)
    {
        try {
                $userData = Auth::user();
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
                    return response()->json([
                        'code' => 200,
                        'message' => 'Off date updated successfully!',
                        'data' => $updated
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
                "error" => $ex
            ]);
        }
    }
    /**
     * Delete rejected date
     *
     * This api deletes a rejected date.
     *
     * @param id date required The id of the date. Example: 3
     *
     * @return JsonResponse
     */
    public function destroy($id)
    {
        try {
                $userData = Auth::user();
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
                        return response()->json([
                            'code' => 200,
                            'message' => 'Off date has been deleted successfully!'
                        ]);
                    } else {
                        return response()->json([
                            'code' => 400,
                            'message' => 'Unknown error occurred, Please try again!',
                            "error" => "Unknown error occurred, Please try again!"
                        ]);
                    }
                } else {
                    return false;
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
