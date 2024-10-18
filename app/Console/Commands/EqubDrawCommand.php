<?php

namespace App\Console\Commands;

use App\Http\Controllers\Controller;
use App\Models\Equb;
use App\Models\EqubType;
use App\Models\LotteryWinner;
use App\Models\Member;
use App\Models\User;
use App\Service\Notification;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class EqubDrawCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'equb:draw';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Draw Automatic equbs';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $controller = app()->make(Controller::class);
        $now = Carbon::now();
        $members = [];
        $winners = [];
        $winnerUsers = [];
        $checkUserArray = [];
        $equbsMembersToNotify = [];
        $shortcode = config('key.SHORT_CODE');
        $equbTypes = DB::table('equb_types')
            ->whereDate('lottery_date', '=', $now)
            ->where("deleted_at", "=", null)
            ->where("status", "=", "Active")
            ->get();
        foreach ($equbTypes as $equbType) {
            $equbsMembers = DB::table('equbs')
                ->where('equb_type_id', $equbType->id)
                ->where('status', 'Active')
                ->where('check_for_draw', true)
                ->pluck('member_id')
                ->toArray();
            // dd($equbsMembers);
            $equbsMembersToNotify = DB::table('equbs')->where('equb_type_id', $equbType->id)->where('status', 'Active')->pluck('member_id')->toArray();
            $equbs = DB::table('equbs')->where('equb_type_id', $equbType->id)->where('status', 'Active')->get();
            if ($equbsMembers) {
                foreach ($equbsMembers as $member) {
                    $checkUser = LotteryWinner::where('equb_type_id', $equbType->id)->where('member_id', $member)->first();
                    if (!$checkUser && !in_array($member, $checkUserArray)) {
                        array_push($checkUserArray, $member);
                    }
                }
            }
            if (count($checkUserArray) <= 1) {
                foreach ($equbs as $equb) {
                    Equb::where('id', $equb->id)->update(['status' => 'Deactive']);
                }
            }
            if ($equbsMembers) {
                foreach ($equbsMembers as $member) {
                    if (!in_array($member, $members)) {
                        array_push($members, $member);
                    }
                }
                $winner = $this->drawRandomId($members);
                $checkUser = LotteryWinner::where('equb_type_id', $equbType->id)->where('member_id', $winner)->first();
                if ($checkUser) {
                    $filteredIds = array_filter($members, function ($member) use ($winner) {
                        return $member !== $winner;
                    });
                    if ($filteredIds && count($filteredIds) > 0) {
                        $winner = $this->drawRandomId($filteredIds);
                    } else {
                        break;
                    }
                }
                array_push($winners, ["EqubTypeId" => $equbType->id, "EqubTypeName" => $equbType->name, "Winner" => $winner]);
                $members = [];
            }
        }
        if ($winners && count($winners) > 0) {
            foreach ($winners as $winner) {
                $user = Member::where('id', $winner['Winner'])->first();
                array_push($winnerUsers, [
                    "equb_type_id" => $winner['EqubTypeId'],
                    "equb_type_name" => $winner['EqubTypeName'],
                    "member_id" => $user->id,
                    "member_name" => $user->full_name
                ]);
            }
            foreach ($winnerUsers as $winnerUser) {
                LotteryWinner::create($winnerUser);
                $member = DB::table('members')->where('id', $winnerUser['member_id'])->first();
                $notifiedMember = User::where('phone_number', $member->phone)->first();
                $title = "Congratulations";
                $memberBody = "You have been selected as the winner of the equb " . $winnerUser['equb_type_name'] . " For further information please call " . $shortcode;
                Notification::sendNotification($notifiedMember->fcm_id, $memberBody, $title);
                $controller->sendSms($notifiedMember->phone_number, $memberBody);
                foreach ($equbsMembersToNotify as $memberId) {
                    if ($memberId !== $winnerUser['member_id']) {
                        $memberDomain = Member::where('id', $memberId)->first();
                        $user = User::where('phone_number', $memberDomain->phone)->first();
                        $adminTitle = $winnerUser['equb_type_name'] . "'s Lottery Winner";
                        $adminBody = $winnerUser['member_name'] . " has been selected as the winner of the equb " . $winnerUser['equb_type_name'];
                        Notification::sendNotification($user->fcm_id, $adminBody, $adminTitle, $notifiedMember->name);
                        $controller->sendSms($user->phone_number, $adminBody);
                    }
                }
            }
            foreach ($equbTypes as $equbType) {
                $daysToBeAdded = 0;
                if ($equbType->rote === "Daily") {
                    $daysToBeAdded = 1;
                } elseif ($equbType->rote === "Weekly") {
                    $daysToBeAdded = 7;
                } elseif ($equbType->rote === "Biweekly") {
                    $daysToBeAdded = 14;
                } elseif ($equbType->rote === "Monthly") {
                    $daysToBeAdded = 30;
                }
                $updatedLotterDate = $now->copy()->addDays($daysToBeAdded)->format('Y-m-d');
                EqubType::where('id', $equbType->id)->update(['lottery_date' => $updatedLotterDate]);
            }
        }
        $this->info('Draw Complete');
    }
    function drawRandomId(array $ids)
    {
        // Shuffle the array of IDs
        shuffle($ids);

        // Pick a random index and return the corresponding ID
        $randomIndex = array_rand($ids);
        $randomId = $ids[$randomIndex];

        return $randomId;
    }
}
