<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\{
    RegistrationController,
    LoginController,
    ForgotPasswordController,
    ResetPasswordController,
    UsersController
};

// Register
Route::post('register', [RegistrationController::class, 'store']);

// Login
Route::post('login', [LoginController::class, 'login']);

// Social Login
Route::post('social/login', [UsersController::class, 'socialLogin']);

// Forgot / Reset Password
Route::post('password/forgot',        [ForgotPasswordController::class, 'getResetTokens']);
Route::post('password/forgot/resend', [ForgotPasswordController::class, 'resendResetPasswordCode']);
Route::post('password/check',         [ResetPasswordController::class, 'check']);
Route::post('password/reset',         [ResetPasswordController::class, 'reset']);
