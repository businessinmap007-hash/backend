<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\ImageController;
use App\Http\Controllers\Api\V1\AlbumController;

Route::middleware('auth:api')->group(function () {

    // Images
    Route::post('upload/image',         [ImageController::class, 'fileUploader']);
    Route::post('upload/multi/images',  [ImageController::class, 'store']);

    // Albums
    Route::post('albums/store',            [AlbumController::class, 'store']);
    Route::post('albums/{album}/update',   [AlbumController::class, 'update']);
    Route::delete('albums/{album}/destroy',[AlbumController::class, 'destroy']);
});
