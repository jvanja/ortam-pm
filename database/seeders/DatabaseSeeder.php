<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder {
  /**
   * Seed the application's database.
   */
  public function run(): void {

    User::factory()->create([
      'name' => 'Vanja Jelic',
      'email' => 'jelicvanja@gmail.com',
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
    ]);
  }
}
