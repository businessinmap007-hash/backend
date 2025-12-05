<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\PaymentController;

Route::middleware('auth:api')->group(function () {

    // Charge Account
    Route::post('payment/charge/account', [PaymentController::class, 'chargeAccount']);

    // Transfer
    Route::post('payment/transfer',       [PaymentController::class, 'transferToAnother']);

    // Subscription
    Route::post('payment/subscription',   [PaymentController::class, 'store']);

    // Fawry callbacks
    Route::post('fawry-success-payment',  [PaymentController::class, 'fawrySuccessPayment']);
});
