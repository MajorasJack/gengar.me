<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

test('only the public pages are registered', function (): void {
    expect(collect(Route::getRoutes()->getRoutes())->map->uri()->sort()->values()->all())->toBe(['/', 'up']);
});

it('sets no cookies', function (string $uri): void {
    $this->get($uri)->assertHeaderMissing('Set-Cookie');
})->with('public responses');

it('loads no third-party resources', function (string $uri): void {
    expect($this->get($uri)->getContent())
        ->not->toMatch('#<(?:(?:link|script|img|iframe|source|video|audio|embed|object)\b[^>]*\b(?:href|src|data|poster)|form\b[^>]*\baction)\s*=\s*["\']?\s*(?:https?:|//)#i');
})->with('public responses');

it('ships no JavaScript', function (string $uri): void {
    expect($this->get($uri)->getContent())
        ->not->toMatch('#<script\b|javascript:|<[a-z][^>]*\son[a-z]+\s*=|\brel\s*=\s*["\']?modulepreload#i');
})->with('public responses');
