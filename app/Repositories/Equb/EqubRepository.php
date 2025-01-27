<?php


namespace App\Repositories\Equb;

use Carbon\Carbon;
use App\Models\Equb;
use App\Models\Payment;
use App\Models\EqubType;
use App\Models\LotteryWinner;
use Illuminate\Support\Facades\DB;

class EqubRepository implements IEqubRepository
{
    private $model;
    private $limit;
    public function __construct(Equb $equb)
    {
        $this->model = $equb;
        $this->limit = 50;
    }

    public function getDailyStats()
    {
        $date = Carbon::today();

        $paidAmount = Payment::whereDate('created_at', $date)->sum('amount');
        $expectedAmount = Equb::whereDate('start_date', '<=', $date)
            ->whereDate('end_date', '>=', $date)
            ->sum('amount');
        $unpaidAmount = $expectedAmount - $paidAmount;

        return [
            'expected' => $expectedAmount,
            'paid' => $paidAmount,
            'unpaid' => max($unpaidAmount, 0),
        ];
    }

    public function getWeeklyStats()
    {
        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek = Carbon::now()->endOfWeek();

        $paidAmount = Payment::whereBetween('created_at', [$startOfWeek, $endOfWeek])->sum('amount');
        $expectedAmount = Equb::whereBetween('start_date', [$startOfWeek, $endOfWeek])
                ->sum('amount');

        $unpaidAmount = $expectedAmount - $paidAmount;

        return [
            'expected' => $expectedAmount,
            'paid' => $paidAmount,
            'unpaid' => max($unpaidAmount, 0),
        ];
    }

    public function getMonthlyStats()
    {
        $currentMonth = Carbon::now()->month;

        $paidAmount = Payment::whereMonth('created_at', $currentMonth)->sum('amount');
        $expectedAmount = Equb::whereMonth('start_date', '<=', $currentMonth)
                ->whereMonth('end_date', '>=', $currentMonth)
                ->sum('amount');

        $unpaidAmount = $expectedAmount - $paidAmount;

        return [
            'expected' => $expectedAmount,
            'paid' => $paidAmount,
            'unpaid' => max($unpaidAmount, 0),
        ];
    }

    public function getYearlyStats()
    {
        $currentYear = Carbon::now()->year;

        $paidAmount = Payment::whereYear('created_at', $currentYear)->sum('amount');
        $expectedAmount = Equb::whereYear('start_date', '<=', $currentYear)
                ->whereYear('end_date', '>=', $currentYear)
                ->sum('amount');

        $unpaidAmount = $expectedAmount - $paidAmount;

        return [
            'expected' => $expectedAmount,
            'paid' => $paidAmount,
            'unpaid' => max($unpaidAmount, 0),
        ];
    }

