<?php

use App\Http\Controllers\EmployeesController;
use App\Http\Controllers\TimesheetController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PCAReportController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\OrganizationController;
use App\Http\Controllers\InvoiceController;

Route::get('/', function () {
  if (Auth::check()) {
    return redirect('/dashboard');
  }
  return Inertia::render('Welcome');
})->name('home');


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


Route::middleware(['auth', 'verified'])->group(function () {
  // Route::group(['middleware' => ['role:superadmin|admin']], function () {
  //   Route::get('/dashboard', [DashboardController::class, 'showAdmin'])->name('dashboard');
  // });

  Route::get('/dashboard', [DashboardController::class, 'show'])->name('dashboard');

  Route::resource('projects', ProjectController::class);
  Route::delete('/projects/{project}/remove-employee/{userId}', [ProjectController::class, 'removeEmployee'])->name('projects.employee.remove');

  Route::resource('clients', ClientController::class);

  Route::resource('pca-reports', PCAReportController::class);

  Route::resource('timesheets', TimesheetController::class);

  Route::resource('invoices', InvoiceController::class);

  Route::post('/organizations', [OrganizationController::class, 'store'])->name('organizations.store');

  Route::resource('employees', EmployeesController::class)->only(['index']); // Keep only index for now if others aren't used
  Route::post('/employees/invite', [EmployeesController::class, 'invite'])->name('employees.invite');

  Route::get('users/{id}', [RegisteredUserController::class, 'show']);
  Route::patch('users/{id}', [RegisteredUserController::class, 'update'])->name('users.update');

  // User switching routes for superadmin
  Route::get('admin/user/switch/start/{id}', [RegisteredUserController::class, 'user_switch_start']);
  Route::get('admin/user/switch/stop', [RegisteredUserController::class, 'user_switch_stop']);
});

require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';
