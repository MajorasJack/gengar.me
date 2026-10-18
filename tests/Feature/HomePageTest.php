<?php

declare(strict_types=1);

it('renders the homepage', function (): void {
    $this->get('/')->assertOk()->assertViewIs('home');
});

it('sets no cookies on the homepage', function (): void {
    $this->get('/')->assertHeaderMissing('Set-Cookie');
});
