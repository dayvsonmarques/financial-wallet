<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\WalletController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('auth')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me']);
    });

    Route::get('/wallet/balance', [WalletController::class, 'balance']);

    Route::prefix('transactions')->group(function () {
        Route::get('/', [TransactionController::class, 'index']);
        Route::post('/transfer', [TransactionController::class, 'transfer']);
        Route::post('/deposit', [TransactionController::class, 'deposit']);
        Route::post('/{transaction}/reverse', [TransactionController::class, 'reverse']);
        Route::get('/{transaction}', [TransactionController::class, 'show']);
    });
});

