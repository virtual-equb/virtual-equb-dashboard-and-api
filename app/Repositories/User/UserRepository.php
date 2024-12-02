<?php


namespace App\Repositories\User;


use App\Models\User;
use Illuminate\Support\Facades\Auth;
use phpDocumentor\Reflection\PseudoTypes\True_;
use Spatie\Permission\Models\Role;

class UserRepository implements IUserRepository
{
    private $model;
    private $limit;

    public function __construct(User $user)
    {
        $this->model = $user;
        $this->limit = 10;
    }

    public function getRoles()
    {
        return Role::where('guard_name', 'web')->get();
    }

    public function getUserRoles()
    {
        return User::with('roles')->get();
    }

    public function getAll()
    {
        return $this->model->paginate(50, ['*'], 'page');
    }
    public function countUser($searchInput)
    {
        return $this->model->where('name', 'LIKE', "%{$searchInput}%")->orWhere('phone_number', 'LIKE', "%{$searchInput}%")->count();
    }
    public function searchUser($offset, $searchInput)
    {
        return $this->model->where('name', 'LIKE', "%{$searchInput}%")
            ->orWhere('phone_number', 'LIKE', "%{$searchInput}%")
            ->offset($offset)
            ->limit($this->limit)
            ->orderBy('name', 'asc')
            ->get();
    }
    public function getUserId($member_phone)
    {
        return $this->model->where('phone_number', $member_phone)->first();
    }

    public function getCollecters()
    {
        // return $this->model
        //     ->where('role', 'admin')
        //     ->orWhere('role', 'equb_collector')
        //     ->orWhere('role', 'finance')
        //     ->orWhere('role', 'customer_service')
        //     ->get();

        // Alternative User Helper
        // $users = User::all()->filter(function ($user) {
        //     return $user->hasAnyRole(['admin', 'equb_collector', 'finance', 'call_center']);
        // });
        // return $users;

        return User::whereHas('roles', function ($query) {
            $query->whereIn('name', ['admin', 'equb_collector', 'finance', 'call_center']);
        });
    }
    public function getUsersWithRoles(array $roles)
    {
        return User::whereHas('roles', function ($query) use ($roles) {
            $query->whereIn('name', $roles);
        })->get();
    }
    

    public function getUser()
    {
        return $this->model->where('enabled', 1)->count();
    }

    public function getDeactivatedUser()
    {
        return $this->model->where('enabled', 0)->count();
    }

    public function getDeactive($offset)
    {
        return $this->model->orderBy('name', 'asc')->where('enabled', 0)->offset($offset)->limit($this->limit)->get();
    }

    public function getActive($offset)
    {
        return $this->model->orderBy('name', 'asc')->where('enabled', 1)->offset($offset)->limit($this->limit)->get();
    }
    public function getActiveForUsers($offset, $id)
    {
        return $this->model->with('roles')->orderBy('name', 'asc')->where('id', '!=', $id)->where('gender', '!=', '')->where('enabled', 1)->offset($offset)->limit($this->limit)->get();
    }

    public function getById($id)
    {
        return $this->model->findOrFail($id);
    }

    public function checkPhone($phone_number)
    {
        return $this->model->where('phone_number', $phone_number)->first() ? 1 : 0;
    }

    public function getByPhone($phoneNumber)
    {
        return $this->model->where('phone', $phoneNumber)->orderBy('created_at', 'desc')->get();
    }

    public function createUser(array $attributes)
    {
        return $this->model->create($attributes);
    }

    public function updateUser($id, array $attributes)
    {
        $user = User::findOrFail($id);
        $user->update($attributes);
        return $user;
        // return $this->model->where("id", $id)->update($attributes);
    }

    public function deleteUser($id)
    {
        return $this->model->where("id", $id)->delete();
    }

    public function forceDeleteUser($id)
    {
        return $this->model->where("id", $id)->forceDelete();
    }

    public function getAllActiveUsers($role)
    {

        return $this->model->where([['role', $role], ['is_active', true]])->orderBy('created_at', 'desc')->get();
    }

    public function getAllDeactivatedUsers($role)
    {

        return $this->model->where([['role', $role], ['is_active', false]])->orderBy('created_at', 'desc')->get();
    }

    public function getActiveUsersByLimit($role, $offset)
    {

        $userData = Auth::user();
        if ($userData && $userData['id'] != null) {
            return $this->model->where([["id", '!=', $userData['id']], ['is_active', true], ['role', $role]])
                ->offset($offset)
                ->limit($this->limit)
                ->orderBy('created_at', 'desc')
                ->get();
        } else {
            return $this->model->where([['is_active', true], ['role', $role]])
                ->offset($offset)
                ->limit($this->limit)
                ->orderBy('created_at', 'desc')
                ->get();
        }
    }

    public function getDeactivatedUsersByLimit($role, $offset)
    {

        $userData = Auth::user();
        if ($userData && $userData['id'] != null) {
            return $this->model->where([["id", '!=', $userData['id']], ["id", '!=', '0f5d3447-88b9-4e03-bde0-7b43db29346c'], ['is_active', false], ['role', $role]])
                ->offset($offset)
                ->limit($this->limit)
                ->orderBy('created_at', 'desc')
                ->get();
        } else {
            return $this->model->where([['is_active', false], ['role', $role]])
                ->offset($offset)
                ->limit($this->limit)
                ->orderBy('created_at', 'desc')
                ->get();
        }
    }

