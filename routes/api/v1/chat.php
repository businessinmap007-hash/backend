<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\ConversationsController;

Route::middleware('auth:api')->group(function () {

    Route::post('conversations',               [ConversationsController::class, 'sendMessage']);
    Route::get('conversations/list',           [ConversationsController::class, 'getListOfConversations']);
    Route::get('conversations/messages',       [ConversationsController::class, 'messages']);
    Route::get('conversations/messages/list',  [ConversationsController::class, 'getAllMessages']);

    Route::post('conversation/delete',         [ConversationsController::class, 'deleteConversation']);
    Route::post('conversations/asread',        [ConversationsController::class, 'markConversationAsRead']);
    Route::post('conversation/offline',        [ConversationsController::class, 'makeUserConversationOffline']);

    Route::post('check/user/hasConversations', [ConversationsController::class, 'checkUserHasConversation']);
    Route::post('devices/delete',              [ConversationsController::class, 'deleteUserDevices']);

});
