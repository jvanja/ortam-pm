<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\Auth\RegisteredUserController;

Route::get('/', function () {
  if (Auth::check()) {
    return redirect('/dashboard');
  }
  return Inertia::render('Welcome');
})->name('home');

Route::get('/dashboard', [DashboardController::class, 'show'])->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/translations/{locale}', function ($locale) {
  $path = base_path("lang/{$locale}.json");
  if (!File::exists($path)) {
    abort(404);
  }
  return response()->json(json_decode(File::get($path), true));
});

Route::get('/set-locale/{locale}', function ($locale, Request $request) {
  $request->session()->put('locale', $locale);
  return redirect()->back();
});

// User switching routes
Route::get( 'admin/user/switch/start/{id}', [RegisteredUserController::class, 'user_switch_start']);
Route::get( 'admin/user/switch/stop', [RegisteredUserController::class, 'user_switch_stop'] );

Route::middleware(['auth', 'verified'])->group(function () {
  Route::resource('projects', ProjectController::class); //->except(['store']);
});

// - TODO:
// Maybe just have them all here instead of requiring
require base_path('routes/clients.php');
require base_path('routes/pca.php');

require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';
