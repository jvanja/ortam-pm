<?php

use App\Http\Controllers\ProjectController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->group(function() {
    // Route::post('projects', [ProjectController::class, 'store'])->name('projects.store');
    Route::resource('projects', ProjectController::class); //->except(['store']);
});