    public function getStatusById($id)
    {
        return $this->model->find($id);
    }
    public function getMemberByEqubType($id)
    {
        // Use where to filter members by equb_type_id
        return $this->model->with('member')->where('equb_type_id', $id)->get();
    }
    public function getMemberIdById($id)
    {

        return $this->model->where('id', $id)
            ->pluck('member_id');
    }
    public function getUnPaidLotteryCount($member_id)
    {
        return $this->model->where('status', 'Active')
            ->whereHas('equbTakers', fn ($q) =>  $q->where('status', "!=", "paid")->orWhere('status', "!=", "void"))
            ->whereBetween('lottery_date', [Carbon::now()->subDays(180), Carbon::now()])
            // ->whereRaw('NOT FIND_IN_SET(member_id,"'.$member_id.'")')
            // ->where('lottery_date','<=',Carbon::now())
            ->count();
    }
    public function getUnPaidLotteryByEqubTypeCount($member_id, $equbType)
    {
        // return $this->model->where('status', 'Active')
        //     ->whereHas('equbTakers', fn ($q) =>  $q->where('status', "!=", "paid")->orWhere('status', "!=", "void"))
        //     ->whereBetween('lottery_date', [Carbon::now()->subDays(180), Carbon::now()])
        //     // ->whereRaw('NOT FIND_IN_SET(member_id,"'.$member_id.'")')
        //     // ->where('lottery_date','<=',Carbon::now())
        //     ->count();
        if ($equbType != 'all') {
            return $this->model->where('status', 'Active')
                ->whereHas('equbType', fn ($q) =>  $q->where('id', "=", $equbType))
                ->whereHas('equbTakers', fn ($q) =>  $q->where('status', "!=", "paid")->orWhere('status', "!=", "void"))
                ->whereBetween('lottery_date', [Carbon::now()->subDays(180), Carbon::now()])
                // ->whereRaw('NOT FIND_IN_SET(member_id,"'.$member_id.'")')
                // ->where('lottery_date','<=',Carbon::now())
                ->count();
        } else {
            return $this->model->where('status', 'Active')
                ->whereHas('equbTakers', fn ($q) =>  $q->where('status', "!=", "paid")->orWhere('status', "!=", "void"))
                ->whereBetween('lottery_date', [Carbon::now()->subDays(180), Carbon::now()])
                // ->whereRaw('NOT FIND_IN_SET(member_id,"'.$member_id.'")')
                // ->where('lottery_date','<=',Carbon::now())
                ->count();
        }
    }
    public function getUnPaidLotteryByLotteryDateCount($member_id, $lotteryDate, $equbType)
    {
        // return $this->model->where('status', 'Active')
        //     ->whereHas('equbTakers', fn ($q) =>  $q->where('status', "!=", "paid")->orWhere('status', "!=", "void"))
        //     ->whereRaw("FIND_IN_SET(?, lottery_date) > 0", [$lotteryDate])
        //     // ->whereBetween('lottery_date', [Carbon::now()->subDays(180), Carbon::now()])
        //     // ->whereRaw('NOT FIND_IN_SET(member_id,"'.$member_id.'")')
        //     // ->where('lottery_date','<=',Carbon::now())
        //     ->count();
        if ($equbType != 'all') {
            return $this->model->where('status', 'Active')
                ->whereHas('equbType', fn ($q) =>  $q->where('id', "=", $equbType))
                ->whereHas('equbTakers', fn ($q) =>  $q->where('status', "!=", "paid")->orWhere('status', "!=", "void"))
                ->whereRaw("FIND_IN_SET(?, lottery_date) > 0", [$lotteryDate])
                ->count();
        } else {
            return $this->model->where('status', 'Active')
                ->whereHas('equbTakers', fn ($q) =>  $q->where('status', "!=", "paid")->orWhere('status', "!=", "void"))
                ->whereRaw("FIND_IN_SET(?, lottery_date) > 0", [$lotteryDate])
                ->count();
        }
    }
    public function getUnPaidLottery($member_id, $offset)
    {
        return $this->model->where('status', 'Active')
            ->whereBetween('lottery_date', [Carbon::now()->subDays(180), Carbon::now()])
            ->whereHas('equbTakers', fn ($q) =>  $q->where('status', "!=", "paid")->Where('status', "!=", "void"))
            ->offset($offset)
            ->limit($this->limit)
            ->with('member', 'equbType', 'equbTakers')
            ->get();
            
    }
    public function updateUnPaidLotteryToPaid($member_id, $offset)
    {
        // Find and update unpaid lotteries
        return $this->model->where('status', 'Active')
            ->whereBetween('lottery_date', [Carbon::now()->subDays(180), Carbon::now()])
            ->whereHas('equbTakers', function ($q) {
                $q->where('status', "!=", "paid")->where('status', "!=", "void");
            })
            ->offset($offset)
            ->limit($this->limit)
            ->update(['status' => 'paid']);
    }
    public function getUnPaidLotteryByEqubType($member_id, $equbType, $offset)
    {
        // return $this->model->where('status', 'Active')
        //     ->whereBetween('lottery_date', [Carbon::now()->subDays(180), Carbon::now()])
        //     ->whereHas('equbTakers', fn ($q) =>  $q->where('status', "!=", "paid")->Where('status', "!=", "void"))
        //     ->offset($offset)
        //     ->limit($this->limit)
        //     ->with('member', 'equbType', 'equbTakers')
        //     ->get();
        if ($equbType != 'all') {
            return $this->model->where('status', 'Active')
                ->whereBetween('lottery_date', [Carbon::now()->subDays(180), Carbon::now()])
                ->whereHas('equbType', fn ($q) =>  $q->where('id', "=", $equbType))
                ->whereHas('equbTakers', fn ($q) =>  $q->where('status', "!=", "paid")->Where('status', "!=", "void"))
                ->offset($offset)
                ->limit($this->limit)
                ->with('member', 'equbType', 'equbTakers')
                ->get();
        } else {
            return $this->model->where('status', 'Active')
                ->whereBetween('lottery_date', [Carbon::now()->subDays(180), Carbon::now()])
                ->whereHas('equbTakers', fn ($q) =>  $q->where('status', "!=", "paid")->Where('status', "!=", "void"))
                ->offset($offset)
                ->limit($this->limit)
                ->with('member', 'equbType', 'equbTakers')
                ->get();
        }
    }
    public function getUnPaidLotteryByLotteryDate($member_id, $lotteryDate, $offset, $equbType)
    {
        // return $this->model->where('status', 'Active')
        //     // Assuming $lotteryDate is properly formatted for your query
        //     ->whereRaw("FIND_IN_SET(?, lottery_date) > 0", [$lotteryDate])
        //     ->whereHas('equbTakers', function ($q) {
        //         $q->where('status', "!=", "paid")
        //             ->where('status', "!=", "void");
        //     })
        //     ->offset($offset)
        //     ->limit($this->limit)
        //     ->with('member', 'equbType', 'equbTakers')
        //     ->get();
        if ($equbType != 'all') {
            return $this->model->where('status', 'Active')
                // Assuming $lotteryDate is properly formatted for your query
                ->where('equb_type_id', $equbType)
                ->whereRaw("FIND_IN_SET(?, lottery_date) > 0", [$lotteryDate])
                ->whereHas('equbTakers', function ($q) {
                    $q->where('status', "!=", "paid")
                        ->where('status', "!=", "void");
                })
                ->offset($offset)
                ->limit($this->limit)
                ->with('member', 'equbType', 'equbTakers')
                ->get();
        } else {
            return $this->model->where('status', 'Active')
                // Assuming $lotteryDate is properly formatted for your query
                ->whereRaw("FIND_IN_SET(?, lottery_date) > 0", [$lotteryDate])
                ->whereHas('equbTakers', function ($q) {
                    $q->where('status', "!=", "paid")
                        ->where('status', "!=", "void");
                })
                ->offset($offset)
                ->limit($this->limit)
                ->with('member', 'equbType', 'equbTakers')
                ->get();
        }
    }
    public function getReservedLotteryDatesCount($dateFrom, $dateTo, $memberId, $equbType)
    {
        // return $this->model->where('status', 'Active')
        //     ->whereBetween('lottery_date', [$dateFrom, $dateTo])
        //     ->count();
        if ($equbType != 'all') {
            return $this->model->where('status', 'Active')
                ->where('equb_type_id', $equbType)
                ->whereBetween('lottery_date', [$dateFrom, $dateTo])
                ->count();
        } else {
            return $this->model->where('status', 'Active')
                ->whereBetween('lottery_date', [$dateFrom, $dateTo])
                ->count();
        }
    }
    public function getReservedLotteryDates($dateFrom, $dateTo, $memberId, $offset, $equbType)
    {
        // dd($equbType);
        // return $this->model->where('status', 'Active')
        //     //->whereRaw('NOT FIND_IN_SET(member_id,"' . $member_id . '")')
        //     ->whereBetween('lottery_date', [$dateFrom, $dateTo])
        //     // ->whereDate('lottery_date', '>=', $dateFrom)
        //     // ->whereDate('lottery_date', '<=', $dateTo)
        //     ->offset($offset)
        //     ->limit($this->limit)
        //     ->with('member', 'equbType')
        //     ->get();
        if ($equbType != 'all') {
            return $this->model->where('status', 'Active')
                ->where('equb_type_id', $equbType)
                ->whereBetween('lottery_date', [$dateFrom, $dateTo])
                ->offset($offset)
                ->limit($this->limit)
                ->with('member', 'equbType')
                ->get();
        } else {
            return $this->model->where('status', 'Active')
                ->whereBetween('lottery_date', [$dateFrom, $dateTo])
                ->offset($offset)
                ->limit($this->limit)
                ->with('member', 'equbType')
                ->get();
        }
    }
    public function getDailyPaid($equb_id)
    {
        return $this->model->where('status', 'Active')->where('id', $equb_id)->pluck('amount')->first();
    }
    public function getLotteryDate()
    {
        return $this->model->where('status', 'Active')->pluck('lottery_date');
    }
    public function getRemainingLotteryAmount($id)
    {
        return $this->model->where('status', 'Active')->with('equbTakers')->find($id);
    }

