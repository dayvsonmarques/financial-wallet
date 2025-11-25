<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\HealthController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\WalletController;
use Illuminate\Support\Facades\Route;

// Health check - sem rate limit
Route::get('/health', [HealthController::class, 'check']);

// Autenticação com rate limiting para prevenir brute force
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register'])
        ->middleware('throttle:login');
    Route::post('/login', [AuthController::class, 'login'])
        ->middleware('throttle:login');
});

// Rotas protegidas com autenticação e rate limiting
Route::middleware(['auth:sanctum', 'throttle:api'])->group(function () {
    Route::prefix('auth')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me']);
    });

    Route::get('/wallet/balance', [WalletController::class, 'balance']);

    Route::prefix('transactions')->group(function () {
        Route::get('/', [TransactionController::class, 'index']);
        Route::get('/{transaction}', [TransactionController::class, 'show']);
        
        // Transferências com rate limiting específico
        Route::post('/transfer', [TransactionController::class, 'transfer'])
            ->middleware('throttle:transfers');
        
        // Depósitos com rate limiting específico
        Route::post('/deposit', [TransactionController::class, 'deposit'])
            ->middleware('throttle:deposits');
        
        // Reversões com rate limiting específico
        Route::post('/{transaction}/reverse', [TransactionController::class, 'reverse'])
            ->middleware('throttle:reversals');
    });
});

