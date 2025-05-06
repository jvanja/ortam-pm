<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\ProjectPipelineStage;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder {
  /**
   * Run the database seeds.
   */
  public function run(): void {
    Project::factory(10)->create()->each(function ($proj) {
      $defaultStages = ProjectPipelineStage::where('is_system_default', true)->get();
      foreach ($defaultStages as $index => $stage) {
        $newStage = $stage->replicate();
        $newStage->project_id = $proj->id;
        $newStage->is_system_default = false;
        $newStage->save();

        if($index === 0) {
          $proj->current_project_pipeline_stage_id = $newStage->id;
          $proj->save();
        }
      }
    });
  }
}
