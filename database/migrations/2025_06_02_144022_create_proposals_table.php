<?php

use App\Enums\ProposalStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  /**
   * Run the migrations.
   */
  public function up(): void {
    Schema::create('proposals', function (Blueprint $table) {
      $table->id();


      // Proposal details
      $table->string('title');
      $table->text('description')->nullable();
      $table->string('currency', 3); // e.g., 'USD', 'EUR'
      $table->decimal('subtotal_amount', 10, 2)->default(0.00);
      $table->decimal('tax_amount', 10, 2)->default(0.00);
      $table->decimal('total_amount', 10, 2)->default(0.00);

      // State
      $table->string('state');
      // $table->enum('state', [ProposalStatus::Draft ->value, ProposalStatus::Sent->value, ProposalStatus::Viewed->value, ProposalStatus::Accepted->value, ProposalStatus::Rejected->value, ProposalStatus::Expired->value, ProposalStatus::Archived->value])->default('draft');

      // Relationships
      $table->foreignId('client_id')->constrained()->onDelete('cascade');
      $table->foreignId('project_id')->nullable()->constrained()->onDelete('set null');
      $table->foreignId('organization_id')->nullable()->constrained()->onDelete('set null');

      // Timestamps
      $table->timestamp('sent_at')->nullable();
      $table->timestamp('accepted_at')->nullable();
      $table->timestamp('rejected_at')->nullable();
      $table->timestamp('expires_at')->nullable();

      // Link to Invoice (after conversion)
      $table->foreignId('invoice_id')->nullable()->constrained('invoices')->onDelete('set null');

      $table->timestamps(); // created_at and updated_at
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void {
    Schema::dropIfExists('proposals');
  }
};
