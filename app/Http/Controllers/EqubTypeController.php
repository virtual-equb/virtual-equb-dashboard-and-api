<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EqubType;
use App\Http\Controllers\Controller;
use App\Models\Equb;
use App\Models\LotteryWinner;
use App\Models\MainEqub;
use App\Models\Member;
use App\Models\User;
use Exception;
use App\Repositories\EqubType\IEqubTypeRepository;
use App\Repositories\Equb\IEqubRepository;
use App\Repositories\EqubTaker\IEqubTakerRepository;
use App\Repositories\Payment\IPaymentRepository;
use App\Repositories\Member\IMemberRepository;
use App\Repositories\ActivityLog\IActivityLogRepository;
use App\Repositories\MainEqub\MainEqubRepositoryInterface;
use App\Service\Notification;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class EqubTypeController extends Controller
{
    private $activityLogRepository;
    private $equbTypeRepository;
    private $equbRepository;
    private $mainEqubRepository;
    private $equbTakerRepository;
    private $paymentRepository;
    private $memberRepository;
    private $title;
    public function __construct(
        IEqubTypeRepository $equbTypeRepository,
        IEqubRepository $equbRepository,
        IEqubTakerRepository $equbTakerRepository,
        IPaymentRepository $paymentRepository,
        IMemberRepository $memberRepository,
        IActivityLogRepository $activityLogRepository,
        MainEqubRepositoryInterface $mainEqubRepository
    ) {
        $this->activityLogRepository = $activityLogRepository;
        $this->equbRepository = $equbRepository;
        $this->equbTypeRepository = $equbTypeRepository;
        $this->equbTakerRepository = $equbTakerRepository;
        $this->paymentRepository = $paymentRepository;
        $this->memberRepository = $memberRepository;
        $this->title = "Virtual Equb - Equb Type";
        $this->mainEqubRepository = $mainEqubRepository;
        // // Guards
        // $this->middleware('permission_check_logout:update equb_type', ['only' => ['update', 'edit', 'updateStatus']]);
        // $this->middleware('permission_check_logout:delete equb_type', ['only' => ['destroy', 'dateInterval']]);
        // $this->middleware('permission_check_logout:view equb_type', ['only' => ['index', 'show']]);
        // $this->middleware('permission_check_logout:create equb_type', ['only' => ['store', 'create']]);
    }
    public function index()
    {
        try {
            $userData = Auth::user();
            // if ($userData && in_array($userData['role'], ["admin", "member", "general_manager", "operation_manager", "it", "customer_service", "assistant"])) {
                $data['equbTypes'] = $this->equbTypeRepository->getAll();
                $data['deactiveEqubType']  = $this->equbTypeRepository->getDeactive();
                $data['activeEqubType']  = $this->equbTypeRepository->getActive();
                $data['title']  = $this->title;
                $data['mainEqubs'] = $this->mainEqubRepository->all();
               /* return response()->json([
                    'equbTypes' => $data['equbTypes'],
                ]);
               dd( $data['equbTypes'] );*/
                return view('admin/equbType.equbTypeList', $data);
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
    public function dateInterval()
    {
        try {
            $data['deactiveEqubType']  = $this->equbTypeRepository->getDeactive();
        } catch (Exception $ex) {
            $msg = "Unable to process your request, Please try again!";
            $type = 'error';
            Session::flash($type, $msg);
            return back();
        }
    }
    public function nameEqubTypeCheck(Request $request)
    {
        try {
            $name = $request->name;
            $round = $request->round;
            $type = $request->type;
            $rote = $request->rote;
            if (!empty($name)) {
                $name_count = EqubType::where('name', $name)->where('round', $round)->where('type', $type)->where('rote', $rote)->count();
                if ($name_count > 0) {
                    echo "false";
                } else {
                    echo "true";
                }
            } else {
                echo "true";
            }
        } catch (Exception $ex) {
            $msg = "Unable to process your request, Please try again!";
            $type = 'error';
            Session::flash($type, $msg);
            return back();
        }
    }
    public function nameEqubTypeCheckForUpdate(Request $request)
    {
        try {
            $name = $request->update_name;
            $round = $request->update_round;
            $type = $request->update_type;
            $rote = $request->update_rote;
            $did = $request->did;
            if (!empty($name)) {
                $name_count = EqubType::where('name', $name)->where('round', $round)->where('type', $type)->where('rote', $rote)->where('id', '!=', $did)->count();
                if ($name_count > 0) {
                    echo "false";
                } else {
                    echo "true";
                }
            } else {
                echo "true";
            }
        } catch (Exception $ex) {
            $msg = "Unable to process your request, Please try again!";
            $type = 'error';
            Session::flash($type, $msg);
            return back();
        }
    }
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
            $msg = "Unable to process your request, Please try again!";
            $type = 'error';
            Session::flash($type, $msg);
            return back();
        }
    }
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
            $msg = "Unable to process your request, Please try again!";
            $type = 'error';
            Session::flash($type, $msg);
            return back();
        }
    }
    public function create()
    {   
        try {
            
            $data['title'] = $this->title;
            $data['mainEqubs'] = $this->mainEqubRepository->all();
                
            return view('admin/equbType/addEqubType', $data);
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
            
                $this->validate($request, [
                    'name' => 'required',
                    'round' => 'required',
                    'rote' => 'required',
                    'type' => 'required',
                    'main_equb_id' => 'required',
                    'start_date' => 'required|date'
                ]);
                $name = $request->input('name');
                $id = $request->input('id');
                $round = $request->input('round');
                $rote = $request->input('rote');
                $type = $request->input('type');
                $remark = $request->input('remark');
                $lottery_date = $request->input('lottery_date');
                $start_date = $request->input('start_date');
                $end_date = $request->input('end_date');
                $quota = $request->input('quota');
                $terms = $request->input('terms');
                $main_equb = $request->input('main_equb_id');
                $amount = $request->input('amount');
                $total_amount = $request->input('total_amount');
                // $expected_members = $request->input('quota');
                
                // Ensure start_date is in YMD format
                $formattedStartDate = Carbon::parse($start_date)->format('Y-m-d');

                // check if type is 'Automatic' and set lottery_date to 7 days after start_date
                $lottery_date = $request->input('lottery_date');
                if ($type === 'Automatic' && !$lottery_date) {
                    $lottery_date = Carbon::parse($formattedStartDate)->addDays(7)->format('Y-m-d');
                    $total_amount = 100 * $amount;
                    $expected_members = 100;
                }
                
                if ($end_date) {
                    $endDateCheck = $this->isDateInYMDFormat($end_date);
                    $formattedEndDate = $end_date;
                    if (!$endDateCheck) {
                        $carbonDate = Carbon::createFromFormat('m/d/Y', $end_date);
                        $formattedEndDate = $carbonDate->format('Y-m-d');
                    }
                }
                $equbTypeData = [
                    'name' => $name,
                    'round' => $round,
                    'rote' => $rote,
                    'type' => $type,
                    'remark' => $remark,
                    'lottery_date' => $lottery_date,
                    'start_date' => $start_date,
                    'end_date' => $end_date ? $formattedEndDate : null,
                    'quota' => $quota,
                    'remaining_quota' => $quota,
                    'terms' => $terms,
                    'main_equb_id' => $main_equb,
                    'amount' => $amount,
                    'expected_members' => $expected_members,
                    'total_amount' => $total_amount,
                ];
                if ($request->file('icon')) {
                    $image = $request->file('icon');
                    $imageName = time() . '.' . $image->getClientOriginalExtension();
                    $image->storeAs('public/equbTypeIcons', $imageName);
                    $equbTypeData['image'] = 'equbTypeIcons/' . $imageName;
                }

                $create = $this->equbTypeRepository->create($equbTypeData);
                $user = Auth::user();
                $roleName = $user->getRoleNames()->first();
                if ($create) {
                    $activityLog = [
                        'type' => 'equb_types',
                        'type_id' => $create->id,
                        'action' => 'created',
                        'user_id' => $userData->id,
                        'username' => $userData->name,
                        'role' => $roleName,
                    ];
                    $this->activityLogRepository->createActivityLog($activityLog);
                    $msg = "Equb type has been registered successfully!";
                    $type = 'success';
                    Session::flash($type, $msg);
                    return redirect('/equbType');
                } else {
                    $msg = "Unknown Error Occurred, Please try again!";
                    $type = 'error';
                    Session::flash($type, $msg);
                    redirect('/equbType');
                }
        } catch (Exception $ex) {
            // dd($ex);
            $msg = $ex->getMessage();
            $type = 'error';
            Session::flash($type, $msg);
            return back();
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
    public function drawAutoWinners(Request $request)
    {
        try {
            $equbTypeId = $request->equbTypeId;
            $now = Carbon::now()->startOfDay();
            $allWinners = []; // Track all winners to exclude from future draws
            // $totalMembers = 100; // totalMembers
            $winnerCount = 0;
            $totalMembers = 1;
            $members = DB::table('equbs')
                        ->where('equb_type_id', $equbTypeId)
                        ->where('status', 'Active')
                        ->pluck('member_id')
                        ->toArray();

            if (count($members) < $totalMembers) {
                // Ensure there are at least 100 eligible members for the draw
                $msg = "Not enough members for the lottery draw.";
                $type = 'error';
                Session::flash($type, $msg);
                return back();
            }

            $equbEndDate = DB::table('equb_types')
                            ->where('id', $equbTypeId)
                            ->value('end_date');

            while (Carbon::now()->startOfDay()->lte($equbEndDate)) {
                $eligibleMembers = array_diff($members, $allWinners); // Exclude previous winners
                if (count($eligibleMembers) < 7) {
                    break; // Stop if there are fewer than 7 eligible members
                }

                // Draw 7 new unique winners
                $roundWinners = $this->drawRandomId($eligibleMembers, 7);
                $allWinners = array_merge($allWinners, $roundWinners);

                // Save each round's winners to the LotteryWinners table
                foreach ($roundWinners as $winnerId) {
                    // Get members full name
                    $member = Member::find($winnerId);
                    $memberName = $member ? $member->full_name : "Unknown Member";

                    // Get the equb type name
                    $equbType = EqubType::find($equbTypeId);
                    $equbTypeName = $equbType ? $equbType->name : "Unknown Equb Type";
                    $winnerEntry = [
                        'equb_type_id' => $equbTypeId,
                        'member_id' => $winnerId,
                        'member_name' => $memberName,
                        'equb_type_name' => $equbTypeName,
                        'created_at' => $now,
                        'updated_at' => $now
                    ];
                    LotteryWinner::create($winnerEntry);
                    $winnerCount++;
                }

                // Notify winners and other members
                $this->notifyWinnersAndMembers($equbTypeId, $roundWinners, $members);

                // Advance lottery date by 7 days for the next draw
                $lotteryDate = Carbon::parse($now)->addDays(7);
                EqubType::where('id', $equbTypeId)->update(['lottery_date' => $lotteryDate]);

                if ($lotteryDate->gt($equbEndDate)) {
                    break; // Stop if the next lottery date exceeds the Equb end date
                }
            }

            $msg = "Equb draw has been successfully completed! Total Winners: $winnerCount";
            $type = 'success';
            Session::flash($type, $msg);
            return back()->with(['winnerCount' => $winnerCount]);
        } catch (Exception $ex) {
            $msg = "Unable to process your request, please try again!";
            $type = 'error';
            Session::flash($type, $msg);
            return back();
        }
    }
    protected function notifyWinnersAndMembers($equbTypeId, array $roundWinners, array $allMembers)
    {
        $equbType = EqubType::find($equbTypeId);
        $shortcode = config('key.SHORT_CODE');

        foreach ($roundWinners as $winnerId) {
            $winner = Member::find($winnerId);
            $title = "Congratulations";
            $message = "You have been selected as the winner of the equb {$equbType->name}. For further information, please call {$shortcode}.";
            
            // Notify the winner
            $notifiedWinner = User::where('phone_number', $winner->phone)->first();
            Notification::sendNotification($notifiedWinner->fcm_id, $message, $title);
            $this->sendSms($notifiedWinner->phone_number, $message);
        }

        // Notify remaining members of the 7 selected winners
        $winnerNames = Member::whereIn('id', $roundWinners)->pluck('full_name')->toArray();
        $winnerList = implode(", ", $winnerNames);
        $message = "The winners for the current Equb round are: {$winnerList}.";
        
        foreach ($allMembers as $memberId) {
            if (!in_array($memberId, $roundWinners)) { // Notify only non-winners
                $member = Member::find($memberId);
                $notifiedMember = User::where('phone_number', $member->phone)->first();
                Notification::sendNotification($notifiedMember->fcm_id, $message, "Equb Round Results");
                $this->sendSms($notifiedMember->phone_number, $message);
            }
        }
    }

    function drawRandomId(array $ids, int $numWinners = 7)
    {
        // Shuffle and pick random winners without repetition
        shuffle($ids);
        return array_slice($ids, 0, $numWinners);
    }
    // public function drawAutoWinners(Request $request)
    // {
    //     // dd($request->equbTypeId);
    //     try {
    //         $equbTypeId = $request->equbTypeId;
    //         // $controller = app()->make(Controller::class);
    //         $now = Carbon::now()->startOfDay();
    //         $members = [];
    //         $winners = [];
    //         $winnerUsers = [];
    //         $checkUserArray = [];
    //         $equbsMembersToNotify = [];
    //         $winnerCount = 0;

    //         if ($equbTypeId === 'all') {
    //             $equbTypes = DB::table('equb_types')
    //                 ->whereDate('lottery_date', '=', $now)
    //                 ->where("deleted_at", "=", null)
    //                 ->where("status", "=", "Active")
    //                 ->get();

    //             if ($equbTypes && count($equbTypes) > 0) {
    //                 foreach ($equbTypes as $equbType) {
    //                     $equbsMembers = DB::table('equbs')
    //                         ->where('equb_type_id', $equbType->id)
    //                         ->where('status', 'Active')
    //                         ->where('check_for_draw', true)
    //                         ->pluck('member_id')
    //                         ->toArray();
    //                     $equbsMembersToNotify = DB::table('equbs')
    //                             ->where('equb_type_id', $equbType->id)
    //                             ->where('status', 'Active')
    //                             ->pluck('member_id')
    //                             ->toArray();

    //                     // dd($equbsMembers);
    //                     $equbs = DB::table('equbs')->where('equb_type_id', $equbType->id)->where('status', 'Active')->get();
                       
    //                     if ($equbsMembers) {
    //                         foreach ($equbsMembers as $member) {
    //                             $checkUser = LotteryWinner::where('equb_type_id', $equbType->id)->where('member_id', $member)->first();
    //                             if (!$checkUser && !in_array($member, $checkUserArray)) {
    //                                 array_push($checkUserArray, $member);
    //                             }
    //                         }
    //                     }
    //                     if (count($checkUserArray) <= 1) {
    //                         foreach ($equbs as $equb) {
    //                             Equb::where('id', $equb->id)->update(['status' => 'Deactive']);
    //                         }
    //                     }
    //                     if ($equbsMembers) {
    //                         foreach ($equbsMembers as $member) {
    //                             if (!in_array($member, $members)) {
    //                                 array_push($members, $member);
    //                             }
    //                         }
    //                         $winner = $this->drawRandomId($members);
    //                         $checkUser = LotteryWinner::where('equb_type_id', $equbType->id)->where('member_id', $winner)->first();
    //                         if ($checkUser) {
    //                             $filteredIds = array_filter($members, function ($member) use ($winner) {
    //                                 return $member !== $winner;
    //                             });
    //                             if ($filteredIds && count($filteredIds) > 0) {
    //                                 $winner = $this->drawRandomId($filteredIds);
    //                             } else {
    //                                 break;
    //                             }
    //                         }
    //                         array_push($winners, ["EqubTypeId" => $equbType->id, "EqubTypeName" => $equbType->name, "Winner" => $winner]);
    //                         $members = [];
    //                     }
    //                 }
    //                 if ($winners && count($winners) > 0) {
    //                     foreach ($winners as $winner) {
    //                         $user = Member::where('id', $winner['Winner'])->first();
    //                         array_push($winnerUsers, [
    //                             "equb_type_id" => $winner['EqubTypeId'],
    //                             "equb_type_name" => $winner['EqubTypeName'],
    //                             "member_id" => $user->id,
    //                             "member_name" => $user->full_name
    //                         ]);
    //                     }
    //                     foreach ($winnerUsers as $winnerUser) {
    //                         $shortcode = config('key.SHORT_CODE');
    //                         LotteryWinner::create($winnerUser);
    //                         $member = DB::table('members')->where('id', $winnerUser['member_id'])->first();
    //                         $notifiedMember = User::where('phone_number', $member->phone)->first();
    //                         $title = "Congratulations";
    //                         $memberBody = "You have been selected as the winner of the equb " . $winnerUser['equb_type_name'] . " For further information please call " . $shortcode;
    //                         Notification::sendNotification($notifiedMember->fcm_id, $memberBody, $title);
    //                         $this->sendSms($notifiedMember->phone_number, $memberBody);
    //                         // $users = User::where('role', 'admin')->orWhere('role', 'equb_collector')->get();
    //                         // foreach ($users as $user) {
    //                         //     $adminTitle = $winnerUser['equb_type_name'] . "'s Lottery Winner";
    //                         //     $adminBody = $winnerUser['member_name'] . " has been selected as the winner of the equb " . $winnerUser['equb_type_name'];
    //                         //     Notification::sendNotification($user->fcm_id, $adminBody, $adminTitle, $notifiedMember->name);
    //                         //     $this->sendSms($user->phone_number, $adminBody);
    //                         // }
    //                         foreach ($equbsMembersToNotify as $memberId) {
    //                             if ($memberId !== $winnerUser['member_id']) {
    //                                 $memberDomain = Member::where('id', $memberId)->first();
    //                                 $user = User::where('phone_number', $memberDomain->phone)->first();
    //                                 $adminTitle = $winnerUser['equb_type_name'] . "'s Lottery Winner";
    //                                 $adminBody = $winnerUser['member_name'] . " has been selected as the winner of the equb " . $winnerUser['equb_type_name'];
    //                                 Notification::sendNotification($user->fcm_id, $adminBody, $adminTitle, $notifiedMember->name);
    //                                 $this->sendSms($user->phone_number, $adminBody);
    //                             }
    //                         }
    //                     }
    //                     foreach ($equbTypes as $equbType) {
    //                         $daysToBeAdded = 7;
    //                         if ($equbType->rote === "Daily") {
    //                             $daysToBeAdded = 1;
    //                         } elseif ($equbType->rote === "Weekly") {
    //                             $daysToBeAdded = 7;
    //                         } elseif ($equbType->rote === "Biweekly") {
    //                             $daysToBeAdded = 14;
    //                         } elseif ($equbType->rote === "Monthly") {
    //                             $daysToBeAdded = 30;
    //                         }
    //                         $updatedLotterDate = $now->copy()->addDays($daysToBeAdded)->format('Y-m-d');
    //                         EqubType::where('id', $equbType->id)->update(['lottery_date' => $updatedLotterDate]);
    //                     }
    //                 }
    //             } else {
    //                 $msg = "No active equb types with lottery date of today found";
    //                 $type = 'error';
    //                 Session::flash($type, $msg);
    //                 return back();
    //             }
    //         } else {
    //             $equbType = DB::table('equb_types')
    //                 ->where("id", "=", $equbTypeId)
    //                 ->first();
    //             $lotteryDate = Carbon::parse($equbType->lottery_date)->startOfDay();
    //             if ($lotteryDate == $now && $equbType->status == 'Active') {
    //                 $equbsMembers = DB::table('equbs')
    //                     ->where('equb_type_id', $equbType->id)
    //                     ->where('status', 'Active')
    //                     ->where('check_for_draw', true)
    //                     ->pluck('member_id')
    //                     ->toArray();
    //                 $equbsMembersToNotify = DB::table('equbs')->where('equb_type_id', $equbType->id)->where('status', 'Active')->pluck('member_id')->toArray();
    //                 // dd($equbsMembers);
    //                 $equbs = DB::table('equbs')->where('equb_type_id', $equbType->id)->where('status', 'Active')->get();
    //                 if ($equbsMembers) {

    //                     foreach ($equbsMembers as $member) {
    //                         $checkUser = LotteryWinner::where('equb_type_id', $equbType->id)->where('member_id', $member)->first();
    //                         if (!$checkUser && !in_array($member, $checkUserArray)) {
    //                             array_push($checkUserArray, $member);
    //                         }
    //                     }
    //                 }
    //                 if (count($checkUserArray) <= 1) {
    //                     foreach ($equbs as $equb) {
    //                         Equb::where('id', $equb->id)->update(['status' => 'Deactive']);
    //                     }
    //                 }
    //                 if ($equbsMembers) {
    //                     foreach ($equbsMembers as $member) {
    //                         if (!in_array($member, $members)) {
    //                             array_push($members, $member);
    //                         }
    //                     }
    //                     $winner = $this->drawRandomId($members);
    //                     $checkUser = LotteryWinner::where('equb_type_id', $equbType->id)->where('member_id', $winner)->first();
    //                     if ($checkUser) {
    //                         $filteredIds = array_filter($members, function ($member) use ($winner) {
    //                             return $member !== $winner;
    //                         });
    //                         if ($filteredIds && count($filteredIds) > 0) {
    //                             $winner = $this->drawRandomId($filteredIds);
    //                         }
    //                     }
    //                     array_push($winners, ["EqubTypeId" => $equbType->id, "EqubTypeName" => $equbType->name, "Winner" => $winner]);
    //                     $members = [];
    //                 }

    //                 if ($winners && count($winners) > 0) {
    //                     foreach ($winners as $winner) {
    //                         $user = Member::where('id', $winner['Winner'])->first();
    //                         array_push($winnerUsers, [
    //                             "equb_type_id" => $winner['EqubTypeId'],
    //                             "equb_type_name" => $winner['EqubTypeName'],
    //                             "member_id" => $user->id,
    //                             "member_name" => $user->full_name
    //                         ]);
    //                     }
    //                     foreach ($winnerUsers as $winnerUser) {
    //                         $shortcode = config('key.SHORT_CODE');
    //                         LotteryWinner::create($winnerUser);
    //                         $member = DB::table('members')->where('id', $winnerUser['member_id'])->first();
    //                         $notifiedMember = User::where('phone_number', $member->phone)->first();
    //                         $title = "Congratulations";
    //                         $memberBody = "You have been selected as the winner of the equb " . $winnerUser['equb_type_name'] . " For further information please call " . $shortcode;
    //                         Notification::sendNotification($notifiedMember->fcm_id, $memberBody, $title);
    //                         $this->sendSms($notifiedMember->phone_number, $memberBody);
    //                         // $users = User::where('role', 'admin')->orWhere('role', 'equb_collector')->get();
    //                         // foreach ($users as $user) {
    //                         //     $adminTitle = $winnerUser['equb_type_name'] . "'s Lottery Winner";
    //                         //     $adminBody = $winnerUser['member_name'] . " has been selected as the winner of the equb " . $winnerUser['equb_type_name'];
    //                         //     Notification::sendNotification($user->fcm_id, $adminBody, $adminTitle, $notifiedMember->name);
    //                         //     $this->sendSms($user->phone_number, $adminBody);
    //                         // }
    //                         foreach ($equbsMembersToNotify as $memberId) {
    //                             if ($memberId !== $winnerUser['member_id']) {
    //                                 $memberDomain = Member::where('id', $memberId)->first();
    //                                 $user = User::where('phone_number', $memberDomain->phone)->first();
    //                                 $adminTitle = $winnerUser['equb_type_name'] . "'s Lottery Winner";
    //                                 $adminBody = $winnerUser['member_name'] . " has been selected as the winner of the equb " . $winnerUser['equb_type_name'];
    //                                 Notification::sendNotification($user->fcm_id, $adminBody, $adminTitle, $notifiedMember->name);
    //                                 $this->sendSms($user->phone_number, $adminBody);
    //                             }
    //                         }
    //                     }
    //                     // foreach ($equbTypes as $equbType) {
    //                     $daysToBeAdded = 0;
    //                     if ($equbType->rote === "Daily") {
    //                         $daysToBeAdded = 1;
    //                     } elseif ($equbType->rote === "Weekly") {
    //                         $daysToBeAdded = 7;
    //                     } elseif ($equbType->rote === "Biweekly") {
    //                         $daysToBeAdded = 14;
    //                     } elseif ($equbType->rote === "Monthly") {
    //                         $daysToBeAdded = 30;
    //                     }
    //                     $updatedLotterDate = $now->copy()->addDays($daysToBeAdded)->format('Y-m-d');
    //                     EqubType::where('id', $equbType->id)->update(['lottery_date' => $updatedLotterDate]);
    //                     // }
    //                 }
    //             } else {
    //                 $msg = "Equb Type is inactive or the lottery date is not today.";
    //                 $type = 'error';
    //                 Session::flash($type, $msg);
    //                 return back();
    //             }
    //         }
    //         $msg = "Equb draw has been successfully completed!";
    //         $type = 'success';
    //         Session::flash($type, $msg);
    //         return back();
    //     } catch (Exception $ex) {
    //         // dd($ex);
    //         $msg = "Unable to process your request, Please try again!";
    //         $type = 'error';
    //         Session::flash($type, $msg);
    //         return back();
    //     }
    // }

    // function drawRandomId(array $ids)
    // {
    //     // Shuffle the array of IDs
    //     shuffle($ids);

    //     // Pick a random index and return the corresponding ID
    //     $randomIndex = array_rand($ids);
    //     $randomId = $ids[$randomIndex];

    //     return $randomId;
    // }
    public function show(EqubType $equbType)
    {
        try {
            $userData = Auth::user();
            // if ($userData && ($userData['role'] == "admin" || $userData['role'] == "general_manager" || $userData['role'] == "operation_manager" || $userData['role'] == "it")) {
                $equb = $this->equbTypeRepository->getById($equbType);
                return $equb;
            // } else {
            //     return view('auth/login');
            // };
        } catch (Exception $ex) {
            $msg = "Unable to process your request, Please try again!";
            $type = 'error';
            Session::flash($type, $msg);
            return back();
        }
    }
    public function edit(EqubType $equbType)
    {
        try {
            $data['equbType'] = $this->equbTypeRepository->getById($equbType);

            return view('admin/equbType/updateEqubType', $data);

        } catch (Exception $ex) {
            $msg = "Unable to process your request, Please try again!";
            $type = 'error';
            Session::flash($type, $msg);
            return back();
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
            $result = $winner ? [
                "memberId" => $winner->member_id,
                "memberName" => $winner->member_name
            ] : [];
            return $result;
        } catch (Exception $ex) {
            $msg = "Unable to process your request, Please try again!";
            $type = 'error';
            Session::flash($type, $msg);
            return back();
        }
    }
    public function getWinnerForDashboard($id, Request $request)
    {
        try {
            $winner = LotteryWinner::where('equb_type_id', $id)->orderBy('created_at', 'desc')->first();
            $result = $winner ? [
                "memberId" => $winner->member_id,
                "memberName" => $winner->member_name,
                "memberPhone" => $winner->phone,
                "memberGender" => $winner->member_name
            ] : [];
            return $result;
        } catch (Exception $ex) {
            $msg = "Unable to process your request, Please try again!";
            $type = 'error';
            Session::flash($type, $msg);
            return back();
        }
    }
    public function updateStatus($id, Request $request)
    {
        try {
            $userData = Auth::user();
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
    public function update($id, Request $request)
    {
        // dd($request);
        try {
                $userData = Auth::user();
                $equbTypeDetail = EqubType::where('id', $id)->first();

                $main_equb = $request->input('update_main_equb');
                $name = $request->input('update_name');
                $round = $request->input('update_round');
                $rote = $request->input('update_rote');
                $type = $request->input('update_type');
                $remark = $request->input('update_remark');
                $lottery_date = $request->input('update_lottery_date');
                $start_date = $request->input('start_date');
                $end_date = $request->input('end_date');
                $amount = $request->input('amount');
                $total_amount = $request->input('total_amount');
                if ($start_date) {
                    $startDateCheck = $this->isDateInYMDFormat($start_date);
                    $formattedStartDate = $start_date;
                    if (!$startDateCheck) {
                        $carbonDate = Carbon::createFromFormat('m/d/Y', $start_date);
                        $formattedStartDate = $carbonDate->format('Y-m-d');
                    }
                }
                if ($end_date) {
                    $endDateCheck = $this->isDateInYMDFormat($end_date);
                    $formattedEndDate = $end_date;
                    if (!$endDateCheck) {
                        $carbonDate = Carbon::createFromFormat('m/d/Y', $end_date);
                        $formattedEndDate = $carbonDate->format('Y-m-d');
                    }
                }
                $quota = $request->input('quota');
                $remainingQuota = $equbTypeDetail->quota;
                if ($remainingQuota != $quota) {
                    $difference = $quota - $remainingQuota;
                    if ($difference > 0) {
                        $remainingQuota = $equbTypeDetail->remaining_quota + $difference;
                    } else {
                        $difference = $difference * -1;
                        $remainingQuota = $equbTypeDetail->remaining_quota - $difference;
                    }
                }
                $terms = $request->input('update_terms');
                // dd($request->file('icon_update'));

                $updated = [
                    'main_equb' => $main_equb,
                    'name' => $name,
                    'round' => $round,
                    'rote' => $rote,
                    'type' => $type,
                    'remark' => $remark,
                    'lottery_date' => $lottery_date,
                    'start_date' => $start_date ? $formattedStartDate : null,
                    'end_date' => $end_date ? $formattedEndDate : null,
                    'quota' => $quota,
                    'remaining_quota' => $remainingQuota,
                    'terms' => $terms,
                    'amount' => $amount,
                    'total_amount' => $total_amount
                ];
                
                if ($request->file('icon_update')) {
                    $image = $request->file('icon_update');
                    $imageName = time() . '.' . $image->getClientOriginalExtension();
                    $image->storeAs('public/equbTypeIcons', $imageName);
                    $updated['image'] = 'equbTypeIcons/' . $imageName;
                }
                $oldEqubType = $this->equbTypeRepository->getById($id);
                $updated = $this->equbTypeRepository->update($id, $updated);
                // dd($updated);
                $newEqubType = $this->equbTypeRepository->getById($id);
                if ($updated) {
                    if ($oldEqubType->quota != $newEqubType->quota) {
                        $updatedEqubs = $this->equbRepository->getByEqubTypeId($id);
                        foreach ($updatedEqubs as $equb) {
                            $amount = $equb->amount;
                            $previousQuota = $oldEqubType->quota;
                            $newQuota = $newEqubType->quota;
                            $difference = $newQuota - $previousQuota;
                            if ($difference > 0) {
                                $addedAmount = $amount * $difference;
                                $equb->total_amount += $addedAmount;
                            } elseif ($difference < 0) {
                                $subtractedAmount = $amount * abs($difference);
                                $equb->total_amount -= $subtractedAmount;
                            }
                            $equb->end_date = $newEqubType->end_date;
                            $equb->save();
                        }
                    }
                    $activityLog = [
                        'type' => 'equb_types',
                        'type_id' => $id,
                        'action' => 'updated',
                        'user_id' => $userData->id,
                        'username' => $userData->name,
                        'role' => $userData->role,
                    ];
                    $this->activityLogRepository->createActivityLog($activityLog);
                    $msg = "Equb type has been updated successfully!";
                    $type = 'success';
                    Session::flash($type, $msg);
                    return redirect('equbType/');
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
            // if ($userData && ($userData['role'] == "admin" || $userData['role'] == "general_manager" || $userData['role'] == "operation_manager" || $userData['role'] == "it")) {
                $equb = $this->equbRepository->getEqubType($id);
                if (!$equb->isEmpty()) {
                    $msg = "This equb type is being used, please deactive it instead of deleting";
                    $type = 'error';
                    Session::flash($type, $msg);
                    return redirect('equbType/');
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
                        $msg = "Equb type has been deleted successfully!";
                        $type = 'success';
                        Session::flash($type, $msg);
                        return redirect('equbType/');
                    } else {
                        $msg = "Unknown Error Occurred, Please try again!";
                        $type = 'error';
                        Session::flash($type, $msg);
                        redirect('/equbType');
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