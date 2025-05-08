<?php

namespace App\Http\Controllers\Api;

use Exception;
use Carbon\Carbon;
use App\Models\Equb;
use App\Models\User;
use App\Models\Member;
use App\Models\Payment;
use App\Models\EqubType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Repositories\Equb\IEqubRepository;
use App\Repositories\Member\IMemberRepository;
use App\Repositories\Payment\IPaymentRepository;
use App\Repositories\EqubTaker\IEqubTakerRepository;
use App\Repositories\ActivityLog\IActivityLogRepository;
use App\Events\TelebirrPaymentStatusUpdated;
use App\Repositories\User\IUserRepository;
use App\Services\TelebirrMiniAppCreateOrderService;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

/**
 * @group Payments
 */
define('TELEBIRR_APP_ID', config('key.TELEBIRR_APP_ID'));
define('TELEBIRR_RECEIVER_NAME', config("key.TELEBIRR_RECEIVER_NAME"));
define('TELEBIRR_SHORT_CODE', config('key.TELEBIRR_SHORT_CODE'));
define('TELEBIRR_SUBJECT', config('key.TELEBIRR_SUBJECT'));
define('TELEBIRR_MINIAPP_RETURN_URL', config('key.TELEBIRR_MINIAPP_RETURN_URL'));
define('TELEBIRR_MINIAPP_NOTIFY_URL', config('key.TELEBIRR_MINIAPP_NOTIFY_URL'));
define('TELEBIRR_TIMEOUT_EXPRESS', config('key.TELEBIRR_TIMEOUT_EXPRESS'));
define('TELEBIRR_APP_KEY', config('key.TELEBIRR_APP_KEY'));
define('TELEBIRR_PUBLIC_KEY', config('key.TELEBIRR_PUBLIC_KEY'));
define('TELEBIRR_PUBLIC_KEY_C', config('key.TELEBIRR_PUBLIC_KEY_C'));
define('TELEBIRR_INAPP_PAYMENT_URL', config('key.TELEBIRR_INAPP_PAYMENT_URL'));
define('TELEBIRR_H5_URL', config('key.TELEBIRR_H5_URL'));
define('TELEBIRR_BASE_URL', config('key.TELEBIRR_BASE_URL'));
define('TELEBIRR_FABRIC_APP_ID', config('key.TELEBIRR_FABRIC_APP_ID'));
define('TELEBIRR_APP_SECRET', config('key.TELEBIRR_APP_SECRET'));
define('TELEBIRR_MERCHANT_APP_ID', config('key.TELEBIRR_MERCHANT_APP_ID'));
define('TELEBIRR_MERCHANT_CODE', config('key.TELEBIRR_MERCHANT_CODE'));
define('TELEBIRR_TITLE', config('key.TELEBIRR_TITLE'));
define('PRIVATE_KEY', config('key.PRIVATE_KEY'));

class TelebirrMiniAppController extends Controller
{
    private $activityLogRepository;
    private $paymentRepository;
    private $memberRepository;
    private $equbRepository;
    private $equbTakerRepository;
    private $userRepository;
    private $title;
    public function __construct(
        IPaymentRepository $paymentRepository,
        IMemberRepository $memberRepository,
        IEqubRepository $equbRepository,
        IEqubTakerRepository $equbTakerRepository,
        IActivityLogRepository $activityLogRepository,
        IUserRepository $userRepository,
    ) {
        $this->middleware('auth:api', ['except' => ['callbackMiniApp', 'registerMember', 'callback']]);
        $this->activityLogRepository = $activityLogRepository;
        $this->paymentRepository = $paymentRepository;
        $this->memberRepository = $memberRepository;
        $this->equbRepository = $equbRepository;
        $this->equbTakerRepository = $equbTakerRepository;
        $this->userRepository = $userRepository;
        $this->title = "Virtual Equb - Telebirr MiniApp Payment";
    }

