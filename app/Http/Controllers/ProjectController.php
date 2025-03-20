<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Inertia\Inertia;

class ProjectController extends Controller {
  public function index() {
    $projects = Project::all();

    return Inertia::render('projects/Index', [
      'projects' => $projects,
    ]);
  }

  public function latestProjects() {
    $latestProjects = Project::latest()->take(3)->get();

    return Inertia::render('Dashboard', [
      'projects' => $latestProjects,
    ]);
  }

  public function show($id) {
    $project = Project::with('client')->find($id);


    return Inertia::render('projects/Show', [
      'project' => $project,
    ]);
  }
}
