<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

it('renders the homepage', function (): void {
    $this->get('/')->assertOk()->assertViewIs('home');
});

it('sets no cookies on the homepage', function (): void {
    $this->get('/')->assertHeaderMissing('Set-Cookie');
});

test('the local disk is not served over HTTP', function (): void {
    expect(Route::has('storage.local'))->toBeFalse()
        ->and(Route::has('storage.local.upload'))->toBeFalse();
});

it('loads no third-party resources on the homepage', function (): void {
    $this->get('/')
        ->assertOk()
        ->assertDontSee('src="http', escape: false)
        ->assertDontSee('src="//', escape: false)
        ->assertDontSee('href="//', escape: false)
        ->assertSee('<link rel="preload" href="/fonts/geist-latin.woff2"', escape: false);
});
