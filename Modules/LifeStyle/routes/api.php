<?php

use Illuminate\Support\Facades\Route;
use Modules\LifeStyle\Http\Controllers\LifeStyleController;
use Modules\LifeStyle\Http\Controllers\LifeElementController;

Route::get('/life-style', [LifeStyleController::class, 'index']);
Route::get('/life-style/{id}', [LifeStyleController::class, 'show'])->where('id', '[0-9]+');

Route::get('/life-style/elements', [LifeElementController::class, 'index']);
Route::get('/life-style/elements/{id}', [LifeElementController::class,'show'])->where('id', '[0-9]+');

Route::middleware(['auth:sanctum'])->prefix('life-style')->group(function () {
    // LifeStyle Routes
    Route::middleware('role:Admin')->group(function () {
        Route::post('/', [LifeStyleController::class, 'store']);
        Route::put('/{id}', [LifeStyleController::class, 'update']);
        Route::delete('/{id}', [LifeStyleController::class, 'destroy']);
    });

    // LifeElement Routes
    Route::post('/elements', [LifeElementController::class, 'store']);
    Route::put('/elements/{id}', [LifeElementController::class, 'update']);
    Route::delete('/elements/{id}', [LifeElementController::class, 'destroy']);

    // LifeTaskType Routes
    Route::post('/elements/{element_id}/task-types', [LifeElementController::class, 'storeTaskType']);
    Route::put('/elements/task-types/{id}', [LifeElementController::class, 'updateTaskType']);
    Route::delete('/elements/task-types/{id}', [LifeElementController::class, 'destroyTaskType']);
});
