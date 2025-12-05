<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\SocialPostController;
use App\Http\Controllers\Api\V1\SocialLikesController;
use App\Http\Controllers\Api\V1\SocialFavoritesController;
use App\Http\Controllers\Api\V1\SocialFollowController;
use App\Http\Controllers\Api\V1\SocialTargetingController;
use App\Http\Controllers\Api\V1\SocialCommentController;
use App\Http\Controllers\Api\V1\SocialMediaUploadController;

Route::middleware('auth:api')->group(function () {

    Route::post('social/post',       [SocialPostController::class, 'store']);
    Route::get('social/posts',       [SocialPostController::class, 'index']);

    Route::post('social/like',       [SocialLikesController::class, 'store']);
    Route::post('social/favorite',   [SocialFavoritesController::class, 'store']);

    Route::post('social/follow',     [SocialFollowController::class, 'store']);
    Route::post('social/target',     [SocialTargetingController::class, 'store']);

    Route::post('social/comment',    [SocialCommentController::class, 'store']);

    Route::post('social/upload',     [SocialMediaUploadController::class, 'upload']);

});
