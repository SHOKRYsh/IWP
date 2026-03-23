<?php

use Illuminate\Support\Facades\Route;
use Modules\Notification\Http\Controllers\NotificationController;


Route::middleware(['auth:sanctum'])->prefix('notifications')->group(function () {
    Route::post('/send-notification-to-all', [NotificationController::class, 'sendPushNotificationToAll'])->middleware('role:Admin');
    Route::post('/notifyUser', [NotificationController::class, 'notifyUser'])->middleware('role:Admin');
    Route::get('/', [NotificationController::class, 'getAllNotifications']);
    Route::get('/count-unread', [NotificationController::class, 'getCountUnReadedNotifications']);
    Route::delete('/delete', [NotificationController::class, 'deleteNotifications'])->middleware('role:Admin');
});
