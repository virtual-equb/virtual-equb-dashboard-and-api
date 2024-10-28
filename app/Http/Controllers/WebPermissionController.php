<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Spatie\Permission\Models\Permission;
use App\Repositories\User\IUserRepository;
use App\Repositories\ActivityLog\IActivityLogRepository;

class WebPermissionController extends Controller
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
    }
    
    public function index()
    {
        if (request()->route()->getPrefix() === 'api') {
        // Request is coming from an API route
        } else {
            // Request is coming from a web route
            $data['title'] = $this->title;
            $permissions = Permission::where('guard_name', 'web')->get();
        }
        

        return view('rolePermission.permission.index', ['title' => $this->title, 'permissions' => $permissions]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('rolePermission.permission.create', ['title' => $this->title]);
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

        return redirect('/permission')->with('status', 'Permission created successfully');
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
    public function edit(Permission $permission)
    {
        // $permission = Permission
        return view('rolePermission.permission.edit', ['title' => $this->title, 'permission' => $permission]);
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

        $permission = Permission::findOrFail($permissionId);

        Permission::where('name', $permission->name)->whereIn('guard_name', ['web', 'api'])->update([
            'name' => $request->name
        ]);

        return redirect('permission')->with('status', 'Permission updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($permissionId)
    {
        $permission = Permission::find($permissionId);
        Permission::where('name', $permission->name)->whereIn('guard_name', ['web', 'api'])->delete();

        return redirect('permission')->with('status', 'Permission Deleted successfully');
    }
}
