<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Models\Roles;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function __construct()
    {
        $this->middleware('api_permission_check:edit role', ['only' => ['update', 'edit', 'updatePermissionToRole']]);
        $this->middleware('api_permission_check:delete role', ['only' => ['destroy']]);
        $this->middleware('api_permission_check:view role', ['only' => ['index', 'show']]);
        $this->middleware('api_permission_check:create role', ['only' => ['store', 'create', 'addPermissionToRole']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // $users = User::with('roles')->where('guard_name', 'web')->get();
        return response()->json([
            'roles' => Role::all(),
            // 'users' => $users
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        
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

        $roleWeb = Role::create([
            'name' => $request->name,
            'guard_name' => 'web'
        ]);

        $roleApi = Role::create([
            'name' => $request->name,
            'guard_name' => 'api'
        ]);

        return response()->json([
            'message' => 'Role created successfully',
            'data' =>[
                'web' => $roleWeb,
                'api' => $roleApi
            ]
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Role $role)
    {
        return response()->json($role);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Role $role)
    {
        return $role;
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
        $request->validate([
            'name' => 'required'
        ]);

        try {
            // Find the role by ID, using the current guard name (could be web or api)
            $role = Role::findOrFail($roleId);

            // Update both roles with the same name for 'web' and 'api' guards
            Role::where('name', $role->name)->whereIn('guard_name', ['web', 'api'])->update([
                'name' => $request->name
            ]);

            return response()->json([
                'message' => 'Role updated successfully for both guards',
                'data' => [
                    'web' => Role::where('name', $request->name)->where('guard_name', 'web')->first(),
                    'api' => Role::where('name', $request->name)->where('guard_name', 'api')->first()
                ]
            ]);
        } catch (Exception $ex) {
            return response()->json([
                'message' => 'Error updating role',
                'error' => $ex->getMessage()
            ], 400);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($roleId)
    {
        try {
            $role = Role::findOrFail($roleId);

            Role::where('name', $role->name)->whereIn('guard_name', ['web', 'api'])->delete();

            return response()->json([
                'message' => 'Role Deleted successfully',
                'deleted_role' => $role->name
            ]);

        } catch (Exception $ex) {
            return response()->json([
                'message' => 'Error deleting role',
                'error' => $ex->getMessage()
            ], 400);
        }
        
    }

    public function addPermissionToRole($roleId) {
        
        try {
            $permissions = Permission::get();
            $role = Role::findOrFail($roleId);
            $rolePermissions = DB::table('role_has_permissions')
                                ->where('role_has_permissions.role_id', $role->id)
                                ->pluck('role_has_permissions.permission_id','role_has_permissions.permission_id')->all();

            return response()->json([
                'permissions' => $permissions,
                'role' => $role,
                'rolePermissions' => $rolePermissions
            ]);
        } catch (Exception $ex) {
            return response()->json([
                'code' => 400,
                'error' => $ex->getMessage()
            ]);
        }
        
    }

    public function updatePermissionToRole(Request $request, $roleId) 
    {
        try {
            $request->validate([
                'permission' => 'required'
            ]);
    
            $role = Role::findOrFail($roleId);
            $role->syncPermissions($request->permission);
    
            return response()->json([
                'data' => $role,
                'message' => 'Permissions added to role'
            ]);

        } catch (Exception $ex) {
            return response()->json([
                'code' => 400,
                'error' => $ex->getMessage()
            ]);
        }
        

    }

}
