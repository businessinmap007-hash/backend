<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\FollowController;
use App\Http\Controllers\Api\V1\TargetController;

Route::middleware('auth:api')->group(function () {

    // Follow Users
    Route::get('user/followers', [FollowController::class, 'index']);
    Route::post('follow/user',   [FollowController::class, 'store']);

    // Follow Categories
    Route::post('follow/categories', [FollowController::class, 'storeCategoryFollow']);

    // Targeting Users
    Route::get('user/targets',    [TargetController::class, 'index']);
    Route::post('target/user',    [TargetController::class, 'store']);
    Route::post('target/categories', [TargetController::class, 'storeTargetCategories']);

    // Collection
    Route::get('collection/ids/users', function () {
        return getTargetsAndFollowersBusiness();
    });
});
