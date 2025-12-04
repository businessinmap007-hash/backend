<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\{
    SettingsController,
    JobController,
    CategoryController,
    ProductsController,
    CommentController,
    PostController,
    LocationController,
    SponsorController,
    BusinessController
};

Route::prefix('v1')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | General Public Information
    |--------------------------------------------------------------------------
    */

    Route::get('general/info',         [SettingsController::class, 'generalInfo']);
    Route::get('general/support',      [SettingsController::class, 'support']);
    Route::get('general/contacts',     [SettingsController::class, 'contacts']);
    Route::get('general/about-app',    [SettingsController::class, 'aboutApp']);
    Route::get('general/social/links', [SettingsController::class, 'socialLinks']);

    /*
    |--------------------------------------------------------------------------
    | Public Categories & Jobs
    |--------------------------------------------------------------------------
    */

    Route::get('jobs',       [JobController::class, 'index']);
    Route::get('categories', [CategoryController::class, 'index']);

    // Products under category
    Route::get('category/{category}/products', 
        [ProductsController::class, 'productsByCategoryId']
    );

    /*
    |--------------------------------------------------------------------------
    | Public Posts
    |--------------------------------------------------------------------------
    */

    Route::get('get/posts',           [PostController::class, 'getPosts']);
    Route::get('get/posts/{id}/list', [PostController::class, 'index']);

    /*
    |--------------------------------------------------------------------------
    | Comments (Public Views Only)
    |--------------------------------------------------------------------------
    */

    Route::get('comments/{post}/post',       [CommentController::class, 'index']);
    Route::get('comments/{comment}/replies', [CommentController::class, 'commentRepliesList']);

    /*
    |--------------------------------------------------------------------------
    | Locations (Countries / Cities)
    |--------------------------------------------------------------------------
    */

    Route::get('countries',         [LocationController::class, 'countries']);
    Route::get('cities/{location}', [LocationController::class, 'cities']);

    /*
    |--------------------------------------------------------------------------
    | Sponsors & Business Public Listing
    |--------------------------------------------------------------------------
    */

    Route::get('get/paid/sponsors',       [SponsorController::class, 'paidSponsorList']);
    Route::get('get/free/advertisements', [SponsorController::class, 'getFreeAds']);

    // Share Post on Social
    Route::post('share/{post}/social', [PostController::class, 'sharePost']);

    // Business public listing
    Route::get('category/{category}/business', [BusinessController::class, 'index']);
    Route::get('get/business/list',            [BusinessController::class, 'getBusinessList']);

});
