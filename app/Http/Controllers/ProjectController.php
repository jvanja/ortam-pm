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
    $this->authorize('project.view', Project::class);

    $projects = Project::all();

    return Inertia::render('projects/Index', [
      'projects' => $projects,
    ]);
  }

  /**
   * Get the latest 3 projects for the dashboard
   */
  public function latestProjects() {
    $this->authorize('project.view', Project::class);
    $latestProjects = Project::latest()->take(3)->get();

    return Inertia::render('Dashboard', [
      'projects' => $latestProjects,
    ]);
  }

  /**
   * Display the specified resource.
   */
  public function show($id) {
    $this->authorize('project.edit', Project::class);

    $project = Project::find($id);
    $employees = $project->users()->get();
    $client = $project->client()->first();

    return Inertia::render('projects/Show', [
      'project' => $project,
      'client' => $client,
      'employees' => $employees,
    ]);
  }

  /**
   * Store a newly created resource in storage.
   */
  public function store(Request $request) {

    $this->authorize('project.create', Project::class);

    $validated = $request->validate([
      'type' => 'required|string',
      'department' => 'required|string',
      'manager' => 'required|string',
      'language' => 'required|string',
      'address' => 'required|string',
      'status' => 'required|string',
      'opening_date' => 'required|date',
      'budget' => 'required|numeric',
      'sales_representative_name' => 'required|string',
      'deadline' => 'required|date',
      'client_id' => 'nullable|exists:clients,id'
    ]);

    $project = Project::create($validated);

    return redirect()->route('projects.show', $project->id)
      ->with('message', 'Project created successfully');
  }

  /**
   * Update the specified resource in storage.
   */
  public function update(Request $request, $id) {
    $this->authorize('project.edit', Project::class);

    $project = Project::findOrFail($id);

    $validated = $request->validate([
      'type' => 'sometimes|string',
      'department' => 'sometimes|string',
      'manager' => 'sometimes|string',
      'language' => 'sometimes|string',
      'address' => 'sometimes|string',
      'status' => 'sometimes|string',
      'opening_date' => 'sometimes|date',
      'budget' => 'sometimes|numeric',
      'currency' => 'sometimes|string',
      'sales_representative_name' => 'sometimes|string',
      'deadline' => 'sometimes|date',
      'client_id' => 'nullable|exists:clients,id'
    ]);

    $project->update($validated);

    return redirect()->back()->with('success', 'Project updated successfully');
  }

  /**
   * Remove an employee from the project.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \App\Models\Project  $project
   * @param  int|string  $userId
   * @return \Illuminate\Http\RedirectResponse
   */
  public function removeEmployee(Request $request, Project $project, $userId) {

    $this->authorize('project.edit', Project::class);

    // Check if the employee is assigned to the project
    if ($project->users()->where('user_id', $userId)->exists()) {
      $project->users()->detach($userId);

      return redirect()->back()->with('success', 'Employee removed successfully.');
    }

    return redirect()->back()->with('error', 'Employee not found in project.');
  }

  /**
   * Remove the specified resource from storage.
   */
  public function destroy($id) {
    $this->authorize('project.delete', Project::class);
    $project = Project::findOrFail($id);
    $project->delete();

    return redirect()->back()->with('success', 'Project deleted successfully');
  }
}
