<?php

declare(strict_types=1);

use App\Portfolio\Project;

$portfolio = require dirname(__DIR__, 3).'/config/portfolio.php';

test('there are five featured projects', function () use ($portfolio): void {
    expect($portfolio['projects'])->toHaveCount(5);
});

it('builds every featured project through Project::fromArray', function (array $entry): void {
    expect(Project::fromArray($entry))->toBeInstanceOf(Project::class);
})->with(array_map(fn (array $entry): array => [$entry], array_column($portfolio['projects'], null, 'slug')));
