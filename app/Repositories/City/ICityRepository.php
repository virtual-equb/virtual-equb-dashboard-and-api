<?php

namespace App\Repositories\City;

interface ICityRepository
{
    public function getAll();
    
    public function getCity();
    
    public function getActiveCity();

    public function getById($id);

    public function create(array $data);

    public function update($id, array $data);

    public function delete($id);

    public function active();

    public function inactive();
}