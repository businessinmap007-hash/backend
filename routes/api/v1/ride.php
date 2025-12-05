<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\RideController;
use App\Http\Controllers\Api\V1\DriverLocationController;

Route::middleware('auth:api')->group(function () {

    Route::post('ride/request', [RideController::class, 'store']);
    Route::get('ride/list',     [RideController::class, 'index']);

    Route::post('driver/location/update', [DriverLocationController::class, 'updateLocation']);
});
