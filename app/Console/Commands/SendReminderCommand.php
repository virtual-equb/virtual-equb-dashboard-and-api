<?php

namespace App\Console\Commands;

use App\Http\Controllers\Controller;
use App\Models\Member;
use Illuminate\Console\Command;

class SendReminderCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:reminders';

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
        $shortcode = config('key.SHORT_CODE');
        $members = Member::get();
        foreach ($members as $member) {
            if (count($member->equbs) <= 0) {
                $message = "Thanks for joining VirtualEkub. Please subscribe to an equb and start your journey with us. For further information please call $shortcode";
                $controller->sendSms($member->phone, $message);
            }
        }
        $this->info('Reminders sent');
    }
}
