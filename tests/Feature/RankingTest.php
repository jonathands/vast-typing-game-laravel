<?php

use App\Models\Result;
use App\Models\TextPassage;
use App\Models\User;

test('can fetch general ranking', function () {
    $passage = TextPassage::factory()->create();

    $user1 = User::factory()->create(['name' => 'Top Player']);
    $user2 = User::factory()->create(['name' => 'Second Player']);

    Result::factory()->create([
        'user_id' => $user1->id,
        'text_passage_id' => $passage->id,
        'wpm' => 100,
        'accuracy' => 95,
    ]);

    Result::factory()->create([
        'user_id' => $user2->id,
        'text_passage_id' => $passage->id,
        'wpm' => 80,
        'accuracy' => 85,
    ]);

    $response = $this->getJson('/api/rankings/general');

    $response->assertSuccessful();
    $response->assertJsonStructure([
        'data' => [
            '*' => [
                'rank',
                'user' => ['id', 'name'],
                'avg_wpm',
                'avg_accuracy',
                'total_games',
                'score',
            ],
        ],
    ]);

    expect($response->json('data.0.user.name'))->toBe('Top Player');
    expect($response->json('data.0.rank'))->toBe(1);
});

test('general ranking is ordered by combined score', function () {
    $passage = TextPassage::factory()->create();

    $user1 = User::factory()->create(['name' => 'Third Place']);
    $user2 = User::factory()->create(['name' => 'First Place']);
    $user3 = User::factory()->create(['name' => 'Second Place']);

    Result::factory()->create([
        'user_id' => $user1->id,
        'text_passage_id' => $passage->id,
        'wpm' => 70,
        'accuracy' => 80,
    ]);

    Result::factory()->create([
        'user_id' => $user2->id,
        'text_passage_id' => $passage->id,
        'wpm' => 100,
        'accuracy' => 95,
    ]);

    Result::factory()->create([
        'user_id' => $user3->id,
        'text_passage_id' => $passage->id,
        'wpm' => 85,
        'accuracy' => 90,
    ]);

    $response = $this->getJson('/api/rankings/general');

    $response->assertSuccessful();
    expect($response->json('data.0.user.name'))->toBe('First Place');
    expect($response->json('data.1.user.name'))->toBe('Second Place');
    expect($response->json('data.2.user.name'))->toBe('Third Place');
});

test('can fetch accuracy ranking', function () {
    $passage = TextPassage::factory()->create();

    $user1 = User::factory()->create(['name' => 'Accurate Player']);
    $user2 = User::factory()->create(['name' => 'Less Accurate']);

    Result::factory()->create([
        'user_id' => $user1->id,
        'text_passage_id' => $passage->id,
        'wpm' => 70,
        'accuracy' => 99,
    ]);

    Result::factory()->create([
        'user_id' => $user2->id,
        'text_passage_id' => $passage->id,
        'wpm' => 100,
        'accuracy' => 85,
    ]);

    $response = $this->getJson('/api/rankings/accuracy');

    $response->assertSuccessful();
    $response->assertJsonStructure([
        'data' => [
            '*' => [
                'rank',
                'user' => ['id', 'name'],
                'avg_wpm',
                'avg_accuracy',
                'total_games',
                'score',
            ],
        ],
    ]);

    expect($response->json('data.0.user.name'))->toBe('Accurate Player');
    expect($response->json('data.0.rank'))->toBe(1);
});

test('accuracy ranking is ordered by accuracy', function () {
    $passage = TextPassage::factory()->create();

    $user1 = User::factory()->create(['name' => 'Second Most Accurate']);
    $user2 = User::factory()->create(['name' => 'Most Accurate']);
    $user3 = User::factory()->create(['name' => 'Least Accurate']);

    Result::factory()->create([
        'user_id' => $user1->id,
        'text_passage_id' => $passage->id,
        'wpm' => 100,
        'accuracy' => 92,
    ]);

    Result::factory()->create([
        'user_id' => $user2->id,
        'text_passage_id' => $passage->id,
        'wpm' => 70,
        'accuracy' => 98,
    ]);

    Result::factory()->create([
        'user_id' => $user3->id,
        'text_passage_id' => $passage->id,
        'wpm' => 120,
        'accuracy' => 80,
    ]);

    $response = $this->getJson('/api/rankings/accuracy');

    $response->assertSuccessful();
    expect($response->json('data.0.user.name'))->toBe('Most Accurate');
    expect($response->json('data.1.user.name'))->toBe('Second Most Accurate');
    expect($response->json('data.2.user.name'))->toBe('Least Accurate');
});

