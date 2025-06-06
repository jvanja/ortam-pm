<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  /**
   * Run the migrations.
   */
  public function up(): void {
    Schema::table('time_sheets', function (Blueprint $table) {
        $table->foreignId('project_pipeline_stage_id')->after('project_id')->constrained();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void {
    Schema::table('time_sheets', function (Blueprint $table) {
      $table->dropColumn('project_pipeline_stage_id');
    });
  }
};
