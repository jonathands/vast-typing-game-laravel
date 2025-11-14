<?php

use App\Models\Result;
use App\Models\TextPassage;
use App\Models\User;
use App\Services\RankingService;

beforeEach(function () {
    $this->rankingService = new RankingService();
});

test('getGeneralRanking returns users ordered by combined score', function () {
    $passage = TextPassage::factory()->create();

    $user1 = User::factory()->create(['name' => 'Balanced Player']);
    $user2 = User::factory()->create(['name' => 'Speed Demon']);
    $user3 = User::factory()->create(['name' => 'Accuracy Expert']);

    Result::factory()->create([
        'user_id' => $user1->id,
        'text_passage_id' => $passage->id,
        'wpm' => 80,
        'accuracy' => 90,
    ]);

    Result::factory()->create([
        'user_id' => $user2->id,
        'text_passage_id' => $passage->id,
        'wpm' => 100,
        'accuracy' => 70,
    ]);

    Result::factory()->create([
        'user_id' => $user3->id,
        'text_passage_id' => $passage->id,
        'wpm' => 60,
        'accuracy' => 98,
    ]);

    $ranking = $this->rankingService->getGeneralRanking(10);

    expect($ranking)->toHaveCount(3);
    expect($ranking->first()->name)->toBe('Speed Demon');
    expect($ranking->first()->rank)->toBe(1);
    expect($ranking->last()->name)->toBe('Accuracy Expert');
});

test('getAccuracyRanking returns users ordered by accuracy', function () {
    $passage = TextPassage::factory()->create();

    $user1 = User::factory()->create(['name' => 'Good Accuracy']);
    $user2 = User::factory()->create(['name' => 'Perfect Accuracy']);
    $user3 = User::factory()->create(['name' => 'Low Accuracy']);

    Result::factory()->create([
        'user_id' => $user1->id,
        'text_passage_id' => $passage->id,
        'wpm' => 80,
        'accuracy' => 85,
    ]);

    Result::factory()->create([
        'user_id' => $user2->id,
        'text_passage_id' => $passage->id,
        'wpm' => 70,
        'accuracy' => 99,
    ]);

    Result::factory()->create([
        'user_id' => $user3->id,
        'text_passage_id' => $passage->id,
        'wpm' => 100,
        'accuracy' => 75,
    ]);

    $ranking = $this->rankingService->getAccuracyRanking(10);

    expect($ranking)->toHaveCount(3);
    expect($ranking->first()->name)->toBe('Perfect Accuracy');
    expect($ranking->first()->rank)->toBe(1);
    expect($ranking->last()->name)->toBe('Low Accuracy');
});

test('getSpeedRanking only includes results with accuracy >= 85%', function () {
    $passage = TextPassage::factory()->create();

    $user1 = User::factory()->create(['name' => 'Fast and Accurate']);
    $user2 = User::factory()->create(['name' => 'Fast but Sloppy']);
    $user3 = User::factory()->create(['name' => 'Moderate Speed Good Accuracy']);

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

    $ranking = $this->rankingService->getSpeedRanking(10);

    expect($ranking)->toHaveCount(2);
    expect($ranking->first()->name)->toBe('Fast and Accurate');
    expect($ranking->last()->name)->toBe('Moderate Speed Good Accuracy');
});

test('getSpeedRanking excludes user with low accuracy even if very fast', function () {
    $passage = TextPassage::factory()->create();

    $user1 = User::factory()->create(['name' => 'Fast and Accurate']);
    $user2 = User::factory()->create(['name' => 'Very Fast but Sloppy']);

    Result::factory()->create([
        'user_id' => $user1->id,
        'text_passage_id' => $passage->id,
        'wpm' => 90,
        'accuracy' => 85,
    ]);

    Result::factory()->create([
        'user_id' => $user2->id,
        'text_passage_id' => $passage->id,
        'wpm' => 150,
        'accuracy' => 84,
    ]);

    $ranking = $this->rankingService->getSpeedRanking(10);

    expect($ranking)->toHaveCount(1);
    expect($ranking->first()->name)->toBe('Fast and Accurate');
});

test('ranking methods calculate averages correctly', function () {
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

    $ranking = $this->rankingService->getGeneralRanking(10);

    expect($ranking)->toHaveCount(1);
    expect($ranking->first()->avg_wpm)->toBe(90.0);
    expect($ranking->first()->avg_accuracy)->toBe(92.0);
});

test('ranking methods respect limit parameter', function () {
    $passage = TextPassage::factory()->create();

    for ($i = 0; $i < 20; $i++) {
        $user = User::factory()->create();
        Result::factory()->create([
            'user_id' => $user->id,
            'text_passage_id' => $passage->id,
            'wpm' => 80 + $i,
            'accuracy' => 90,
        ]);
    }

    $ranking = $this->rankingService->getGeneralRanking(5);
    expect($ranking)->toHaveCount(5);

    $ranking = $this->rankingService->getAccuracyRanking(3);
    expect($ranking)->toHaveCount(3);

    $ranking = $this->rankingService->getSpeedRanking(7);
    expect($ranking)->toHaveCount(7);
});

test('ranking assigns sequential ranks correctly', function () {
    $passage = TextPassage::factory()->create();

    for ($i = 0; $i < 5; $i++) {
        $user = User::factory()->create();
        Result::factory()->create([
            'user_id' => $user->id,
            'text_passage_id' => $passage->id,
            'wpm' => 100 - ($i * 10),
            'accuracy' => 90,
        ]);
    }

    $ranking = $this->rankingService->getGeneralRanking(10);

    expect($ranking[0]->rank)->toBe(1);
    expect($ranking[1]->rank)->toBe(2);
    expect($ranking[2]->rank)->toBe(3);
    expect($ranking[3]->rank)->toBe(4);
    expect($ranking[4]->rank)->toBe(5);
});
