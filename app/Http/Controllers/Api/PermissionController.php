<?php

namespace App\Http\Controllers\Api;

use App\Models\Roles;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Exception;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionController extends Controller
{
    public function __construct()
    {
        // $this->middleware('permission:edit', ['only' => ['update', 'edit']]);
        // $this->middleware('permission:delete', ['only' => ['destroy']]);
        // $this->middleware('permission:read', ['only' => ['index', 'show']]);
        // $this->middleware('permission:create', ['only' => ['store', 'create']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->json(Permission::all());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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

        $permissionWeb = Permission::create([
            'name' => $request->name,
            'guard_name' => 'web'
        ]);

        $permissionApi = Permission::create([
            'name' => $request->name,
            'guard_name' => 'api'
        ]);

        return response()->json([
            'message' => 'Permission created successfully',
            'data' => [
                'web' => $permissionWeb,
                'api' => $permissionApi
            ]
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Permission $permission)
    {
        return response()->json($permission);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $permissionId)
    {
        $request->validate([
            'name' => 'required'
        ]);

        try {
            $permission = Permission::findOrFail($permissionId);

            Permission::where('name', $permission->name)->whereIn('guard_name', ['web', 'api'])->update([
                'name' => $request->name
            ]);

            return response()->json([
                'message' => 'Permission updated successfully for both guards',
                'data' => [
                    'web' => Permission::where('name', $request->name)->where('guard_name', 'web')->first(),
                    'api' => Permission::where('name', $request->name)->where('guard_name', 'api')->first()
                ]
            ]);

        } catch (Exception $ex) {
            return response()->json([
                'message' => 'Error updating permission',
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
    public function destroy($permissionId)
    {
        try {
            $permission = Permission::findOrFail($permissionId);

            Permission::where('name', $permission->name)->whereIn('guard_name', ['web', 'api'])->delete();

            return response()->json([
                'message' => 'Role Deleted successfully',
                'deleted_role' => $permission->name
            ]);

        } catch (Exception $ex) {
            return response()->json([
                'message' => 'Error deleting permission',
                'error' => $ex->getMessage()
            ], 400);
        }

        return response()->json([
            'message' => 'Permission deleted successfully',
        ]);
    }

    public function assignPermission(Request $request, $roleId)
    {
        $request->validate([
            'permissions' => 'required|array',  // Validate that 'permissions' is an array
            'permissions.*' => 'exists:permissions,name',  // Ensure each permission exists
        ]);

        // Fetch the role by ID
        $role = Roles::findOrFail($roleId);

        // Assign the permissions to the role
        $role->syncPermissions($request->input('permissions'));

        return response()->json([
            'message' => 'Permissions assigned successfully!',
            'role' => $role->load('permissions'),  // Optionally load the permissions relation
        ]);
    }
}
