<?php


namespace App\Repositories\Member;


interface IMemberRepository
{
    public function getAll();

    public function getAllByPaginate($offset);

    public function getAllPendingByPaginate($offset);

    public function getAllByPaginateApp($offset);

    public function getMemberWithEqub();

    public function getActiveMember();

    public function getEqubTypeActiveMember($equbType);

    public function getActiveMemberNotification();

    public function getById($id);

    public function getPhone($id);

    public function getByIdToDelete($id);

    public function getMemberById($id);

    public function getMembersByEqubType($equbType);

    public function getMemberWithPayment($id);

    public function getByPhone($phone);

    public function getByDate($dateFrom, $dateTo, $offset);

    public function getCountByDate($dateFrom, $dateTo);

    public function getStatusById($id);

    public function getEqubs($id);

    public function getMember();

    public function getPendingMembers();

    public function getAllPendingMembers();

    public function getAllPendingMembersNotification();

    public function getEqubTypeMember($equbTypeId);

    public function countMember($searchInput);

    public function countPendingMember($searchInput);

    public function countEqubMember($searchInput);

    public function countPendingEqubMember($searchInput);

    public function countStatusMember($searchInput);

    public function searchMember($offset, $searchInput);

    public function searchPendingMember($offset, $searchInput);

    public function searchEqub($offset, $searchInput);

    public function searchPendingEqub($offset, $searchInput);

    public function searchStatus($offset, $searchInput);

    public function create(array $attributes);

    public function getByIdNested($id);

    public function update($id, array $attributes);

    public function countActiveEqubs($memberId);

    public function countCompletedEqubs($memberId);

    public function delete($id);

    public function forceDelete($id);

    public function checkPhone($phone);
}
