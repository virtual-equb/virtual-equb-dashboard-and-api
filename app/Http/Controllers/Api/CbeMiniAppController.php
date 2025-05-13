<?php

namespace App\Http\Controllers\Api;

use Exception;
use App\Models\Equb;
use App\Models\User;
use App\Models\Member;
use App\Models\Payment;
use App\Models\AppToken;
use App\Models\EqubType;
use App\Models\MainEqub;
use App\Models\CallbackData;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Spatie\Permission\Models\Role;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use App\Http\Resources\Api\MemberResource;
use App\Repositories\Equb\IEqubRepository;
use App\Repositories\User\IUserRepository;
use App\Http\Resources\Api\MainEqubResource;
use App\Repositories\Member\IMemberRepository;
use App\Repositories\Payment\IPaymentRepository;
use App\Repositories\EqubTaker\IEqubTakerRepository;
use App\Repositories\ActivityLog\IActivityLogRepository;
use Illuminate\Console\View\Components\Alert;

class CbeMiniAppController extends Controller
{
    private $activityLogRepository;
    private $paymentRepository;
    private $equbRepository;
    private $equbTakerRepository;
    private $memberRepository;
    private $userRepository;
    public function __construct(
        IPaymentRepository $paymentRepository,
        IMemberRepository $memberRepository,
        IEqubRepository $equbRepository,
        IEqubTakerRepository $equbTakerRepository,
        IActivityLogRepository $activityLogRepository,
        IUserRepository $userRepository
    )
    {
        $this->middleware('auth:api', ['except' => ['miniAppLogin', 'registerMember', 'callback']]);
        $this->activityLogRepository = $activityLogRepository;
        $this->paymentRepository = $paymentRepository;
        $this->memberRepository = $memberRepository;
        $this->equbRepository = $equbRepository;
        $this->equbTakerRepository = $equbTakerRepository;
        $this->userRepository = $userRepository;

    }

