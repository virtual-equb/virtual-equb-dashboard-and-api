<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Services\DeletionService;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Artisan;

class RolesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $roles = Role::all();
        $title = "Permission";
        return view('settings.permission_settings', [
            'roles' => $roles,
            'title' => $title // Add the title to the data array
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $equb = Permission::where('name', 'like', '%equb%')->get()->sortBy('name');
        $title = "Permission";
        
        return view('roles.create_role', [ 'title' => $title,'equb' => $equb,]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $formFields = $request->validate([
            'name' => ['required']
        ]);

        $formFields['guard_name'] = 'web';
        $title = "Permission";
        $role = Role::create($formFields);
        $filteredPermissions = array_filter($request->input('permissions'), function ($permission) {
            return $permission != 0;
        });
        $role->permissions()->sync($filteredPermissions);
        Artisan::call('cache:clear');

        Session::flash('message', 'Role created successfully.');
        return response()->json(['error' => false]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $title = "Permission";
        $role = Role::findOrFail($id);
        $role_permissions = $role->permissions;
        $guard = $role->guard_name == 'api' ? 'api' : 'web';
        return view('roles.edit_role', ['role' => $role, 'role_permissions' => $role_permissions, 'guard' => $guard, 'user' => getAuthenticatedUser()]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // Validate the incoming request
        $formFields = $request->validate([
            'name' => ['required']
        ]);
    
        // Find the role by ID
        $role = Role::findOrFail($id);
        $role->name = $formFields['name'];

        $role->save();
    
        // Filter and sync permissions
        $filteredPermissions = array_filter($request->input('permissions'), function ($permission) {
            return $permission != 0;
        });
        $role->permissions()->sync($filteredPermissions);
       // dd( $filteredPermissions);
        // Clear cache
        Artisan::call('cache:clear');
    
        // Flash message and redirect
        Session::flash('message', 'Role updated successfully.');
        return redirect($request->input('redirect_url')); // Assumes you have a redirect URL in the form
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function destroy($id)
    {
        $title = "Permission";
        $response = DeletionService::delete(Role::class, $id, 'Role');
        return $response;
    }

    public function create_permission()
    {
        Permission::create(['name' => 'edit_equb', 'guard_name' => 'web']);
    }

    public function rolesPermision()
    {
        $rolesAndPermissions = $this->getRolesAndPermissions();
        return response()->json($rolesAndPermissions);
    }
    public function getRolesAndPermissions()
    {
        return Role::with('permissions')->get()->map(function ($role) {
            return [
                'name' => $role->name,
                'permissions' => $role->permissions->pluck('name')->toArray(),
            ];
        });
    }
}
