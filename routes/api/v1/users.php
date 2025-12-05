<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\UsersController;

Route::middleware('auth:api')->group(function () {

    Route::get('counts/list', [UsersController::class, 'countNotifications']);

    Route::post('user/logout', [UsersController::class, 'logout']);
});
