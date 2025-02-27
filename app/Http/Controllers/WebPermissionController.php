<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Spatie\Permission\Models\Permission;
use App\Repositories\User\IUserRepository;
use App\Repositories\ActivityLog\IActivityLogRepository;

class WebPermissionController extends Controller
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
        if (request()->route()->getPrefix() === 'api') {
        } else {
            $data['title'] = $this->title;
            $permissions = Permission::where('guard_name', 'web')->get();

            $totalPermission = Permission::where('guard_name', 'web')->count();
        }

        return view('rolePermission.permission.index', ['title' => $this->title, 'permissions' => $permissions], compact('totalPermission'));
    }

    public function create()
    {
        return view('rolePermission.permission.create', ['title' => $this->title]);
    }

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

    public function show($id)
    {
        //
    }

    public function edit(Permission $permission)
    {
        return view('rolePermission.permission.edit', ['title' => $this->title, 'permission' => $permission]);
    }

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

    public function destroy($permissionId)
    {
        $permission = Permission::find($permissionId);
        Permission::where('name', $permission->name)->whereIn('guard_name', ['web', 'api'])->delete();

        return redirect('permission')->with('status', 'Permission Deleted successfully');
    }
}
