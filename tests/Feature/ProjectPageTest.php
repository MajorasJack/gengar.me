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

    expect(Str::between($response->getContent(), '<dl class="facts">', '</dl>'))->not->toContain('<a ');
});

it('shows the problem from the project detail', function (): void {
    config(['portfolio.projects.0.detail' => ['problem' => fake()->sentence()]]);

    $this->get('/projects/promotr')
        ->assertOk()
        ->assertSeeInOrder(['<h2 id="problem">the problem</h2>', e(config('portfolio.projects.0.detail.problem'))], escape: false);
});

it('shows what was built from the project detail', function (): void {
    config(['portfolio.projects.0.detail' => ['built' => fake()->sentence()]]);

    $this->get('/projects/promotr')
        ->assertOk()
        ->assertSeeInOrder(['<h2 id="built">what was built</h2>', e(config('portfolio.projects.0.detail.built'))], escape: false);
});

it('lists the engineering notes from the project detail', function (): void {
    config(['portfolio.projects.0.detail' => ['notes' => fake()->sentences(3)]]);

    $this->get('/projects/promotr')
        ->assertOk()
        ->assertSeeInOrder(['<h2 id="notes">engineering notes</h2>', ...array_map(fn (string $note): string => sprintf('<li>%s</li>', e($note)), config('portfolio.projects.0.detail.notes'))], escape: false);
});

it('shows no detail sections for a project without detail copy', function (): void {
    config(['portfolio.projects.0.detail' => null]);

    $this->get('/projects/promotr')
        ->assertOk()
        ->assertDontSee('id="problem"', escape: false)
        ->assertDontSee('id="built"', escape: false)
        ->assertDontSee('id="notes"', escape: false);
});

it('lists the stack under its heading', function (): void {
    $this->get('/projects/promotr')
        ->assertOk()
        ->assertSeeInOrder(['<dt>stack</dt>', ...config('portfolio.projects.0.stack')], escape: false);
});

it('labels the link or visibility label as the host', function (): void {
    $this->get('/projects/larder')
        ->assertOk()
        ->assertSeeInOrder(['<dt>host</dt>', e(config('portfolio.projects.1.visibility'))], escape: false);
});

it('shows the test count with the date it was checked, written in words', function (): void {
    config(['portfolio.projects.0.tests' => fake()->numberBetween(1000, 9999), 'portfolio.projects.0.tests_checked_at' => fake()->date()]);

    $this->get('/projects/promotr')
        ->assertOk()
        ->assertSeeInOrder(['<dt>tests</dt>', sprintf('~%s, checked %s', number_format(config('portfolio.projects.0.tests')), date('j F Y', strtotime(config('portfolio.projects.0.tests_checked_at'))))], escape: false);
});

it('links back to the homepage', function (): void {
    $this->get('/projects/promotr')->assertOk()->assertSee('<a href="/">', escape: false);
});

it('renders the styled not found page for an unknown slug', function (): void {
    $this->get(sprintf('/projects/%s', fake()->unique()->slug()))
        ->assertNotFound()
        ->assertSee('<h1>Nothing lives at this address</h1>', escape: false)
        ->assertSee('<a href="/">', escape: false);
});

it('links to the source code when the project has a public repository', function (): void {
    config(['portfolio.projects.0.source' => sprintf('https://github.com/%s/%s', fake()->userName(), fake()->slug(2))]);

    $this->get('/projects/promotr')
        ->assertOk()
        ->assertSeeInOrder(['<dt>source</dt>', sprintf('<a href="%s">', e(config('portfolio.projects.0.source')))], escape: false);
});

it('shows no source row when the repository is private', function (): void {
    config(['portfolio.projects.0.source' => null]);

    $this->get('/projects/promotr')->assertOk()->assertDontSee('<dt>source</dt>', escape: false);
});
