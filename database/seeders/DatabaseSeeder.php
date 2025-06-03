<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder {
  /**
   * Seed the application's database.
   */
  public function run(): void {

    $this->call([
      OrganizationSeeder::class,
      RolePermissionSeeder::class,
      ClientSeeder::class,
      ProjectPipelineStageSeeder::class,
      ProjectSeeder::class,
      // InvoiceSeeder::class,
      ProposalSeeder::class,
      TimesheetSeeder::class,
      PCASeeder::class,
      UserProjectSeeder::class,
    ]);
  }
}
