<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
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
    $validated = $request->validate([
      'project_type' => 'required|string',
      'department' => 'required|string',
      'project_manager' => 'required|string',
      'project_language' => 'required|string',
      'project_address' => 'required|string',
      'project_status' => 'required|string',
      'project_opening_date' => 'required|date',
      'budget' => 'required|numeric',
      'sales_representative_name' => 'required|string',
      'deadline' => 'required|date',
      'client_id' => 'nullable|exists:clients,id'
    ]);

    dd($validated);
    $project = Project::create($validated);

    // return redirect()->back()->with('success', 'Project created successfully');
    return redirect()->route('projects.show', $project->id)
      ->with('message', 'Project created successfully');
  }

  /**
   * Update the specified resource in storage.
   */
  public function update(Request $request, $id) {
    $project = Project::findOrFail($id);

    $validated = $request->validate([
      'project_type' => 'sometimes|string',
      'department' => 'sometimes|string',
      'project_manager' => 'sometimes|string',
      'project_language' => 'sometimes|string',
      'project_address' => 'sometimes|string',
      'project_status' => 'sometimes|string',
      'project_opening_date' => 'sometimes|date',
      'budget' => 'sometimes|numeric',
      'sales_representative_name' => 'sometimes|string',
      'deadline' => 'sometimes|date',
      'client_id' => 'nullable|exists:clients,id'
    ]);

    $project->update($validated);

    return redirect()->back()->with('success', 'Project updated successfully');
  }

  /**
   * Remove the specified resource from storage.
   */
  public function destroy($id) {
    $project = Project::findOrFail($id);
    $project->delete();

    return redirect()->back()->with('success', 'Project deleted successfully');
  }
}
