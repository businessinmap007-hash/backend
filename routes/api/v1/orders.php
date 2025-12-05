<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\OrderController;

Route::middleware('auth:api')->group(function () {

    Route::get('orders',               [OrderController::class, 'index']);
    Route::get('orders/{order}',       [OrderController::class, 'show']);
    Route::post('orders/create',       [OrderController::class, 'store']);
    Route::post('orders/{order}/update',[OrderController::class, 'update']);
    Route::delete('orders/{order}/delete',[OrderController::class, 'destroy']);

});
