<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\{
    ProfileController,
    UsersController,
    NotificationController,
    RatesController
};

Route::prefix('v1')->middleware('auth:sanctum')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Profile Information
    |--------------------------------------------------------------------------
    */

    Route::get('profile',            [ProfileController::class, 'index']);
    Route::post('profile/update',    [ProfileController::class, 'updateProfile']);
    Route::post('user/update/phone', [ProfileController::class, 'updatePhone']);

    /*
    |--------------------------------------------------------------------------
    | Language / Settings / Logout
    |--------------------------------------------------------------------------
    */

    Route::post('profile/update/lang',         [UsersController::class, 'profileUpdateLang']);
    Route::post('profile/update/notification', [UsersController::class, 'profileUpdateNotification']);
    Route::post('update/language',             [ProfileController::class, 'updateLanguage']);

    // Logout
    Route::post('user/logout', [ProfileController::class, 'logout']);

    /*
    |--------------------------------------------------------------------------
    | Ratings
    |--------------------------------------------------------------------------
    */

    Route::post('user/rate', [RatesController::class, 'postRate']);
    Route::post('rating',    [RatesController::class, 'postRating']); // old route compatibility

    /*
    |--------------------------------------------------------------------------
    | Notifications
    |--------------------------------------------------------------------------
    */

    Route::get('notifications',          [NotificationController::class, 'index']);
    Route::get('notifications/unread',   [NotificationController::class, 'unread']);
    Route::get('notifications/count',    [NotificationController::class, 'countForUser']);
    Route::get('notifications/{id}',     [NotificationController::class, 'show']);
    Route::post('notifications/{id}/read', [NotificationController::class, 'markAsRead']);
    Route::delete('notifications/{id}',  [NotificationController::class, 'destroy']);

    /*
    |--------------------------------------------------------------------------
    | Account Management
    |--------------------------------------------------------------------------
    */

    Route::post('password/change', [UsersController::class, 'changePassword']);
    Route::post('user/delete',     [UsersController::class, 'deleteUser']);    

    /*
    |--------------------------------------------------------------------------
    | Extra User Stats
    |--------------------------------------------------------------------------
    */

    // counters (notifications, messages, etc)
    Route::get('counts/list', [UsersController::class, 'countNotifications']);

});
