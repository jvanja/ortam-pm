<?php

namespace Database\Seeders;

use Elegantly\Invoices\Database\Factories\InvoiceFactory;
use Elegantly\Invoices\Database\Factories\InvoiceItemFactory;
use Illuminate\Database\Seeder;

class InvoiceSeeder extends Seeder {
  /**
   * Run the database seeds.
   */
  public function run(): void {
    InvoiceFactory::new()->count(100)->create()->each(function ($invoice) {
      $invoice->project_id = 3;
      $invoice->save();
      InvoiceItemFactory::new()->count(3)->create(['invoice_id' => $invoice->id]);
    });
  }
}
