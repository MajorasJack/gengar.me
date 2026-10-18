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
