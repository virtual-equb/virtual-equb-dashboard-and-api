<?php

namespace App\Http\Controllers;

use Mail;
use Exception;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use App\Repositories\User\IUserRepository;
use App\Repositories\ActivityLog\IActivityLogRepository;

class UserController extends Controller
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
        try {
            $this->middleware('auth');
            $data['title'] = $this->title;
            $data['roles'] = $this->userRepository->getRoles();
                
            return view('admin/user.admins', $data);
        } catch (Exception $ex) {
            $msg = "Unable to process your request, Please try again!";
            $type = 'error';
            Session::flash($type, $msg);
            return back();
        }
    }
    
    public function indexForDeactivated()
    {
        try {
            $this->middleware('auth');
            $data['title'] = $this->title;

            return view('admin/user.admins', $data);

        } catch (Exception $ex) {
            $msg = "Unable to process your request, Please try again!";
            $type = 'error';
            Session::flash($type, $msg);
            return back();
        }
    }

    public function user($offsetVal, $pageNumberVal)
    {
        try {
            $offset = $offsetVal;
            $pageNumber = $pageNumberVal;
            $userData = Auth::user();
            $this->middleware('auth');
            $data['title'] = $this->title;
            $data['totalUser'] = $this->userRepository->getUser();;
            $data['pageNumber'] = $pageNumber;
            $data['offset'] = $offset;
            $data['limit'] = 10;
            $data['deactivatedUsers']  = $this->userRepository->getDeactive($offset);
            $data['activeUsers']  = $this->userRepository->getActiveForUsers($offset, $userData->id);
            
            return view('admin/user/activeUser', $data);

        } catch (Exception $ex) {
            $msg = "Unable to process your request, Please try again!";
            $type = 'error';
            Session::flash($type, $msg);
            return back();
        }
    }

    public function deactiveUser($offsetVal, $pageNumberVal)
    {
        try {
            $offset = $offsetVal;
            $pageNumber = $pageNumberVal;
            $this->middleware('auth');
            $data['title'] = $this->title;
            $data['totalDeacivatedUser'] = $this->userRepository->getDeactivatedUser();;
            $data['pageNumber'] = $pageNumber;
            $data['offset'] = $offset;
            $data['limit'] = 50;
            $data['deactivatedUsers']  = $this->userRepository->getDeactive($offset);
            $data['activeUsers']  = $this->userRepository->getActive($offset);

            return view('admin/user/deactivatedUser', $data);

        } catch (Exception $ex) {
            $msg = "Unable to process your request, Please try again!";
            $type = 'error';
            Session::flash($type, $msg);
            return back();
        }
    }

    public function phoneCheck(Request $request)
    {
        try {
            if (!empty($request->phone)) {
                $phoneCheck = $request->phone;
                $users_count = User::where('phone', $phoneCheck)->count();
                if ($users_count > 0) {
                    echo "false";
                } else {
                    echo "true";
                }
            } else {
                echo "true";
            }
        } catch (Exception $ex) {
            $msg = "Unknown Error Occurred, Please try again!";
            $type = 'error';
            Session::flash($type, $msg);
            return back();
        }
    }

    public function userPhoneCheck(Request $request)
    {
        try {
            if (!empty($request->phone_number)) {
                $userId = $request->user_id;
                $phoneCheck = $request->phone_number;
                $users_count = User::where('phone_number', $phoneCheck)->where('id', '!=', $userId)->count();
                if ($users_count > 0) {
                    echo "false";
                } else {
                    echo "true";
                }
            } else {
                echo "true";
            }
        } catch (Exception $ex) {
            $msg = "Unknown Error Occurred, Please try again!";
            $type = 'error';
            Session::flash($type, $msg);
            return back();
        }
    }

    public function resetPassword(Request $request)
    {
        try {
            $u_id = $request->input('u_id');
            $password = rand(100000, 999999);
            $user = User::where('id', $u_id)->first();
                $updated = [
                    'password' => Hash::make($password),
                ];
                $updated = $this->userRepository->updateUser($u_id, $updated);
                if ($updated) {
                    try {
                        $shortcode = config('key.SHORT_CODE');
                        $message = "Your Virtual Equb password has been reset to $password. You can now login through the app. For further information please call " . $shortcode;
                        $this->sendSms($user->phone_number, $message);
                    } catch (Exception $ex) {
                        return redirect()->back()->with('error', 'Failed to send SMS' . $ex->getMessage());
                    };
                    $msg = "Password has been changed successfully!";
                    $type = 'success';
                    Session::flash($type, $msg);
                    return back();
                } else {
                    $msg = "Unknown error occurred, Please try again!";
                    $type = 'error';
                    Session::flash($type, $msg);
                    return back();
                }
        } catch (Exception $ex) {
            $msg = "Unable to process your request, Please try again!";
            $type = 'error';
            Session::flash($type, $msg);
            return back();
        }
    }

    public function emailCheck(Request $request)
    {
        try {
            if (!empty($request->email)) {
                $userId = $request->user_id;
                $emailCheck = $request->email;
                $users_count = User::where('email', $emailCheck)->where('id', '!=', $userId)->count();
                if ($users_count > 0) {
                    echo "false";
                } else {
                    echo "true";
                }
            } else {
                echo "true";
            }
        } catch (Exception $ex) {
            $msg = "Unknown Error Occurred, Please try again!";
            $type = 'error';
            $error = $ex->getMessage();
            Session::flash($type, $msg);
            return back();
        }
    }

    public function store(Request $request)
    {
        try {
            $userData = Auth::user();
                $this->validate(
                    $request,
                    [
                        'name' => 'required',
                        'email' => 'required',
                        'phone_number' => 'required',
                        'gender' => 'required',
                        'role' => 'required|array',
                        // 'password' => 'required'
                    ]
                );
                $fullName = $request->input('name');
                $email = $request->input('email');
                $phone_number = $request->input('phone_number');
                $gender = $request->input('gender');
                $roles = $request->input('role');
                $password = rand(100000, 999999);
                $userData = [
                    'name' => $fullName,
                    'email' => $email,
                    'password' => Hash::make($password),
                    'phone_number' => $phone_number,
                    'gender' => $gender,
                ];
                $create = $this->userRepository->createUser($userData);
                
                if ($create) {
                    foreach ($roles as $roleName) {
                        // First, ensure the roles exist for each guard
                        $roleForWeb = Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
                        $roleForApi = Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'api']);
        
                        // Assign the roles to the user for both guards
                        $create->assignRole($roleForWeb);
                        $create->assignRole($roleForApi);
                    }
                    $userData = Auth::user();
                    $activityLog = [
                        'type' => 'users',
                        'type_id' => $create->id,
                        'action' => 'created',
                        'user_id' => $userData->id,
                        'username' => $userData->name,
                        'role' => $userData->role,
                    ];
                    $this->activityLogRepository->createActivityLog($activityLog);
                    try {
                        $shortcode = config('key.SHORT_CODE');
                        $message = "Welcome to Virtual Equb! You have registered succesfully. Your username is " . $email . " and your password is " . $password . ". For further information please call " . $shortcode;
                        $this->sendSms($request->phone_number, $message);
                    } catch (Exception $ex) {
                        return redirect()->back()->with('error', 'Failed to send SMS');
                    };
                    $msg = "User has been registered successfully!";
                    $type = 'success';
                    Session::flash($type, $msg);
                    return redirect('/user');
                } else {
                    $msg = "Unknown Error Occurred, Please try again!";
                    $type = 'error';
                    Session::flash($type, $msg);
                    redirect('/user');
                }
        } catch (Exception $ex) {
            $msg = $ex->getMessage();
            $type = 'error';
            Session::flash($type, $msg);
            return back();
        }
    }

    public function storeUser(Request $request) {
        $shortcode = config('key.SHORT_CODE');
        try {
            $request->validate([
                'name' => 'required',
                'email' => 'required',
                'gender' => 'required',
                'role' => 'required|array'
            ]);
            $roles = $request->input('role');
            $otp = random_int(100000, 999999);
    
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'gender' => $request->gender,
                'phone_number' => $request->phone_number,
                'password' => Hash::make($otp),
            ]);
            if ($user) {
    
                // Assign each role separately for both guards
                foreach ($roles as $roleName) {
                    // First, ensure the roles exist for each guard
                    $roleForWeb = Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
                    $roleForApi = Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'api']);
    
                    // Assign the roles to the user for both guards
                    $user->assignRole($roleForWeb);
                    $user->assignRole($roleForApi);
                }

                $userData = Auth::user();
                    $activityLog = [
                        'type' => 'users',
                        'type_id' => $userData->id,
                        'action' => 'created',
                        'user_id' => $userData->id,
                        'username' => $userData->name,
                        'role' => $userData->role,
                    ];
                    $this->activityLogRepository->createActivityLog($activityLog);
                    try {
                        $message = "Welcome to Virtual Equb! You have registered succesfully. Use the email address " . $request->phone . " and password " . $otp . " to log in." . " For further information please call " . $shortcode;
                        $this->sendSms($request->phone, $message);
                    } catch (Exception $ex) {
                        return redirect()->back()->with('error', $ex->getMessage());
                    };
            }
    
            $msg = "User has been registered successfully!";
            $type = 'success';
            Session::flash($type, $msg);
            return redirect('/user');
        } catch (Exception $ex) {
            $msg = $ex->getMessage();
            $type = 'error';
            Session::flash($type, $msg);
            return back();
        }
    }

    public function deactiveStatus($id, Request $request)
    {
        try {
            $userData = Auth::user();
                $enabled = 0;
                $updated = [
                    'enabled' => $enabled,
                ];
                $updated = $this->userRepository->updateUser($id, $updated);
                if ($updated) {
                    $activityLog = [
                        'type' => 'users',
                        'type_id' => $id,
                        'action' => 'deactivated',
                        'user_id' => $userData->id,
                        'username' => $userData->name,
                        'role' => $userData->role,
                    ];
                    $this->activityLogRepository->createActivityLog($activityLog);
                    $msg = "User deactivated successfully!";
                    $type = 'success';
                    Session::flash($type, $msg);
                    return back();
                } else {
                    $msg = "Unknown error occurred, Please try again!";
                    $type = 'error';
                    Session::flash($type, $msg);
                    return back();
                }
        } catch (Exception $ex) {
            $msg = "Unable to process your request, Please try again!";
            $type = 'error';
            Session::flash($type, $msg);
            return back();
        }
    }

    public function activeUser($id, Request $request)
    {
        try {
            $userData = Auth::user();
                $enabled = 1;
                $updated = [
                    'enabled' => $enabled,
                ];
                $updated = $this->userRepository->updateUser($id, $updated);
                if ($updated) {
                    $activityLog = [
                        'type' => 'users',
                        'type_id' => $id,
                        'action' => 'activet',
                        'user_id' => $userData->id,
                        'username' => $userData->name,
                        'role' => $userData->role,
                    ];
                    $this->activityLogRepository->createActivityLog($activityLog);
                    $msg = "User Activated successfully!";
                    $type = 'success';
                    Session::flash($type, $msg);
                    return back();
                } else {
                    $msg = "Unknown error occurred, Please try again!";
                    $type = 'error';
                    Session::flash($type, $msg);
                    return back();
                }
        } catch (Exception $ex) {
            $msg = "Unable to process your request, Please try again!";
            $type = 'error';
            Session::flash($type, $msg);
            return back();
        }
    }

    public function edit($id)
    {
        try {
                $userData = Auth::user();
                if ($userData) {
                    $user = User::find($id);
                    $data['user'] = $this->userRepository->getById($id);
                    $data['roles'] = Role::where('guard_name', 'web')->get();
                    $data['userRoles'] = $user->roles->where('guard_name', 'web')->pluck('name')->toArray();
                    return view('admin/user/editUser', $data);
                } else {
                    return back();
                }
        } catch (Exception $ex) {
            $msg = "Unable to process your request, Please try again!";
            $type = 'error';
            Session::flash($type, $msg);
            return back();
        }
    }
   
    public function update1($id, Request $request)
    {
        try {
            $userData = Auth::user();
            $this->validate(
                $request,
                [
                    'name' => 'required',
                    'email' => 'required',
                    'phone_number' => 'required',
                    'gender' => 'required',
                    'role' => 'required|array',
                ]
            );

            $name = $request->input('name');
            $email = $request->input('email');
            $phone = $request->input('phone_number');
            $gender = $request->input('gender');
            $roles = $request->input('role');

            // Update user details
            $updatedData = [
                'name' => $name,
                'email' => $email,
                'phone_number' => $phone,
                'gender' => $gender,
            ];
            $updated = $this->userRepository->updateUser($id, $updatedData);

            if ($updated) {
                // Ensure roles exist for both guards
                // $rolesForWeb = [];
                // $rolesForApi = [];

                // foreach ($roles as $roleName) {
                //     $rolesForWeb[] = Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
                //     $rolesForApi[] = Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'api']);
                // }

                // // Sync roles for each guard
                // $updated->syncRoles(collect($rolesForWeb)->pluck('name')->toArray());
                // $updated->syncRoles(collect($rolesForApi)->pluck('name')->toArray());
                $rolesForWeb = [];
                $rolesForApi = [];
    
                foreach ($roles as $roleName) {
                    $rolesForWeb[] = Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web'])->name;
                    $rolesForApi[] = Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'api'])->name;
                }
    
                // Sync roles for 'web' guard
                $updated->syncRoles($rolesForWeb);
    
                // Assign roles for 'api' guard
                $updated->syncRoles([]); // Clear all roles for API first
                foreach ($rolesForApi as $roleName) {
                    $role = Role::where('name', $roleName)->where('guard_name', 'api')->first();
                    if ($role) {
                        $updated->assignRole($role->name);
                    }
                }

                // Log activity
                $activityLog = [
                    'type' => 'users',
                    'type_id' => $id,
                    'action' => 'updated',
                    'user_id' => $userData->id,
                    'username' => $userData->name,
                    'role' => $userData->role,
                ];
                $this->activityLogRepository->createActivityLog($activityLog);

                $msg = "User detail has been updated successfully!";
                $type = 'success';
                Session::flash($type, $msg);
                return back();
            } else {
                $msg = "Unknown error occurred, Please try again!";
                $type = 'error';
                Session::flash($type, $msg);
                return back();
            }
        } catch (Exception $ex) {
            $msg = "Unable to process your request, Please try again!" . $ex->getMessage();
            $type = 'error';
            Session::flash($type, $msg);
            return back();
        }
    }
    public function update($id, Request $request)
    {
        try {
            $userData = Auth::user();

            // Validate the request
            $this->validate(
                $request,
                [
                    'name' => 'required',
                    'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($id)],
                    'phone_number' => ['required',  'max:255', Rule::unique('users', 'phone_number')->ignore($id)],
                    'gender' => 'required',
                    'role' => 'required|array',
                ]
            );

            $name = $request->input('name');
            $email = $request->input('email');
            $phone = $request->input('phone_number');
            $gender = $request->input('gender');
            $roles = $request->input('role');

            // Update user details
            $updatedData = [
                'name' => $name,
                'email' => $email,
                'phone_number' => $phone,
                'gender' => $gender,
            ];
            $updated = $this->userRepository->updateUser($id, $updatedData);

            if ($updated) {
                // Sync roles for the 'web' guard
                // $rolesForWeb = [];
                // foreach ($roles as $roleName) {
                //     $rolesForWeb[] = Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web'])->name;
                // }
                // $updated->syncRoles($rolesForWeb); // Sync roles for the 'web' guard

                // // Handle roles for the 'api' guard
                // // First, remove existing roles for the 'api' guard
                // $currentApiRoles = $updated->roles()->where('guard_name', 'api')->get();
                // foreach ($currentApiRoles as $role) {
                //     $updated->removeRole($role->name);
                // }

                // // Assign new roles for the 'api' guard
                // foreach ($roles as $roleName) {
                //     $roleApi = Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'api']);
                //     $updated->assignRole($roleApi->name); // Assign role for 'api'
                // }
                $rolesForWeb = [];
                foreach ($roles as $roleName) {
                    $roleWeb = Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
                    $rolesForWeb[] = $roleWeb->name;
                }
                $updated->syncRoles($rolesForWeb); // Sync roles for 'web'

                // Clear and assign roles for the 'api' guard
                // First, remove existing roles for 'api'
                $updated->roles()->where('guard_name', 'api')->delete();

                // Then assign new roles for 'api'
                foreach ($roles as $roleName) {
                    $roleApi = Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'api']);
                    DB::table('model_has_roles')->insert([
                        'role_id' => $roleApi->id,
                        'model_type' => get_class($updated),
                        'model_id' => $updated->id,
                    ]);
                }


                // Log activity
                $activityLog = [
                    'type' => 'users',
                    'type_id' => $id,
                    'action' => 'updated',
                    'user_id' => $userData->id,
                    'username' => $userData->name,
                    'role' => $userData->role,
                ];
                $this->activityLogRepository->createActivityLog($activityLog);

                Session::flash('success', "User detail has been updated successfully!");
                return back();
            } else {
                Session::flash('error', "Unknown error occurred, Please try again!");
                return back();
            }
        } catch (Exception $ex) {
            Session::flash('error', "Unable to process your request, Please try again! " . $ex->getMessage());
            return back();
        }
    }

    public function destroy($id)
    {
        try {
                $userData = Auth::user();
                $user = $this->userRepository->getById($id);
                if ($user != null) {
                    $check = $this->activityLogRepository->getByAdminId($id);
                    if ($check) {
                        $msg = "User has history and can not be deleted";
                        $type = 'error';
                        Session::flash($type, $msg);
                        return back();
                    }
                    $deleted = $this->userRepository->deleteUser($id);
                    if ($deleted) {
                        $activityLog = [
                            'type' => 'users',
                            'type_id' => $id,
                            'action' => 'deleted',
                            'user_id' => $userData->id,
                            'username' => $userData->name,
                            'role' => $userData->role,
                        ];
                        $this->activityLogRepository->createActivityLog($activityLog);
                        $msg = "User has been deleted successfully!";
                        $type = 'success';
                        Session::flash($type, $msg);
                        return back();
                    } else {
                        $msg = "Unknown Error Occurred, Please try again!";
                        $type = 'error';
                        Session::flash($type, $msg);
                        return back();
                    }
                } else {
                    return false;
                }
        } catch (Exception $ex) {
            $msg = "Unable to process your request, Please try again!";
            $type = 'error';
            Session::flash($type, $msg);
            return $msg;
        }
    }

    public function searchUser($searchInput, $offset, $pageNumber = null)
    {
        try {
            $userData = Auth::user();
            $adminRoles = ['admin', 'general_manager', 'operation_manager', 'it', 'call_center'];
            $memberRole = ['member', 'equb_collector'];
            if ($userData && $userData->hasAnyRole($adminRoles)) {
                $data['offset'] = $offset;
                $limit = 50;
                $data['limit'] = $limit;
                $data['totalMember'] = $this->userRepository->countUser($searchInput);
                if ($offset == 0) {
                    $data['pageNumber'] = 1;
                } else {
                    $data['pageNumber'] = $pageNumber;
                }
                $data['searchInput'] = $searchInput;
                $data['users'] = $this->userRepository->searchUser($offset, $searchInput);
                // dd($data['users']);
                return view('admin/user/searchUsers', $data)->render();
            } elseif ($userData && $userData->hasAnyRole($memberRole)) {
                $data['offset'] = $offset;
                $limit = 50;
                $data['limit'] = $limit;
                $data['totalMember'] = $this->userRepository->countUser($searchInput);
                if ($offset == 0) {
                    $data['pageNumber'] = 1;
                } else {
                    $data['pageNumber'] = $pageNumber;
                }
                $data['searchInput'] = $searchInput;
                $data['users'] = $this->userRepository->searchUser($offset, $searchInput);
                return view('equbCollecter/user/searchUsers', $data)->render();
            }
        } catch (Exception $ex) {
            $msg = "Unable to process your request, Please try again!";
            $type = 'error';
            Session::flash($type, $msg);
            return back();
        }
    }
}
