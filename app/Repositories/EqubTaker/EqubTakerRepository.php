<?php


namespace App\Repositories\EqubTaker;

use App\Models\EqubTaker;
use Carbon\Carbon;


class EqubTakerRepository implements IEqubTakerRepository
{
    private $model;
    private $limit;
    public function __construct(EqubTaker $equbTaker)
    {
        $this->model = $equbTaker;
        $this->limit = 50;
    }

    public function getAll()
    {
       // return $this->model->all();
       return $this->model->with(['member', 'equb'])->paginate($this->limit);
    }

    public function getReportById()
    {
        return $this->model->all();
    }

    public function getMemberId()
    {

        return $this->model->where('remaining_amount', '==', 0)
            ->pluck('member_id');
    }
    public function getMemberIdById($id)
    {

        return $this->model->where('equb_id', $id)
            ->pluck('member_id')->toSql();
    }
    public function getEkubTaker($equbId, $memberId)
    {
        // dd($equbId,$memberId);
        return $this->model->where('equb_id', $equbId)
            ->where('member_id', $memberId)
            ->get();
    }
    public function getEkubTakerById($id)
    {
        // dd($equbId,$memberId);
        return $this->model->where('id', $id)
            ->get();
    }

    public function getTotalEqubAmount($equb_id)
    {
        return $this->model->where('equb_id', $equb_id)->where('status', '!=', 'unpaid')->sum('amount');
    }

    public function getByDate($dateFrom, $dateTo, $offset)
    {
        \DB::statement("SET SQL_MODE=''");
        return $this->model->selectRaw('full_name,equb_types.name,equb_types.round,equb_takers.amount,equb_takers.created_at,equb_takers.remaining_amount,equb_takers.total_payment,equb_takers.remaining_payment,equb_takers.cheque_amount,equb_takers.cheque_bank_name,equb_takers.cheque_description,equb_takers.status')
            ->whereDate('equb_takers.created_at', '>=', $dateFrom)
            ->whereDate('equb_takers.created_at', '<=', $dateTo)
            ->join('equbs', 'equb_takers.equb_id', '=', 'equbs.id')
            ->join('members', 'equb_takers.member_id', '=', 'members.id')
            ->join('equb_types', 'equb_types.id', '=', 'equbs.equb_type_id')
            ->groupBy('equb_takers.id')
            ->offset($offset)
            ->limit($this->limit)
            ->get();
    }
    public function getWithDateAndEqub($dateFrom, $dateTo, $equb_type_id, $offset)
    {
        \DB::statement("SET SQL_MODE=''");
        return $this->model->selectRaw('full_name,equb_types.name,equb_types.round,equb_takers.amount,equb_takers.created_at,equb_takers.remaining_amount,equb_takers.total_payment,equb_takers.remaining_payment,equb_takers.cheque_amount,equb_takers.cheque_bank_name,equb_takers.cheque_description,equb_takers.status')
            ->whereDate('equb_takers.created_at', '>=', $dateFrom)
            ->whereDate('equb_takers.created_at', '<=', $dateTo)
            ->where('equb_types.id', $equb_type_id)
            ->join('equbs', 'equb_takers.equb_id', '=', 'equbs.id')
            ->join('members', 'equb_takers.member_id', '=', 'members.id')
            ->join('equb_types', 'equb_types.id', '=', 'equbs.equb_type_id')
            ->groupBy('equb_takers.id')
            ->offset($offset)
            ->limit($this->limit)
            ->get();
    }
    public function getWithDateAndMember($dateFrom, $dateTo, $member_id, $offset)
    {
        \DB::statement("SET SQL_MODE=''");
        return $this->model->selectRaw('full_name,equb_types.name,equb_types.round,equb_takers.amount,equb_takers.created_at,equb_takers.remaining_amount,equb_takers.total_payment,equb_takers.remaining_payment,equb_takers.cheque_amount,equb_takers.cheque_bank_name,equb_takers.cheque_description,equb_takers.status')
            ->whereDate('equb_takers.created_at', '>=', $dateFrom)
            ->whereDate('equb_takers.created_at', '<=', $dateTo)
            ->where('equb_takers.member_id', $member_id)
            ->join('equbs', 'equb_takers.equb_id', '=', 'equbs.id')
            ->join('members', 'equb_takers.member_id', '=', 'members.id')
            ->join('equb_types', 'equb_types.id', '=', 'equbs.equb_type_id')
            ->groupBy('equb_takers.id')
            ->offset($offset)
            ->limit($this->limit)
            ->get();
    }
    public function getWithDateMemberAndEqub($dateFrom, $dateTo, $member_id, $equb_type_id, $offset)
    {
        \DB::statement("SET SQL_MODE=''");
        return $this->model->selectRaw('full_name,equb_types.name,equb_types.round,equb_takers.amount,equb_takers.created_at,equb_takers.remaining_amount,equb_takers.total_payment,equb_takers.remaining_payment,equb_takers.cheque_amount,equb_takers.cheque_bank_name,equb_takers.cheque_description,equb_takers.status')
            ->whereDate('equb_takers.created_at', '>=', $dateFrom)
            ->whereDate('equb_takers.created_at', '<=', $dateTo)
            ->where([
                ['equb_takers.member_id', $member_id],
                ['equb_types.id', $equb_type_id],
            ])
            ->join('members', 'equb_takers.member_id', '=', 'members.id')
            ->join('equbs', 'equb_takers.equb_id', '=', 'equbs.id')
            ->join('equb_types', 'equb_types.id', '=', 'equbs.equb_type_id')
            ->groupBy('equb_takers.id')
            ->offset($offset)
            ->limit($this->limit)
            ->get();
    }

