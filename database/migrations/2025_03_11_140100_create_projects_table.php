<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\Language;
use App\Enums\ProjectStatus;

return new class extends Migration {
  /**
   * Run the migrations.
   */
  public function up(): void {
    Schema::create('projects', function (Blueprint $table) {
      $table->id();
      $table->string('type');
      $table->string('department')->nullable();
      $table->string('manager')->nullable();
      $table->enum('language', [Language::English->value, Language::French->value]);
      $table->string('address')->nullable();
      $table->float('budget')->nullable();
      $table->string('sales_representative_name')->nullable();
      $table->enum('status', [ProjectStatus::Ongoing->value, ProjectStatus::Completed->value, ProjectStatus::Canceled->value]);
      $table->date('opening_date')->nullable();
      $table->date('deadline')->nullable();
      $table->foreignId('client_id')->constrained()->onUpdate('cascade');
      $table->foreignId('organization_id')->constrained()->onUpdate('cascade');
      $table->timestamps();
    });

    Schema::create('project_user', function (Blueprint $table) {
      $table->unsignedBigInteger('project_id');
      $table->unsignedBigInteger('user_id');
      $table->timestamps();

      $table->primary(['project_id', 'user_id']);
      $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
      $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void {
    Schema::dropIfExists('projects');
  }
};
