<?php

use App\Http\Controllers\OrganizationController;
use App\Http\Controllers\Settings\PasswordController;
use App\Http\Controllers\Settings\ProfileController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::middleware('auth')->group(function () {
    Route::redirect('settings', '/settings/profile');

    Route::get('settings/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('settings/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('settings/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('settings/organization', [OrganizationController::class, 'edit'])->name('organization.edit');
    Route::patch('settings/organization/{organization}', [OrganizationController::class, 'update'])->name('organization.update');
    Route::get('settings/branding', [OrganizationController::class, 'editBranding'])->name('organization.editBranding');
    Route::post('settings/branding/{organization}', [OrganizationController::class, 'updateBranding'])->name('organization.branding.update');

    Route::get('settings/password', [PasswordController::class, 'edit'])->name('password.edit');
    Route::put('settings/password', [PasswordController::class, 'update'])->name('password.update');

    Route::get('settings/appearance', function () {
        return Inertia::render('settings/Appearance');
    })->name('appearance');
});
