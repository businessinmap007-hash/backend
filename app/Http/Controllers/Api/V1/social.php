<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\Social\{
    SocialPostController,
    SocialCommentController,
    SocialLikeController,
    SocialFollowController,
    SocialTargetingController,
    SocialAlbumController,
    SocialMediaUploadController
};

/*
|--------------------------------------------------------------------------
| SOCIAL MODULE — PUBLIC ROUTES
|--------------------------------------------------------------------------
|
| هذه المسارات لا تحتاج تسجيل دخول
|
*/

Route::prefix('v1/social')->group(function () {

    // Posts (Public GET only)
    Route::get('posts',               [SocialPostController::class, 'getPosts']);
    Route::get('posts/{id}',          [SocialPostController::class, 'index']);

    // Comments (public read)
    Route::get('comments/{post}/post',        [SocialCommentController::class, 'index']);
    Route::get('comments/{comment}/replies',  [SocialCommentController::class, 'commentRepliesList']);

    // Sponsors Ads (كانت موجودة داخل Social)
    Route::get('sponsors/free',     [\App\Http\Controllers\Api\V1\SponsorController::class, 'getFreeAds']);
    Route::get('sponsors/paid',     [\App\Http\Controllers\Api\V1\SponsorController::class, 'paidSponsorList']);

});

/*
|--------------------------------------------------------------------------
| SOCIAL MODULE — AUTHENTICATED ROUTES
|--------------------------------------------------------------------------
|
| أي عمليات إنشاء / تعديل / تفاعل تحتاج توكن
|
*/

Route::prefix('v1/social')->middleware('auth:sanctum')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Posts
    |--------------------------------------------------------------------------
    */
    Route::post('posts',                [SocialPostController::class, 'store']);
    Route::post('posts/{post}/update',  [SocialPostController::class, 'update']);
    Route::delete('posts/{post}',       [SocialPostController::class, 'delete']);
    Route::post('posts/{post}/share',   [SocialPostController::class, 'sharePost']);

    /*
    |--------------------------------------------------------------------------
    | Comments
    |--------------------------------------------------------------------------
    */
    Route::post('comments',                     [SocialCommentController::class, 'store']);
    Route::post('comments/{comment}/reply',     [SocialCommentController::class, 'commentReplies']);

    /*
    |--------------------------------------------------------------------------
    | Likes
    |--------------------------------------------------------------------------
    */
    Route::post('posts/like', [SocialLikeController::class, 'index']);

    /*
    |--------------------------------------------------------------------------
    | Follow System
    |--------------------------------------------------------------------------
    */
    Route::get('followings',         [SocialFollowController::class, 'index']);
    Route::post('follow/user',       [SocialFollowController::class, 'store']);
    Route::post('follow/categories', [SocialFollowController::class, 'storeCategoryFollow']);

    /*
    |--------------------------------------------------------------------------
    | Targeting System
    |--------------------------------------------------------------------------
    */
    Route::get('targets',           [SocialTargetingController::class, 'index']);
    Route::post('targets',          [SocialTargetingController::class, 'store']);
    Route::post('targets/categories', [SocialTargetingController::class, 'storeTargetCategories']);

    /*
    |--------------------------------------------------------------------------
    | Albums & Images
    |--------------------------------------------------------------------------
    */
    Route::post('albums',                    [SocialAlbumController::class, 'store']);
    Route::post('albums/{album}/update',     [SocialAlbumController::class, 'update']);
    Route::delete('albums/{album}',          [SocialAlbumController::class, 'destroy']);

    Route::post('images/upload-multiple',   [SocialMediaUploadController::class, 'store']);
    Route::post('images/upload-single',     [SocialMediaUploadController::class, 'fileUploader']);
});