    public function initialize (Request $request)
    {
        Log::info('To Payment - Request Data', $request->all());

        try {
            $request->validate([
                'amount' => 'required|numeric',
                'member_id' => 'required|exists:members,id',
                'equb_id' => 'required|exists:equbs,id',
            ]);

            $req = $request->all();
            $user = Auth::user();

            $equbId = $request->input('equb_id');
            $amount = $request->input('amount');
            $equb_id = $equbId;
            $paymentType = "Telebirr MiniApp";
            $memberData = Member::where('phone', $user->phone_number)->first();
            $collector = User::where('name', 'telebirr')->first();

            $equb_status = $this->equbRepository->getStatusById($equbId);
            if ($equb_status->status != 'Active') {
                return response()->json([
                    'code' => 500,
                    'message' => 'Payment processing failed: The Equb is currently not in active status.',
                ], 500);
            }

            // Check if a payment was made by this member for this equb in the last 24 hours
            $existingPayment = Payment::where('member_id', $memberData->id)
                ->where('equb_id', $equbId)
                ->where('status', 'paid')
                ->where('created_at', '>=', now()->subDay())
                ->exists();

            if ($existingPayment) {
                return response()->json([
                    'code' => 400,
                    'message' => 'You can only make one payment transaction per 24 hours for this Equb.'
                ], 400);
            }

            // Check if entered amount is more that the total amount to be paid and restrict user from paying more than the required amount
            $totalEqubAmountToPay = $this->equbRepository->getTotalEqubAmount($equb_id);
            $totalPaidAmount = $this->paymentRepository->getTotalPaid($equb_id);
            $remainingAmountToPay = $totalEqubAmountToPay - $totalPaidAmount;

            if ($amount > $remainingAmountToPay) {
                return response()->json([
                    'code' => 500,
                    'message' => 'Payment processing failed: You cannot pay more than the required total amount for this Equb.',
                ], 500);
            }
        
            $paymentData = [
                'member_id' => $memberData->id,
                'equb_id' => $equb_id,
                'payment_type' => $paymentType,
                'amount' => $amount,
                'collecter' => $collector->id,
                'status' => 'pending'
            ];

            $telebirr = $this->paymentRepository->create($paymentData);

            // Telebirr initialization 
            if ($telebirr) {
                $baseUrl = TELEBIRR_BASE_URL;
                $fabricAppId = TELEBIRR_FABRIC_APP_ID;
                $appSecret = TELEBIRR_APP_SECRET;
                $merchantAppId = TELEBIRR_MERCHANT_APP_ID;
                $merchantCode = TELEBIRR_MERCHANT_CODE;

                // You can also get the request parameters directly from the request object
                $req = $request->all();

                // Create an instance of TelebirrMiniAppCreateOrderService
                $telebirrMiniAppCreateOrderService = new TelebirrMiniAppCreateOrderService(
                    $baseUrl,
                    (object) $req, // This casts the array to an object
                    $fabricAppId,
                    $appSecret,
                    $merchantAppId,
                    $merchantCode,
                    $telebirr->id // Cast to string if necessary
                );

                $result = $telebirrMiniAppCreateOrderService->createOrder();
              
                return $result;
            } else {
                return response()->json([
                    'code' => 400,
                    'message' => 'Unknown error occurred, Please try again!'
                ], 400);
            }
        } catch (Exception $error) {

            // Log::error('Error creating TelebirrMiniAppCreateOrderService: ' . $error->getMessage());
            return response()->json([
                'code' => 500,
                'message' => 'Failed to create order service',
                'error' => $error->getMessage(),
            ], 500);
        }
    }
    
