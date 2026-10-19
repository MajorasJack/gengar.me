<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

test('only the public pages are registered', function (): void {
    expect(collect(Route::getRoutes()->getRoutes())->map->uri()->sort()->values()->all())->toBe(['/', 'projects/{slug}', 'sitemap.xml', 'up']);
});

it('sets no cookies', function (string $uri): void {
    $this->get($uri)->assertHeaderMissing('Set-Cookie');
})->with('public responses');

it('loads no third-party resources', function (string $uri): void {
    expect($this->get($uri)->getContent())
        ->not->toMatch(sprintf('#<(?:(?:link|script|img|iframe|source|video|audio|embed|object)\b[^>]*\b(?:(?:href|src|data|poster)\s*=\s*["\']?|(?:image)?srcset\s*=\s*["\']?(?:[^"\'>]*,)?)|form\b[^>]*\baction\s*=\s*["\']?)\s*(?:https?:(?!//%1$s/)|//(?!%1$s/))#i', preg_quote(config('portfolio.canonical_host'), '#')));
})->with('public responses');

it('ships no JavaScript', function (string $uri): void {
    expect($this->get($uri)->getContent())
        ->not->toMatch('#<script\b(?!\s+type="application/ld\+json">)|javascript:|<[a-z][^>]*\son[a-z]+\s*=|\brel\s*=\s*["\']?modulepreload#i');
})->with('public responses');

it('asks proxies not to transform the response', function (string $uri): void {
    expect($this->get($uri)->headers->get('Cache-Control'))->toContain('no-transform');
})->with('public responses');
