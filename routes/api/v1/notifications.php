<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\{
    NotificationController,
    NotificationsController
};


Route::middleware('auth:sanctum')->group(function () {

    // جميع الإشعارات
    Route::get('notifications',            [NotificationController::class, 'index']);

    // إشعارات غير مقروءة
    Route::get('notifications/unread',     [NotificationController::class, 'unread']);

    // عرض تفاصيل إشعار واحد
    Route::get('notifications/{id}',       [NotificationController::class, 'show']);

    // وضع الإشعار كمقروء
    Route::post('notifications/{id}/read', [NotificationController::class, 'markAsRead']);

    // حذف الإشعار (من NotificationsController القديم)
    Route::post('notification/delete',     [NotificationsController::class, 'delete']);

    // عداد الإشعارات
    Route::get('notifications/count',      [NotificationController::class, 'countForUser']);
});
