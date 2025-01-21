<?php


namespace App\Repositories\Payment;

use Carbon\Carbon;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class PaymentRepository implements IPaymentRepository
{
    private $model;
    private $limit;
    public function __construct(Payment $payment)
    {
        $this->model = $payment;
        $this->limit = 50;
    }

    public function getAll()
    {
        return $this->model->all();
    }

    public function getAllPayment()
    {
        return $this->model->with('member', 'equb.equbType')->orderByDesc('created_at')->get();
    }
    public function getAllPendingPayments()
    {
        return $this->model->where('status', 'pending')->with('member', 'equb.equbType')->orderByDesc('created_at')->get();
    }
    public function getAllPaidPayments()
    {
        return $this->model->where('status', 'paid')->with('member', 'equb.equbType')->orderByDesc('created_at')->get();
    }
    public function getAllPendingByPaginate($offset)
    {
        $limit = 10;
        return $this->model->where('status', 'pending')->with('member', 'equb.equbType')->orderByDesc('created_at')->offset($offset)->limit($limit)->get();
    }
    public function getAllPaidByPaginate($offset)
    {
        $limit = 10;
        return $this->model->where('status', 'paid')->with('member', 'equb.equbType')->orderByDesc('created_at')->offset($offset)->limit($limit)->get();
    }
    public function getByDate($dateFrom, $dateTo, $offset)
    {

        \DB::statement("SET SQL_MODE=''");
        return $this->model->selectRaw('full_name,equb_types.name,equb_types.round,payments.id,payments.balance,payments.creadit ,payments.member_id,payments.equb_id,payments.payment_type,payments.amount,payments.status,payments.created_at')->whereDate('payments.created_at', '>=', $dateFrom)
            ->whereDate('payments.created_at', '<=', $dateTo)
            ->join('equbs', 'payments.equb_id', '=', 'equbs.id')
            ->join('members', 'payments.member_id', '=', 'members.id')
            ->join('equb_types', 'equb_types.id', '=', 'equbs.equb_type_id')
            ->where('payments.status', 'paid')
            ->groupBy('payments.id')
            ->offset($offset)
            ->limit($this->limit)
            ->get();
    }
    public function getWithDateAndEqub($dateFrom, $dateTo, $equbType_id, $offset)
    {
        \DB::statement("SET SQL_MODE=''");
        return $this->model->selectRaw('full_name,equb_types.name,equb_types.round,payments.id,payments.balance,payments.creadit ,payments.member_id,payments.equb_id,payments.payment_type,payments.amount,payments.status,payments.created_at')
            ->where('equb_types.id', $equbType_id)
            ->whereDate('payments.created_at', '>=', $dateFrom)
            ->whereDate('payments.created_at', '<=', $dateTo)
            ->join('equbs', 'payments.equb_id', '=', 'equbs.id')
            ->join('members', 'payments.member_id', '=', 'members.id')
            ->join('equb_types', 'equb_types.id', '=', 'equbs.equb_type_id')
            ->groupBy('payments.id')
            ->offset($offset)
            ->limit($this->limit)
            ->get();
    }
    public function getWithDateAndMember($dateFrom, $dateTo, $member_id, $offset)
    {
        \DB::statement("SET SQL_MODE=''");
        return $this->model->selectRaw('full_name,equb_types.name,equb_types.round,payments.id,payments.balance,payments.creadit ,payments.member_id,payments.equb_id,payments.payment_type,payments.amount,payments.status,payments.created_at')
            ->where('payments.member_id', $member_id)
            ->whereDate('payments.created_at', '>=', $dateFrom)
            ->whereDate('payments.created_at', '<=', $dateTo)
            ->join('equbs', 'payments.equb_id', '=', 'equbs.id')
            ->join('members', 'payments.member_id', '=', 'members.id')
            ->join('equb_types', 'equb_types.id', '=', 'equbs.equb_type_id')
            ->groupBy('payments.id')
            ->offset($offset)
            ->limit($this->limit)
            ->get();
    }
    // public function getCollectedByUser($dateFrom, $dateTo, $collecter, $offset, $equbType)
    // {
    //     if ($collecter != "all" && $equbType != "all") {
    //         \DB::statement("SET SQL_MODE=''");
    //         return $this->model->selectRaw('full_name,equb_types.name,equb_types.round,payments.id,payments.creadit,payments.balance,payments.member_id,payments.equb_id,payments.payment_type,payments.amount,payments.status,payments.created_at')
    //             ->where('payments.collecter', $collecter)
    //             ->whereDate('payments.created_at', '>=', $dateFrom)
    //             ->whereDate('payments.created_at', '<=', $dateTo)
    //             ->join('equbs', 'payments.equb_id', '=', 'equbs.id')
    //             ->join('members', 'payments.member_id', '=', 'members.id')
    //             ->join('equb_types', 'equb_types.id', '=', 'equbs.equb_type_id')
    //             ->where('equb_types.id', $equbType)
    //             ->groupBy('payments.id')
    //             ->offset($offset)
    //             ->limit($this->limit)
    //             ->get();
    //     } elseif ($collecter == "all" && $equbType != "all") {
    //         \DB::statement("SET SQL_MODE=''");
    //         return $this->model->selectRaw('full_name,equb_types.name,equb_types.round,payments.id,payments.creadit,payments.balance,payments.member_id,payments.equb_id,payments.payment_type,payments.amount,payments.status,payments.created_at')
    //             ->whereDate('payments.created_at', '>=', $dateFrom)
    //             ->whereDate('payments.created_at', '<=', $dateTo)
    //             ->join('equbs', 'payments.equb_id', '=', 'equbs.id')
    //             ->join('members', 'payments.member_id', '=', 'members.id')
    //             ->join('equb_types', 'equb_types.id', '=', 'equbs.equb_type_id')
    //             ->where('equb_types.id', $equbType)
    //             ->groupBy('payments.id')
    //             ->offset($offset)
    //             ->limit($this->limit)
    //             ->get();
    //     } elseif ($collecter != "all" && $equbType == "all") {
    //         \DB::statement("SET SQL_MODE=''");
    //         return $this->model->selectRaw('full_name,equb_types.name,equb_types.round,payments.id,payments.creadit,payments.balance,payments.member_id,payments.equb_id,payments.payment_type,payments.amount,payments.status,payments.created_at')
    //             ->where('payments.collecter', $collecter)
    //             ->whereDate('payments.created_at', '>=', $dateFrom)
    //             ->whereDate('payments.created_at', '<=', $dateTo)
    //             ->join('equbs', 'payments.equb_id', '=', 'equbs.id')
    //             ->join('members', 'payments.member_id', '=', 'members.id')
    //             ->join('equb_types', 'equb_types.id', '=', 'equbs.equb_type_id')
    //             ->groupBy('payments.id')
    //             ->offset($offset)
    //             ->limit($this->limit)
    //             ->get();
    //     } else {
    //         \DB::statement("SET SQL_MODE=''");
    //         return $this->model->selectRaw('full_name,equb_types.name,equb_types.round,payments.id,payments.creadit,payments.balance,payments.member_id,payments.equb_id,payments.payment_type,payments.amount,payments.status,payments.created_at')
    //             ->whereDate('payments.created_at', '>=', $dateFrom)
    //             ->whereDate('payments.created_at', '<=', $dateTo)
    //             ->join('equbs', 'payments.equb_id', '=', 'equbs.id')
    //             ->join('members', 'payments.member_id', '=', 'members.id')
    //             ->join('equb_types', 'equb_types.id', '=', 'equbs.equb_type_id')
    //             ->groupBy('payments.id')
    //             ->offset($offset)
    //             ->limit($this->limit)
    //             ->get();
    //     }
    // }
    public function getCollectedByUser($dateFrom, $dateTo, $collector, $offset, $equbType)
    {
        \DB::statement("SET SQL_MODE=''");
    
        // Start building the query
        $query = $this->model->selectRaw('
            full_name,
            equb_types.name,
            equb_types.round,
            payments.id,
            payments.creadit,
            payments.balance,
            payments.member_id,
            payments.equb_id,
            payments.payment_type,
            payments.amount,
            payments.status,
            payments.created_at
        ')
        ->join('equbs', 'payments.equb_id', '=', 'equbs.id')
        ->join('members', 'payments.member_id', '=', 'members.id')
        ->join('equb_types', 'equb_types.id', '=', 'equbs.equb_type_id')
        ->whereDate('payments.created_at', '>=', $dateFrom)
        ->whereDate('payments.created_at', '<=', $dateTo)
        ->whereRaw('LOWER(payments.status) = ?', ['paid']) // Case-insensitive check
        ->groupBy('payments.id')
        ->where('payments.status', 'paid')
        ->offset($offset)
        ->limit($this->limit);
    
        // Apply additional filters dynamically
        if ($collector != "all") {
            $query->where('payments.collecter', $collector);
        }
    
        if ($equbType != "all") {
            $query->where('equb_types.id', $equbType);
        }
    
        // Log the SQL query for debugging
        Log::info($query->toSql());
    
        // Execute the query and return the result
        return $query->get();
    }
    public function getWithDateMemberAndEqub($dateFrom, $dateTo, $member_id, $equb_id, $offset)
    {
        \DB::statement("SET SQL_MODE=''");
        return $this->model->selectRaw('full_name,equb_types.name,equb_types.round,payments.id,payments.balance,payments.creadit ,payments.member_id,payments.equb_id,payments.payment_type,payments.amount,payments.status,payments.created_at')
            ->where('payments.member_id', $member_id)
            ->where('payments.equb_id', $equb_id)
            ->whereDate('payments.created_at', '>=', $dateFrom)
            ->whereDate('payments.created_at', '<=', $dateTo)
            ->join('members', 'payments.member_id', '=', 'members.id')
            ->join('equbs', 'payments.equb_id', '=', 'equbs.id')
            ->join('equb_types', 'equb_types.id', '=', 'equbs.equb_type_id')
            ->groupBy('payments.id')
            ->offset($offset)
            ->limit($this->limit)
            ->get();
    }

    public function getById($id)
    {
        return $this->model->find($id);
    }
    public function getByReferenceId($id)
    {
        return $this->model->where('transaction_number', $id)->get();
    }

    public function getByMemberId($member_id, $equb_id)
    {
        return $this->model->where('member_id', $member_id)->where('equb_id', $equb_id)->get();
    }

    public function getTotal($id)
    {
        return $this->model->where('equb_id', $id)->sum('amount');
    }

    public function getLastId($id)
    {
        return $this->model->where('equb_id', $id)->pluck('id')->last();
    }

    public function getTotalCredit($id)
    {
        return $this->model->where('equb_id', $id)->pluck('creadit')->last();
    }

    public function getAmount($id)
    {
        return $this->model->where('id', $id)->pluck('amount')->first();
    }

    public function getTotalBalance($id)
    {
        return $this->model->where('equb_id', $id)->pluck('balance')->last();
    }

    public function getTotalPaid($id)
    {
        return $this->model->where('equb_id', $id)->where('status', 'paid')->sum('amount');
    }
    public function getTotalCount($id)
    {
        return $this->model->where('equb_id', $id)->count('id');
    }
    public function getTotalPayment()
    {
        return $this->model->where('status', 'paid')->sum('amount');
    }
    public function getAutomaticTotalPayment()
    {
        return $this->model->where('status', 'paid')
            ->whereHas('equb.equbType', function ($query) {
                $query->where('type', 'Automatic');
            })
            ->sum('amount');
    }
    public function getManualTotalPayment()
    {
        return $this->model->where('status', 'paid')
            ->whereHas('equb.equbType', function ($query) {
                $query->where('type', 'Manual');
            })
            ->sum('amount');
    }
    public function getEqubTypeTotalPayment($equbTypeId)
    {
        return $this->model->whereHas('equb', function ($query) use ($equbTypeId) {
            $query->where('equb_type_id', $equbTypeId);
        })->where('status', 'paid')->sum('amount');
    }
    public function getSinglePayment($member_id, $equb_id, $offset)
    {
        $limit = 10;
        return $this->model->where('member_id', $member_id)->where('status', '!=', 'unpaid')->where('status', '!=', 'pending')->where('equb_id', $equb_id)->with('collecter', 'member', 'equb')->orderBy('created_at', 'DESC')->offset($offset)->limit($limit)->get();
    }
    ////
    public function getPaidAmount()
    {
        return $this->model->where('status', 'paid')
            ->selectRaw("SUM(amount) as paidAmount")
            ->with(['equb.equbType' => function ($query) {
                $query->groupBy('equbType.name');
            }])
            ->get();
    }
    public function getUnpaidAmount()
    {
        return $this->model->where('status', 'unpaid')->selectRaw("SUM(amount) as unPaidAmount")->groupBy('equb_id->equbType->id')->get();
    }
    public function getPendingAmount()
    {
        return $this->model->where('status', 'pending')->selectRaw("SUM(amount) as pendingAmount")->groupBy('equb_id->equbType->id')->get();
    }

    public function getDaylyPaidAmount()
    {
        // return $this->model->where('status', 'paid')->whereDate('created_at', Carbon::now())->sum('amount');
        return DB::table('payments')
            ->where('status', 'paid')
            ->whereDate('created_at', Carbon::now())
            ->sum('amount');
    }
    public function getAutomaticDailyPaidAmount()
    {
        return $this->model->where('status', 'paid')
            ->whereDate('created_at', Carbon::now())
            ->whereHas('equb.equbType', function ($query) {
                $query->where('type', 'Automatic');
            })
            ->sum('amount');
    }
    public function getManualDailyPaidAmount()
    {
        return $this->model->where('status', 'paid')
            ->whereDate('created_at', Carbon::now())
            ->whereHas('equb.equbType', function ($query) {
                $query->where('type', 'Manual');
            })
            ->sum('amount');
    }
    public function getEqubTypeDaylyPaidAmount($equbTypeId)
    {
        return $this->model->where('status', 'paid')
            ->whereDate('created_at', Carbon::today())
            ->whereHas('equb', function ($query) use ($equbTypeId) {
                $query->where('equb_type_id', $equbTypeId);
            })
            ->sum('amount');
    }
    public function getDaylyUnpaidAmount()
    {
        return $this->model->where('status', 'unpaid')->whereDate('created_at', Carbon::now())->sum('amount');
    }
    public function getDaylyPendingAmount()
    {
        return $this->model->where('status', 'pending')->whereDate('created_at', Carbon::now())->sum('amount');
    }
    public function getAutomaticDailyPendingAmount()
    {
        return $this->model->where('status', 'pending')
            ->whereDate('created_at', Carbon::now())
            ->whereHas('equb.equbType', function ($query) {
                $query->where('type', 'Automatic');
            })
            ->sum('amount');
    }
    public function getManualDailyPendingAmount()
    {
        return $this->model->where('status', 'pending')
            ->whereDate('created_at', Carbon::now())
            ->whereHas('equb.equbType', function ($query) {
                $query->where('type', 'Manual');
            })
            ->sum('amount');
    }
    public function getEqubTypeDaylyPendingAmount($equbTypeId)
    {
        return $this->model->where('status', 'pending')
            ->whereDate('created_at', Carbon::today())
            ->whereHas('equb', function ($query) use ($equbTypeId) {
                $query->where('equb_type_id', $equbTypeId);
            })
            ->sum('amount');
    }
    public function getWeeklyPaidAmount()
    {
        return $this->model->whereDate('created_at', '>=', Carbon::now()->subDays(7))
            ->whereDate('created_at', '<=', Carbon::now())
            ->sum('amount');
    }
    public function getAutomaticWeeklyPaidAmount()
    {
        return $this->model->whereDate('created_at', '>=', Carbon::now()->subDays(7))
            ->whereDate('created_at', '<=', Carbon::now())
            ->whereHas('equb.equbType', function ($query) {
                $query->where('type', 'Automatic');
            })
            ->sum('amount');
    }
    public function getManualWeeklyPaidAmount()
    {
        return $this->model->whereDate('created_at', '>=', Carbon::now()->subDays(7))
            ->whereDate('created_at', '<=', Carbon::now())
            ->whereHas('equb.equbType', function ($query) {
                $query->where('type', 'Manual');
            })
            ->sum('amount');
    }
    public function getEqubTypeWeeklyPaidAmount($equbTypeId)
    {
        return $this->model->whereHas('equb', function ($query) use ($equbTypeId) {
            $query->where('equb_type_id', $equbTypeId);
        })
            ->whereDate('created_at', '>=', Carbon::now()->subDays(7))
            ->whereDate('created_at', '<=', Carbon::now())
            ->where('status', 'paid')
            ->sum('amount');
    }
    public function getWeeklyUnpaidAmount()
    {
        return $this->model->where('status', 'unpaid')->whereBetween('created_at', [Carbon::now()->subDays(7), Carbon::now()])->sum('amount');
    }
    public function getWeeklyPendingAmount()
    {
        return $this->model->where('status', 'pending')->whereBetween('created_at', [Carbon::now()->subDays(7), Carbon::now()])->sum('amount');
    }
    public function getAutomaticWeeklyPendingAmount()
    {
        return $this->model->where('status', 'pending')
            ->whereBetween('created_at', [Carbon::now()->subDays(7), Carbon::now()])
            ->whereHas('equb.equbType', function ($query) {
                $query->where('type', 'Automatic');
            })
            ->sum('amount');
    }
    public function getManualWeeklyPendingAmount() {
        return $this->model->where('status', 'pending')
            ->whereBetween('created_at', [Carbon::now()->subDays(7), Carbon::now()])
            ->whereHas('equb.equbType', function ($query) {
                $query->where('type', 'Automatic');
            })
            ->sum('amount');
    }
    public function getEqubTypeWeeklyPendingAmount($equbTypeId)
    {
        return $this->model->whereHas('equb', function ($query) use ($equbTypeId) {
            $query->where('equb_type_id', $equbTypeId);
        })
            ->where('status', 'pending')
            ->whereBetween('created_at', [Carbon::now()->subDays(7), Carbon::now()])
            ->sum('amount');
    }
    public function getPaidByDate($dateFrom, $dateTo)
    {

        return $this->model->whereDate('created_at', '>=', $dateFrom)
            ->whereDate('created_at', '<=', $dateTo)
            ->pluck('equb_id');
    }
    public function getMonthlyPaidAmount()
    {
        return $this->model->whereDate('created_at', '>=', Carbon::now()->subDays(30))
            ->whereDate('created_at', '<=', Carbon::now())
            ->sum('amount');
    }
    public function getAutomaticMonthlyPaidAmount()
    {
        return $this->model->whereDate('created_at', '>=', Carbon::now()->subDays(30))
            ->whereDate('created_at', '<=', Carbon::now())
            ->whereHas('equb.equbType', function ($query) {
                $query->where('type', 'Automatic');
            })
            ->sum('amount');
    }
    public function getManualMonthlyPaidAmount()
    {
        return $this->model->whereDate('created_at', '>=', Carbon::now()->subDays(30))
            ->whereDate('created_at', '<=', Carbon::now())
            ->whereHas('equb.equbType', function ($query) {
                $query->where('type', 'Manual');
            })
            ->sum('amount');
    }
    public function getEqubTypeMonthlyPaidAmount($equbTypeId)
    {
        return $this->model->whereHas('equb', function ($query) use ($equbTypeId) {
            $query->where('equb_type_id', $equbTypeId);
        })
            ->whereDate('created_at', '>=', Carbon::now()->subDays(30))
            ->whereDate('created_at', '<=', Carbon::now())
            ->sum('amount');
    }
    public function getMonthlyUnpaidAmount()
    {
        return $this->model->where('status', 'unpaid')->whereMonth('created_at', date('m'))
            ->whereYear('created_at', date('Y'))->sum('amount');
    }
    public function getMonthlyPendingAmount()
    {
        return $this->model->where('status', 'pending')->whereMonth('created_at', date('m'))
            ->whereYear('created_at', date('Y'))->sum('amount');
    }
    public function getAutomaticMonthlyPendingAmount()
    {
        return $this->model->where('status', 'pending')
                ->whereMonth('created_at', date('m'))
                ->whereYear('created_at', date('Y'))
                ->whereHas('equb.equbType', function ($query) {
                    $query->where('type', 'Automatic');
                })
                ->sum('amount');
    }
    public function getManualMonthlyPendingAmount()
    {
        return $this->model->where('status', 'pending')
            ->whereMonth('created_at', date('m'))
            ->whereYear('created_at', date('Y'))
            ->whereHas('equb.equbType', function ($query) {
                $query->where('type', 'Manual');
            })
            ->sum('amount');
    }
    public function getEqubTypeMonthlyPendingAmount($equbTypeId)
    {
        return $this->model->whereHas('equb', function ($query) use ($equbTypeId) {
            $query->where('equb_type_id', $equbTypeId);
        })
            ->where('status', 'pending')
            ->whereMonth('created_at', date('m'))
            ->whereYear('created_at', date('Y'))
            ->sum('amount');
    }

    public function getYearlyPaidAmount()
    {
        return $this->model->whereDate('created_at', '>=', Carbon::now()->subDays(365))
            ->whereDate('created_at', '<=', Carbon::now())
            ->sum('amount');
    }
    public function getAutomaticYearlyPaidAmount()
    {
        return $this->model->whereDate('created_at', '>=', Carbon::now()->subDays(365))
            ->whereDate('created_at', '<=', Carbon::now())
            ->whereHas('equb.equbType', function ($query) {
                $query->where('type', 'Automatic');
            })
            ->sum('amount');
    }
    public function getManualYearlyPaidAmount()
    {
        return $this->model->whereDate('created_at', '>=', Carbon::now()->subDays(365))
            ->whereDate('created_at', '<=', Carbon::now())
            ->whereHas('equb.equbType', function ($query) {
                $query->where('type', 'Manual');
            })
            ->sum('amount');
    }
    public function getEqubTypeYearlyPaidAmount($equbTypeId)
    {
        return $this->model->whereHas('equb', function ($query) use ($equbTypeId) {
            $query->where('equb_type_id', $equbTypeId);
        })
            ->whereDate('created_at', '>=', Carbon::now()->subDays(365))
            ->whereDate('created_at', '<=', Carbon::now())
            ->sum('amount');
    }
    public function getYearlyUnpaidAmount()
    {
        return $this->model->where('status', 'unpaid')->whereYear('created_at', date('Y'))->sum('amount');
    }
    public function getYearlyPendingAmount()
    {
        return $this->model->where('status', 'pending')->whereYear('created_at', date('Y'))->sum('amount');
    }
    public function getAutomaticYearlyPendingAmount()
    {
        return $this->model->where('status', 'pending')
            ->whereYear('created_at', date('Y'))
            ->whereHas('equb.equbType', function ($query) {
                $query->where('type', 'Automatic');
            })
            ->sum('amount');
    }
    public function getManualYearlyPendingAmount()
    {
        return $this->model->where('status', 'pending')
            ->whereYear('created_at', date('Y'))
            ->whereHas('equb.equbType', function ($query) {
                $query->where('type', 'Manual');
            })
            ->sum('amount');
    }
    public function getEqubTypeYearlyPendingAmount($equbTypeId)
    {
        return $this->model->whereHas('equb', function ($query) use ($equbTypeId) {
            $query->where('equb_type_id', $equbTypeId);
        })
            ->where('status', 'pending')
            ->whereYear('created_at', date('Y'))
            ->sum('amount');
    }

    public function getPayment($id)
    {
        return $this->model->with('equb.equbType')->find($id);
    }

    public function getPayments()
    {
        return $this->model->all()->count();
    }
    public function countPendingPayments()
    {
        return $this->model->where('status', 'pending')->count();
    }

    public function getCountCollectedBys($dateFrom, $dateTo)
    {
        return $this->model->whereDate('created_at', '>=', $dateFrom)
            ->whereDate('created_at', '<=', $dateTo)
            ->where('status', 'paid')
            ->count();
    }
    public function getCountCollectedBysWithCollecter($dateFrom, $dateTo, $collecter = 'all', $equbType = 'all')
    {
        if ($collecter != "all" && $equbType != "all") {
            return $this->model->whereDate('created_at', '>=', $dateFrom)
                ->whereDate('created_at', '<=', $dateTo)
                ->where('collecter', $collecter)
                ->where('status', 'paid')
                ->whereHas('equb', fn ($q) =>  $q->where('equb_type_id', "=", $equbType))
                ->count();
        } elseif ($collecter == "all" && $equbType != "all") {
            return $this->model->whereDate('created_at', '>=', $dateFrom)
                ->whereDate('created_at', '<=', $dateTo)
                ->where('status', 'paid')
                ->whereHas('equb', fn ($q) =>  $q->where('equb_type_id', "=", $equbType))
                ->count();
        } elseif ($collecter != "all" && $equbType == "all") {
            return $this->model->whereDate('created_at', '>=', $dateFrom)
                ->whereDate('created_at', '<=', $dateTo)
                ->where('collecter', $collecter)
                ->where('status', 'paid')
                ->count();
        } else {
            return $this->model->whereDate('created_at', '>=', $dateFrom)
                ->whereDate('created_at', '<=', $dateTo)
                ->where('status', 'paid')
                ->count();
        }
        // return $this->model->whereDate('created_at', '>=', $dateFrom)
        //     ->whereDate('created_at', '<=', $dateTo)
        //     ->where('collecter', $collecter)
        //     ->count();
    }
    public function getCountWithDateAndEqub($dateFrom, $dateTo, $equb_id)
    {
        return $this->model->whereDate('created_at', '>=', $dateFrom)
            ->whereDate('created_at', '<=', $dateTo)
            ->where('equb_id', $equb_id)
            ->count();
    }

    public function getCountWithDate($dateFrom, $dateTo)
    {
        return $this->model->whereDate('created_at', '>=', $dateFrom)
            ->whereDate('created_at', '<=', $dateTo)
            ->count();
    }

    public function getCountDateAndMember($dateFrom, $dateTo, $member_id)
    {
        return $this->model->whereDate('created_at', '>=', $dateFrom)
            ->whereDate('created_at', '<=', $dateTo)
            ->where('member_id', $member_id)
            ->count();
    }

    public function getCountWithDateMemberAndEqub($dateFrom, $dateTo, $member_id, $equb_id)
    {
        return $this->model->whereDate('created_at', '>=', $dateFrom)
            ->whereDate('created_at', '<=', $dateTo)
            ->where('member_id', $member_id)
            ->where('equb_id', $equb_id)
            ->count();
    }

    public function getEqub($id)
    {
        return $this->model->where('equb_id', $id)->get();
    }
    public function getEqubForDelete($id)
    {
        return $this->model->where('equb_id', $id)->first();
    }

    public function create(array $attributes)
    {
        return $this->model->create($attributes);
    }

    public function update($id, array $attributes)
    {
        return $this->model->where('id', $id)->update($attributes);
    }

    public function updateCredit($equb_id, array $attributes)
    {
        return  $this->model->where('equb_id', $equb_id)->update($attributes);
    }

    public function updateBalance($equb_id, array $attributes)
    {
        return  $this->model->where('equb_id', $equb_id)->update($attributes);
    }

    public function updateAmount($equb_id, array $attributes)
    {
        return  $this->model->where('equb_id', $equb_id)->update($attributes);
    }

    public function delete($id)
    {
        return  $this->model->where('id', $id)->delete();
    }

    public function deleteAll($member_id, $equb_id)
    {
        return  $this->model->where('member_id', $member_id)->where('equb_id', $equb_id)->forceDelete();
    }

    public function forceDelete($id)
    {
        return  $this->model->where('id', $id)->forceDelete();
    }
    public function searchPendingPayment($offset, $searchInput)
    {
        // dd($searchInput);
        $limit = 10;
        return $this->model
            ->whereHas('member', fn ($q) =>  $q->where('full_name', 'LIKE', "%{$searchInput}%")
                ->orWhere('phone', 'LIKE', "%{$searchInput}%"))
            ->where('status', 'Pending')
            ->offset($offset)
            ->limit($limit)
            ->orderBy('created_at', 'desc')
            ->get();
    }
    public function searchPendingPaymentCount($searchInput)
    {
        return $this->model
            ->whereHas('member', fn ($q) =>  $q->where('full_name', 'LIKE', "%{$searchInput}%")
                ->orWhere('phone', 'LIKE', "%{$searchInput}%"))
            ->where('status', 'Pending')
            ->count();
    }
    public function searchPaidPayment($offset, $searchInput)
    {
        // dd($searchInput);
        $limit = 10;
        return $this->model
            ->whereHas('member', fn ($q) =>  $q->where('full_name', 'LIKE', "%{$searchInput}%")
                ->orWhere('phone', 'LIKE', "%{$searchInput}%"))
            ->where('status', 'paid')
            ->offset($offset)
            ->limit($limit)
            ->orderBy('created_at', 'desc')
            ->get();
    }
    public function searchPaidPaymentCount($searchInput)
    {
        return $this->model
            ->whereHas('member', fn ($q) =>  $q->where('full_name', 'LIKE', "%{$searchInput}%")
                ->orWhere('phone', 'LIKE', "%{$searchInput}%"))
            ->where('status', 'paid')
            ->count();
    }
}
