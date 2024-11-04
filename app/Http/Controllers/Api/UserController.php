<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Mail;
use App\Models\User;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\UserResource;
use App\Models\Permission;
use App\Models\Roles;
use Exception;
use App\Repositories\User\IUserRepository;
use App\Repositories\ActivityLog\IActivityLogRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;

/**
 * @group Users
 */
class UserController extends Controller
{
    private $activityLogRepository;
    private $userRepository;
    private $title;
    private $afroApiKey;
    private $afroSenderId;
    private $afroSenderName;
    private $afroBaseUrl;
    private $afroSpaceBefore;
    private $afroSpaceAfter;
    private $afroExpiresIn;
    private $afroLength;
    private $afroType;

    public function __construct(IUserRepository $userRepository, IActivityLogRepository $activityLogRepository)
    {
        $this->middleware('auth:api')->except(['checkPhone', 'resetPasswordUser', 'sendOtp', 'verifyOtp']);
        $this->activityLogRepository = $activityLogRepository;
        $this->userRepository = $userRepository;
        $this->title = "Virtual Equb - User";
        $this->afroApiKey = config('key.AFRO_API_KEY');
        $this->afroSenderId = config('key.AFRO_IDENTIFIER_ID');
        $this->afroSenderName = config('key.AFRO_SENDER_NAME');
        $this->afroBaseUrl = config('key.AFRO_BASE_URL');

        $this->afroSpaceBefore = config('key.AFRO_SPACE_BEFORE_OTP');
        $this->afroSpaceAfter = config('key.AFRO_SPACE_AFTER_OTP');
        $this->afroExpiresIn = config('key.AFRO_OTP_EXPIRES_IN_SECONDS');
        $this->afroLength = config('key.AFRO_OPT_LENGTH');
        $this->afroType = config('key.AFRO_OTP_TYPE');

        // Permission Guard
        $this->middleware('api_permission_check:update user', ['only' => ['update', 'edit', 'activeUser', 'deactiveStatus']]);
        $this->middleware('api_permission_check:delete user', ['only' => ['destroy']]);
        $this->middleware('api_permission_check:view user', ['only' => ['index', 'show', 'user', 'deactiveUser']]);
        $this->middleware('api_permission_check:create user', ['only' => ['store', 'create', 'resetPassword']]);
    }
    /**
     * Sent OTP
     *
     * This api sends OTP.
     *
     * @param phone int required The date to end filter. Example: +251913202020
     *
     * @return JsonResponse
     */
    // public function sendOtp($phone)
    // {

