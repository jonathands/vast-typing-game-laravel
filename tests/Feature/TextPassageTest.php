<?php

use App\Models\TextPassage;

test('can fetch random passage', function () {
    TextPassage::factory()->create([
        'text' => 'The quick brown fox jumps over the lazy dog',
        'language' => 'en',
        'word_count' => 9,
        'character_count' => 44,
    ]);

    $response = $this->getJson('/api/passages');

    $response->assertSuccessful();
    $response->assertJsonStructure([
        'data' => [
            'id',
            'text',
            'language',
            'word_count',
            'character_count',
            'category',
            'created_at',
        ],
    ]);
});

test('can fetch specific passage by id', function () {
    $passage = TextPassage::factory()->create([
        'text' => 'Specific passage test',
        'word_count' => 3,
        'character_count' => 21,
    ]);

    $response = $this->getJson("/api/passages/{$passage->id}");

    $response->assertSuccessful();
    $response->assertJsonPath('data.id', $passage->id);
    $response->assertJsonPath('data.text', 'Specific passage test');
});

test('returns 404 when no passages available', function () {
    $response = $this->getJson('/api/passages');

    $response->assertNotFound();
    $response->assertJson(['message' => 'No passages available']);
});

test('returns 404 when passage id does not exist', function () {
    $response = $this->getJson('/api/passages/99999');

    $response->assertNotFound();
});
