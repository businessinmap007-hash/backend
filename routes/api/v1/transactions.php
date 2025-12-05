<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\TransactionController;

Route::middleware('auth:api')->group(function () {

    Route::get('transactions',               [TransactionController::class, 'index']);
    Route::get('total/user/balance',         [TransactionController::class, 'getUserBalance']);

});
