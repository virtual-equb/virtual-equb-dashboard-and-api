<?php

namespace App\Repositories\MainEqub;

use App\Models\MainEqub;

class MainEqubRepository implements MainEqubRepositoryInterface
{
    private $model;
    private $limit;

    public function __construct(MainEqub $mainEqub)
    {
        $this->model = $mainEqub;
    }

    public function all() {
        return MainEqub::with('subEqub')->get();
    }

    public function find($id) {
        return $this->model->find($id);
    }

    public function active()
    {
        return $this->model->active()->get();
    }

    public function inactive()
    {
        return $this->model->inactive()->get();
    }
}