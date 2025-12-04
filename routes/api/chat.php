<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\ChatController;
use App\Http\Controllers\Api\V1\ConversationsController;
use App\Http\Controllers\Api\V1\AdvertisementsController;

Route::prefix('v1')->middleware('auth:sanctum')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Chat System (Conversations + Messages)
    |--------------------------------------------------------------------------
    | يجمع كل وظائف المحادثات:
    | - إنشاء محادثة
    | - إرسال رسالة
    | - عرض الرسائل
    | - جلب كل المحادثات
    | - تعليم محادثة كمقروءة
    | - حذف محادثة
    | - التحقق من وجود محادثة مسبقاً
    | - بيانات المراسلة في الإعلانات
    */

    // -------------------------------------------------------
    // Conversations — المحادثات
    // -------------------------------------------------------

    // إنشاء محادثة جديدة
    Route::post('conversations', [ConversationsController::class, 'sendMessage']);

    // قائمة المحادثات
    Route::get('conversations/list', [ConversationsController::class, 'getListOfConversations']);

    // فحص إذا كان هناك محادثة بين مستخدمين
    Route::post('check/user/hasConversations', [ConversationsController::class, 'checkUserHasConversation']);

    // تعليم المحادثة كمقروءة
    Route::post('conversations/asread', [ConversationsController::class, 'markConversationAsRead']);

    // حذف محادثة
    Route::post('conversation/delete', [ConversationsController::class, 'deleteConversation']);

    // جعل المستخدم Offline في محادثة
    Route::post('conversation/offline', [ConversationsController::class, 'makeUserConversationOffline']);

    // حذف أجهزة المستخدم (من النظام القديم)
    Route::post('devices/delete', [ConversationsController::class, 'deleteUserDevices']);

    // -------------------------------------------------------
    // Messages — الرسائل داخل المحادثات
    // -------------------------------------------------------

    // جميع الرسائل في محادثة واحدة
    Route::get('conversations/messages/list', [ConversationsController::class, 'getAllMessages']);

    // استرجاع الرسائل التابعة لإعلان
    Route::get('conversations/messages', [AdvertisementsController::class, 'messages']);

    // -------------------------------------------------------
    // ChatController (النظام الجديد)
    // -------------------------------------------------------

    Route::prefix('chat')->controller(ChatController::class)->group(function () {

        // جلب كل المحادثات
        Route::get('conversations', 'conversations');

        // إنشاء محادثة جديدة
        Route::post('conversations', 'startConversation');

        // جلب الرسائل داخل محادثة
        Route::get('conversations/{id}/messages', 'messages');

        // إرسال رسالة جديدة
        Route::post('conversations/{id}/messages', 'sendMessage');

        // تعليم كمقروء
        Route::post('conversations/{id}/read', 'markAsRead');

        // حذف محادثة
        Route::delete('conversations/{id}', 'deleteConversation');
    });

});
