<?php


namespace App\Repositories\EqubType;

use App\Models\EqubType;
use App\Models\MainEqub;

class EqubTypeRepository implements IEqubTypeRepository
{
    private $model;
    private $limit;
    public function __construct(EqubType $equbType)
    {
        $this->model = $equbType;
        $this->limit = 10;
    }

    public function getEqubs()
    {
        return MainEqub::all();
    }
    public function getAll()
    {
        // $query = EqubType::with('mainEqub');
        // $result = $query->get();
        
        // if ($result->isEmpty()) {
        //     dd('No records found');
        // }
    
        // return $result;
        return $this->model->with('mainEqub')->get();
    }
    public function getStartDate($id)
    {
        return $this->model->where('id', $id)->first();
    }

    public function getByDate($dateFrom, $dateTo)
    {
        return $this->model->where([
            ['created_at', '>=', $dateFrom],
            ['created_at', '<=', $dateTo]
        ])->get();
    }

    public function getLable()
    {
        return $this->model->distinct('name')->groupBy('equb_types.name')->pluck('name');
    }
    public function getDeactive()
    {
        return $this->model->where('status', 'Deactive')->get();
    }

    public function getActive()
    {
        return $this->model->where('status', 'Active')->get();
    }

    public function getById($id)
    {
        return $this->model->with('mainEqub')->find($id);
    }

    public function getStatusById($id)
    {
        return $this->model->find($id);
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
}