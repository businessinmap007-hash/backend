<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\LikeController;
use App\Http\Controllers\Api\V1\RatesController;

Route::middleware('auth:api')->group(function () {

    // Likes
    Route::post('post/like', [LikeController::class, 'index']);

    // Ratings
    Route::post('user/rate', [RatesController::class, 'postRate']);
    Route::post('rating',    [RatesController::class, 'postRating']);
});
