<?php

use App\Http\Controllers\PCAReportController;
use Illuminate\Support\Facades\Route;

Route::prefix('pca-reports')->group(function () {
  Route::get('/', [PCAReportController::class, 'index'])->middleware(['auth', 'verified']);
  Route::get('/{id}', [PCAReportController::class, 'show'])->middleware(['auth', 'verified']);
  Route::patch('/{id}', [PCAReportController::class, 'update'])->middleware(['auth', 'verified'])->name('pca_reports.update');
});
