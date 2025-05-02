<?php

namespace Database\Seeders;

use App\Models\ProjectPipelineStage;
use Illuminate\Database\Seeder;

class ProjectPipelineStageSeeder extends Seeder {
  /**
   * Run the database seeds.
   */
  public function run(): void {
    ProjectPipelineStage::factory(10)->create();
  }
}
