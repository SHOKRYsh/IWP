<?php

use Illuminate\Support\Facades\Route;
use Modules\Settings\Http\Controllers\SettingsController;

Route::prefix('settings')->group(function () {
    Route::get('/', [SettingsController::class, 'index']);
    Route::put('/', [SettingsController::class, 'update'])->middleware(['auth:sanctum', 'role:Admin']);
});
