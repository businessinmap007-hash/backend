<?php

use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {

    require __DIR__ . '/v1/auth.php';
    require __DIR__ . '/v1/public.php';

    require __DIR__ . '/v1/users.php';
    require __DIR__ . '/v1/social.php';
    require __DIR__ . '/v1/follow_target.php';
    require __DIR__ . '/v1/likes_ratings.php';

    require __DIR__ . '/v1/products.php';
    require __DIR__ . '/v1/business.php';

    require __DIR__ . '/v1/menu.php';
    require __DIR__ . '/v1/delivery.php';
    require __DIR__ . '/v1/car.php';

    require __DIR__ . '/v1/booking.php';
    require __DIR__ . '/v1/ride.php';

    require __DIR__ . '/v1/chat.php';
    require __DIR__ . '/v1/notifications.php';

    require __DIR__ . '/v1/payment.php';
    require __DIR__ . '/v1/wallet.php';
    require __DIR__ . '/v1/escrow.php';
    require __DIR__ . '/v1/deposit.php';

    require __DIR__ . '/v1/transactions.php';
    require __DIR__ . '/v1/orders.php';

    require __DIR__ . '/v1/cart.php';
    require __DIR__ . '/v1/media.php';
    require __DIR__ . '/v1/posts.php';

    require __DIR__ . '/v1/general.php';
});
