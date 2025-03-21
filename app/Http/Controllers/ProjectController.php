<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Inertia\Inertia;

class ProjectController extends Controller {
  /**
   * Display a listing of the resource.
   */
  public function index() {
    $projects = Project::all();

    return Inertia::render('projects/Index', [
      'projects' => $projects,
    ]);
  }

  /**
   * Get the latest 3 projects for the dashboard
   */
  public function latestProjects() {
    $latestProjects = Project::latest()->take(3)->get();

    return Inertia::render('Dashboard', [
      'projects' => $latestProjects,
    ]);
  }

  /**
   * Display the specified resource.
   */
  public function show($id) {
    $project = Project::with('client')->find($id);


    return Inertia::render('projects/Show', [
      'project' => $project,
    ]);
  }
  /**
   * Store a newly created resource in storage.
   */
  public function store(Request $request) {
    //
  }
}
