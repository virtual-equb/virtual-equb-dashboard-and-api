<?php

namespace App\Http\Controllers\api;

use Illuminate\Http\Request;
use App\Models\EqubType;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\EqubTypeResource;
use App\Models\LotteryWinner;
use Exception;
use App\Repositories\EqubType\IEqubTypeRepository;
use App\Repositories\Equb\IEqubRepository;
use App\Repositories\EqubTaker\IEqubTakerRepository;
use App\Repositories\Payment\IPaymentRepository;
use App\Repositories\Member\IMemberRepository;
use App\Repositories\ActivityLog\IActivityLogRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

/**
 * @group Equb Types
 */
class EqubTypeController extends Controller
{
    private $activityLogRepository;
    private $equbTypeRepository;
    private $equbRepository;
    private $title;
    private $equbTakerRepository;
    private $paymentRepository;
    private $memberRepository;
    public function __construct(
        IEqubTypeRepository $equbTypeRepository,
        IEqubRepository $equbRepository,
        IEqubTakerRepository $equbTakerRepository,
        IPaymentRepository $paymentRepository,
        IMemberRepository $memberRepository,
        IActivityLogRepository $activityLogRepository,
    ) {
        $this->middleware('auth:api')->except(['getIcon']);
        $this->activityLogRepository = $activityLogRepository;
        $this->equbRepository = $equbRepository;
        $this->equbTypeRepository = $equbTypeRepository;
        $this->equbTakerRepository = $equbTakerRepository;
        $this->paymentRepository = $paymentRepository;
        $this->memberRepository = $memberRepository;
        $this->title = "Virtual Equb - Equb Type";

        // Guard Permission
        $this->middleware('api_permission_check:update equb_type', ['only' => ['update', 'edit', 'nameEqubTypeCheckForUpdate', 'updateStatus']]);
        $this->middleware('api_permission_check:delete equb_type', ['only' => ['destroy']]);
        $this->middleware('api_permission_check:view equb_type', ['only' => ['index', 'show', 'create', 'getIcon']]);
        $this->middleware('api_permission_check:create equb_type', ['only' => ['store', 'create']]);

    }
    public function getIcon($equbTypeId)
    {
        $equbType = EqubType::findOrFail($equbTypeId);
        // dd($equbType);
        $path = 'public/' . $equbType->image;
        // dd($path);
        if (Storage::exists($path)) {
            $file = Storage::get($path);
            $type = Storage::mimeType($path);

            return response($file, 200)->header('Content-Type', $type);
        }
        return response()->json([
            'code' => 400,
            'message' => 'Image not found',
            "error" => "Image not found"
        ]);
    }
    /**
     * Get All Equb types
     *
     * This api returns all Equbs types.
     *
     * @return JsonResponse
     */
    public function index()
    {
        try {
            $userData = Auth::user();
            $equbTypes = EqubType::with('mainEqub')->get();
            
            $data['equbTypes'] = $this->equbTypeRepository->getAll();
            $data['deactiveEqubType'] = $this->equbTypeRepository->getDeactive();
            $data['activeEqubType'] = $this->equbTypeRepository->getActive();
            $data['title'] = $this->title;
        
            return response()->json([
                'code' => 200,
                'data' => EqubTypeResource::collection($equbTypes),
                'activeEqubType' => EqubTypeResource::collection($data['activeEqubType']),
                'deactiveEqubType' => EqubTypeResource::collection($data['deactiveEqubType'])
            ]);
        } catch (Exception $ex) {
            return response()->json([
                'code' => 500,
                'message' => 'Unable to process your request, Please try again!',
                "error" => $ex->getMessage()
            ], 400);
        }
    }
    /**
     * Get All deactivated Equb types
     *
     * This api returns all deactivated Equbs types.
     *
     * @return JsonResponse
     */
    public function dateInterval()
    {
        try {
            $data['deactiveEqubType']  = $this->equbTypeRepository->getDeactive();
        } catch (Exception $ex) {
            return response()->json([
                'code' => 500,
                'message' => 'Unable to process your request, Please try again!',
                "error" => $ex
            ]);
        }
    }
    /**
     * Get All Equb types with name and round
     *
     * This api returns all Equbs types with name and round.
     *
     * @bodyParam name string required The name of the equb type. Example: Weekly equb
     * @bodyParam round int required The round of the equb type. Example: 1
     *
     * @return JsonResponse
     */
    public function nameEqubTypeCheck(Request $request)
    {
        try {
            $name = $request->name;
            $round = $request->round;
            if (!empty($name)) {
                $name_count = EqubType::where('name', $name)->where('round', $round)->count();
                if ($name_count > 0) {
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
     * Get All Equb types with name and round for update
     *
     * This api returns all Equbs types with name and round.
     *
     * @bodyParam id int required The id of the equb type. Example: 3
     * @bodyParam name string required The name of the equb type. Example: Weekly equb
     * @bodyParam round int required The round of the equb type. Example: 1
     *
     * @return JsonResponse
     */
    public function nameEqubTypeCheckForUpdate(Request $request)
    {
        try {
            $name = $request->update_name;
            $round = $request->update_round;
            $did = $request->did;
            if (!empty($name)) {
                $name_count = EqubType::where('name', $name)->where('round', $round)->where('id', '!=', $did)->count();
                if ($name_count > 0) {
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
     * Equb type end date check
     *
     * This api returns boolean after hecking if end date has passed.
     *
     * @bodyParam end_date date required The end date of the equb type. Example: 01/01/2023
     *
     * @return JsonResponse
     */
    public function dateEqubTypeCheck(Request $request)
    {
        try {
            $date = $request->end_date;
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
     * Equb type end date check for update
     *
     * This api returns boolean after hecking if end date has passed.
     *
     * @bodyParam update_end_date date required The end date of the equb type. Example: 01/01/2023
     *
     * @return JsonResponse
     */
    public function dateEqubTypeCheckForUpdate(Request $request)
    {
        try {
            $date = $request->update_end_date;
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
                "error" => $ex->getMessage()
            ]);
        }
    }
    /**
     * Store equb type
     *
     * This api stores and creates equb type.
     *
     * @bodyParam name string required The name of the equb type. Example: Weekly equb
     * @bodyParam round int required The round of the equb type. Example: 1
     * @bodyParam rote int required The rote of the equb type. Example: Weekly
     * @bodyParam type int required The type of the equb type. Example: Automatic
     * @bodyParam lottery_date int required The lottery date of the equb type. Example: 01/01/2022
     *
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        // dd($request);
        try {
                $userData = Auth::user();

                $this->validate($request, [
                    'main_equb_id' => 'required',
                    'name' => 'required',
                    'round' => 'required',
                    'rote' => 'required',
                    'type' => 'required',
                ]);
                $name = $request->input('name');
                $main_equb_id = $request->input('main_equb_id');
                $round = $request->input('round');
                $rote = $request->input('rote');
                $type = $request->input('type');
                $remark = $request->input('remark');
                $lottery_date = $request->input('lottery_date');
                $start_date = $request->input('start_date');
                $end_date = $request->input('end_date');
                $quota = $request->input('quota');
                $equbTypeData = [
                    'name' => $name,
                    'round' => $round,
                    'rote' => $rote,
                    'type' => $type,
                    'remark' => $remark,
                    'lottery_date' => $lottery_date,
                    'start_date' => $start_date,
                    'end_date' => $end_date,
                    'quota' => $quota,
                    'main_equb_id' => $main_equb_id
                ];
                $name_count = EqubType::where('name', $name)->where('round', $round)->where('rote', $rote)->where('type', $type)->count();
                if ($name_count > 0) {
                    return response()->json([
                        'code' => 403,
                        'message' => 'Equb Type already exist!'
                    ]);
                }
                $create = $this->equbTypeRepository->create($equbTypeData);
                if ($create) {
                    $activityLog = [
                        'type' => 'equb_types',
                        'type_id' => $create->id,
                        'action' => 'created',
                        'user_id' => $userData->id,
                        'username' => $userData->name,
                        'role' => $userData->role,
                    ];
                    $this->activityLogRepository->createActivityLog($activityLog);
                    return response()->json([
                        'code' => 200,
                        'message' => 'Equb type has been registered successfully!',
                        'data' => new EqubTypeResource($create)
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
    public function show($id)
    {
        try {
            $userData = Auth::user();
            $data = EqubType::where('id', $id)->with('mainEqub')->first();
            return response([
                'data' => new EqubTypeResource($data)
            ]);
        } catch (Exception $ex) {
            return response()->json([
                'code' => 500,
                'message' => 'Unable to process your request, Please try again!',
                "error" => $ex
            ]);
        }
    }
    public function edit(EqubType $equbType)
    {
        try {
            $userData = Auth::user();

            $data['equbType'] = $this->equbTypeRepository->getById($equbType);
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
     * Update status of equb type
     *
     * This api updates the status of the equb type.
     *
     * @bodyParam id int required The id of the equb type. Example: Weekly equb
     *
     * @return JsonResponse
     */
    public function updateStatus($id, Request $request)
    {
        try {
            $userData = Auth::user();
            // if ($userData && in_array($userData['role'], ["admin", "general_manager", "operation_manager", "it"])) {
                $status = $this->equbTypeRepository->getStatusById($id)->status;
                if ($status == "Deactive") {
                    $status = "Active";
                } else {
                    $status = "Deactive";
                }
                $updated = [
                    'status' => $status,
                ];
                $updated = $this->equbTypeRepository->update($id, $updated);
                if ($updated) {
                    if ($status == "Deactive") {
                        $status = "Deactivated";
                    } else {
                        $status = "Activated";
                    }
                    $activityLog = [
                        'type' => 'equb_types',
                        'type_id' => $id,
                        'action' => $status,
                        'user_id' => $userData->id,
                        'username' => $userData->name,
                        'role' => $userData->role,
                    ];
                    $this->activityLogRepository->createActivityLog($activityLog);
                    return response()->json([
                        'code' => 200,
                        'message' => 'Status updated successfully!',
                        'data' => $updated
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
                "error" => $ex->getMessage()
            ]);
        }
    }
    /**
     * Get winner of equb type
     *
     * This api returns the winner of the draw
     *
     * @param id int required The id of the equb type. Example: 1
     *
     * @return JsonResponse
     */
    public function getWinner($id, Request $request)
    {
        try {
            $winner = LotteryWinner::where('equb_type_id', $id)->orderBy('created_at', 'desc')->first();
            return $winner ? response()->json([
                'code' => 200,
                "data" => [
                    "memberId" => $winner->member_id,
                    "memberName" => $winner->member_name
                ]
            ]) : response()->json([
                'code' => 200,
                "message" => "Winner has not been selected yet",
                "data" => []
            ]);
        } catch (Exception $ex) {
            return response()->json([
                'code' => 500,
                'message' => 'Unable to process your request, Please try again!',
                "error" => $ex
            ]);
        }
    }
    /**
     * Update of equb type
     *
     * This api updates the equb type.
     *
     * @bodyParam name string required The name of the equb type. Example: Weekly equb
     * @bodyParam round int required The round of the equb type. Example: Weekly equb
     *
     * @param id int required The id of the equb type. Example: 1
     *
     * @return JsonResponse
     */

    public function update($id, Request $request)
    {
        // dd($request);
        try {
            $userData = Auth::user();
            // if ($userData && in_array($userData['role'],  ["admin", "general_manager", "operation_manager", "it"])) {
                $validated = $this->validate($request, []);
                $name = $request->input('update_name');
                $round = $request->input('update_round');
                $rote = $request->input('update_rote');
                $type = $request->input('update_type');
                $remark = $request->input('update_remark');
                $lottery_date = $request->input('update_lottery_date');
                $quota = $request->input('update_quota');
                $start_date = $request->input('update_start_date');
                $end_date = $request->input('update_end_date');
                $updated = [
                    'name' => $name,
                    'round' => $round,
                    'rote' => $rote,
                    'type' => $type,
                    'remark' => $remark,
                    'lottery_date' => $lottery_date,
                    'start_date' => $start_date,
                    'end_date' => $end_date,
                    'quota' => $quota,
                ];
                $updated = $this->equbTypeRepository->update($id, $updated);
                if ($updated) {
                    $activityLog = [
                        'type' => 'equb_types',
                        'type_id' => $id,
                        'action' => 'updated',
                        'user_id' => $userData->id,
                        'username' => $userData->name,
                        'role' => $userData->role,
                    ];
                    $this->activityLogRepository->createActivityLog($activityLog);
                    return response()->json([
                        'code' => 200,
                        'message' => 'Equb type updated successfully!',
                        'data' => new EqubTypeResource($updated)
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
     * Delete equb type
     *
     * This api deletes the equb type.
     *
     * @param id int required The id of the equb type. Example: 1
     *
     * @return JsonResponse
     */
    public function destroy($id)
    {
        try {
            $userData = Auth::user();
                $equb = $this->equbRepository->getEqubType($id);
                if (!$equb->isEmpty()) {
                    return response()->json([
                        'code' => 400,
                        'message' => 'This equb type is being used, You cannot delete it!'
                    ]);
                }
                $equbType = $this->equbTypeRepository->getById($id);
                if ($equbType != null) {
                    $deleted = $this->equbTypeRepository->delete($id);
                    if ($deleted) {
                        $activityLog = [
                            'type' => 'equb_types',
                            'type_id' => $id,
                            'action' => 'deleted',
                            'user_id' => $userData->id,
                            'username' => $userData->name,
                            'role' => $userData->role,
                        ];
                        $this->activityLogRepository->createActivityLog($activityLog);
                        return response()->json([
                            'code' => 200,
                            'message' => 'Equb type has been deleted successfully!'
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
                "error" => $ex->getMessage()
            ]);
        }
    }
}
