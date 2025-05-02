<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  /**
   * Run the migrations.
   */
  public function up(): void {
    Schema::create('project_pipeline_stages', function (Blueprint $table) {
      $table->uuid('id')->primary();
      $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();
      $table->string('name'); // Specific name for this stage in this project
      $table->foreignUuid('pipeline_stage_id')->nullable()->constrained('pipeline_stages')->nullOnDelete(); // Link to default stage type
      $table->integer('stage_order')->default(0);
      $table->string('status')->default('pending'); // e.g., pending, in_progress, completed, skipped
      $table->timestamp('completed_at')->nullable();
      $table->text('notes')->nullable();
      $table->timestamps();

      $table->index(['project_id', 'stage_order']);
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void {
    Schema::dropIfExists('project_pipeline_stages');
  }
};
