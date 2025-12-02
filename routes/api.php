<?php

use Illuminate\Support\Facades\Route;
// use Carbon\Carbon; // حالياً غير مستخدم، تقدر تمسحه أو تسيبه لو هتحتاجه

// Controllers (V1)
use App\Http\Controllers\Api\V1\{
    RegistrationController,
    LoginController,
    ForgotPasswordController,
    ResetPasswordController,
    UsersController,
    SettingsController,
    JobController,
    CategoryController,
    ProductsController,
    CommentController,
    PostController,
    LocationController,
    SponsorController,
    BusinessController,
    ProfileController,
    PaymentController,
    ApplyController,
    FollowController,
    TargetController,
    AlbumController,
    ImageController,
    RatesController,
    CategoriesController,
    CouponController,
    TransactionController,
    NotificationController,
    BookingController,
    DeliveryController,
    RideController,
    ChatController,
    CarController,
    MenuItemController,
    MenuItemSizeController,
    MenuItemExtraController,
    CartController,
    OrderController,
    MenuOrderController,
    DriverLocationController
};

/*
|--------------------------------------------------------------------------
| Public APIs (prefix: /v1)
|--------------------------------------------------------------------------
|
| كل المسارات هنا Public لا تحتاج توكن.
|
*/
Route::group(['prefix' => 'v1'], function () {

    // ==========================
    // Auth (Register / Login / Password Reset)
    // ==========================
    Route::post('register', [RegistrationController::class, 'store']);
    Route::post('login',    [LoginController::class, 'login']);

    Route::post('password/forgot',        [ForgotPasswordController::class, 'getResetTokens']);
    Route::post('password/check',         [ResetPasswordController::class,   'check']);
    Route::post('password/reset',         [ResetPasswordController::class,   'reset']);
    Route::post('password/forgot/resend', [ForgotPasswordController::class,  'resendResetPasswordCode']);

    // ==========================
    // General Settings
    // ==========================
    Route::get('general/info',         [SettingsController::class, 'generalInfo']);
    Route::get('general/support',      [SettingsController::class, 'support']);
    Route::get('general/contacts',     [SettingsController::class, 'contacts']);
    Route::get('general/about-app',    [SettingsController::class, 'aboutApp']);
    Route::get('general/social/links', [SettingsController::class, 'socialLinks']);

    // ==========================
    // Public Lists (Jobs / Categories / Products)
    // ==========================
    Route::get('jobs',                        [JobController::class,       'index']);
    Route::get('categories',                  [CategoryController::class,  'index']);
    Route::get('category/{category}/products',[ProductsController::class, 'productsByCategoryId']);

    // ==========================
    // Comments (Public GET)
    // ==========================
    Route::get('comments/{post}/post',     [CommentController::class, 'index']);
    Route::get('comments/{comment}/replies',[CommentController::class, 'commentRepliesList']);

    // ==========================
    // Posts (Public)
    // ==========================
    Route::get('get/posts',          [PostController::class, 'getPosts']);
    Route::get('get/posts/{id}/list',[PostController::class, 'index']);

    // ==========================
    // Locations
    // ==========================
    Route::get('countries',        [LocationController::class, 'countries']);
    Route::get('cities/{location}',[LocationController::class, 'cities']);

    // ==========================
    // Sponsors & Business Listing
    // ==========================
    Route::get('get/paid/sponsors',      [SponsorController::class, 'paidSponsorList']);
    Route::get('get/free/advertisements',[SponsorController::class, 'getFreeAds']);
    Route::post('share/{post}/social',   [PostController::class,   'sharePost']);

    Route::get('category/{category}/business', [BusinessController::class, 'index']);
    Route::get('get/business/list',            [BusinessController::class, 'getBusinessList']);

    // ==========================
    // Payment (Public Callbacks – Simplified)
    // ==========================

    // Fawry success callback
    Route::post('fawry-success-payment', [PaymentController::class, 'fawrySuccessPayment']);

    // CashU success (logic داخل PaymentController@cashuSuccess)
    Route::any('payment/success/cashu', [PaymentController::class, 'cashuSuccess']);

    // CashU error echo back
    Route::any('payment/error/cashu', function (\Illuminate\Http\Request $request) {
        return $request->all();
    });
});
/*
|--------------------------------------------------------------------------
| Authenticated APIs  (prefix: /v1, middleware: auth:api)
|--------------------------------------------------------------------------
|
| كل المسارات هنا محمية بتوكن auth:api
|
*/
Route::group(['prefix' => 'v1', 'middleware' => 'auth:api'], function () {
    // ==========================
    // Profile & Account
    // ==========================
    Route::get('profile',           [ProfileController::class, 'index']);
    Route::post('profile/update',   [ProfileController::class, 'updateProfile']);
    Route::post('user/update/phone',[ProfileController::class, 'updatePhone']);

    // ==========================
    // Notifications
    // ==========================
    Route::get('notifications',          [NotificationController::class, 'index']);
    Route::get('notifications/unread',   [NotificationController::class, 'unread']);
    Route::get('notifications/{id}',     [NotificationController::class, 'show']);
    Route::post('notifications/{id}/read',[NotificationController::class, 'markAsRead']);
    Route::delete('notifications/{id}',  [NotificationController::class, 'destroy']);
    Route::post('notifications',         [NotificationController::class, 'store']);

    // ==========================
    // Cars
    // ==========================
    Route::prefix('cars')->group(function () {
        Route::get('my',   [CarController::class, 'myCars']);
        Route::post('/',   [CarController::class, 'store']);
        Route::get('{id}', [CarController::class, 'show']);
        Route::put('{id}', [CarController::class, 'update']);
        Route::delete('{id}', [CarController::class, 'destroy']);
    });
    // ==========================
    // Menu (Items / Sizes / Extras)
    // ==========================
    Route::prefix('menu')->group(function () {

        // Items
        Route::get('/',                 [MenuItemController::class, 'index']);
        Route::post('item',             [MenuItemController::class, 'store']);
        Route::post('item/{id}/update', [MenuItemController::class, 'update']);
        Route::delete('item/{id}',      [MenuItemController::class, 'delete']);
        Route::get('search',            [MenuItemController::class, 'search']);

        // Sizes
        Route::post('item/{id}/size', [MenuItemSizeController::class, 'store']);
        Route::delete('size/{id}',    [MenuItemSizeController::class, 'delete']);

        // Extras
        Route::post('item/{id}/extra', [MenuItemExtraController::class, 'store']);
        Route::delete('extra/{id}',    [MenuItemExtraController::class, 'delete']);
    });
    // ==========================
    // Menu Cart
    // ==========================
    Route::prefix('menu/cart')
        ->controller(CartController::class)
        ->group(function () {

            // GET /api/v1/menu/cart
            Route::get('/', 'getCart')->name('cart.get');

            // POST /api/v1/menu/cart/items
            Route::post('/items', 'addItem')->name('cart.items.add');

            // PUT /api/v1/menu/cart/items/{item}
            Route::put('/items/{item}', 'updateItem')->name('cart.items.update');

            // DELETE /api/v1/menu/cart/items/{item}
            Route::delete('/items/{item}', 'removeItem')->name('cart.items.remove');

            // DELETE /api/v1/menu/cart
            Route::delete('/', 'clearCart')->name('cart.clear');

            // POST /api/v1/menu/cart/checkout
            Route::post('/checkout', 'checkout')->name('cart.checkout');
        });

            // ==========================
    // MENU ORDERS (from menu/cart)
    // ==========================
    Route::prefix('menu/orders')
        ->controller(MenuOrderController::class)
        ->group(function () {

            // إنشاء طلب من الكارت
            // POST /api/v1/menu/orders/from-cart
            Route::post('from-cart', 'createFromCart');

            // طلبات المستخدم
            // GET /api/v1/menu/orders/my
            Route::get('my', 'myOrders');

            // طلبات البزنس
            // GET /api/v1/menu/orders/business
            Route::get('business', 'businessOrders');

            // تفاصيل طلب
            // GET /api/v1/menu/orders/{id}
            Route::get('{id}', 'show');

            // تحديث حالة الطلب (بزنس / أدمن)
            // POST /api/v1/menu/orders/{id}/status
            Route::post('{id}/status', 'updateStatus');
        });

    /*
    |--------------------------------------------------------------------------
    | Orders (General)
    |--------------------------------------------------------------------------
    */
    Route::prefix('orders')->group(function () {
        Route::post('/',           [OrderController::class, 'store']);
        Route::get('my',           [OrderController::class, 'myOrders']);
        Route::get('business',     [OrderController::class, 'businessOrders']);
        Route::get('{id}',         [OrderController::class, 'show']);
        Route::post('{id}/status', [OrderController::class, 'updateStatus']);
    });

    /*
    |--------------------------------------------------------------------------
    | Delivery Orders
    |--------------------------------------------------------------------------
    */
    Route::prefix('delivery')->group(function () {
        Route::post('orders',           [DeliveryController::class, 'store']);
        Route::get('orders',            [DeliveryController::class, 'myOrders']);
        Route::get('orders/business',   [DeliveryController::class, 'businessOrders']);
        Route::get('orders/driver',     [DeliveryController::class, 'driverOrders']);
        Route::get('orders/{id}',       [DeliveryController::class, 'show']);
        Route::post('orders/{id}/accept',[DeliveryController::class, 'accept']);
        Route::post('orders/{id}/status',[DeliveryController::class, 'updateStatus']);
        Route::delete('orders/{id}',    [DeliveryController::class, 'cancel']);
    });
    /*
    |--------------------------------------------------------------------------
    | Driver Location (Clean Single Version)
    |--------------------------------------------------------------------------
    */
    Route::prefix('driver/location')->group(function () {
        Route::post('update',       [DriverLocationController::class, 'update']);
        Route::get('{driver_id}',   [DriverLocationController::class, 'show']);
    });
    /*
    |--------------------------------------------------------------------------
    | Rides (Uber-like)
    |--------------------------------------------------------------------------
    */
    Route::prefix('rides')->group(function () {
        Route::post('/',           [RideController::class, 'store']);
        Route::get('/',            [RideController::class, 'myRides']);
        Route::get('{id}',         [RideController::class, 'show']);
        Route::delete('{id}',      [RideController::class, 'cancel']);
        Route::post('{id}/status', [RideController::class, 'updateStatus']);
        Route::post('{id}/accept', [RideController::class, 'acceptRide']);
    });

    // رحلات السائق
    Route::get('driver/rides', [RideController::class, 'driverRides']);
    /*
    |--------------------------------------------------------------------------
    | Chat (New Version Only)
    |--------------------------------------------------------------------------
    */
    Route::prefix('chat')->group(function () {
        Route::get('conversations',               [ChatController::class, 'conversations']);
        Route::post('conversations',              [ChatController::class, 'startConversation']);
        Route::get('conversations/{id}/messages', [ChatController::class, 'messages']);
        Route::post('conversations/{id}/messages',[ChatController::class, 'sendMessage']);
        Route::post('conversations/{id}/read',    [ChatController::class, 'markAsRead']);
        Route::delete('conversations/{id}',       [ChatController::class, 'deleteConversation']);
    });
    /*
    |--------------------------------------------------------------------------
    | Booking
    |--------------------------------------------------------------------------
    */
    Route::prefix('booking')->group(function () {
        Route::post('create',        [BookingController::class, 'store']);
        Route::post('update-status', [BookingController::class, 'updateStatus']);
        Route::get('my',             [BookingController::class, 'myBookings']);
        Route::get('business',       [BookingController::class, 'businessBookings']);
    });
    /*
    |--------------------------------------------------------------------------
    | Payments (Internal Actions)
    |--------------------------------------------------------------------------
    */
    Route::post('payment/charge/account', [PaymentController::class, 'chargeAccount']);
    Route::post('payment/subscription',   [PaymentController::class, 'store']);
    Route::post('payment/transfer',       [PaymentController::class, 'transferToAnother']);

});
