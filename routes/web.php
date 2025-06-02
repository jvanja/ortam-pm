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
use App\Http\Controllers\ProjectPipelineStageController;
use App\Http\Controllers\ProposalController;
use App\Http\Controllers\FileController;

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
  Route::post('/projects/{project}/add-employee/{userId}', [ProjectController::class, 'addEmployee'])->name('projects.employee.add');
  Route::post('/projects/{project}/uploadFile', [ProjectController::class, 'uploadFile'])->name('projects.file.add');

  // Project Pipeline Stage Routes
  Route::patch('/projects/{project}/pipeline-stages/order', [ProjectPipelineStageController::class, 'updateOrder'])->name('projects.pipeline-stages.updateOrder');
  Route::patch('/projects/{project}/pipeline-stages/{stage}/set-current', [ProjectPipelineStageController::class, 'setCurrent'])->name('projects.pipeline-stages.setCurrent');
  Route::patch('/projects/pipeline-stages/{stage}/update', [ProjectPipelineStageController::class, 'update'])->name('projects.pipeline-stages.update');
  Route::delete('/projects/{project}/pipeline-stages/{stage}', [ProjectPipelineStageController::class, 'destroy'])->name('projects.pipeline-stages.destroy');
  Route::post('/projects/{project}/pipeline-stages', [ProjectPipelineStageController::class, 'store'])->name('projects.pipeline-stages.store');

  // File Routes
  Route::post('/files/{model}/{uuid}/store', [FileController::class, 'store'])->name('files.store');
  Route::delete('/files/{file}', [FileController::class, 'destroy'])->name('files.destroy');

  Route::resource('clients', ClientController::class);

  Route::resource('pca-reports', PCAReportController::class);

  Route::resource('timesheets', TimesheetController::class);

  // Proposals
  Route::resource('proposals', ProposalController::class);

  // Invoices
  Route::resource('invoices', InvoiceController::class);
  Route::post('/invoices/{invoice}/send', [InvoiceController::class, 'send'])->name('invoices.send');

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
