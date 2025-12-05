<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\DeliveryController;
use App\Http\Controllers\Api\V1\DeliveryDriverController;
use App\Http\Controllers\Api\V1\DeliveryOrderController;

Route::middleware('auth:api')->group(function () {

    Route::get('delivery/list', [DeliveryController::class, 'index']);
    Route::post('delivery/order/create', [DeliveryOrderController::class, 'store']);
    Route::post('delivery/order/{id}/update', [DeliveryOrderController::class, 'update']);
    Route::get('delivery/order/{id}', [DeliveryOrderController::class, 'show']);

    Route::post('delivery/driver/location', [DeliveryDriverController::class, 'updateLocation']);
});
