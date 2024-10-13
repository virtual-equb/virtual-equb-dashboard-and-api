<?php

namespace App\Console\Commands;

use App\Http\Controllers\Controller;
use App\Models\Equb;
use App\Models\EqubType;
use App\Models\Payment;
use App\Models\User;
use App\Service\Notification;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class UnpaidPaymentsCheckCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:unpaidPayments';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Checks for members that have unpaid payments and notifies them';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $now = Carbon::now();
        $controller = app()->make(Controller::class);
        $equbs = Equb::where("deleted_at", "=", null)
            ->where("status", "=", "Active")
            ->with('equbType')
            ->get();
        $shortcode = config('key.SHORT_CODE');
        foreach ($equbs as $equb) {
            $startDate = Carbon::parse($equb->start_date);
            $differenceInDays = $startDate->diffInDays($now);
            if ($differenceInDays > 0) {
                $equbRote = EqubType::where('id', $equb->equb_type_id)->pluck('rote')->first();
                $totalPaid = Payment::where('member_id', $equb->member_id)->where('equb_id', $equb->id)->sum('amount');
                $equbAmount = $equb->amount;
                $totalToBePaid = 0;
                if ($equbRote == "Daily") {
                    $totalToBePaid = $equbAmount * $differenceInDays;
                } elseif ($equbRote == "Weekly") {
                    $intDiffInDays = (int) ($differenceInDays / 7);
                    if ($intDiffInDays > 0) {
                        $totalToBePaid = $equbAmount * $intDiffInDays;
                    }
                } elseif ($equbRote == "Biweekly") {
                    $intDiffInDays = (int) ($differenceInDays / 15);
                    if ($intDiffInDays > 0) {
                        $totalToBePaid = $equbAmount * $intDiffInDays;
                    }
                } elseif ($equbRote == "Monthly") {
                    $intDiffInDays = (int) ($differenceInDays / 30);
                    if ($intDiffInDays > 0) {
                        $totalToBePaid = $equbAmount * $intDiffInDays;
                    }
                }
                if ($totalPaid != $totalToBePaid) {
                    $member = DB::table('members')->where('id', $equb->member_id)->first();
                    $notifiedMember = User::where('phone_number', $member->phone)->first();
                    $title = "Unpaid Payment";
                    $equbName = $equb->equbType->name;
                    $memberBody = "You have an unpaid equb amount of ETB $totalToBePaid for the equb $equbName" . " For further information please call " . $shortcode;
                    Notification::sendNotification($notifiedMember->fcm_id, $memberBody, $title);
                    $controller->sendSms($notifiedMember->phone_number, $memberBody);
                }
            }
        }
        $this->info('Unpaid Ekub Amount Check Complete');
    }
}
