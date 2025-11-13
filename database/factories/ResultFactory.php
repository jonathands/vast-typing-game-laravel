<?php

namespace Database\Factories;

use App\Models\TextPassage;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Result>
 */
class ResultFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'text_passage_id' => TextPassage::factory(),
            'wpm' => fake()->randomFloat(2, 20, 150),
            'accuracy' => fake()->randomFloat(2, 70, 100),
            'time_taken' => fake()->numberBetween(30, 300),
            'errors_count' => fake()->numberBetween(0, 20),
            'completed_at' => fake()->dateTimeBetween('-30 days', 'now'),
        ];
    }
}
