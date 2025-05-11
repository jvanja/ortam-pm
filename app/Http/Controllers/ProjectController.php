<?php

namespace App\Http\Controllers;

use App\Models\ProjectPipelineStage;
use Illuminate\Http\Request;
use App\Models\Project;
use Inertia\Inertia;
use Illuminate\Database\Eloquent\Builder;

class ProjectController extends Controller {
  /**
   * Display a listing of the resource.
   */
  public function index(Request $request) {
    $this->authorize('project.view', Project::class);

    $searchQuery = $request->input('search');
    $managerFilter = $request->input('manager') === 'all' ? '' : $request->input('manager');
    $statusFilter = $request->input('status') === 'all' ? '' : $request->input('status');

    // Fetch distinct managers and statuses for filter options
    $managers = Project::distinct()->pluck('manager')->filter()->sort()->values()->toArray();
    $statuses = Project::distinct()->pluck('status')->filter()->sort()->values()->toArray();


    $projects = Project::query()
      ->when($searchQuery, function (Builder $query, string $search) {
        // Search in 'type', 'department', 'address', etc. Adjust fields as needed.
        $query->where(function (Builder $subQuery) use ($search) {
          $subQuery->where('type', 'like', "%{$search}%")
            ->orWhere('department', 'like', "%{$search}%")
            ->orWhere('address', 'like', "%{$search}%");
        });
      })
      ->when($managerFilter, function (Builder $query, string $manager) {
        $query->where('manager', $manager);
      })
      ->when($statusFilter, function (Builder $query, string $status) {
        $query->where('status', $status);
      })
      ->latest()
      ->paginate(10);

    return Inertia::render('projects/Index', [
      'projects' => [
        'data' => $projects->items(),
        'meta' => [
          'current_page' => $projects->currentPage(),
          'first_page_url' => $projects->url(1),
          'from' => $projects->firstItem(),
          'last_page' => $projects->lastPage(),
          'last_page_url' => $projects->url($projects->lastPage()),
          'links' => $projects->linkCollection(),
          'next_page_url' => $projects->nextPageUrl(),
          'path' => $projects->path(),
          'per_page' => $projects->perPage(),
          'prev_page_url' => $projects->previousPageUrl(),
          'to' => $projects->lastItem(),
          'total' => $projects->total(),
        ]
      ],
      'filters' => [
        'search' => $searchQuery,
        'manager' => $managerFilter,
        'status' => $statusFilter,
      ],
      'managers' => $managers,
      'statuses' => $statuses,
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

    $project = Project::with(['pipelineStages', 'currentPipelineStage'])->findOrFail($id);
    if ($project->pipelineStages->count() == 0) {
      $project->pipelineStages = ProjectPipelineStage::where('is_system_default', '=', true)->get();
    }
    $employees = $project->users()->get();
    $invoices = $project->invoices()->get();
    $client = $project->client()->first();

    return Inertia::render('projects/Show', [
      'project' => $project,
      'client' => $client,
      'employees' => $employees,
      'invoices' => $invoices,
      'pipelineStages' => $project->pipelineStages,
      'currentPipelineStage' => $project->currentPipelineStage,
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
