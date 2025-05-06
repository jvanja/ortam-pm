<?php

namespace Database\Seeders;

use App\Models\Organization;
use Illuminate\Database\Seeder;

class OrganizationSeeder extends Seeder {
  /**
   * Run the database seeds.
   */
  public function run(): void {
    Organization::factory()->create([
      'name' => 'Ortam Groupe',
    ]);
    Organization::factory(2)->create();
  }
}
