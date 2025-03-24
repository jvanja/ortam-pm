<?php

namespace Database\Seeders;

use App\Models\Organization;
use App\Models\Client;
use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Seeder;

class OrganizationSeeder extends Seeder {
  /**
   * Run the database seeds.
   */
  public function run(): void {
    Organization::factory(2)->create();
    // Organization::factory()
    //   ->count(2)
    //   ->has(Client::factory()->count(5), 'clients')
    //   ->has(Project::factory()->count(5), 'projects')
    //   ->has(User::factory()->count(5), 'users')
    //   ->create();
  }
}
