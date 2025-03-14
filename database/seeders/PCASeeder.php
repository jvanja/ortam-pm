<?php

namespace Database\Seeders;

use App\Models\PCAReport;
use Illuminate\Database\Seeder;

class PCASeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        PCAReport::factory(10)->create();
    }
}
