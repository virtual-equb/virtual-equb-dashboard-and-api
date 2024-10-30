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
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    private $activityLogRepository;
    private $userRepository;
    private $title;
    public function __construct(IUserRepository $userRepository, IActivityLogRepository $activityLogRepository)

    {
        //$this->middleware('auth');
        $this->activityLogRepository = $activityLogRepository;
        $this->userRepository = $userRepository;
        $this->title = "Virtual Equb - User";

        // // Guard Permission
        // $this->middleware('permission:edit role', ['only' => ['update', 'edit', 'updatePermissionToRole']]);
        // $this->middleware('permission:delete role', ['only' => ['destroy']]);
        // $this->middleware('permission:view role', ['only' => ['index', 'show']]);
        // $this->middleware('permission:create role', ['only' => ['store', 'create', 'addPermissionToRole']]);
    }
    public function index()
    {
        $roles = Role::get();

        return view('rolePermission.role.index', ['title' => $this->title, 'roles' => $roles]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('rolePermission.role.create', ['title' => $this->title]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
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

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Role $role)
    {
        return view('rolePermission.role.edit', ['title' => $this->title, 'role' => $role]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
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

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($roleId)
    {
        $role = Role::findOrFail($roleId);
        
        Role::where('name', $role->name)->whereIn('guard_name', ['web', 'api'])->delete();

        return redirect('roles')->with('status', 'Role deleted successfully');
    }

    public function assignPermission($roleId)  {

        $role = Role::findOrFail($roleId);

        $permissions = Permission::get();

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
