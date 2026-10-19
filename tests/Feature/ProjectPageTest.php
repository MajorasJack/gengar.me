<?php

declare(strict_types=1);

use Illuminate\Support\Str;

it('renders a detail page for every featured project', function (): void {
    collect(config('portfolio.projects'))->each(fn (array $project) => $this->get(sprintf('/projects/%s', $project['slug']))
        ->assertOk()
        ->assertViewIs('project')
        ->assertSee(sprintf('<h1>%s</h1>', e($project['name'])), escape: false));
});

it('returns a 404 for a slug that matches no project', function (): void {
    $this->get(sprintf('/projects/%s', fake()->unique()->slug()))->assertNotFound();
});

it('links a live project to its site', function (): void {
    config(['portfolio.projects' => [[
        'slug' => 'example',
        'name' => fake()->company(),
        'summary' => fake()->sentence(),
        'stack' => [fake()->word()],
        'tests' => fake()->numberBetween(1, 1000),
        'tests_checked_at' => fake()->date(),
        'url' => sprintf('https://%s', fake()->domainName()),
        'visibility' => null,
        'status' => 'live',
    ]]]);

    $this->get('/projects/example')
        ->assertOk()
        ->assertSee(sprintf('<a href="%s">', e(config('portfolio.projects.0.url'))), escape: false);
});

it('shows the visibility label in place of a link for a private project', function (): void {
    config(['portfolio.projects' => [[
        'slug' => 'example',
        'name' => fake()->company(),
        'summary' => fake()->sentence(),
        'stack' => [fake()->word()],
        'tests' => fake()->numberBetween(1, 1000),
        'tests_checked_at' => fake()->date(),
        'url' => null,
        'visibility' => fake()->words(2, true),
        'status' => 'private',
    ]]]);

    $response = $this->get('/projects/example')->assertOk()->assertSeeText(config('portfolio.projects.0.visibility'));

    expect(Str::between($response->getContent(), '<main id="content">', '</main>'))->not->toContain('<a ');
});