    public function getAll()
    {
        return $this->model->with('equbType.mainEqub', 'payments')->get();
        
        // return EqubType::with('mainEqub')->get();
    }

    public function getByDate($dateFrom, $dateTo, $equbType, $offset)
    {
        // return $this->model->where('status', 'Active')
        //     ->whereDate('start_date', '>=', $dateFrom)
        //     ->whereDate('end_date', '<=', $dateTo)
        //     ->offset($offset)
        //     ->limit($this->limit)
        //     ->with('member')->get();
        if ($equbType != 'all') {
            return $this->model
                ->where('status', 'Active')
                ->where('equb_type_id', $equbType)
                ->whereDate('start_date', '>=', $dateFrom)
                ->whereDate('end_date', '<=', $dateTo)
                ->with('member')
                ->offset($offset)
                ->limit($this->limit)
                ->get();
        } else {
            return $this->model
                ->where('status', 'Active')
                ->whereDate('start_date', '>=', $dateFrom)
                ->whereDate('end_date', '<=', $dateTo)
                ->with('member')
                ->offset($offset)
                ->limit($this->limit)
                ->get();
        }
    }
    public function getByPaymentMethod($dateFrom, $dateTo, $equbType, $offset)
    {
        if ($equbType != 'all') {
            return $this->model
                ->where('status', 'Active')
                ->where('equb_type_id', $equbType)
                ->whereDate('start_date', '>=', $dateFrom)
                ->whereDate('end_date', '<=', $dateTo)
                ->with('member')
                ->offset($offset)
                ->limit($this->limit)
                ->get();
        } else {
            return $this->model
                ->where('status', 'Active')
                ->whereDate('start_date', '>=', $dateFrom)
                ->whereDate('end_date', '<=', $dateTo)
                ->with('member')
                ->offset($offset)
                ->limit($this->limit)
                ->get();
        }
    }
    public function getUnPaidByDate($dateFrom, $dateTo, $equbId, $offset, $equbType)
    {
        // return $this->model->where('status', 'Active')
        //     ->whereRaw('NOT FIND_IN_SET(id,"' . $equbId . '")')
        //     ->whereDate('start_date', '<=', Carbon::now())
        //     ->whereDate('end_date', '>=', Carbon::now())
        //     ->offset($offset)
        //     ->limit($this->limit)
        //     ->with('member')->get();
        if ($equbType != 'all') {
            return $this->model->where('status', 'Active')
                ->where('equb_type_id', $equbType)
                ->whereRaw('NOT FIND_IN_SET(id,"' . $equbId . '")')
                ->whereDate('start_date', '<=', Carbon::now())
                ->whereDate('end_date', '>=', Carbon::now())
                ->offset($offset)
                ->limit($this->limit)
                ->with('member')->get();
        } else {
            return $this->model->where('status', 'Active')
                ->whereRaw('NOT FIND_IN_SET(id,"' . $equbId . '")')
                ->whereDate('start_date', '<=', Carbon::now())
                ->whereDate('end_date', '>=', Carbon::now())
                ->offset($offset)
                ->limit($this->limit)
                ->with('member')->get();
        }
    }

