<?php

namespace App\Http\Controllers\Api;

use App\Models\Roles;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:edit', ['only' => ['update', 'edit']]);
        $this->middleware('permission:delete', ['only' => ['destroy']]);
        $this->middleware('permission:read', ['only' => ['index', 'show']]);
        $this->middleware('permission:create', ['only' => ['store', 'create']]);
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
            'name' => 'required|unique:permissions,name'
        ]);

        $permission = Permission::create([
            'name' => $request->name
        ]);

        return response()->json([
            'message' => 'Permission created successfully',
            'data' => $permission
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
    public function update(Request $request, Permission $permission)
    {
        $request->validate([
            'name' => 'required|unique:permissions,name,' . $permission->id
        ]);

        $permission->update(['name' => $request->name]);

        return response()->json([
            'message' => 'Permission updated successfully',
            'data' => $permission
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Permission $permission)
    {
        $permission->delete();

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
