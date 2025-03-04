<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SubController;
use App\Http\Controllers\BoleController;
use App\Http\Controllers\CityController;
use App\Http\Controllers\EqubController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RolesController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\SubCityController;
use App\Http\Controllers\WebRoleController;
use App\Http\Controllers\EqubTypeController;
use App\Http\Controllers\MainEqubController;
use App\Http\Controllers\EqubTakerController;
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\PaymentTypeController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\RejectedDateController;
use App\Http\Controllers\FrontMainEqubController;
use App\Http\Controllers\WebPermissionController;
use App\Http\Controllers\Api\CbeMiniAppController;
use App\Http\Controllers\TermsAndConditionsController;
use App\Http\Middleware\LogUserActionMiddleware;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
// Route::get('/cbe-payment', function () {
//     return view('cbe_payment');
// })->name('cbe.payment');

Route::get('/unauthorized', function () {
    return view('errorPages.authorization'); // Replace with your unauthorized view
})->name('unauthorized');

Route::resource('/permission', WebPermissionController::class);
Route::get('/permission/{permissionId}/delete',[ WebPermissionController::class, 'destroy']);
Route::resource('/roles', WebRoleController::class);
Route::get('/roles/{roleId}/delete', [WebRoleController::class, 'destroy']);
Route::get('/roles/{roleId}/assign-permission', [WebRoleController::class, 'assignPermission']);
Route::put('/roles/{roleId}/assign-permission', [WebRoleController::class, 'updateRolePermission']);

// mini app
Route::get('/cbe-payment', [CbeMiniAppController::class, 'index']);
Route::get('/validate-token', [CbeMiniAppController::class, 'validateToken']);
Route::post('/process-payment', [CbeMiniAppController::class, 'processPayment'])->name('cbe.initialize');
// Route::post('/callback', [CbeMiniAppController::class, 'paymentCallback'])->name('cbe.callback');

Route::get('/register', function () {
    return view('auth/login');
});

