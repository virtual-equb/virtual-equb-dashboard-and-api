<?php


namespace App\Repositories\Member;

use App\Models\City;
use App\Models\Equb;
use App\Models\Member;
use App\Models\Payment;
use Illuminate\Support\Facades\Cache;

class MemberRepository implements IMemberRepository
{
    private $model;
    private $limit;
    public function __construct(Member $member)
    {
        $this->model = $member;
        $this->limit = 10;
    }

    public function getAll()
    {
        return $this->model->with(['memberCity', 'memberSubcity'])->get();
    }

    public function getPhone($id)
    {
        return $this->model->where('id', $id)->first();
    }

    // public function getAllByPaginate($offset, $limit)
    // {
    //     $limit = 10;
    //     return $this->model->with(['memberCity', 'memberSubcity'])
    //         ->where('gender', '!=', '')
    //         ->orderBy('full_name', 'asc')
    //         ->offset($offset)
    //         ->limit($limit)
    //         ->get();
    // }
    public function getAllByPaginate($offset, $limit = 10)
    {
        return $this->model
            ->with(['memberCity', 'memberSubcity'])
            ->whereNotNull('gender')
            ->orderBy('full_name', 'asc')
            ->offset($offset)
            ->limit($limit)
            ->get();
    }
    public function getAllPendingByPaginate($offset)
    {
        $limit = 10;
        $members = $this->model->orderBy('full_name', 'asc')->offset($offset)->limit($limit)->get();
        $membersArray = [];
        foreach ($members as $member) {
            $activeEqubs = $this->countActiveEqubs($member->id);
            $inactiveEqubs = $this->countCompletedEqubs($member->id);
            $memberArr = [
                "member" => $member,
                "activeEqubs" => $activeEqubs,
                "completedEqubs" => $inactiveEqubs,
            ];
            array_push($membersArray, $memberArr);
        }
        return $membersArray;
        
    }
    public function getAllByPaginateApp($offset)
    {
        $limit = 10;
        $members = $this->model->orderBy('full_name', 'asc')->offset($offset)->limit($limit)->get();
        $membersArray = [];
        foreach ($members as $member) {
            $activeEqubs = $this->countActiveEqubs($member->id);
            $inactiveEqubs = $this->countCompletedEqubs($member->id);
            $memberArr = [
                "member" => $member,
                "activeEqubs" => $activeEqubs,
                "completedEqubs" => $inactiveEqubs,
            ];
            array_push($membersArray, $memberArr);
        }
        return $membersArray;
    }
    public function getByPhone($phone)
    {
        return $this->model->where('phone', $phone)->get();
    }
    public function getActiveMember()
    {
        return $this->model->where('status', 'Active')->where('gender', '!=', '')->count('id');
    }
    public function getEqubTypeActiveMember($equbTypeId)
    {
        return $this->model->whereHas('equbs', function ($query) use ($equbTypeId) {
            $query->where('equb_type_id', $equbTypeId);
        })->where('status', 'Active')->count('id');
    }
    public function getActiveMemberNotification()
    {
        return $this->model->where('status', 'Active')->get();
    }

    public function getByDate($dateFrom, $dateTo, $offset)
    {
        return $this->model->whereDate('created_at', '>=', $dateFrom)
            ->whereDate('created_at', '<=', $dateTo)
            ->where('gender', '!=', '')
            ->with('equbs')
            ->orderBy('created_at', 'DESC')
            ->offset($offset)
            ->limit($this->limit)
            ->get();
    }

    public function getCountByDate($dateFrom, $dateTo)
    {
        return $this->model->whereDate('created_at', '>=', $dateFrom)
            ->whereDate('created_at', '<=', $dateTo)->count();
    }

    public function getMemberWithEqub()
    {
        return $this->model->where('status', 'Active')->with('equbs.equbType')->get();
    }

    public function getEqubs($id)
    {
        return $this->model->where('status', 'Active')->with('equbs.equbType')->find($id);
    }
    public function getAllEqubs()
    {
        return $this->model->where('status', 'Active')->with('equbs.equbType')->get();
    }

    public function getStatusById($id)
    {
        return $this->model->find($id);
    }

    public function getById($id)
    {
        return $this->model->where('status', 'Active')->find($id);
    }

    public function getByIdToDelete($id)
    {
        return $this->model->find($id);
    }

    // public function getMemberById($id)
    // {
    //     // dd($this->model->find($id));
    //     return $this->model->with('equbs')->find($id);
    // }
    public function getMemberById($id)
    {
        return Cache::remember("member_{$id}", 60, function () use ($id) {
            return $this->model
                ->with('equbs')
                ->find($id);
        });
    }
    public function getMembersByEqubType($equbType)
    {
        // dd($equbType);
        return Member::whereHas('equbs.equbType', fn ($q) => $q->where('equb_types.id', $equbType))->get();
    }
    public function getMemberWithPayment($id)
    {
        return $this->model->with('payments.collecter', 'payments.equb')->find($id);
    }

