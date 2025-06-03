<?php

namespace Database\Seeders;

use App\Models\Proposal;
use Illuminate\Database\Seeder;

class ProposalSeeder extends Seeder {
  /**
   * Run the database seeds.
   */
  public function run(): void {
    Proposal::factory(10)->create();
  }
}
