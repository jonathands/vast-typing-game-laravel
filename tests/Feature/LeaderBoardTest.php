<?php

use App\Models\Result;
use App\Models\TextPassage;
use App\Models\User;

test('can fetch leaderboard', function () {
    $passage = TextPassage::factory()->create();

    $user1 = User::factory()->create(['name' => 'Fast Typer']);
    $user2 = User::factory()->create(['name' => 'Slow Typer']);

    Result::factory()->create([
        'user_id' => $user1->id,
        'text_passage_id' => $passage->id,
        'wpm' => 100,
        'accuracy' => 99,
    ]);

    Result::factory()->create([
        'user_id' => $user2->id,
        'text_passage_id' => $passage->id,
        'wpm' => 50,
        'accuracy' => 95,
    ]);

    $response = $this->getJson('/api/leaderboard');

    $response->assertSuccessful();
    $response->assertJsonStructure([
        'data' => [
            '*' => [
                'rank',
                'user' => ['id', 'name'],
                'best_wpm',
                'best_accuracy',
                'total_games',
            ],
        ],
    ]);

    expect($response->json('data.0.user.name'))->toBe('Fast Typer');
    expect($response->json('data.0.rank'))->toBe(1);
});

test('leaderboard is ordered by best wpm', function () {
    $passage = TextPassage::factory()->create();

    $user1 = User::factory()->create(['name' => 'Third Place']);
    $user2 = User::factory()->create(['name' => 'First Place']);
    $user3 = User::factory()->create(['name' => 'Second Place']);

    Result::factory()->create([
        'user_id' => $user1->id,
        'text_passage_id' => $passage->id,
        'wpm' => 70,
    ]);

    Result::factory()->create([
        'user_id' => $user2->id,
        'text_passage_id' => $passage->id,
        'wpm' => 100,
    ]);

    Result::factory()->create([
        'user_id' => $user3->id,
        'text_passage_id' => $passage->id,
        'wpm' => 85,
    ]);

    $response = $this->getJson('/api/leaderboard');

    $response->assertSuccessful();
    expect($response->json('data.0.user.name'))->toBe('First Place');
    expect($response->json('data.1.user.name'))->toBe('Second Place');
    expect($response->json('data.2.user.name'))->toBe('Third Place');
});

test('leaderboard respects limit parameter', function () {
    $passage = TextPassage::factory()->create();

    for ($i = 0; $i < 15; $i++) {
        $user = User::factory()->create();
        Result::factory()->create([
            'user_id' => $user->id,
            'text_passage_id' => $passage->id,
        ]);
    }

    $response = $this->getJson('/api/leaderboard?limit=5');

    $response->assertSuccessful();
    expect($response->json('data'))->toHaveCount(5);
});

test('leaderboard shows best wpm per user', function () {
    $passage = TextPassage::factory()->create();
    $user = User::factory()->create(['name' => 'Improving Typer']);

    Result::factory()->create([
        'user_id' => $user->id,
        'text_passage_id' => $passage->id,
        'wpm' => 50,
    ]);

    Result::factory()->create([
        'user_id' => $user->id,
        'text_passage_id' => $passage->id,
        'wpm' => 100,
    ]);

    Result::factory()->create([
        'user_id' => $user->id,
        'text_passage_id' => $passage->id,
        'wpm' => 75,
    ]);

    $response = $this->getJson('/api/leaderboard');

    $response->assertSuccessful();
    expect($response->json('data.0.best_wpm'))->toBe(100);
});

test('leaderboard shows total games per user', function () {
    $passage = TextPassage::factory()->create();
    $user = User::factory()->create();

    Result::factory()->count(7)->create([
        'user_id' => $user->id,
        'text_passage_id' => $passage->id,
    ]);

    $response = $this->getJson('/api/leaderboard');

    $response->assertSuccessful();
    expect($response->json('data.0.total_games'))->toBe(7);
});

test('leaderboard is publicly accessible', function () {
    $passage = TextPassage::factory()->create();
    $user = User::factory()->create();

    Result::factory()->create([
        'user_id' => $user->id,
        'text_passage_id' => $passage->id,
    ]);

    $response = $this->getJson('/api/leaderboard');

    $response->assertSuccessful();
});