    public function countActiveUsers($role)
    {
        $userData = Auth::user();
        if ($userData && $userData['id'] != null) {
            return $this->model->where([["id", '!=', $userData['id']], ["id", '!=', '0f5d3447-88b9-4e03-bde0-7b43db29346c'], ['is_active', true], ['role', $role]])->count();
        } else {
            return $this->model->where([['is_active', true], ['role', $role]])->count();
        }
    }

    public function countDeactivatedUsers($role)
    {
        $userData = Auth::user();
        if ($userData && $userData['id'] != null) {
            return $this->model->where([["id", '!=', $userData['id']], ["id", '!=', '0f5d3447-88b9-4e03-bde0-7b43db29346c'], ['is_active', false], ['role', $role]])->count();
        } else {
            return $this->model->where([['is_active', false], ['role', $role]])->count();
        }
    }

    public function checkUserExistenceWithEmail($email)
    {
        return $this->model->where('email', $email)->first();
    }

    public function checkUserExistenceWithPhone($phone)
    {
        return $this->model->where('phone', $phone)->first();
    }

    public function activateUser($id)
    {
        return $this->model->where("id", $id)->update(['is_active' => true]);
    }

    public function deactivateUser($id)
    {
        return $this->model->where("id", $id)->update(['is_active' => false]);
    }

    public function getPassword()
    {

        $length = 10;
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ@!#$%&()';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    public function countActiveUsersSearchResult($role, $searchInput)
    {
        $userData = Auth::user();
        if ($userData && $userData['id'] != null) {
            return $this->model->where([["id", '!=', $userData['id']], ["id", '!=', '0f5d3447-88b9-4e03-bde0-7b43db29346c'], ['is_active', true], ['role', $role], ['name', 'LIKE', "%{$searchInput}%"]])->orWhere([["id", '!=', $userData['id']], ["id", '!=', '0f5d3447-88b9-4e03-bde0-7b43db29346c'], ['is_active', true], ['role', 'client'], ['phone', 'LIKE', "%{$searchInput}%"]])->count();
        } else {
            return $this->model->where([['is_active', true], ['role', $role], ['name', 'LIKE', "%{$searchInput}%"]])->orWhere([["id", '!=', $userData['id']], ["id", '!=', '0f5d3447-88b9-4e03-bde0-7b43db29346c'], ['is_active', true], ['role', 'client'], ['phone', 'LIKE', "%{$searchInput}%"]])->count();
        }
    }

    public function countDeactivatedUsersSearchResult($role, $searchInput)
    {
        $userData = Auth::user();
        if ($userData && $userData['id'] != null) {
            return $this->model->where([["id", '!=', $userData['id']], ["id", '!=', '0f5d3447-88b9-4e03-bde0-7b43db29346c'], ['is_active', false], ['role', $role], ['name', 'LIKE', "%{$searchInput}%"]])->orWhere([["id", '!=', $userData['id']], ["id", '!=', '0f5d3447-88b9-4e03-bde0-7b43db29346c'], ['is_active', false], ['role', 'client'], ['phone', 'LIKE', "%{$searchInput}%"]])->count();
        } else {
            return $this->model->where([['is_active', false], ['role', $role], ['name', 'LIKE', "%{$searchInput}%"]])->orWhere([["id", '!=', $userData['id']], ["id", '!=', '0f5d3447-88b9-4e03-bde0-7b43db29346c'], ['is_active', false], ['role', 'client'], ['phone', 'LIKE', "%{$searchInput}%"]])->count();
        }
    }

    public function searchActiveUsers($role, $offset, $searchInput)
    {
        $userData = Auth::user();
        if ($userData && $userData['id'] != null) {
            return $this->model->where([["id", '!=', $userData['id']], ["id", '!=', '0f5d3447-88b9-4e03-bde0-7b43db29346c'], ['is_active', true], ['role', $role], ['name', 'LIKE', "%{$searchInput}%"]])
                ->orWhere([["id", '!=', $userData['id']], ["id", '!=', '0f5d3447-88b9-4e03-bde0-7b43db29346c'], ['is_active', true], ['role', $role], ['phone', 'LIKE', "%{$searchInput}%"]])
                ->offset($offset)
                ->limit($this->limit)
                ->orderBy('created_at', 'desc')
                ->get();
        } else {
            return $this->model->where([['is_active', true], ['role', $role], ['name', 'LIKE', "%{$searchInput}%"]])
                ->orWhere([['is_active', true], ['role', $role], ['phone', 'LIKE', "%{$searchInput}%"]])
                ->offset($offset)
                ->limit($this->limit)
                ->orderBy('created_at', 'desc')
                ->get();
        }
    }

    public function searchDeactivatedUsers($role, $offset, $searchInput)
    {
        $userData = Auth::user();
        if ($userData && $userData['id'] != null) {
            return $this->model->where([["id", '!=', $userData['id']], ["id", '!=', '0f5d3447-88b9-4e03-bde0-7b43db29346c'], ['is_active', false], ['role', $role], ['name', 'LIKE', "%{$searchInput}%"]])
                ->orWhere([["id", '!=', $userData['id']], ["id", '!=', '0f5d3447-88b9-4e03-bde0-7b43db29346c'], ['is_active', false], ['role', $role], ['phone', 'LIKE', "%{$searchInput}%"]])
                ->offset($offset)
                ->limit($this->limit)
                ->orderBy('created_at', 'desc')
                ->get();
        } else {
            return $this->model->where([['is_active', false], ['role', $role], ['name', 'LIKE', "%{$searchInput}%"]])
                ->orWhere([['is_active', false], ['role', $role], ['phone', 'LIKE', "%{$searchInput}%"]])
                ->offset($offset)
                ->limit($this->limit)
                ->orderBy('created_at', 'desc')
                ->get();
        }
    }
}
