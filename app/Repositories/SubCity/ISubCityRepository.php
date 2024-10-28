<?php

namespace App\Repositories\SubCity;

interface ISubCityRepository
{
    public function getAll();
    
    public function getById($id);

    public function getByIdToDelete($id);

    public function getSubCityById($id);

    public function getSubCityByCityId($cityId);

    public function create(array $data);

    public function update($id, array $data);

    public function delete($id);
}