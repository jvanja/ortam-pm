<?php

namespace Database\Seeders;

use App\Models\ProjectPipelineStage;
use Illuminate\Database\Seeder;

class ProjectPipelineStageSeeder extends Seeder {
  /**
   * Run the database seeds.
   */
  public function run(): void {
    ProjectPipelineStage::factory()
      ->count(9)
      ->sequence(
        [
          'name' => 'Inquiry',
          'stage_order' => 10
        ],
        [
          'name' => 'Discovery call',
          'stage_order' => 20
        ],
        [
          'name' => 'Proposal sent',
          'stage_order' => 30
        ],
        [
          'name' => 'Proposal accepted',
          'stage_order' => 40
        ],
        [
          'name' => 'Planning',
          'stage_order' => 50
        ],
        [
          'name' => 'Kick-off',
          'stage_order' => 60
        ],
        [
          'name' => 'Completed',
          'stage_order' => 70
        ],
        [
          'name' => 'Invoice sent',
          'stage_order' => 80
        ],
        [
          'name' => 'Payment received',
          'stage_order' => 90
        ],
      )->create();
  }
}