    public function callback(Request $request)
    {
        try {
            Log::info('callback request data - Telebirr MiniApp', $request->all());

            if ($request) {
                $merch_order_id = $request['merch_order_id'];
                $payment = Payment::find($merch_order_id);
               
                if ($request['trade_status'] == 'Completed') {
                    
                    $member = $payment->member_id;
                    $equb_id = $payment->equb_id;
                    $amount = $payment->amount;
                    $paymentType = $payment->payment_type;

                    $equbAmount = $this->equbRepository->getEqubAmount($member, $equb_id);
                    $totalCredit = $this->paymentRepository->getTotalCreditAPI($equb_id) ?? 0;
                    $availableBalance = $this->paymentRepository->getTotalBalanceAPI($equb_id) ?? 0;
                    $credit = $equbAmount-$amount;

                    if ($credit <= 0) {
                        $credit = 0;
                    }
                    
                    if ($totalCredit == null) {
                        $totalCredit = 0;
                    }
                    $creditData = [
                        'creadit' => 0
                    ];
                    $this->paymentRepository->updateCredit($equb_id, $creditData);
                    
                    $lastTc = $totalCredit;
                    $totalCredit = $credit + $totalCredit;
                    $tc = $totalCredit;
                
                    
                    if ($availableBalance == null) {
                        $availableBalance = 0;
                    }
                    $balanceData = [
                        'balance' => 0
                    ];
                    $this->paymentRepository->updateBalance($equb_id, $balanceData);
                    
                    $at = $amount;
                    $amount = $availableBalance + $amount;
                    if ($amount > $equbAmount) {
                        if ($totalCredit > 0) {
                            if ($totalCredit < $amount) {
                                if ($at < $equbAmount) {
                                    $availableBalance = $availableBalance - $totalCredit;
                                    $totalCredit = 0;
                                } elseif ($at > $equbAmount) {
                                    $diff = $at - $equbAmount;
                                    //RECENT CODE FIX - REGARDING CREDIT AND BALANCE CALCULATION
                                    $totalCredit = max (0, $lastTc - $diff);
                                    $availableBalance = max(0, $availableBalance + $diff - $lastTc);
                                } elseif ($at = $equbAmount) {
                                    $availableBalance = $availableBalance;
                                }
                                $amount = $at;
                            } else {
                                $amount = $at;
                                $totalCredit = $totalCredit;
                            }
                        } else {
                            $totalCredit = $totalCredit;
                            if ($at < $equbAmount) {
                                $availableBalance = $availableBalance - $totalCredit;
                            } elseif ($at > $equbAmount) {
                                $diff = $at - $equbAmount;
                                $totalCredit = $totalCredit - $diff;
                                $availableBalance = $availableBalance + $diff;
                                $totalCredit = 0;
                            } elseif ($at = $equbAmount) {
                                $availableBalance = $availableBalance;
                            }
                            $amount = $at;
                        }
                    } elseif ($amount == $equbAmount) {
                        $amount = $at;
                        $totalCredit = $lastTc;
                        $availableBalance = 0;
                    } elseif ($amount < $equbAmount) {
                        if ($lastTc == 0) {
                            $totalCredit = $equbAmount - $amount;
                            $availableBalance = 0;
                            $amount = $at;
                        } else {
                            $totalCredit = $totalCredit;
                            $availableBalance = 0;
                            $amount = $at;
                        }
                    }

                    $memberData = Member::where('id', $member)->first();
                    $collector = User::where('name', 'telebirr')->first();
                    $tradeDt = $request['notify_time'];

                    // Convert milliseconds to seconds (PHP expects seconds)
                    $seconds = $tradeDt / 1000;

                    // Create a Carbon instance from the timestamp
                    $date = Carbon::createFromTimestamp($seconds);

                    // Format the date as desired
                    $readableDate = $date->format('Y-m-d H:i:s');
                    $telebirrObj = [
                        'amount' => $request['total_amount'],
                        'tradeDate' => $readableDate,
                        'tradeNo' => $request['payment_order_id'],
                        'tradeStatus' => $request['trade_status'],
                        'transaction_number' => $request['payment_order_id'],
                        'status' => 'paid'
                    ];
                    $collector = User::where('name', 'telebirr')->first();
                    $payment->amount = $telebirrObj['amount'];
                    $payment->tradeDate = $telebirrObj['tradeDate'];
                    $payment->tradeNo = $telebirrObj['tradeNo'];
                    $payment->tradeStatus = $telebirrObj['tradeStatus'];
                    $payment->transaction_number = $telebirrObj['transaction_number'];
                    $payment->status = $telebirrObj['status'];
                    $payment->collecter = $collector->id;
                    $payment->creadit = $totalCredit;
                    $payment->balance = $availableBalance;
                    $payment->payment_type = $paymentType;
                    $payment->save();

                    $equb_id = $payment->equb_id;

                    $totalPpayment = $this->paymentRepository->getTotalPaid($equb_id);
                    $totalEqubAmount = $this->equbRepository->getTotalEqubAmount($equb_id);
                    $remainingPayment = max(0, $totalEqubAmount - $totalPpayment);

                    $updated = [
                        'total_payment' => $totalPpayment,
                        'remaining_payment' => $remainingPayment,
                        'remaining_amount' => $remainingPayment,
                        'status' => $remainingPayment == 0 ? 'paid' : 'partially_paid',
                    ];

                    $updated = $this->equbTakerRepository->updatePayment($equb_id, $updated);
                    $equbTaker = $this->equbTakerRepository->getByEqubId($equb_id);

                    if ($remainingPayment == 0 && $equbTaker) {
                        $ekubStatus = [
                            'status' => 'Deactive'
                        ];
                        $ekubStatusUpdate = $this->equbRepository->update($equb_id, $ekubStatus);
                    }

                    try {
                        event(new TelebirrPaymentStatusUpdated($payment));
                    } catch (\Exception $e) {
                        Log::error('Error broadcasting TelebirrPaymentStatusUpdated event: ' . $e->getMessage());
                    }

                    return response()->json([
                        'code' => 200,
                        'message' => 'You have succesfully paid!'
                    ], 200);
                } else {
                    return response()->json([
                        'code' => 400,
                        'message' => 'Payment failed, Please try again!'
                    ], 400);
                }
            }            
        } catch (Exception $error) {
            return response()->json([
                'code' => 500,
                'message' => 'Unable to process your request, Please try again!',
                "error" => $error
            ], 500);
        }
    }

