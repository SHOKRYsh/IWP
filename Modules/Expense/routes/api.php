<?php

use Illuminate\Support\Facades\Route;
use Modules\Expense\Http\Controllers\ExpenseController;

Route::middleware(['auth:sanctum'])->prefix('expenses')->group(function () {
    Route::get('/budget', [ExpenseController::class, 'getBudget']);
    Route::post('/budget', [ExpenseController::class, 'setBudget']);
    
    Route::get('/', [ExpenseController::class, 'index']);
    Route::post('/', [ExpenseController::class, 'store']);
    Route::get('/analysis', [ExpenseController::class, 'getAnalysis']);
});
