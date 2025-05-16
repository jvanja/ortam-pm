<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  /**
   * Run the migrations.
   */
  public function up(): void {
    Schema::table('invoices', function (Blueprint $table) {
      $table->foreignId('project_id')->default(1)->after('seller_id');
      $table->foreignId('client_id')->default(1)->after('project_id');
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void {
    Schema::table('invoices', function (Blueprint $table) {
      $table->dropColumn('project_id');
      $table->dropColumn('client_id');
    });
  }
};
