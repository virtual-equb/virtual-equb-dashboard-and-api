<?php

namespace App\Repositories\SubCity;

use App\Models\SubCity;

class SubCityRepository implements ISubCityRepository
{
    private $model;

    public function __construct(SubCity $subCity)
    {
        $this->model = $subCity;
    }

    /**
     * Get all sub-cities.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAll()
    {
        return $this->model->all(); // Retrieve all sub-cities
    }

    /**
     * Get an active sub-city by ID.
     *
     * @param int $id
     * @return SubCity|null
     */
    public function getById($id)
    {
        return $this->model->where('active', true)->find($id); // Retrieve active sub-city by ID
    }

    /**
     * Get sub-cities by city ID.
     *
     * @param int $cityId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getSubCityByCityId($cityId)
    {
        return $this->model->where('city_id', $cityId)->get(); // Retrieve sub-cities for a specific city
    }

    /**
     * Create a new sub-city.
     *
     * @param array $data
     * @return SubCity
     */
    public function create(array $data)
    {
        return $this->model->create($data); // Create a new sub-city
    }

    /**
     * Update an existing sub-city.
     *
     * @param int $id
     * @param array $data
     * @return SubCity|null
     */
    public function update($id, array $data)
    {
        $subCity = $this->model->find($id); // Find the sub-city by ID
        if ($subCity) {
            $subCity->update($data); // Update the sub-city with new data
            return $subCity;
        }
        return null; // Return null if sub-city not found
    }

    /**
     * Delete a sub-city.
     *
     * @param int $id
     * @return bool|null
     */
    public function delete($id)
    {
        $subCity = $this->getByIdToDelete($id); // Get the sub-city to delete
        return $subCity ? $subCity->delete() : null; // Delete sub-city if found
    }

    /**
     * Get a sub-city by ID (for deletion).
     *
     * @param int $id
     * @return SubCity|null
     */
    public function getByIdToDelete($id)
    {
        return $this->model->find($id); // Retrieve sub-city by ID for deletion
    }
}