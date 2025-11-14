<?php

use App\Models\Result;

test('result has fillable attributes', function () {
    $result = new Result;

    expect($result->getFillable())->toContain('user_id');
    expect($result->getFillable())->toContain('text_passage_id');
    expect($result->getFillable())->toContain('wpm');
    expect($result->getFillable())->toContain('accuracy');
    expect($result->getFillable())->toContain('time_taken');
    expect($result->getFillable())->toContain('errors_count');
    expect($result->getFillable())->toContain('completed_at');
});

test('result defines casts', function () {
    $result = new Result;
    $casts = $result->getCasts();

    expect($casts)->toHaveKey('wpm');
    expect($casts)->toHaveKey('accuracy');
    expect($casts)->toHaveKey('time_taken');
    expect($casts)->toHaveKey('errors_count');
    expect($casts)->toHaveKey('completed_at');
    expect($casts['wpm'])->toBe('decimal:2');
    expect($casts['accuracy'])->toBe('decimal:2');
    expect($casts['time_taken'])->toBe('integer');
    expect($casts['errors_count'])->toBe('integer');
    expect($casts['completed_at'])->toBe('datetime');
});

test('result has user relationship method', function () {
    $result = new Result;

    expect(method_exists($result, 'user'))->toBeTrue();
});

test('result has text passage relationship method', function () {
    $result = new Result;

    expect(method_exists($result, 'textPassage'))->toBeTrue();
});
