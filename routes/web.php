<?php

use Illuminate\Support\Facades\{
    Route,
    Auth
};
use App\Http\Controllers\{
    ActivityController,
    HomeController,
    JillaController,
    MedicineController,
    TalukaController,
    UserController,
    GramController,
    VibhagController,
    GramjuthController,
    MedicineRequestController,
    CsvController,
    PrantController,
    ReportsController,
    Controller,
    MedicineOrderController,
    BeneficiariesController,
    MedicineStockController,
    StockiestMedicineRequestController,
    UserExportController,
    StockReportController,
    OrderPdfController
};

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return redirect('/login');
});

Auth::routes();
Route::get('/privacy-policy-arogya-mitra', [Controller::class, 'privacyPolicy'])->name('privacyPolicy');

Route::group(['middleware' => ['auth']], function () {
    Route::get('/home', [HomeController::class, 'index'])->name('home');
    //Route::get('/privacy-policy-arogya-mitra', [HomeController::class, 'privacyPolicy'])->name('privacyPolicy');
    Route::get('/import', [CsvController::class, 'show'])->name('show');
    Route::post('/importCsv', [CsvController::class, 'importCsv'])->name('importCsv');
    Route::get('profile', [UserController::class, 'profile'])->name('profile.show');
    Route::get('password', [UserController::class, 'password'])->name('password.create');
    Route::post('change-password', [UserController::class, 'storePassword'])->name('password.change');
    Route::get('vibhag-list', [UserController::class, 'getVibhagList']);
    Route::get('jilla-list', [UserController::class, 'getJillaList']);
    Route::get('taluka-list', [UserController::class, 'getTalukaList']);
    Route::get('gramjuth-list', [UserController::class, 'getGramjuthList']);
    Route::get('gram-list', [UserController::class, 'getGramList']);
    Route::get('jilla-gram-list', [UserController::class, 'getJillaGramList']);
    Route::post('check-email', [UserController::class, 'checkExistsEmail']);
    Route::post('check-mobile', [UserController::class, 'checkExistsMobile']);
    Route::post('change-prant-status', [PrantController::class, 'changePrantStatus']);
    Route::post('change-vibhag-status', [VibhagController::class, 'changeVibhagStatus']);
    Route::post('change-jilla-status', [JillaController::class, 'changeDistrictStatus']);
    Route::post('change-taluka-status', [TalukaController::class, 'changeSubDistrictStatus']);
    Route::post('change-gramjuth-status', [GramjuthController::class, 'changeGramjuthStatus']);
    Route::post('change-gram-status', [GramController::class, 'changeGramStatus']);
    Route::post('delete-prant', [PrantController::class, 'deletePrant']);
    Route::post('delete-vibhag', [VibhagController::class, 'deleteVibhag']);
    Route::post('delete-jilla', [JillaController::class, 'deleteJilla']);
    Route::post('delete-taluka', [TalukaController::class, 'deleteTaluka']);
    Route::post('delete-gramjuth', [GramjuthController::class, 'deleteGramjuth']);
    Route::post('delete-gram', [GramController::class, 'deleteGram']);
    Route::post('change-user-status', [UserController::class, 'changeUserStatus']);
    Route::resource('prant', PrantController::class);
    Route::resource('vibhag', VibhagController::class);
    Route::resource('jilla', JillaController::class);
    Route::resource('taluka', TalukaController::class);
    Route::resource('gramjuth', GramjuthController::class);
    Route::resource('grams', GramController::class);

    Route::any('users', [UserController::class, 'index'])->name('users.index');

    Route::get('users/create', [UserController::class, 'create'])->name('users.create');
    Route::post('users/store', [UserController::class, 'store'])->name('users.store');
    Route::get('users/{id}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::post('users/update', [UserController::class, 'update'])->name('users.update');
    Route::get('users/{id}/show', [UserController::class, 'show'])->name('users.show');
    Route::post('users/{id}/delete', [UserController::class, 'destroy'])->name('users.destroy');

    Route::resource('medicines', MedicineController::class);
    Route::get('report/medicines-stock', [ReportsController::class, 'index'])->name('report.medicines.index');
    Route::post('report/medicines-stock', [ReportsController::class, 'index'])->name('report.medicines');
    Route::get('report/order/medicines-request', [MedicineOrderController::class, 'index'])->name('order.medicines.index');
    Route::post('report/order/medicines-request', [MedicineOrderController::class, 'index'])->name('order.medicines');
    Route::put('/medicines/{id}/updateStatus', [MedicineController::class, 'updateStatus'])->name('medicines.updateStatus');
    Route::post('/medicine-request/delivered/flag/update', [MedicineRequestController::class, 'delivered_flag_update'])->name('medicineRequest.delivered_flag_update');

    Route::get('active-log', [ActivityController::class, 'index'])->name('activeLog.index');

    Route::any('report/beneficiaries', [BeneficiariesController::class, 'index'])->name('report.beneficiaries');

    Route::any('medicine-stock', [MedicineStockController::class, 'index'])->name('medicineStock.index');
    Route::post('medicine-stock/store', [MedicineStockController::class, 'store'])->name('medicine-stock.store');
    Route::get('medicineRequest/export/{status}', [MedicineRequestController::class, 'export'])
        ->name('medicineRequest.export');
    Route::post('medicineRequest/deliver', [MedicineRequestController::class, 'deliver'])
        ->name('medicineRequest.deliver');


    /* App User Medicine Request */
    /* Route::get('change-status', [MedicineRequestController::class, 'updateStatus']);
    Route::get('medicineRequest', [MedicineRequestController::class, 'index'])->name('medicineRequest.index');
    Route::post('medicineRequest', [MedicineRequestController::class, 'index'])->name('medicineRequest.status');
    Route::POST('/medicineRequest/{id}/updateStatus', [MedicineRequestController::class, 'updateRequestStatus'])->name('medicineRequest.updateRequestStatus'); */

    /* Stockiest Medicine Request */
    Route::get('change-status', [StockiestMedicineRequestController::class, 'updateStatus']);
    Route::any('medicineRequest', [StockiestMedicineRequestController::class, 'index'])->name('medicineRequest.index');
    Route::post('medicineRequest', [StockiestMedicineRequestController::class, 'index'])->name('medicineRequest.status');
    Route::post('/medicineRequest/{id}/updateStatus', [StockiestMedicineRequestController::class, 'updateRequestStatus'])->name('medicineRequest.updateRequestStatus');
    Route::get('medicineReqReport', [StockiestMedicineRequestController::class, 'medicineReqReport'])->name('medicineReqReport');

    Route::get('accept-stock', [StockiestMedicineRequestController::class, 'acceptStock']);
    Route::get('export', [UserExportController::class, 'export'])->name('user-export');
    Route::get('report/stock-report', [StockReportController::class, 'index'])->name('report.backend');
    Route::get('report/stock-report-show/{id}/show', [StockReportController::class, 'show'])->name('report.show');
    Route::any('report/stockiest-stock-report', [StockReportController::class, 'stockiestStock'])->name('report.stockiest');
    Route::any('report/stockiest-report-show/{id}/{stockiest}/show', [StockReportController::class, 'stockiestShow'])->name('report.stockiest-show');
    Route::any('report/stock-report-appuser', [StockReportController::class, 'appUsersReport'])->name('report.appUsers');
    Route::any('report/appUsers-report-show/{id}/{appUser}/show', [StockReportController::class, 'appUsersMedicineTrack'])->name('report.appUser-show');
    Route::get('user-gram-list', [StockReportController::class, 'getUserGramList']);

    Route::get('/order-patrak-pdf/{id}', [OrderPdfController::class, 'generateOrderPatrak'])->name('generateOrderPatrak');
});
