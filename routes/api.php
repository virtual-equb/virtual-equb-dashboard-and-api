<?php

use App\Models\User;
use App\Models\Roles;
use Illuminate\Support\Facades\Log;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Request;
use App\Http\Controllers\CodeController;
use App\Http\Controllers\SubcityController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CityController;
use App\Http\Controllers\Api\EqubController;
use App\Http\Controllers\Api\HomeController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\ChapaController;
use App\Http\Controllers\Api\MemberController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\CountryController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\EqubTypeController;
use App\Http\Controllers\Api\MainEqubController;
use App\Http\Controllers\Api\EqubTakerController;
use App\Http\Controllers\Api\PermissionController;
use App\Http\Controllers\Api\ActivityLogController;
use App\Http\Controllers\Api\CbeMiniAppController;
use App\Http\Controllers\Api\CountryCodeController;
use App\Http\Controllers\Api\PaymentTypeController;
use App\Http\Controllers\Api\RejectedDateController;
use App\Http\Controllers\Api\PaymentTesterController;
use App\Http\Controllers\Api\PaymentGatewayController;
use App\Http\Controllers\EqubController as ControllersEqubController;
use App\Http\Controllers\Api\SubcityController as ApiSubcityController;
use App\Http\Controllers\EqubTypeController as ControllersEqubTypeController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::get('/cbe-payment', [CbeMiniAppController::class, 'index']);
// Route::get('/validate-token', [CbeMiniAppController::class, 'validateToken']);
Route::post('/registermember', [CbeMiniAppController::class, 'register']);
Route::post('/loginmember', [CbeMiniAppController::class, 'login']);
Route::get('/getmainEqub', [CbeMiniAppController::class, 'mainEqub']);
Route::post('/joinequb', [CbeMiniAppController::class, 'joinEqub']);
Route::post('/process-payment', [CbeMiniAppController::class, 'processPayment'])->name('cbe.initialize');
Route::post('/callback', [CbeMiniAppController::class, 'paymentCallback'])->name('cbe.callback');


Route::post('/drawauto', [ControllersEqubTypeController::class, 'drawSeasonedAutoWinners']);
Route::get('/jwt', [PaymentGatewayController::class, 'testJWT']);
Route::post('/transaction-status', [PaymentGatewayController::class, 'transactionStatus']);
Route::post('/payments/telebirr/callback', [PaymentController::class, 'callback'])->name('callback');
Route::post('/notify-equb-start', [EqubController::class, 'sendStartNotifications']);
Route::post('/notify-equb-ends', [EqubController::class, 'sendEndNotifications']);
Route::post('/daily-payment-notification', [EqubController::class, 'sendDailyPaymentNotification']);
Route::post('/lottery-notification', [EqubController::class, 'sendLotteryNotification']);
Route::post('/notify-missing-payment', [EqubController::class, 'sendMissedPaymentNotification']);

Route::get('/registrationCity', [CityController::class, 'index'])->name('registrationCity');
Route::post('member/registerMember', [MemberController::class, 'register'])->name('storeMember');
Route::post('member/updateProfile/{id}', [MemberController::class, 'updateProfile'])->name('updateProfile'); // old update
// Route::middleware(['auth:api'])->put('member/updateProfile/{id}', [MemberController::class, 'updateProfile'])->name('updateProfile'); // new update
Route::post('/checkMemberPhoneExist', [MemberController::class, 'checkMemberPhoneExist'])->name('check_member_phone_exist');
Route::post('/checkUserPhoneExist', [UserController::class, 'checkPhone'])->name('check_user_phone_exist');
Route::post('/resetPassword', [UserController::class, 'resetPasswordUser']);
Route::get('/getMembersByEqubType', [MemberController::class, 'getMembersByEqubType'])->name('getMembersByEqubType');
Route::get('/getPaymentsByReference/{reference}', [PaymentController::class, 'getPaymentsByReference'])->name('getPaymentsByReference');
Route::get('/sendOtp/{phone}', [UserController::class, 'sendOtp'])->name('sendOtp');
Route::get('/verifyOtp/{code}/{phone}', [UserController::class, 'verifyOtp'])->name('verifyOtp');
// Route::get('/getEqubType', [EqubTypeController::class, 'getEqubType'])->name('getEqubType');


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::group([
    'middleware' => 'api',
    'prefix' => 'auth'

], function ($router) {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('refresh', [AuthController::class, 'refresh']);
    Route::get('profile', [AuthController::class, 'profile']);
});

