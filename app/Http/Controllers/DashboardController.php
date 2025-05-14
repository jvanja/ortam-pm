<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Project;
use App\Models\ProjectPipelineStage;
use Inertia\Inertia;

class DashboardController extends Controller {
  public function show() {

    $user = Auth::user();
    if(!$user->organization_id) {
      return Inertia::render('dashboard/FirstTime');
    }

    if ($user->hasRole(['superadmin', 'admin'])) {
      $defaultStages = ProjectPipelineStage::where('is_system_default', '1')->get();
      $latestProjects = Project::with(['pipelineStages', 'currentPipelineStage'])->latest()->take(3)->get();
      foreach($latestProjects as $project) {
        if(count($project->pipelineStages) == 0) {
          $project->pipelineStages = $defaultStages;
        }
      }
      return Inertia::render('Dashboard', [
        'projects' => $latestProjects,
      ]);
    } else if ($user->hasRole('client')) {
      // Fetch projects related to the client if needed for DashboardClient
      // $clientProjects = Project::where('client_id', $user->client_id)->get(); // Example if client relationship exists
      return Inertia::render('DashboardClient', [
        // 'projects' => $clientProjects,
      ]);
    } else {
      // Employee role: Fetch projects assigned to this user
      $employeeProjects = $user->projects()->get(['id', 'address']); // Fetch only needed columns (id for value, address for display)

      return Inertia::render('DashboardEmployee', [
        'projects' => $employeeProjects,
        // No need to pass organizations anymore
      ]);
    }
  }
}
