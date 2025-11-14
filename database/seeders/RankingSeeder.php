<?php

namespace Database\Seeders;

use App\Models\Result;
use App\Models\TextPassage;
use App\Models\User;
use Illuminate\Database\Seeder;

class RankingSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Starting Ranking seeding...');

        $passages = TextPassage::all();

        if ($passages->isEmpty()) {
            $this->command->warn('No text passages found. Please run TextPassageSeeder first.');
            return;
        }

        foreach ($passages as $passage) {
            $numResults = fake()->numberBetween(1, 5);

            for ($i = 0; $i < $numResults; $i++) {
                $user = User::factory()->create([
                    'name' => 'Player ' . fake()->numberBetween(1, 9999),
                ]);

                $wpm = fake()->randomFloat(2, 40, 120);
                $accuracy = fake()->randomFloat(2, 70, 100);

                $wordCount = $passage->word_count;
                $timeTaken = (int) (($wordCount / $wpm) * 60);
                $errorsCount = (int) (($wordCount * (100 - $accuracy)) / 100);

                Result::create([
                    'user_id' => $user->id,
                    'text_passage_id' => $passage->id,
                    'user_input' => $passage->text,
                    'wpm' => $wpm,
                    'accuracy' => $accuracy,
                    'time_taken' => $timeTaken,
                    'errors_count' => max(0, $errorsCount),
                    'completed_at' => fake()->dateTimeBetween('-7 days', 'now'),
                ]);
            }

        }

        $totalUsers = User::count();
        $totalResults = Result::count();
        $this->command->info("Finished Ranking seeding. Total users: {$totalUsers}, Total results: {$totalResults}");
    }
}
