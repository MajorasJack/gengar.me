<?php

declare(strict_types=1);

use App\Portfolio\Project;

$portfolio = require sprintf('%s/config/portfolio.php', dirname(__DIR__, 3));

test('there are six featured projects', function () use ($portfolio): void {
    expect($portfolio['projects'])->toHaveCount(6);
});

it('builds every featured project through Project::fromArray', function (array $entry): void {
    expect(Project::fromArray($entry))->toBeInstanceOf(Project::class);
})->with(array_map(fn (array $entry): array => [$entry], array_column($portfolio['projects'], null, 'slug')));

it('gives every featured project an overview, a problem, what was built and three to five engineering notes', function (array $entry): void {
    expect($entry['detail'])->toHaveKeys(['overview', 'problem', 'built', 'notes'])
        ->and(count($entry['detail']['notes']))->toBeGreaterThanOrEqual(3)->toBeLessThanOrEqual(5);
})->with(array_map(fn (array $entry): array => [$entry], array_column($portfolio['projects'], null, 'slug')));

test('detail copy uses no em dash', function (array $entry): void {
    expect(json_encode($entry['detail'], JSON_UNESCAPED_UNICODE))->not->toContain('—');
})->with(array_map(fn (array $entry): array => [$entry], array_column($portfolio['projects'], null, 'slug')));

it('ships each screenshot as a WebP file with the width and height its entry states', function (array $screenshot): void {
    expect(getimagesize(sprintf('%s/public%s', dirname(__DIR__, 3), $screenshot['src'])))
        ->toMatchArray([0 => $screenshot['width'], 1 => $screenshot['height'], 'mime' => 'image/webp']);
})->with(array_map(fn (array $entry): array => [$entry['screenshot']], array_filter(array_column($portfolio['projects'], null, 'slug'), fn (array $entry): bool => isset($entry['screenshot']))));

it('gives each screenshot alt text', function (array $screenshot): void {
    expect(trim($screenshot['alt']))->not->toBeEmpty();
})->with(array_map(fn (array $entry): array => [$entry['screenshot']], array_filter(array_column($portfolio['projects'], null, 'slug'), fn (array $entry): bool => isset($entry['screenshot']))));

test('no slug, current or previous, belongs to two projects', function () use ($portfolio): void {
    $slugs = array_merge(...array_map(fn (array $entry): array => [$entry['slug'], ...$entry['previous_slugs'] ?? []], $portfolio['projects']));

    expect($slugs)->toBe(array_values(array_unique($slugs)));
});