    public function callbackMiniApp(Request $request)
    {
        try {
            Log::info('Callback From Telebirr MiniApp request data', $request->all());

            return response()->json([
                'code' => 200,
                'message' => 'Callback Response Successfully Cached - Telebirr MiniApp!'
            ], 200);          
        } catch (Exception $error) {
            return response()->json([
                'code' => 500,
                'message' => 'Unable to catch response request from telebirr miniApp, Please try again!',
                "error" => $error
            ]);
        }
    }

    public  function decrypt_RSA($publicPEMKey, $data)
    {
        $pkey_public = openssl_pkey_get_public(TELEBIRR_PUBLIC_KEY);
        $DECRYPT_BLOCK_SIZE = 256;
        $decrypted = '';
        $data = str_split(base64_decode($data), $DECRYPT_BLOCK_SIZE);
        foreach ($data as $chunk) {
            $partial = '';
            $decryptionOK = openssl_public_decrypt($chunk, $partial, $pkey_public, OPENSSL_PKCS1_PADDING);
            if ($decryptionOK === false) {
                return false;
            }
            $decrypted .= $partial;
        }
        return $decrypted;
    }
    
    public function getTransaction($id)
    {
        try {
            $payment = $this->paymentRepository->getById($id);

            return response()->json([
                'code' => 200,
                'transaction' => $payment
            ]);
        } catch (Exception $error) {
            return response()->json([
                'code' => 500,
                'message' => 'Unable to process your request, Please try again!',
                "error" => $error
            ]);
        }
    }

