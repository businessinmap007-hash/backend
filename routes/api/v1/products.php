<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\ProductsController;

Route::get('/categories', [ProductsController::class, 'index']);
Route::get('/category/{category}/products', [ProductsController::class, 'productsByCategoryId']);
Route::get('get/business/list', [ProductsController::class, 'getBusinessList']);