    public function getCountByDate($dateFrom, $dateTo, $equbType)
    {
        // return $this->model->where('status', 'Active')
        //     ->whereDate('start_date', '>=', $dateFrom)
        //     ->whereDate('end_date', '<=', $dateTo)
        //     ->count();
        if ($equbType != 'all') {
            return $this->model
                ->where('status', 'Active')
                ->where('equb_type_id', $equbType)
                ->whereDate('start_date', '>=', $dateFrom)
                ->whereDate('end_date', '<=', $dateTo)
                ->count();
        } else {
            return $this->model
                ->where('status', 'Active')
                ->whereDate('start_date', '>=', $dateFrom)
                ->whereDate('end_date', '<=', $dateTo)
                ->count();
        }
    }
    public function getCountUnPaidByDate($dateFrom, $dateTo, $equbId, $equbType)
    {
        // return $this->model->where('status', 'Active')
        //     ->whereRaw('NOT FIND_IN_SET(id,"' . $equbId . '")')
        //     ->whereDate('start_date', '<=', Carbon::now())
        //     ->whereDate('end_date', '>=', Carbon::now())
        //     ->count();
        if ($equbType != 'all') {
            return $this->model->where('status', 'Active')
                ->where('equb_type_id', $equbType)
                ->whereRaw('NOT FIND_IN_SET(id,"' . $equbId . '")')
                ->whereDate('start_date', '<=', Carbon::now())
                ->whereDate('end_date', '>=', Carbon::now())
                ->count();
        } else {
            return $this->model->where('status', 'Active')
                ->whereRaw('NOT FIND_IN_SET(id,"' . $equbId . '")')
                ->whereDate('start_date', '<=', Carbon::now())
                ->whereDate('end_date', '>=', Carbon::now())
                ->count();
        }
    }
    public function getWithDateAndMember($dateFrom, $dateTo, $member_id)
    {
        return $this->model->where('status', 'Active')
            ->whereDate('start_date', '>=', $dateFrom)
            ->whereDate('end_date', '<=', $dateTo)
            ->where('member_id', $member_id)
            ->with('member')->get();
    }

