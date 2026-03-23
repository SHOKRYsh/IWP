<?php

use Illuminate\Support\Facades\Route;
use Modules\Task\Http\Controllers\HabitController;
use Modules\Task\Http\Controllers\TaskController;

Route::middleware(['auth:sanctum'])->prefix('tasks')->group(function () {
    Route::get('/daily-progress', [TaskController::class, 'getDailyProgress']);

    // Habit Routes
    Route::prefix('habits')->group(function () {
        Route::get('/', [HabitController::class, 'index']);
        Route::post('/', [HabitController::class, 'store']);
        Route::get('/{id}', [HabitController::class, 'show']);
        Route::put('/{id}', [HabitController::class, 'update']);
        Route::delete('/{id}', [HabitController::class, 'destroy']);
        Route::post('/{id}/complete', [HabitController::class, 'complete']);
    });

    // Task Routes
    Route::get('/', [TaskController::class, 'index']);
    Route::post('/', [TaskController::class, 'store']);
    Route::get('/{id}', [TaskController::class, 'show']);
    Route::put('/{id}', [TaskController::class, 'update']);
    Route::delete('/{id}', [TaskController::class, 'destroy']);
    Route::post('/{id}/complete', [TaskController::class, 'complete']);
});
