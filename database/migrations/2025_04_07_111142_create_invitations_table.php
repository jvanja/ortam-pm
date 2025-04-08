<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  /**
   * Run the migrations.
   */
  public function up(): void {
    Schema::create('invitations', function (Blueprint $table) {
      $table->id();
      $table->string('email'); // Email of the invited person
      $table->foreignId('organization_id')->constrained()->onDelete('cascade'); // Organization they are invited to
      $table->string('token', 64)->unique(); // Unique invitation token
      $table->foreignId('inviter_id')->constrained('users')->onDelete('cascade'); // User who sent the invite
      $table->timestamp('expires_at'); // When the invitation expires
      $table->timestamps(); // created_at, updated_at

      // Index for faster lookups by token
      $table->index('token');
      // Index for faster lookups by email (useful for validation rule)
      $table->index(['email', 'expires_at']);
      // Add a unique constraint for pending invitations per organization
      // to prevent multiple active invites for the same email to the same org
      $table->unique(['organization_id', 'email'], 'invitations_organization_email_unique');
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void {
    Schema::dropIfExists('invitations');
  }
};
