<?php

use App\Models\Category;
use Carbon\Carbon;

// كنترولرز جديدة بنستخدمها بالـ class syntax
use App\Http\Controllers\Api\V1\NotificationController;
use App\Http\Controllers\Api\V1\Controller;
use App\Http\Controllers\Api\V1\BookingController;
use App\Http\Controllers\Api\V1\RideController;
use App\Http\Controllers\Api\V1\ChatController;
use App\Http\Controllers\Api\V1\DeliveryController;
use App\Http\Controllers\Api\V1\CarController;
use App\Http\Controllers\Api\V1\CartController;
use App\Http\Controllers\Api\V1\OrderController;
use App\Http\Controllers\Api\V1\MenuItemController;
use App\Http\Controllers\Api\V1\MenuItemSizeController;
use App\Http\Controllers\Api\V1\MenuItemExtraController;
use App\Http\Controllers\Api\V1\MenuCartController;




Route::group(['prefix' => 'v1'], function () {

    Route::get('distance', function () {
        return getDistanceBetweenPointsNew(
            26.296127174184168,
            43.72053164988756,
            27.507722529999988,
            41.75938371999999,
            'Km'
        );
    });

    // Registration
    Route::post('register', 'App\Http\Controllers\Api\V1\RegistrationController@store');

    // Login after activate account
    Route::post('login', 'App\Http\Controllers\Api\V1\LoginController@login');

    // Change password first enter phone number and will check if is correct.
    Route::post('password/forgot', 'App\Http\Controllers\Api\V1\ForgotPasswordController@getResetTokens');

    // After arrive reset code send to check is true.
    Route::post('password/check', 'App\Http\Controllers\Api\V1\ResetPasswordController@check');

    // After arrive reset code send to other again and reset password.
    Route::post('password/reset', 'App\Http\Controllers\Api\V1\ResetPasswordController@reset');

    // Resent Reset Code
    Route::post('password/forgot/resend', 'App\Http\Controllers\Api\V1\ForgotPasswordController@resendResetPasswordCode');

    // Social Login
    Route::post('social/login', 'App\Http\Controllers\Api\V1\UsersController@socialLogin');

    // General info
    Route::get('general/info', 'App\Http\Controllers\Api\V1\SettingsController@generalInfo');
    Route::get('general/support', 'App\Http\Controllers\Api\V1\SettingsController@support');
    Route::get('general/contacts', 'App\Http\Controllers\Api\V1\SettingsController@contacts');

    Route::get('general/about-app', 'App\Http\Controllers\Api\V1\SettingsController@aboutApp');
    Route::get('general/social/links', 'App\Http\Controllers\Api\V1\SettingsController@socialLinks');

    // Public lists
    Route::get('/jobs', 'App\Http\Controllers\Api\V1\JobController@index');
    Route::get('/categories', 'App\Http\Controllers\Api\V1\CategoryController@index');
    Route::get('/category/{category}/products', 'App\Http\Controllers\Api\V1\ProductsController@productsByCategoryId');

    // Comments
    Route::get('comments/{post}/post', 'App\Http\Controllers\Api\V1\CommentController@index');
    Route::get('comments/{comment}/replies', 'App\Http\Controllers\Api\V1\CommentController@commentRepliesList');

    // Posts
    Route::get('get/posts', 'App\Http\Controllers\Api\V1\PostController@getPosts');

    // Locations
    Route::get('countries', 'App\Http\Controllers\Api\V1\LocationController@countries');
    Route::get('cities/{location}', 'App\Http\Controllers\Api\V1\LocationController@cities');

    // Sponsors
    Route::get('get/paid/sponsors', 'App\Http\Controllers\Api\V1\SponsorController@paidSponsorList');
    Route::post('share/{post}/social', 'App\Http\Controllers\Api\V1\PostController@sharePost');
    Route::get('get/free/advertisements', 'App\Http\Controllers\Api\V1\SponsorController@getFreeAds');

    // Jobs as posts
    Route::get('get/jobs', 'App\Http\Controllers\Api\V1\PostController@getJobs');

    // Profile public info
    Route::get('profile/information', 'App\Http\Controllers\Api\V1\ProfileController@getProfileInformation');

    // Business listing
    Route::get('category/{category}/business', 'App\Http\Controllers\Api\V1\BusinessController@index');
    Route::get('get/business/list', 'App\Http\Controllers\Api\V1\BusinessController@getBusinessList');
    Route::get('get/posts/{id}/list', 'App\Http\Controllers\Api\V1\PostController@index');

    // Fawry payment status
    Route::get('payment/fawry/status', function (\Illuminate\Http\Request $request) {
        $payment = \App\Models\Payment::whereId($request->paymentId)->first();
        if ($payment) {
            $hash = hash(
                'sha256',
                "siYxylRjSPzwey/eiO8sMw==" . $payment->id . "0aacb642-2a17-42bd-a573-4dfdeed6dd97"
            );
            return [$payment, $hash];
        } else {
            return "No payment!";
        }
    });

    // Currency converter
    Route::get('currency/converter', function (\Illuminate\Http\Request $request) {
        return response()->json([
            'status' => 200,
            'total'  => currencyConverter($request->from, $request->to, $request->amount)
        ]);
    });

    Route::get('test/service', function (\Illuminate\Http\Request $request) {
        return currencyConverter($request->amount);
    });

    Route::get('fawry-payment-fail', function () {
        return "failed payment";
    });

    // CashU success callback
    Route::any('payment/success/cashu', function (\Illuminate\Http\Request $request) {

        $cashU = $request->all();

        $durationCategory = explode('--', $cashU['txt5']);
        $userId          = $cashU['txt1'];
        $price           = currencyConverter("USD", "EGP", $cashU['txt2']);
        $code            = $cashU['txt3'];
        $duration        = $durationCategory[0];
        $serviceName     = $cashU['txt4'];

        $paymentData = [
            'price'          => $price,
            'payment_type'   => "cashu",
            'payment_no'     => $cashU['trn_id'],
            'operation_type' => $serviceName,
        ];

        $categoryId = $durationCategory[1];
        $code       = explode('--', $code);

        $user    = \App\Models\User::whereId($userId)->first();
        $payment = $user->payments()->create($paymentData);

        if ($serviceName != "recharge") :

            if ($code && $code[0] == 'profileCode') {

                $setting   = new \App\Models\Setting;
                $ownerCode = \App\Models\User::whereCode($code[1])->first();

                if ($ownerCode && $user->code != $code[1]) {

                    if (isset($categoryId) && $categoryId != '') {
                        $category = Category::whereId($request->categoryId)->first();
                        $cost     = $category->per_month;
                        if ($duration >= 12) {
                            $cost = $category->per_year;
                        }
                    } else {
                        $cost = optional($user->category)->parent->per_month;
                        if ($duration >= 12) {
                            $cost = optional($user->category)->parent->per_year;
                        }
                    }

                    $commissionMonths = $setting->getBody('commission_months');
                    if ($ownerCode->gifts != null) {
                        $commissionMonths = $ownerCode->gifts->commission_months;
                    }

                    $costPerMonth          = $cost;
                    if ($duration >= 12) {
                        $costPerMonth = $cost / 12;
                    }
                    $ownerCodeCommission = $costPerMonth * $commissionMonths * ($duration / 12);

                    $dataOwner = [
                        'status'    => 'deposit',
                        'price'     => sprintf("%.2f", $ownerCodeCommission),
                        'operation' => 'award',
                        'notes'     => 'From Registeration By Code Profile - ' . $user->code,
                        'target_id' => $user->id
                    ];
                    $ownerCode->transactions()->create($dataOwner);
                }
            }

            $month = 0;
            if ($subscription = $user->subscriptions->where('is_active', 1)->first()) {
                $month = Carbon::parse($subscription->finished_at)->format('m') - Carbon::now()->format('m');
                $subscription->update(['is_active' => 0]);
            }

            $inputs['category_id'] = optional($user->category)->parent_id;
            $inputs['finished_at'] = Carbon::now()->addMonths($duration + $month)->toDateTimeString();
            $inputs['user_id']     = $userId;
            $inputs['price']       = $price;
            $inputs['duration']    = $duration;
            $inputs['coupon_id']   = $code[0] == 'couponCode' ? $code[1] : null;

            if ($newSubscription = $user->subscriptions()->create($inputs)) :
                $payment->update(['operation_id' => $newSubscription->id]);
                return redirect(route('redirect-after-cashu-payment'));
            endif;

        else :
            $transactionData = [
                'status'    => 'deposit',
                'price'     => $price,
                'operation' => $cashU['txt4'],
                'notes'     => 'Recharge Account.',
                'target_id' => null
            ];
            $transaction = $user->transactions()->create($transactionData);
            $payment->update(['operation_id' => $transaction->id]);

            return redirect(route('redirect-after-cashu-payment'));
        endif;
    });

    Route::any('payment/error/cashu', function (\Illuminate\Http\Request $request) {
        return $request->all();
    });

    Route::post('fawry-success-payment', "App\Http\Controllers\Api\V1\PaymentController@fawrySuccessPayment");

    Route::any('redirect/cashu/payment/{Transaction_Code}', function (\Illuminate\Http\Request $request, $Transaction_Code) {
        return view('payment.cashu', compact('Transaction_Code'));
    })->name('cashu.redirect.payment');

    Route::get('redirect-after-cashu-payment', function () {
        return view('payment.after-redirect');
    })->name('redirect-after-cashu-payment');
});


