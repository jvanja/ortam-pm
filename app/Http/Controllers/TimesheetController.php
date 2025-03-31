<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Timesheet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class TimesheetController extends Controller {
  /**
   * Display a listing of the resource.
   */
  public function index() {
    $this->authorize('timesheet.view', Timesheet::class);

    $user = Auth::user();

    $timesheets = $user->timesheets()->with('project')->latest()->limit(10)->get();

    return Inertia::render('timesheets/Index', [
      'timesheets' => $timesheets,
    ]);
  }

  /**
   * Store a newly created resource in storage.
   */
  public function store(Request $request) {
    $this->authorize('timesheet.create', Timesheet::class);

    $user = Auth::user();

    $validated = $request->validate([
      'task_performed' => ['required', 'string', Rule::in(['visit', 'research', 'fieldwork', 'report'])],
      'worked_duration' => 'required|integer|min:1',
      // Validate that the project exists and the user is assigned to it
      'project_id' => [
        'required',
        'string',
        Rule::exists('projects', 'id')->where(function ($query) use ($user) {
            // Ensure the project is one the user is assigned to via project_user pivot table
            $query->whereExists(function ($subQuery) use ($user) {
                $subQuery->select(\DB::raw(1))
                         ->from('project_user')
                         ->whereColumn('projects.id', 'project_user.project_id')
                         ->where('project_user.user_id', $user->id);
            });
        }),
      ],
      'details' => 'nullable|string|max:65535', // Optional details field
      // user_id is added below from authenticated user
    ]);

    // Fetch the project to get the organization_id
    $project = Project::findOrFail($validated['project_id']);

    Timesheet::create([
      'task_performed' => $validated['task_performed'],
      'worked_duration' => $validated['worked_duration'],
      'project_id' => $validated['project_id'],
      'organization_id' => $project->organization_id, // Get org ID from the project
      'details' => $validated['details'] ?? null, // Add details, defaulting to null if empty
      'user_id' => $user->id,
    ]);

    // Redirect back to the dashboard
    return redirect()->back()->with('success', 'Timesheet entry added successfully.');
  }

  /**
   * Display the specified resource.
   */
  public function show(string $id) {
    // Find the timesheet by ID, including project and user details
    $timesheet = Timesheet::with(['project', 'user'])->findOrFail($id);

    // Authorize that the current user can view this specific timesheet
    // Adjust authorization logic as needed (e.g., user owns it, or is admin/manager)
    $this->authorize('timesheet.view', $timesheet);

    return Inertia::render('timesheets/Show', [
      'timesheet' => $timesheet,
    ]);
  }

  /**
   * Update the specified resource in storage.
   */
  public function update(Request $request, string $id) {
    //
  }

  /**
   * Remove the specified resource from storage.
   */
  public function destroy(string $id) {
    //
  }
}
