<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\{
    LoginController,
    StockController,
    StockiestController,
    SyncController
};

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('login', [LoginController::class, 'login']);
Route::post('logout', [LoginController::class, 'logout']);
Route::middleware(['auth:api'])->group(function () {
    Route::post('stock_update', [StockController::class, 'updateStockDetailsByType']);
    Route::post('list', [StockController::class, 'getStockList']);
    Route::post('village-list', [StockController::class, 'getVillageList']);
    Route::post('arogya-mitra-list', [StockController::class, 'getArogyaMitraList']);
    Route::post('syn-data', [SyncController::class, 'syncData']);
    Route::post('export', [StockController::class, 'exportArogyaMitraList']);

    /* Stockiest */
    Route::post('app-user', [StockiestController::class, 'getAppUser']);
    Route::post('medicine-request', [StockiestController::class, 'getMedicineRequest']);
    Route::post('medicine-delivered', [StockiestController::class, 'getmedicinedelivered']);
    Route::post('reject-Medicine-Request', [StockiestController::class, 'rejectMedicineRequest']);

    Route::post('receive-stock', [StockiestController::class, 'receivestock']);
    Route::post('used-note', [StockiestController::class, 'usednote']);

    Route::post('beneficiaries-add', [StockiestController::class, 'beneficiariesadd']);

    Route::post('stockiest-stock-update', [StockiestController::class, 'updateStock']);
    Route::post('request-stock', [StockiestController::class, 'getRequestStock']);
    Route::post('stockiest-sync-data', [StockiestController::class, 'syncData']);
});
//Route::post('syn-data', [SyncController::class, 'syncData']);
