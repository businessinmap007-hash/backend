<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\WalletController;
use App\Http\Controllers\Api\V1\WalletPinController;

Route::middleware('auth:api')->group(function () {

    // Wallet Operations
    Route::get('wallet/balance', [WalletController::class, 'balance']);
    Route::post('wallet/deposit', [WalletController::class, 'deposit']);
    Route::post('wallet/withdraw', [WalletController::class, 'withdraw']);

    // PIN
    Route::post('wallet/pin/set', [WalletPinController::class, 'setPin']);
    Route::post('wallet/pin/verify', [WalletPinController::class, 'verifyPin']);
});
