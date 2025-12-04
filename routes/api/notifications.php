<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\NotificationController;

Route::prefix('v1')->middleware('auth:sanctum')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Notifications System
    |--------------------------------------------------------------------------
    | تشمل:
    | - جلب الإشعارات
    | - جلب الإشعارات غير المقروءة
    | - عرض إشعار معيّن
    | - حذف إشعار
    | - إنشاء إشعار
    | - عداد الإشعارات
    */

    // جميع الإشعارات
    Route::get('notifications', [NotificationController::class, 'index']);

    // الإشعارات غير المقروءة
    Route::get('notifications/unread', [NotificationController::class, 'unread']);

    // عرض إشعار معيّن
    Route::get('notifications/{id}', [NotificationController::class, 'show']);

    // تعليم إشعار كمقروء
    Route::post('notifications/{id}/read', [NotificationController::class, 'markAsRead']);

    // حذف إشعار
    Route::delete('notifications/{id}', [NotificationController::class, 'destroy']);

    // إنشاء إشعار جديد (يستخدم داخل النظام)
    Route::post('notifications', [NotificationController::class, 'store']);

    // عداد الإشعارات (موجود في النظام القديم)
    Route::get('notifications/count', [NotificationController::class, 'countForUser']);

    // من النسخة القديمة: عداد كامل لحالات المستخدم
    Route::get('counts/list', [NotificationController::class, 'countNotifications']);
});
