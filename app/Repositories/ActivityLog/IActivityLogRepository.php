<?php

namespace App\Repositories\ActivityLog;


interface IActivityLogRepository
{

    public function getAll();

    public function getById($id);

    public function getByAdminId($id);

    public function createActivityLog(array $attributes);

    public function countActivityLog($type, $searchInput = null);

    public function getAllActivityLog($type, $offset, $searchInput);

    public function countActivityLogType($type, $id);

    public function countByType();

    public function paginateCountByType($offset);

    public function totalCountByType();

    public function forceDeleteLog($id);

    public function forceDeleteLogByUserId($userId);

    public function searchActivity($offset, $searchInput, $type);

    public function countActivity($searchInput, $type);
}
