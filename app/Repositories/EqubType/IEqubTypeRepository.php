<?php


namespace App\Repositories\EqubType;


interface IEqubTypeRepository
{

   public function getAll();

   public function getStatusById($id);

   public function getStartDate($id);

   public function getLable();

   public function getByDate($dateFrom,$dateTo);

   public function getDeactive();

   public function getActive();

    public function getById($id);

    public function create(array $attributes);

    public function update($id, array $attributes);

    public function delete($id);

    public function getEqubs();

}
