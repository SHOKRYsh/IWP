<?php

use Illuminate\Support\Facades\Route;
use Modules\Subscription\Http\Controllers\SubscriptionController;
use Modules\Subscription\Http\Controllers\PlanController;
use Modules\Subscription\Http\Controllers\BillingController;

Route::middleware(['auth:sanctum'])->prefix('subscriptions')->group(function () {
    Route::get('/', [SubscriptionController::class, 'getAllSubscriptions']);
    Route::get('/status', [SubscriptionController::class, 'status']);
    Route::get('/billing/history', [BillingController::class, 'index']);

    Route::middleware(['role:Admin'])->group(function () {
        Route::post('/admin', [SubscriptionController::class, 'adminSubscription']);
    });
});

Route::middleware(['auth:sanctum'])->prefix('plan')->group(function () {
    Route::get('/', [PlanController::class,'index']);
    Route::get('/{id}', [PlanController::class,'show']);

    Route::middleware(['role:Admin'])->group(function () {
        Route::post('/', [PlanController::class,'store']);
        Route::put('/{id}', [PlanController::class,'update']);
        Route::delete('/{id}', [PlanController::class,'destroy']);
    });
});