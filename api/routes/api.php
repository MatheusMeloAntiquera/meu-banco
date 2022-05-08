<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\StoreKeeperController;
use App\Http\Controllers\TransactionController;

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

Route::apiResource('users', UserController::class);
Route::apiResource('storekeepers', StoreKeeperController::class);

Route::prefix('transactions')->group(function () {
    Route::post('/', [TransactionController::class, 'transfToUser'])->name('transactions.toUser');
    Route::post('/store', [TransactionController::class, 'transfToStore'])->name('transactions.toStore');
});
