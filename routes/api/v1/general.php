<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\SettingsController;

Route::get('general/info',          [SettingsController::class, 'generalInfo']);
Route::get('general/support',       [SettingsController::class, 'support']);
Route::get('general/contacts',      [SettingsController::class, 'contacts']);
Route::get('general/about-app',     [SettingsController::class, 'aboutApp']);
Route::get('general/social/links',  [SettingsController::class, 'socialLinks']);

Route::middleware('auth:api')->group(function () {
    Route::post('contact-us',          [SettingsController::class, 'postMessage']);
    Route::post('support/post/message',[SettingsController::class, 'postMessage']);
});
