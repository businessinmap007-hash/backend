<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\MenuController;
use App\Http\Controllers\Api\V1\MenuCartController;
use App\Http\Controllers\Api\V1\MenuOrderController;

Route::get('categories', [MenuController::class, 'index']);

Route::middleware('auth:api')->group(function () {

    // Menu Items
    Route::post('menu/store', [MenuController::class, 'store']);
    Route::post('menu/{item}/update', [MenuController::class, 'update']);

    // Cart
    Route::post('menucart/add', [MenuCartController::class, 'store']);
    Route::get('menucart',      [MenuCartController::class, 'index']);

    // Orders
    Route::post('menuorder/create', [MenuOrderController::class, 'store']);
    Route::get('menuorder/{id}',    [MenuOrderController::class, 'show']);
});
