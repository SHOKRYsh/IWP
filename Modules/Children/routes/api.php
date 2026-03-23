<?php

use Illuminate\Support\Facades\Route;
use Modules\Children\Http\Controllers\ChildrenController;

Route::middleware(['auth:sanctum'])->prefix('children')->group(function () {
    Route::get('/', [ChildrenController::class, 'index']);
    Route::get('/{id}', [ChildrenController::class, 'show']);
    
    Route::middleware('role:User')->group(function()
    {
        Route::post('/', [ChildrenController::class, 'store']);
        Route::put('/{id}', [ChildrenController::class, 'update']);
        Route::delete('/{id}', [ChildrenController::class, 'destroy']);
    });
});
