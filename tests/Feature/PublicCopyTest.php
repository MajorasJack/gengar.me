<?php

declare(strict_types=1);

use App\Portfolio\DenylistMatcher;

it('shows no denylisted name', function (string $uri): void {
    expect(new DenylistMatcher(config('portfolio.denylist'))->matches($this->get($uri)->getContent()))
        ->toBeEmpty();
})->with('public responses');

it('shows no performance figure', function (string $uri): void {
    expect(html_entity_decode($this->get($uri)->getContent()))
        ->not->toMatch('/\d+(\.\d+)?s\s*→\s*\d/u');
})->with('public responses');

it('shows no phone number', function (string $uri): void {
    expect(html_entity_decode($this->get($uri)->getContent()))
        ->not->toMatch('/\+?\d(?:[\s().-]*\d){9,}/');
})->with('public responses');
