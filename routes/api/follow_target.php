<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\{
    FollowController,
    TargetController
};

Route::prefix('v1')->middleware('auth:sanctum')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Follow System
    |--------------------------------------------------------------------------
    |
    | متابعة المستخدمين + متابعة الفئات
    |
    */

    // متابعة مستخدم
    Route::post('follow/user', [FollowController::class, 'store']);

    // متابعة تصنيفات
    Route::post('follow/categories', [FollowController::class, 'storeCategoryFollow']);

    // عرض متابعيني
    Route::get('user/followers', [FollowController::class, 'index']);

    // عرض من أتابعهم (Targets)
    Route::get('user/targets', [TargetController::class, 'index']);

    /*
    |--------------------------------------------------------------------------
    | Targeting System (Users / Categories)
    |--------------------------------------------------------------------------
    */

    // استهداف مستخدم
    Route::post('target/user', [TargetController::class, 'store']);

    // استهداف تصنيفات
    Route::post('target/categories', [TargetController::class, 'storeTargetCategories']);

});
