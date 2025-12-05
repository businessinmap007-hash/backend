<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\ProfileController;
use App\Http\Controllers\Api\V1\UsersController;

Route::middleware('auth:api')->group(function () {

    Route::get('profile',                     [ProfileController::class, 'index']);
    Route::get('profile/information',         [ProfileController::class, 'getProfileInformation']);

    Route::post('profile/update',             [ProfileController::class, 'updateProfile']);
    Route::post('user/update/phone',          [ProfileController::class, 'updatePhone']);
    Route::post('profile/update/lang',        [UsersController::class, 'profileUpdateLang']);
    Route::post('profile/update/notification',[UsersController::class, 'profileUpdateNotification']);

    Route::post('password/change',            [UsersController::class, 'changePassword']);
    Route::post('user/delete',                [UsersController::class, 'deleteUser']);

});
