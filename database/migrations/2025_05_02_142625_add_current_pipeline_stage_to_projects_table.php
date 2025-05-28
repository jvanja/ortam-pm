<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  /**
   * Run the migrations.
   */
  public function up(): void {
    Schema::table('projects', function (Blueprint $table) {
      // Ensure the column type matches the primary key of project_pipeline_stages
      $table->foreignId('current_project_pipeline_stage_id')
        ->nullable()
        ->after('status') // Or adjust position as needed
        ->constrained('project_pipeline_stages')
        ->nullOnDelete();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void {
    Schema::table('projects', function (Blueprint $table) {
      // Drop foreign key first (Laravel 10+ syntax)
      if (Schema::hasColumn('projects', 'current_project_pipeline_stage_id')) {
        // Check if the foreign key exists before trying to drop it
        $foreignKeys = collect(DB::select("SHOW CREATE TABLE projects"))->first()->{'Create Table'};
        if (str_contains($foreignKeys, 'projects_current_project_pipeline_stage_id_foreign')) {
          $table->dropForeign(['current_project_pipeline_stage_id']);
        }
        $table->dropColumn('current_project_pipeline_stage_id');
      }
    });
  }
};
