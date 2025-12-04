<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

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
    DriverLocationController,
    DepositController
};

/*
|--------------------------------------------------------------------------
| API v1
|--------------------------------------------------------------------------
|
| جميع مسارات نسخة الـ API v1 تحت prefix واحد: /api/v1
| - جزء Public (لا يحتاج توكن)
| - جزء Authenticated (auth:sanctum)
|   - داخله مسارات خاصة بالبزنس فقط (middleware: business)
|
*/

Route::prefix('v1')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Public APIs (لا تحتاج توكن)
    |--------------------------------------------------------------------------
    */

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
    Route::get('jobs',                         [JobController::class,       'index']);
    Route::get('categories',                   [CategoryController::class,  'index']);
    Route::get('category/{category}/products', [ProductsController::class,  'productsByCategoryId']);

    // ==========================
    // Comments (Public GET)
    // ==========================
    Route::get('comments/{post}/post',       [CommentController::class, 'index']);
    Route::get('comments/{comment}/replies', [CommentController::class, 'commentRepliesList']);

    // ==========================
    // Posts (Public)
    // ==========================
    Route::get('get/posts',           [PostController::class, 'getPosts']);
    Route::get('get/posts/{id}/list', [PostController::class, 'index']);

    // ==========================
    // Locations
    // ==========================
    Route::get('countries',         [LocationController::class, 'countries']);
    Route::get('cities/{location}', [LocationController::class, 'cities']);

    // ==========================
    // Sponsors & Business Listing
    // ==========================
    Route::get('get/paid/sponsors',       [SponsorController::class, 'paidSponsorList']);
    Route::get('get/free/advertisements', [SponsorController::class, 'getFreeAds']);
    Route::post('share/{post}/social',    [PostController::class,   'sharePost']);

    Route::get('category/{category}/business', [BusinessController::class, 'index']);
    Route::get('get/business/list',            [BusinessController::class, 'getBusinessList']);

    // ==========================
    // Payment Callbacks (Public – من شركات الدفع)
    // ==========================

    // Fawry success callback
    Route::post('fawry-success-payment', [PaymentController::class, 'fawrySuccessPayment']);

    // CashU success (المنطق داخل PaymentController@cashuSuccess)
    Route::any('payment/success/cashu', [PaymentController::class, 'cashuSuccess']);

    // CashU error echo back (لأغراض التتبع)
    Route::any('payment/error/cashu', function (Request $request) {
        return $request->all();
    });

    /*
    |--------------------------------------------------------------------------
    | Authenticated APIs (middleware: auth:sanctum)
    |--------------------------------------------------------------------------
    |
    | كل المسارات هنا تتطلب توكن صالح (Sanctum).
    | داخلها مسارات خاصة بالبزنس فقط (business middleware).
    |
    */

    Route::middleware('auth:sanctum')->group(function () {

        // ==========================
        // Profile & Account
        // ==========================
        Route::get('profile',            [ProfileController::class, 'index']);
        Route::post('profile/update',    [ProfileController::class, 'updateProfile']);
        Route::post('user/update/phone', [ProfileController::class, 'updatePhone']);

        // ==========================
        // Notifications
        // ==========================
        Route::get('notifications',            [NotificationController::class, 'index']);
        Route::get('notifications/unread',     [NotificationController::class, 'unread']);
        Route::get('notifications/{id}',       [NotificationController::class, 'show']);
        Route::post('notifications/{id}/read', [NotificationController::class, 'markAsRead']);
        Route::delete('notifications/{id}',    [NotificationController::class, 'destroy']);
        Route::post('notifications',           [NotificationController::class, 'store']);

        // ==========================
        // Cars
        // ==========================
        Route::prefix('cars')->group(function () {
            Route::get('my',      [CarController::class, 'myCars']);
            Route::post('/',      [CarController::class, 'store']);
            Route::get('{id}',    [CarController::class, 'show']);
            Route::put('{id}',    [CarController::class, 'update']);
            Route::delete('{id}', [CarController::class, 'destroy']);
        });

        // ==========================
        // Menu (Items / Sizes / Extras)
        // ==========================
        Route::prefix('menu')->group(function () {

            /*
            |-----------------------------
            | Read-Only (أي مستخدم مسجّل)
            |-----------------------------
            */
            Route::get('/',      [MenuItemController::class, 'index']);
            Route::get('search', [MenuItemController::class, 'search']);

            /*
            |-----------------------------
            | Business-Only (لوحة تحكم البزنس)
            |-----------------------------
            */
            Route::middleware('business')->group(function () {
                // Items CRUD
                Route::post('item',             [MenuItemController::class, 'store']);
                Route::post('item/{id}/update', [MenuItemController::class, 'update']);
                Route::delete('item/{id}',      [MenuItemController::class, 'delete']);

                // Sizes
                Route::post('item/{id}/size', [MenuItemSizeController::class, 'store']);
                Route::delete('size/{id}',    [MenuItemSizeController::class, 'delete']);

                // Extras
                Route::post('item/{id}/extra', [MenuItemExtraController::class, 'store']);
                Route::delete('extra/{id}',    [MenuItemExtraController::class, 'delete']);
            });
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
        // MENU ORDERS (من الكارت)
        // ==========================
        Route::prefix('menu/orders')
            ->controller(MenuOrderController::class)
            ->group(function () {

                // إنشاء طلب من الكارت (User)
                Route::post('from-cart', 'createFromCart');

                // طلبات المستخدم
                Route::get('my', 'myOrders');

                // تفاصيل طلب (يُسمح للمستخدم والبزنس عبر Policy داخل الكنترولر)
                Route::get('{id}', 'show');

                // جزء خاص بالبزنس فقط
                Route::middleware('business')->group(function () {
                    // طلبات البزنس
                    Route::get('business', 'businessOrders');
                    // تحديث حالة الطلب
                    Route::post('{id}/status', 'updateStatus');
                });
            });

        /*
        |--------------------------------------------------------------------------
        | Orders (General)
        |--------------------------------------------------------------------------
        */
        Route::prefix('orders')->group(function () {

            // إنشاء طلب عام (User)
            Route::post('/',   [OrderController::class, 'store']);

            // طلبات المستخدم
            Route::get('my',   [OrderController::class, 'myOrders']);

            // عرض طلب معيّن
            Route::get('{id}', [OrderController::class, 'show']);

            // جزء خاص بالبزنس فقط
            Route::middleware('business')->group(function () {
                // طلبات خاصة بالبزنس
                Route::get('business',     [OrderController::class, 'businessOrders']);
                // تحديث حالة الطلب
                Route::post('{id}/status', [OrderController::class, 'updateStatus']);
            });
        });

        /*
        |--------------------------------------------------------------------------
        | Delivery Orders
        |--------------------------------------------------------------------------
        |
        | لم نضف business middleware هنا حتى لا نكسر منطق السائقين / البزنس
        | الفصل يتم غالباً داخل DeliveryController حسب نوع المستخدم.
        |
        */
        Route::prefix('delivery')->group(function () {
            Route::post('orders',             [DeliveryController::class, 'store']);
            Route::get('orders',              [DeliveryController::class, 'myOrders']);
            Route::get('orders/business',     [DeliveryController::class, 'businessOrders']);
            Route::get('orders/driver',       [DeliveryController::class, 'driverOrders']);
            Route::get('orders/{id}',         [DeliveryController::class, 'show']);
            Route::post('orders/{id}/accept', [DeliveryController::class, 'accept']);
            Route::post('orders/{id}/status', [DeliveryController::class, 'updateStatus']);
            Route::delete('orders/{id}',      [DeliveryController::class, 'cancel']);
        });

        /*
        |--------------------------------------------------------------------------
        | Driver Location
        |--------------------------------------------------------------------------
        */
        Route::prefix('driver/location')->group(function () {
            Route::post('update',     [DriverLocationController::class, 'update']);
            Route::get('{driver_id}', [DriverLocationController::class, 'show']);
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
        | Chat
        |--------------------------------------------------------------------------
        */
        Route::prefix('chat')->group(function () {
            Route::get('conversations',                [ChatController::class, 'conversations']);
            Route::post('conversations',               [ChatController::class, 'startConversation']);
            Route::get('conversations/{id}/messages',  [ChatController::class, 'messages']);
            Route::post('conversations/{id}/messages', [ChatController::class, 'sendMessage']);
            Route::post('conversations/{id}/read',     [ChatController::class, 'markAsRead']);
            Route::delete('conversations/{id}',        [ChatController::class, 'deleteConversation']);
        });

        /*
        |--------------------------------------------------------------------------
        | Booking
        |--------------------------------------------------------------------------
        */
        Route::prefix('booking')->group(function () {

            // إنشاء حجز (User)
            Route::post('create',        [BookingController::class, 'store']);

            // تحديث حالة الحجز (صلاحياتها تضبط داخل الكنترولر)
            Route::post('update-status', [BookingController::class, 'updateStatus']);

            // حجوزات المستخدم
            Route::get('my', [BookingController::class, 'myBookings']);

            // حجوزات البزنس فقط
            Route::middleware('business')->group(function () {
                Route::get('business', [BookingController::class, 'businessBookings']);
            });
        });

        /*
        |--------------------------------------------------------------------------
        | Payments (Internal Actions)
        |--------------------------------------------------------------------------
        */
        Route::post('payment/charge/account', [PaymentController::class, 'chargeAccount']);
        Route::post('payment/subscription',   [PaymentController::class, 'store']);
        Route::post('payment/transfer',       [PaymentController::class, 'transferToAnother']);

        /*
        |--------------------------------------------------------------------------
        | Transactions (Financial History)
        |--------------------------------------------------------------------------
        |
        | المعاملات المالية للحساب الحالي (Client أو Business).
        |
        */
        Route::prefix('transactions')
            ->controller(TransactionController::class)
            ->group(function () {
                Route::get('/',       'index');   // جميع المعاملات للحساب الحالي
                Route::get('balance', 'balance'); // الرصيد الحالي (من Main::calculateUserBalance)
                Route::get('summary', 'summary'); // ملخص (إيداع / سحب)
                Route::get('{id}',    'show');    // عملية محددة
            });
            Route::middleware('auth:sanctum')->prefix('wallet')->group(function () {
                 Route::post('set-pin',   'setPin');
                 Route::post('check-pin', 'checkPin');
            // balance
            Route::get('balance', [WalletController::class, 'balance']);

            // deposit / withdraw / transfer
            Route::post('deposit',  [WalletController::class, 'deposit']);
            Route::post('withdraw', [WalletController::class, 'withdraw']);
            Route::post('transfer', [WalletController::class, 'transfer']);

            // transaction history
            Route::get('transactions', [WalletController::class, 'transactions']);

            // PIN
            Route::post('pin/set',    [WalletPinController::class, 'setPin']);
            Route::post('pin/verify', [WalletPinController::class, 'verifyPin']);
             });
            Route::middleware(['auth:sanctum', 'check.pin'])->group(function () {

            Route::post('wallet/withdraw',  [WalletController::class, 'withdraw']);
            Route::post('wallet/transfer',  [WalletController::class, 'transfer']);

            // مستقبلًا: عمليات Escrow
            Route::post('escrow/lock',   [EscrowController::class, 'lockAmount']);
            Route::post('escrow/release',[EscrowController::class, 'releaseAmount']);
            });

            Route::prefix('wallet')->controller(WalletController::class)->group(function () {
            Route::post('pin/set',        'setPin');        // إنشاء PIN لأول مرة
            Route::post('pin/check',      'verifyPin');     // التحقق من PIN
            Route::post('pin/update',     'updatePin');     // تغيير PIN

            // Balance
            Route::get('balance',         'balance');       // عرض الرصيد

            // Financial operations
            Route::post('deposit',        'deposit');       // إيداع
            Route::post('withdraw',       'withdraw');      // سحب
            Route::post('transfer',       'transfer');      // تحويل بين المستخدمين

        });

        // =======================================
        // Escrow System
        // =======================================
        Route::prefix('escrow')->controller(\App\Http\Controllers\Api\V1\EscrowController::class)->group(function () {

            Route::post('create', 'create');              // إنشاء Escrow
            Route::get('my', 'myEscrows');                // كل Escrows الخاصة بالمستخدم
            Route::get('{id}', 'show');                   // تفاصيل Escrow

            Route::post('{id}/release', 'release');       // تحرير الأموال للطرفين
            Route::post('{id}/cancel', 'cancel');         // إلغاء Escrow

        });



        /*
        |--------------------------------------------------------------------------
        | Deposit / Escrow System
        |--------------------------------------------------------------------------
        |
        | نظام الدفعة المقدَّمة بين العميل والبزنس (Escrow / Deposit).
        |
        */
        Route::prefix('deposit')
            ->controller(DepositController::class)
            ->group(function () {

                // إنشاء عملية إسكرو جديدة بين طرفين
                Route::post('create', 'create');

                // تأكيد العميل أنه دفع خارج BIM أو داخلها حسب السيناريو
                Route::post('{id}/confirm/client',   'clientConfirm');
                // تأكيد البزنس
                Route::post('{id}/confirm/business', 'businessConfirm');

                // في حالة الدفع خارج BIM مع تسجيل الإقرار فقط
                Route::post('{id}/outside-bim/client',   'clientOutsideBim');
                Route::post('{id}/outside-bim/business', 'businessOutsideBim');

                // فتح نزاع على العملية
                Route::post('{id}/dispute/open', 'openDispute');
            });
            Route::middleware('auth:sanctum')->prefix('wallet')->group(function () {
            Route::post('pin/set', [WalletPinController::class, 'setPin']);
            Route::post('pin/verify', [WalletPinController::class, 'verifyPin']);
            });

    });

});
        