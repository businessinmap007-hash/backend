<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\CartController;

Route::middleware('auth:api')->group(function () {

    Route::get('cart', [CartController::class, 'index']);
    Route::post('cart/add', [CartController::class, 'store']);
    Route::post('cart/update', [CartController::class, 'update']);
    Route::delete('cart/delete', [CartController::class, 'delete']);

});
