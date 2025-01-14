<?php


namespace App\Repositories\Payment;


interface IPaymentRepository
{
    public function getAll();

    public function getAllPayment();

    public function getAllPendingPayments();
    public function getAllPendingByPaginate($offset);
    public function getAllPaidByPaginate($offset);
    public function getAllPaidPayments();

    public function getTotalPayment();

    public function getEqubTypeTotalPayment($equbTypeId);

    public function getById($id);

    public function getByReferenceId($id);

    public function getByMemberId($member_id, $equb_id);

    public function getLastId($id);

    public function getAmount($id);

    public function getPaidByDate($dateFrom, $dateTo);

    public function getByDate($dateFrom, $dateTo, $offset);

    public function getWithDateAndEqub($dateFrom, $dateTo, $equbType_id, $offset);

    public function getWithDateAndMember($dateFrom, $dateTo, $member_id, $offset);

    public function getWithDateMemberAndEqub($dateFrom, $dateTo, $member_id, $equb_id, $offset);

    public function getSinglePayment($member_id, $equb_id, $offset);

    public function getPayment($id);

    public function getPayments();

    public function countPendingPayments();

    public function getCountWithDateAndEqub($dateFrom, $dateTo, $equb_id);

    public function getCountWithDate($dateFrom, $dateTo);

    public function getCountDateAndMember($dateFrom, $dateTo, $member_id);
   
    public function getCountWithDateMemberAndEqub($dateFrom, $dateTo, $member_id, $equb_id);

    public function getCollectedByUser($dateFrom, $dateTo, $collecter, $offset, $equbType);

    public function getByPaymentMethod($dateFrom, $dateTo, $equbType, $offset);

    
    public function getCountCollectedBys($dateFrom, $dateTo);

    public function getCountCollectedBysWithCollecter($dateFrom, $dateTo, $collecter, $equbtype);

    public function getEqub($id);

    public function getEqubForDelete($id);

    public function getTotal($id);

    public function getTotalPaid($id);

    public function getTotalCount($id);

    public function getTotalCredit($id);

    public function getTotalBalance($id);

    ///

    public function getPaidAmount();

    public function getUnpaidAmount();

    public function getPendingAmount();
    ///

    public function getDaylyPaidAmount();

    public function getEqubTypeDaylyPaidAmount($equbTypeId);

    public function getDaylyUnpaidAmount();

    public function getDaylyPendingAmount();

    public function getEqubTypeDaylyPendingAmount($equbTypeId);


    public function getWeeklyPaidAmount();

    public function getEqubTypeWeeklyPaidAmount($equbTypeId);

    public function getWeeklyUnpaidAmount();

    public function getWeeklyPendingAmount();

    public function getEqubTypeWeeklyPendingAmount($equbTypeId);


    public function getMonthlyPaidAmount();

    public function getEqubTypeMonthlyPaidAmount($equbTypeId);

    public function getMonthlyUnpaidAmount();

    public function getMonthlyPendingAmount();

    public function getEqubTypeMonthlyPendingAmount($equbTypeId);


    public function getYearlyPaidAmount();

    public function getEqubTypeYearlyPaidAmount($equbTypeId);

    public function getYearlyUnpaidAmount();

    public function getYearlyPendingAmount();

    public function getEqubTypeYearlyPendingAmount($equbTypeId);


    public function create(array $attributes);

    public function updateCredit($equb_id, array $attributes);

    public function updateBalance($equb_id, array $attributes);

    public function updateAmount($equb_id, array $attributes);

    public function update($id, array $attributes);

    public function delete($id);

    public function deleteAll($member_id, $equb_id);

    public function forceDelete($id);

    public function searchPendingPayment($offset, $searchInput);

    public function searchPendingPaymentCount($searchInput);

    public function searchPaidPayment($offset, $searchInput);

    public function searchPaidPaymentCount($searchInput);
    
}
