<?php

use App\Models\Result;
use App\Models\TextPassage;
use App\Models\User;

test('authenticated user can submit game result', function () {
    $user = User::factory()->create();
    $passage = TextPassage::factory()->create();

    $response = $this->actingAs($user, 'sanctum')->postJson('/api/game/submit', [
        'text_passage_id' => $passage->id,
        'wpm' => 75.50,
        'accuracy' => 98.5,
        'time_taken' => 120,
        'errors_count' => 3,
    ]);

    $response->assertSuccessful();
    $response->assertJsonStructure([
        'data' => [
            'id',
            'user_id',
            'text_passage_id',
            'wpm',
            'accuracy',
            'time_taken',
            'errors_count',
            'completed_at',
        ],
    ]);

    expect(Result::count())->toBe(1);
    expect(Result::first()->user_id)->toBe($user->id);
    expect(Result::first()->wpm)->toBe('75.50');
});

test('unauthenticated user cannot submit game result', function () {
    $passage = TextPassage::factory()->create();

    $response = $this->postJson('/api/game/submit', [
        'text_passage_id' => $passage->id,
        'wpm' => 75.50,
        'accuracy' => 98.5,
        'time_taken' => 120,
        'errors_count' => 3,
    ]);

    $response->assertUnauthorized();
});

test('game result validation requires text_passage_id', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user, 'sanctum')->postJson('/api/game/submit', [
        'wpm' => 75.50,
        'accuracy' => 98.5,
        'time_taken' => 120,
        'errors_count' => 3,
    ]);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors('text_passage_id');
});

test('game result validation requires valid text_passage_id', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user, 'sanctum')->postJson('/api/game/submit', [
        'text_passage_id' => 99999,
        'wpm' => 75.50,
        'accuracy' => 98.5,
        'time_taken' => 120,
        'errors_count' => 3,
    ]);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors('text_passage_id');
});

test('game result validation requires wpm', function () {
    $user = User::factory()->create();
    $passage = TextPassage::factory()->create();

    $response = $this->actingAs($user, 'sanctum')->postJson('/api/game/submit', [
        'text_passage_id' => $passage->id,
        'accuracy' => 98.5,
        'time_taken' => 120,
        'errors_count' => 3,
    ]);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors('wpm');
});

test('game result validation requires valid wpm', function () {
    $user = User::factory()->create();
    $passage = TextPassage::factory()->create();

    $response = $this->actingAs($user, 'sanctum')->postJson('/api/game/submit', [
        'text_passage_id' => $passage->id,
        'wpm' => -10,
        'accuracy' => 98.5,
        'time_taken' => 120,
        'errors_count' => 3,
    ]);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors('wpm');
});

test('game result validation requires accuracy between 0 and 100', function () {
    $user = User::factory()->create();
    $passage = TextPassage::factory()->create();

    $response = $this->actingAs($user, 'sanctum')->postJson('/api/game/submit', [
        'text_passage_id' => $passage->id,
        'wpm' => 75.50,
        'accuracy' => 150,
        'time_taken' => 120,
        'errors_count' => 3,
    ]);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors('accuracy');
});

test('game result validation requires time_taken', function () {
    $user = User::factory()->create();
    $passage = TextPassage::factory()->create();

    $response = $this->actingAs($user, 'sanctum')->postJson('/api/game/submit', [
        'text_passage_id' => $passage->id,
        'wpm' => 75.50,
        'accuracy' => 98.5,
        'errors_count' => 3,
    ]);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors('time_taken');
});

test('game result validation requires errors_count', function () {
    $user = User::factory()->create();
    $passage = TextPassage::factory()->create();

    $response = $this->actingAs($user, 'sanctum')->postJson('/api/game/submit', [
        'text_passage_id' => $passage->id,
        'wpm' => 75.50,
        'accuracy' => 98.5,
        'time_taken' => 120,
    ]);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors('errors_count');
});

test('user can view their game history', function () {
    $user = User::factory()->create();
    $passage = TextPassage::factory()->create();

    Result::factory()->count(5)->create([
        'user_id' => $user->id,
        'text_passage_id' => $passage->id,
    ]);

    $response = $this->actingAs($user, 'sanctum')->getJson('/api/user/history');

    $response->assertSuccessful();
    $response->assertJsonCount(5, 'data');
});

test('user history is ordered by most recent first', function () {
    $user = User::factory()->create();
    $passage = TextPassage::factory()->create();

    $oldResult = Result::factory()->create([
        'user_id' => $user->id,
        'text_passage_id' => $passage->id,
        'wpm' => 50,
        'completed_at' => now()->subDays(2),
    ]);

    $newResult = Result::factory()->create([
        'user_id' => $user->id,
        'text_passage_id' => $passage->id,
        'wpm' => 100,
        'completed_at' => now(),
    ]);

    $response = $this->actingAs($user, 'sanctum')->getJson('/api/user/history');

    $response->assertSuccessful();
    expect($response->json('data.0.id'))->toBe($newResult->id);
    expect($response->json('data.1.id'))->toBe($oldResult->id);
});

test('user history only shows their own results', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();
    $passage = TextPassage::factory()->create();

    Result::factory()->count(3)->create([
        'user_id' => $user1->id,
        'text_passage_id' => $passage->id,
    ]);

    Result::factory()->count(5)->create([
        'user_id' => $user2->id,
        'text_passage_id' => $passage->id,
    ]);

    $response = $this->actingAs($user1, 'sanctum')->getJson('/api/user/history');

    $response->assertSuccessful();
    $response->assertJsonCount(3, 'data');
});

test('unauthenticated user cannot view game history', function () {
    $response = $this->getJson('/api/user/history');

    $response->assertUnauthorized();
});
