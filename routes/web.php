<?php

use App\Http\Controllers\Web\AuthWebController;
use App\Http\Controllers\Web\TransactionWebController;
use App\Http\Controllers\Web\WalletController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
})->name('home');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthWebController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthWebController::class, 'login']);
    Route::get('/register', [AuthWebController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthWebController::class, 'register']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthWebController::class, 'logout'])->name('logout');
    
    Route::get('/dashboard', [WalletController::class, 'dashboard'])->name('dashboard');
    
    Route::get('/transactions', [TransactionWebController::class, 'index'])->name('transactions.index');
    Route::get('/transactions/transfer', [TransactionWebController::class, 'showTransfer'])->name('transactions.transfer');
    Route::post('/transactions/transfer', [TransactionWebController::class, 'transfer']);
    Route::get('/transactions/deposit', [TransactionWebController::class, 'showDeposit'])->name('transactions.deposit');
    Route::post('/transactions/deposit', [TransactionWebController::class, 'deposit']);
    Route::post('/transactions/{transaction}/reverse', [TransactionWebController::class, 'reverse'])->name('transactions.reverse');
});
