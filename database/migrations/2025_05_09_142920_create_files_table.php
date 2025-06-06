<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  /**
   * Run the migrations.
   */
  public function up(): void {
    Schema::create('files', function (Blueprint $table) {
      $table->uuid('id')->primary();
      $table->uuid('parent_id');
      $table->timestamps();
      $table->string('name');
      $table->string('path');
      $table->string('mime_type');
      $table->bigInteger('size');
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void {
    Schema::dropIfExists('files');
  }
};
