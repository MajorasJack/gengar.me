<?php

declare(strict_types=1);

use App\Portfolio\Project;

$portfolio = require sprintf('%s/config/portfolio.php', dirname(__DIR__, 3));

test('there are five featured projects', function () use ($portfolio): void {
    expect($portfolio['projects'])->toHaveCount(5);
});

it('builds every featured project through Project::fromArray', function (array $entry): void {
    expect(Project::fromArray($entry))->toBeInstanceOf(Project::class);
})->with(array_map(fn (array $entry): array => [$entry], array_column($portfolio['projects'], null, 'slug')));

it('gives every featured project a problem, what was built and three to five engineering notes', function (array $entry): void {
    expect($entry['detail'])->toHaveKeys(['problem', 'built', 'notes'])
        ->and(count($entry['detail']['notes']))->toBeGreaterThanOrEqual(3)->toBeLessThanOrEqual(5);
})->with(array_map(fn (array $entry): array => [$entry], array_column($portfolio['projects'], null, 'slug')));

test('detail copy uses no em dash', function (array $entry): void {
    expect(json_encode($entry['detail'], JSON_UNESCAPED_UNICODE))->not->toContain('—');
})->with(array_map(fn (array $entry): array => [$entry], array_column($portfolio['projects'], null, 'slug')));
