<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;
use App\Http\Controllers\DashboardController;

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

require base_path('routes/projects.php');
require base_path('routes/pca.php');

require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';
