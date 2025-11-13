<?php

namespace Database\Seeders;

use App\Models\TextPassage;
use Illuminate\Database\Seeder;

class TextPassageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Starting TextPassage seeding...');
        TextPassage::factory()->count(10)->create();
        $this->command->info('Finished TextPassage seeding.');
    }
}
