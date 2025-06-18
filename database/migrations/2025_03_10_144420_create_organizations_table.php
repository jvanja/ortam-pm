<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  /**
   * Run the migrations.
   */
  public function up(): void {
    Schema::create('organizations', function (Blueprint $table) {
      $table->id();
      $table->timestamps();
      $table->string('name');
      $table->string('email')->unique();
      $table->string('phone')->nullable();
      $table->json('address')->nullable();
      $table->string('payment_instructions')->nullable();
      $table->string('logo')->nullable();
      $table->string('brand_color')->nullable();
    });

    // add organization_id to the users table
    Schema::table('users', function (Blueprint $table) {
      $table->unsignedBigInteger('organization_id')->nullable();
      $table->foreign('organization_id')->references('id')->on('organizations');
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void {
    Schema::dropIfExists('organizations');
  }
};
