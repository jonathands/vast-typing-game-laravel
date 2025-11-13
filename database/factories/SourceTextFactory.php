<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SourceText>
 */
class SourceTextFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $paragraphs = [];
        $numParagraphs = fake()->numberBetween(5, 10);

        for ($i = 0; $i < $numParagraphs; $i++) {
            $paragraphs[] = fake()->paragraph(fake()->numberBetween(10, 30));
        }

        $sourceText = implode("\n\n", $paragraphs);

        return [
            'text' => $sourceText,
            'author' => fake()->name(),
            'title' => fake()->sentence(3),
            'language' => 'en',
            'word_count' => str_word_count($sourceText),
            'character_count' => strlen($sourceText),
        ];
    }
}