    public function getByLotteryDate($dateFrom, $dateTo, $offset)
    {
        // return $this->model->where('status','Active')->where([
        //                          ['lottery_date', '>=', $dateFrom],
        //                          ['lottery_date', '<=', $dateTo]
        //                      ])
        return $this->model->where('status', 'Active')
            ->whereRaw('FIND_IN_SET("' . $dateFrom . '",lottery_date)')
            ->offset($offset)
            ->limit($this->limit)
            ->with('member')->get();
    }

    public function getCountByLotteryDate($dateFrom, $dateTo)
    {
        return $this->model->where('status', 'Active')->where([
            ['lottery_date', '>=', $dateFrom],
            ['lottery_date', '<=', $dateTo]
        ])->count();
    }

    public function getExpected($equbTypeId)
    {
        // return $this->model
        //     ->whereDate('start_date', '<=', Carbon::now())
        //     // ->whereDate('end_date', '>=', Carbon::now())
        //     ->whereIn('equb_type_id', $equbTypeId)
        //     ->where('status', 'Active')
        //     ->whereIn('id', function ($query) {
        //         // Subquery to filter equbs by remaining payment not equal to 0 in equb_takers table
        //         $query->selectRaw('equb_id')
        //             ->from('equb_takers')
        //             ->whereRaw('equb_takers.equb_id = equbs.id')
        //             ->where('remaining_payment', '>', 0)
        //             ->groupBy('equb_id');
        //     })
        //     ->selectRaw("SUM(amount) as expected")
        //     ->groupBy('equb_type_id')
        //     ->orderBy('equb_type_id', 'asc')
        //     ->get();
        return DB::table('equbs')
            ->selectRaw('equb_type_id, SUM(amount) as expected')
            ->whereDate('start_date', '<=', Carbon::now())
            ->where('status', 'Active')
            ->whereIn('equb_type_id', $equbTypeId)
            ->whereIn('id', function ($query) {
                $query->selectRaw('equb_id')
                    ->from('equb_takers')
                    ->whereRaw('equb_takers.equb_id = equbs.id')
                    ->where('remaining_payment', '>', 0)
                    ->groupBy('equb_id');
            })
            ->groupBy('equb_type_id')
            ->orderBy('equb_type_id', 'asc')
            ->get();
    }

    public function getAutomaticExpected($equbTypeId)
    {
        return $this->model
            ->whereDate('start_date', '<=', Carbon::now())
            ->whereIn('equb_type_id', $equbTypeId)
            ->where('status', 'Active')
            ->whereIn('id', function ($query) {
                $query->selectRaw('equb_id')
                    ->from('equb_takers')
                    ->whereRaw('equb_takers.equb_id = equbs.id')
                    ->where('remaining_payment', '>', 0)
                    ->groupBy('equb_id');
            })
            ->selectRaw("SUM(amount) as expected")
            ->whereHas('equbType', function ($query) {
                $query->where('type', 'Automatic');
            })
            ->groupBy('equb_type_id')
            ->orderBy('equb_type_id', 'asc')
            ->get();
    }

    public function getManualExpected($equbTypeId)
    {
        return $this->model
            ->whereDate('start_date', '<=', Carbon::now())
            ->whereIn('equb_type_id', $equbTypeId)
            ->where('status', 'Active')
            ->whereIn('id', function ($query) {
                $query->selectRaw('equb_id')
                    ->from('equb_takers')
                    ->whereRaw('equb_takers.equb_id = equbs.id')
                    ->where('remaining_payment', '>', 0)
                    ->groupBy('equb_id');
            })
            ->selectRaw("SUM(amount) as expected")
            ->whereHas('equbType', function ($query) {
                $query->where('type', 'Manual');
            })
            ->groupBy('equb_type_id')
            ->orderBy('equb_type_id', 'asc')
            ->get();
    }

