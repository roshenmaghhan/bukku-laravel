<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\TransactionController;
use Illuminate\Http\Request; 


Route::post('register', [UserController::class, 'register']);
Route::post('login', [UserController::class, 'login']);
Route::middleware(['auth:api'])->group(function () { // All endpoints aside from register and login, should require auth
    Route::post('purchase', [TransactionController::class, 'recordPurchase']);
    Route::post('sale', [TransactionController::class, 'recordSale']);
    Route::get('purchases', function() {
        return app(TransactionController::class)->getSalesOrPurchases('purchase');
    });
    Route::get('sales', function() {
        return app(TransactionController::class)->getSalesOrPurchases('sale');
    });
    Route::put('transactions/{id}', [TransactionController::class, 'updateTransaction']);
    Route::delete('transactions/{id}', [TransactionController::class, 'deleteTransaction']);
});