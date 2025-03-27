<?php

namespace Database\Seeders;

use App\Models\TimeSheet;
use Illuminate\Database\Seeder;

class TimesheetSeeder extends Seeder {
  /**
   * Run the database seeds.
   */
  public function run(): void {
    Timesheet::factory(500)->create();
  }
}
