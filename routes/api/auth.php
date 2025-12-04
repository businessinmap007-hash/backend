<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\{
    RegistrationController,
    LoginController,
    ForgotPasswordController,
    ResetPasswordController,
    UsersController
};

Route::prefix('v1')->group(function () {

    // Registration
    Route::post('register', [RegistrationController::class, 'store']);

    // Login
    Route::post('login', [LoginController::class, 'login']);

    // Social Login (من النسخة القديمة)
    Route::post('social/login', [UsersController::class, 'socialLogin']);

    // Forgot Password
    Route::post('password/forgot', [ForgotPasswordController::class, 'getResetTokens']);

    // Check code
    Route::post('password/check', [ResetPasswordController::class, 'check']);

    // Reset password
    Route::post('password/reset', [ResetPasswordController::class, 'reset']);

    // Resend reset code
    Route::post('password/forgot/resend', [ForgotPasswordController::class, 'resendResetPasswordCode']);
});
