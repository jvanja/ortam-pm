<?php

namespace Database\Seeders;

use App\Models\Organization;
use App\Models\Project;
use App\Models\ProjectPipelineStage;
use Elegantly\Invoices\Database\Factories\InvoiceFactory;
use Elegantly\Invoices\Database\Factories\InvoiceItemFactory;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder {
  /**
   * Run the database seeds.
   */
  public function run(): void {
    Project::factory(10)->create()->each(function ($proj) {
      $defaultStages = ProjectPipelineStage::where('is_system_default', true)->get();
      foreach ($defaultStages as $index => $stage) {
        $newStage = $stage->replicate();
        $newStage->project_id = $proj->id;
        $newStage->is_system_default = false;
        $newStage->save();

        if($index === 0) {
          $proj->current_project_pipeline_stage_id = $newStage->id;
          $proj->save();
        }
      }
      $org = Organization::find(1);
      InvoiceFactory::new()->count(3)->create([
        'total_amount' => rand(10000, 100000),
        'project_id' => $proj->id,
        'client_id' => $proj->client_id,
        'seller_information' => json_decode(
          '{"name":"' . $org->name . '", "address":' . $org->address . ', "email":"' . $org->email . '", "phone":"' . $org->phone . '", "tax_number": null}', true),
      ])->each(function ($invoice) {
          InvoiceItemFactory::new()->count(3)->create([
            'invoice_id' => $invoice->id,
            'quantity' => 1,
            'tax_percentage' => 0,
          ]);
      });
    });
  }
}
