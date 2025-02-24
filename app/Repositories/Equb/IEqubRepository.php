<?php


namespace App\Repositories\Equb;


interface IEqubRepository
{
    public function getAll();

    public function getDailyStats();

    public function getWeeklyStats();

    public function getMonthlyStats();

    public function getYearlyStats();

    public function getAllWithPagination($limit);

    public function getByMember($id);

    public function getExpected($equbTypeId);

    public function getAutomaticExpected($equbTypeId);

    public function getManualExpected($equbTypeId);

    public function getMemberByEqubType($id);

    public function getCountByDateAndEqubType($dateFrom, $dateTo, $equbType);

    public function getByDateAndEqubType($dateFrom, $dateTo, $equbType, $offset);

    public function getById($id);

    public function getDailyPaid($equb_id);

    public function getLotteryDate();

    public function getExpectedAmount();

    public function getAutomaticExpectedAmount();

    public function getManualExpectedAmount();

    public function getEqubTypeExpectedAmount($equbTypeId);

    public function geteEubById($id);

    public function getExpectedTotal();

    public function getAutomaticExpectedTotal();

    public function getManualExpectedTotal();

    public function getEqubTypeExpectedTotal($equbTypeId);

    public function getUnPaidLotteryCount($member_id);

    public function getUnPaidLotteryByEqubTypeCount($member_id, $equbType);

    public function getByMemeberIdAndEqubType($memberId, $equbTypeId);

    public function getByEqubTypeId($equbTypeId);

    public function getUnPaidLottery($member_id, $offset);

    public function updateUnPaidLotteryToPaid($member_id, $offset);

    public function getUnPaidLotteryByEqubType($member_id, $equbType, $offset);

    public function getUnPaidLotteryByLotteryDateCount($member_id, $lotteryDate, $equbType);

    public function getUnPaidLotteryByLotteryDate($member_id, $lotteryDate, $offset, $equbType);

    public function getReservedLotteryDatesCount($dateFrom, $dateTo, $memberId, $equbType);

    public function getReservedLotteryDates($dateFrom, $dateTo, $memberId, $offset, $equbType);

    public function getExpectedByLotteryDate($lotteryDate);

    public function getExpectedBackPayment();

    public function getAutomaticExpectedBackPayment();

    public function getManualExpectedBackPayment();

    public function getEqubTypeExpectedBackPayment($equbTypeId);

    public function getRemainingLotteryAmount($id);

    public function getStatusById($id);

    public function getByDate($dateFrom, $dateTo, $equbType, $offset);
    
    public function getByPaymentMethod($dateFrom, $dateTo, $equbType, $offset);

    public function getUnPaidByDate($dateFrom, $dateTo, $equbId, $offset, $equbType);

    public function getCountByDate($dateFrom, $dateTo, $equbType);

    public function getCountUnPaidByDate($dateFrom, $dateTo, $equbId, $equbType);

    public function getByLotteryDate($dateFrom, $dateTo, $offset);

    public function getCountByLotteryDate($dateFrom, $dateTo);

    public function getWithDateAndMember($dateFrom, $dateTo, $member_id);

    public function getEqubType($id);

    public function getMember($id);

    public function getMemberIdById($id);

    //    public function getMemberIdByEqubTypeId($id);

    public function getEqubAmount($member_id, $equb_id);

    public function getTotalEqubAmount($equb_id);

    public function tudayPaidMember();

    public function tudayEqubTypePaidMember($equbTypeId);

    public function todayPaidEqubTypesAutomatic();

    public function todayPaidMembersAutomatic($equbTypeId);

    public function getTotalAmount();

    public function getActiveMember();

    public function getByIdNested($id);

    public function getByIdNestedForLottery($id);

    public function create(array $attributes);

    public function update($id, array $attributes);

    public function updateEqubStatus($member_id, array $attributes);

    public function delete($id);

    public function filterEqubEndDates($dateFrom, $dateTo, $offset, $equbType);
    public function filterEqubByPaymentMethod($dateFrom, $dateTo, $offset, $equbType);
    public function countFilterEqubEndDates($dateFrom, $dateTo, $equbType);
}
