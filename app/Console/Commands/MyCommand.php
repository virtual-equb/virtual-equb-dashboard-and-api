<?php

namespace App\Console\Commands;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\NewNotification;
use App\Service\Notification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Http;

class MyCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    // protected $signature = 'command:name';
    protected $signature = 'check:enddate';


    /**
     * The console command description.
     *
     * @var string
     */
    // protected $description = 'Command description';
    protected $description = 'Checks for records that have an end date five days away';


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
            ->whereDate('end_date', '>=', $now)
            ->whereDate('end_date', '<=', $fiveDaysFromNow)
            ->where("deleted_at", "=", null)
            ->where("status", "=", "Active")
            ->get();
        if ($equbs) {
            foreach ($equbs as $equb) {
                //Equb End date
                $endDate = Carbon::parse($equb->end_date);
                //Days till equb expire
                $differenceInDays = $now->diffInDays($endDate);
                //Member from members table
                $member = DB::table('members')->where('id', $equb->member_id)->first();
                if ($member) {
                    $notifiedMember = User::where('phone_number', $member->phone)->first();
                    if ($notifiedMember) {
                        //Member from users table
                        $memberBody = "Your equb will expire in $differenceInDays days". " For further information please call " . $shortcode;
                        $title = "$notifiedMember->name's Equb End Date";
                        $servicedd = Notification::sendNotification($notifiedMember->fcm_id, $memberBody, $title);
                        $controller->sendSms($notifiedMember->phone_number, $memberBody);
                        //Admin and equb collectors
                        $users = User::where('role', 'admin')->orWhere('role', 'equb_collector')->get();
                        foreach ($users as $user) {
                            $body = "$notifiedMember->name's equb will expire in $differenceInDays days";
                            $servicedd = Notification::sendNotification($user->fcm_id, $body, $notifiedMember->name, $title);
                            $controller->sendSms($user->phone_number, $body);
                        }
                    }
                }
            }

            $this->info('End date check completed.');
        }
    }
    // public function sendNotification($fcm_id, $body)
    // {
    //     $headers = [
    //         'Authorization: key=' . env('FIREBASE_SERVER_KEY'),
    //         'Content-Type: application/json',
    //     ];
    //     $data = [
    //         'notification' => [
    //             'title' => 'New Notification',
    //             'body' => $body
    //         ],
    //         'to' => $fcm_id,
    //     ];
    //     $ch = curl_init();
    //     curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
    //     curl_setopt($ch, CURLOPT_POST, true);
    //     curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    //     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    //     curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // skip SSL verification (not recommended for production)
    //     curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    //     $response = curl_exec($ch);
    //     curl_close($ch);
    // }
}