// ============================
//  Authenticated APIs (auth:api)
// ============================
Route::group(['prefix' => 'v1', 'middleware' => 'auth:api'], function () {

    Route::post('contact-us', 'App\Http\Controllers\Api\V1\SettingsController@postMessage');

    Route::post('update/user/password', 'App\Http\Controllers\Api\V1\CompaniesController@updateUserPassword');
    Route::post('support/message', "App\Http\Controllers\Api\V1\SupportsController@sendMessage");

    Route::get("collection/ids/users", function () {
        return getTargetsAndFollowersBusiness();
    });
    Route::prefix('cars')->group(function () {
        Route::get('my',   [CarController::class, 'myCars']);   // سيارات السائق الحالى
        Route::post('/',   [CarController::class, 'store']);    // إضافة سيارة
        Route::get('{id}', [CarController::class, 'show']);     // تفاصيل سيارة
        Route::put('{id}', [CarController::class, 'update']);   // تحديث
        Route::delete('{id}', [CarController::class, 'destroy']); // حذف



    });

    // Posts
    Route::post('posts/store', "App\Http\Controllers\Api\V1\PostController@store");
    Route::delete('posts/{post}/delete', 'App\Http\Controllers\Api\V1\PostController@delete');
    Route::post('posts/{post}/update', "App\Http\Controllers\Api\V1\PostController@update");

    // Jobs Apply
    Route::post('job/apply', "App\Http\Controllers\Api\V1\ApplyController@apply");
    Route::post('approve/applied/job', "App\Http\Controllers\Api\V1\ApplyController@approve");
    Route::get('applies/list', "App\Http\Controllers\Api\V1\ApplyController@index");

    // Followers / Targets
    Route::get('user/followers', "App\Http\Controllers\Api\V1\FollowController@index");
    Route::get('user/targets', "App\Http\Controllers\Api\V1\TargetController@index");

    Route::post('post/like', "App\Http\Controllers\Api\V1\LikeController@index");
    Route::post('follow/user', "App\Http\Controllers\Api\V1\FollowController@store");
    Route::post('follow/categories', "App\Http\Controllers\Api\V1\FollowController@storeCategoryFollow");

    Route::post('target/user', "App\Http\Controllers\Api\V1\TargetController@store");
    Route::post('target/categories', "App\Http\Controllers\Api\V1\TargetController@storeTargetCategories");

    // Albums
    Route::post('albums/store', "App\Http\Controllers\Api\V1\AlbumController@store");
    Route::delete('albums/{album}/destroy', "App\Http\Controllers\Api\V1\AlbumController@destroy");
    Route::post('albums/{album}/update', "App\Http\Controllers\Api\V1\AlbumController@update");

    // Uploads
    Route::post('upload/multi/images', "App\Http\Controllers\Api\V1\ImageController@store");
    Route::post('upload/image', "App\Http\Controllers\Api\V1\ImageController@fileUploader");

    // Profile
    Route::get('profile', "App\Http\Controllers\Api\V1\ProfileController@index");
    Route::post('profile/update', "App\Http\Controllers\Api\V1\ProfileController@updateProfile");
    Route::post('user/update/phone', "App\Http\Controllers\Api\V1\ProfileController@updatePhone");

    // Rating
    Route::post('user/rate', 'App\Http\Controllers\Api\V1\RatesController@postRate');
    Route::post('/rating', 'App\Http\Controllers\Api\V1\RatesController@postRating');

    // Sponsors
    Route::get('sponsors', 'App\Http\Controllers\Api\V1\SponsorController@index');
    Route::post('post/ads', 'App\Http\Controllers\Api\V1\SponsorController@store');
    Route::post('sponsor/{sponsor}/update', 'App\Http\Controllers\Api\V1\SponsorController@update');
    Route::delete('sponsor/{sponsor}/delete', 'App\Http\Controllers\Api\V1\SponsorController@delete');
    Route::post('sponsor/{sponsor}/stop', 'App\Http\Controllers\Api\V1\SponsorController@stop');

    // Language & notification settings
    Route::post('update/language', "App\Http\Controllers\Api\V1\ProfileController@updateLanguage");
    Route::post('profile/update/lang', 'App\Http\Controllers\Api\V1\UsersController@profileUpdateLang');
    Route::post('profile/update/notification', 'App\Http\Controllers\Api\V1\UsersController@profileUpdateNotification');

    // Auth
    Route::post('user/logout', "App\Http\Controllers\Api\V1\ProfileController@logout");
    Route::post('password/change', 'App\Http\Controllers\Api\V1\UsersController@changePassword');
    Route::post('user/delete', 'App\Http\Controllers\Api\V1\UsersController@deleteUser');

    // Categories (protected)
    Route::get('categories/{id?}', 'App\Http\Controllers\Api\V1\CategoriesController@index');

    // Coupon
    Route::post("coupon/discount", "App\Http\Controllers\Api\V1\CouponController@discount");

    // Support
    Route::post('support/post/message', 'App\Http\Controllers\Api\V1\SupportsController@postMessage');

    // Balance & Transactions
    Route::get('total/user/balance', "App\Http\Controllers\Api\V1\TransactionController@getUserBalance");
    Route::get('transactions', 'App\Http\Controllers\Api\V1\TransactionController@index');

    // ==========================
    //  Notifications API
    // ==========================
    Route::get('notifications',        [NotificationController::class, 'index']);
    Route::get('notifications/unread', [NotificationController::class, 'unread']);
    Route::get('notifications/{id}',   [NotificationController::class, 'show']);
    Route::post('notifications/{id}/read', [NotificationController::class, 'markAsRead']);
    Route::delete('notifications/{id}',    [NotificationController::class, 'destroy']);
    Route::post('notifications',           [NotificationController::class, 'store']);

    // ==========================
    //  Cart API
    // ==========================
    Route::get('/cart',        [CartController::class, 'index']);
    Route::post('/cart/add',   [CartController::class, 'add']);
    Route::post('/cart/update', [CartController::class, 'updateQty']);
    Route::post('/cart/delete', [CartController::class, 'delete']);

   Route::prefix('booking')->middleware('auth:api')->group(function () {

    Route::post('/create', [BookingController::class, 'store']);
    Route::post('/update-status', [BookingController::class, 'updateStatus']);
    Route::get('/my', [BookingController::class, 'myBookings']);
    Route::get('/business', [BookingController::class, 'businessBookings']);

    });



     //========================
    //      Delivery API
    // =========================

    Route::prefix('delivery')->group(function () {

      
    // إنشاء طلب دليفري
    Route::post('orders', [DeliveryController::class, 'store']);

    // طلبات المستخدم (عميل)
    Route::get('orders', [DeliveryController::class, 'myOrders']);

    // طلبات البزنس
    Route::get('orders/business', [DeliveryController::class, 'businessOrders']);

    // طلبات السائق
    Route::get('orders/driver', [DeliveryController::class, 'driverOrders']);

    // تفاصيل طلب
    Route::get('orders/{id}', [DeliveryController::class, 'show']);

    // السائق يقبل الطلب
    Route::post('orders/{id}/accept', [DeliveryController::class, 'accept']);

    // تحديث حالة الطلب
    Route::post('orders/{id}/status', [DeliveryController::class, 'updateStatus']);

    // إلغاء الطلب
    Route::delete('orders/{id}', [DeliveryController::class, 'cancel']);
    });
    Route::prefix('menu')->middleware('auth:api')->group(function () {

    // Items
    Route::get('/', [MenuItemController::class, 'index']);
    Route::post('/item', [MenuItemController::class, 'store']);
    Route::post('/item/{id}/update', [MenuItemController::class, 'update']);
    Route::delete('/item/{id}', [MenuItemController::class, 'delete']);
    Route::get('/menu/search', [MenuItemController::class, 'search']);


    // Sizes
    Route::post('/item/{id}/size', [MenuItemSizeController::class, 'store']);
    Route::delete('/size/{id}', [MenuItemSizeController::class, 'delete']);

    // Extras
    Route::post('/item/{id}/extra', [MenuItemExtraController::class, 'store']);
    Route::delete('/extra/{id}', [MenuItemExtraController::class, 'delete']);

    });
    Route::group(['middleware' => 'auth:api', 'prefix' => 'menu/cart'], function () {

    Route::get('/', [MenuCartController::class, 'index']);
    Route::post('/add', [MenuCartController::class, 'add']);
    Route::post('/update', [MenuCartController::class, 'updateQty']);
    Route::post('/delete', [MenuCartController::class, 'delete']);
    Route::post('/clear', [MenuCartController::class, 'clear']);
  });

    Route::middleware('auth:api')->group(function () {

    // Orders
    Route::post('/orders', [OrderController::class, 'store']);
    Route::get('/orders/my', [OrderController::class, 'myOrders']);
    Route::get('/orders/business', [OrderController::class, 'businessOrders']);
    Route::post('/orders/{id}/status', [OrderController::class, 'updateStatus']);

});

Route::group(['prefix' => 'v1', 'middleware' => 'auth:api'], function () {
    
    // ========== MENU ORDERS ==========
    Route::prefix('orders')->group(function () {

        // إنشاء طلب جديد
        Route::post('/create', [\App\Http\Controllers\Api\OrderController::class, 'create']);

        // طلبات المستخدم
        Route::get('/my', [\App\Http\Controllers\Api\OrderController::class, 'myOrders']);

        // طلبات البزنس
        Route::get('/business', [\App\Http\Controllers\Api\OrderController::class, 'businessOrders']);

        // حالة الطلب
        Route::post('/update-status', [\App\Http\Controllers\Api\OrderController::class, 'updateStatus']);

        // عرض تفاصيل طلب
        Route::get('/{id}', [\App\Http\Controllers\Api\OrderController::class, 'show']);
         // إنشاء طلب من المنيو (من الكارت)
        Route::post('orders/from-menu-cart', [OrderController::class, 'storeFromMenuCart']);

        // طلبات المستخدم (client / business كـ customer)
        Route::get('orders/my', [OrderController::class, 'myOrders']);

        // طلبات البزنس (كمقدم خدمة)
        Route::get('orders/business', [OrderController::class, 'businessOrders']);

        // تفاصيل طلب
        Route::get('orders/{id}', [OrderController::class, 'show']);

        // تحديث حالة الطلب (من البزنس)
        Route::post('orders/{id}/status', [OrderController::class, 'updateStatus']);
    });

    });




    // ==========================
    //  Rides API (Uber-like)
    // ==========================
    Route::prefix('rides')->group(function () {
        // إنشاء طلب رحلة
        Route::post('/', [RideController::class, 'store']);

        // رحلات المستخدم (client / business)
        Route::get('/', [RideController::class, 'myRides']);

        // تفاصيل رحلة
        Route::get('/{id}', [RideController::class, 'show']);

        // إلغاء رحلة
        Route::delete('/{id}', [RideController::class, 'cancel']);

        // تحديث حالة الرحلة
        Route::post('/{id}/status', [RideController::class, 'updateStatus']);

        // قبول الرحلة من السائق
        Route::post('/{id}/accept', [RideController::class, 'acceptRide']);
    });

    // رحلات السائق
    Route::get('driver/rides', [RideController::class, 'driverRides']);

    // ========================
    //       Chat API
    // ========================
    Route::prefix('chat')->group(function () {
        // قائمة المحادثات
        Route::get('conversations', [ChatController::class, 'conversations']);

        // بدء / فتح محادثة مع مستخدم آخر
        Route::post('conversations', [ChatController::class, 'startConversation']);

        // رسائل محادثة معينة
        Route::get('conversations/{id}/messages', [ChatController::class, 'messages']);

        // إرسال رسالة داخل محادثة
        Route::post('conversations/{id}/messages', [ChatController::class, 'sendMessage']);

        // تعليم رسائل المحادثة كمقروءة
        Route::post('conversations/{id}/read', [ChatController::class, 'markAsRead']);

        // حذف محادثة
        Route::delete('conversations/{id}', [ChatController::class, 'deleteConversation']);
    });

    // ==========================
    //  Conversations / Chat
    // ==========================
    Route::get('counts/list', 'App\Http\Controllers\Api\V1\UsersController@countNotifications');

    Route::get('conversations/messages', 'App\Http\Controllers\Api\V1\AdvertisementsController@messages');
    Route::get('conversations/list', 'App\Http\Controllers\Api\V1\ConversationsController@getListOfConversations');

    Route::post('check/user/hasConversations', 'App\Http\Controllers\Api\V1\ConversationsController@checkUserHasConversation');
    Route::post('conversations/asread', 'App\Http\Controllers\Api\V1\ConversationsController@markConversationAsRead');
    Route::get('conversations/messages/list', 'App\Http\Controllers\Api\V1\ConversationsController@getAllMessages');
    Route::post('conversation/offline', 'App\Http\Controllers\Api\V1\ConversationsController@makeUserConversationOffline');
    Route::post('conversation/delete', 'App\Http\Controllers\Api\V1\ConversationsController@deleteConversation');
    Route::post('devices/delete', 'App\Http\Controllers\Api\V1\ConversationsController@deleteUserDevices');

    // ==========================
    //  Comments
    // ==========================
    Route::post('comments/store', 'App\Http\Controllers\Api\V1\CommentController@store');
    Route::post('comments/{comment}/replies', 'App\Http\Controllers\Api\V1\CommentController@commentReplies');

    // ==========================
    //  Payment & Subscription
    // ==========================
    Route::post('payment/subscription', 'App\Http\Controllers\Api\V1\PaymentController@store');
    Route::post('payment/charge/account', 'App\Http\Controllers\Api\V1\PaymentController@chargeAccount');
    Route::post('payment/transfer', 'App\Http\Controllers\Api\V1\PaymentController@transferToAnother');

    // CashU Online payment
    Route::get('/payment/cashu/online', function (\Illuminate\Http\Request $request) {
        $main = new \App\Libraries\Main;
        $user = $request->user();

        $addtions = [
            'price'      => currencyConverter("EGP", "USD", $request->price),
            'code'       => $request->code,
            'codeType'   => $request->codeType,
            'duration'   => $request->duration,
            'actionType' => $request->actionType,
            'categoryId' => $request->categoryId
        ];

        return $main->paymentAction(
            $addtions['price'],
            $user->id,
            '',
            '',
            'USD',
            'balance',
            'en,',
            'Test',
            '',
            '',
            $addtions
        );
    });

    // CashU charge account
    Route::get('/payment/cashu/charge/account', function (\Illuminate\Http\Request $request) {
        $main = new \App\Libraries\Main;
        $user = $request->user();

        $addtions = [
            'price'      => currencyConverter("EGP", "USD", $request->price),
            'actionType' => 'recharge',
        ];

        return $main->paymentAction(
            $addtions['price'],
            $user->id,
            '',
            '',
            'USD',
            'balance',
            'en,',
            $user->name,
            '',
            '',
            $addtions
        );
    });

    // Fawry charge account
    Route::get('fawry/payment/charge/account', function (\Illuminate\Http\Request $request) {
        $main = new \App\Libraries\Main;

        $user = $request->user();
        $paymentData = [
            'price'          => $request->price,
            'payment_type'   => null,
            'payment_no'     => null,
            'operation_type' => 'recharge',
            'paid_at'        => null,
        ];

        $payment        = $user->payments()->create($paymentData);
        $paymentResponse = $main->fawryPayment(
            $request->price,
            $payment->id,
            '',
            '',
            'USD',
            'balance',
            'en,',
            $user->name,
            $user->phone,
            $user->email,
            $user->id
        );

        $url          = $paymentResponse['url'];
        $chargeRequest = $paymentResponse['chargeRequest'];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($chargeRequest));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

        return response()->json([
            'status'     => 200,
            'message'    => 'success handle data.',
            'paymentUrl' => curl_exec($ch)
        ]);
    });

    // Fawry subscription
    Route::get('generate/fawry/payment/subscription', function (\Illuminate\Http\Request $request) {
        $user = $request->user();
        $main = new \App\Libraries\Main;

        $paymentData = [
            'price'          => $request->price,
            'payment_type'   => null,
            'payment_no'     => null,
            'operation_type' => 'subscription',
            'paid_at'        => null,
        ];

        $payment = $user->payments()->create($paymentData);

        $subscriptionData = [
            'category_id' => $request->categoryId,
            'user_id'     => $user->id,
            'price'       => $request->price,
            'duration'    => $request->duration,
            'coupon_id'   => $request->code != "" ? $request->code : null,
            'code_type'   => $request->codeType,
            'finished_at' => null,
            'is_active'   => 0,
        ];

        if ($subscripe = $user->subscriptions()->create($subscriptionData)) {
            $payment->update(['operation_id' => $subscripe->id]);
        }

        $paymentResponse = $main->fawryPayment(
            $request->price,
            $payment->id,
            $user->id,
            $user->phone,
            $user->email
        );

        $url           = $paymentResponse['url'];
        $chargeRequest = $paymentResponse['chargeRequest'];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($chargeRequest));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

        return response()->json([
            'status'     => 200,
            'message'    => 'success handle data.',
            'paymentUrl' => curl_exec($ch)
        ]);
    });
});
