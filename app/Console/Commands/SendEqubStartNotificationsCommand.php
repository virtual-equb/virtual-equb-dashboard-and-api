<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\EqubController;

class SendEqubStartNotificationsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'equb:notify-due-starts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send SMS notifications to members for Equb start dates due in 24 hours';

    /**
     * Execute the console command.
     *
     * @return int
     */
    protected $equbController;

    public function __construct(EqubController $equbController)
    {
        parent::__construct();
        $this->equbController = $equbController;
    }

    public function handle()
    {
        // $controller = new EqubController();
        // $controller->sendStartNotifications();
        $this->equbController->sendStartNotifications();
        $this->info("Due Equb notifications sent successfully.");
    }
}
