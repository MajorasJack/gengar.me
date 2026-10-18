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
