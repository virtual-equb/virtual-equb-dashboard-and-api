<?php


namespace App\Repositories\EqubTaker;


interface IEqubTakerRepository
{
    public function getAll();

    public function getAllEqubTaker($id);

    public function getById($id);

    public function getByEqubId($equb_id);

    public function getMemberId();

    public function getPaidMemberId();

    public function getMemberIdById($id);

    public function getEkubTaker($equbId, $memberId);

    public function getEkubTakerById($id);

    public function getTotalEqubAmount($equb_id);

    public function getTotalPaidLotterAmount($equb_id);

    public function getByDate($dateFrom,$dateTo,$offset);

    public function getWithDateAndEqub($dateFrom,$dateTo,$equb_type_id,$offset);

    public function getWithDateAndMember($dateFrom,$dateTo,$member_id,$offset);

    public function getWithDateMemberAndEqub($dateFrom,$dateTo,$member_id,$equb_type_id,$offset);

     public function getCountWithDateAndEqub($dateFrom,$dateTo,$equb_type_id);

     public function getCountByDate($dateFrom,$dateTo);

     public function getCountWithDateAndMember($dateFrom,$dateTo,$member_id);

     public function getCountWithDateMemberAndEqub($dateFrom,$dateTo,$member_id,$equb_type_id);

     public function getReportById();

    public function getByIdNested($id);

    // public function getAllEqubTakerById($id);

    public function getSinglePayment($member_id,$equb_id);

    public function getPayment($id);

    public function getTotal($id);

    public function getDaylyPaidAmount();

    public function getDaylyUnpaidAmount();

    public function getDaylyPendingAmount();


    public function getWeeklyPaidAmount();

    public function getWeeklyUnpaidAmount();

    public function getWeeklyPendingAmount();


    public function getMonthlyPaidAmount();

    public function getMonthlyUnpaidAmount();

    public function getMonthlyPendingAmount();


    public function getYearlyPaidAmount();

    public function getYearlyUnpaidAmount();

    public function getYearlyPendingAmount();


    public function create(array $attributes);

    public function update($id, array $attributes);

    public function updatePayment($equb_id,array $attributes);

    public function delete($id);

}
