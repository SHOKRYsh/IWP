<?php

use Illuminate\Support\Facades\Route;
use Modules\Auth\Http\Controllers\AuthController;
use Modules\Auth\Http\Controllers\RoleController;
use Modules\Auth\Http\Controllers\UserController;
use Illuminate\Support\Facades\Artisan;

Route::prefix('auth/')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('forget-password', [AuthController::class, 'forgetPassword']);
    Route::post('check-otp', [AuthController::class, 'checkPhoneOTPForgetPassword']);
    Route::post('resend-otp', [AuthController::class, 'resendOtp']);
    Route::post('google', [AuthController::class, 'redirectToGoogle']);
    Route::get('google/callback', [AuthController::class, 'handleGoogleCallback']);
});

Route::middleware('auth:sanctum')->group(function () {

    // AuthController
    Route::post('auth/logout', [AuthController::class, 'logout']);
    Route::post('auth/reset-password', [AuthController::class, 'resetPassword']);

    // UserController
    Route::get('user/profile', [UserController::class, 'showProfile']);
    Route::post('user/update-profile', [UserController::class, 'updateProfile']);
    Route::post('user/change-password', [UserController::class, 'changePassword']);
    Route::delete('user/delete-account', [UserController::class, 'deleteUser'])->middleware('role:User');

    Route::get('/users', [UserController::class, 'getAllUsers'])->middleware('role:Admin');
});

Route::get('run-seeder',function(){
    Artisan::call('db:seed', [
             '--class' => 'Database\\Seeders\\DatabaseSeeder'
     ]);
     return response()->json(['message' => 'Seeder run successfully']);
 });