Route::get('/', function () {
    return view('auth/login');
});
Route::get('/privacyPolicy', function () {
    return view('policy');
});
Route::get('/support', function () {
    return view('support');
});
Route::get('/terms', function () {
    return view('terms');
});
Route::get('/terms-and-conditions', [TermsAndConditionsController::class, 'index'])->name('terms.show');
Route::middleware([
    'auth',
    config('jetstream.auth_session'),
    'verified'
])->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\HomeController::class, 'index'])->name('dashboard');
    Route::get('/equbTypeDashboard/{equb_type_id}', [App\Http\Controllers\HomeController::class, 'equbTypeIndex'])->name('equbTypeDashboard');
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

    //equb type routes
    Route::group(['prefix' => 'equbType'], function () {
        Route::get('/', [EqubTypeController::class, 'index'])->name('showEqubType');
        Route::get('/register', [EqubTypeController::class, 'create'])->name('creatEqubType');
        Route::post('/register', [EqubTypeController::class, 'store'])->name('registerEqubType');
        Route::post('/drawAutoWinners', [EqubTypeController::class, 'drawAutoWinners'])->name('drawAutoWinners');
        Route::post('/drawAutoSeasonal', [EqubTypeController::class, 'drawSeasonedAutoWinners'])->name('drawAutoSeasonal');
        Route::get('/edit/{id}', [EqubTypeController::class, 'edit'])->name('editEqubType');
        Route::post('/update/{id}', [EqubTypeController::class, 'update'])->name('updateEqubType');
        Route::put('/deactiveEqubTypeStatus/{id}', [EqubTypeController::class, 'deactiveStatus'])->name('deactiveEqubTypeStatus');
        Route::put('/activeEqubTypeStatus/{id}', [EqubTypeController::class, 'activeStatus'])->name('activeEqubTypeStatus');
        Route::delete('/delete/{id}', [EqubTypeController::class, 'destroy'])->name('deleteEqubType');
        Route::put('/updateStatus/{id}', [EqubTypeController::class, 'updateStatus'])->name('updateStatus');
        Route::put('/updatePendingStatus/{id}/{status}', [EqubTypeController::class, 'updatePendingStatus'])->name('updatePendingStatus'); 
        Route::get('/member/{id}', [EqubController::class, 'memberByEqubType'])->name('memberByEqubType');
    });

    Route::group(['prefix' => 'mainEqub'], function () {
        Route::get('/mainequbs', [FrontMainEqubController::class, 'index'])->name('mainequbIndex');
        Route::get('/viewmainequb/{id}', [FrontMainEqubController::class, 'show'])->name('viewMainEqub');
        Route::post('/storemainequb', [FrontMainEqubController::class, 'store'])->name('storeMainEqub');
    });

    Route::group(['prefix' => 'member'], function () {
        Route::get('/', [MemberController::class, 'index'])->name('showMember');
        Route::get('/showPendingMembers', [MemberController::class, 'indexPending'])->name('showPendingMembers');
        Route::get('/countPendingMembers', [MemberController::class, 'countPending'])->name('countPending');
        Route::get('/getPending', [MemberController::class, 'getPending'])->name('getPending');
        Route::get('/clearSearchEntry', [MemberController::class, 'clearSearchEntry'])->name('clearSearchEntry');
        Route::get('/clearPendingSearchEntry', [MemberController::class, 'clearPendingSearchEntry'])->name('clearPendingSearchEntry');
        Route::get('/member/{offsetVal}/{pageNumberVal}', [MemberController::class, 'member']);
        Route::get('/pendingMember/{offsetVal}/{pageNumberVal}', [MemberController::class, 'pendingMember']);

        Route::get('/get-equbs/{id}', [MemberController::class, 'getEqubs'])->name('getEqub');
        Route::get('/search-member/{searchInput}/{offset}/{pageNumber?}', [MemberController::class, 'searchMember'])->name('searchMember');
        Route::get('/search-pending-member/{searchInput}/{offset}/{pageNumber?}', [MemberController::class, 'searchPendingMember'])->name('searchPendingMember');
        Route::get('/search-equb/{searchInput}/{offset}/{pageNumber?}', [MemberController::class, 'searchEqub'])->name('searchEqub');
        Route::get('/search-pending-equb/{searchInput}/{offset}/{pageNumber?}', [MemberController::class, 'searchPendingEqub'])->name('searchPendingEqub');
        Route::get('/search-status/{searchInput}/{offset}/{pageNumber?}', [MemberController::class, 'searchStatus'])->name('searchStatus');
        Route::get('/get-allEqubs', [MemberController::class, 'getAllEqubs'])->name('getAllEqub');
        Route::get('/show-member/{id}', [MemberController::class, 'show'])->name('showAllMember');
        Route::get('/create-member', [MemberController::class, 'create'])->name('createMember');
        // Route::get('/register',[MemberController::class, 'create'])->name('createMember');
        Route::post('/register', [MemberController::class, 'store'])->name('registerMember');
        Route::get('/edit/{id}', [MemberController::class, 'edit'])->name('editMember');
        Route::put('/update/{id}', [MemberController::class, 'update'])->name('updateMember');
        Route::put('/rate/{id}', [MemberController::class, 'rate'])->name('rateMember');
        Route::put('/updateStatus/{id}', [MemberController::class, 'updateStatus'])->name('updateMemberStatus');
        Route::put('/updatePendingStatus/{id}/{status}', [MemberController::class, 'updatePendingStatus'])->name('updatePendingMemberStatus');
        Route::delete('/delete/{id}', [MemberController::class, 'destroy'])->name('deleteMember');

        Route::get('/equb-lottery-detail/{lottery_date}', [EqubController::class, 'getReservedLotteryDate'])->name('showAllEqub');
        Route::get('/equb-register', [EqubController::class, 'create'])->name('creatEqub');
        Route::get('/show-equb/{id}', [EqubController::class, 'show'])->name('showAllEkub');
        Route::post('/equb-register', [EqubController::class, 'store'])->name('registerEqub');
        Route::get('/equb-edit/{id}', [EqubController::class, 'edit'])->name('editEqub');
        Route::post('/equb-update/{id}', [EqubController::class, 'update'])->name('updateEqub');
        Route::put('/equbStatus-update/{id}', [EqubController::class, 'updateStatus'])->name('updateEqubStatus');
        Route::put('/equb-check-for-draw-update/{id}', [EqubController::class, 'equbCheckForDrawUpdate'])->name('equbCheckForDrawUpdate');
        Route::delete('/equb-delete/{id}', [EqubController::class, 'destroy'])->name('deleteEqub');
        Route::post('/add-unpaid/{id}', [EqubController::class, 'addUnpaid'])->name('addUnpaid');
    });

    // payment routes
    Route::group(['prefix' => 'payment'], function () {
        Route::post('/defaultPayment.', [PaymentController::class, 'defaultPayment'])->name('defaultPayment');
        Route::get('/{member_id}/{equb_id}', [PaymentController::class, 'index'])->name('showAllPayment');
        Route::get('/show-payment/{member_id}/{equb_id}/{offsetVal}/{pageNumberVal}', [PaymentController::class, 'show'])->name('showPayment');
        Route::get('/show-all-pending-payment', [PaymentController::class, 'indexAll'])->name('showAllPendingPayments');
        Route::get('/show-all-paid-payment', [PaymentController::class, 'paidPayment'])->name('showAllPaidPayments');
        Route::get('/show-pending-payment/{offsetVal}/{pageNumberVal}', [PaymentController::class, 'indexPendingPaginate'])->name('showPendingPayments');
        Route::get('/show-paid-payment/{offsetVal}/{pageNumberVal}', [PaymentController::class, 'indexPaidPaginate'])->name('showPaidPayments');
        
        Route::get('/search-pending-payment/{searchInput}/{offset}/{pageNumber?}', [PaymentController::class, 'searchPendingPayment'])->name('searchPendingMember');
        Route::get('/search-paid-payment/{searchInput}/{offset}/{pageNumber?}', [PaymentController::class, 'searchPaidPayment'])->name('searchPaidMember');
        
        Route::get('/clearPendingSearchEntry', [PaymentController::class, 'clearPendingSearchEntry'])->name('clearPendingSearchEntry');
        Route::get('/register', [PaymentController::class, 'create'])->name('creatPayment');
        Route::post('/register', [PaymentController::class, 'store'])->name('registerPayment');
        Route::get('/edit/{id}', [PaymentController::class, 'edit'])->name('editPayment');
        Route::put('/update/{id}', [PaymentController::class, 'update'])->name('updatePayment');
        Route::get('/editPayment/{member_id}/{equb_id}/{id}', [PaymentController::class, 'editPayment']);
        Route::put('/updatePayment/{member_id}/{equb_id}/{id}', [PaymentController::class, 'updatePayment']);
        Route::put('/updatePendingPayment/{member_id}/{equb_id}/{id}', [PaymentController::class, 'updatePendingPayment']);
        Route::delete('/deleteAll/{member_id}/{equb_id}', [PaymentController::class, 'deleteAllPayment'])->name('deleteAllPayment');
        Route::delete('/delete/{id}', [PaymentController::class, 'destroy'])->name('deletePayment');
        Route::delete('/deletePending/{id}', [PaymentController::class, 'destroyPending'])->name('deletePendingPayment');
        Route::put('/approve/{id}', [PaymentController::class, 'approvePayment'])->name('approvePayment');
        Route::put('/approvePending/{id}', [PaymentController::class, 'approvePendingPayment'])->name('approvePendingPayment');
        Route::put('/reject/{id}', [PaymentController::class, 'rejectPayment'])->name('rejectPayment');
        Route::put('/rejectPending/{id}', [PaymentController::class, 'rejectPendingPayment'])->name('rejectPendingPayment');
        Route::delete('/deletePayment/{member_id}/{equb_id}/{id}', [PaymentController::class, 'deletePayment']);
    });

    // equbtaker routes
    Route::group(['prefix' => 'equbTaker'], function () {
        Route::get('/', [EqubTakerController::class, 'index'])->name('showEqubTaker');
        Route::get('/equbTaker-register', [EqubTakerController::class, 'create'])->name('creatEqubTaker');
        Route::post('/equbTaker-register', [EqubTakerController::class, 'store'])->name('registerEqubTaker');
        Route::get('/equbTaker-edit/{id}', [EqubTakerController::class, 'edit'])->name('editEqubTaker');
        Route::put('/equbTaker-update/{id}', [EqubTakerController::class, 'update'])->name('updateEqubTaker');
        Route::put('/updateLottery/{member_id}/{equb_id}/{id}', [EqubTakerController::class, 'updateLottery']);
        Route::delete('/equbTaker-delete/{id}', [EqubTakerController::class, 'destroy'])->name('deleteEqubTaker');
        Route::post('/equbTaker-change-status/{status}/{id}', [EqubTakerController::class, 'changeStatus'])->name('changeStatusEqubTaker');
    });

    //off date routes
    Route::group(['prefix' => 'rejectedDate'], function () {
        Route::get('/', [RejectedDateController::class, 'index'])->name('showRejectedDate');
        Route::get('/register', [RejectedDateController::class, 'create'])->name('creatRejectedDate');
        Route::post('/register', [RejectedDateController::class, 'store'])->name('registerRejectedDate');
        Route::get('/edit/{id}', [RejectedDateController::class, 'edit'])->name('editRejectedDate');
        Route::put('/update/{id}', [RejectedDateController::class, 'update'])->name('updateRejectedDate');
        Route::delete('/delete/{id}', [RejectedDateController::class, 'destroy'])->name('deleteRejectedDate');
    });

    //notification routes
    Route::group(['prefix' => 'notification'], function () {
        Route::get('/', [NotificationController::class, 'index'])->name('showNotifations');
        Route::get('/sent', [NotificationController::class, 'index'])->name('showSentNotifications');
        Route::get('/create', [NotificationController::class, 'create'])->name('creatNotifation');
        Route::post('/store', [NotificationController::class, 'store'])->name('sendNotifation');
        Route::post('/sendIndividualNotifation', [NotificationController::class, 'sendToIndividual'])->name('sendIndividualNotifation');
        Route::get('/edit/{id}', [NotificationController::class, 'edit'])->name('editNotifation');
        Route::put('/update/{id}', [NotificationController::class, 'update'])->name('resendNotifation');
        Route::put('/updatePending/{id}', [NotificationController::class, 'updatePending'])->name('updatePending');
        Route::delete('/delete/{id}', [NotificationController::class, 'destroy'])->name('deleteNotifation');
        Route::post('/approve/{id}', [NotificationController::class, 'approve'])->name('approveNotifation');
    });


    // user route
    Route::prefix('user')->middleware('auth')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('user');
        Route::get('/user/{offsetVal}/{pageNumberVal}', [UserController::class, 'user']);
        Route::get('/search-user/{searchInput}/{offset}/{pageNumber?}', [UserController::class, 'searchUser'])->name('searchUser');
        Route::get('/deactiveUser/{offsetVal}/{pageNumberVal}', [UserController::class, 'deactiveUser']);
        Route::post('/store-user', [UserController::class, 'store'])->name('registerUser');
        Route::post('/createusers', [UserController::class, 'storeUser'])->name('createUser');
        Route::get('/edit/{id}', [UserController::class, 'edit'])->name('editUser');
        Route::put('/update/{id}', [UserController::class, 'update'])->name('updateUser');
        Route::put('/deactivateUser/{id}', [UserController::class, 'deactiveStatus'])->name('deactivateUser');
        Route::put('/activateUser/{id}', [UserController::class, 'activeUser'])->name('activateUser');
        Route::delete('/delete/{id}', [UserController::class, 'destroy'])->name('deleteUser');
        Route::post('/resetPassword', [UserController::class, 'resetPassword']);
    });

    Route::prefix('reports')->middleware('auth')->group(function () {
        Route::get('/memberFilter', [ReportController::class, 'memberFilter'])->name('memberFilter');
        Route::get('/members/{dateFrom}/{dateTo}', [ReportController::class, 'members'])->name('members');
        Route::get('/paginateMembers/{dateFrom}/{dateTo}/{offsetVal}/{pageNumberVal}', [ReportController::class, 'paginateMembers']);
        Route::get('/memberFilterByEqubType', [ReportController::class, 'memberFilterByEqubType'])->name('memberFilterByEqubType');
        Route::get('/membersByEqubType/{dateFrom}/{dateTo}/{equbType}', [ReportController::class, 'membersByEqubType'])->name('membersByEqubType');
        Route::get('/paginateMembersByEqubType/{dateFrom}/{dateTo}/{equbType}/{offsetVal}/{pageNumberVal}', [ReportController::class, 'paginateMembersByEqubType']);       
        Route::get('/equbTypeFilter', [ReportController::class, 'equbTypeFilter'])->name('equbTypeFilter');
        Route::get('/equbTypes/{dateFrom}/{dateTo}', [ReportController::class, 'equbTypes'])->name('equbTypes');
        Route::get('/paymentFilter', [ReportController::class, 'paymentFilter'])->name('paymentFilter');

        Route::get('/payments', [ReportController::class, 'payments'])->name('payments');

        Route::get('/equbFilter', [ReportController::class, 'equbFilter'])->name('equbFilter');

        Route::get('/equbs/{dateFrom}/{dateTo}/{equbType}', [ReportController::class, 'equbs'])->name('equbs');
        Route::get('/equbs1/{dateFrom}/{dateTo}/{equbType}', [ReportController::class, 'equbs1'])->name('equbs1');

        Route::get('/reportByMethod/{dateFrom}/{dateTo}/{equbType}', [ReportController::class, 'reportByMethod'])->name('equbs');
        Route::get('/paginateEqubs/{dateFrom}/{dateTo}/{offsetVal}/{pageNumberVal}/{equbType}', [ReportController::class, 'paginateEqubs']);
        Route::get('/paginatePaymentMethod/{dateFrom}/{dateTo}/{offsetVal}/{pageNumberVal}/{equbType}', [ReportController::class, 'paginatePaymentMethod']);


        Route::get('/lotteryDateFilter', [ReportController::class, 'lotteryDateFilter'])->name('lotteryDateFilter');
        Route::get('/lotteryDate/{dateFrom}/{dateTo}', [ReportController::class, 'lotteryDate'])->name('lotteryDate');
        Route::get('/paginateLotteryDate/{dateFrom}/{dateTo}/{offsetVal}/{pageNumberVal}', [ReportController::class, 'paginateLotteryDate']);

        Route::get('/lotteryFilter', [ReportController::class, 'lotteryFilter'])->name('lotteryFilter');
        Route::get('/lotterys/{dateFrom}/{dateTo}/{member_id}/{equb_type_id}', [ReportController::class, 'lotterys'])->name('lotterys');
        Route::get('/paginateLotterys/{dateFrom}/{dateTo}/{member_id}/{equb_type_id}/{offsetVal}/{pageNumberVal}', [ReportController::class, 'paginateLotterys']);

        Route::get('/unPaidLotteryFilter', [ReportController::class, 'unPaidLotteryFilter'])->name('unPaidLotteryFilter');
        Route::post('/lotteries/update-to-paid', [ReportController::class, 'updateToPaidLotterys'])->name('lotteries.updateToPaid');
        Route::get('/unPaidLotterys', [ReportController::class, 'unPaidLotterys'])->name('unPaidLotterys');
        Route::get('/paginateUnPaidLotterys/{offsetVal}/{pageNumberVal}', [ReportController::class, 'paginateUnPaidLotterys']);

        Route::get('/unPaidLotteryByDateFilter', [ReportController::class, 'unPaidLotteryByDateFilter'])->name('unPaidLotteryFilter');
        Route::get('/unPaidLotterysByDate/{lotterDyate}/{equbType}', [ReportController::class, 'unPaidLotterysByDate'])->name('unPaidLotterys');
        Route::get('/paginateUnPaidLotterysByDate/{lotterDyate}/{offsetVal}/{pageNumberVal}/{equbType}', [ReportController::class, 'paginateUnPaidLotterysByDate']);

        Route::get('/reservedLotteryDatesFilter', [ReportController::class, 'reservedLotteryDatesFilter'])->name('reservedLotteryDatesFilter');
        Route::get('/reservedLotteryDates/{dateFrom}/{dateTo}/{equbType}', [ReportController::class, 'reservedLotteryDates'])->name('reservedLotteryDates');
        Route::get('/paginateReservedLotteryDates/{dateFrom}/{dateTo}/{offsetVal}/{pageNumberVal}/{equbType}', [ReportController::class, 'paginateReservedLotteryDates']);

        Route::get('/paginatePayments/{dateFrom}/{dateTo}/{member_id}/{equb_id}/{offsetVal}/{pageNumberVal}', [ReportController::class, 'paginatePayments']);
        Route::get('/paymentFilter', [ReportController::class, 'paymentFilter'])->name('paymentFilter');
        Route::get('/payments/{dateFrom}/{dateTo}/{member_id}/{equb_id}', [ReportController::class, 'payments'])->name('payments');
        Route::get('/paginateCllectedBys/{dateFrom}/{dateTo}/{collecter}/{paymentMethod}/{offsetVal}/{pageNumberVal}/{equbType}', [ReportController::class, 'paginateCllectedBys']);
        Route::get('/collectedByFilter', [ReportController::class, 'collectedByFilter'])->name('collectedByFilter');
        Route::get('/collectedBys/{dateFrom}/{dateTo}/{collecter}/{paymentMethod}/{equbType}', [ReportController::class, 'collectedBys'])->name('collectedBys');

        Route::get('/unPaidFilter', [ReportController::class, 'unPaidFilter'])->name('unPaidFilter');
        Route::get('/filterByMethod', [ReportController::class, 'filterByMethod'])->name('filterByMethod');
        
        Route::get('/unPaids/{dateFrom}/{dateTo}/{equbType}', [ReportController::class, 'unPaids'])->name('unPaids');
        Route::get('/loadMoreUnPaids/{dateFrom}/{dateTo}/{offsetVal}/{pageNumberVal}/{equbType}', [ReportController::class, 'loadMoreUnPaids']);

        Route::get('/filterEqubEndDates', [ReportController::class, 'equbEndDates'])->name('unPaidFilter');
        Route::get('/filterPaymentMethod/{dateFrom}/{dateTo}/{equbType}', [ReportController::class, 'filterPaymentMethod'])->name('filterPaymentMethod');
        Route::get('/loadMoreFilterEqubEndDates/{dateFrom}/{dateTo}/{offsetVal}/{pageNumberVal}/{equbType}', [ReportController::class, 'loadMoreFilterEqubEndDates']);
    });

    //activity log routes
    Route::group(['prefix' => 'activityLog'], function () {
        Route::get('/', [ActivityLogController::class, 'index'])->name('showActivityLog');
        Route::get('/activityLog/{offsetVal}/{pageNumberVal}', [ActivityLogController::class, 'paginateActivityLog']);
        Route::get('/logDetail/{type}/{searchInput?}', [ActivityLogController::class, 'logDetail'])->name('logDetail');
        Route::get('/logDetailPaginate/{type}/{offsetVal}/{pageNumberVal}', [ActivityLogController::class, 'logDetailPaginate'])->name('logDetailPaginate');
        Route::get('/search-activity/{type}/{searchInput}/{offset}/{pageNumber?}', [ActivityLogController::class, 'searchActivity'])->name('searchActivity');
        Route::get('/clearSearchEntry', [ActivityLogController::class, 'clearSearchEntry'])->name('clearSearchEntry');
    });

    // Cities routes
    Route::prefix('cities')->group(function () {        
        Route::get('/', [CityController::class, 'index'])->name('cities.index');
        Route::get('/create', [CityController::class, 'create'])->name('admin.city.addCity');
        Route::post('/', [CityController::class, 'store'])->name('cities.store');
        Route::get('{id}', [CityController::class, 'show'])->name('cities.show');
        Route::put('{id}', [CityController::class, 'update'])->name('cities.update');
        Route::delete('{id}', [CityController::class, 'destroy'])->name('cities.destroy');
    });

    // sub-cities route
    Route::prefix('subcities')->group(function () {
        Route::get('/', [SubCityController::class, 'index'])->name('subcities.index');
        Route::get('{id}', [SubCityController::class, 'show'])->name('subcities.show');
        Route::post('/', [SubCityController::class, 'store'])->name('subcities.store');
        Route::put('{id}', [SubCityController::class, 'update'])->name('subcities.update');
        Route::delete('{id}', [SubCityController::class, 'destroy'])->name('subcities.destroy');
        Route::get('city/{cityId}', [SubCityController::class, 'getSubCitiesByCityId'])->name('subcities.byCityId');
    });

    Route::get('/rolesnew', [RolesController::class, 'rolesPermision'])->name('roles.rolesPermision');

    // main equb routes
    Route::prefix('main-equbs')->group(function () {
          Route::get('/', [MainEqubController::class, 'index'])->name('mainEqubs.index');
          Route::get('/types', [MainEqubController::class, 'getTypes'])->name('mainEqubs.types');
          Route::get('{id}', [MainEqubController::class, 'show'])->name('mainEqubs.show');
          Route::put('{id}', [MainEqubController::class, 'update'])->name('mainEqubs.update');
          Route::delete('{id}', [MainEqubController::class, 'destroy'])->name('mainEqubs.destroy');
    });

    Route::middleware(['web','api','auth'])->group(function () {
        // Route to create a new permission
        Route::get('/settings/permission/create', [RolesController::class, 'create_permission'])
            ->name('permissions.create'); // Add permission check
    
        // Route to view all permissions
        Route::get('/settings/permission', [RolesController::class, 'index'])
            ->name('permissions.index'); // Add permission check
    
        // Route to delete a role
        Route::delete('/roles/destroy/{id}', [RolesController::class, 'destroy'])
            ->name('roles.destroy'); // Example permission check
    
        // Route to create a new role
        Route::get('/roles/create', [RolesController::class, 'create'])
            ->name('roles.create'); // Add permission check
    
        // Route to store a new role
        Route::post('/roles/store', [RolesController::class, 'store'])
            ->name('roles.store');
    
        // Route to edit a specific role
        Route::get('/roles/edit/{id}', [RolesController::class, 'edit'])
            ->name('roles.edit');
    
        // Route to update a specific role
        Route::put('/roles/update/{id}', [RolesController::class, 'update'])
            ->name('roles.update');
    });
   
});