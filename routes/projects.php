<?php

use App\Http\Controllers\ProjectController;
use Illuminate\Support\Facades\Route;

Route::prefix('projects')->group(function () {
    Route::get('/', [ProjectController::class, 'index'])->middleware(['auth', 'verified']);
    Route::get('/{id}', [ProjectController::class, 'show'])->middleware(['auth', 'verified']);
});
