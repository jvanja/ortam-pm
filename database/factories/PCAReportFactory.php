<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Project;
use App\Models\Client;
use App\Models\Organization;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class PCAReportFactory extends Factory {
  /**
   * Define the model's default state.
   *
   * @return array<string, mixed>
   */
  public function definition(): array {
    return [
      'name' => $this->faker->company(),
      'occupation_of_the_property' => 'Residential',
      'total_site_area' => $this->faker->numberBetween(2000, 5000), // m²
      'surface_area_of_the_building' => $this->faker->numberBetween(500, 1000), // m²
      'occupation_of_the_building' => $this->faker->randomFloat(2, 10, 50), // percentage
      'year_of_construction' => $this->faker->year(),
      'structure' => $this->faker->randomElement(['Reinforced concrete', 'Steel frame', 'Wood frame']),
      'fondation' => $this->faker->randomElement(['Reinforced concrete', 'Piled foundation', 'Slab-on-grade']),
      'building' => $this->faker->randomElement(['Bricks', 'Metal siding', 'Vinyl siding']),
      'numbers_of_floors' => $this->faker->numberBetween(1, 10),
      'basement' => $this->faker->boolean(30), // 30% chance of having a basement
      'residential_units' => $this->faker->numberBetween(10, 100),
      'non_residential_units' => $this->faker->numberBetween(0, 10),
      'project_id' => Project::inRandomOrder()->first(),
      'client_id' => Client::inRandomOrder()->first(),
      'organization_id' => Organization::inRandomOrder()->first(),
      // 'project_id' => Project::factory(),
      // 'client_id' => Client::factory(),
      // 'organization_id' => Organization::factory(),
    ];
  }
}
