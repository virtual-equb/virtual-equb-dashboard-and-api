<?php

namespace App\Console\Commands;

use App\Models\EqubType;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CheckIfAutoEqubTypesStartDatePassed extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:equbtype';

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
        $now = Carbon::now();
        $equbTypes = EqubType::where('type', 'Automatic')
            ->where('start_date', '<=', $now)
            ->where('remaining_quota', '>', 0)
            ->get();
        if ($equbTypes) {
            foreach ($equbTypes as $equbType) {
                $equbTypeData = EqubType::where('id', $equbType->id)->first();
                $startDate = Carbon::parse($equbType->start_date);
                $newStartDate = $startDate->addDays(1);

                $endDate = Carbon::parse($equbType->end_date);
                $newEndDate = $endDate->addDays(1);

                $lotteryDate = Carbon::parse($equbType->lottery_date);
                $newLotteryDate = $lotteryDate->addDays(1);

                $equbTypeData->start_date = $newStartDate;
                $equbTypeData->end_date = $newEndDate;
                $equbTypeData->lottery_date = $newLotteryDate;
                $equbTypeData->save();
            }
        }
        $this->info('Equb Type Check Complete');
    }
}
