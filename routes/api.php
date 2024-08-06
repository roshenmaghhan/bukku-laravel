<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\TransactionController;

Route::post('register', [UserController::class, 'register']);
Route::post('login', [UserController::class, 'login']);
Route::middleware('auth:api')->group(function () { // All endpoints aside from register and login, should require auth
    Route::post('purchase', [TransactionController::class, 'recordPurchase']);
    Route::post('sale', [TransactionController::class, 'recordSale']);
    Route::get('purchases', [TransactionController::class, 'getPurchases']);
    Route::get('sales', [TransactionController::class, 'getSales']);
    Route::put('transactions/{id}', [TransactionController::class, 'updateTransaction']);
    Route::delete('transactions/{id}', [TransactionController::class, 'deleteTransaction']);
});