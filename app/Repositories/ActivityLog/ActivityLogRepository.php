<?php

namespace App\Repositories\ActivityLog;

use App\Models\ActivityLog;

class ActivityLogRepository implements IActivityLogRepository
{

    private $model;
    private $limit;

    public function __construct(ActivityLog $activityLog)
    {
        $this->model = $activityLog;
        $this->limit = 10;
    }

    public function getAll()
    {
        return $this->model->all();
    }

    public function getById($id)
    {
        return $this->model->findOrFail($id);
    }

    public function getByAdminId($id)
    {
        return $this->model->where('user_id', $id);
    }

    public function createActivityLog(array $attributes)
    {
        return $this->model->create($attributes);
    }

    public function countActivityLog($type, $searchInput = null)
    {
        if ($searchInput) {
            return $this->model
                ->where('type', $type)
                ->where('type', 'LIKE', "%{$searchInput}%")
                ->orWhere('action', 'LIKE', "%{$searchInput}%")
                ->count();
        } else {
            return $this->model
                ->where('type', $type)
                ->count();
        }
    }

    public function countByType()
    {
        return $this->model->selectRaw('type, COUNT(*) as total')->groupBy('type')->get();
    }
    public function paginateCountByType($offset)
    {
        // $limit = 10;
        return $this->model
            ->selectRaw('type, COUNT(*) as total')
            ->groupBy('type')
            ->offset($offset)
            ->limit($this->limit)
            ->get();
    }
    public function totalCountByType()
    {
        return $this->model->selectRaw('COUNT(0)')->groupBy('type')->get()->count();
    }
    public function getAllActivityLog($type, $offset, $searchInput = null)
    {
        if ($searchInput) {
            return $this->model->where('type', $type)
                ->where('type', 'LIKE', "%{$searchInput}%")
                ->orWhere('action', 'LIKE', "%{$searchInput}%")
                ->offset($offset)
                ->limit($this->limit)
                ->orderBy('created_at', 'desc')
                ->get();
        } else {
            return $this->model->where('type', $type)
                ->offset($offset)
                ->limit($this->limit)
                ->orderBy('created_at', 'desc')
                ->get();
        }
    }

    public function countActivityLogType($type, $id)
    {
        return $this->model->where([['type', $type], ['type_id', $id]])->orWhere('user_id', $id)->count();
    }

    public function forceDeleteLog($id)
    {
        return $this->model->where("id", $id)->forceDelete();
    }
    public function forceDeleteLogByUserId($userId)
    {
        return $this->model->where("user_id", $userId)->forceDelete();
    }
    // public function searchActivity($offset, $searchInput)
    // {
    //     return $this->model->where('type', 'LIKE', "%{$searchInput}%")
    //         ->offset($offset)
    //         ->limit($this->limit)
    //         ->orderBy('created_at', 'asc')
    //         ->get();
    // }
    // public function countActivity($searchInput)
    // {
    //     return $this->model->where('type', 'LIKE', "%{$searchInput}%")
    //         // ->orWhere('phone_number', 'LIKE', "%{$searchInput}%")
    //         ->count();
    // }
    public function searchActivity($offset, $searchInput, $type)
    {
        // dd($type);
        // $limit = 10;
        // return $this->model
        //     ->selectRaw('type, COUNT(*) as total')
        //     ->where('type', 'LIKE', "%{$searchInput}%")
        //     ->orWhere('action', 'LIKE', "%{$searchInput}%")
        //     ->groupBy('type')
        //     ->offset($offset)
        //     ->limit($this->limit)
        //     ->get();
        return $this->model
            ->where('type', $type)
            ->whereHas('user', function ($query) use ($searchInput) {
                $query->where('name', 'LIKE', "%{$searchInput}%");
            })
            ->orderByDesc('created_at')
            ->offset($offset)
            ->limit($this->limit)
            ->get();
    }
    public function countActivity($searchInput, $type)
    {
        // return $this->model
        //     ->selectRaw('COUNT(0)')
        //     ->where('type', 'LIKE', "%{$searchInput}%")
        //     ->orWhere('action', 'LIKE', "%{$searchInput}%")
        //     ->groupBy('type')
        //     ->get()
        //     ->count();
        return $this->model
            ->where('type', $type)
            ->whereHas('user', function ($query) use ($searchInput) {
                $query->where('name', 'LIKE', "%{$searchInput}%");
            })
            ->count();
    }
}