    //     $prefixMessage = "Your Verification Code is";
    //     $response = Http::withHeaders([
    //         'Authorization' => 'Bearer ' . $this->afroApiKey,
    //     ])
    //         ->baseUrl($this->afroBaseUrl)
    //         ->get('/challenge?from=' . $this->afroSenderId .
    //             '&sender=' . $this->afroSenderName .
    //             '&to=' . $phone .
    //             '&pr=' . $prefixMessage .
    //             '&sb=' . $this->afroSpaceBefore .
    //             '&sa=' . $this->afroSpaceAfter .
    //             '&ttl=' . $this->afroExpiresIn .
    //             '&len=' . $this->afroLength .
    //             '&t=' . $this->afroType);
    //     $responseData = $response->json();
    //     if ($responseData['acknowledge'] == 'success') {
    //         return ['acknowledge' => $responseData['acknowledge']];
    //     }
    //     return [
    //         'acknowledge' => $responseData['acknowledge'],
    //         'message' => $responseData['response']['errors']
    //     ];
    // }
    public function sendOtp($phone)
    {
        $prefixMessage = "Your Verification Code is";
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->afroApiKey,
        ])
        ->baseUrl($this->afroBaseUrl)
        ->withOptions(['verify' => false])
        // ->withOptions(['verify' => base_path('C:/wamp64/cacert.pem')])  
        ->get('/challenge', [
            'from' => $this->afroSenderId,
            'sender' => $this->afroSenderName,
            'to' => $phone,
            'pr' => $prefixMessage,
            'sb' => $this->afroSpaceBefore,
            'sa' => $this->afroSpaceAfter,
            'ttl' => $this->afroExpiresIn,
            'len' => $this->afroLength,
            't' => $this->afroType
        ]);

        $responseData = $response->json();
        if ($responseData['acknowledge'] == 'success') {
            return ['acknowledge' => $responseData['acknowledge']];
        }
        return [
            'acknowledge' => $responseData['acknowledge'],
            'message' => $responseData['response']['errors']
        ];
    }

    /**
     * Verify OTP
     *
     * This api verifies OTP.
     *
     * @param code int required The date to end filter. Example: 1234
     * @param phone int required The date to end filter. Example: +251913202020
     *
     * @return JsonResponse
     */
    public function verifyOtp($code, $phone)
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->afroApiKey,
        ])
            ->baseUrl($this->afroBaseUrl)
            ->get('/verify?&to=' . $phone . '&code=' . $code);
        $responseData = $response->json();
        if ($responseData['acknowledge'] == 'success') {
            return ['acknowledge' => $responseData['acknowledge']];
        }
        return [
            'acknowledge' => $responseData['acknowledge'],
            'message' => $responseData['response']['errors']
        ];
    }
    /**
     * Get all users
     *
     * This api returns all users.
     *
     * @return JsonResponse
     */
    public function index()
    {
        try {

            $userData = Auth::user();
            $this->middleware('auth');
            $data['title'] = $this->title;
            $users = User::get();
            
            return response()->json([
                'title' => $data['title'],
                'users' => UserResource::collection($users)
            ]);

        } catch (Exception $ex) {
            return response()->json([
                'code' => 500,
                'message' => 'Unable to process your request, Please try again!',
                "error" => $ex->getMessage()
            ]);
        }
    }
    public function indexForDeactivated()
    {
        try {

            $this->middleware('auth');
            $data['title'] = $this->title;

            return response()->json($data);

        } catch (Exception $ex) {
            return response()->json([
                'code' => 500,
                'message' => 'Unable to process your request, Please try again!',
                "error" => $ex->getMessage()
            ]);
        }
    }
    /**
     * Get all users with pagination
     *
     * This api returns all users with pagination.
     *
     * @param offsetVal int required The date to end filter. Example: 0
     * @param pageNumberVal int required The date to end filter. Example: 1
     *
     * @return JsonResponse
     */
    public function user($offsetVal, $pageNumberVal)
    {
        try {
            $offset = $offsetVal;
            $pageNumber = $pageNumberVal;
            $this->middleware('auth');
            $data['title'] = $this->title;
            $data['totalUser'] = $this->userRepository->getUser();;
            $data['pageNumber'] = $pageNumber;
            $data['offset'] = $offset;
            $data['limit'] = 50;
            $data['deactivatedUsers']  = $this->userRepository->getDeactive($offset);
            $data['activeUsers']  = $this->userRepository->getActive($offset);
            return response()->json($data);
        } catch (Exception $ex) {
            return response()->json([
                'code' => 500,
                'message' => 'Unable to process your request, Please try again!',
                "error" => $ex->getMessage()
            ]);
        }
    }
    /**
     * Get deactivated users with pagination
     *
     * This api returns deactivated users with pagination.
     *
     * @param offsetVal int required The date to end filter. Example: 0
     * @param pageNumberVal int required The date to end filter. Example: 1
     *
     * @return JsonResponse
     */
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
            return response()->json($data);

        } catch (Exception $ex) {
            return response()->json([
                'code' => 500,
                'message' => 'Unable to process your request, Please try again!',
                "error" => $ex->getMessage()
            ]);
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
            return response()->json([
                'code' => 500,
                'message' => 'Unable to process your request, Please try again!',
                "error" => $ex->getMessage()
            ]);
        }
    }
    /**
     * Check if phone exists
     *
     * This api checks if phone exists.
     *
     * @bodyParam phone string required The phone number of the user. Example: 0911222222
     *
     * @return JsonResponse
     */
    public function checkPhone(Request $request)
    {
        try {
            if (!empty($request->phone)) {
                $phoneCheck = $request->phone;
                $users_count = User::where('phone_number', $phoneCheck)->exists();
                $userId = User::where('phone_number', $phoneCheck)->first();
                $id = $userId ? $userId->id : null;
                return response()->json([
                    'code' => 200,
                    "data" => $users_count,
                    "userId" => $id
                ]);
            } else {
                return response()->json([
                    'code' => 400,
                    "message" => 'Phone should not be empty'
                ]);
            }
        } catch (Exception $ex) {
            return response()->json([
                'code' => 500,
                'message' => 'Unable to process your request, Please try again!',
                "error" => $ex->getMessage()
            ]);
        }
    }
    /**
     * Check if phone exists
     *
     * This api checks if phone exists.
     *
     * @bodyParam id string required The id of the user. Example: 1
     * @bodyParam phone string required The phone number of the user. Example: 0911222222
     *
     * @return JsonResponse
     */
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
            return response()->json([
                'code' => 500,
                'message' => 'Unable to process your request, Please try again!',
                "error" => $ex->getMessage()
            ]);
        }
    }
    /**
     * Reset Password
     *
     * This api resets users passwords.
     *
     * @bodyParam u_id string required The id of the user. Example: 1
     * @bodyParam password string required The password to be reset. Example: P@ssw0rd
     *
     * @return JsonResponse
     */
    public function resetPassword(Request $request)
    {
        try {
            $u_id = $request->input('u_id');
            $password = $request->input('reset_password');
                $updated = [
                    'password' => Hash::make($password),
                ];
                $updated = $this->userRepository->updateUser($u_id, $updated);
                if ($updated) {
                    return response()->json([
                        'code' => 200,
                        'message' => 'Reset password successfully',
                        'data' => new UserResource($updated)
                    ]);
                } else {
                    return response()->json([
                        'code' => 400,
                        'message' => 'Unknown error occurred, Please try again!',
                        "error" => "Unknown error occurred, Please try again!"
                    ]);
                }
        } catch (Exception $ex) {
            return response()->json([
                'code' => 500,
                'message' => 'Unable to process your request, Please try again!',
                "error" => $ex->getMessage()
            ]);
        }
    }
    /**
     * Reset Password From App
     *
     * This api resets users passwords.
     *
     * @bodyParam u_id string required The id of the user. Example: 1
     * @bodyParam reset_password string required The password to be reset. Example: P@ssw0rd
     *
     * @return JsonResponse
     */
    public function resetPasswordUser(Request $request)
    {
        try {
            $this->validate(
                $request,
                [
                    'u_id' => 'required',
                    'reset_password' => 'required',
                    'otp' => 'required'
                ]
            );
            $u_id = $request->input('u_id');
            $password = $request->input('reset_password');
            $otp = $request->input('otp');
            $user = $this->userRepository->getById($u_id);
            $otpCheck = $this->verifyOtp($otp, $user->phone_number);
            if ($otpCheck['acknowledge'] != 'success') {
                return response()->json([
                    'code' => 400,
                    'message' => 'Token is not valid. Please try again!',
                    "error" => "Token is not valid. Please try again!"
                ]);
            }
            $updated = [
                'password' => Hash::make($password),
            ];
            $updated = $this->userRepository->updateUser($u_id, $updated);
            if ($updated) {
                return response()->json([
                    'code' => 200,
                    'message' => 'Reset password successfully',
                    'data' => new UserResource($updated)
                ]);
            } else {
                return response()->json([
                    'code' => 400,
                    'message' => 'Unknown error occurred, Please try again!',
                    "error" => "Unknown error occurred, Please try again!"
                ]);
            }
        } catch (Exception $ex) {
            return response()->json([
                'code' => 500,
                'message' => 'Unable to process your request, Please try again!',
                "error" => $ex->getMessage()
            ]);
        }
    }
    /**
     * Change Password
     *
     * This api changes passwords.
     *
     * @param u_id string required The id of the user. Example: 1
     * @bodyParam old_password string required The old password to be reset. Example: P@ssw0rd
     * @bodyParam new_password string required The new password to be reset. Example: P@ssw0rd
     *
     * @return JsonResponse
     */
    public function changePassword(Request $request, $id)
    {
        try {
                $oldPasswordFromDatabase = User::where('id', $id)->pluck('password')->first();
                $oldPasswordFromView = $request->input('old_password');
                $newPassword = $request->input('new_password');
                if (Hash::check($oldPasswordFromView, $oldPasswordFromDatabase)) {
                    $updated = [
                        'password' => Hash::make($newPassword),
                    ];
                    $updated = $this->userRepository->updateUser($id, $updated);
                    if ($updated) {
                        return response()->json([
                            'code' => 200,
                            'message' => 'Password has been changed successfully',
                            'data' => new UserResource($updated)
                        ]);
                    } else {
                        return response()->json([
                            'code' => 400,
                            'message' => 'Unknown error occurred, Please try again!',
                            "error" => "Unknown error occurred, Please try again!"
                        ]);
                    }
                } else {
                    return response()->json([
                        'code' => 400,
                        'message' => 'Please enter correct old password'
                    ]);
                }
        } catch (Exception $ex) {
            return response()->json([
                'code' => 500,
                'message' => 'Unable to process your request, Please try again!',
                "error" => $ex->getMessage()
            ]);
        }
    }
    /**
     * Check if email exists
     *
     * This api checks if email exists.
     *
     * @bodyParam u_id string required The id of the user. Example: 1
     * @bodyParam email string required The email of the user. Example: eyob@gmail.com
     *
     * @return JsonResponse
     */
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
            return response()->json([
                'code' => 500,
                'message' => 'Unable to process your request, Please try again!',
                "error" => $ex->getMessage()
            ]);
        }
    }
    /**
     * Create user
     *
     * This api creates a user.
     *
     * @bodyParam name string required The name of the user. Example: eyob
     * @bodyParam email string required The email of the user. Example: eyob@gmail.com
     * @bodyParam phone_number string required The phone of the user. Example: 0911111111
     * @bodyParam gender string required The gender of the user. Example: male
     * @bodyParam role string required The role of the user. Example: admin
     *
     * @return JsonResponse
     */
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
                        'role' => 'required',
                    ]
                );
                $fullName = $request->input('name');
                $email = $request->input('email');
                $phone_number = $request->input('phone_number');
                $gender = $request->input('gender');
                $role = $request->input('role');
                $password = rand(100000, 999999);
                $userData = [
                    'name' => $fullName,
                    'email' => $email,
                    'password' => Hash::make($password),
                    'phone_number' => $phone_number,
                    'gender' => $gender,
                    'role' => $role,
                ];
                $create = $this->userRepository->createUser($userData);
                if ($create) {
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
                    return response()->json([
                        'code' => 200,
                        'message' => "User has been registered successfully! User password is " . $password,
                        'data' => new UserResource($create)
                    ]);
                } else {
                    return response()->json([
                        'code' => 400,
                        'message' => 'Unknown error occurred, Please try again!',
                        "error" => "Unknown error occurred, Please try again!"
                    ]);
                }
        } catch (Exception $ex) {
            return response()->json([
                'code' => 500,
                'message' => 'Unable to process your request, Please try again!',
                "error" => $ex->getMessage()
            ]);
        }
    }
    /**
     * Deactivate user
     *
     * This api deactivates a user.
     *
     * @param id string required The id of the user. Example: 1
     *
     * @return JsonResponse
     */
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
                        'action' => 'deactivet',
                        'user_id' => $userData->id,
                        'username' => $userData->name,
                        'role' => $userData->role,
                    ];
                    $this->activityLogRepository->createActivityLog($activityLog);
                    return response()->json([
                        'code' => 200,
                        'message' => 'User has been deactivated successfully!',
                        'data' => $updated
                    ]);
                } else {
                    return response()->json([
                        'code' => 400,
                        'message' => 'Unknown error occurred, Please try again!',
                        "error" => "Unknown error occurred, Please try again!"
                    ]);
                }
        } catch (Exception $ex) {
            return response()->json([
                'code' => 500,
                'message' => 'Unable to process your request, Please try again!',
                "error" => $ex->getMessage()
            ]);
        }
    }
    /**
     * Activate user
     *
     * This api activates a user.
     *
     * @param id string required The id of the user. Example: 1
     *
     * @return JsonResponse
     */
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
                    return response()->json([
                        'code' => 200,
                        'message' => 'Use has been activated successfully!',
                        'data' => new UserResource($updated)
                    ]);
                } else {
                    return response()->json([
                        'code' => 400,
                        'message' => 'Unknown error occurred, Please try again!',
                        "error" => "Unknown error occurred, Please try again!"
                    ]);
                }
        } catch (Exception $ex) {
            return response()->json([
                'code' => 500,
                'message' => 'Unable to process your request, Please try again!',
                "error" => $ex->getMessage()
            ]);
        }
    }
    public function edit($id)
    {
        try {
            $data['user'] = $this->userRepository->getById($id);
            return response()->json([
                'user' => new UserResource($data['user'])
            ]);
        } catch (Exception $ex) {
            return response()->json([
                'code' => 500,
                'message' => 'Unable to process your request, Please try again!',
                "error" => $ex->getMessage()
            ]);
        }
    }
    /**
     * Update user
     *
     * This api updates a user.
     *
     * @param id string required The id of the user. Example: 1
     * @bodyParam name string required The name of the user. Example: eyob
     * @bodyParam email string required The email of the user. Example: eyob@gmail.com
     * @bodyParam phone_number string required The phone of the user. Example: 0911111111
     * @bodyParam gender string required The gender of the user. Example: male
     * @bodyParam role string required The role of the user. Example: admin
     *
     * @return JsonResponse
     */
    public function update($id, Request $request)
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
                        'role' => 'required',
                    ]
                );
                $name = $request->input('name');
                $email = $request->input('email');
                $phone = $request->input('phone_number');
                $gender = $request->input('gender');
                $role = $request->input('role');
                $updated = [
                    'name' => $name,
                    'email' => $email,
                    'phone_number' => $phone,
                    'gender' => $gender,
                    'role' => $role,
                ];
                $updated = $this->userRepository->updateUser($id, $updated);
                if ($updated) {
                    $activityLog = [
                        'type' => 'users',
                        'type_id' => $id,
                        'action' => 'updated',
                        'user_id' => $userData->id,
                        'username' => $userData->name,
                        'role' => $userData->role,
                    ];
                    $this->activityLogRepository->createActivityLog($activityLog);

                    return response()->json([
                        'code' => 200,
                        'message' => 'User detail has been updated successfully!',
                        'data' => new UserResource($updated)
                    ]);
                } else {
                    return response()->json([
                        'code' => 400,
                        'message' => 'Unknown error occurred, Please try again!',
                        "error" => "Unknown error occurred, Please try again!"
                    ]);
                }
        } catch (Exception $ex) {
            return response()->json([
                'code' => 500,
                'message' => 'Unable to process your request, Please try again!',
                "error" => $ex->getMessage()
            ]);
        }
    }
    /**
     * Delete user
     *
     * This api deletes a user.
     *
     * @param id string required The id of the user. Example: 1
     *
     * @return JsonResponse
     */
    public function destroy($id)
    {
        try {
                $userData = Auth::user();
                $user = $this->userRepository->getById($id);
                if ($user != null) {
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
                        return response()->json([
                            'code' => 200,
                            'message' => 'User has been deleted successfully!'
                        ]);
                    } else {
                        return response()->json([
                            'code' => 400,
                            'message' => 'Unknown error occurred, Please try again!',
                            "error" => "Unknown error occurred, Please try again!"
                        ]);
                    }
                } else {
                    return false;
                }
        } catch (Exception $ex) {
            return response()->json([
                'code' => 500,
                'message' => 'Unable to process your request, Please try again!',
                "error" => $ex->getMessage()
            ]);
        }
    }

    // public function assignRole(Request $request, User $user) {
    //     $role = Roles::find($request->role_id);
    //     if ($role) {
    //         $user->assignRole($role->id);
    //         return response()->json([
    //             'message' => 'Role assigned successfully'
    //         ]);
    //     }
    //     return response()->json([
    //         'message' => 'Role not found'
    //     ], 400);
    // }

    // public function assignPermission(Request $request, Roles $role)
    // {
    //     $permission = Permission::find($request->permission_id);
    //     if ($permission) {
    //         $role->permissions()->attach($permission->id);
    //         return response()->json([
    //             'message' => 'Permission assigned successfully'
    //         ]);
    //     }
    //     return response()->json(['message' => 'Permission not found'], 404);
    // }

}
