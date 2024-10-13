<?php

namespace App\Console\Commands;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Service\Notification;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class LotteryDateCheckCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:lotterydate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Checks for records that have lottery dates five days away';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $now = Carbon::now();
        $fiveDaysFromNow = $now->copy()->addDays(5);
        $controller = app()->make(Controller::class);
        $shortcode = config('key.SHORT_CODE');
        $equbs = DB::table('equbs')
            ->whereDate('lottery_date', '>', $now)
            ->whereDate('lottery_date', '<=', $fiveDaysFromNow)
            ->where("deleted_at", "=", null)
            ->where("status", "=", 'Active')
            ->get();
        if ($equbs) {
            foreach ($equbs as $equb) {
                //Equb End date
                $lotteryDate = $equb->lottery_date;
                //Days till equb expire
                $differenceInDays = $now->diffInDays($lotteryDate);
                //Member from members table
                $member = DB::table('members')->where('id', $equb->member_id)->first();
                if ($member) {
                    $notifiedMember = User::where('phone_number', $member->phone)->first();
                    if ($notifiedMember) {
                        //Member from users table
                        $s = $differenceInDays == 1 ? "" : "s";
                        $memberBody = "Your equb's lottery will come out in $differenceInDays day" . $s. " For further information please call " . $shortcode;
                        $title = "$notifiedMember->name's Equb Lottery Date";
                        $servicedd = Notification::sendNotification($notifiedMember->fcm_id, $memberBody, $title);
                        $controller->sendSms($notifiedMember->phone_number, $memberBody);
                        //Admin and equb collectors
                        $users = User::where('role', 'admin')->orWhere('role', 'equb_collector')->get();
                        foreach ($users as $user) {
                            $body = "$notifiedMember->name's equb lottery will come out in $differenceInDays days";
                            $servicedd = Notification::sendNotification($user->fcm_id, $body, $notifiedMember->name, $title);
                            $controller->sendSms($user->phone_number, $body);
                        }
                    }
                }
            }

            $this->info('Lottery date check completed.');
        }
    }
}
