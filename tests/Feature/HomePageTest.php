<?php

declare(strict_types=1);

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
