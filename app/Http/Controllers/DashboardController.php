<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Project;
use Inertia\Inertia;

class DashboardController extends Controller {
  public function show() {

    $user = Auth::user();

    if ($user->hasRole(['superadmin', 'admin'])) {
      $latestProjects = Project::latest()->take(3)->get();
      return Inertia::render('Dashboard', [
        'projects' => $latestProjects,
      ]);
    } else if ($user->hasRole('client')) {
      return Inertia::render('DashboardClient', []);
    } else {
      return Inertia::render('DashboardEmployee', []);
    }
  }
}
