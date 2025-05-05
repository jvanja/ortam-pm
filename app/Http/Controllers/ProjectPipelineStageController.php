<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectPipelineStage;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ProjectPipelineStageController extends Controller {
  /**
   * Store a newly created pipeline stage for a project.
   */
  public function store(Request $request, Project $project) {
    // Assuming a policy exists for 'project.edit'
    $this->authorize('project.edit', $project);

    $validated = $request->validate([
      'name' => 'required|string|max:255',
    ]);

    // Determine the next stage order
    $maxOrder = $project->pipelineStages()->max('stage_order');
    $nextOrder = $maxOrder !== null ? $maxOrder + 1 : 1;

    $stage = $project->pipelineStages()->create([
      'name' => $validated['name'],
      'stage_order' => $nextOrder,
      'is_system_default' => false, // Assuming new stages are not system defaults
      'status' => 'pending', // Default status for a new stage
      'notes' => null,
    ]);

    // Redirect back to the project show page, Inertia will handle the reload
    return redirect()->back()->with('success', 'Pipeline stage added successfully.');
  }

  /**
   * Update the order of pipeline stages for a project.
   */
  public function updateOrder(Request $request, Project $project) {
    // Assuming a policy exists for 'project.edit'
    $this->authorize('project.edit', $project);

    $validated = $request->validate([
      'stage_ids' => 'required|array',
      'stage_ids.*' => 'string|exists:project_pipeline_stages,id',
    ]);

    $stageIds = $validated['stage_ids'];

    // ProjectPipelineStage::where('project_id', $project->id)->delete();
    foreach ($stageIds as $index => $stageId) {
      $stage = ProjectPipelineStage::find($stageId);
      if ($stage->is_system_default != true) {
        $stage->delete();
      }
      $newStage = $stage->replicate();
      $newStage->project_id = $project->id;
      $newStage->is_system_default = false;
      $newStage->stage_order = $index * 10;
      $newStage->save();

      // update current pipeline stage with the new stage id
      if ($project->currentPipelineStage->id == $stage->id) {
        $project->update(['current_project_pipeline_stage_id' => $newStage->id]);
      }
    }

    return redirect()->back()->with('success', 'Pipeline stage added successfully.');
  }

  /**
   * Set the current pipeline stage for a project.
   */
  public function setCurrent(Request $request, Project $project, ProjectPipelineStage $stage) {
    // Assuming a policy exists for 'project.edit'
    $this->authorize('project.edit', $project);

    // Ensure the stage belongs to the project
    if (!$stage->is_system_default && $stage->project_id != $project->id) {
      throw ValidationException::withMessages(['stage' => 'The selected stage does not belong to this project. And it\'s not a default stage.']);
    }

    // Optional: Add logic here to mark previous stage as completed if needed
    // $currentStage = $project->currentPipelineStage;
    // if ($currentStage && $currentStage->id !== $stage->id) {
    //     $currentStage->update(['status' => 'completed', 'completed_at' => now()]);
    // }

    $project->update(['current_project_pipeline_stage_id' => $stage->id]);

    // Redirect back to the project show page, Inertia will handle the reload
    return redirect()->back()->with('success', 'Current pipeline stage updated successfully.');
  }

  /**
   * Remove the specified pipeline stage from storage.
   */
  public function destroy(Project $project, ProjectPipelineStage $stage) {
    // Assuming a policy exists for 'project.edit'
    $this->authorize('project.edit', $project);

    // Ensure the stage belongs to the project
    if ($stage->project_id !== $project->id) {
      throw ValidationException::withMessages(['stage' => 'The selected stage does not belong to this project.']);
    }

    // Prevent deleting the current stage directly? Or handle it?
    // For now, if the deleted stage was the current one, set project's current_project_pipeline_stage_id to null.
    if ($project->current_project_pipeline_stage_id === $stage->id) {
      $project->update(['current_project_pipeline_stage_id' => null]);
    }

    $stage->delete();

    // Reorder remaining stages? This might be complex.
    // A simpler approach is to just delete and let the next drag reorder.
    // Or, fetch stages again after deletion to get the correct order.
    // Let's rely on fetching updated stages after redirect for simplicity.

    // Redirect back to the project show page, Inertia will handle the reload
    return redirect()->back()->with('success', 'Pipeline stage deleted successfully.');
  }
}
