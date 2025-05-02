<?php

namespace Database\Seeders;

use App\Models\Organization;
use App\Models\ProjectPipelineStage;
use App\Models\User;
use App\Models\PipelineStage;
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

    PipelineStage::factory()
      ->count(9)
      ->sequence(
        [
          'default_name' => 'Inquiry',
          'default_order' => 10
        ],
        [
          'default_name' => 'Discovery call',
          'default_order' => 20
        ],
        [
          'default_name' => 'Proposal sent',
          'default_order' => 30
        ],
        [
          'default_name' => 'Proposal accepted',
          'default_order' => 40
        ],
        [
          'default_name' => 'Planning',
          'default_order' => 50
        ],
        [
          'default_name' => 'Kick-off',
          'default_order' => 60
        ],
        [
          'default_name' => 'Completed',
          'default_order' => 70
        ],
        [
          'default_name' => 'Invoice sent',
          'default_order' => 80
        ],
        [
          'default_name' => 'Payment received',
          'default_order' => 90
        ],
      )->create();

    // ProjectPipelineStage::factory()
    //   ->count(9)->create();

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
