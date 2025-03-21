<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\Language;
use App\Enums\QuoteStatus;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('quotes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('project_type');
            $table->enum('quote_language', [Language::English->value, Language::French->value]);
            $table->string('project_address');
            $table->float('budget');
            $table->string('sales_representative_name');
            $table->enum('quote_status', [QuoteStatus::InPreparation->value, QuoteStatus::Sent->value, QuoteStatus::Approved->value, QuoteStatus::Rejected->value]);
            $table->uuid('project_id');
            $table->timestamps();

            $table->foreign('project_id')->references('id')->on('projects');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quotes');
    }
};
