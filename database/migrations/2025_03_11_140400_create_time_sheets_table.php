<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\Task;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('time_sheets', function (Blueprint $table) {
            $table->id();
            $table->enum('task_performed', [Task::Visit->value, Task::Research->value, Task::Fieldwork->value, Task::Report->value]);
            $table->float('worked_duration');
            $table->foreignId('user_id')->constrained();
            $table->foreignId('project_id')->constrained();

            // $table->uuid('project_id');
            $table->timestamps();

            // $table->foreign('project_id')->references('id')->on('projects');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('time_sheets');
    }
};
