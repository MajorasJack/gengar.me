<?php

declare(strict_types=1);

use App\Portfolio\Exceptions\InvalidProjectException;
use App\Portfolio\Project;

$validEntry = fn (): array => [
    'slug' => fake()->slug(2),
    'name' => fake()->words(2, true),
    'summary' => fake()->sentence(),
    'stack' => fake()->words(3),
    'tests' => fake()->numberBetween(1, 9000),
    'tests_checked_at' => fake()->date(),
    'url' => fake()->url(),
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
        status: 'live',
    ));
});

it('builds a private project with a visibility label and no url', function () use ($validEntry): void {
    $visibility = fake()->words(2, true);

    $project = Project::fromArray([...$validEntry(), 'url' => null, 'visibility' => $visibility, 'status' => 'private']);

    expect($project->url)->toBeNull()
        ->and($project->visibility)->toBe($visibility);
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
