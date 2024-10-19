<?php

namespace App\Console\Commands;

use App\Http\Controllers\Controller;
use App\Models\Member;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gaddisa:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        $controller = app()->make(Controller::class);

        $user = Member::first();

        $members = Member::latest()->whereHas('payments', function($query) {
            return $query->where('paid_date', '>=',  Carbon::now()->subDays(3)->format('Y-m-d'));
        })->toSql();

        // dd($members, $user);

        $controller->sendSms($user->phone, "hello");
        // echo "YES";
    }
}
