<?php

namespace App\Repositories\MainEqub;

interface MainEqubRepositoryInterface
{
    public function all(); // Retrieve all MainEqubs
    
    public function find($id); // Retrieve MainEqub by id
}