    public function getCountWithDateAndEqub($dateFrom, $dateTo, $equb_type_id)
    {
        return $this->model->whereDate('equb_takers.created_at', '>=', $dateFrom)
            ->whereDate('equb_takers.created_at', '<=', $dateTo)
            ->join('members', 'equb_takers.member_id', '=', 'members.id')
            ->join('equbs', 'equb_takers.equb_id', '=', 'equbs.id')
            ->join('equb_types', 'equb_types.id', '=', 'equbs.equb_type_id')
            ->where('equb_types.id', $equb_type_id)
            ->count();
    }

    public function getCountByDate($dateFrom, $dateTo)
    {
        return $this->model->whereDate('created_at', '>=', $dateFrom)
            ->whereDate('created_at', '<=', $dateTo)
            ->count();
    }

    public function getCountWithDateAndMember($dateFrom, $dateTo, $member_id)
    {
        return $this->model->whereDate('created_at', '>=', $dateFrom)
            ->whereDate('created_at', '<=', $dateTo)
            ->where('member_id', $member_id)
            ->count();
    }

    public function getCountWithDateMemberAndEqub($dateFrom, $dateTo, $member_id, $equb_type_id)
    {
        return $this->model->whereDate('equb_takers.created_at', '>=', $dateFrom)
            ->whereDate('equb_takers.created_at', '<=', $dateTo)
            ->join('members', 'equb_takers.member_id', '=', 'members.id')
            ->join('equbs', 'equb_takers.equb_id', '=', 'equbs.id')
            ->join('equb_types', 'equb_types.id', '=', 'equbs.equb_type_id')
            ->where('equb_takers.member_id', $member_id)
            ->where('equb_types.id', $equb_type_id)
            ->count();
    }

    public function getAllEqubTaker($id)
    {
        return $this->model->with('member.equbs.equbType')->find($id);
    }

    public function getByIdNested($id)
    {
        return $this->model->with('equb.equbType')->find($id);
    }

    public function getById($id)
    {
        return $this->model->find($id);
    }

    public function getByEqubId($equb_id)
    {
        return $this->model->where('equb_id', $equb_id)->where('status', 'paid')->first();
    }

    public function getTotal($id)
    {
        return $this->model->where('equb_id', $id)->sum('amount');
    }

    public function getSinglePayment($member_id, $equb_id)
    {
        return $this->model->where('member_id', $member_id)->where('equb_id', $equb_id)->with('member')->get();
    }

    public function getDaylyPaidAmount()
    {
        return $this->model->where('status', 'paid')->whereDate('created_at', Carbon::today())->sum('amount');
    }
    public function getDaylyUnpaidAmount()
    {
        return $this->model->where('status', 'unpaid')->whereDate('created_at', Carbon::today())->sum('amount');
    }
    public function getDaylyPendingAmount()
    {
        return $this->model->where('status', 'pending')->whereDate('created_at', Carbon::today())->sum('amount');
    }


    public function getWeeklyPaidAmount()
    {
        return $this->model->where('status', 'paid')->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->sum('amount');
    }
    public function getWeeklyUnpaidAmount()
    {
        return $this->model->where('status', 'unpaid')->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->sum('amount');
    }
    public function getWeeklyPendingAmount()
    {
        return $this->model->where('status', 'pending')->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->sum('amount');
    }

    public function getMonthlyPaidAmount()
    {
        return $this->model->where('status', 'paid')->whereMonth('created_at', date('m'))
            ->whereYear('created_at', date('Y'))->sum('amount');
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


    public function getYearlyPaidAmount()
    {
        return $this->model->where('status', 'paid')->whereYear('created_at', date('Y'))->sum('amount');
    }
    public function getYearlyUnpaidAmount()
    {
        return $this->model->where('status', 'unpaid')->whereYear('created_at', date('Y'))->sum('amount');
    }
    public function getYearlyPendingAmount()
    {
        return $this->model->where('status', 'pending')->whereYear('created_at', date('Y'))->sum('amount');
    }


    public function getPayment($id)
    {
        return $this->model->with('equb.equbType')->find($id);
    }

    public function create(array $attributes)
    {
        return $this->model->create($attributes);
    }

    public function update($id, array $attributes)
    {
        return $this->model->where('id', $id)->update($attributes);
    }
    public function updatePayment($equb_id, array $attributes)
    {

        // return \DB::table('equb_takers')->update($attributes);
        return $this->model->where('equb_id', $equb_id)->update($attributes);
    }

    public function delete($id)
    {
        return $this->model->where('id', $id)->forceDelete();
    }
}
