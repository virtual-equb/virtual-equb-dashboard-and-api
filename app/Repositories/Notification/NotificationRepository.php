<?php


namespace App\Repositories\Notification;

use App\Models\Notification;

class NotificationRepository implements INotificationRepository
{
    private $model;
    private $limit;
    public function __construct(Notification $notification)
    {
        $this->model = $notification;
        $this->limit = 10;
    }

    public function getAll()
    {
        return $this->model->orderBy('created_at', 'desc')->get();
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
        // dd($id);
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