    public function getExpectedByLotteryDate($lotteryDate)
    {
        return $this->model
            ->where('start_date', '<=', $lotteryDate)
            ->whereDate('end_date', '>=', $lotteryDate)
            ->where('status', 'Active')
            ->selectRaw("SUM(amount) as expected")
            ->get();
    }

    public function getActiveMember()
    {
        return $this->model->where('status', 'Active')->distinct('member_id')->count('member_id');
    }

    public function tudayPaidMember()
    {
        // return $this->model->where('status', 'Active')->whereRaw('FIND_IN_SET("' . Carbon::today()->format('Y-m-d') . '",lottery_date)')->with('member')->get();
        $today = Carbon::today()->format('Y-m-d');
        
        return $this->model
            ->where('status', 'Active')
            ->whereRaw('FIND_IN_SET(?, lottery_date)', [$today])
            ->with('member')
            ->get();
    }
    public function tudayEqubTypePaidMember($equbTypeId)
    {
        return $this->model
            ->where('status', 'Active')
            ->where('equb_type_id', $equbTypeId)
            ->whereRaw('FIND_IN_SET("' . Carbon::today()->format('Y-m-d') . '",lottery_date)')
            ->with('member')
            ->get();
    }
    public function todayPaidEqubTypesAutomatic()
    {
        return EqubType::whereRaw('FIND_IN_SET("' . Carbon::today()->format('Y-m-d') . '",lottery_date)')->get();
    }
    public function todayPaidMembersAutomatic($equbTypeId)
    {
        return Equb::whereRaw('FIND_IN_SET("' . $equbTypeId . '",equb_type_id)')->with('member')->get();
    }
    public function getEqubAmount($member_id, $equb_id)
    {
        return $this->model->where('status', 'Active')->where('member_id', $member_id)->where('id', $equb_id)->pluck('amount')->first();
    }
    public function getTotalEqubAmount($equb_id)
    {
        return $this->model->where('status', 'Active')->select('total_amount')->where('id', $equb_id)->pluck('total_amount')->first();
    }
    public function getExpectedTotal()
    {
        // return $this->model
        //     ->whereDate('start_date', '<=', Carbon::today())
        //     ->whereDate('end_date', '>=', Carbon::today())
        //     ->where('status', 'Active')
        //     // ->whereIn('id', function ($query) {
        //     //     // Subquery to filter equbs by remaining payment not equal to 0 in equb_takers table
        //     //     $query->selectRaw('equb_id')
        //     //         ->from('equb_takers')
        //     //         ->whereRaw('equb_takers.equb_id = equbs.id')
        //     //         ->where('remaining_payment', '>', 0)
        //     //         ->groupBy('equb_id');
        //     // })
        //     ->sum('amount');
            return  DB::table('equbs')
            ->join('equb_types', 'equbs.equb_type_id', '=', 'equb_types.id')
            ->where('equbs.status', 'Active')
            ->whereDate('equbs.start_date', '<=', Carbon::today())
            ->whereDate('equbs.end_date', '>=', Carbon::today())
            // ->where('equb_types.rote', 'Daily')
            ->whereIn('id', function ($query) {
                $query->selectRaw('equb_id')
                    ->from('equb_takers')
                    ->whereRaw('equb_takers.equb_id = equbs.id')
                    ->where('remaining_payment', '>', 0)
                    ->groupBy('equb_id');
            })
            ->sum('equbs.amount');
    }
    public function getAutomaticExpectedTotal()
    {
        return $this->model
            ->whereDate('start_date', '<=', Carbon::today())
            ->whereDate('end_date', '>=', Carbon::today())
            ->where('status', 'Active')
            ->whereIn('id', function ($query) {
                $query->selectRaw('equb_id')
                    ->from('equb_takers')
                    ->whereRaw('equb_takers.equb_id = equbs.id')
                    ->where('remaining_payment', '>', 0)
                    ->groupBy('equb_id');
            })
            ->whereHas('equbType', function ($query) {
                $query->where('type', 'Automatic');
            })
            ->sum('amount');
    }
    public function getManualExpectedTotal()
    {
        return $this->model
            ->whereDate('start_date', '<=', Carbon::today())
            ->whereDate('end_date', '>=', Carbon::today())
            ->where('status', 'Active')
            ->whereIn('id', function ($query) {
                $query->selectRaw('equb_id')
                    ->from('equb_takers')
                    ->whereRaw('equb_takers.equb_id = equbs.id')
                    ->where('remaining_payment', '>', 0)
                    ->groupBy('equb_id');
            })
            ->whereHas('equbType', function ($query) {
                $query->where('type', 'Manual');
            })
            ->sum('amount');
    }
    public function getEqubTypeExpectedTotal($equbTypeId)
    {
        return $this->model
            ->whereDate('start_date', '<=', Carbon::today())
            // ->whereDate('end_date', '>=', Carbon::today())
            ->where('status', 'Active')
            ->where('equb_type_id', $equbTypeId)
            ->whereIn('id', function ($query) {
                // Subquery to filter equbs by remaining payment not equal to 0 in equb_takers table
                $query->selectRaw('equb_id')
                    ->from('equb_takers')
                    ->whereRaw('equb_takers.equb_id = equbs.id')
                    ->where('remaining_payment', '>', 0)
                    ->groupBy('equb_id');
            })
            ->sum('amount');
    }
    public function getTotalAmount()
    {
        return $this->model->where('status', 'Active')->sum('amount');
    }
    public function getExpectedAmount()
    {
        return $this->model->where('status', 'Active')
            ->whereDate('start_date', '<=', Carbon::now())
            // ->whereDate('end_date', '>=', Carbon::now())
            ->get();
    }
    public function getAutomaticExpectedAmount()
    {
        return $this->model->where('status', 'Active')
            ->whereDate('start_date', '<=', Carbon::now())
            ->whereHas('equbType', function ($query) {
                $query->where('type', 'Automatic');
            })
            ->get();
    }
    public function getManualExpectedAmount()
    {
        return $this->model->where('status', 'Active')
            ->whereDate('start_date', '<=', Carbon::now())
            ->whereHas('equbType', function ($query) {
                $query->where('type', 'Manual');
            })
            ->get();
    }
    public function getEqubTypeExpectedAmount($equbTypeId)
    {
        return $this->model->where('status', 'Active')
            ->where('equb_type_id', $equbTypeId)
            ->whereDate('start_date', '<=', Carbon::now())
            // ->whereDate('end_date', '>=', Carbon::now())
            ->get();
    }
    public function getExpectedBackPayment()
    {
        return $this->model->where('status', 'Active')
            ->whereDate('start_date', '<=', Carbon::now()->subDays(7))
            ->whereDate('end_date', '>=', Carbon::now()->subDays(7))
            ->get();
    }
    public function getAutomaticExpectedBackPayment()
    {
        return $this->model->where('status', 'Active')
            ->whereDate('start_date', '<=', Carbon::now()->subDays(7))
            ->whereDate('end_date', '>=', Carbon::now()->subDays(7))
            ->whereHas('equbType', function ($query) {
                $query->where('type', 'Automatic');
            })
            ->get();
    }
    public function getManualExpectedBackPayment()
    {
        return $this->model->where('status', 'Active')
            ->whereDate('start_date', '<=', Carbon::now()->subDays(7))
            ->whereDate('end_date', '>=', Carbon::now()->subDays(7))
            ->whereHas('equbType', function ($query) {
                $query->where('type', 'Manual');
            })
            ->get();
    }
    public function getEqubTypeExpectedBackPayment($equbTypeId)
    {
        return $this->model->where('status', 'Active')
            ->where('equb_type_id', $equbTypeId)
            ->whereDate('start_date', '<=', Carbon::now()->subDays(7))
            ->whereDate('end_date', '>=', Carbon::now()->subDays(7))
            ->get();
    }
    public function getMonthlyExpectedAmount()
    {
        return $this->model->where('status', 'Active')->whereMonth('created_at', date('m'))
            ->whereYear('created_at', date('Y'))->sum('amount');
    }
    public function getYearlyExpectedAmount()
    {
        return $this->model->where('status', 'Active')->whereYear('created_at', date('Y'))->sum('amount');
    }
    public function getByIdNested($id)
    {
        // $equb = $this->model
        //     ->where('status', 'Active')
        //     ->with('equbType.mainEqub', 'payments')
        //     ->find($id);

        // dd($equb);
        return $this->model->where('status', 'Active')->with('equbType.mainEqub', 'payments')->find($id);
    }

