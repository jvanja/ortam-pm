<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\Language;
use App\Enums\ProjectStatus;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('project_type');
            $table->string('department');
            $table->string('project_manager');
            $table->enum('project_language', [Language::English->value, Language::French->value]);
            $table->string('project_address');
            $table->float('budget');
            $table->string('sales_representative_name');
            $table->enum('project_status', [ProjectStatus::Ongoing->value, ProjectStatus::Completed->value, ProjectStatus::Canceled->value]);
            $table->date('project_opening_date');
            $table->date('deadline');
            $table->uuid('quote_id')->nullable();
            $table->timestamps();

            $table->foreign('quote_id')->references('id')->on('quotes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