test('can fetch speed ranking', function () {
    $passage = TextPassage::factory()->create();

    $user1 = User::factory()->create(['name' => 'Fast Player']);
    $user2 = User::factory()->create(['name' => 'Slow Player']);

    Result::factory()->create([
        'user_id' => $user1->id,
        'text_passage_id' => $passage->id,
        'wpm' => 100,
        'accuracy' => 90,
    ]);

    Result::factory()->create([
        'user_id' => $user2->id,
        'text_passage_id' => $passage->id,
        'wpm' => 70,
        'accuracy' => 95,
    ]);

    $response = $this->getJson('/api/rankings/speed');

    $response->assertSuccessful();
    $response->assertJsonStructure([
        'data' => [
            '*' => [
                'rank',
                'user' => ['id', 'name'],
                'avg_wpm',
                'avg_accuracy',
                'total_games',
                'score',
            ],
        ],
    ]);

    expect($response->json('data.0.user.name'))->toBe('Fast Player');
    expect($response->json('data.0.rank'))->toBe(1);
});

test('speed ranking only includes users with accuracy >= 85%', function () {
    $passage = TextPassage::factory()->create();

    $user1 = User::factory()->create(['name' => 'Fast and Accurate']);
    $user2 = User::factory()->create(['name' => 'Fast but Sloppy']);
    $user3 = User::factory()->create(['name' => 'Moderate Speed']);

    Result::factory()->create([
        'user_id' => $user1->id,
        'text_passage_id' => $passage->id,
        'wpm' => 100,
        'accuracy' => 95,
    ]);

    Result::factory()->create([
        'user_id' => $user2->id,
        'text_passage_id' => $passage->id,
        'wpm' => 120,
        'accuracy' => 70,
    ]);

    Result::factory()->create([
        'user_id' => $user3->id,
        'text_passage_id' => $passage->id,
        'wpm' => 80,
        'accuracy' => 90,
    ]);

    $response = $this->getJson('/api/rankings/speed');

    $response->assertSuccessful();
    expect($response->json('data'))->toHaveCount(2);
    expect($response->json('data.0.user.name'))->toBe('Fast and Accurate');
    expect($response->json('data.1.user.name'))->toBe('Moderate Speed');
});

test('ranking endpoints respect limit parameter', function () {
    $passage = TextPassage::factory()->create();

    for ($i = 0; $i < 15; $i++) {
        $user = User::factory()->create();
        Result::factory()->create([
            'user_id' => $user->id,
            'text_passage_id' => $passage->id,
            'wpm' => 80 + $i,
            'accuracy' => 90,
        ]);
    }

    $response = $this->getJson('/api/rankings/general?limit=5');
    $response->assertSuccessful();
    expect($response->json('data'))->toHaveCount(5);

    $response = $this->getJson('/api/rankings/accuracy?limit=3');
    $response->assertSuccessful();
    expect($response->json('data'))->toHaveCount(3);

    $response = $this->getJson('/api/rankings/speed?limit=7');
    $response->assertSuccessful();
    expect($response->json('data'))->toHaveCount(7);
});

test('ranking endpoints are publicly accessible', function () {
    $passage = TextPassage::factory()->create();
    $user = User::factory()->create();

    Result::factory()->create([
        'user_id' => $user->id,
        'text_passage_id' => $passage->id,
        'wpm' => 80,
        'accuracy' => 90,
    ]);

    $response = $this->getJson('/api/rankings/general');
    $response->assertSuccessful();

    $response = $this->getJson('/api/rankings/accuracy');
    $response->assertSuccessful();

    $response = $this->getJson('/api/rankings/speed');
    $response->assertSuccessful();
});

test('ranking shows average wpm and accuracy', function () {
    $passage = TextPassage::factory()->create();
    $user = User::factory()->create();

    Result::factory()->create([
        'user_id' => $user->id,
        'text_passage_id' => $passage->id,
        'wpm' => 80,
        'accuracy' => 90,
    ]);

    Result::factory()->create([
        'user_id' => $user->id,
        'text_passage_id' => $passage->id,
        'wpm' => 100,
        'accuracy' => 94,
    ]);

    $response = $this->getJson('/api/rankings/general');

    $response->assertSuccessful();
    expect($response->json('data.0.avg_wpm'))->toBe(90.0);
    expect($response->json('data.0.avg_accuracy'))->toBe(92.0);
});

test('ranking shows total games count', function () {
    $passage = TextPassage::factory()->create();
    $user = User::factory()->create();

    Result::factory()->count(5)->create([
        'user_id' => $user->id,
        'text_passage_id' => $passage->id,
        'wpm' => 80,
        'accuracy' => 90,
    ]);

    $response = $this->getJson('/api/rankings/general');

    $response->assertSuccessful();
    expect($response->json('data.0.total_games'))->toBe(5);
});
