<?php

namespace Tests\Feature;

use App\Models\Organization;
use App\Models\Project;
use App\Models\ProjectPipelineStage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class PipelineTest extends TestCase {
  use RefreshDatabase;
  use WithFaker;

  protected User $user;
  protected Project $project;

  protected function setUp(): void {
    parent::setUp();

    // Create a user and authenticate
    $this->user = User::factory()->create();
    // set up role and permissions
    $adminRole = Role::findOrCreate('admin');
    $permission = Permission::create(['name' => 'project.edit']);
    $adminRole->givePermissionTo($permission);
    $this->user->assignRole('admin');


    $this->actingAs($this->user);

    // Needed to create a project
    Organization::factory()->create();

    // Create a project for the user
    $this->project = Project::factory()->create();
  }

  /**
   * Test a user can add a pipeline stage to a project.
   */
  public function test_user_can_add_pipeline_stage_to_project(): void {
    $stageName = 'Initial Stage';

    $response = $this->post(route('projects.pipeline-stages.store', $this->project), [
      'name' => $stageName,
    ]);

    $response->assertRedirect(); // Inertia redirect back
    $response->assertSessionHas('success', 'Pipeline stage added successfully.');

    // Assert the first stage is created with order 1
    $this->assertDatabaseHas('project_pipeline_stages', [
      'project_id' => $this->project->id,
      'name' => $stageName,
      'stage_order' => 1,
      'is_system_default' => false,
      'status' => 'pending',
    ]);

    // Add a second stage to check order increment
    $stageName2 = 'Second Stage';
    $response2 = $this->post(route('projects.pipeline-stages.store', $this->project), [
      'name' => $stageName2,
    ]);
    $response2->assertRedirect();

    // Assert the second stage is created with order 2
    $this->assertDatabaseHas('project_pipeline_stages', [
      'project_id' => $this->project->id,
      'name' => $stageName2,
      'stage_order' => 2,
    ]);
  }

  /**
   * Test a user can reorder pipeline stages for a project.
   * Note: The controller replicates non-system default stages, changing their IDs.
   */
  public function test_user_can_reorder_pipeline_stages(): void {
    // Create initial stages
    $stageA = ProjectPipelineStage::create(['project_id' => $this->project->id, 'name' => 'Stage A', 'stage_order' => 10, 'is_system_default' => false]);
    $stageB = ProjectPipelineStage::create(['project_id' => $this->project->id, 'name' => 'Stage B', 'stage_order' => 20, 'is_system_default' => false]);
    $stageC = ProjectPipelineStage::create(['project_id' => $this->project->id, 'name' => 'Stage C', 'stage_order' => 30, 'is_system_default' => false]);

    // New order using original IDs: C, A, B
    $newOrderIds = [$stageC->id, $stageA->id, $stageB->id];

    $response = $this->patch(route('projects.pipeline-stages.updateOrder', $this->project), [
      'stage_ids' => $newOrderIds,
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('success', 'Pipeline stage reordered successfully.');

    // Assert old stages are deleted (non-system default)
    $this->assertDatabaseMissing('project_pipeline_stages', ['id' => $stageA->id]);
    $this->assertDatabaseMissing('project_pipeline_stages', ['id' => $stageB->id]);
    $this->assertDatabaseMissing('project_pipeline_stages', ['id' => $stageC->id]);

    // Fetch the new stages for this project, ordered by the new stage_order
    $newStages = $this->project->pipelineStages()->orderBy('stage_order')->get();

    $this->assertCount(3, $newStages);

    // Assert new stages exist with correct names, project_id, is_system_default=false, and order
    $this->assertEquals('Stage C', $newStages[0]->name);
    $this->assertEquals($this->project->id, $newStages[0]->project_id);
    $this->assertFalse($newStages[0]->is_system_default);
    $this->assertEquals(0, $newStages[0]->stage_order); // Controller uses index * 10

    $this->assertEquals('Stage A', $newStages[1]->name);
    $this->assertEquals($this->project->id, $newStages[1]->project_id);
    $this->assertFalse($newStages[1]->is_system_default);
    $this->assertEquals(10, $newStages[1]->stage_order);

    $this->assertEquals('Stage B', $newStages[2]->name);
    $this->assertEquals($this->project->id, $newStages[2]->project_id);
    $this->assertFalse($newStages[2]->is_system_default);
    $this->assertEquals(20, $newStages[2]->stage_order);
  }

  /**
   * Test a user can set the current pipeline stage for a project.
   */
  public function test_user_can_set_current_pipeline_stage(): void {
    // Create stages
    $stageA = ProjectPipelineStage::create(['project_id' => $this->project->id, 'name' => 'Stage A', 'stage_order' => 10, 'is_system_default' => false]);
    $stageB = ProjectPipelineStage::create(['project_id' => $this->project->id, 'name' => 'Stage B', 'stage_order' => 20, 'is_system_default' => false]);

    // Ensure project starts with no current stage
    $this->assertNull($this->project->current_project_pipeline_stage_id);

    // Set Stage B as current
    $response = $this->patch(route('projects.pipeline-stages.setCurrent', [$this->project, $stageB]));

    $response->assertRedirect();
    $response->assertSessionHas('success', 'Current pipeline stage updated successfully.');

    // Refresh project model to get the updated current stage ID
    $this->project->refresh();
    $this->assertEquals($stageB->id, $this->project->current_project_pipeline_stage_id);

    // Set Stage A as current
    $response2 = $this->patch(route('projects.pipeline-stages.setCurrent', [$this->project, $stageA]));

    $response2->assertRedirect();
    $response2->assertSessionHas('success', 'Current pipeline stage updated successfully.');

    // Refresh project model
    $this->project->refresh();
    $this->assertEquals($stageA->id, $this->project->current_project_pipeline_stage_id);
  }

  /**
   * Test a user can delete a pipeline stage from a project.
   */
  public function test_user_can_delete_pipeline_stage(): void {
    // Create stages
    $stageA = ProjectPipelineStage::create(['project_id' => $this->project->id, 'name' => 'Stage A', 'stage_order' => 10, 'is_system_default' => false]);
    $stageB = ProjectPipelineStage::create(['project_id' => $this->project->id, 'name' => 'Stage B', 'stage_order' => 20, 'is_system_default' => false]);

    // Assert both exist
    $this->assertDatabaseHas('project_pipeline_stages', ['id' => $stageA->id]);
    $this->assertDatabaseHas('project_pipeline_stages', ['id' => $stageB->id]);

    // Delete Stage A
    $response = $this->delete(route('projects.pipeline-stages.destroy', [$this->project, $stageA]));

    $response->assertRedirect();
    $response->assertSessionHas('success', 'Pipeline stage deleted successfully.');

    // Assert Stage A is gone and Stage B still exists
    $this->assertDatabaseMissing('project_pipeline_stages', ['id' => $stageA->id]);
    $this->assertDatabaseHas('project_pipeline_stages', ['id' => $stageB->id]);
  }

  /**
   * Test deleting the current stage sets the project's current stage to null.
   */
  public function test_deleting_current_stage_sets_project_current_stage_to_null(): void {
    // Create stages
    $stageA = ProjectPipelineStage::create(['project_id' => $this->project->id, 'name' => 'Stage A', 'stage_order' => 10, 'is_system_default' => false]);
    $stageB = ProjectPipelineStage::create(['project_id' => $this->project->id, 'name' => 'Stage B', 'stage_order' => 20, 'is_system_default' => false]);

    // Set Stage A as current
    $this->project->update(['current_project_pipeline_stage_id' => $stageA->id]);
    $this->assertEquals($stageA->id, $this->project->current_project_pipeline_stage_id);

    // Delete Stage A (the current one)
    $response = $this->delete(route('projects.pipeline-stages.destroy', [$this->project, $stageA]));

    $response->assertRedirect();
    $response->assertSessionHas('success', 'Pipeline stage deleted successfully.');

    // Assert Stage A is gone
    $this->assertDatabaseMissing('project_pipeline_stages', ['id' => $stageA->id]);
    // Assert project's current stage is null
    $this->project->refresh();
    $this->assertNull($this->project->current_project_pipeline_stage_id);
    // Assert Stage B still exists
    $this->assertDatabaseHas('project_pipeline_stages', ['id' => $stageB->id]);
  }

  /**
   * Test a user cannot delete a stage that belongs to another project.
   */
  public function test_cannot_delete_stage_from_another_project(): void {
    // Create another project and a stage for it
    $anotherProject = Project::factory()->create();
    $stageFromAnotherProject = ProjectPipelineStage::create([
      'project_id' => $anotherProject->id,
      'name' => 'Other Project Stage',
      'stage_order' => 10,
      'is_system_default' => false,
    ]);

    // Attempt to delete the stage from the other project using the current project's route
    $response = $this->delete(route('projects.pipeline-stages.destroy', [$this->project, $stageFromAnotherProject]));

    // Expect a validation error (422) because the stage project_id doesn't match the route project_id
    $response->assertStatus(422);
    $response->assertExactJson(['stage' => 'The selected stage does not belong to this project.']);

    // Assert the stage still exists
    $this->assertDatabaseHas('project_pipeline_stages', ['id' => $stageFromAnotherProject->id]);
  }

  /**
   * Test a user can set a system default stage as current for a project.
   */
  public function test_can_set_current_system_default_stage_as_current(): void {
    // Create a system default stage (e.g., project_id is null, is_system_default is true)
    $systemDefaultStage = ProjectPipelineStage::create([
      'project_id' => null, // System default stages typically have project_id null
      'name' => 'System Default Stage',
      'stage_order' => 1,
      'is_system_default' => true, // Important: system default
    ]);

    // Attempt to set the system default stage as current for the current project
    $response = $this->patch(route('projects.pipeline-stages.setCurrent', [$this->project, $systemDefaultStage]));

    $response->assertRedirect();
    $response->assertSessionHas('success', 'Current pipeline stage updated successfully.');

    // Assert the current project's current stage ID is updated
    $this->project->refresh();
    $this->assertEquals($systemDefaultStage->id, $this->project->current_project_pipeline_stage_id);
  }

  /**
   * Test reordering stages updates the project's current stage ID if the current stage was replicated.
   */
  public function test_reordering_updates_current_stage_id_if_it_was_replicated(): void {
    // Create stages A, B, C
    $stageA = ProjectPipelineStage::create(['project_id' => $this->project->id, 'name' => 'Stage A', 'stage_order' => 10, 'is_system_default' => false]);
    $stageB = ProjectPipelineStage::create(['project_id' => $this->project->id, 'name' => 'Stage B', 'stage_order' => 20, 'is_system_default' => false]);
    $stageC = ProjectPipelineStage::create(['project_id' => $this->project->id, 'name' => 'Stage C', 'stage_order' => 30, 'is_system_default' => false]);

    // Set Stage B as current
    $this->project->update(['current_project_pipeline_stage_id' => $stageB->id]);
    $this->assertEquals($stageB->id, $this->project->current_project_pipeline_stage_id);

    // New order using original IDs: C, A, B
    $newOrderIds = [$stageC->id, $stageA->id, $stageB->id];

    $response = $this->patch(route('projects.pipeline-stages.updateOrder', $this->project), [
      'stage_ids' => $newOrderIds,
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('success', 'Pipeline stage reordered successfully.');

    // Assert old stages are deleted
    $this->assertDatabaseMissing('project_pipeline_stages', ['id' => $stageA->id]);
    $this->assertDatabaseMissing('project_pipeline_stages', ['id' => $stageB->id]);
    $this->assertDatabaseMissing('project_pipeline_stages', ['id' => $stageC->id]);

    // Find the new stage that corresponds to the original Stage B (by name)
    $newStageB = ProjectPipelineStage::where('project_id', $this->project->id)
      ->where('name', 'Stage B')
      ->first();

    $this->assertNotNull($newStageB, 'New Stage B should exist after reordering.');

    // Refresh project and assert current stage ID is the new ID of Stage B
    $this->project->refresh();
    $this->assertEquals($newStageB->id, $this->project->current_project_pipeline_stage_id);

    // Optional: Assert the order of the new stages
    $newStages = $this->project->pipelineStages()->orderBy('stage_order')->get();
    $this->assertCount(3, $newStages);
    $this->assertEquals('Stage C', $newStages[0]->name);
    $this->assertEquals('Stage A', $newStages[1]->name);
    $this->assertEquals('Stage B', $newStages[2]->name);
  }

  /**
   * Test sequence: Set Current -> Reorder -> Delete (the original current stage).
   */
  public function test_sequence_set_current_reorder_delete(): void {
    // Create stages A, B, C
    $stageA = ProjectPipelineStage::create(['project_id' => $this->project->id, 'name' => 'Stage A', 'stage_order' => 10, 'is_system_default' => false]);
    $stageB = ProjectPipelineStage::create(['project_id' => $this->project->id, 'name' => 'Stage B', 'stage_order' => 20, 'is_system_default' => false]);
    $stageC = ProjectPipelineStage::create(['project_id' => $this->project->id, 'name' => 'Stage C', 'stage_order' => 30, 'is_system_default' => false]);

    // 1. Set B as current
    $responseSetCurrent = $this->patch(route('projects.pipeline-stages.setCurrent', [$this->project, $stageB]));
    $responseSetCurrent->assertRedirect();
    $this->project->refresh();
    $this->assertEquals($stageB->id, $this->project->current_project_pipeline_stage_id);

    // 2. Reorder: C, A, B (using original IDs)
    $newOrderIds = [$stageC->id, $stageA->id, $stageB->id];
    $responseReorder = $this->patch(route('projects.pipeline-stages.updateOrder', $this->project), [
      'stage_ids' => $newOrderIds,
    ]);
    $responseReorder->assertRedirect();
    $responseReorder->assertSessionHas('success', 'Pipeline stage reordered successfully.');

    // Find the new stage corresponding to the original Stage B (by name)
    $newStageB = ProjectPipelineStage::where('project_id', $this->project->id)
      ->where('name', 'Stage B')
      ->first();
    $this->assertNotNull($newStageB, 'New Stage B should exist after reordering.');

    // Refresh project and assert current stage ID is the new ID of Stage B
    $this->project->refresh();
    $this->assertEquals($newStageB->id, $this->project->current_project_pipeline_stage_id);

    // 3. Delete the new Stage B (using its new ID)
    $responseDelete = $this->delete(route('projects.pipeline-stages.destroy', [$this->project, $newStageB]));
    $responseDelete->assertRedirect();
    $responseDelete->assertSessionHas('success', 'Pipeline stage deleted successfully.');

    // Assert the new Stage B is gone
    $this->assertDatabaseMissing('project_pipeline_stages', ['id' => $newStageB->id]);
    // Assert project's current stage is null
    $this->project->refresh();
    $this->assertNull($this->project->current_project_pipeline_stage_id);
    // Assert Stage A and C (new IDs) still exist
    $newStageA = ProjectPipelineStage::where('project_id', $this->project->id)->where('name', 'Stage A')->first();
    $newStageC = ProjectPipelineStage::where('project_id', $this->project->id)->where('name', 'Stage C')->first();
    $this->assertDatabaseHas('project_pipeline_stages', ['id' => $newStageA->id]);
    $this->assertDatabaseHas('project_pipeline_stages', ['id' => $newStageC->id]);
  }

  /**
   * Test sequence: Reorder -> Set Current (a new stage) -> Delete (another stage).
   */
  public function test_sequence_reorder_set_current_delete(): void {
    // Create stages A, B, C
    $stageA = ProjectPipelineStage::create(['project_id' => $this->project->id, 'name' => 'Stage A', 'stage_order' => 10, 'is_system_default' => false]);
    $stageB = ProjectPipelineStage::create(['project_id' => $this->project->id, 'name' => 'Stage B', 'stage_order' => 20, 'is_system_default' => false]);
    $stageC = ProjectPipelineStage::create(['project_id' => $this->project->id, 'name' => 'Stage C', 'stage_order' => 30, 'is_system_default' => false]);

    // 1. Reorder: C, A, B (using original IDs)
    $newOrderIds = [$stageC->id, $stageA->id, $stageB->id];
    $responseReorder = $this->patch(route('projects.pipeline-stages.updateOrder', $this->project), [
      'stage_ids' => $newOrderIds,
    ]);
    $responseReorder->assertRedirect();
    $responseReorder->assertSessionHas('success', 'Pipeline stage reordered successfully.');

    // Find the new stages after reordering
    $newStageA = ProjectPipelineStage::where('project_id', $this->project->id)->where('name', 'Stage A')->first();
    $newStageB = ProjectPipelineStage::where('project_id', $this->project->id)->where('name', 'Stage B')->first();
    $newStageC = ProjectPipelineStage::where('project_id', $this->project->id)->where('name', 'Stage C')->first();
    $this->assertNotNull($newStageA);
    $this->assertNotNull($newStageB);
    $this->assertNotNull($newStageC);

    // 2. Set the new Stage A as current (using its new ID)
    $responseSetCurrent = $this->patch(route('projects.pipeline-stages.setCurrent', [$this->project, $newStageA]));
    $responseSetCurrent->assertRedirect();
    $responseSetCurrent->assertSessionHas('success', 'Current pipeline stage updated successfully.');
    $this->project->refresh();
    $this->assertEquals($newStageA->id, $this->project->current_project_pipeline_stage_id);

    // 3. Delete the new Stage C (using its new ID)
    $responseDelete = $this->delete(route('projects.pipeline-stages.destroy', [$this->project, $newStageC]));
    $responseDelete->assertRedirect();
    $responseDelete->assertSessionHas('success', 'Pipeline stage deleted successfully.');

    // Assert the new Stage C is gone
    $this->assertDatabaseMissing('project_pipeline_stages', ['id' => $newStageC->id]);
    // Assert project's current stage is still the new Stage A
    $this->project->refresh();
    $this->assertEquals($newStageA->id, $this->project->current_project_pipeline_stage_id);
    // Assert the new Stage B still exists
    $this->assertDatabaseHas('project_pipeline_stages', ['id' => $newStageB->id]);
  }

  /**
   * Test sequence: Delete -> Set Current (a remaining stage) -> Reorder (remaining stages).
   */
  public function test_sequence_delete_set_current_reorder(): void {
    // Create stages A, B, C
    $stageA = ProjectPipelineStage::create(['project_id' => $this->project->id, 'name' => 'Stage A', 'stage_order' => 10, 'is_system_default' => false]);
    $stageB = ProjectPipelineStage::create(['project_id' => $this->project->id, 'name' => 'Stage B', 'stage_order' => 20, 'is_system_default' => false]);
    $stageC = ProjectPipelineStage::create(['project_id' => $this->project->id, 'name' => 'Stage C', 'stage_order' => 30, 'is_system_default' => false]);

    // 1. Delete Stage B
    $responseDelete = $this->delete(route('projects.pipeline-stages.destroy', [$this->project, $stageB]));
    $responseDelete->assertRedirect();
    $responseDelete->assertSessionHas('success', 'Pipeline stage deleted successfully.');
    $this->assertDatabaseMissing('project_pipeline_stages', ['id' => $stageB->id]);
    $this->assertDatabaseHas('project_pipeline_stages', ['id' => $stageA->id]);
    $this->assertDatabaseHas('project_pipeline_stages', ['id' => $stageC->id]);
    $this->project->refresh();
    // Current stage should be null if B was current, or unchanged if it wasn't.
    // Since we didn't set a current stage initially, it should still be null.
    $this->assertNull($this->project->current_project_pipeline_stage_id);

    // 2. Set Stage C as current (using its original ID)
    $responseSetCurrent = $this->patch(route('projects.pipeline-stages.setCurrent', [$this->project, $stageC]));
    $responseSetCurrent->assertRedirect();
    $responseSetCurrent->assertSessionHas('success', 'Current pipeline stage updated successfully.');
    $this->project->refresh();
    $this->assertEquals($stageC->id, $this->project->current_project_pipeline_stage_id);

    // 3. Reorder remaining stages: C, A (using original IDs)
    $newOrderIds = [$stageC->id, $stageA->id];
    $responseReorder = $this->patch(route('projects.pipeline-stages.updateOrder', $this->project), [
      'stage_ids' => $newOrderIds,
    ]);
    $responseReorder->assertRedirect();
    $responseReorder->assertSessionHas('success', 'Pipeline stage reordered successfully.');

    // Find the new stages after reordering
    $newStageA = ProjectPipelineStage::where('project_id', $this->project->id)->where('name', 'Stage A')->first();
    $newStageC = ProjectPipelineStage::where('project_id', $this->project->id)->where('name', 'Stage C')->first();
    $this->assertNotNull($newStageA);
    $this->assertNotNull($newStageC);

    // Refresh project and assert current stage ID is the new ID of Stage C
    $this->project->refresh();
    $this->assertEquals($newStageC->id, $this->project->current_project_pipeline_stage_id);

    // Assert the order of the new stages
    $newStages = $this->project->pipelineStages()->orderBy('stage_order')->get();
    $this->assertCount(2, $newStages);
    $this->assertEquals('Stage C', $newStages[0]->name);
    $this->assertEquals('Stage A', $newStages[1]->name);
  }
}
