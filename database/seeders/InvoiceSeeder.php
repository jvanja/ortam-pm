<?php

namespace Database\Seeders;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use Illuminate\Database\Seeder;

class InvoiceSeeder extends Seeder {
  /**
   * Run the database seeds.
   */
  public function run(): void {
    Invoice::factory(100)->create()->each(function ($invoice) {
      InvoiceItem::factory(3)->create(['invoice_id' => $invoice->id]);
    });
  }
}
