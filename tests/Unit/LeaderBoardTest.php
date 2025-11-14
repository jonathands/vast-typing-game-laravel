<?php

use App\Models\User;

test('user has results relationship method', function () {
    $user = new User;

    expect(method_exists($user, 'results'))->toBeTrue();
});

test('user has fillable attributes', function () {
    $user = new User;

    expect($user->getFillable())->toContain('name');
    expect($user->getFillable())->toContain('email');
    expect($user->getFillable())->toContain('password');
});

test('user has hidden attributes', function () {
    $user = new User;

    expect($user->getHidden())->toContain('password');
    expect($user->getHidden())->toContain('remember_token');
});
