<?php

namespace App\Repositories\SubCity;

use App\Models\Sub_city;

class SubCityRepository implements ISubCityRepository
{
    private $model;

    public function __construct(Sub_city $subCity)
    {
        $this->model = $subCity;
    }

    public function getAll()
    {
        return $this->model->all(); // Retrieve all sub-cities
    }
  
    public function getById($id)
    {
        return $this->model->where('active', true)->find($id); // Retrieve active sub-city by ID
    }

    public function getSubCityByCityId($cityId)
    {
        return $this->model->where('city_id', $cityId)->get(); // Retrieve sub-cities for a specific city
    }

    public function getSubCityById($id){
        
        return $this->model->findOrFail($id);
    }

    public function create(array $data)
    {
        return $this->model->create($data); // Create a new sub-city
    }

    public function update($id, array $data)
    {
        $subCity = $this->model->find($id); // Find the sub-city by ID
        if ($subCity) {
            $subCity->update($data); // Update the sub-city with new data
            return $subCity;
        }
        return null; // Return null if sub-city not found
    }

    public function delete($id)
    {
        $subCity = $this->getByIdToDelete($id); // Get the sub-city to delete
        return $subCity ? $subCity->delete() : null; // Delete sub-city if found
    }

    public function getByIdToDelete($id)
    {
        return $this->model->find($id); // Retrieve sub-city by ID for deletion
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