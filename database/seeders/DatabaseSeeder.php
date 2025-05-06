<?php

namespace Database\Seeders;

use App\Models\User;
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
      InvoiceSeeder::class,
      QuoteSeeder::class,
      TimesheetSeeder::class,
      PCASeeder::class,
      UserProjectSeeder::class,
    ]);
  }
}
