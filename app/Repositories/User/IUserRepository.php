<?php


namespace App\Repositories\User;


interface IUserRepository
{
    public function getAll();

    public function countUser($searchInput);

    public function searchUser($offset, $searchInput);

    public function getUser();

    public function getUserId($member_phone);

    public function getDeactivatedUser();

    public function getCollecters();

    public function getById($id);

    public function getDeactive($offset);

    public function getActive($offset);

    public function getActiveForUsers($offset, $id);

    public function checkPhone($phone_number);

    public function getByPhone($phoneNumber);

    public function createUser(array $attributes);

    public function updateUser($id, array $attributes);

    public function deleteUser($id);

    public function forceDeleteUser($id);

    public function getAllActiveUsers($role);

    public function getAllDeactivatedUsers($role);

    public function getActiveUsersByLimit($role,$offset);

    public function getDeactivatedUsersByLimit($role,$offset);

    public function countActiveUsers($role);

    public function countDeactivatedUsers($role);

    public function checkUserExistenceWithEmail($email);

    public function checkUserExistenceWithPhone($phone);

    public function activateUser($id);

    public function deactivateUser($id);

    public function getPassword();

    public function countActiveUsersSearchResult($role,$searchInput);

    public function countDeactivatedUsersSearchResult($role,$searchInput);

    public function searchActiveUsers($role,$offset,$searchInput);

    public function searchDeactivatedUsers($role,$offset,$searchInput);
}
