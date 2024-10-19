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

    /**
     * Get all cities.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAll()
    {
        return $this->model->all(); // Retrieve all cities
    }

    /**
     * Get an active city by ID.
     *
     * @param int $id
     * @return City|null
     */
    public function getById($id)
    {
        return $this->model->where('active', true)->find($id); // Retrieve active city by ID
    }
    public function getCity()
    {
        return $this->model->all()->count();
    }
    /**
     * Get a city by ID (for deletion).
     *
     * @param int $id
     * @return City|null
     */
    public function getByIdToDelete($id)
    {
        return $this->model->find($id); // Retrieve city by ID for deletion
    }

    /**
     * Create a new city.
     *
     * @param array $data
     * @return City
     */
    public function create(array $data)
    {
        return $this->model->create($data); // Create a new city
    }

    /**
     * Update an existing city.
     *
     * @param int $id
     * @param array $data
     * @return City|null
     */
    public function update($id, array $data)
    {
        $city = $this->model->find($id); // Find city by ID
        if ($city) {
            $city->update($data); // Update city with new data
            return $city;
        }
        return null; // Return null if city not found
    }

    /**
     * Delete a city.
     *
     * @param int $id
     * @return bool|null
     */
    public function delete($id)
    {
        $city = $this->getByIdToDelete($id); // Get city to delete
        return $city ? $city->delete() : null; // Delete city if found
    }
}