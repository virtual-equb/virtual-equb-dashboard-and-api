<?php

namespace App\Http\Controllers;

use Exception;
use Carbon\Carbon;
use App\Models\Equb;
use App\Models\User;
use App\Models\Member;
use App\Models\Payment;
use App\Models\EqubType;
use App\Models\MainEqub;
use Illuminate\Http\Request;
use App\Models\LotteryWinner;
use App\Service\Notification;
use App\Jobs\NotifyWinnersJob;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Repositories\Equb\IEqubRepository;
use App\Repositories\Member\IMemberRepository;
use App\Repositories\Payment\IPaymentRepository;
use App\Repositories\EqubType\IEqubTypeRepository;
use App\Repositories\EqubTaker\IEqubTakerRepository;
use App\Repositories\ActivityLog\IActivityLogRepository;
use App\Repositories\MainEqub\MainEqubRepositoryInterface;

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
    }
    public function index()
    {
        try {
            $userData = Auth::user();
                $data['equbTypes'] = $this->equbTypeRepository->getAll();
                $data['deactiveEqubType']  = $this->equbTypeRepository->getDeactive();
                $data['activeEqubType']  = $this->equbTypeRepository->getActive();
                $data['title']  = $this->title;
                $data['mainEqubs'] = $this->mainEqubRepository->all();
                $totalEqubType = EqubType::count();
                $totalActiveEqubType =  EqubType::active()->count();
                $totalInactiveEqubType =  EqubType::inactive()->count();

                return view('admin/equbType.equbTypeList', $data, compact('totalEqubType', 'totalActiveEqubType', 'totalInactiveEqubType'));
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

    public function store(Request $request) {
        try {
            $userData = Auth::user();
        
            $this->validate($request, [
                'name' => 'required',
                'round' => 'required',
                'rote' => 'required',
                'type' => 'required',
                'main_equb_id' => 'required|exists:main_equbs,id',
                'start_date' => 'required|date',
            ]);
        
            $name = $request->input('name');
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
        
            // Format the start_date for consistency
            $formattedStartDate = Carbon::parse($start_date)->format('Y-m-d');
        
            // Set default values based on the type
            if ($type === 'Automatic' && !$lottery_date) {
                $lottery_date = Carbon::parse($formattedStartDate)->addDays(7)->format('Y-m-d');
                $total_amount = $total_amount;
                $expected_members = 105;
            } elseif ($type === 'Seasonal') {
                $lottery_date = Carbon::parse($start_date)->addDays(7)->format('Y-m-d');
                $end_date = Carbon::parse($start_date)->addDays(21)->format('Y-m-d');
                $total_amount = $quota * $amount;
                $expected_members = $request->input('quota');
                $quota = null;
                $terms = "1. Registration is open until one day before the lottery date. 
                          2. Members registering late must pay for missed days. 
                          3. The Equb lasts for 21 days, with weekly lotteries.
                          4. Number of weekly winners: total_members ÷ 3.";
            }
        
            // For manual type, exclude irrelevant fields
            if ($type === 'Manual') {
                $lottery_date = null;
                $quota = null;
                $amount = null;
                $total_amount = null;
                $expected_members = null;
            }
        
            // Prepare the data for insertion
            $equbTypeData = [
                'name' => $name,
                'round' => $round,
                'rote' => $rote,
                'type' => $type,
                'remark' => $remark,
                'lottery_date' => $type === 'Manual' ? null : $lottery_date,
                'start_date' => $start_date,
                'end_date' => $end_date ? Carbon::parse($end_date)->format('Y-m-d') : null,
                'quota' => $quota,
                'remaining_quota' => $quota,
                'terms' => $terms,
                'main_equb_id' => $main_equb,
                'amount' => $type === 'Manual' ? null : $amount,
                'expected_members' => $type === 'Manual' ? null : $expected_members,
                'total_amount' => $type === 'Manual' ? null : $total_amount,
            ];
        
            // Handle file upload
            if ($request->file('icon')) {
                $image = $request->file('icon');
                $imageName = time() . '.' . $image->getClientOriginalExtension();
                $image->storeAs('public/equbTypeIcons', $imageName);
                $equbTypeData['image'] = 'equbTypeIcons/' . $imageName;
            }
        
            // Remove null fields for clean insertion
            $equbTypeData = array_filter($equbTypeData, function ($value) {
                return !is_null($value);
            });
        
            // Insert the data
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
                Session::flash('success', $msg);
                return redirect('/equbType');
            } else {
                $msg = "Unknown Error Occurred, Please try again!";
                Session::flash('error', $msg);
                return redirect('/equbType');
            }
        } catch (Exception $ex) {
            $msg = $ex->getMessage();
            Session::flash('error', $msg);
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
    
            // Fetch Equbtype details
            $equb = EqubType::find($equbTypeId);
            if (!$equb) {
                Session::flash('error', 'Invalid Equb type');
                return back();
            }
    
            $equbStartDate = Carbon::parse($equb->start_date);
            $equbEndDate = Carbon::parse($equb->end_date);
            $lotteryDate = Carbon::parse($equb->lottery_date);
    
            // Ensure the lottery date matches today and hasn't already ended
            if (!$now->eq($lotteryDate)) {
                Session::flash('error', "Lottery cannot proceed. Ensure the lottery date matches the current date.");
                return back();
            }
    
            if ($now->gt($equbEndDate)) {
                Session::flash('error', "The Equb has already ended.");
                return back();
            }
    
            // Fetch active members for the equb
            $members = DB::table('equbs')
                ->where('equb_type_id', $equbTypeId)
                ->where('status', 'Active')
                ->pluck('member_id')
                ->toArray();
    
            // Exclude previous winners based on winner_round
            $previousWinners = LotteryWinner::where('equb_type_id', $equbTypeId)
                ->where('winner_round', '!=', 0) // Exclude members with non-zero winner_round
                ->pluck('member_id')
                ->toArray();
    
            $eligibleMembers = array_diff($members, $previousWinners);
    
            // Identify inactive members who missed 5 or more payments
            $inactiveMembersIds = Payment::select('member_id')
                ->whereBetween('created_at', [$equbStartDate, $lotteryDate])
                ->groupBy('member_id')
                ->havingRaw('COUNT(*) < 5') // Members with fewer than 5 payments
                ->pluck('member_id')
                ->toArray();
    
            // Exclude inactive members from eligible members
            $eligibleMembers = array_diff($eligibleMembers, $inactiveMembersIds);
    
            // Stop the draw if no eligible members remain
            if (empty($eligibleMembers)) {
                // Retrieve users with roles 'admin' and 'operation_manager'
                $userToNotify = User::role(['admin', 'operation_manager'])->get();
    
                $message = "No eligible members for the lottery draw. Please review and ensure payments are up-to-date.";
    
                foreach ($userToNotify as $user) {
                    try {
                        $this->sendSms($user->phone_number, $message);
                    } catch (Exception $e) {
                        Log::error("Failed to send SMS to {$user->phone_number}: {$e->getMessage()}");
                    }
                }
    
                // Flash an error message for the web
                Session::flash('error', "No eligible members for the lottery draw. Ensure payments are up-to-date.");
                return back();
            }
    
            // Add Demo users if eligible members are less than 105
            if (count($eligibleMembers) < 105) {
                $demoUsersNeeded = 105 - count($eligibleMembers);
                $demoUsers = Member::where('gender', '')
                    ->whereNotIn('id', $eligibleMembers)
                    ->limit($demoUsersNeeded)
                    ->pluck('id')
                    ->toArray();
    
                $eligibleMembers = array_merge($eligibleMembers, $demoUsers);
            }
    
            // Ensure we have at least 7 eligible members
            if (count($eligibleMembers) < 7) {
                Session::flash('error', "Not enough eligible members for the lottery draw.");
                return back();
            }
    
            // Draw 7 unique winners
            $roundWinners = $this->drawRandomId($eligibleMembers, 7);
    
            // Save winners to the database
            $winnerEntries = [];
            foreach ($roundWinners as $winnerId) {
                $member = Member::find($winnerId);
                $memberName = $member ? $member->full_name : "Unknown Member";
                $equbTypeName = $equb->name ?? "Unknown Equb Type";
    
                $winnerEntries[] = [
                    'equb_type_id' => $equbTypeId,
                    'member_id' => $winnerId,
                    'member_name' => $memberName,
                    'equb_type_name' => $equbTypeName,
                    'winner_round' => $equb->lottery_round + 1, // Set the current round as winner_round
                    'created_at' => $now,
                    'updated_at' => $now
                ];
            }
    
            LotteryWinner::insert($winnerEntries);
    
            // Notify winners and other members
            $this->notifyWinnersAndMembers($equbTypeId, $roundWinners, $members);
    
            // Update the next lottery date
            $nextLotteryDate = $now->addDays(7);
            if ($nextLotteryDate->lte($equbEndDate)) {
                $equb->update([
                    'lottery_date' => $nextLotteryDate,
                    'lottery_round' => $equb->lottery_round + 1
                ]);
            }
    
        } catch (Exception $ex) {
            $msg = "Unable to process your request: " . $ex->getMessage();
            Session::flash('error', $msg);
            return back();
        }
    }
    
    protected function notifyWinnersAndMembers($equbTypeId, array $roundWinners, array $allMembers)
    {
        $equbType = EqubType::find($equbTypeId);
        $shortcode = config('key.SHORT_CODE');

        // Prepare winner names for the non-winners message
        $winnerNames = Member::whereIn('id', $roundWinners)->pluck('full_name')->toArray();
        $winnerList = implode(", ", $winnerNames);

        foreach ($allMembers as $memberId) {
            $member = Member::find($memberId);
            
            if (in_array($memberId, $roundWinners)) {
                // If the member is a winner, send a congrats message
                $title = "Congratulations";
                $message = "You have been selected as the winner of the equb {$equbType->name}. For further information, please call {$shortcode}.";

                $notifiedWinner = User::where('phone_number', $member->phone)->first();
                if ($notifiedWinner) {
                    Notification::sendNotification($notifiedWinner->fcm_id, $message, $title);
                    $this->sendSms($notifiedWinner->phone_number, $message);
                }
            } else {
                // If the member is not a winner, send the winners' list message
                $message = "The winners for the current Equb round are: {$winnerList}. For further information, please call {$shortcode}.";

                $notifiedMember = User::where('phone_number', $member->phone)->first();
                if ($notifiedMember) {
                    Notification::sendNotification($notifiedMember->fcm_id, $message, "Equb Round Results");
                    $this->sendSms($notifiedMember->phone_number, $message);
                }
            }
        }
    }

    function drawRandomId(array $ids, int $numWinners = 7)
    {
        // Shuffle and pick random winners without repetition
        shuffle($ids);
        return array_slice($ids, 0, $numWinners);
    }

    public function drawSeasonedAutoWinners(Request $request)
    {
        try {
            $equbTypeId = $request->equbTypeId;
            $now = Carbon::now()->startOfDay();
    
            // Fetch equbType details
            $equb = EqubType::find($equbTypeId);
            if (!$equb) {
                Session::flash('error', 'Invalid Equb type');
                return back();
            }
    
            $equbStartDate = Carbon::parse($equb->start_date);
            $equbEndDate = $equbStartDate->copy()->addDays(21);
            $lotteryDate = Carbon::parse($equb->lottery_date);
    
            // Ensure the current date matches the lottery date
            if (!$now->eq($lotteryDate)) {
                Session::flash('error', "Lottery cannot proceed. The lottery date must match today's date.");
                return back();
            }
    
            // Ensure the equb has not ended
            if ($now->gt($equbEndDate)) {
                Session::flash('error', "The equb has already ended.");
                return back();
            }
    
            // Fetch all active members
            $members = DB::table('equbs')
                ->where('equb_type_id', $equbTypeId)
                ->where('status', 'Active')
                ->pluck('member_id')
                ->toArray();
    
            // Exclude previous winners
            $previousWinners = LotteryWinner::where('equb_type_id', $equbTypeId)
                ->pluck('member_id')
                ->toArray();
    
            $eligibleMembers = array_diff($members, $previousWinners);
    
            // Filter out members who have not made the required payments
            $eligibleMembers = array_filter($eligibleMembers, function($memberId) use ($equbStartDate, $now) {
                $registrationDate = Member::where('id', $memberId)->value('created_at');
                $registrationDate = Carbon::parse($registrationDate);
    
                $totalOwedDays = $registrationDate->gt($equbStartDate) 
                    ? $registrationDate->diffInDays($now) 
                    : $equbStartDate->diffInDays($now);
    
                $totalPaymentsMade = Payment::where('member_id', $memberId)
                    ->where('status', 'Paid') // Ensure payment status is verified
                    ->count();
    
                return $totalPaymentsMade >= $totalOwedDays; // Member must have made all payments up to now
            });
    
            if (empty($eligibleMembers)) {
                Session::flash('error', "No eligible members for the lottery. Ensure all participants have made the required payments.");
                return back();
            }
    
            // Determine winners
            $totalMembers = count($eligibleMembers);
            $winnersPerWeek = (int) ceil($totalMembers / 3);
            $roundWinners = $this->drawRandomSeasonId($eligibleMembers, $winnersPerWeek);
    
            // Save winners to the database
            $winnerEntries = [];
            foreach ($roundWinners as $winnerId) {
                $member = Member::find($winnerId);
                $memberName = $member ? $member->full_name : "Unknown Member";
                $equbTypeName = $equb->name ?? "Unknown Equb Type";
    
                $winnerEntries[] = [
                    'equb_type_id' => $equbTypeId,
                    'member_id' => $winnerId,
                    'member_name' => $memberName,
                    'equb_type_name' => $equbTypeName,
                    'created_at' => $now,
                    'updated_at' => $now
                ];
            }
    
            LotteryWinner::insert($winnerEntries);
    
            // Notify winners
            $this->notifyWinnersAndMembers($equbTypeId, $roundWinners, $members);
    
            // Update the next lottery date
            $nextLotteryDate = $now->addDays(7);
            if ($nextLotteryDate->lte($equbEndDate)) {
                $equb->update([
                    'lottery_date' => $nextLotteryDate,
                    'lottery_round' => $equb->lottery_round + 1
                ]);
            }
    
            Session::flash('success', "Lottery draw completed successfully");
            return back();
    
        } catch (Exception $ex) {
            $msg = "Unable to process your request: " . $ex->getMessage();
            Session::flash('error', $msg);
            return back();
        }
    }
    
    private function drawRandomSeasonId(array $eligibleMembers, int $numberOfWinners): array
    {
        shuffle($eligibleMembers);
        return array_slice($eligibleMembers, 0, $numberOfWinners);
    }
    
    public function show(EqubType $equbType)
    {
        try {
            $userData = Auth::user();
            $equb = $this->equbTypeRepository->getById($equbType);

            return $equb;
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
                $equbTypeDetail = EqubType::where('id', $id)->first();
                $name = $request->input('update_name');
                $round = $request->input('update_round');
                $rote = $request->input('update_rote');
                $type = $request->input('update_type');
                $remark = $request->input('update_remark');
                $lottery_date = $request->input('update_lottery_date');
                $start_date = $request->input('start_date');
                $end_date = $request->input('end_date');
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

                $updated = [
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
                ];
                if ($request->file('icon_update')) {
                    $image = $request->file('icon_update');
                    $imageName = time() . '.' . $image->getClientOriginalExtension();
                    $image->storeAs('public/equbTypeIcons', $imageName);
                    $updated['image'] = 'equbTypeIcons/' . $imageName;
                }
                $oldEqubType = $this->equbTypeRepository->getById($id);
                $updated = $this->equbTypeRepository->update($id, $updated);
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
        } catch (Exception $ex) {
            $msg = "Unable to process your request, Please try again!";
            $type = 'error';
            Session::flash($type, $msg);
            return $msg;
        }
    }
}
