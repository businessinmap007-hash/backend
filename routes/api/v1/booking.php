<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\BookingController;

Route::middleware('auth:api')->group(function () {

    Route::get('booking/list', [BookingController::class, 'index']);
    Route::post('booking/create', [BookingController::class, 'store']);
    Route::post('booking/{id}/update', [BookingController::class, 'update']);
    Route::delete('booking/{id}/delete', [BookingController::class, 'destroy']);

});