    public function miniAppLogin(Request $request)
    {
        try {
            $authHeader = $request->header('Authorization');

            if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
                Log::info('CbeMiniApp - Token Not Found');
                return 'The token is missing or improperly formatted. Please verify and try again.';
            }

            $token = substr($authHeader, 7);

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Bearer' . $token
            ])->get('https://cbebirrpaymentgateway.cbe.com.et:8888/auth/user');
            
            if ($response->failed()) {
                Log::info('CbeMiniApp - Request failed: ' . $response->status());
                return 'Request failed. Please check your network or the API endpoint.';
            }

            if (!$response->json('phone')) {
                return 'Phone number is missing or invalid';
            }

            $phoneNumber = '+' . $response->json('phone');

            return redirect()->away("https://cbebirr.virtualequb.com?token={$phoneNumber}");
        } catch (Exception $ex) {
            return 'There was an issue extracting the token. Please ensure the token is being passed and handled correctly.';
        }
    }

    public function registerMember(Request $request)
    {
        Log::info('Member Registration Data from CbeBirr MiniApp', $request->all());

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

    public function initialize(Request $request)
    {
        try {
            // Step 2: Process the payment
            // Step 2.1: Preparing data to be sent
            $validated = $request->validate([
                'amount' => 'required|string',
                'equb_id' => 'required|exists:equbs,id',
                // 'token' => 'required|exists:app_tokens,token',
                'phone' => 'required|exists:app_tokens,phone',
            ]);
            // Log::info('datas', $validated);
    
            $transactionId = uniqid(); // Generate unique transaction ID
            $transactionTime = now()->toIso8601String(); // Get current timestamp in ISO8601 format
            $callbackUrl = route('cbe.callback'); // Callback URL for response handling
            $companyName = config('key.CBE_MINI_COMPANY_NAME'); // Provided company name
            $hashingKey = config('key.CBE_MINI_HASHING_KEY'); // Provided hashing key
            $tillCode = config('key.CBE_MINI_TILL_CODE'); // Provided till code

            // Payment data
            $equb = Equb::with('equbType')->findOrFail($validated['equb_id']);
            $member = $equb->member->where('phone', $validated['phone'])->first();
            $token = AppToken::where('phone', $validated['phone'])->orderBy('created_at', 'desc')->pluck('token')->first();
            Log::info('token'. $token);
            // dd($callbackUrl);
            // Prepare payload for hashing (including 'key')
            $payloadForHashing = [
                "amount" => $validated['amount'],
                "callBackURL" => $callbackUrl,
                "companyName" => $companyName,
                "key" => $hashingKey,
                "tillCode" => $tillCode,
                "token" => $token,
                "transactionId" => $transactionId,
                "transactionTime" => $transactionTime,
            ];

            // Step 2.3: Sorting payload and preparing hashing payload
            ksort($payloadForHashing); // Sort payload by keys

            $processedPayload = urldecode(http_build_query($payloadForHashing)); // Convert sorted payload to query string
    
            // Step 2.3.3: Hash the processed payload
            // $signature = hash_hmac('sha256', $processedPayload, $hashingKey);
            $signature = hash('sha256', $processedPayload);
            // dd($signature);
            // Prepare final payload (excluding 'key')
            $payload = [
                "amount" => $validated['amount'],
                "callBackURL" => $callbackUrl,
                "companyName" => $companyName,
                "signature" => $signature, // Add the signature
                "tillCode" => $tillCode,
                "token" => $token,
                "transactionId" => $transactionId,
                "transactionTime" => $transactionTime,
            ];
    
            // Ensure payload is sorted according to the desired order
            $orderedKeys = [
                "amount",
                "callBackURL",
                "companyName",
                "signature", // Place "signature" before "key"
                "tillCode",
                "token",
                "transactionId",
                "transactionTime",
            ];
            
            $sortedPayload = array_merge(array_flip($orderedKeys), $payload);
            Log::info('Payload sent to API:', $sortedPayload);
            Log::info('Headers:', [
                'Authorization' => "Bearer " . $token,
                'Content-Type' => 'application/json',
            ]);
            // ksort($sortedPayload);
            // $finalPayload = http_build_query($sortedPayload);
            // Step 2.5: Sending the final payload
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => "Bearer " . $token,
            ])->post('https://cbebirrpaymentgateway.cbe.com.et:8888/auth/pay', $sortedPayload);
            Log::info('response ' . $response->json('token'));
            Log::info('CBE API Response:', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            // Check the response status
            if ($response->status() === 200) {

                Payment::create([
                    'member_id' => $member->id,
                    'equb_id' => $equb->id,
                    'transaction_number' => $transactionId,
                    'amount' => $validated['amount'],
                    'status' => 'pending',
                    'payment_type' => 'CBE Mini App',
                    'collecter' => $member->id,
                    'signature' => $signature,
                ]);
                Log::info('Response body:', [$response->body()]);
                return response()->json(['status' => 'success', 'token' => $response->json('token'), 'signature' => $signature], 200);
            } else {
                Log::error('CBE API Error:', ['response' => $response->json()]);
                return response()->json(['status' => 'error', 'message' => 'Transaction failed'], $response->status());
            }
        } catch (\Exception $ex) {
            return response()->json(['status' => 'error', 'message' => $ex->getMessage()], 500);
        }
    }

    public function callback(Request $request)
    {
        try {
            // return 123;
            $token = $request->header('Authorization');
            // $token = "Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJwaG9uZSI6IjI1MTkxODA5NDQ1NSIsImV4cCI6MTczODYzMjEzMH0.cN95szHJNoJwp8tdtpDOk29vPmQeVoYP8dbKFBFy4_M";
            if (!$token) {
                return response()->json([
                    'error' => 'Token is missing'
                ], 400);
            }
            
            // Validate the token
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => $token,
            ])->get('https://cbebirrpaymentgateway.cbe.com.et:8888/auth/user');

            if ($response->status() !== 200) {
                return response()->json(['error' => 'Invalid Token'], 401);
            }

            

            // Verify the signature
            $data = $request->all();
            $callbackData = CallbackData::create([
                'paidAmount' => $data['paidAmount'],
                'paidByNumber' => $data['paidByNumber'],
                'transactionId' => $data['transactionId'],
                'transactionTime' => $data['transactionTime'],
                'tillCode' => $data['tillCode'],
                'token' => $data['token'],
                'signature' => $data['signature']
            ]);
            // $receivedSignature = $data['signature'] ?? null;
            // unset($data['signature']);

            // $hashingKey = 'XLcFp4RASjDwvTsH0DxeM06s5sOrHe0eSJwb7pB';
            // $data['key'] = $hashingKey;

            // ksort($data);

            // $processedPayload = http_build_query($data);
            // // $calculatedSignature = hash_hmac('sha256', $processedPayload, $hashingKey);
            // $calculatedSignature = hash('sha256', $processedPayload);

            // if ($calculatedSignature !== $receivedSignature) {
            //     return response()->json(['error' => 'Invalid Signature'], 400);
            // }
            // if (!$signature) {
            //     return response()->json(['error' => 'Invalid Signature'], 400);
            // }
            $payment = Payment::where('transaction_number', $data['transactionId'])->first();
            if (!$payment) {
                return response()->json(['message' => 'Payment record not found'], 404);
            }
            // payment calculations
            $equbId = $payment->equb_id;
            $memberId = $payment->member_id;
            $amount = $payment->amount;
            $credit = $payment->creadit;

            // Compute total credit and balance
            $totalCredit = $this->paymentRepository->getTotalCredit($equbId) ?? 0;
            $equbAmount = $this->equbRepository->getEqubAmount($memberId, $equbId);
            $availableBalance = $this->paymentRepository->getTotalBalance($equbId) ?? 0;

            $creditData = ['creadit' => 0];
            $this->paymentRepository->updateCredit($equbId, $creditData);

            $lastTc = $totalCredit;
            $totalCredit += $credit;

            $balanceData = ['balance' => 0];
            $this->paymentRepository->updateBalance($equbId, $balanceData);

            $at = $amount;
            $amount += $availableBalance;

            if ($amount > $equbAmount) {
                if ($totalCredit > 0) {
                    if ($totalCredit < $amount) {
                        if ($at < $equbAmount) {
                            $availableBalance -= $totalCredit;
                            $totalCredit = 0;
                        } elseif ($at > $equbAmount) {
                            $diff = $at - $equbAmount;
                            $totalCredit -= $diff;
                            $availableBalance = ($availableBalance + $diff) - $totalCredit;
                            $totalCredit = 0;
                        }
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
                } else {
                    $totalCredit = $totalCredit;
                    $availableBalance = 0;
                }
                $amount = $at;
            }
            // Update the payment record with the CBE details
            $payment->update([
                'transaction_number' => $data['transactionId'],
                'status' => 'paid',
                'paid_date' => now(),
                'amount' => $amount,
                'creadit' => $totalCredit,
                'balance' => $availableBalance,
                'payment_type' => 'CBE Mini App',
                'collecter' => $memberId,
                // 'signature' => $data['signature'],
            ]);
            // Update equb total payment and remaining payment
            $totalPaid = $this->paymentRepository->getTotalPaid($equbId);
            $totalEqubAmount = $this->equbRepository->getTotalEqubAmount($equbId);
            $remainingPayment = $totalEqubAmount - $totalPaid;

            $updated = [
                'total_payment' => $totalPaid,
                'remaining_payment' => $remainingPayment,
            ];
            $this->equbTakerRepository->updatePayment($equbId, $updated);

            // Mark equb as deactivated if fully paid
            if ($remainingPayment == 0) {
                $this->equbRepository->update($equbId, ['status' => 'Deactive']);
            }

            // Log the activity
            // $activityLog = [
            //     'type' => 'payments',
            //     'type_id' => $payment->id,
            //     'action' => 'updated',
            //     'user_id' => Auth::id(),
            //     'username' => Auth::user()->name,
            //     'role' => Auth::user()->role,
            // ];
            // $this->activityLogRepository->createActivityLog($activityLog);
            Log::info('Transaction verified successfully.');
           

            // Process the transaction
            return response()->json(['status' => 'success'], 200);

        } catch (Exception $ex) {
            return response()->json([
                'error' => $ex->getMessage()
            ], 500);
        }
    }

    public function isDateInYMDFormat($dateString)
    {
        try {
            // Attempt to parse the data using Carbon
            $parsedDate = Carbon::createFromFormat('Y-m-d', $dateString);

            // check if the parsed date matches the input date string
            return $parsedDate->format('Y-m-d') === $dateString;

        } catch (Exception $ex) {
            return false;
        }
    }


    // below code may not be relevant
    public function cbeDatas(Request $request){
        try {

            // $validated = $request->validate([
            //     'amount' => 'required|numeric',
            //     'equb_id' => 'required|exists:equbs,id',
            //     // 'token' => 'required|exists:app_tokens,token',
            //     'phone' => 'required|exists:app_tokens,phone',
            // ]);
            $amount = 5;
            $equb_id = 254;
            $phone = "+251918094455";


            // $phone = AppToken::where('phone', $validated['phone'])->orderBy('created_at', 'desc')->pluck('phone')->first();
            $token = AppToken::where('phone', $phone)->orderBy('created_at', 'desc')->pluck('token')->first();
            // $equb_id = $validated['equb_id'];
            // $amount = $validated['amount'];

            // $equb = Equb::with('equbType')->whereHas('member', function ($query) use ($phone) {
            //     $query->where('phone', $phone);
            // })->get();

            // if ($equb->count() === 0) {
            //     return view('cbe_payment', [
            //         'token' => $token,
            //         'phone' => $phone,
            //         'equbs' => [],
            //         'error' => 'No equb found for the user'
            //     ]);
            // }
            $equb = Equb::with('equbType')->where('id', $equb_id)->first();

            // **Render the Blade view with the required data**
            $view = view('cbe_payment', [
                'token' => $token,
                'phone' => $phone,
                'equb' => $equb,
                'amount' => $amount,
                'error' => '',
                'url' => route('cbe.payment')
            ]);

            // **Return JSON response for the mobile app**
            // if ($request->wantsJson()) {
            //     return response()->json([
            //         'url' => route('cbe.payment'), // Return the route of the view
            //     ]);
            // }

            // **If it's a normal web request, return the Blade view**
            // return $view;
            return response($view)->header('X-URL', route('cbe.payment'));
            

            
        } catch (Exception $ex) {
            return response()->json([
                'error' => $ex->getMessage()
            ], 500);
        }
        
    }

    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60, // Use 'api' guard for JWT
        ]);
    }

    public function mainEqub(Request $request)
    {
        try {
            $mainEqubs = MainEqub::with(['subEqub' => function ($query) {
                $query->where('end_date', '>=', now());
            }])->get();

            return response()->json([
                'data' => MainEqubResource::collection($mainEqubs),
                'code' => 200
            ]);

        } catch (Exception $ex) {
            return response()->json([
                'error' => $ex->getMessage()
            ], 500);
        }
    }

    public function joinEqub(Request $request) {
        try {
            // Dynamic Validation
            $rules = [
                'type' => 'required|in:Manual,Automatic',
                'amount' => ['required', function ($attribute, $value, $fail) use ($request) {
                    if ($request->input('type') === 'Manual' && ($value < 500 || $value > 15000)) {
                        $fail("The {$attribute} must be between 500 and 15000.");
                    }
                }],
                'total_amount' => 'required',
                'start_date' => 'required|date_format:Y-m-d',
            ];

            if ($request->input('type') === 'Automatic') {
                $rules['equb_type_id'] = 'required|exists:equb_types,id';
            }
            $this->validate($request, $rules);

            // Collecting request inputs
            $type = $request->input('type');
            $amount = $request->input('amount');
            $totalAmount = $request->input('total_amount');
            $startDate = $request->input('start_date');
            $endDate = $request->input('end_date');
            $timeline = $request->input('timeline');
            $lotteryDate = $request->input('lottery_date');
            $memberId = $request->input('member_id');
            $main_equb_id = $request->input('main_equb_id');

            // Validate Member existence
            $member = Member::findOrFail($memberId);
            $userData = User::where('phone_number', $member->phone)->first();
            if (!$member) {
                return response()->json([
                    'code' => 404,
                    'message' => 'Member not found'
                ]);
            }

            // Format end date if needed
            if (!$this->isDateInYMDFormat($endDate)) {
                try {
                    $carbonDate = Carbon::createFromFormat('m/d/Y', $endDate);
                    $endDate = $carbonDate->format('Y-m-d');

                } catch (Exception $ex) {
                    Log::error("Date parsing error for endDate: " . $ex->getMessage());
                    return response()->json([
                        'code' => 400,
                        'message' => 'Invalid date format for end date.'
                    ]);
                }
            }
            // Handle manual equb (create new EqubType)
            if ($type === 'Manual') {
                $equbType = EqubType::create([
                    'name' => 'Manual Equb -' . now()->timestamp,
                    'main_equb_id' => $main_equb_id,
                    'round' => 1,
                    'amount' => $amount,
                    'total_amount' => $totalAmount,
                    'total_members' => 0,
                    'expected_members' => 0,
                    'status' => 'active',
                    'remark' => 'Auto-created Manual Equb',
                    'rote' => 'Daily',
                    'type' => 'Manual',
                    'terms' => 'Standard terms apply',
                    'quota' => 0,
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                    'remaining_quota' => 0,
                    'image' => null,
                    'lottery_round' => 0
                ]);
            } else {
                $equbType = EqubType::find($request->input('equb_type_id'));
                if (!$equbType) {
                    return response()->json([
                        'code' => 404,
                        'message' => 'Equb type not found'
                    ]);
                }
            }

            // prevent duplicate equb registsration for the same member
            if (Equb::where('equb_type_id', $equbType->id)->where('member_id', $memberId)->exists()) {
                return response()->json([
                    'code' => 400,
                    'message' => 'You have already joined this equb'
                ]);
            }
            // Automaticallhy calculate lottery date for 'Manual' Equb
            if ($type === 'Manual') {
                if (!$lotteryDate) {
                    $lotteryDate = Carbon::parse($startDate)->addDays(45)->format('Y-m-d');
                }

                // Check for existing lotteries on the same date
                if (Equb::where('lottery_date', $lotteryDate)->exists()) {
                    return response()->json([
                        'code' => 400,
                        'message' => 'Lottery date already exists for another equb.'
                    ]);
                }

                // Ensure sufficient fund before finalizing lottery date
                $cashProjection = Equb::whereDate('lottery_date', $lotteryDate)->sum('amount');
                if ($cashProjection < $totalAmount) {
                    // if insufficient funds, extend the lottery date
                    $lotteryDate = Carbon::parse($lotteryDate)->addDay()->format('Y-m-d');
                }
            } else {
                // Assign the existing lottery date from equbType for automatic equb
                $lotteryDate = $equbType->lottery_date;
            }

            // Create equb entry
            $equbData = [
                'member_id' => $memberId,
                'equb_type_id' => $equbType->id,
                'amount' => $amount,
                'total_amount' => $totalAmount,
                'start_date' => $startDate,
                'timeline' => $timeline,
                'end_date' => $endDate,
                'lottery_date' => $lotteryDate
            ];
            $equb = $this->equbRepository->create($equbData);

            if ($equb) {
                // if automatic, update equbType member count
                if ($type === 'Automatic') {
                    $equbType->increment('remaining_quota', 1);
                    $equbType->increment('total_members', 1);
                }

                // Register Equb taker
                $this->equbTakerRepository->create([
                    'member_id' => $memberId,
                    'equb_id' => $equb->id,
                    'payment_type' => '',
                    'amount' => $totalAmount,
                    'remaining_amount' => $totalAmount,
                    'status' => 'unpaid',
                    'paid_by' => '',
                    'total_payment' => 0,
                    'remaining_payment' => $totalAmount,
                    'cheque_amount' => '',
                    'cheque_bank_name' => '',
                    'cheque_description' => '',
                ]);

                // Log activity
                $this->activityLogRepository->createActivityLog([
                    'type' => 'equbs',
                    'type_id' => $equb->id,
                    'action' => 'created',
                    'user_id' => $userData->id,
                    'username' => $userData->name,
                    'role' => $userData->role
                ]);

                // Send SMS Notifications
                $shortcode = config('key.SHORT_CODE');
                $member = Member::find($memberId);
                if ($member && $member->phone) {
                    $memberMessage = "Dear {$member->full_name}, Your Equb has been successfully registered. Our customer service will contact you soon. For more information call {$shortcode}";
                    $this->sendSms($member->phone, $memberMessage);
                }

                // Finance Sms
                $finances = User::role('finance')->get();
                foreach ($finances as $finance) {
                    if ($finance->phone_number) {
                        $financeMessage = "Finance Alert: A New Member {$member->full_name}, has joined Equb titled {$equb->equbType->name}. Please review the details.";
                        $this->sendSms($finance->phone_number, $financeMessage);
                    }
                }

                // Call center sms
                $call_centers = User::role('call_center')->get();
                foreach($call_centers as $finance) {
                    if ($finance->phone_number) {
                        $financeMessage = "Call Center Alert: A New Member {$member->full_name}, has joined Equb titled {$equb->equbType->name}. Please review the details.";
                        $this->sendSms($finance->phone_number, $financeMessage);
                    }
                }

                return response()->json([
                    'code' => 200,
                    'message' => 'Equb has been registered successfully!',
                    'data' => $equb
                ]);
            } else {
                return response()->json([
                    'code' => 400,
                    'message' => 'Unknown error occured, Please try again!'
                ]);
            }
            


        } catch (Exception $ex) {
            return response()->json([
                'error' => $ex->getMessage()
            ], 500);
        }
    }

    public function validateToken(Request $request)
    {
        try {
            $token = $request->header('Authorization');
            // $token = "Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJwaG9uZSI6IjI1MTkxODA5NDQ1NSIsImV4cCI6MTczODYzMjEzMH0.cN95szHJNoJwp8tdtpDOk29vPmQeVoYP8dbKFBFy4_M";
            // $token = "Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJwaG9uZSI6IjI1MTkxODA5NDQ1NSIsImV4cCI6MTczODI3OTU0NH0.8NrfTbeErIXyin-PH0Vgvnkq4-q2TeVvQz4P3FtBqZU";
            // $token = "Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJwaG9uZSI6IjI1MTkxODA5NDQ1NSIsImV4cCI6MTc0MzA0MjQxNn0.d3keyZuG0FCQpRVolicjFIgbiSqmSRywJGf4spseeeA";
            // $token = "Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJwaG9uZSI6IjI1MTkxODA5NDQ1NSIsImV4cCI6MTc0NTM2NTMxNX0.FovGa2KY6PXdgfva0OlldWcs3Yv9fQfF9mgLUEr3CcY";
            Log::info('mini app Token', ['token' => $token]);
            // dd($token);
            if (!$token) {
                return response()->json([
                    'error' => 'Token is missing'
                ], 400);
            }

            // Remove the 'Bearer' prefix
            $cleanedToken = str_replace('Bearer ', '', $token);

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => $token,
            ])->get('https://cbebirrpaymentgateway.cbe.com.et:8888/auth/user');

            if ($response->status() === 200) {
                // Save the token to the database
                $Phone =  $response->json('phone');

                // Check if the phone starts with "+"
                if (!$Phone) {
                    return response()->json(['error' => 'Phone number is missing or invalid'], 400);
                }
                if (strpos($request->json('phone'), '+') !== 0) {
                    $Phone = '+' . $Phone;
                }
                AppToken::create([
                    'phone' => $Phone,
                    'token' => $cleanedToken
                ]);
                // $phone = $response->json('phone');
                $equb = Equb::with('equbType')->whereHas('member', function ($query) use ($Phone) {
                    $query->where('phone', $Phone);
                })->get();
                // dd($equb);
                if ($equb->count() === 0) {
                    // return response()->json(['error' => 'No equb found for the user'], 404);
                    return view('cbe_payment', [
                        'token' => $token, 
                        'phone' => $Phone, 
                        'equbs' => [], 
                        'error' => 'No equb found for the user'
                    ]);
                }
                return view('cbe_payment', [
                    'token' => $cleanedToken, 
                    'phone' => $Phone, 
                    'equbs' => $equb,
                    'error' => ''
                ]);
            } else {
                return response()->json(['error' => 'Invalid Token'], 401);
            }
        } catch (Exception $ex) {
            return response()->json([
                'error' => $ex->getMessage()
            ], 500);
        }
    }
}