    public function getByIdNestedForLottery($id)
    {
        return $this->model->with('equbType.mainEqub', 'payments')->find($id);
    }
    public function geteEubById($id)
    {
        return $this->model->with('equbType', 'payments')->find($id);
    }

    public function getById($id)
    {
        return $this->model->where('status', 'Active')->find($id);
    }

    public function getEqubType($id)
    {
        return $this->model->where('status', 'Active')->where('equb_type_id', $id)->get();
    }
    public function getByMemeberIdAndEqubType($memberId, $equbTypeId)
    {
        return $this->model->where('status', 'Active')->where('member_id', $memberId)->where('equb_type_id', $equbTypeId)->get();
    }

    public function getByEqubTypeId($equbTypeId)
    {
        return $this->model->where('status', 'Active')->where('equb_type_id', $equbTypeId)->get();
    }

    public function getMember($id)
    {
        return $this->model->where('member_id', $id)->where('deleted_at', null)->get();
    }

    public function create(array $attributes)
    {
        return $this->model->with('equbType')->create($attributes);
    }

    public function update($id, array $attributes)
    {
        return $this->model->where('id', $id)->update($attributes);
    }

    public function updateEqubStatus($member_id, array $attributes)
    {
        return $this->model->where('member_id', $member_id)->update($attributes);
    }
    public function getCountByDateAndEqubType($dateFrom, $dateTo, $equbType)
    {
        if ($equbType != 'all') {
            return $this->model->where('equb_type_id', $equbType)->whereDate('created_at', '>=', $dateFrom)
                ->whereDate('created_at', '<=', $dateTo)->count();
        } else {
            return $this->model->whereDate('created_at', '>=', $dateFrom)
                ->whereDate('created_at', '<=', $dateTo)->count();
        }
    }
    public function getByDateAndEqubType($dateFrom, $dateTo, $equbType, $offset)
    {
        if ($equbType != 'all') {
            return $this->model
                ->where('equb_type_id', $equbType)
                ->whereDate('created_at', '>=', $dateFrom)
                ->whereDate('created_at', '<=', $dateTo)
                ->offset($offset)
                ->limit($this->limit)
                ->get();
        } else {
            return $this->model
                ->whereDate('created_at', '>=', $dateFrom)
                ->whereDate('created_at', '<=', $dateTo)
                ->offset($offset)
                ->limit($this->limit)
                ->get();
        }
    }

