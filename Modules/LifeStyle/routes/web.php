<?php

use Illuminate\Support\Facades\Route;
use Modules\LifeStyle\Http\Controllers\LifeStyleController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('lifestyles', LifeStyleController::class)->names('lifestyle');
});
