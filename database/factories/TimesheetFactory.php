<?php

namespace Database\Factories;

use App\Enums\Language;
use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Enums\Task;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Quote>
 */
class TimesheetFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'task_performed' => fake()->randomElement(Task::cases()),
            'worked_duration' => fake()->randomNumber(2),
            'project_id' => Project::factory(),
            'user_id' => User::factory(),
        ];
    }
}