    public function getMember()
    {
        // $this->model->all()->count();
        return $this->model->where('gender', '!=', '')->count();
    }
    public function getPendingMembers()
    {
        return $this->model->where('status', 'Pending')->count();
    }
    public function getAllPendingMembers()
    {
        return $this->model->where('status', 'Pending')->get();
    }
    public function getAllPendingMembersNotification()
    {
        return $this->model->where('status', 'Pending')->limit(10)->get();
    }
    public function getEqubTypeMember($equbTypeId)
    {
        return $this->model->whereHas('equbs', function ($query) use ($equbTypeId) {
            $query->where('equb_type_id', $equbTypeId); // Assuming 'id' is the primary key in the related equbType model
        })
            ->count();
    }
    public function countMember($searchInput)
    {
        return $this->model->where('full_name', 'LIKE', "%{$searchInput}%")->orWhere('phone', 'LIKE', "%{$searchInput}%")->count();
    }
    public function countPendingMember($searchInput)
    {
        return $this->model->where('status', 'Pending')->where('full_name', 'LIKE', "%{$searchInput}%")->orWhere('phone', 'LIKE', "%{$searchInput}%")->count();
    }
    public function countEqubMember($searchInput)
    {
        return $this->model->whereHas('equbs', function ($query) use ($searchInput) {
            $query->where('equb_type_id', $searchInput);
        })->count();
    }
    public function countPendingEqubMember($searchInput)
    {
        return $this->model
            ->where('status', 'Pending')
            ->whereHas('equbs', function ($query) use ($searchInput) {
                $query->where('equb_type_id', $searchInput);
            })->count();
    }
    public function countStatusMember($searchInput)
    {
        return $this->model->where('status', $searchInput)->count();
    }
    public function countActiveEqubs($memberId)
    {
        return $this->model->where('id', $memberId)->whereHas('equbs', function ($query) {
            $query->where('status', 'Active');
        })->count();
    }
    public function countCompletedEqubs($memberId)
    {
        return $this->model->where('id', $memberId)->whereHas('equbs.equbTakers', function ($query) {
            $query->where('remaining_payment', 0);
        })->count();
    }

    public function searchMember($offset, $searchInput)
    {
        $limit = 10;
        return $this->model->where('full_name', 'LIKE', "%{$searchInput}%")
            ->orWhere('phone', 'LIKE', "%{$searchInput}%")
            ->offset($offset)
            ->limit($limit)
            ->orderBy('full_name', 'asc')
            ->get();
    }
    public function searchPendingMember($offset, $searchInput)
    {
        $limit = 10;
        return $this->model
            ->where('status', 'Pending')
            ->where('full_name', 'LIKE', "%{$searchInput}%")
            ->orWhere('phone', 'LIKE', "%{$searchInput}%")
            ->offset($offset)
            ->limit($limit)
            ->orderBy('full_name', 'asc')
            ->get();
    }
    public function searchEqub($offset, $searchInput)
    {
        $limit = 10;
        return $this->model->whereHas('equbs', function ($query) use ($searchInput) {
            $query->where('equb_type_id', $searchInput);
        })->offset($offset)
            ->limit($limit)
            ->get();
    }
    public function searchPendingEqub($offset, $searchInput)
    {
        // dd($this->limit);
        return $this->model->where('status', 'Pending')->whereHas('equbs', function ($query) use ($searchInput) {
            $query->where('equb_type_id', $searchInput);
        })->offset($offset)
            ->limit($this->limit)
            ->get();
        // return $members;
    }

    public function searchStatus($offset, $searchInput)
    {
        $limit = 10;
        return $this->model->where('status', $searchInput)
            ->offset($offset)
            ->limit($limit)
            ->get();
    }

    public function getByIdNested($id)
    {
        return $this->model->with('equbs.equbType', 'equbs.equbTakers', 'equbs.payments.collecter')->find($id);
    }
    public function create(array $attributes)
    {
        return $this->model->create($attributes);
    }

    public function update($id, array $attributes)
    {
        return $this->model->where('id', $id)->update($attributes);
    }

    public function delete($id)
    {
        return $this->model->where('id', $id)->delete();
    }

    public function forceDelete($id)
    {
        return $this->model->where('id', $id)->forceDelete();
    }
    public function checkPhone($phone)
    {
        return $this->model->where('phone', $phone)->first() ? 1 : 0;
    }
}
