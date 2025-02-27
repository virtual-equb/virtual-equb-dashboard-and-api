<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Repositories\User\IUserRepository;
use App\Repositories\ActivityLog\IActivityLogRepository;

class WebRoleController extends Controller
{
    private $activityLogRepository;
    private $userRepository;
    private $title;
    
    public function __construct(IUserRepository $userRepository, IActivityLogRepository $activityLogRepository)
    {
        $this->activityLogRepository = $activityLogRepository;
        $this->userRepository = $userRepository;
        $this->title = "Virtual Equb - User";
    }

    public function index()
    {
        $roles = Role::where('guard_name', 'web')->get();

        $totalRoles = Role::where('guard_name', 'web')->count();

        return view('rolePermission.role.index', ['title' => $this->title, 'roles' => $roles], compact('totalRoles'));
    }

    public function create()
    {
        return view('rolePermission.role.create', ['title' => $this->title]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required'
        ]);

        $roleApi = Role::create([
            'name' => $request->name,
            'guard_name' => 'api'
        ]);

        $roleWeb = Role::create([
            'name' => $request->name,
            'guard_name' => 'web'
        ]);

        return redirect('roles')->with('status', 'Role created successfully ');
    }

    public function show($id)
    {
        //
    }

    public function edit(Role $role)
    {
        return view('rolePermission.role.edit', ['title' => $this->title, 'role' => $role]);
    }

    public function update(Request $request, $roleId)
    {
        $role = Role::findOrFail($roleId);
        $request->validate([
            'name' => 'required'
        ]);

        Role::where('name', $role->name)->whereIn('guard_name', ['web', 'api'])->update([
            'name' => $request->name
        ]);

        return redirect('roles')->with('status', 'Role updated succesfully');
    }

    public function destroy($roleId)
    {
        $role = Role::findOrFail($roleId);
        
        Role::where('name', $role->name)->whereIn('guard_name', ['web', 'api'])->delete();

        return redirect('roles')->with('status', 'Role deleted successfully');
    }

    public function assignPermission($roleId)  {

        $role = Role::findOrFail($roleId);

        $permissions = Permission::where('guard_name', 'web')->get();

        $rolePermissions = DB::table('role_has_permissions')
                            ->where('role_has_permissions.role_id', $role->id)
                            ->pluck('role_has_permissions.permission_id', 'role_has_permissions.permission_id')
                            ->all(); 

        return view('rolePermission.role.assign-permission', 
                    [
                         'title' => $this->title,
                         'role' => $role,
                         'permissions' => $permissions,
                         'rolePermissions' => $rolePermissions
                    ]);
    }

    public function updateRolePermission(Request $request, $roleId) {

        $request->validate([
            'permission' => 'required|array'
        ]);

        $role = Role::findOrFail($roleId);
        $roleName = $role->name;

        foreach(['web', 'api'] as $guard) {
            $role = Role::firstOrCreate(['name' => $roleName, 'guard_name' => $guard]);
            $role->syncPermissions($request->permission);
        }
        

        return redirect()->back()->with('status', 'Permissions added to role');
    }
}