Route::get('/dashboard', [HomeController::class, 'index'])->name('dashboard');
Route::get('/equbTypeDashboard/{equb_type_id}', [HomeController::class, 'equbTypeIndex'])->name('equbTypeDashboard');
Route::post('/offDateCheck', [RejectedDateController::class, 'offDateCheck'])->name('offDateCheck');
Route::post('/updateoffDateCheck', [RejectedDateController::class, 'offDateCheck'])->name('updateoffDateCheck');
Route::post('/equbTypeCheck', [EqubController::class, 'equbCheck'])->name('equb_type_check');
Route::post('/equbTypeCheckForUpdate', [EqubController::class, 'equbCheckForUpdate'])->name('equb_type_check_for_update');
Route::post('/phoneCheck', [MemberController::class, 'phoneCheck'])->name('phone_check');
Route::post('/nameCheck', [EqubTypeController::class, 'nameEqubTypeCheck'])->name('name_check');
Route::post('/nameCheckForUpdate', [EqubTypeController::class, 'nameEqubTypeCheckForUpdate'])->name('name_check_for_update');
Route::post('/dateCheckForUpdate', [EqubTypeController::class, 'dateEqubTypeCheckForUpdate'])->name('date_check_for_update');
Route::post('/dateCheck', [EqubTypeController::class, 'dateEqubTypeCheck'])->name('date_check');
Route::post('/dateEqubCheck', [EqubController::class, 'dateEqubCheck'])->name('date_equb_check');
Route::post('/startDateCheck', [EqubController::class, 'startDateCheck'])->name('start_date_check');
Route::post('/dateEqubLotteryCheck', [EqubController::class, 'dateEqubLotteryCheck'])->name('date_equb_lottery_check');
Route::post('/userPhoneCheck', [UserController::class, 'userPhoneCheck'])->name('user_phone_check');
Route::post('/emailCheck', [UserController::class, 'emailCheck'])->name('email_check');
Route::post('/lotteryDateCheck', [EqubController::class, 'lotteryDateCheck'])->name('lottery_Date_check');
Route::post('/lotteryDateCheckForUpdate', [EqubController::class, 'lotteryDateCheckForUpdate'])->name('lottery_date_check_for_update');
Route::get('/getRemainingLotteryAmount/{id}', [EqubTakerController::class, 'getRemainingLotteryAmount'])->name('getRemainingLotteryAmount');
Route::post('/dateInterval', [EqubController::class, 'dateInterval'])->name('dateInterval');
Route::get('/getDailyPaidAmount/{equb_id}', [EqubController::class, 'getDailyPaidAmount'])->name('getDailyPaidAmount');
Route::post('/changePassword/{id}', [UserController::class, 'changePassword'])->name('changePassword');


// Main Equb
Route::middleware(['auth:api'])->group(function () {
    Route::resource('/mainequb', MainEqubController::class);
    Route::resource('/countries', CountryController::class);
    Route::resource('/countrycode', CountryCodeController::class);
    Route::resource('/city', CityController::class);
    Route::resource('/subcity', ApiSubcityController::class);
    // Roles & Permissions
    Route::resource('/roles', RoleController::class);
    Route::resource('/permissions', PermissionController::class);
    Route::get('/roles/{roleId}/give-permissions', [RoleController::class, 'addPermissionToRole']);
    Route::put('/roles/{roleId}/give-permissions', [RoleController::class, 'updatePermissionToRole']);
    // CBE
    Route::post('/cbegateway', [PaymentGatewayController::class, 'generateUrl']);
    Route::post('/retry/cbegateway/{id}', [PaymentGatewayController::class, 'regenerateUrl']);
    Route::delete('/cancel/cbegateway/{id}', [PaymentGatewayController::class, 'cancelPayment']);
});


