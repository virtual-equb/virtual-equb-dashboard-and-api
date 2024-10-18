<?php


namespace App\Repositories\RejectedDate;

use App\Models\RejectedDate;

class RejectedDateRepository implements IRejectedDateRepository
{
    private $model;
    private $limit;
    public function __construct(RejectedDate $rejectedDate)
    {
        $this->model = $rejectedDate;
        $this->limit = 10;
    }

    public function getAll()
    {
        return $this->model->all();
    }

    public function getById($id)
    {
        return $this->model->find($id);
    }

    public function create(array $attributes)
    {
        return $this->model->create($attributes);
    }

    public function update($id, array $attributes)
    {
        return $this->model->where('id',$id)->update($attributes);
    }

    public function delete($id)
    {
        return $this->model->where('id',$id)->delete();
    }

    public function forceDelete($id)
    {
        return $this->model->where('id',$id)->forceDelete();
    }

}
