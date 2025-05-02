<?php

namespace Database\Seeders;

use App\Models\Organization;
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
        ['default_name' => 'Inquiry'],
        ['default_name' => 'Discovery call'],
        ['default_name' => 'Proposal sent'],
        ['default_name' => 'Proposal accepted'],
        ['default_name' => 'Planning'],
        ['default_name' => 'Kick-off'],
        ['default_name' => 'Completed'],
        ['default_name' => 'Invioce sent'],
        ['default_name' => 'Payment received'],
      )->create();

    $this->call([
      RolePermissionSeeder::class,
      // OrganizationSeeder::class,
      // ClientSeeder::class,
      // ProjectSeeder::class,
      InvoiceSeeder::class,
      QuoteSeeder::class,
      TimesheetSeeder::class,
      PCASeeder::class,
      UserProjectSeeder::class,
    ]);
  }
}