    public function registerMember(Request $request)
    {
        Log::info('Member Registration Data from Telebirr MiniApp', $request->all());

        try {
            $shortcode = config('key.SHORT_CODE');

            $this->validate(
                $request,
                [
                    'full_name' => 'required',
                    'phone' => 'required',
                    'email' => 'nullable|email',
                    'gender' => 'required',
                    'date_of_birth' => 'required|date|before:' . now()->subYears(18)->format('Y-m-d'), // Must be before 18 years ago
                ],
                [
                    'date_of_birth.before' => 'You must be at least 18 years old to register.'
                ]
            );

            // Handle the input data
            $phone = $request->input('phone');
            $fullName = $request->input('full_name');
            $email = $request->input('email');
            $gender = $request->input('gender');
            $dateofBirth = $request->input('date_of_birth');

            $city = $request->input('city');
            $subcity = $request->input('subcity');
            $woreda = $request->input('woreda');
            $housenumber = $request->input('housenumber');
            $location = $request->input('location');

            // Check if the phone number already exists
            if (!empty($phone)) {
                $member_count = Member::where('phone', $phone)->count();
                $user_count = User::where('phone_number', $phone)->count();
                if ($member_count > 0 || $user_count > 0) {
                    return response()->json([
                        'code' => 200,
                        'message' => 'Phone already exists',
                    ]);
                }
            }

            // Check if the email already exists
            if (!empty($email)) {
                $member_count = Member::where('email', $email)->count();
                $user_count = User::where('email', $email)->count();
                if ($member_count > 0 || $user_count > 0) {
                    return response()->json([
                        'code' => 200,
                        'message' => 'Email already exists',
                    ], 200);
                }
            }

            // Prepare the member data
            $memberData = [
                'full_name' => $fullName,
                'phone' => $phone,
                'gender' => $gender,
                'email' => $email,
                'city' => $city,
                'subcity' => $subcity,
                'woreda' => $woreda,
                'house_number' => $housenumber,
                'specific_location' => $location,
                'status' => "Active",
                'date_of_birth' => $dateofBirth
            ];

            // Handle the profile picture upload
            if ($request->file('profile_picture')) {
                $image = $request->file('profile_picture');
                $imageName = time() . '.' . $image->getClientOriginalExtension();
                $image->storeAs('public/profile_pictures', $imageName);
                $memberData['profile_photo_path'] = 'profile_pictures/' . $imageName;
            }

            // Create member and user
            $create = $this->memberRepository->create($memberData);

            $password = rand(100000, 999999);
            $hashedPassword = Hash::make($password);
            $user = [
                'name' => $fullName,
                'email' => $email,
                'password' => $hashedPassword,
                'phone_number' => $phone,
                'gender' => $gender,
                'status' => 'Active'
            ];
            $user = $this->userRepository->createUser($user);

            $memberRoleAPI = Role::firstOrCreate(['name' => 'member', 'guard_name' => 'api']);
            $memberRoleWEB = Role::firstOrCreate(['name' => 'member', 'guard_name' => 'web']);
            $user->assignRole($memberRoleWEB);
            $user->assignRole($memberRoleAPI);

            if ($create && $user) {
                try {
                    $message = "Welcome to Virtual Equb! You have successfully registered. Please use your phone number ({$phone}) and password ({$password}) when logging in through our mobile application. For support, please call {$shortcode}.";

                    $this->sendSms($phone, $message);

                    $authController = new AuthController();
        
                    $loginRequest = new \Illuminate\Http\Request([
                        'phone_number' => $phone,
                        'password' => $password,
                    ]);
            
                    // Call the login() method
                    return $authController->login($loginRequest);
                } catch (Exception $ex) {
                    return response()->json([
                        'code' => 200,
                        'message' => 'Failed to send SMS',
                        "error" => "Failed to send SMS"
                    ], 200);
                };
            } else {
                return response()->json([
                    'code' => 400,
                    'message' => 'Registration failed. Please try again!',
                    'error' => 'Registration process encountered an unknown error.'
                ], 400);
            }
        } catch (Exception $ex) {
            return response()->json([
                'code' => 500,
                'message' => 'Unknown error occurred, Please try again!',
                "error" => $ex->getMessage()
            ], 500);
        }
    }
}