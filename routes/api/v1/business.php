<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\BusinessController;

Route::get('business/list', [BusinessController::class, 'getBusinessList']);
Route::get('category/{category}/business', [BusinessController::class, 'index']);
