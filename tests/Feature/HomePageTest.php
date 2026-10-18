<?php

declare(strict_types=1);

use Illuminate\Support\Str;

it('renders the homepage', function (): void {
    $this->get('/')->assertOk()->assertViewIs('home');
});

it('preloads the self-hosted fonts', function (): void {
    $this->get('/')
        ->assertSee('<link rel="preload" href="/fonts/geist-latin.woff2"', escape: false)
        ->assertSee('<link rel="preload" href="/fonts/geist-mono-latin.woff2"', escape: false);
});

it('renders the profile text from config', function (string $key): void {
    config([sprintf('portfolio.profile.%s', $key) => fake()->sentence()]);

    $this->get('/')->assertOk()->assertSee(config(sprintf('portfolio.profile.%s', $key)));
})->with(['name', 'role', 'location', 'remote', 'intro']);

it('links to the profile pages from config', function (string $key): void {
    config([sprintf('portfolio.profile.%s', $key) => fake()->url()]);

    $this->get('/')->assertOk()->assertSee(sprintf('href="%s"', e(config(sprintf('portfolio.profile.%s', $key)))), escape: false);
})->with(['linkedin', 'github', 'cv']);

it('shows the email address as literal text', function (): void {
    $this->get('/')->assertOk()->assertSeeText('jack@gengar.me');
});

it('links the email address with mailto', function (): void {
    $this->get('/')->assertOk()->assertSee('<a href="mailto:jack@gengar.me">jack@gengar.me</a>', escape: false);
});

it('shows available for work while the flag is on', function (): void {
    config(['portfolio.available' => true]);

    $this->get('/')->assertOk()->assertSeeText('available for work');
});

it('omits available for work while the flag is off', function (): void {
    config(['portfolio.available' => false]);

    $this->get('/')->assertOk()->assertDontSeeText('available for work');
});

it('renders each what I do area from config', function (): void {
    config(['portfolio.what_i_do' => [
        ['title' => fake()->words(2, true), 'description' => fake()->sentence()],
        ['title' => fake()->words(2, true), 'description' => fake()->sentence()],
    ]]);

    $this->get('/')->assertOk()->assertSeeInOrder([
        'what I do',
        config('portfolio.what_i_do.0.title'),
        config('portfolio.what_i_do.0.description'),
        config('portfolio.what_i_do.1.title'),
        config('portfolio.what_i_do.1.description'),
    ]);
});

it('labels the CV button', function (): void {
    $this->get('/')->assertOk()->assertSee('<a class="primary" href="/cv.pdf">Download CV</a>', escape: false);
});

it('lists the featured projects in config order', function (): void {
    config(['portfolio.projects' => [
        ['slug' => fake()->unique()->slug(), 'name' => 'Zebra', 'summary' => fake()->sentence(), 'stack' => ['Laravel 13'], 'tests' => fake()->numberBetween(1, 5000), 'tests_checked_at' => '2026-10-18', 'url' => fake()->url(), 'visibility' => null, 'status' => 'live'],
        ['slug' => fake()->unique()->slug(), 'name' => 'Aardvark', 'summary' => fake()->sentence(), 'stack' => ['Laravel 13'], 'tests' => fake()->numberBetween(1, 5000), 'tests_checked_at' => '2026-10-18', 'url' => fake()->url(), 'visibility' => null, 'status' => 'live'],
    ]]);

    $this->get('/')->assertOk()->assertSeeInOrder(['running projects', 'Zebra', 'Aardvark']);
});

it('shows each project summary, stack and approximate test count', function (): void {
    config(['portfolio.projects' => [
        ['slug' => fake()->slug(), 'name' => fake()->word(), 'summary' => fake()->sentence(), 'stack' => ['Laravel 13', 'Vue 3', 'Reverb'], 'tests' => 30945, 'tests_checked_at' => '2026-10-18', 'url' => fake()->url(), 'visibility' => null, 'status' => 'live'],
    ]]);

    $this->get('/')->assertOk()->assertSeeInOrder([
        config('portfolio.projects.0.summary'),
        'Laravel 13 · Vue 3 · Reverb',
        '~30,945',
    ]);
});

it('links a public project by its host', function (): void {
    config(['portfolio.projects' => [
        ['slug' => fake()->slug(), 'name' => fake()->word(), 'summary' => fake()->sentence(), 'stack' => ['Laravel 13'], 'tests' => fake()->numberBetween(1, 5000), 'tests_checked_at' => '2026-10-18', 'url' => 'https://darkroom.gengar.me', 'visibility' => null, 'status' => 'live'],
    ]]);

    $this->get('/')->assertOk()->assertSee('<a href="https://darkroom.gengar.me">darkroom.gengar.me</a>', escape: false);
});

it('shows a private project its visibility label without a link', function (): void {
    config(['portfolio.projects' => [
        ['slug' => fake()->slug(), 'name' => fake()->word(), 'summary' => fake()->sentence(), 'stack' => ['Laravel 13'], 'tests' => fake()->numberBetween(1, 5000), 'tests_checked_at' => '2026-10-18', 'url' => null, 'visibility' => 'private beta', 'status' => 'private'],
    ]]);

    $table = Str::between($this->get('/')->assertOk()->getContent(), '<table', '</table>');

    expect($table)->toContain('<td class="host private" data-label="host">private beta</td>')->not->toContain('<a ');
});

it('marks a private project with the off status dot', function (): void {
    config(['portfolio.projects' => [
        ['slug' => fake()->slug(), 'name' => fake()->word(), 'summary' => fake()->sentence(), 'stack' => ['Laravel 13'], 'tests' => fake()->numberBetween(1, 5000), 'tests_checked_at' => '2026-10-18', 'url' => null, 'visibility' => 'private beta', 'status' => 'private'],
    ]]);

    $this->get('/')->assertOk()->assertSee('<span class="dot off" aria-hidden="true"></span>', escape: false);
});

it('labels each project cell for the stacked mobile layout', function (string $label): void {
    $this->get('/')->assertOk()->assertSee(sprintf('data-label="%s"', $label), escape: false);
})->with(['host', 'what it does', 'stack', 'tests']);

it('lists the smaller tools without links', function (): void {
    config(['portfolio.smaller_tools' => [
        ['name' => fake()->words(2, true), 'description' => fake()->sentence()],
        ['name' => fake()->words(2, true), 'description' => fake()->sentence()],
    ]]);

    $tools = Str::between($this->get('/')->assertOk()->getContent(), 'id="smaller-tools"', '</section>');

    expect($tools)
        ->toContain(e(config('portfolio.smaller_tools.0.name')), e(config('portfolio.smaller_tools.0.description')), e(config('portfolio.smaller_tools.1.name')))
        ->not->toContain('<a ');
});
