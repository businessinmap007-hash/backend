<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\EscrowController;

Route::middleware('auth:api')->group(function () {

    Route::post('escrow/create', [EscrowController::class, 'create']);
    Route::post('escrow/{escrow}/release', [EscrowController::class, 'release']);
    Route::post('escrow/{escrow}/cancel',  [EscrowController::class, 'cancel']);
    Route::get('escrow/list', [EscrowController::class, 'index']);

});
