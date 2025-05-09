<?php

namespace Database\Seeders;

use App\Models\Organization;
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

    User::factory()->create([
      'name' => 'Vanja Jelic',
      'email' => 'jelicvanja@gmail.com',
      'password' => bcrypt('jeremy11'),
    ]);
    User::factory()->create([
      'name' => 'Employee Jelic',
      'email' => 'employee@gmail.com',
      'password' => bcrypt('jeremy11'),
    ]);
    User::factory()->create([
      'name' => 'Client Jelic',
      'email' => 'client@gmail.com',
      'password' => bcrypt('jeremy11'),
    ]);
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
