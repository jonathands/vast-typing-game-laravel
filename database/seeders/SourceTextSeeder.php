<?php

namespace Database\Seeders;

use App\Models\SourceText;
use Illuminate\Database\Seeder;

class SourceTextSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Starting SourceText seeding...');
        SourceText::factory()->count(3)->create();
        $this->command->info('Finished SourceText seeding.');
    }
}
