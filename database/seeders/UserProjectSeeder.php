<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Project;

class UserProjectSeeder extends Seeder {
  /**
   * Run the database seeds.
   */
  public function run(): void {
    // Ensure there are projects available
    $projects = Project::all();

    if ($projects->isEmpty()) {
      $this->command->info('No projects found, creating sample projects...');
      $projects = Project::factory()->count(10)->create();
    }

    // Cache the project IDs to avoid an extra query for each user
    $projectIds = $projects->pluck('id')->toArray();

    // Create 50 users and assign each a random project
    User::factory(47)->create()->each(function ($user) use ($projectIds) {
      // Pick a random project ID
      $randomProjectId = $projectIds[array_rand($projectIds)];
      // Assign the user to that project via the pivot table
      $user->projects()->attach($randomProjectId);
      $user->assignRole('employee');
    });
  }
}
