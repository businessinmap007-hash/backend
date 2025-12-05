<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\DepositController;

Route::middleware('auth:api')->group(function () {

    Route::post('deposit/create', [DepositController::class, 'store']);
    Route::get('deposit/history', [DepositController::class, 'index']);

});
