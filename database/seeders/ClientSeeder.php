<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Project;
use App\Models\Invoice;
use App\Models\Quote;
use Illuminate\Database\Seeder;

class ClientSeeder extends Seeder {
  /**
   * Run the database seeds.
   */
  public function run(): void {
    Client::factory(10)->create();
    Project::factory(10)->create();
    Invoice::factory(10)->create();
    Quote::factory(10)->create();
  }
}
