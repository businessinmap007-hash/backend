<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\CarController;

Route::middleware('auth:api')->group(function () {

    Route::get('car/list', [CarController::class, 'index']);
    Route::post('car/store', [CarController::class, 'store']);
    Route::post('car/{car}/update', [CarController::class, 'update']);
    Route::delete('car/{car}/delete', [CarController::class, 'destroy']);

});
