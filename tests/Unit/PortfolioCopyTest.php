<?php

declare(strict_types=1);

$portfolio = require dirname(__DIR__, 2).'/config/portfolio.php';

test('the intro is no longer than 70 words', function () use ($portfolio): void {
    expect(count(preg_split('/\s+/', trim($portfolio['profile']['intro']))))->toBeLessThanOrEqual(70);
});

test('what I do has four areas, each with a title and a description', function () use ($portfolio): void {
    expect($portfolio['what_i_do'])->toHaveCount(4)
        ->each->toHaveKeys(['title', 'description']);
});

test('the denylist is not empty', function () use ($portfolio): void {
    expect($portfolio['denylist'])->not->toBeEmpty();
});
