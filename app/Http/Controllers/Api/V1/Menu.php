<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\{
    MenuItemController,
    MenuItemSizeController,
    MenuItemExtraController,
    CartController,
    MenuOrderController
};

/*
|--------------------------------------------------------------------------
| MENU MODULE — PUBLIC ROUTES
|--------------------------------------------------------------------------
| تظهر للجميع (أي مستخدم مسجّل فقط)
|--------------------------------------------------------------------------
*/

Route::prefix('v1/menu')->middleware('auth:sanctum')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Items — Public Read Only
    |--------------------------------------------------------------------------
    */
    Route::get('/',            [MenuItemController::class, 'index']);
    Route::get('/search',      [MenuItemController::class, 'search']);

    /*
    |--------------------------------------------------------------------------
    | Business Panel — Only business accounts can add/edit items
    |--------------------------------------------------------------------------
    */
    Route::middleware('business')->group(function () {

        // ------------------------
        // Menu Items CRUD
        // ------------------------
        Route::post('/item',               [MenuItemController::class, 'store']);
        Route::post('/item/{id}/update',   [MenuItemController::class, 'update']);
        Route::delete('/item/{id}',        [MenuItemController::class, 'delete']);

        // ------------------------
        // Sizes
        // ------------------------
        Route::post('/item/{id}/size',     [MenuItemSizeController::class, 'store']);
        Route::delete('/size/{id}',        [MenuItemSizeController::class, 'delete']);

        // ------------------------
        // Extras
        // ------------------------
        Route::post('/item/{id}/extra',    [MenuItemExtraController::class, 'store']);
        Route::delete('/extra/{id}',       [MenuItemExtraController::class, 'delete']);
    });


    /*
    |--------------------------------------------------------------------------
    | MENU CART
    |--------------------------------------------------------------------------
    */
    Route::prefix('cart')->controller(CartController::class)->group(function () {

        Route::get('/',                 'getCart')->name('cart.get');
        Route::post('/items',           'addItem')->name('cart.items.add');
        Route::put('/items/{item}',     'updateItem')->name('cart.items.update');
        Route::delete('/items/{item}',  'removeItem')->name('cart.items.remove');
        Route::delete('/',              'clearCart')->name('cart.clear');

        // Checkout
        Route::post('/checkout',        'checkout')->name('cart.checkout');
    });


    /*
    |--------------------------------------------------------------------------
    | MENU ORDERS
    |--------------------------------------------------------------------------
    */
    Route::prefix('orders')->controller(MenuOrderController::class)->group(function () {

        // User فقط ينشئ طلب من الكارت
        Route::post('/from-cart', 'createFromCart');

        // طلباته فقط
        Route::get('/my', 'myOrders');

        // عرض طلب
        Route::get('/{id}', 'show');

        /*
        |--------------------------------------------------------------------------
        | BUSINESS OPERATIONS
        |--------------------------------------------------------------------------
        */
        Route::middleware('business')->group(function () {

            Route::get('/business', 'businessOrders');
            Route::post('/{id}/status', 'updateStatus');
        });
    });
});
