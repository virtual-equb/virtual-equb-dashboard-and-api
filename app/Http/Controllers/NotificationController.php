<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Repositories\ActivityLog\IActivityLogRepository;
use App\Repositories\EqubType\IEqubTypeRepository;
use App\Repositories\Member\IMemberRepository;
use App\Repositories\Notification\INotificationRepository;
use App\Service\Notification;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class NotificationController extends Controller
{
    private $activityLogRepository;
    private $notificationRepository;
    private $memberRepository;
    private $equbTypeRepository;
    private $title;
    public function __construct(
        INotificationRepository $notificationRepository,
        IEqubTypeRepository $equbTypeRepository,
        IMemberRepository $memberRepository,
        IActivityLogRepository $activityLogRepository
    ) {
        $this->activityLogRepository = $activityLogRepository;
        $this->notificationRepository = $notificationRepository;
        $this->equbTypeRepository = $equbTypeRepository;
        $this->memberRepository = $memberRepository;
        $this->title = "Virtual Equb - Notification";

        // // Permission guard
        // $this->middleware('permission_check_logout:update notification', ['only' => ['update', 'edit', 'approve', 'updatePending']]);
        // $this->middleware('permission_check_logout:delete notification', ['only' => ['destroy']]);
        // $this->middleware('permission_check_logout:view notification', ['only' => ['index', 'show']]);
        // $this->middleware('permission_check_logout:create notification', ['only' => ['store', 'create', 'sendToIndividual']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $userData = Auth::user();
            // if ($userData && ($userData['role'] == "admin" || $userData['role'] == "general_manager" || $userData['role'] == "operation_manager" || $userData['role'] == "assistant")) {
                $data['title'] = $this->title;
                $data['notifications']  = $this->notificationRepository->getAll();
                $data['equbTypes']  = $this->equbTypeRepository->getActive();
                // dd($data);
                return view('admin/notification.notificationList', $data);
            // } else {
            //     return view('auth/login');
            // }
        } catch (Exception $ex) {
            // dd($ex);
            $msg = "Unknown Error Occurred, Please try again!";
            $type = 'error';
            Session::flash($type, $msg);
            return back();
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // dd($request);
        try {
            $userData = Auth::user();
            // if ($userData && ($userData['role'] == "admin" || $userData['role'] == "general_manager" || $userData['role'] == "operation_manager" || $userData['role'] == "assistant")) {
                $this->validate($request, [
                    'title' => 'required',
                    'body' => 'required',
                    'equb_type_id' => 'required',
                    'method' => 'required',
                ]);
                $title = $request->input('title');
                $body = $request->input('body');
                $equb_type_id = $request->input('equb_type_id');
                $method = $request->input('method');
                $notificationData = [
                    'title' => $title,
                    'body' => $body,
                    'equb_type_id' => $equb_type_id,
                    'method' => $method,
                ];
                // dd($notificationData);
                // if ($equb_type_id != "all") {
                //     $members = $this->memberRepository->getMembersByEqubType($equb_type_id);
                // } else {
                //     $members = $this->memberRepository->getActiveMemberNotification();
                // }
                // dd($members);
                // foreach ($members as $member) {
                //     $notifiedMember = User::where('phone_number', $member->phone)->first();
                //     if ($method == 'sms') {
                //         try {
                //             $this->sendSms($member->phone, $body);
                //         } catch (Exception $ex) {
                //             return redirect()->back()->with('error', 'Failed to send SMS');
                //         };
                //     } elseif ($method == 'notification') {
                //         Notification::sendNotification($notifiedMember->fcm_id, $title, $body);
                //     } elseif ($method == 'both') {
                //         // dd($method);
                //         try {
                //             $this->sendSms($member->phone, $body);
                //         } catch (Exception $ex) {
                //             return redirect()->back()->with('error', 'Failed to send SMS');
                //         };
                //         Notification::sendNotification($notifiedMember->fcm_id, $title, $body);
                //     }
                // }
                $create = $this->notificationRepository->create($notificationData);
                // dd($create);
                if ($create) {
                    $activityLog = [
                        'type' => 'notification',
                        'type_id' => $create->id,
                        'action' => 'created',
                        'user_id' => $userData->id,
                        'username' => $userData->name,
                        'role' => $userData->role,
                    ];
                    $this->activityLogRepository->createActivityLog($activityLog);
                    $msg = "Notification has been created successfully!";
                    $type = 'success';
                    Session::flash($type, $msg);
                    return redirect('/notification');
                } else {
                    $msg = "Unknown Error Occurred, Please try again!";
                    $type = 'error';
                    Session::flash($type, $msg);
                    redirect('/notification');
                }
            // } else {
            //     return view('auth/login');
            // }
        } catch (Exception $ex) {
            $msg = "Unknown Error Occurred, Please try again!";
            $type = 'error';
            Session::flash($type, $msg);
            return back();
        }
    }
    public function sendToIndividual(Request $request)
    {
        try {
            $userData = Auth::user();
            // if ($userData && ($userData['role'] == "admin" || $userData['role'] == "general_manager" || $userData['role'] == "operation_manager" || $userData['role'] == "assistant")) {
                $this->validate($request, [
                    'send_title' => 'required',
                    'send_body' => 'required',
                    'm_phone' => 'required',
                    'send_method' => 'required',
                ]);
                $title = $request->input('send_title');
                $body = $request->input('send_body');
                $phone = $request->input('m_phone');
                $method = $request->input('send_method');
                $notificationData = [
                    'title' => $title,
                    'body' => $body,
                    'phone' => $phone,
                    'method' => $method,
                ];
                $notifiedMember = User::where('phone_number', $phone)->first();
                if ($method == 'sms') {
                    try {
                        $this->sendSms($phone, $body);
                    } catch (Exception $ex) {
                        return redirect()->back()->with('error', 'Failed to send SMS');
                    };
                } elseif ($method == 'notification') {
                    Notification::sendNotification($notifiedMember->fcm_id, $title, $body);
                } elseif ($method == 'both') {
                    try {
                        $this->sendSms($phone, $body);
                    } catch (Exception $ex) {
                        return redirect()->back()->with('error', 'Failed to send SMS');
                    };
                    Notification::sendNotification($notifiedMember->fcm_id, $title, $body);
                }
                $create = $this->notificationRepository->create($notificationData);
                if ($create) {
                    $activityLog = [
                        'type' => 'notification',
                        'type_id' => $create->id,
                        'action' => 'created',
                        'user_id' => $userData->id,
                        'username' => $userData->name,
                        'role' => $userData->role,
                    ];
                    $this->activityLogRepository->createActivityLog($activityLog);
                    $msg = "Notification has been sent successfully!";
                    $type = 'success';
                    Session::flash($type, $msg);
                    return redirect('/member');
                } else {
                    $msg = "Unknown Error Occurred, Please try again!";
                    $type = 'error';
                    Session::flash($type, $msg);
                    redirect('/member');
                }
            // } else {
            //     return view('auth/login');
            // }
        } catch (Exception $ex) {
            $msg = "Unknown Error Occurred, Please try again!";
            $type = 'error';
            Session::flash($type, $msg);
            return back();
        }
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function approve($id)
    {
        // dd($request);
        try {
            $notification = $this->notificationRepository->getById($id);
            $userData = Auth::user();
            // if ($userData && ($userData['role'] == "admin" || $userData['role'] == "general_manager" || $userData['role'] == "operation_manager" || $userData['role'] == "assistant")) {
                $title = $notification->title;
                $body = $notification->body;
                $equb_type_id = $notification->equb_type_id;
                $method = $notification->method;
                // $notificationData = [
                //     'title' => $title,
                //     'body' => $body,
                //     'equb_type_id' => $equb_type_id,
                //     'method' => $method,
                // ];
                // dd($notificationData);
                if ($equb_type_id != "all") {
                    $members = $this->memberRepository->getMembersByEqubType($equb_type_id);
                } else {
                    $members = $this->memberRepository->getActiveMemberNotification();
                }
                // dd($members);
                foreach ($members as $member) {
                    $notifiedMember = User::where('phone_number', $member->phone)->first();
                    if ($method == 'sms') {
                        try {
                            $this->sendSms($member->phone, $body);
                        } catch (Exception $ex) {
                            return redirect()->back()->with('error', 'Failed to send SMS');
                        };
                    } elseif ($method == 'notification') {
                        Notification::sendNotification($notifiedMember->fcm_id, $title, $body);
                    } elseif ($method == 'both') {
                        // dd($method);
                        try {
                            $this->sendSms($member->phone, $body);
                        } catch (Exception $ex) {
                            return redirect()->back()->with('error', 'Failed to send SMS');
                        };
                        Notification::sendNotification($notifiedMember->fcm_id, $title, $body);
                    }
                }
                $update = [
                    'status' => 'approved'
                ];
                // dd($update);
                $create = $this->notificationRepository->update($id, $update);
                // dd($create);
                if ($create) {
                    $activityLog = [
                        'type' => 'notification',
                        'type_id' => $id,
                        'action' => 'approved',
                        'user_id' => $userData->id,
                        'username' => $userData->name,
                        'role' => $userData->role,
                    ];
                    $this->activityLogRepository->createActivityLog($activityLog);
                    $msg = "Notification has been sent successfully!";
                    $type = 'success';
                    Session::flash($type, $msg);
                    return redirect('/notification');
                } else {
                    $msg = "Unknown Error Occurred, Please try again!";
                    $type = 'error';
                    Session::flash($type, $msg);
                    redirect('/notification');
                }
            // } else {
            //     return view('auth/login');
            // }
        } catch (Exception $ex) {
            $msg = "Unknown Error Occurred, Please try again!";
            $type = 'error';
            Session::flash($type, $msg);
            return back();
        }
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
        try {
            $userData = Auth::user();
            // if ($userData && ($userData['role'] == "admin" || $userData['role'] == "general_manager" || $userData['role'] == "operation_manager" || $userData['role'] == "assistant")) {
                $this->validate($request, [
                    'update_title' => 'required',
                    'update_body' => 'required',
                    'update_equb_type' => 'required',
                    'update_method' => 'required',
                ]);
                $update_title = $request->input('update_title');
                $update_body = $request->input('update_body');
                $update_equb_type = $request->input('update_equb_type');
                $update_method = $request->input('update_method');
                $updated = [
                    'title' => $update_title,
                    'body' => $update_body,
                    'equb_type_id' => $update_equb_type,
                    'method' => $update_method,
                ];
                $members = $this->memberRepository->getMembersByEqubType($update_equb_type);
                foreach ($members as $member) {
                    $notifiedMember = User::where('phone_number', $member->phone)->first();
                    if ($update_method == 'sms') {
                        try {
                            $this->sendSms($member->phone, $update_body);
                        } catch (Exception $ex) {
                            return redirect()->back()->with('error', 'Failed to send SMS');
                        };
                    } elseif ($update_method == 'notification') {
                        Notification::sendNotification($notifiedMember->fcm_id, $update_title, $update_body);
                    } elseif ($update_method == 'both') {
                        try {
                            $this->sendSms($member->phone, $update_body);
                        } catch (Exception $ex) {
                            return redirect()->back()->with('error', 'Failed to send SMS');
                        };
                        Notification::sendNotification($notifiedMember->fcm_id, $update_title, $update_body);
                    }
                }
                $updated = $this->notificationRepository->create($updated);
                if ($updated) {
                    $activityLog = [
                        'type' => 'notification',
                        'type_id' => $id,
                        'action' => 'create',
                        'user_id' => $userData->id,
                        'username' => $userData->name,
                        'role' => $userData->role,
                    ];
                    $this->activityLogRepository->createActivityLog($activityLog);
                    $msg = "Notification has been resent successfully!";
                    $type = 'success';
                    Session::flash($type, $msg);
                    return back();
                } else {
                    $msg = "Unknown error occurred, Please try again!";
                    $type = 'error';
                    Session::flash($type, $msg);
                    return back();
                }
            // } else {
            //     return view('auth/login');
            // }
        } catch (Exception $ex) {
            $msg = "Unable to process your request, Please try again!";
            $type = 'error';
            Session::flash($type, $msg);
            return back();
        }
    }
    public function updatePending(Request $request, $id)
    {
        try {
            $userData = Auth::user();
            // if ($userData && ($userData['role'] == "admin" || $userData['role'] == "general_manager" || $userData['role'] == "operation_manager" || $userData['role'] == "assistant")) {
                $this->validate($request, [
                    'update_title_pending' => 'required',
                    'update_body_pending' => 'required',
                    'update_equb_type_pending' => 'required',
                    'update_method_pending' => 'required',
                ]);
                $update_title = $request->input('update_title_pending');
                $update_body = $request->input('update_body_pending');
                $update_equb_type = $request->input('update_equb_type_pending');
                $update_method = $request->input('update_method_pending');
                $updated = [
                    'title' => $update_title,
                    'body' => $update_body,
                    'equb_type_id' => $update_equb_type,
                    'method' => $update_method,
                ];
                $updated = $this->notificationRepository->update($id, $updated);
                if ($updated) {
                    $activityLog = [
                        'type' => 'notification',
                        'type_id' => $id,
                        'action' => 'update',
                        'user_id' => $userData->id,
                        'username' => $userData->name,
                        'role' => $userData->role,
                    ];
                    $this->activityLogRepository->createActivityLog($activityLog);
                    $msg = "Notification has been updated successfully!";
                    $type = 'success';
                    Session::flash($type, $msg);
                    return back();
                } else {
                    $msg = "Unknown error occurred, Please try again!";
                    $type = 'error';
                    Session::flash($type, $msg);
                    return back();
                }
            // } else {
            //     return view('auth/login');
            // }
        } catch (Exception $ex) {
            $msg = "Unable to process your request, Please try again!";
            $type = 'error';
            Session::flash($type, $msg);
            return back();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $userData = Auth::user();
            // if ($userData && ($userData['role'] == "admin" || $userData['role'] == "general_manager" || $userData['role'] == "operation_manager" || $userData['role'] == "assistant")) {
                $notification = $this->notificationRepository->getById($id);
                if ($notification != null) {
                    $deleted = $this->notificationRepository->delete($id);
                    if ($deleted) {
                        $activityLog = [
                            'type' => 'notification',
                            'type_id' => $id,
                            'action' => 'deleted',
                            'user_id' => $userData->id,
                            'username' => $userData->name,
                            'role' => $userData->role,
                        ];
                        $this->activityLogRepository->createActivityLog($activityLog);
                        $msg = "Notification has been deleted successfully!";
                        $type = 'success';
                        Session::flash($type, $msg);
                        return redirect('notification/');
                    } else {
                        $msg = "Unknown Error Occurred, Please try again!";
                        $type = 'error';
                        Session::flash($type, $msg);
                        redirect('/notification');
                    }
                } else {
                    return false;
                }
            // } else {
            //     return view('auth/login');
            // }
        } catch (Exception $ex) {
            $msg = "Unable to process your request, Please try again!";
            $type = 'error';
            Session::flash($type, $msg);
            return $msg;
        }
    }
}
