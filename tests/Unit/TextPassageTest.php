<?php

use App\Models\TextPassage;

test('text passage has fillable attributes', function () {
    $passage = new TextPassage;

    expect($passage->getFillable())->toContain('text');
    expect($passage->getFillable())->toContain('language');
    expect($passage->getFillable())->toContain('word_count');
    expect($passage->getFillable())->toContain('character_count');
    expect($passage->getFillable())->toContain('category');
});

test('text passage defines casts', function () {
    $passage = new TextPassage;
    $casts = $passage->getCasts();

    expect($casts)->toHaveKey('word_count');
    expect($casts)->toHaveKey('character_count');
    expect($casts['word_count'])->toBe('integer');
    expect($casts['character_count'])->toBe('integer');
});

test('text passage has results relationship method', function () {
    $passage = new TextPassage;

    expect(method_exists($passage, 'results'))->toBeTrue();
});
