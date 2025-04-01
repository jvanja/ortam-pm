<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  /**
   * Run the migrations.
   */
  public function up(): void {
    Schema::create('invoices', function (Blueprint $table) {
      $table->id();
      $table->string('invoice_number');
      $table->float('amount');
      $table->boolean('status');
      $table->foreignId('project_id')->constrained()->onUpdate('cascade');
      $table->foreignId('client_id')->constrained()->onUpdate('cascade');
      $table->foreignId('organization_id')->constrained()->onUpdate('cascade');
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void {
    Schema::dropIfExists('invoices');
  }
};
