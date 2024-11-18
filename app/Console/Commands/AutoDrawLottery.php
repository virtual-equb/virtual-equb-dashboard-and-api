<?php

namespace App\Console\Commands;

use App\Models\EqubType;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;

class AutoDrawLottery extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'equb:auto-draw-lottery';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically draw winners if lottery_date matches the current date.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $now = now()->startOfDay(); // current Date

        // Fetch all equbTypes where the lottery_date matches today
        $equbs = EqubType::whereDate('lottery_date', $now)->get();

        if ($equbs->isEmpty()) {
            $this->info("No Equb lotteries are scheduled for today.");
            return;
        }

        foreach ($equbs as $equb) {
            $this->info("processing Equb: {$equb->name}");

            // Call the drawAutoWinners method from the controller
            $equbController = App::make('App\Http\Controllers\EqubTypeController');
            $request = request();
            $request->merge(['equbTypeId' => $equb->id]);

            try {
                $response = $equbController->drawAutoWinners($request);

                if ($response->getStatusCode() === 200) {
                    $this->info("Lottery draw completed successfully for Equb: {$equb->name}");
                } else {
                    $this->error("Failed to complete the draw for Equb: {$equb->name}");
                }
            } catch (Exception $ex) {
                $this->error("Error processing Equb {$equb->name}: " . $ex->getMessage());
            }
        }
    }
}
