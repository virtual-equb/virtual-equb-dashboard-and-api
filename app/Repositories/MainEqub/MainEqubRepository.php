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
        // get all datas
        // return $this->model->all();
        return MainEqub::with('subEqub')->get();
    }

    public function find($id) {
        return $this->model->find($id);
    }
}