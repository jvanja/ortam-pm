<?php

use App\Http\Controllers\PCAReportController;
use Illuminate\Support\Facades\Route;

Route::prefix('pca-reports')->group(function () {
    Route::get('/', [PCAReportController::class, 'index'])->middleware(['auth', 'verified']);
    Route::get('/{id}', [PCAReportController::class, 'show'])->middleware(['auth', 'verified']);
});
