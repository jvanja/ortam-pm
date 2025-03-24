<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  /**
   * Run the migrations.
   */
  public function up(): void {
    Schema::create('p_c_a_reports', function (Blueprint $table) {
      $table->id();
      $table->string('name');
      $table->string('occupation_of_the_property');
      $table->integer('total_site_area')->comment('Measured in square meters');
      $table->integer('surface_area_of_the_building')->comment('Measured in square meters');
      $table->decimal('occupation_of_the_building', 5, 2)->comment('Percentage of total lot area');
      $table->year('year_of_construction');
      $table->string('structure');
      $table->string('fondation');
      $table->string('building');
      $table->integer('numbers_of_floors');
      $table->boolean('basement');
      $table->integer('residential_units');
      $table->integer('non_residential_units')->default(0);
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
    Schema::dropIfExists('p_c_a_reports');
  }
};
