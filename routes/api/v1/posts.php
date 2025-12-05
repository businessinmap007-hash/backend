<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\PostController;
use App\Http\Controllers\Api\V1\CommentController;

Route::get('get/posts',       [PostController::class, 'getPosts']);
Route::get('posts/{id}/list', [PostController::class, 'index']);

Route::get('comments/{post}/post',       [CommentController::class, 'index']);
Route::get('comments/{comment}/replies', [CommentController::class, 'commentRepliesList']);

Route::middleware('auth:api')->group(function () {

    // Posts
    Route::post('posts/store',           [PostController::class, 'store']);
    Route::post('posts/{post}/update',   [PostController::class, 'update']);
    Route::delete('posts/{post}/delete', [PostController::class, 'delete']);
    Route::post('share/{post}/social',   [PostController::class, 'sharePost']);
    Route::get('get/jobs',               [PostController::class, 'getJobs']);

    // Comments
    Route::post('comments/store',            [CommentController::class, 'store']);
    Route::post('comments/{comment}/replies',[CommentController::class, 'commentReplies']);

});
