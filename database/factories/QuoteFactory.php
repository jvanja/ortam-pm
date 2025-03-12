<?php

namespace Database\Factories;

use App\Enums\Language;
use App\Enums\QuoteStatus;
use App\Models\Client;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Quote>
 */
class QuoteFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'project_type' => fake()->jobTitle(),
            'quote_language' => fake()->randomElement(Language::cases()),
            'project_address' => fake()->address(),
            'budget' => fake()->randomFloat(2, 1000, 10000),
            'sales_representative_name' => fake()->name(),
            'quote_status' => fake()->randomElement(QuoteStatus::cases()),
            'client_id' => Client::factory(),
        ];
    }
}
