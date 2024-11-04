<?php

namespace App\Console\Commands;

use App\Http\Controllers\api\EqubController;
use App\Http\Controllers\EqubController as ControllersEqubController;
use Illuminate\Console\Command;

class SendEqubEndNotificationsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'equb:notify-due-ends';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send SMS notification to members for Equb end dates due in 24 hours';

    /**
     * Execute the console command.
     *
     * @return int
     */
    protected $equbController;

    public function __construct(ControllersEqubController $equbController)
    {
        parent::__construct();
        $this->equbController = $equbController;
    }
    public function handle()
    {
        $this->equbController->sendEndNotifications();
        $this->info("End Equb notifications sent successfully.");
    }
}