    public function delete($id)
    {
        return $this->model->where('id', $id)->forceDelete();
    }
    public function filterEqubEndDates($dateFrom, $dateTo, $offset, $equbType)
    {
        // return $this->model
        // ->where('end_date', '>=', $dateFrom)
        // ->where('end_date', '<=', $dateTo)
        // ->offset($offset)
        //     ->limit($this->limit)
        //     ->with('member')->get();
        if ($equbType != 'all') {
            return $this->model
                ->where('equb_type_id', $equbType)
                ->where('end_date', '>=', $dateFrom)
                ->where('end_date', '<=', $dateTo)
                ->offset($offset)
                ->limit($this->limit)
                ->with('member')->get();
        } else {
            return $this->model
                ->where('end_date', '>=', $dateFrom)
                ->where('end_date', '<=', $dateTo)
                ->offset($offset)
                ->limit($this->limit)
                ->with('member')->get();
        }
    }
    public function filterEqubByPaymentMethod($dateFrom, $dateTo, $offset, $equbType)
    {
        $query = Payment::query(); // Start building the query
    
        if ($equbType != 'all') {
            $query->where('payment_type', $equbType);
        }else{
    
       $query->where('payment_type', $equbType);
    
            }
    
        return $query->get(); // Execute the query and return results
    }
    public function countFilterEqubEndDates($dateFrom, $dateTo, $equbType)
    {
        // return $this->model
        //     ->where('end_date', '>=', $dateFrom)
        //     ->where('end_date', '<=', $dateTo)
        //     ->count();
        if ($equbType != 'all') {
            return $this->model
                ->where('equb_type_id', $equbType)
                ->where('end_date', '>=', $dateFrom)
                ->where('end_date', '<=', $dateTo)
                ->count();
        } else {
            return $this->model
                ->where('end_date', '>=', $dateFrom)
                ->where('end_date', '<=', $dateTo)
                ->count();
        }
    }
}
