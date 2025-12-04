<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\{
    RatesController,
    LikeController
};

Route::prefix('v1')->middleware('auth:sanctum')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Likes System
    |--------------------------------------------------------------------------
    | الإعجابات تخص البوستات فقط
    */

    // عمل لايك للبوست
    Route::post('post/like', [LikeController::class, 'index']);

    /*
    |--------------------------------------------------------------------------
    | Rating System
    |--------------------------------------------------------------------------
    | تقييم المستخدمين / البزنس / الخدمات
    */

    // تقييم عام
    Route::post('rating', [RatesController::class, 'postRating']);

    // تقييم مستخدم
    Route::post('user/rate', [RatesController::class, 'postRate']);

});
