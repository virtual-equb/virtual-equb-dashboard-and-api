<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Request;
use App\Http\Controllers\EqubTypeController;
use App\Models\EqubType;

class DrawSeasonedAutoWinners extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'equb:draw-winners {equbTypeId?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Draws seasoned automatic winners for a given equb type';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        try {
            $equbTypeId = $this->argument('equbTypeId') ?? null;

            if ($equbTypeId) {
                // Process a specific EqubType
                $this->processEqubType($equbTypeId);
            } else {
                // Process all EqubTypes with today's lottery_date
                $this->info('No equbTypeId provided. Processing EqubTypes with today\'s lottery_date...');

                $today = \Carbon\Carbon::now()->startOfDay();
                $equbTypes = \App\Models\EqubType::where('status', 'Active')
                    ->whereDate('lottery_date', $today)
                    ->pluck('id');

                if ($equbTypes->isEmpty()) {
                    $this->warn('No EqubTypes found with today\'s lottery_date.');
                    return;
                }

                foreach ($equbTypes as $id) {
                    $this->processEqubType($id);
                }
            }
        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
            return 1; // Error
        }
    }

    protected function processEqubType($equbTypeId)
    {
        try {
            $this->info("Processing EqubType ID: $equbTypeId");

            // Simulate calling the logic from the controller
            $request = new \Illuminate\Http\Request();
            $request->merge(['equbTypeId' => $equbTypeId]);

            app('App\Http\Controllers\EqubTypeController')->drawSeasonedAutoWinners($request);

            $this->info("Successfully processed EqubType ID: $equbTypeId");
        } catch (\Exception $e) {
            $this->error("Error processing EqubType ID: $equbTypeId - " . $e->getMessage());
        }
    }
}
