<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EqubTypeController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PaymentTypeController;
use App\Http\Controllers\EqubController;
use App\Http\Controllers\EqubTakerController;
use App\Http\Controllers\RejectedDateController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\FrontMainEqubController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\CityController;
use App\Http\Controllers\SubCityController;
use App\Http\Controllers\MainEqubController;
use App\Http\Controllers\RolesController;



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
Route::middleware([
    'auth:sanctum',
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

    Route::group(['prefix' => 'equbType'], function () {
        Route::get('/', [EqubTypeController::class, 'index'])->name('showEqubType');
        Route::get('/register', [EqubTypeController::class, 'create'])->name('creatEqubType');
        Route::post('/register', [EqubTypeController::class, 'store'])->name('registerEqubType');
        Route::post('/drawAutoWinners', [EqubTypeController::class, 'drawAutoWinners'])->name('drawAutoWinners');
        Route::get('/edit/{id}', [EqubTypeController::class, 'edit'])->name('editEqubType');
        Route::post('/update/{id}', [EqubTypeController::class, 'update'])->name('updateEqubType');
        Route::put('/deactiveEqubTypeStatus/{id}', [EqubTypeController::class, 'deactiveStatus'])->name('deactiveEqubTypeStatus');
        Route::put('/activeEqubTypeStatus/{id}', [EqubTypeController::class, 'activeStatus'])->name('activeEqubTypeStatus');
        Route::delete('/delete/{id}', [EqubTypeController::class, 'destroy'])->name('deleteEqubType');
        Route::put('/updateStatus/{id}', [EqubTypeController::class, 'updateStatus'])->name('updateStatus');
        Route::put('/updatePendingStatus/{id}/{status}', [EqubTypeController::class, 'updatePendingStatus'])->name('updatePendingStatus');
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
    Route::group(['prefix' => 'payment'], function () {
        Route::post('/defaultPayment.', [PaymentController::class, 'defaultPayment'])->name('defaultPayment');
        Route::get('/{member_id}/{equb_id}', [PaymentController::class, 'index'])->name('showAllPayment');
        Route::get('/show-payment/{member_id}/{equb_id}/{offsetVal}/{pageNumberVal}', [PaymentController::class, 'show'])->name('showPayment');
        Route::get('/show-all-pending-payment', [PaymentController::class, 'indexAll'])->name('showAllPendingPayments');
        Route::get('/show-pending-payment/{offsetVal}/{pageNumberVal}', [PaymentController::class, 'indexPendingPaginate'])->name('showPendingPayments');
        Route::get('/search-pending-payment/{searchInput}/{offset}/{pageNumber?}', [PaymentController::class, 'searchPendingPayment'])->name('searchPendingMember');
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
    Route::group(['prefix' => 'rejectedDate'], function () {
        Route::get('/', [RejectedDateController::class, 'index'])->name('showRejectedDate');
        Route::get('/register', [RejectedDateController::class, 'create'])->name('creatRejectedDate');
        Route::post('/register', [RejectedDateController::class, 'store'])->name('registerRejectedDate');
        Route::get('/edit/{id}', [RejectedDateController::class, 'edit'])->name('editRejectedDate');
        Route::put('/update/{id}', [RejectedDateController::class, 'update'])->name('updateRejectedDate');
        Route::delete('/delete/{id}', [RejectedDateController::class, 'destroy'])->name('deleteRejectedDate');
    });
    Route::group(['prefix' => 'notification'], function () {
        Route::get('/', [NotificationController::class, 'index'])->name('showNotifations');
        Route::get('/create', [NotificationController::class, 'create'])->name('creatNotifation');
        Route::post('/store', [NotificationController::class, 'store'])->name('sendNotifation');
        Route::post('/sendIndividualNotifation', [NotificationController::class, 'sendToIndividual'])->name('sendIndividualNotifation');
        Route::get('/edit/{id}', [NotificationController::class, 'edit'])->name('editNotifation');
        Route::put('/update/{id}', [NotificationController::class, 'update'])->name('resendNotifation');
        Route::put('/updatePending/{id}', [NotificationController::class, 'updatePending'])->name('updatePending');
        Route::delete('/delete/{id}', [NotificationController::class, 'destroy'])->name('deleteNotifation');
        Route::post('/approve/{id}', [NotificationController::class, 'approve'])->name('approveNotifation');
    });
    Route::prefix('user')->middleware('auth')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('user');
        Route::get('/user/{offsetVal}/{pageNumberVal}', [UserController::class, 'user']);
        Route::get('/search-user/{searchInput}/{offset}/{pageNumber?}', [UserController::class, 'searchUser'])->name('searchUser');
        Route::get('/deactiveUser/{offsetVal}/{pageNumberVal}', [UserController::class, 'deactiveUser']);
        Route::post('/store-user', [UserController::class, 'store'])->name('registerUser');
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
        Route::get('/paginateEqubs/{dateFrom}/{dateTo}/{offsetVal}/{pageNumberVal}/{equbType}', [ReportController::class, 'paginateEqubs']);

        Route::get('/lotteryDateFilter', [ReportController::class, 'lotteryDateFilter'])->name('lotteryDateFilter');
        Route::get('/lotteryDate/{dateFrom}/{dateTo}', [ReportController::class, 'lotteryDate'])->name('lotteryDate');
        Route::get('/paginateLotteryDate/{dateFrom}/{dateTo}/{offsetVal}/{pageNumberVal}', [ReportController::class, 'paginateLotteryDate']);

        Route::get('/lotteryFilter', [ReportController::class, 'lotteryFilter'])->name('lotteryFilter');
        Route::get('/lotterys/{dateFrom}/{dateTo}/{member_id}/{equb_type_id}', [ReportController::class, 'lotterys'])->name('lotterys');
        Route::get('/paginateLotterys/{dateFrom}/{dateTo}/{member_id}/{equb_type_id}/{offsetVal}/{pageNumberVal}', [ReportController::class, 'paginateLotterys']);

        Route::get('/unPaidLotteryFilter', [ReportController::class, 'unPaidLotteryFilter'])->name('unPaidLotteryFilter');
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
        Route::get('/paginateCllectedBys/{dateFrom}/{dateTo}/{collecter}/{offsetVal}/{pageNumberVal}/{equbType}', [ReportController::class, 'paginateCllectedBys']);
        Route::get('/collectedByFilter', [ReportController::class, 'collectedByFilter'])->name('collectedByFilter');
        Route::get('/collectedBys/{dateFrom}/{dateTo}/{collecter}/{equbType}', [ReportController::class, 'collectedBys'])->name('collectedBys');

        Route::get('/unPaidFilter', [ReportController::class, 'unPaidFilter'])->name('unPaidFilter');
        Route::get('/unPaids/{dateFrom}/{dateTo}/{equbType}', [ReportController::class, 'unPaids'])->name('unPaids');
        Route::get('/loadMoreUnPaids/{dateFrom}/{dateTo}/{offsetVal}/{pageNumberVal}/{equbType}', [ReportController::class, 'loadMoreUnPaids']);

        Route::get('/filterEqubEndDates', [ReportController::class, 'equbEndDates'])->name('unPaidFilter');
        Route::get('/filterEqubEndDates/{dateFrom}/{dateTo}/{equbType}', [ReportController::class, 'filterEqubEndDates'])->name('filterEqubEndDates');
        Route::get('/loadMoreFilterEqubEndDates/{dateFrom}/{dateTo}/{offsetVal}/{pageNumberVal}/{equbType}', [ReportController::class, 'loadMoreFilterEqubEndDates']);
    });
    Route::group(['prefix' => 'activityLog'], function () {
        Route::get('/', [ActivityLogController::class, 'index'])->name('showActivityLog');
        Route::get('/activityLog/{offsetVal}/{pageNumberVal}', [ActivityLogController::class, 'paginateActivityLog']);
        Route::get('/logDetail/{type}/{searchInput?}', [ActivityLogController::class, 'logDetail'])->name('logDetail');
        Route::get('/logDetailPaginate/{type}/{offsetVal}/{pageNumberVal}', [ActivityLogController::class, 'logDetailPaginate'])->name('logDetailPaginate');
        Route::get('/search-activity/{type}/{searchInput}/{offset}/{pageNumber?}', [ActivityLogController::class, 'searchActivity'])->name('searchActivity');
        Route::get('/clearSearchEntry', [ActivityLogController::class, 'clearSearchEntry'])->name('clearSearchEntry');
    });
    Route::prefix('cities')->group(function () {
        // Get all cities
        
        Route::get('/', [CityController::class, 'index'])->name('cities.index');
            // Create a new city
            Route::get('/create', [CityController::class, 'create'])->name('admin.city.addCity');
            Route::post('/', [CityController::class, 'store'])->name('cities.store');
        // Get a city by ID
        Route::get('{id}', [CityController::class, 'show'])->name('cities.show');
    
        // Create a new city
        Route::post('/', [CityController::class, 'store'])->name('cities.store');
    
        // Update an existing city
        Route::put('{id}', [CityController::class, 'update'])->name('cities.update');
    
        // Delete a city
        Route::delete('{id}', [CityController::class, 'destroy'])->name('cities.destroy');
    });
    Route::prefix('subcities')->group(function () {
        // Get all sub-cities
        Route::get('/', [SubCityController::class, 'index'])->name('subcities.index');
    
        // Get a sub-city by ID
        Route::get('{id}', [SubCityController::class, 'show'])->name('subcities.show');
    
        // Create a new sub-city
        Route::post('/', [SubCityController::class, 'store'])->name('subcities.store');
    
        // Update an existing sub-city
        Route::put('{id}', [SubCityController::class, 'update'])->name('subcities.update');
    
        // Delete a sub-city
        Route::delete('{id}', [SubCityController::class, 'destroy'])->name('subcities.destroy');
    
        // Get sub-cities by city ID
        Route::get('city/{cityId}', [SubCityController::class, 'getSubCitiesByCityId'])->name('subcities.byCityId');
        
    });
    Route::prefix('main-equbs')->group(function () {
        // Get all cities
          Route::get('/', [MainEqubController::class, 'index'])->name('mainEqubs.index');
          // Get types of equbs
          Route::get('/types', [MainEqubController::class, 'getTypes'])->name('mainEqubs.types');
    
          // Create a new main equb
          Route::post('/', [MainEqubController::class, 'store'])->name('mainEqubs.store');
      
          // Get a main equb by ID
          Route::get('{id}', [MainEqubController::class, 'show'])->name('mainEqubs.show');
      
          // Update an existing main equb
          Route::put('{id}', [MainEqubController::class, 'update'])->name('mainEqubs.update');
        
          // Delete a main equb
          Route::delete('{id}', [MainEqubController::class, 'delete'])->name('mainEqubs.destroy');
    });
   
});