// Route::get('/testequb', [MainEqubController::class, 'getTypes']);

Route::prefix('chapa')->group(function () {
    Route::post('initialize', [ChapaController::class, 'initialize'])->name('initialize');
    // Route::post('return', [ChapaController::class, 'return'])->name('return');
    Route::get('callback/{userId}/{equbId}/{amount}/{reference}', [ChapaController::class, 'callback'])->name('callback');
});

Route::prefix('equbType')->group(function () {
    Route::get('/', [EqubTypeController::class, 'index'])->name('showEqubType');
    Route::get('/showequbtype/{id}', [EqubTypeController::class, 'show'])->name('viewEqubType');
    Route::post('/register', [EqubTypeController::class, 'store'])->name('registerEqubType');
    Route::post('/update/{id}', [EqubTypeController::class, 'update'])->name('updateEqubType');
    Route::delete('/delete/{id}', [EqubTypeController::class, 'destroy'])->name('deleteEqubType');
    Route::put('/updateStatus/{id}', [EqubTypeController::class, 'updateStatus'])->name('updateStatus');
    Route::get('/get-winner/{id}', [EqubTypeController::class, 'getWinner'])->name('getWinner');
    Route::get('/{equbTypeId}/icon', [EqubTypeController::class, 'getIcon'])->name('getIcon');
});
Route::prefix('equb')->group(function () {
    Route::get('/', [EqubController::class, 'index'])->name('showEqub');
    Route::get('/equb-lottery-detail/{lottery_date}', [EqubController::class, 'getReservedLotteryDate'])->name('showAllEqub');
    Route::get('/equb-register', [EqubController::class, 'create'])->name('creatEqub');
    Route::get('/show-equb/{id}', [EqubController::class, 'show'])->name('showAllEkub');
    Route::post('/equb-register', [EqubController::class, 'store1'])->name('registerEqub');
    Route::post('/equb-update/{id}', [EqubController::class, 'update'])->name('updateEqub');
    Route::put('/equbStatus-update/{id}', [EqubController::class, 'updateStatus'])->name('updateEqubStatus');
    Route::delete('/equb-delete/{id}', [EqubController::class, 'destroy'])->name('deleteEqub');
    Route::get('/get-paid-equbs/{memberId}', [EqubController::class, 'getPaidEqubs'])->name('getPaidEqubs');
});
Route::prefix('member')->group(function () {
    Route::get('/', [MemberController::class, 'index'])->name('showMember');
    Route::get('/getMemberById/{id}', [MemberController::class, 'getMemberById'])->name('getMemberById');
    Route::get('/clearSearchEntry', [MemberController::class, 'clearSearchEntry'])->name('clearSearchEntry');
    Route::get('/loadMoreMember/{offsetVal}/{pageNumberVal}', [MemberController::class, 'loadMoreMember']);
    Route::get('/get-equbs/{id}', [MemberController::class, 'show'])->name('getEqub');
    Route::get('/get-paid-equbs/{id}', [MemberController::class, 'getPaidEqubs'])->name('getPaidEqub');
    Route::get('/get-passed-equbs/{id}', [MemberController::class, 'getEndedEqubs']);
    Route::get('/search-member/{searchInput}/{offset}/{pageNumber?}', [MemberController::class, 'searchMember'])->name('searchMember');
    Route::get('/get-allEqubs', [MemberController::class, 'getAllEqubs'])->name('getAllEqub');
    Route::get('/create-member', [MemberController::class, 'create'])->name('createMember');
    Route::post('/register', [MemberController::class, 'store'])->name('registerMember');
    Route::get('/edit/{id}', [MemberController::class, 'edit'])->name('editMember');
    Route::put('/update/{id}', [MemberController::class, 'update'])->name('updateMember');
    Route::put('/rate/{id}', [MemberController::class, 'rate'])->name('rateMember');
    Route::put('/updateStatus/{id}', [MemberController::class, 'updateStatus'])->name('updateMemberStatus');
    Route::delete('/delete/{id}', [MemberController::class, 'destroy'])->name('deleteMember');
    Route::get('/{userId}/profile-picture', [MemberController::class, 'getProfilePicture'])->name('getProfilePicture');
});
Route::prefix('payment')->group(function () {
    Route::get('/check-payment/{payment}', [PaymentController::class, 'getTransaction'])->name('getTransaction');
    Route::get('/{member_id}/{equb_id}', [PaymentController::class, 'index'])->name('showAllPayment');
    Route::get('/show-payment/{member_id}/{equb_id}/{offsetVal}/{pageNumberVal}', [PaymentController::class, 'show'])->name('showPayment');
    Route::post('/register', [PaymentController::class, 'store'])->name('registerPayment');
    Route::post('/registerForAdmin', [PaymentController::class, 'storeForAdmin'])->name('registerPaymentForAdmin');
    Route::put('/updatePayment/{member_id}/{equb_id}/{id}', [PaymentController::class, 'updatePayment']);
    Route::delete('/deleteAll/{member_id}/{equb_id}', [PaymentController::class, 'deleteAllPayment'])->name('deleteAllPayment');
    Route::delete('/delete/{id}', [PaymentController::class, 'destroy'])->name('deletePayment');
    // Route::post('telebirr', [PaymentController::class, 'initialize'])->name('initialize');
    // Route::get('/telebirr/callback/{payment}', [PaymentController::class, 'callback'])->name('callback');
    Route::post('telebirr', [PaymentController::class, 'initialize'])->name('initialize');
});
Route::prefix('equbTaker')->group(function () {
    Route::get('/', [EqubTakerController::class, 'index'])->name('showEqubTaker');
    Route::post('/equbTaker-register', [EqubTakerController::class, 'store'])->name('registerEqubTaker');
    Route::put('/updateLottery/{member_id}/{equb_id}/{id}', [EqubTakerController::class, 'updateLottery']);
    Route::delete('/equbTaker-delete/{id}', [EqubTakerController::class, 'destroy'])->name('deleteEqubTaker');
});
Route::prefix('rejectedDate')->group(function () {
    Route::get('/', [RejectedDateController::class, 'index'])->name('showRejectedDate');
    Route::post('/register', [RejectedDateController::class, 'store'])->name('registerRejectedDate');
    Route::put('/update/{id}', [RejectedDateController::class, 'update'])->name('updateRejectedDate');
    Route::delete('/delete/{id}', [RejectedDateController::class, 'destroy'])->name('deleteRejectedDate');
});
Route::prefix('user')->group(function () {
    Route::get('/', [UserController::class, 'index'])->name('user');
    Route::get('/user/{offsetVal}/{pageNumberVal}', [UserController::class, 'user']);
    Route::get('/deactiveUser/{offsetVal}/{pageNumberVal}', [UserController::class, 'deactiveUser']);
    Route::post('/store-user', [UserController::class, 'store'])->name('registerUser');
    Route::get('/edit/{id}', [UserController::class, 'edit'])->name('editUser');
    Route::put('/update/{id}', [UserController::class, 'update'])->name('updateUser');
    Route::put('/deactivateUser/{id}', [UserController::class, 'deactiveStatus'])->name('deactivateUser');
    Route::put('/activateUser/{id}', [UserController::class, 'activeUser'])->name('activateUser');
    Route::delete('/delete/{id}', [UserController::class, 'destroy'])->name('deleteUser');
    Route::post('/resetPassword', [UserController::class, 'resetPassword']);
});
Route::prefix('reports')->group(function () {
    Route::get('/memberFilter', [ReportController::class, 'memberFilter'])->name('memberFilter');
    Route::get('/members/{dateFrom}/{dateTo}', [ReportController::class, 'members'])->name('members');
    Route::get('/paginateMembers/{dateFrom}/{dateTo}/{offsetVal}/{pageNumberVal}', [ReportController::class, 'paginateMembers']);
    Route::get('/equbTypeFilter', [ReportController::class, 'equbTypeFilter'])->name('equbTypeFilter');
    Route::get('/equbTypes/{dateFrom}/{dateTo}', [ReportController::class, 'equbTypes'])->name('equbTypes');
    Route::get('/equbFilter', [ReportController::class, 'equbFilter'])->name('equbFilter');
    Route::get('/equbs/{dateFrom}/{dateTo}', [ReportController::class, 'equbs'])->name('equbs');
    Route::get('/paginateEqubs/{dateFrom}/{dateTo}/{offsetVal}/{pageNumberVal}', [ReportController::class, 'paginateEqubs']);

    Route::get('/lotteryDateFilter', [ReportController::class, 'lotteryDateFilter'])->name('lotteryDateFilter');
    Route::get('/lotteryDate/{dateFrom}/{dateTo}', [ReportController::class, 'lotteryDate'])->name('lotteryDate');
    Route::get('/paginateLotteryDate/{dateFrom}/{dateTo}/{offsetVal}/{pageNumberVal}', [ReportController::class, 'paginateLotteryDate']);

    Route::get('/lotteryFilter', [ReportController::class, 'lotteryFilter'])->name('lotteryFilter');
    Route::get('/lotterys/{dateFrom}/{dateTo}/{member_id}/{equb_type_id}', [ReportController::class, 'lotterys'])->name('lotterys');
    Route::get('/paginateLotterys/{dateFrom}/{dateTo}/{member_id}/{equb_type_id}/{offsetVal}/{pageNumberVal}', [ReportController::class, 'paginateLotterys']);

    Route::get('/unPaidLotteryFilter', [ReportController::class, 'unPaidLotteryFilter'])->name('unPaidLotteryFilter');
    Route::get('/unPaidLotterys', [ReportController::class, 'unPaidLotterys'])->name('unPaidLotterys');
    Route::get('/paginateUnPaidLotterys/{offsetVal}/{pageNumberVal}', [ReportController::class, 'paginateUnPaidLotterys']);

    Route::get('/paginatePayments/{dateFrom}/{dateTo}/{member_id}/{equb_id}/{offsetVal}/{pageNumberVal}', [ReportController::class, 'paginatePayments']);
    Route::get('/paymentFilter', [ReportController::class, 'paymentFilter'])->name('paymentFilter');
    Route::get('/payments/{dateFrom}/{dateTo}/{member_id}/{equb_id}', [ReportController::class, 'payments'])->name('payments');

    Route::get('/paginateCllectedBys/{dateFrom}/{dateTo}/{collecter}/{offsetVal}/{pageNumberVal}', [ReportController::class, 'paginateCllectedBys']);
    Route::get('/collectedByFilter', [ReportController::class, 'collectedByFilter'])->name('collectedByFilter');
    Route::get('/collectedBys/{dateFrom}/{dateTo}/{collecter}/', [ReportController::class, 'collectedBys'])->name('collectedBys');
});
Route::get('fallback/', function () {
    return response()->json([
        'code' => 401,
        'message' => 'Unauthorized!'
    ], 401);
})->name('api-fallback');
Route::prefix('activityLog')->group(function () {
    Route::get('/', [ActivityLogController::class, 'index'])->name('showActivityLog');
    Route::get('/logDetail/{type}', [ActivityLogController::class, 'logDetail'])->name('logDetail');
    Route::get('/logDetailPaginate/{type}/{offsetVal}/{pageNumberVal}', [ActivityLogController::class, 'logDetailPaginate'])->name('logDetailPaginate');
});
