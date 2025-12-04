<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\{
    PostController,
    CommentController,
    LikeController,
    AlbumController,
    ImageController
};

Route::prefix('v1')->middleware('auth:sanctum')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Posts CRUD
    |--------------------------------------------------------------------------
    */

    Route::post('posts/store',        [PostController::class, 'store']);
    Route::post('posts/{post}/update',[PostController::class, 'update']);
    Route::delete('posts/{post}/delete', [PostController::class, 'delete']);

    // عرض بوستات المستخدم أو غيره (مطلوب من النسخة القديمة)
    Route::get('get/posts',             [PostController::class, 'getPosts']);
    Route::get('get/posts/{id}/list',   [PostController::class, 'index']);

    /*
    |--------------------------------------------------------------------------
    | Likes
    |--------------------------------------------------------------------------
    */

    Route::post('post/like', [LikeController::class, 'index']);

    /*
    |--------------------------------------------------------------------------
    | Comments
    |--------------------------------------------------------------------------
    */

    // إضافة تعليق
    Route::post('comments/store', [CommentController::class, 'store']);

    // جلب تعليقات بوست
    Route::get('comments/{post}/post', [CommentController::class, 'index']);

    // إضافة رد على تعليق
    Route::post('comments/{comment}/replies', [CommentController::class, 'commentReplies']);

    // جلب الردود
    Route::get('comments/{comment}/replies/list', [CommentController::class, 'commentRepliesList']);


    /*
    |--------------------------------------------------------------------------
    | Albums
    |--------------------------------------------------------------------------
    */

    Route::post('albums/store',           [AlbumController::class, 'store']);
    Route::post('albums/{album}/update',  [AlbumController::class, 'update']);
    Route::delete('albums/{album}/destroy',[AlbumController::class, 'destroy']);

    /*
    |--------------------------------------------------------------------------
    | Images Uploading
    |--------------------------------------------------------------------------
    */

    Route::post('upload/multi/images', [ImageController::class, 'store']);
    Route::post('upload/image',        [ImageController::class, 'fileUploader']);

});
