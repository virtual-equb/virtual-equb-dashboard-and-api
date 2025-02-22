<?php

namespace App\Repositories\City;

use App\Models\City;

class CityRepository implements ICityRepository
{
    private $model;
    private $limit;

    public function __construct(City $city)
    {
        $this->model = $city;
        $this->limit = 50; // Default limit if needed
    }

    public function getAll()
    {
        return $this->model->all(); // Retrieve all cities
    }
    public function getCityById($id){
        
        return $this->model->findOrFail($id);
    }
    public function getActiveCity()
    {
        return $this->model->where('name', 'Addis Ababa')->get(); // Retrieve the active city
    }

    public function getById($id)
    {
        return $this->model->where('active', true)->find($id); // Retrieve active city by ID
    }
    public function getCity()
    {
        return $this->model->all()->count();
    }

    public function getByIdToDelete($id)
    {
        return $this->model->find($id);
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update($id, array $data)
    {
        $city = $this->model->find($id);
        if ($city) {
            $city->update($data);
            return $city;
        }
        return null;
    }

    public function delete($id)
    {
        $city = $this->getByIdToDelete($id);
        return $city ? $city->delete() : null;
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