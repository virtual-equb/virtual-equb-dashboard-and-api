<?php


namespace App\Repositories\RejectedDate;


interface IRejectedDateRepository
{
   public function getAll();

    public function getById($id);

    public function create(array $attributes);

    public function update($id, array $attributes);

    public function delete($id);

    public function forceDelete($id);

    public function getRejectedDateAfterToday();
}