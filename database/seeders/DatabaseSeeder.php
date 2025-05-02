<?php

namespace Database\Seeders;

use App\Models\Organization;
use App\Models\ProjectPipelineStage;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder {
  /**
   * Seed the application's database.
   */
  public function run(): void {

    Organization::factory()->create([
      'name' => 'Ortam Groupe',
    ]);

    User::factory()
      ->count(3)
      ->sequence(
        [
          'name' => 'Vanja Jelic',
          'email' => 'jelicvanja@gmail.com',
          'password' => bcrypt('jeremy11'),
        ],
        [
          'name' => 'Employee Jelic',
          'email' => 'employee@gmail.com',
          'password' => bcrypt('jeremy11'),
        ],
        [
          'name' => 'Client Jelic',
          'email' => 'client@gmail.com',
          'password' => bcrypt('jeremy11'),
        ]
      )->create();

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

    $this->call([
      RolePermissionSeeder::class,
      // OrganizationSeeder::class,
      // ClientSeeder::class,
      // ProjectSeeder::class,
      // ProjectPipelineStageSeeder::class,
      InvoiceSeeder::class,
      QuoteSeeder::class,
      TimesheetSeeder::class,
      PCASeeder::class,
      UserProjectSeeder::class,
    ]);
  }
}
