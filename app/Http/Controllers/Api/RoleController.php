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
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::with('roles')->get();
        return response()->json([
            'roles' => Role::all(),
            'users' => $users
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
            'name' => 'required|unique:roles,name'
        ]);

        $role = Role::create([
            'name' => $request->name
        ]);

        return response()->json([
            'message' => 'Role created successfully',
            'data' => $role
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
    public function update(Request $request, Role $role)
    {
        $request->validate([
            'name' => 'required|unique:roles,name,' . $role->id 
        ]);

        $role->update([
            'name' => $request->name
        ]);

        return response()->json([
            'message' => 'Role updated successfully',
            'data' => $role
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Role $role)
    {
        $role->delete();

        return response()->json([
            'message' => 'Role Deleted successfully'
        ]);
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

    // public function assignPermissionRole(Request $request, $roleId)
    // {
    //     // $request->validate([
    //     //     'roles' => 'required|array',  // Validate that 'roles' is an array
    //     //     'roles.*' => 'exists:roles,name',  // Ensure each role exists
    //     // ]);

    //     // // Fetch the user by ID
    //     // $user = User::findOrFail($roleId);

    //     // // Assign the roles to the user
    //     // $user->syncRoles($request->input('roles'));

    //     // return response()->json([
    //     //     'message' => 'Roles assigned successfully!',
    //     //     'user' => $user->load('roles'),  // Optionally load the roles relation
    //     // ]);
    //     $request->validate([
    //         'permission' => 'required|array'
    //     ]);

    //     $role = Role::findOrFail($roleId);
    //     $role->syncPermissions($request->permission);


    // }

}
