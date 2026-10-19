<?php

declare(strict_types=1);

use App\Http\Middleware\AddNoTransformCacheControl;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

it('adds no-transform to the existing Cache-Control directives', function (): void {
    $maxAge = fake()->numberBetween(1, 86400);

    $response = (new AddNoTransformCacheControl)->handle(
        Request::create('/'),
        fn (): Response => new Response(headers: ['Cache-Control' => sprintf('public, max-age=%d', $maxAge)]),
    );

    expect($response->headers->get('Cache-Control'))->toBe(sprintf('max-age=%d, no-transform, public', $maxAge));
});
