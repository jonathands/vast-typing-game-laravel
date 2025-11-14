<?php

namespace Database\Factories;

use App\Models\SourceText;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TextPassage>
 */
class TextPassageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $sourceText = SourceText::inRandomOrder()->first();

        if (!$sourceText) {
            $sourceText = SourceText::factory()->create();
        }

        $paragraphs = array_filter(explode("\n\n", $sourceText->text));
        $randomParagraph = fake()->randomElement($paragraphs);

        $words = explode(' ', $randomParagraph);
        $targetWordCount = fake()->numberBetween(100, 200);
        $extractedWords = array_slice($words, 0, min($targetWordCount, count($words)));
        $text = implode(' ', $extractedWords);

        $passageCount = \App\Models\TextPassage::count() + 1;
        $title = $sourceText->title . ' - Passage ' . $passageCount;

        return [
            'title' => $title,
            'text' => $text,
            'language' => 'en',
            'word_count' => str_word_count($text),
            'character_count' => strlen($text),
            'category' => fake()->randomElement(['fiction', 'non-fiction', 'quotes', 'code', null]),
        ];
    }
}
