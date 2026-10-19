<?php

declare(strict_types=1);

use App\Portfolio\Exceptions\InvalidProjectException;
use App\Portfolio\Project;
use App\Portfolio\ProjectStatus;

$validEntry = fn (): array => [
    'slug' => fake()->slug(2),
    'name' => fake()->words(2, true),
    'summary' => fake()->sentence(),
    'stack' => fake()->words(3),
    'tests' => fake()->numberBetween(1, 9000),
    'tests_checked_at' => fake()->date(),
    'url' => sprintf('https://%s', fake()->domainName()),
    'visibility' => null,
    'status' => 'live',
];

it('builds a project from a valid entry', function () use ($validEntry): void {
    $entry = $validEntry();

    $project = Project::fromArray($entry);

    expect($project)->toEqual(new Project(
        slug: $entry['slug'],
        name: $entry['name'],
        summary: $entry['summary'],
        stack: $entry['stack'],
        tests: $entry['tests'],
        testsCheckedAt: $entry['tests_checked_at'],
        url: $entry['url'],
        visibility: null,
        status: ProjectStatus::Live,
    ));
});

it('builds a private project with a visibility label and no url', function () use ($validEntry): void {
    $visibility = fake()->words(2, true);

    $project = Project::fromArray([...$validEntry(), 'url' => null, 'visibility' => $visibility, 'status' => 'private']);

    expect($project->url)->toBeNull()
        ->and($project->visibility)->toBe($visibility);
});

it('carries the detail copy when the entry has it', function () use ($validEntry): void {
    $detail = ['problem' => fake()->sentence(), 'built' => fake()->sentence(), 'notes' => fake()->sentences(3)];

    expect(Project::fromArray([...$validEntry(), 'detail' => $detail])->detail)->toBe($detail);
});

it('leaves the detail empty when the entry has none', function () use ($validEntry): void {
    expect(Project::fromArray($validEntry())->detail)->toBeNull();
});

it('carries the slugs the project had before a rename', function () use ($validEntry): void {
    $previousSlugs = [fake()->slug(2)];

    expect(Project::fromArray([...$validEntry(), 'previous_slugs' => $previousSlugs])->previousSlugs)->toBe($previousSlugs);
});

it('has no previous slugs when the entry lists none', function () use ($validEntry): void {
    expect(Project::fromArray($validEntry())->previousSlugs)->toBe([]);
});

it('names the missing key when an entry lacks a required key', function (string $key) use ($validEntry): void {
    $entry = $validEntry();
    unset($entry[$key]);

    expect(fn (): Project => Project::fromArray($entry))
        ->toThrow(InvalidProjectException::class, sprintf('missing the required key "%s"', $key));
})->with(['slug', 'name', 'summary', 'stack', 'tests', 'tests_checked_at', 'status']);

it('rejects a project with neither a url nor a visibility label', function () use ($validEntry): void {
    $entry = [...$validEntry(), 'url' => null, 'visibility' => null];

    expect(fn (): Project => Project::fromArray($entry))
        ->toThrow(InvalidProjectException::class, 'needs a url or a visibility label');
});

it('names the slug and the value when the url is not https with a host', function (string $url) use ($validEntry): void {
    $entry = [...$validEntry(), 'url' => $url];

    expect(fn (): Project => Project::fromArray($entry))
        ->toThrow(InvalidProjectException::class, sprintf('Portfolio project "%s" has an invalid url "%s"', $entry['slug'], $url));
})->with([
    'no scheme or host' => fn (): string => fake()->domainName(),
    'http' => fn (): string => sprintf('http://%s', fake()->domainName()),
]);

it('names the slug and the value when the status is unknown', function () use ($validEntry): void {
    $entry = [...$validEntry(), 'status' => fake()->word()];

    expect(fn (): Project => Project::fromArray($entry))
        ->toThrow(InvalidProjectException::class, sprintf('Portfolio project "%s" has an unknown status "%s".', $entry['slug'], $entry['status']));
});
